<?php
/**
 * Template Name: 相册页面
 * @link https://www.mysqil.com
 * @package Kizumi
 */

//www.mysqil.com===安全设置=阻止直接访问主题文件
if(!defined('ABSPATH')){
    echo'Look your sister';
    exit;
}

get_header(); ?>

<div class="container">
    <div class="<?php echo kizumi_layout_setting(); ?>">
        <div class="blog-single <?php echo kizumi_border_setting(); ?>">
            <div class="post-single">
            <div class="single-category">
                <a href="<?php echo home_url('/gallery/'); ?>" class="tag-cloud" rel="category tag">
                    <i class="tagfa fa fa-camera"></i>相册
                </a>
            </div>
            <h1 class="single-title">相册展示</h1>
            <hr class="horizontal dark">
            
            <!-- 相册分类筛选 -->
            <div class="gallery-filter">
                <div class="filter-tabs">
                    <a href="<?php echo kizumi_get_gallery_archive_url(); ?>" class="filter-tab active" data-category="">
                        <i class="fa fa-th"></i> 全部
                    </a>
                    <?php
                    $categories = get_terms(array(
                        'taxonomy' => 'gallery_category',
                        'hide_empty' => true
                    ));
                    foreach ($categories as $category) : ?>
                        <a href="<?php echo get_term_link($category); ?>" class="filter-tab" data-category="<?php echo $category->term_id; ?>">
                            <i class="fa fa-folder"></i> <?php echo esc_html($category->name); ?>
                            <span class="count">(<?php echo $category->count; ?>)</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <?php
            // 获取相册列表
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $gallery_query = new WP_Query(array(
                'post_type' => 'gallery',
                'posts_per_page' => 12,
                'paged' => $paged,
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'DESC'
            ));
            
            if ($gallery_query->have_posts()) : ?>
                <div class="gallery-archive-container">
                    <div class="gallery-stats">
                        <span class="gallery-total">
                            <i class="fa fa-camera"></i> 共 <?php echo $gallery_query->found_posts; ?> 个相册
                        </span>
                        <div class="gallery-view-options">
                            <button class="view-option active" data-view="grid" title="网格视图">
                                <i class="fa fa-th"></i>
                            </button>
                            <button class="view-option" data-view="list" title="列表视图">
                                <i class="fa fa-list"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="gallery-grid" id="gallery-container">
                        <?php while ($gallery_query->have_posts()) : $gallery_query->the_post(); 
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
                            
                            $categories = get_the_terms(get_the_ID(), 'gallery_category');
                        ?>
                            <div class="gallery-card" data-categories="<?php echo $categories ? implode(',', wp_list_pluck($categories, 'term_id')) : ''; ?>">
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
                                        <?php if ($categories && !is_wp_error($categories)) : ?>
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
                        <?php
                        echo paginate_links(array(
                            'total' => $gallery_query->max_num_pages,
                            'current' => $paged,
                            'prev_text' => '<i class="fa fa-angle-left"></i> 上一页',
                            'next_text' => '下一页 <i class="fa fa-angle-right"></i>',
                            'type' => 'list',
                        ));
                        ?>
                    </div>
                </div>
            <?php else : ?>
                <div class="no-galleries">
                    <div class="empty-state">
                        <i class="fa fa-camera fa-3x"></i>
                        <h3>暂无相册</h3>
                        <p>还没有发布任何相册，请稍后再来查看。</p>
                        <?php if (current_user_can('publish_posts')) : ?>
                            <a href="<?php echo admin_url('post-new.php?post_type=gallery'); ?>" class="btn btn-primary">
                                <i class="fa fa-plus"></i> 创建相册
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; 
            
            wp_reset_postdata();
            ?>
            </div>
        </div>
    </div>
</div>

<style>
/* 相册筛选器 */
.gallery-filter {
    margin: 20px 0 30px 0;
    padding: 20px;
    background: var(--bs-white);
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border: 1px solid rgba(0,0,0,0.05);
}

