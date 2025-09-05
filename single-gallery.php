<?php
/**
 * 相册详情页面模板
 * @link https://www.mysqil.com
 * @package Kizumi
 */

//www.mysqil.com===安全设置=阻止直接访问主题文件
if(!defined('ABSPATH')){
    echo'Look your sister';
    exit;
}

get_header(); ?>

<div class="<?php echo kizumi_layout_setting(); ?>">
    <div class="blog-single <?php echo kizumi_border_setting(); ?>">
        <div class="post-single">
            <?php while (have_posts()) : the_post(); ?>
                <?php
                $gallery_id = get_the_ID();
                
                // 获取所有图片（本地+外链）
                $all_gallery_images = kizumi_get_all_gallery_images($gallery_id);
                
                // 调试信息
                $external_images_raw = get_post_meta($gallery_id, '_gallery_external_images', true);
                $external_images = kizumi_get_gallery_external_images($gallery_id);
                
                // 如果没有找到图片，尝试其他方法获取
                if (empty($all_gallery_images)) {
                    $gallery_images = get_post_meta($gallery_id, '_gallery_images', true);
                    
                    if (empty($gallery_images)) {
                        // 尝试从文章内容中提取图片
                        $content = get_the_content();
                        if (has_shortcode($content, 'gallery')) {
                            // 如果有WordPress原生gallery短代码
                            preg_match('/\[gallery[^\]]*ids="([^"]*)"/', $content, $matches);
                            if (!empty($matches[1])) {
                                $gallery_images = explode(',', $matches[1]);
                                $gallery_images = array_map('intval', $gallery_images);
                            }
                        }
                        
                        // 如果还是没有，尝试获取附件
                        if (empty($gallery_images)) {
                            $attachments = get_attached_media('image', $gallery_id);
                            if (!empty($attachments)) {
                                $gallery_images = array_keys($attachments);
                            }
                        }
                    }
                    
                    // 转换为统一格式
                    $all_gallery_images = array();
                    if (is_array($gallery_images)) {
                        foreach ($gallery_images as $image_id) {
                            $all_gallery_images[] = array(
                                'type' => 'local',
                                'id' => $image_id,
                                'url' => wp_get_attachment_image_url($image_id, 'medium'),
                                'full_url' => wp_get_attachment_image_url($image_id, 'full'),
                                'caption' => wp_get_attachment_caption($image_id)
                            );
                        }
                    }
                }
                
                ?>
                
                <div class="gallery-back-nav mb-4">
                    <a href="<?php echo kizumi_get_gallery_archive_url(); ?>" class="back-to-gallery">
                        <i class="fa fa-arrow-left"></i> 返回相册
                    </a>
                </div>

                <!-- 相册标题和信息 -->
                <div class="gallery-header mb-4">
                    <h1 class="gallery-title"><?php the_title(); ?></h1>
                    <div class="gallery-meta">
                        <div class="gallery-info">
                            <span class="gallery-author">
                                <i class="fa fa-user"></i>
                                作者: <?php the_author(); ?>
                            </span>
                            <span class="gallery-date">
                                <i class="fa fa-calendar"></i>
                                <?php the_date(); ?>
                            </span>
                            <?php if (current_user_can('edit_posts')) : ?>
                                <?php edit_post_link('编辑', '<span class="edit-link"><i class="fa fa-edit"></i> ', '</span>'); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 相册描述 -->
                <?php if (get_the_content()) : ?>
                    <div class="gallery-description mb-4">
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>





                <!-- 相册图片 -->
                <?php if (!empty($all_gallery_images)) : ?>
                    <div class="gallery-container">
                        <!-- 工具栏 -->
                        <div class="alert alert-info mb-3" role="alert">
                            <i class="fa fa-picture-o"></i> 共 <?php echo count($all_gallery_images); ?> 张图片
                        </div>

                        <!-- 图片瀑布流 -->
                        <div class="gallery-images masonry">
                            <?php foreach ($all_gallery_images as $image_data) : ?>
                                <div class="gallery-item" data-type="<?php echo esc_attr($image_data['type']); ?>">
                                    <div class="gallery-image-wrapper">
                                        <a href="<?php echo esc_url($image_data['full_url'] ?? $image_data['url']); ?>" 
                                           data-fancybox="gallery-detail" 
                                           data-caption="<?php echo esc_attr($image_data['caption']); ?>">
                                            <img src="<?php echo esc_url($image_data['url']); ?>" 
                                                 alt="<?php echo esc_attr($image_data['caption']); ?>" 
                                                 class="gallery-image-thumb"
                                                 loading="lazy">
                                        </a>
                                    </div>
                                    <?php if ($image_data['caption']) : ?>
                                        <div class="gallery-caption"><?php echo esc_html($image_data['caption']); ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> 此相册暂无图片。
                    </div>
                <?php endif; ?>

                <!-- 相册导航 -->
                <div class="gallery-navigation mt-5">
                    <div class="row">
                        <?php
                        $prev_post = get_previous_post();
                        $next_post = get_next_post();
                        ?>
                        <div class="col-md-6">
                            <?php if ($prev_post) : ?>
                                <a href="<?php echo get_permalink($prev_post->ID); ?>" class="btn btn-outline-primary">
                                    <i class="fa fa-angle-left"></i>
                                    上一个相册: <?php echo get_the_title($prev_post->ID); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 text-end">
                            <?php if ($next_post) : ?>
                                <a href="<?php echo get_permalink($next_post->ID); ?>" class="btn btn-outline-primary">
                                    下一个相册: <?php echo get_the_title($next_post->ID); ?>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php get_sidebar(); ?>

