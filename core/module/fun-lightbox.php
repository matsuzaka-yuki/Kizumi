<?php
/**
 * 全局图片灯箱功能
 * @link https://www.mysqil.com
 * @package Kizumi
 */

// 安全设置--------------------------www.mysqil.com--------------------------
if(!defined('ABSPATH')){
    echo'Look your sister';
    exit;
}

// 引入Fancybox资源
function kizumi_enqueue_lightbox_assets() {
    // 检查是否开启灯箱功能
    if (!get_kizumi('kizumi_lightbox_switch')) {
        return;
    }
    
    // 引入Fancybox CSS（已存在）
    wp_enqueue_style('fancybox-css', kizumi_theme_url() . '/assets/css/fancybox.min.css', array(), THEME_VERSION);
    
    // 引入Fancybox JavaScript
    wp_enqueue_script('fancybox-js', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', array('jquery'), '5.0.0', true);
    
    // 引入自定义灯箱初始化脚本
    wp_enqueue_script('kizumi-lightbox', kizumi_theme_url() . '/assets/js/lightbox.js', array('jquery', 'fancybox-js'), THEME_VERSION, true);
}
add_action('wp_enqueue_scripts', 'kizumi_enqueue_lightbox_assets');

// 自动为内容中的图片添加灯箱功能
function kizumi_add_lightbox_to_images($content) {
    // 检查是否开启灯箱功能
    if (!get_kizumi('kizumi_lightbox_switch')) {
        return $content;
    }
    
    // 在文章、页面、日记和相册中处理
    if (!is_single() && !is_page()) {
        return $content;
    }
    
    // 使用正则表达式匹配img标签
    $pattern = '/<img([^>]+)src=["\']([^"\'>]+)["\']([^>]*)>/i';
    
    $content = preg_replace_callback($pattern, function($matches) use ($content) {
        $img_tag = $matches[0];
        $img_src = $matches[2];
        
        // 检查是否已经被链接包围
        $before_img = substr($content, 0, strpos($content, $img_tag));
        $after_img = substr($content, strpos($content, $img_tag) + strlen($img_tag));
        
        // 如果图片已经在链接中，不处理
        if (preg_match('/<a[^>]*>\s*$/', $before_img) && preg_match('/^\s*<\/a>/', $after_img)) {
            return $img_tag;
        }
        
        // 为图片添加灯箱链接
        return '<a href="' . $img_src . '" data-fancybox="gallery" data-caption="">' . $img_tag . '</a>';
    }, $content);
    
    return $content;
}
add_filter('the_content', 'kizumi_add_lightbox_to_images');

// 为画廊添加灯箱功能
function kizumi_gallery_lightbox($output, $attr) {
    // 检查是否开启灯箱功能
    if (!get_kizumi('kizumi_lightbox_switch')) {
        return $output;
    }
    
    global $post;
    
    if (!empty($attr['ids'])) {
        $ids = explode(',', $attr['ids']);
    } else {
        $ids = get_children(array(
            'post_parent' => $post->ID,
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'orderby' => 'menu_order',
            'order' => 'ASC'
        ));
        $ids = array_keys($ids);
    }
    
    if (empty($ids)) {
        return $output;
    }
    
    $size = isset($attr['size']) ? $attr['size'] : 'thumbnail';
    $columns = isset($attr['columns']) ? intval($attr['columns']) : 3;
    
    $gallery_html = '<div class="kizumi-gallery gallery-columns-' . $columns . '">';
    
    foreach ($ids as $id) {
        $image_src = wp_get_attachment_image_src($id, 'full');
        $image_thumb = wp_get_attachment_image_src($id, $size);
        $image_alt = get_post_meta($id, '_wp_attachment_image_alt', true);
        $image_caption = wp_get_attachment_caption($id);
        
        if ($image_src && $image_thumb) {
            $gallery_html .= '<div class="gallery-item">';
            $gallery_html .= '<a href="' . $image_src[0] . '" data-fancybox="gallery" data-caption="' . esc_attr($image_caption) . '">';
            $gallery_html .= '<img src="' . $image_thumb[0] . '" alt="' . esc_attr($image_alt) . '" class="img-fluid" />';
            $gallery_html .= '</a>';
            $gallery_html .= '</div>';
        }
    }
    
    $gallery_html .= '</div>';
    
    return $gallery_html;
}
add_filter('post_gallery', 'kizumi_gallery_lightbox', 10, 2);

// 添加灯箱相关CSS样式
function kizumi_lightbox_styles() {
    // 检查是否开启灯箱功能
    if (!get_kizumi('kizumi_lightbox_switch')) {
        return;
    }
    
    echo '<style>
    .kizumi-gallery {
        display: grid;
        gap: 10px;
        margin: 20px 0;
    }
    .kizumi-gallery.gallery-columns-1 { grid-template-columns: 1fr; }
    .kizumi-gallery.gallery-columns-2 { grid-template-columns: repeat(2, 1fr); }
    .kizumi-gallery.gallery-columns-3 { grid-template-columns: repeat(3, 1fr); }
    .kizumi-gallery.gallery-columns-4 { grid-template-columns: repeat(4, 1fr); }
    .kizumi-gallery.gallery-columns-5 { grid-template-columns: repeat(5, 1fr); }
    .kizumi-gallery.gallery-columns-6 { grid-template-columns: repeat(6, 1fr); }
    
    .kizumi-gallery .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        transition: transform 0.3s ease;
    }
    
    .kizumi-gallery .gallery-item:hover {
        transform: scale(1.05);
    }
    
    .kizumi-gallery .gallery-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        transition: opacity 0.3s ease;
    }
    
    .kizumi-gallery .gallery-item:hover img {
        opacity: 0.9;
    }
    
    /* 响应式设计 */
    @media (max-width: 768px) {
        .kizumi-gallery.gallery-columns-3,
        .kizumi-gallery.gallery-columns-4,
        .kizumi-gallery.gallery-columns-5,
        .kizumi-gallery.gallery-columns-6 {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 480px) {
        .kizumi-gallery {
            grid-template-columns: 1fr !important;
        }
    }
    
    /* 灯箱图片样式 */
    .content img {
        cursor: pointer;
        transition: opacity 0.3s ease;
    }
    
    .content img:hover {
        opacity: 0.9;
    }
    </style>';
}
add_action('wp_head', 'kizumi_lightbox_styles');