.filter-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.filter-tab {
    padding: 8px 16px;
    background: var(--bs-white);
    border: 1px solid var(--bs-gray-300);
    border-radius: 20px;
    color: var(--bs-dark);
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
}

.filter-tab:hover,
.filter-tab.active {
    background: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}

.filter-tab .count {
    font-size: 0.8rem;
    opacity: 0.8;
}

/* 相册统计和视图选项 */
.gallery-stats {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px 0;
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.gallery-total {
    color: var(--bs-gray-600);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

.gallery-view-options {
    display: flex;
    gap: 5px;
}

.view-option {
    padding: 8px 12px;
    background: var(--bs-white);
    border: 1px solid var(--bs-gray-300);
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
    color: var(--bs-gray-600);
}

.view-option:hover,
.view-option.active {
    background: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}

/* 相册网格 */
.gallery-archive-container {
    margin-top: 20px;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
    transition: all 0.3s ease;
}

.gallery-grid.list-view {
    grid-template-columns: 1fr;
}

.gallery-grid.list-view .gallery-card {
    display: flex;
    align-items: center;
}

.gallery-grid.list-view .gallery-card-image {
    width: 120px;
    height: 80px;
    flex-shrink: 0;
    margin-right: 20px;
}

.gallery-grid.list-view .gallery-card-content {
    flex: 1;
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
    margin-bottom: 20px;
}

.pagination-wrapper {
    margin-top: 40px;
    text-align: center;
}

.pagination-wrapper .page-numbers {
    display: inline-block;
    padding: 8px 12px;
    margin: 0 2px;
    background: var(--bs-white);
    border: 1px solid var(--bs-gray-300);
    border-radius: 4px;
    color: var(--bs-dark);
    text-decoration: none;
    transition: all 0.3s ease;
}

.pagination-wrapper .page-numbers:hover,
.pagination-wrapper .page-numbers.current {
    background: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}

/* 响应式设计 */
@media (max-width: 768px) {
    .filter-tabs {
        justify-content: center;
    }
    
    .gallery-stats {
        flex-direction: column;
        gap: 15px;
        align-items: center;
    }
    
    .gallery-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .gallery-grid.list-view .gallery-card {
        flex-direction: column;
    }
    
    .gallery-grid.list-view .gallery-card-image {
        width: 100%;
        height: 180px;
        margin-right: 0;
        margin-bottom: 15px;
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
[data-bs-theme="dark"] .gallery-filter {
    background: var(--bs-body-bg);
    border-color: rgba(255,255,255,0.1);
}

[data-bs-theme="dark"] .filter-tab {
    background: var(--bs-body-bg);
    border-color: rgba(255,255,255,0.2);
    color: var(--bs-body-color);
}

[data-bs-theme="dark"] .view-option {
    background: var(--bs-body-bg);
    border-color: rgba(255,255,255,0.2);
    color: var(--bs-body-color);
}

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

<script>
jQuery(document).ready(function($) {
    // 视图切换
    $('.view-option').click(function() {
        var view = $(this).data('view');
        
        $('.view-option').removeClass('active');
        $(this).addClass('active');
        
        var container = $('#gallery-container');
        container.removeClass('list-view grid-view');
        
        if (view === 'list') {
            container.addClass('list-view');
        } else {
            container.addClass('grid-view');
        }
    });
    
    // 分类筛选（如果需要AJAX筛选功能）
    $('.filter-tab').click(function(e) {
        if ($(this).hasClass('active')) {
            return;
        }
        
        $('.filter-tab').removeClass('active');
        $(this).addClass('active');
        
        // 这里可以添加AJAX筛选逻辑
        // 目前使用链接跳转的方式
    });
    
    // 懒加载图片
    if ('IntersectionObserver' in window) {
        var imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img.lazy').forEach(function(img) {
            imageObserver.observe(img);
        });
    }
});
</script>

<?php
get_footer();
?>