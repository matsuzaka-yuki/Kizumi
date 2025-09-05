<?php
/**
 * @link https://www.mysqil.com
 * @package Kizumi
 */
//www.mysqil.com===安全设置=阻止直接访问主题文件
if(!defined('ABSPATH')){echo'Look your sister';exit;}
?>
<?php if(get_kizumi('kizumi_blog_layout')=='two'): ?>
    <div class="col-lg-4 blog-sidebar">
          <div class="position-sticky top">
            <div class="offcanvas-lg offcanvas-end" id="blog-sidebar" tabindex="-1" aria-labelledby="blog-sidebar">
              <div class="offcanvas-header">
                <h5 class="offcanvas-title h4" id="blog-sidebar">
                <?php echo kizumi_logo(); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#blog-sidebar"></button>
              </div>
              <div class="offcanvas-body flex-column">
              <?php 
                    if (function_exists('dynamic_sidebar') && dynamic_sidebar('widget_site_sidebar')) : endif; 
                    if (is_single()){
	                        if (function_exists('dynamic_sidebar') && dynamic_sidebar('widget_post_sidebar')) : endif; 
                        }else if (is_page()){
	                        if (function_exists('dynamic_sidebar') && dynamic_sidebar('widget_page_sidebar')) : endif; 
                        }else if (is_home()){
	                        if (function_exists('dynamic_sidebar') && dynamic_sidebar('widget_home_sidebar')) : endif; 
                        }
                ?>
              </div>
            </div>
          </div>
        </div>
<?php endif; ?>