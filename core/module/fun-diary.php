<?php
/**
 * 日记功能模块
 * @link https://www.mysqil.com
 * @package Kizumi
 */

//www.mysqil.com===安全设置=阻止直接访问主题文件
if(!defined('ABSPATH')){
    echo'Look your sister';
    exit;
}

// 注册日记自定义文章类型
function kizumi_register_diary_post_type() {
    $labels = array(
        'name'                  => '日记',
        'singular_name'         => '日记',
        'menu_name'             => '日记管理',
        'name_admin_bar'        => '日记',
        'archives'              => '日记归档',
        'attributes'            => '日记属性',
        'parent_item_colon'     => '父级日记:',
        'all_items'             => '所有日记',
        'add_new_item'          => '添加新日记',
        'add_new'               => '添加新日记',
        'new_item'              => '新日记',
        'edit_item'             => '编辑日记',
        'update_item'           => '更新日记',
        'view_item'             => '查看日记',
        'view_items'            => '查看日记',
        'search_items'          => '搜索日记',
        'not_found'             => '未找到日记',
        'not_found_in_trash'    => '回收站中未找到日记',
        'featured_image'        => '日记封面',
        'set_featured_image'    => '设置日记封面',
        'remove_featured_image' => '移除日记封面',
        'use_featured_image'    => '使用作为日记封面',
        'insert_into_item'      => '插入到日记',
        'uploaded_to_this_item' => '上传到此日记',
        'items_list'            => '日记列表',
        'items_list_navigation' => '日记列表导航',
        'filter_items_list'     => '筛选日记列表',
    );

    $args = array(
        'label'                 => '日记',
        'description'           => '日记管理',
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'author'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 6,
        'menu_icon'             => 'dashicons-edit-page',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'diary',
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rewrite'               => array('slug' => 'diary'),
    );

    register_post_type('diary', $args);
}
add_action('init', 'kizumi_register_diary_post_type', 0);

/**
 * 获取日记条目数量
 */
function kizumi_get_diary_count() {
    $diary_count = wp_count_posts('diary');
    return $diary_count->publish;
}

/**
 * 获取日记归档URL
 */
function kizumi_get_diary_archive_url() {
    return get_post_type_archive_link('diary');
}

/**
 * 获取日记的所有图片（本地+外链）
 */
function kizumi_get_all_diary_images($diary_id) {
    $all_images = array();
    
    // 获取本地上传的图片
    $local_images = get_attached_media('image', $diary_id);
    foreach ($local_images as $image) {
        $all_images[] = array(
            'type' => 'local',
            'id' => $image->ID,
            'url' => wp_get_attachment_image_url($image->ID, 'medium'),
            'full_url' => wp_get_attachment_image_url($image->ID, 'full'),
            'caption' => wp_get_attachment_caption($image->ID),
            'alt' => get_post_meta($image->ID, '_wp_attachment_image_alt', true)
        );
    }
    
    // 获取外链图片
    $external_images = get_post_meta($diary_id, '_diary_external_images', true);
    if (!empty($external_images) && is_array($external_images)) {
        foreach ($external_images as $external_image) {
            if (!empty($external_image['url'])) {
                $all_images[] = array(
                    'type' => 'external',
                    'id' => 0,
                    'url' => $external_image['url'],
                    'full_url' => $external_image['url'],
                    'caption' => isset($external_image['caption']) ? $external_image['caption'] : '',
                    'alt' => isset($external_image['alt']) ? $external_image['alt'] : ''
                );
            }
        }
    }
    
    return $all_images;
}

/**
 * 获取日记的外链图片
 */
function kizumi_get_diary_external_images($diary_id) {
    $external_images = get_post_meta($diary_id, '_diary_external_images', true);
    return !empty($external_images) && is_array($external_images) ? $external_images : array();
}

/**
 * 添加日记图片管理元框
 */
