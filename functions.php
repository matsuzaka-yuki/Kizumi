<?php
/**
 * @link https://www.mysqil.com
 * @package Kizumi
 */

//www.mysqil.com===安全设置=阻止直接访问主题文件
if(!defined('ABSPATH')){
    echo'Look your sister';
    exit;
}
//时区设置
date_default_timezone_set('Asia/Shanghai');

//www.mysqil.com===加载面板
define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/core/panel/' );
require_once dirname( __FILE__ ) . '/core/panel/options-framework.php';
require_once dirname( __FILE__ ) . '/options.php';
require_once dirname( __FILE__ ) . '/core/panel/options-framework-js.php';
//www.mysqil.com===功能模块
require_once  get_stylesheet_directory() . '/core/module/fun-basis.php';
require_once  get_stylesheet_directory() . '/core/module/fun-admin.php';
require_once  get_stylesheet_directory() . '/core/module/fun-optimize.php';
require_once  get_stylesheet_directory() . '/core/module/fun-gravatar.php';
require_once  get_stylesheet_directory() . '/core/module/fun-navwalker.php';
require_once  get_stylesheet_directory() . '/core/module/fun-user.php';
require_once  get_stylesheet_directory() . '/core/module/fun-user-center.php';
require_once  get_stylesheet_directory() . '/core/module/fun-comments.php';
require_once  get_stylesheet_directory() . '/core/module/fun-seo.php';
require_once  get_stylesheet_directory() . '/core/module/fun-article.php';
require_once  get_stylesheet_directory() . '/core/module/fun-smtp.php';
require_once  get_stylesheet_directory() . '/core/module/fun-msg.php';
require_once  get_stylesheet_directory() . '/core/module/fun-no-category.php';
require_once  get_stylesheet_directory() . '/core/module/fun-shortcode.php';
require_once get_stylesheet_directory() . '/core/module/fun-gallery.php';
require_once get_stylesheet_directory() . '/core/widgets/gallery-widget.php';
require_once get_stylesheet_directory() . '/core/module/fun-lightbox.php';
//www.mysqil.com===自定义代码

// 技能管理系统
require_once get_stylesheet_directory() . '/core/module/fun-skills-management.php';

// 日记功能系统
require_once get_stylesheet_directory() . '/core/module/fun-diary.php';

// F12禁用功能实现（参考loli主题）
function kizumi_disable_functions() {
    // 如果是管理员，不执行禁用功能
    if (current_user_can('administrator')) {
        return;
    }
    
    // 检查是否启用F12禁用功能
    if (get_kizumi('kizumi_disable_f12_non_admin') == '1') {
        kizumi_disable_f12();
    }
}

// 禁止F12功能
function kizumi_disable_f12() {
    echo '<script type="text/javascript">
        document.addEventListener("keydown", function(e) {
            // F12
            if (e.keyCode == 123) {
                e.preventDefault();
                return false;
            }
            // Ctrl+Shift+I
            if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
                e.preventDefault();
                return false;
            }
            // Ctrl+Shift+C
            if (e.ctrlKey && e.shiftKey && e.keyCode == 67) {
                e.preventDefault();
                return false;
            }
            // Ctrl+U
            if (e.ctrlKey && e.keyCode == 85) {
                e.preventDefault();
                return false;
            }
        });
        
        // 禁用右键菜单
        document.addEventListener("contextmenu", function(e) {
            e.preventDefault();
            return false;
        });
        
        // 检测开发者工具
        setInterval(function() {
            if (window.outerHeight - window.innerHeight > 200 || window.outerWidth - window.innerWidth > 200) {
                document.body.innerHTML = "<h1 style=\'text-align:center;margin-top:200px;\'>检测到开发者工具，页面已被禁用！</h1>";
            }
        }, 500);
    </script>';
}

// 启用禁用功能
add_action('wp_head', 'kizumi_disable_functions');

