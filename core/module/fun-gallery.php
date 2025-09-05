<?php
/**
 * Gallery功能模块
 * @link https://www.mysqil.com
 * @package Kizumi
 */

//www.mysqil.com===安全设置=阻止直接访问主题文件
if(!defined('ABSPATH')){
    echo'Look your sister';
    exit;
}

// 注册Gallery自定义文章类型
function kizumi_register_gallery_post_type() {
    $labels = array(
        'name'                  => '相册',
        'singular_name'         => '相册',
        'menu_name'             => '相册管理',
        'name_admin_bar'        => '相册',
        'archives'              => '相册归档',
        'attributes'            => '相册属性',
        'parent_item_colon'     => '父级相册:',
        'all_items'             => '所有相册',
        'add_new_item'          => '添加新相册',
        'add_new'               => '添加新相册',
        'new_item'              => '新相册',
        'edit_item'             => '编辑相册',
        'update_item'           => '更新相册',
        'view_item'             => '查看相册',
        'view_items'            => '查看相册',
        'search_items'          => '搜索相册',
        'not_found'             => '未找到相册',
        'not_found_in_trash'    => '回收站中未找到相册',
        'featured_image'        => '相册封面',
        'set_featured_image'    => '设置相册封面',
        'remove_featured_image' => '移除相册封面',
        'use_featured_image'    => '使用作为相册封面',
        'insert_into_item'      => '插入到相册',
        'uploaded_to_this_item' => '上传到此相册',
        'items_list'            => '相册列表',
        'items_list_navigation' => '相册列表导航',
        'filter_items_list'     => '筛选相册列表',
    );

    $args = array(
        'label'                 => '相册',
        'description'           => '相册管理',
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'author'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-format-gallery',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'gallery',
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rewrite'               => array('slug' => 'gallery'),
    );

    register_post_type('gallery', $args);
}
add_action('init', 'kizumi_register_gallery_post_type', 0);



// 刷新重写规则
function kizumi_gallery_flush_rewrite_rules() {
    kizumi_register_gallery_post_type();
    flush_rewrite_rules();
}

// 主题激活时刷新重写规则
function kizumi_gallery_theme_activation() {
    kizumi_register_gallery_post_type();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'kizumi_gallery_theme_activation');

// 添加管理员工具来手动刷新重写规则
function kizumi_gallery_admin_notice() {
    if (isset($_GET['flush_gallery_rules']) && $_GET['flush_gallery_rules'] == '1') {
        kizumi_gallery_flush_rewrite_rules();
        echo '<div class="notice notice-success is-dismissible"><p>相册重写规则已刷新！</p></div>';
    }
}
add_action('admin_notices', 'kizumi_gallery_admin_notice');

// 在相册管理页面添加刷新按钮
function kizumi_gallery_admin_menu_page() {
    echo '<div class="wrap">';
    echo '<h1>相册设置</h1>';
    echo '<p>如果相册页面出现404错误，请点击下面的按钮刷新重写规则：</p>';
    echo '<a href="' . admin_url('edit.php?post_type=gallery&flush_gallery_rules=1') . '" class="button button-primary">刷新相册重写规则</a>';
    echo '</div>';
}

function kizumi_gallery_add_admin_menu() {
    add_submenu_page(
        'edit.php?post_type=gallery',
        '相册设置',
        '相册设置',
        'manage_options',
        'gallery-settings',
        'kizumi_gallery_admin_menu_page'
    );
}
add_action('admin_menu', 'kizumi_gallery_add_admin_menu');

// 获取相册归档页面URL
function kizumi_get_gallery_archive_url() {
    $gallery_page = get_option('kizumi_gallery_page');
    if ($gallery_page) {
        return get_permalink($gallery_page);
    }
    
    // 如果没有设置专门的相册页面，使用自定义文章类型归档
    $post_type_obj = get_post_type_object('gallery');
    if ($post_type_obj && $post_type_obj->has_archive) {
        return get_post_type_archive_link('gallery');
    }
    
    return home_url('/gallery/');
}