// 全局灯箱初始化 - 适用于所有页面
function kizumi_global_lightbox_init() {
    // 检查是否开启灯箱功能
    if (!get_kizumi('kizumi_lightbox_switch')) {
        return;
    }
    
    ?>
    <script>
    jQuery(document).ready(function($) {
        console.log('Kizumi全局灯箱初始化开始');
        
        // 等待Fancybox加载完成
        function initLightbox() {
            if (typeof Fancybox === 'undefined') {
                setTimeout(initLightbox, 100);
                return;
            }
            
            console.log('Fancybox已加载，开始初始化灯箱');
            
            // 为所有符合条件的图片添加灯箱功能
            function processImages() {
                var allImages = $('img').not('.no-lightbox, .avatar, .emoji, .wp-smiley, .logo, .admin-bar img');
                console.log('找到图片总数:', allImages.length);
                
                allImages.each(function(index) {
                    var $img = $(this);
                    var imgSrc = $img.attr('src') || $img.attr('data-src');
                    
                    if (!imgSrc) return;
                    
                    // 跳过已经处理过的图片
                    if ($img.data('lightbox-processed')) return;
                    $img.data('lightbox-processed', true);
                    
                    var $parent = $img.parent();
                    
                    // 如果图片已经在链接中
                    if ($parent.is('a')) {
                        var linkHref = $parent.attr('href');
                        
                        // 检查链接是否指向图片
                        if (linkHref && (linkHref.match(/\.(jpg|jpeg|png|gif|webp|svg)(\?.*)?$/i) || linkHref === imgSrc)) {
                            // 为现有链接添加灯箱属性
                            $parent.attr('data-fancybox', 'gallery-' + (new Date().getTime() + index));
                            $parent.attr('data-caption', $img.attr('alt') || $img.attr('title') || '');
                            console.log('为现有链接添加灯箱:', linkHref);
                        } else if ($img.closest('.gallery-images, .diary-images, .moment-images').length) {
                            // 相册或日记页面的图片，即使链接不指向图片也要添加灯箱
                            $parent.attr('data-fancybox', 'content-gallery');
                            $parent.attr('data-src', imgSrc);
                            $parent.attr('data-caption', $img.attr('alt') || $img.attr('title') || '');
                            
                            // 阻止原始链接行为，改为打开灯箱
                            $parent.off('click.lightbox').on('click.lightbox', function(e) {
                                e.preventDefault();
                                Fancybox.show([{
                                    src: imgSrc,
                                    type: 'image',
                                    caption: $img.attr('alt') || $img.attr('title') || ''
                                }]);
                            });
                            console.log('为相册/日记图片添加灯箱:', imgSrc);
                        }
                    } else {
                        // 图片没有被链接包围，创建新的灯箱链接
                        var $link = $('<a>');
                        $link.attr('href', imgSrc);
                        $link.attr('data-fancybox', 'auto-gallery');
                        $link.attr('data-caption', $img.attr('alt') || $img.attr('title') || '');
                        $img.wrap($link);
                        console.log('为独立图片创建灯箱链接:', imgSrc);
                    }
                });
                
                // 重新绑定Fancybox
                Fancybox.bind('[data-fancybox]', {
                    animated: true,
                    showClass: 'f-fadeIn',
                    hideClass: 'f-fadeOut',
                    Toolbar: {
                        display: {
                            left: ['infobar'],
                            middle: [],
                            right: ['slideshow', 'zoom', 'fullscreen', 'close']
                        }
                    },
                    Images: {
                        zoom: true,
                        protected: true
                    },
                    l10n: {
                        CLOSE: '关闭',
                        NEXT: '下一张',
                        PREV: '上一张',
                        ERROR: '加载失败，请稍后重试',
                        IMAGE_ERROR: '图片加载失败'
                    }
                });
                
                console.log('灯箱初始化完成');
            }
            
            // 初始处理
            processImages();
            
            // 监听动态内容变化
            var observer = new MutationObserver(function(mutations) {
                var shouldReprocess = false;
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length > 0) {
                        for (var i = 0; i < mutation.addedNodes.length; i++) {
                            var node = mutation.addedNodes[i];
                            if (node.nodeType === 1) { // Element node
                                if (node.tagName === 'IMG' || $(node).find('img').length > 0) {
                                    shouldReprocess = true;
                                    break;
                                }
                            }
                        }
                    }
                });
                
                if (shouldReprocess) {
                    setTimeout(processImages, 100);
                }
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
        
        // 开始初始化
        initLightbox();
    });
    </script>
    <?php
}
add_action('wp_footer', 'kizumi_global_lightbox_init');