function kizumi_add_diary_meta_boxes() {
    add_meta_box(
        'diary-images',
        '日记图片管理',
        'kizumi_diary_images_meta_box_callback',
        'diary',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'kizumi_add_diary_meta_boxes');

/**
 * 加载媒体库脚本
 */
function kizumi_diary_admin_scripts($hook) {
    global $post_type;
    
    if (($hook == 'post-new.php' || $hook == 'post.php') && $post_type == 'diary') {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'kizumi_diary_admin_scripts');

/**
 * 日记图片管理元框回调函数
 */
function kizumi_diary_images_meta_box_callback($post) {
    wp_nonce_field('kizumi_diary_images_nonce', 'diary_images_nonce');
    
    $external_images = get_post_meta($post->ID, '_diary_external_images', true);
    if (!is_array($external_images)) {
        $external_images = array();
    }
    
    // 获取已附加的图片
    $attached_images = get_attached_media('image', $post->ID);
    
    echo '<div id="diary-images-container">';
    echo '<h4>本地图片管理</h4>';
    echo '<button type="button" id="add-media-images" class="button">从媒体库选择图片</button>';
    echo '<div id="selected-images-preview">';
    
    if (!empty($attached_images)) {
        foreach ($attached_images as $image) {
            $image_url = wp_get_attachment_image_url($image->ID, 'medium');
            if (!$image_url) {
                $image_url = wp_get_attachment_url($image->ID);
            }
            echo '<div class="selected-image-item" data-id="' . $image->ID . '">';
            echo '<img src="' . esc_url($image_url) . '" style="max-width: 200px; max-height: 150px; object-fit: contain; margin: 5px; border: 1px solid #ddd;" />';
            echo '<br><button type="button" class="button remove-selected-image">移除</button>';
            echo '</div>';
        }
    }
    
    echo '</div>';
    echo '<input type="hidden" id="selected-image-ids" name="selected_image_ids" value="" />';
    
    echo '<h4>外链图片</h4>';
    echo '<p>添加外部图片链接：</p>';
    echo '<div id="external-images-list">';
    
    if (!empty($external_images)) {
        foreach ($external_images as $index => $image) {
            echo '<div class="external-image-item" data-index="' . $index . '">';
            echo '<input type="url" name="external_images[' . $index . '][url]" value="' . esc_attr($image['url']) . '" placeholder="图片URL" style="width: 60%;" />';
            echo '<input type="text" name="external_images[' . $index . '][alt]" value="' . esc_attr(isset($image['alt']) ? $image['alt'] : '') . '" placeholder="图片描述" style="width: 30%;" />';
            echo '<button type="button" class="button remove-external-image">删除</button>';
            echo '</div>';
        }
    }
    
    echo '</div>';
    echo '<button type="button" id="add-external-image" class="button">添加外链图片</button>';
    echo '</div>';
    
    // 添加JavaScript
    echo '<script>
    jQuery(document).ready(function($) {
        var imageIndex = ' . count($external_images) . ';
        var selectedImageIds = [];
        
        // 媒体库选择功能
        var mediaUploader;
        $("#add-media-images").click(function(e) {
            e.preventDefault();
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: "选择日记图片",
                button: {
                    text: "选择图片"
                },
                multiple: true,
                library: {
                    type: "image"
                },
                frame: "select",
                state: "library"
            });
            
            mediaUploader.on("select", function() {
                var attachments = mediaUploader.state().get("selection").toJSON();
                
                attachments.forEach(function(attachment) {
                    if (selectedImageIds.indexOf(attachment.id) === -1) {
                        selectedImageIds.push(attachment.id);
                        
                        var imageUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
                         var html = "<div class=\"selected-image-item\" data-id=\"" + attachment.id + "\">";
                         html += "<img src=\"" + imageUrl + "\" style=\"max-width: 200px; max-height: 150px; object-fit: contain; margin: 5px; border: 1px solid #ddd;\" />";
                         html += "<br><button type=\"button\" class=\"button remove-selected-image\">移除</button>";
                         html += "</div>";
                        
                        $("#selected-images-preview").append(html);
                    }
                });
                
                $("#selected-image-ids").val(selectedImageIds.join(","));
            });
            
            mediaUploader.open();
        });
        
        // 移除选中的图片
        $(document).on("click", ".remove-selected-image", function() {
            var imageId = $(this).closest(".selected-image-item").data("id");
            var index = selectedImageIds.indexOf(imageId);
            if (index > -1) {
                selectedImageIds.splice(index, 1);
            }
            $(this).closest(".selected-image-item").remove();
            $("#selected-image-ids").val(selectedImageIds.join(","));
        });
        
        // 初始化已选择的图片ID
        $(".selected-image-item").each(function() {
            var imageId = parseInt($(this).data("id"));
            if (selectedImageIds.indexOf(imageId) === -1) {
                selectedImageIds.push(imageId);
            }
        });
        $("#selected-image-ids").val(selectedImageIds.join(","));
        
        // 外链图片管理
        $("#add-external-image").click(function() {
            var html = "<div class=\"external-image-item\" data-index=\"" + imageIndex + "\">";
            html += "<input type=\"url\" name=\"external_images[" + imageIndex + "][url]\" placeholder=\"图片URL\" style=\"width: 60%;\" />";
            html += "<input type=\"text\" name=\"external_images[" + imageIndex + "][alt]\" placeholder=\"图片描述\" style=\"width: 30%;\" />";
            html += "<button type=\"button\" class=\"button remove-external-image\">删除</button>";
            html += "</div>";
            $("#external-images-list").append(html);
            imageIndex++;
        });
        
        $(document).on("click", ".remove-external-image", function() {
            $(this).closest(".external-image-item").remove();
        });
    });
    </script>';
}