<style>
/* 相册样式 */
.gallery-header {
    border-bottom: 1px solid #eee;
    padding-bottom: 20px;
}

.gallery-title {
    margin-bottom: 10px;
    color: #333;
}

.gallery-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.gallery-info span {
    margin-right: 20px;
    color: #666;
    font-size: 14px;
}

.gallery-info i {
    margin-right: 5px;
}

.gallery-description {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}





.gallery-images.masonry {
    display: block;
    column-count: 3;
    column-gap: 20px;
    margin-top: 20px;
}

.gallery-item {
    break-inside: avoid;
    margin-bottom: 20px;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.gallery-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.gallery-image-wrapper {
    position: relative;
    overflow: hidden;
}

.gallery-image-thumb {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.3s ease;
}

.gallery-item:hover .gallery-image-thumb {
    transform: scale(1.05);
}

.external-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(0, 123, 255, 0.9);
    color: white;
    padding: 4px 6px;
    border-radius: 12px;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.external-badge i {
    font-size: 10px;
}

.gallery-caption {
    padding: 15px;
    font-size: 14px;
    color: #666;
    text-align: center;
}

.gallery-navigation {
    border-top: 1px solid #eee;
    padding-top: 30px;
}

/* 响应式设计 */
@media (max-width: 768px) {
    .gallery-images.masonry {
        column-count: 2;
    }
}

@media (max-width: 480px) {
    .gallery-images.masonry {
        column-count: 1;
    }
}

/* 暗色主题适配 */
[data-bs-theme="dark"] .gallery-header {
    border-bottom-color: #495057;
}

[data-bs-theme="dark"] .gallery-title {
    color: #f8f9fa;
}

[data-bs-theme="dark"] .gallery-info span {
    color: #adb5bd;
}

[data-bs-theme="dark"] .gallery-description {
    background: linear-gradient(135deg, #343a40 0%, #495057 100%);
    border-left-color: #0d6efd;
    color: #f8f9fa;
}



[data-bs-theme="dark"] .gallery-item {
    background: #343a40;
    box-shadow: 0 2px 8px rgba(0,0,0,0.4);
    border: 1px solid #495057;
}

[data-bs-theme="dark"] .gallery-item:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.6);
    border-color: #6c757d;
}

[data-bs-theme="dark"] .gallery-caption {
    color: #adb5bd;
    background: rgba(33,37,41,0.9);
}

[data-bs-theme="dark"] .gallery-navigation {
    border-top-color: #495057;
}

[data-bs-theme="dark"] .breadcrumb {
    background: rgba(33,37,41,0.5);
}

[data-bs-theme="dark"] .breadcrumb-item a {
    color: #0d6efd;
}

[data-bs-theme="dark"] .breadcrumb-item.active {
    color: #adb5bd;
}

[data-bs-theme="dark"] .external-badge {
    background: rgba(13, 110, 253, 0.9);
}


</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 瀑布流布局初始化
    function initMasonryLayout() {
        var container = document.querySelector('.gallery-images.masonry');
        if (container) {
            // 确保瀑布流布局正确显示
            var items = container.querySelectorAll('.gallery-item');
            items.forEach(function(item) {
                item.style.breakInside = 'avoid';
                item.style.pageBreakInside = 'avoid';
            });
        }
    }
    
    // 初始化瀑布流
    initMasonryLayout();
    
    // 图片加载完成后重新调整布局
    var images = document.querySelectorAll('.gallery-image-thumb');
    var loadedCount = 0;
    
    images.forEach(function(img) {
        if (img.complete) {
            loadedCount++;
            if (loadedCount === images.length) {
                setTimeout(initMasonryLayout, 100);
            }
        } else {
            img.addEventListener('load', function() {
                loadedCount++;
                if (loadedCount === images.length) {
                    setTimeout(initMasonryLayout, 100);
                }
            });
        }
    });
});
</script>

<?php
get_footer();
?>