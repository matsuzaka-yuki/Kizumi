<?php
/**
 * 日记页面模板
 * Template Name: 日记
 * @link https://www.mysqil.com
 * @package Kizumi
 */

//www.mysqil.com===安全设置=阻止直接访问主题文件
if(!defined('ABSPATH')){
    echo'Look your sister';
    exit;
}

get_header(); 


?>

<div class="<?php echo kizumi_layout_setting(); ?>">
    <div class="blog-single <?php echo kizumi_border_setting(); ?>">
        <div class="post-single">
            <?php while (have_posts()) : the_post(); ?>
                
                <!-- 日记页面头部 -->
                <div class="diary-header mb-4">
                    <div class="header-content">
                        <div class="header-info">
                            <h1 class="diary-title"><?php the_title(); ?></h1>
                            <p class="diary-subtitle">记录生活中的美好瞬间</p>
                        </div>
                        <div class="header-stats">
                            <div class="stat-item">
                                <span class="stat-number"><?php echo kizumi_get_diary_count(); ?></span>
                                <span class="stat-label">条记录</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 日记内容描述 -->
                <?php if (get_the_content()) : ?>
                    <div class="diary-description mb-4">
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>

                <!-- 日记时间线 -->
                <div class="diary-timeline">
                    <div class="timeline-list">
                        <?php
                        // 查询日记文章
                        $diary_query = new WP_Query(array(
                            'post_type' => 'diary',
                            'post_status' => 'publish',
                            'posts_per_page' => 20,
                            'orderby' => 'date',
                            'order' => 'DESC'
                        ));
                        
                        if ($diary_query->have_posts()) :
                            while ($diary_query->have_posts()) : $diary_query->the_post();
                                $diary_images = kizumi_get_all_diary_images(get_the_ID());
                        ?>
                            <div class="moment-item card-base">
                                <div class="moment-content">
                                    <?php if (get_the_title()) : ?>
                                        <h3 class="moment-title"><?php the_title(); ?></h3>
                                    <?php endif; ?>
                                    
                                    <div class="moment-text"><?php the_content(); ?></div>
                                    
                                    <?php if (!empty($diary_images)) : ?>
                                        <div class="moment-images">
                                            <?php 
                                            $image_count = count($diary_images);
                                            $display_count = min($image_count, 4); // 最多显示4张图片
                                            for ($i = 0; $i < $display_count; $i++) : 
                                                $image = $diary_images[$i];
                                                $full_url = isset($image['full_url']) ? $image['full_url'] : $image['url'];
                                            ?>
                                                <div class="image-item">
                                                    <a href="<?php echo esc_url($full_url); ?>" 
                                                       data-fancybox="diary-<?php echo get_the_ID(); ?>" 
                                                       data-caption="<?php echo esc_attr($image['alt'] ?? $image['caption'] ?? ''); ?>">
                                                        <img src="<?php echo esc_url($image['url']); ?>" 
                                                             alt="<?php echo esc_attr($image['alt'] ?? $image['caption'] ?? ''); ?>"
                                                             loading="lazy">
                                                    </a>
                                                </div>
                                            <?php endfor; ?>
                                            <?php if ($image_count > 4) : ?>
                                                <div class="image-more" data-diary-id="<?php echo get_the_ID(); ?>" data-total-images="<?php echo $image_count; ?>">
                                                    <span>+<?php echo ($image_count - 4); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <hr class="moment-divider">
                                
                                <div class="moment-footer">
                                    <div class="moment-time">
                                        <i class="time-icon">🕐</i>
                                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                            <?php echo kizumi_format_diary_time(get_the_date('Y-m-d H:i:s')); ?>
                                        </time>
                                    </div>
                                    
                                    <div class="moment-meta">
                                        <span class="author">by <?php the_author(); ?></span>
                                        <?php if (!empty($diary_images)) : ?>
                                            <span class="image-count"><?php echo count($diary_images); ?> 张图片</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            endwhile;
                            wp_reset_postdata();
                        else : 
                        ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> 还没有日记记录，开始记录生活的美好吧！
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 底部提示 -->
                <div class="diary-tips">
                    <p><i class="fa fa-heart"></i> 每一个平凡的日子，都值得被记录</p>
                </div>

            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php get_sidebar(); ?>

<style>
/* 日记页面样式 */
.diary-header {
    padding: 2rem;
    border-radius: 12px;
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
}

.diary-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 1;
}