// 添加相册自定义字段
function kizumi_add_gallery_meta_boxes() {
    add_meta_box(
        'gallery_images',
        '相册图片',
        'kizumi_gallery_images_callback',
        'gallery',
        'normal',
        'high'
    );
    
    add_meta_box(
        'gallery_external_images',
        '外链图片',
        'kizumi_gallery_external_images_callback',
        'gallery',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'kizumi_add_gallery_meta_boxes');

// 相册图片字段回调
function kizumi_gallery_images_callback($post) {
    wp_nonce_field('kizumi_gallery_meta_box', 'kizumi_gallery_meta_box_nonce');
    
    $gallery_images = get_post_meta($post->ID, '_gallery_images', true);
    $gallery_images = $gallery_images ? $gallery_images : array();
    ?>
    <div id="gallery-images-container">
        <div class="gallery-images-list">
            <?php if (!empty($gallery_images)): ?>
                <?php foreach ($gallery_images as $index => $image_id): ?>
                    <div class="gallery-image-item" data-index="<?php echo $index; ?>">
                        <div class="image-preview">
                            <?php echo wp_get_attachment_image($image_id, 'thumbnail'); ?>
                        </div>
                        <div class="image-controls">
                            <input type="hidden" name="gallery_images[]" value="<?php echo $image_id; ?>">
                            <button type="button" class="button remove-image">移除</button>
                            <span class="drag-handle">⋮⋮</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="gallery-controls">
            <button type="button" id="add-gallery-images" class="button button-primary">添加图片</button>
            <p class="description">点击添加图片按钮选择多张图片，可拖拽调整顺序</p>
        </div>
    </div>
    
    <style>
    .gallery-images-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    .gallery-image-item {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px;
        background: #f9f9f9;
        cursor: move;
    }
    .gallery-image-item:hover {
        border-color: #0073aa;
    }
    .image-preview img {
        width: 100%;
        height: auto;
        border-radius: 2px;
    }
    .image-controls {
        margin-top: 8px;
        text-align: center;
    }
    .drag-handle {
        cursor: move;
        color: #666;
        margin-left: 5px;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // 添加图片
        $('#add-gallery-images').click(function(e) {
            e.preventDefault();
            
            var frame = wp.media({
                title: '选择相册图片',
                multiple: true,
                library: { type: 'image' },
                button: { text: '添加到相册' }
            });
            
            frame.on('select', function() {
                var attachments = frame.state().get('selection').toJSON();
                var container = $('.gallery-images-list');
                
                attachments.forEach(function(attachment) {
                    var index = $('.gallery-image-item').length;
                    var html = '<div class="gallery-image-item" data-index="' + index + '">' +
                              '<div class="image-preview">' +
                              '<img src="' + attachment.sizes.thumbnail.url + '" alt="">' +
                              '</div>' +
                              '<div class="image-controls">' +
                              '<input type="hidden" name="gallery_images[]" value="' + attachment.id + '">' +
                              '<button type="button" class="button remove-image">移除</button>' +
                              '<span class="drag-handle">⋮⋮</span>' +
                              '</div>' +
                              '</div>';
                    container.append(html);
                });
            });
            
            frame.open();
        });
        
        // 移除图片
        $(document).on('click', '.remove-image', function() {
            $(this).closest('.gallery-image-item').remove();
        });
        
        // 拖拽排序
        $('.gallery-images-list').sortable({
            handle: '.drag-handle',
            placeholder: 'ui-state-highlight',
            update: function(event, ui) {
                // 更新索引
                $('.gallery-image-item').each(function(index) {
                    $(this).attr('data-index', index);
                });
            }
        });
    });
    </script>
    <?php
}

// 外链图片字段回调
function kizumi_gallery_external_images_callback($post) {
    $external_images = get_post_meta($post->ID, '_gallery_external_images', true);
    $external_images = $external_images ? $external_images : '';
    ?>
    <div id="gallery-external-images-container">
        <div class="external-images-input">
            <textarea name="gallery_external_images" id="gallery_external_images" rows="10" cols="50" style="width: 100%;" placeholder="请输入外链图片URL，每行一个链接&#10;例如：&#10;https://example.com/image1.jpg&#10;https://example.com/image2.png&#10;https://example.com/image3.gif"><?php echo esc_textarea($external_images); ?></textarea>
            <p class="description">
                <strong>使用说明：</strong><br>
                • 每行输入一个图片链接<br>
                • 支持 jpg、jpeg、png、gif、webp 格式<br>
                • 请确保图片链接可以正常访问<br>
                • 外链图片将与本地上传的图片一起显示
            </p>
        </div>
        
        <div class="external-images-preview" id="external-images-preview" style="margin-top: 20px;">
            <h4>预览外链图片：</h4>
            <div class="preview-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px;">
                <!-- 预览将通过JavaScript动态生成 -->
            </div>
        </div>
    </div>
    
    <style>
    .external-images-input textarea {
        font-family: monospace;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px;
    }
    .external-images-input textarea:focus {
        border-color: #0073aa;
        box-shadow: 0 0 0 1px #0073aa;
        outline: none;
    }
    .preview-container img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
    .preview-item {
        position: relative;
    }
    .preview-error {
        width: 100%;
        height: 120px;
        background: #f0f0f0;
        border: 1px solid #ddd;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        font-size: 12px;
        text-align: center;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // 预览外链图片
        function previewExternalImages() {
            var textarea = $('#gallery_external_images');
            var previewContainer = $('.preview-container');
            var urls = textarea.val().split('
').filter(function(url) {
                return url.trim() !== '';
            });
            
            previewContainer.empty();
            
            if (urls.length === 0) {
                previewContainer.html('<p style="color: #666; grid-column: 1 / -1;">暂无外链图片</p>');
                return;
            }
            
            urls.forEach(function(url, index) {
                url = url.trim();
                if (url) {
                    var previewItem = $('<div class="preview-item"></div>');
                    var img = $('<img>');
                    
                    img.on('load', function() {
                        // 图片加载成功
                    }).on('error', function() {
                        // 图片加载失败
                        $(this).replaceWith('<div class="preview-error">图片加载失败<br>' + url.substring(0, 30) + '...</div>');
                    });
                    
                    img.attr('src', url);
                    img.attr('alt', '外链图片 ' + (index + 1));
                    
                    previewItem.append(img);
                    previewContainer.append(previewItem);
                }
            });
        }
        
        // 监听文本框变化
        $('#gallery_external_images').on('input', function() {
            clearTimeout(window.previewTimer);
            window.previewTimer = setTimeout(previewExternalImages, 1000);
        });
        
        // 初始预览
        previewExternalImages();
    });
    </script>
    <?php
}



