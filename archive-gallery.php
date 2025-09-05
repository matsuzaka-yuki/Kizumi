<?php
/**
 * 相册归档页面模板
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
            <div class="single-category">
                <a href="<?php echo kizumi_get_gallery_archive_url(); ?>" class="tag-cloud" rel="category tag">
                    <i class="tagfa fa fa-camera"></i>相册
                </a>
            </div>
            <h1 class="single-title">相册展示</h1>
            <hr class="horizontal dark">
            
            <?php if (have_posts()) : ?>
                <div class="gallery-archive-container">
                    <div class="gallery-grid">
                        <?php while (have_posts()) : the_post(); 
                            $all_gallery_images = kizumi_get_all_gallery_images(get_the_ID());
                            $image_count = count($all_gallery_images);
                            $cover_image = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                            
                            // 如果没有特色图片，使用第一张相册图片作为封面
                            if (!$cover_image && !empty($all_gallery_images)) {
                                $first_image = $all_gallery_images[0];
                                $cover_image = $first_image['url'];
                            }
                            
                            // 默认封面图片
                            if (!$cover_image) {
                                $cover_image = get_template_directory_uri() . '/assets/images/default-gallery.jpg';
                            }
                        ?>
                            <div class="gallery-card">
                                <div class="gallery-card-image">
                                    <a href="<?php the_permalink(); ?>">
                                        <img src="<?php echo kizumi_lazy_load_images(); ?>" 
                                             data-src="<?php echo esc_url($cover_image); ?>" 
                                             class="lazy" 
                                             alt="<?php the_title_attribute(); ?>">
                                        <div class="gallery-overlay">
                                            <div class="gallery-info">
                                                <i class="fa fa-camera"></i>
                                                <span><?php echo $image_count; ?> 张图片</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="gallery-card-content">
                                    <h3 class="gallery-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <?php if (get_the_excerpt()) : ?>
                                        <p class="gallery-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                                    <?php endif; ?>
                                    <div class="gallery-meta">
                                        <span class="gallery-date">
                                            <i class="fa fa-calendar"></i>
                                            <?php echo get_the_date(); ?>
                                        </span>
                                        <?php 
                                        $categories = get_the_terms(get_the_ID(), 'gallery_category');
                                        if ($categories && !is_wp_error($categories)) : ?>
                                            <span class="gallery-category">
                                                <i class="fa fa-folder"></i>
                                                <?php echo esc_html($categories[0]->name); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <!-- 分页导航 -->
                    <div class="pagination-wrapper">
                        <?php kizumi_pagination(); ?>
                    </div>
                </div>
            <?php else : ?>
                <div class="no-galleries">
                    <div class="empty-state">
                        <i class="fa fa-camera fa-3x"></i>
                        <h3>暂无相册</h3>
                        <p>还没有发布任何相册，请稍后再来查看。</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.gallery-archive-container {
    margin-top: 20px;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.gallery-card {
    background: var(--bs-white);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}

.gallery-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.gallery-card-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.gallery-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-card:hover .gallery-card-image img {
    transform: scale(1.05);
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-card:hover .gallery-overlay {
    opacity: 1;
}

.gallery-info {
    color: white;
    text-align: center;
    font-size: 14px;
}

.gallery-info i {
    display: block;
    font-size: 24px;
    margin-bottom: 5px;
}

.gallery-card-content {
    padding: 20px;
}

.gallery-title {
    margin: 0 0 10px 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.gallery-title a {
    color: var(--bs-dark);
    text-decoration: none;
    transition: color 0.3s ease;
}

.gallery-title a:hover {
    color: var(--bs-primary);
}

.gallery-excerpt {
    color: var(--bs-gray-600);
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 15px;
}

.gallery-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: var(--bs-gray-500);
}

.gallery-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.no-galleries {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    color: var(--bs-gray-400);
    margin-bottom: 20px;
}

.empty-state h3 {
    color: var(--bs-gray-600);
    margin-bottom: 10px;
}

.empty-state p {
    color: var(--bs-gray-500);
}

.pagination-wrapper {
    margin-top: 40px;
    text-align: center;
}

/* 适配主题分页样式 */
.pagination-wrapper .pagenav {
    margin: 0;
}

.pagination-wrapper .pagination {
    justify-content: center;
}

.pagination-wrapper .page-link {
    padding: 8px 12px;
    margin: 0 2px;
    background: var(--bs-white);
    border: 1px solid var(--bs-gray-300);
    border-radius: 4px;
    color: var(--bs-dark);
    text-decoration: none;
    transition: all 0.3s ease;
}

.pagination-wrapper .page-link:hover,
.pagination-wrapper .page-item.active .page-link {
    background: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}

/* 上一页下一页模式样式 */
.pagination-wrapper .pagination-next-prev {
    display: flex;
    justify-content: center;
}

.pagination-wrapper .pagination-next-prev .page-link {
    display: flex;
    align-items: center;
    gap: 5px;
}

/* 响应式设计 */
@media (max-width: 768px) {
    .gallery-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .gallery-card-image {
        height: 180px;
    }
    
    .gallery-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
}

/* 暗色主题适配 */
[data-bs-theme="dark"] .gallery-card {
    background: var(--bs-body-bg);
    border-color: rgba(255,255,255,0.1);
}

[data-bs-theme="dark"] .gallery-card:hover {
    box-shadow: 0 8px 25px rgba(255,255,255,0.1);
}

[data-bs-theme="dark"] .pagination-wrapper .page-numbers {
    background: var(--bs-body-bg);
    border-color: rgba(255,255,255,0.2);
    color: var(--bs-body-color);
}
</style>

<?php
get_sidebar();
get_footer();
?>