/**
 * 保存日记图片数据
 */
function kizumi_save_diary_images($post_id) {
    if (!isset($_POST['diary_images_nonce']) || !wp_verify_nonce($_POST['diary_images_nonce'], 'kizumi_diary_images_nonce')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // 处理媒体库选择的图片
    if (isset($_POST['selected_image_ids']) && !empty($_POST['selected_image_ids'])) {
        $selected_ids = explode(',', $_POST['selected_image_ids']);
        $selected_ids = array_map('intval', $selected_ids);
        $selected_ids = array_filter($selected_ids); // 移除空值
        
        // 获取当前已附加的图片
        $current_attachments = get_attached_media('image', $post_id);
        $current_ids = array();
        foreach ($current_attachments as $attachment) {
            $current_ids[] = $attachment->ID;
        }
        
        // 附加新选择的图片
        foreach ($selected_ids as $image_id) {
            if (!in_array($image_id, $current_ids)) {
                wp_update_post(array(
                    'ID' => $image_id,
                    'post_parent' => $post_id
                ));
            }
        }
        
        // 移除不再选择的图片
        foreach ($current_ids as $current_id) {
            if (!in_array($current_id, $selected_ids)) {
                wp_update_post(array(
                    'ID' => $current_id,
                    'post_parent' => 0
                ));
            }
        }
    } else {
        // 如果没有选择任何图片，移除所有附加的图片
        $current_attachments = get_attached_media('image', $post_id);
        foreach ($current_attachments as $attachment) {
            wp_update_post(array(
                'ID' => $attachment->ID,
                'post_parent' => 0
            ));
        }
    }
    
    // 保存外链图片
    $external_images = array();
    if (isset($_POST['external_images']) && is_array($_POST['external_images'])) {
        foreach ($_POST['external_images'] as $image) {
            if (!empty($image['url'])) {
                $external_images[] = array(
                    'url' => esc_url_raw($image['url']),
                    'alt' => sanitize_text_field($image['alt'])
                );
            }
        }
    }
    
    update_post_meta($post_id, '_diary_external_images', $external_images);
}
add_action('save_post', 'kizumi_save_diary_images');

// 刷新重写规则
function kizumi_diary_flush_rewrite_rules() {
    kizumi_register_diary_post_type();
    flush_rewrite_rules();
}

// 主题激活时刷新重写规则
function kizumi_diary_theme_activation() {
    kizumi_register_diary_post_type();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'kizumi_diary_theme_activation');

/**
 * 格式化日记时间显示
 */
function kizumi_format_diary_time($date) {
    return date('Y年m月d日 H:i', strtotime($date));
}

?>