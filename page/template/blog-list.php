<?php
/**
 * @link https://www.mysqil.com
 * @package Kizumi
 */
//www.mysqil.com===安全设置=阻止直接访问主题文件
if(!defined('ABSPATH')){echo'Look your sister';exit;}
?>
<style>


[data-bs-theme="dark"] .recommended-category,
[data-bs-theme="dark"] .special-category,
[data-bs-theme="dark"] .software-category,
[data-bs-theme="dark"] .software-card {
    background-color: #ffffff4a !important;
}



.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
    border: none;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2) !important;
}

/* 推荐区域效果 */
.zone-1, .zone-2, .zone-3, .zone-4 {
    position: relative;
    width: 100%;
    height: 300px;
    overflow: hidden;
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.zone-1 img, .zone-2 img, .zone-3 img, .zone-4 img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
    border-radius: 10px;
}
.zone-1 img:hover, .zone-2 img:hover, .zone-3 img:hover, .zone-4 img:hover {
    transform: scale(1.05);
}

/* 顶部标签样式 */
.top-label {
    position: absolute;
    top: 10px;
    left: 10px;
    background-color: crimson;
    color: #fff;
    padding: 8px 16px;
    font-size: 20px;
    font-family: 'Comic Sans MS', sans-serif;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    text-transform: uppercase;
    letter-spacing: 2px;
    animation: bounce 1s ease infinite;
    z-index: 1;
    transition: all 0.3s ease;
}

/* 分类标题效果 */
.special-category, .recommended-category, .software-category {
    background-color: rgba(255, 255, 255, 0.8);
    padding: 10px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    margin: 20px 0;
}
.special-category:hover, .recommended-category:hover, .software-category:hover {
    background-color: rgba(255, 255, 255, 1);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    transform: scale(1.02);
}

/* APP软件卡片特殊效果 */
.software-card {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    border: none;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
}
.software-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
}
.software-card .card-img-top {
    transition: transform 0.5s ease;
}
.software-card:hover .card-img-top {
    transform: scale(1.1);
}
.software-card .btn {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.software-card .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}
.software-card .btn:after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 5px;
    height: 5px;
    background: rgba(255, 255, 255, 0.5);
    opacity: 0;
    border-radius: 100%;
    transform: scale(1, 1) translate(-50%);
    transform-origin: 50% 50%;
}
.software-card .btn:focus:after,
.software-card .btn:hover:after {
    animation: ripple 1s ease-out;
}

/* 动画效果 */
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}
@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.2); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}
@keyframes shineAndMove {
    0% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0); }
}
@keyframes ripple {
    0% {
        transform: scale(0, 0);
        opacity: 1;
    }
    20% {
        transform: scale(25, 25);
        opacity: 1;
    }
    100% {
        opacity: 0;
        transform: scale(40, 40);
    }
}

/* 响应式调整 */
@media (max-width: 768px) {
    .zone-1, .zone-2, .zone-3, .zone-4 {
        height: 200px;
    }
    .top-label {
        font-size: 14px;
        padding: 5px 10px;
    }
    .card {
        margin-bottom: 15px;
    }
    .software-card {
        margin-bottom: 20px;
    }
}
</style>


<div class="<?php echo kizumi_layout_setting(); ?> blog-post">
<?php while ( have_posts() ) : the_post(); ?>
  <article class="post-list list-one row <?php echo kizumi_border_setting(); ?>">
    <div class="post-list-img">
      <figure class="mb-4 mb-lg-0 zoom-img">
        <a <?php echo kizumi_article_new_window(); ?> href="<?php echo get_the_permalink(); ?>" title="<?php echo get_the_title().get_the_subtitle(false).kizumi_title_link().get_bloginfo('name')?>">
          <img src="<?php kizumi_lazy_load_images(); ?>" data-src="<?php echo kizumi_article_thumbnail_src(); ?>?id<?php echo get_the_ID(); ?>" alt="<?php the_title(); ?>" class="img-fluid rounded-3 lazy"></a>
      </figure>

    </div>
    <div class="post-list-content">
      <div class="category">
        <div class="tags">
          <?php 
          $categories = get_the_category();
          if (!empty($categories)) {
              foreach ($categories as $category) {
                  echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" rel="category tag">';
                  echo '<i class="tagfa fa fa-dot-circle-o"></i>' . esc_html($category->name);
                  echo '</a> ';
              }
          }
          $tags = get_the_tags();
          if (!empty($tags)) {
              foreach ($tags as $tag) {
                  echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '" rel="tag">';
                  echo '<i class="tagfa fa fa-tag"></i>' . esc_html($tag->name);
                  echo '</a> ';
              }
          }
          ?>
        </div>
      </div>
      <div class="mt-2 mb-2">
        <h3 class="post-title h4">
          <a href="<?php echo get_the_permalink(); ?>" title="<?php echo get_the_title().get_the_subtitle(false).kizumi_title_link().get_bloginfo('name')?>" class="text-reset"><?php echo get_the_title(); ?></a></h3>
        <p class="post-content"><?php echo _get_excerpt(); ?></p></div>
      <div class="post-meta align-items-center">
        <div class="post-list-avatar">
        <img src="<?php echo kizumi_lazy_load_images(); ?>" data-src="<?php echo kizumi_get_avatar_url(get_the_author_meta('ID'), 100); ?>" alt="avatar" class="avatar lazy">
            </div>
        <div class="post-meta-info">
          <div class="post-meta-stats">
            <span class="list-post-view">
              <i class="fa fa-street-view"></i><?php echo getPostViews(get_the_ID()); ?></span>
            <span class="list-post-comment">
              <i class="fa fa-comments-o"></i><?php echo get_comments_number(); ?></span>
          </div>
          <span class="list-post-author">
            <i class="fa fa-at"></i><?php echo get_the_author(); ?>
            <span class="dot"></span><?php echo get_the_date(); ?></span>
        </div>
      </div>
    </div>
  </article>
  
<?php endwhile; ?>

  <div class="col-lg-12 col-md-12 pagenav">
    <?php kizumi_pagination(); ?>            
  </div>
</div>