.diary-title {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    color: white;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.diary-subtitle {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
}

.header-stats {
    text-align: center;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: white;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.stat-label {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
}

.diary-description {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.5rem;
    border-radius: 12px;
    border-left: 4px solid #667eea;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.diary-timeline {
    margin-top: 2rem;
}

.timeline-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* Mizuki风格的卡片基础样式 */
.card-base {
    background: var(--card-bg, #fff);
    border: 1px solid var(--line-divider, #e9ecef);
    border-radius: 12px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.moment-item {
    padding: 1.5rem;
    margin-bottom: 1rem;
}

[data-bs-theme="dark"] .moment-item {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.1);
    box-shadow: none;
}

.moment-item:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.moment-content {
    margin-bottom: 1rem;
}

.moment-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text-color, #333);
    margin-bottom: 0.8rem;
    line-height: 1.4;
}

.moment-text {
    font-size: 1rem;
    line-height: 1.7;
    color: var(--text-90, #333);
    margin-bottom: 1rem;
    text-align: justify;
}

.moment-images {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.image-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    aspect-ratio: 1;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.image-item:hover {
    transform: scale(1.05);
}

.image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.moment-divider {
    border: none;
    border-top: 1px solid var(--line-divider, #e9ecef);
    margin: 1rem 0;
}

.moment-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.moment-time {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    color: var(--text-75, #6c757d);
    font-size: 0.875rem;
}

.time-icon {
    font-size: 0.875rem;
}

.diary-mood {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #f8f9fa;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
}

.mood-emoji {
    font-size: 1.2rem;
}

.mood-text {
    color: #495057;
}

.diary-tips {
    text-align: center;
    margin-top: 3rem;
    padding: 1.5rem;
    background: transparent;
    color: #666;
    font-style: italic;
}

.diary-tips i {
    color: #667eea;
    margin-right: 0.5rem;
}

/* 响应式设计 - 与Mizuki主题保持一致 */
/* 手机端 (小于640px) */
@media (max-width: 640px) {
    .diary-header {
        padding: 0.75rem;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
        gap: 0.75rem;
    }
    
    .diary-title {
        font-size: 2rem;
    }
    
    .moment-images {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .moment-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}

/* 平板竖屏 (641px - 900px) */
@media (min-width: 641px) and (max-width: 900px) {
    .diary-header {
        padding: 1.25rem;
    }
    
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .moment-item {
        padding: 1.5rem;
    }
    
    .moment-images {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        max-width: 500px;
    }
    
    .moment-text {
        font-size: 1rem;
        line-height: 1.7;
    }
    
    .moment-footer {
        margin-top: 1rem;
    }
}

/* 平板横屏和桌面端 (大于900px) */
@media (min-width: 901px) {
    .diary-header {
        padding: 1.5rem;
    }
    
    .moment-item {
        padding: 2rem;
    }
    
    .moment-images {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        max-width: 600px;
        gap: 1rem;
    }
    
    .moment-text {
        font-size: 1.1rem;
        line-height: 1.8;
    }
}

/* 优化小屏幕显示 */
@media (max-width: 480px) {
    .card-base {
        margin: 0 -0.5rem;
    }
    
    .moment-item {
        border-radius: 8px;
    }
    
    .diary-header {
        border-radius: 6px;
        padding: 1rem;
        margin: 0 -0.5rem 2rem;
    }
    
    .diary-title {
        font-size: 1.8rem;
    }
}

/* 暗色主题适配 - 使用CSS变量 */
:root {
    --card-bg: #fff;
    --line-divider: #e9ecef;
    --text-90: #333;
    --text-75: #6c757d;
    --primary: #667eea;
}

:root.dark {
    --card-bg: #fff;
    --line-divider: #e9ecef;
    --text-90: #333;
    --text-75: #6c757d;
    --primary: #667eea;
}

:root.dark .diary-title {
    color: #f8f9fa !important;
}

:root.dark .moment-title {
    color: #f8f9fa !important;
}

[data-bs-theme="dark"] {
    --card-bg: #4a5568;
    --line-divider: #495057;
    --text-90: #f8f9fa;
    --text-75: #adb5bd;
    --primary: #0d6efd;
}

[data-bs-theme="dark"] .diary-title {
    color: #f8f9fa !important;
}

[data-bs-theme="dark"] .moment-title {
    color: #f8f9fa !important;
}

[data-bs-theme="dark"] .diary-description {
    background: linear-gradient(135deg, #343a40 0%, #495057 100%);
    color: #f8f9fa;
}

[data-bs-theme="dark"] .diary-mood {
    background: #495057;
    color: #f8f9fa;
}

[data-bs-theme="dark"] .mood-text {
    color: #adb5bd;
}

[data-bs-theme="dark"] .diary-tips {
    background: transparent;
    color: #adb5bd;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 处理"更多图片"按钮点击事件
    jQuery(document).ready(function($) {
        $('.image-more').on('click', function() {
            var diaryId = $(this).data('diary-id');
            var totalImages = $(this).data('total-images');
            
            if (diaryId && totalImages > 4) {
                // 获取当前日记的所有图片
                var $momentImages = $(this).closest('.moment-images');
                var allImageLinks = $momentImages.find('a[data-fancybox="diary-' + diaryId + '"]');
                
                if (allImageLinks.length > 0) {
                    // 触发第一张图片的灯箱，这样用户可以浏览所有图片
                    allImageLinks.first().trigger('click');
                }
            }
        });
        
        // 为"更多图片"按钮添加悬停效果
        $('.image-more').hover(
            function() {
                $(this).css('transform', 'scale(1.1)');
            },
            function() {
                $(this).css('transform', 'scale(1)');
            }
        );
    });
    
    // 平滑滚动到页面顶部
    const backToTop = document.createElement('button');
    backToTop.innerHTML = '<i class="fa fa-arrow-up"></i>';
    backToTop.className = 'back-to-top';
    backToTop.style.cssText = `
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        cursor: pointer;
        opacity: 0;
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    `;
    
    document.body.appendChild(backToTop);
    
    // 滚动显示/隐藏返回顶部按钮
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTop.style.opacity = '1';
        } else {
            backToTop.style.opacity = '0';
        }
    });
    
    // 点击返回顶部
    backToTop.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});
</script>

<?php
get_footer();
?>