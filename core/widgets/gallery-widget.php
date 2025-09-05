<?php
/**
 * 相册小工具
 * @link https://www.mysqil.com
 * @package Kizumi
 */

//www.mysqil.com===安全设置=阻止直接访问主题文件
if(!defined('ABSPATH')){
    echo'Look your sister';
    exit;
}

class kizumi_Gallery_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'kizumi_gallery_widget',
            'kizumi_相册展示',
            array(
                'description' => '显示最新的相册或指定分类的相册'
            )
        );
    }
    
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '最新相册';
        $number = !empty($instance['number']) ? absint($instance['number']) : 6;
        $show_count = !empty($instance['show_count']) ? $instance['show_count'] : false;
        $show_date = !empty($instance['show_date']) ? $instance['show_date'] : false;
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }
        
        $query_args = array(
            'post_type' => 'gallery',
            'posts_per_page' => $number,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC'
        );
        
        $gallery_query = new WP_Query($query_args);
        
        if ($gallery_query->have_posts()) : ?>
            <div class="gallery-widget">
                <div class="gallery-widget-list">
                    <?php while ($gallery_query->have_posts()) : $gallery_query->the_post();
                        $all_gallery_images = kizumi_get_all_gallery_images(get_the_ID());
                        $image_count = count($all_gallery_images);
                        $cover_image = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
                        
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
                        <div class="gallery-widget-item">
                            <div class="gallery-widget-thumb">
                                <a href="<?php the_permalink(); ?>">
                                    <img src="<?php echo esc_url($cover_image); ?>" alt="<?php the_title_attribute(); ?>">
                                    <div class="gallery-widget-overlay">
                                        <i class="fa fa-camera"></i>
                                    </div>
                                </a>
                            </div>
                            <div class="gallery-widget-content">
                                <h4 class="gallery-widget-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h4>
                                <div class="gallery-widget-meta">
                                    <?php if ($show_count && $image_count > 0) : ?>
                                        <span class="gallery-widget-count">
                                            <i class="fa fa-image"></i> <?php echo $image_count; ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($show_date) : ?>
                                        <span class="gallery-widget-date">
                                            <i class="fa fa-calendar"></i> <?php echo get_the_date('m-d'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div class="gallery-widget-more">
                    <a href="<?php echo kizumi_get_gallery_archive_url(); ?>" class="gallery-widget-link">
                        查看更多相册 <i class="fa fa-angle-right"></i>
                    </a>
                </div>
            </div>
            
            <style>
            .gallery-widget-list {
                display: grid;
                gap: 15px;
            }
            
            .gallery-widget-item {
                display: flex;
                gap: 12px;
                align-items: flex-start;
            }
            
            .gallery-widget-thumb {
                position: relative;
                width: 60px;
                height: 60px;
                flex-shrink: 0;
                border-radius: 8px;
                overflow: hidden;
            }
            
            .gallery-widget-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.3s ease;
            }
            
            .gallery-widget-thumb:hover img {
                transform: scale(1.1);
            }
            
            .gallery-widget-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            
            .gallery-widget-thumb:hover .gallery-widget-overlay {
                opacity: 1;
            }
            
            .gallery-widget-content {
                flex: 1;
                min-width: 0;
            }
            
            .gallery-widget-title {
                margin: 0 0 8px 0;
                font-size: 0.9rem;
                font-weight: 600;
                line-height: 1.3;
            }
            
            .gallery-widget-title a {
                color: var(--bs-dark);
                text-decoration: none;
                transition: color 0.3s ease;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
            
            .gallery-widget-title a:hover {
                color: var(--bs-primary);
            }
            
            .gallery-widget-meta {
                display: flex;
                gap: 10px;
                font-size: 0.75rem;
                color: var(--bs-gray-500);
            }
            
            .gallery-widget-meta span {
                display: flex;
                align-items: center;
                gap: 3px;
            }
            
            .gallery-widget-more {
                margin-top: 20px;
                text-align: center;
                padding-top: 15px;
                border-top: 1px solid rgba(0,0,0,0.1);
            }
            
            .gallery-widget-link {
                color: var(--bs-primary);
                text-decoration: none;
                font-size: 0.85rem;
                font-weight: 500;
                transition: all 0.3s ease;
            }
            
            .gallery-widget-link:hover {
                color: var(--bs-primary);
                text-decoration: underline;
            }
            
            /* 暗色主题适配 */
            [data-bs-theme="dark"] .gallery-widget-more {
                border-top-color: rgba(255,255,255,0.1);
            }
            
            [data-bs-theme="dark"] .gallery-widget-title a {
                color: var(--bs-body-color);
            }
            </style>
            
        <?php else : ?>
            <div class="gallery-widget-empty">
                <p>暂无相册</p>
            </div>
        <?php endif;
        
        wp_reset_postdata();
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '最新相册';
        $number = !empty($instance['number']) ? absint($instance['number']) : 6;
        $show_count = !empty($instance['show_count']) ? $instance['show_count'] : false;
        $show_date = !empty($instance['show_date']) ? $instance['show_date'] : false;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">标题:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>">显示数量:</label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" 
                   step="1" min="1" value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        

        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_count); ?> 
                   id="<?php echo esc_attr($this->get_field_id('show_count')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('show_count')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_count')); ?>">显示图片数量</label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_date); ?> 
                   id="<?php echo esc_attr($this->get_field_id('show_date')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('show_date')); ?>">
            <label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>">显示发布日期</label>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 6;
        $instance['show_count'] = !empty($new_instance['show_count']);
        $instance['show_date'] = !empty($new_instance['show_date']);
        
        return $instance;
    }
}

// 注册小工具
function kizumi_register_gallery_widget() {
    register_widget('kizumi_Gallery_Widget');
}
add_action('widgets_init', 'kizumi_register_gallery_widget');
?>