// 保存相册自定义字段
function kizumi_save_gallery_meta_box($post_id) {
    if (!isset($_POST['kizumi_gallery_meta_box_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['kizumi_gallery_meta_box_nonce'], 'kizumi_gallery_meta_box')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (isset($_POST['post_type']) && 'gallery' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }
    
    // 保存相册图片
    if (isset($_POST['gallery_images'])) {
        $gallery_images = array_map('intval', $_POST['gallery_images']);
        update_post_meta($post_id, '_gallery_images', $gallery_images);
    } else {
        delete_post_meta($post_id, '_gallery_images');
    }
    
    // 保存外链图片
    if (isset($_POST['gallery_external_images'])) {
        $external_images = sanitize_textarea_field($_POST['gallery_external_images']);
        update_post_meta($post_id, '_gallery_external_images', $external_images);
    } else {
        delete_post_meta($post_id, '_gallery_external_images');
    }
    

}
add_action('save_post', 'kizumi_save_gallery_meta_box');

// 获取相册图片
function kizumi_get_gallery_images($post_id) {
    $gallery_images = get_post_meta($post_id, '_gallery_images', true);
    return $gallery_images ? $gallery_images : array();
}

// 获取外链图片
function kizumi_get_gallery_external_images($post_id) {
    $external_images = get_post_meta($post_id, '_gallery_external_images', true);
    if (!$external_images) {
        return array();
    }
    
    // 支持多种分隔符：换行符、分号、逗号
    $urls = preg_split('/[

;,]+/', $external_images);
    $valid_urls = array();
    
    foreach ($urls as $url) {
        $url = trim($url);
        if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
            // 检查是否是图片格式
            $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
            if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif', 'webp'))) {
                $valid_urls[] = $url;
            }
        }
    }
    
    return $valid_urls;
}

// 获取所有相册图片（本地+外链）
function kizumi_get_all_gallery_images($post_id) {
    $local_images = kizumi_get_gallery_images($post_id);
    $external_images = kizumi_get_gallery_external_images($post_id);
    
    $all_images = array();
    
    // 添加本地图片
    foreach ($local_images as $image_id) {
        $all_images[] = array(
            'type' => 'local',
            'id' => $image_id,
            'url' => wp_get_attachment_image_url($image_id, 'medium'),
            'full_url' => wp_get_attachment_image_url($image_id, 'full'),
            'caption' => wp_get_attachment_caption($image_id)
        );
    }
    
    // 添加外链图片
    foreach ($external_images as $index => $url) {
        $all_images[] = array(
            'type' => 'external',
            'id' => 'ext_' . $index,
            'url' => $url,
            'full_url' => $url,
            'caption' => ''
        );
    }
    
    return $all_images;
}



// 修改相册文章列表显示
function kizumi_gallery_columns_head($defaults) {
    $defaults['gallery_images'] = '图片数量';
    return $defaults;
}
add_filter('manage_gallery_posts_columns', 'kizumi_gallery_columns_head');

function kizumi_gallery_columns_content($column_name, $post_id) {
    if ($column_name == 'gallery_images') {
        $images = kizumi_get_gallery_images($post_id);
        echo count($images) . ' 张';
    }
}
add_action('manage_gallery_posts_custom_column', 'kizumi_gallery_columns_content', 10, 2);
?>