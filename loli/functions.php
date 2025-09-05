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
//www.mysqil.com===自定义代码



add_action('wp_ajax_nopriv_user_signup_action', function () {
    // 先验证 nonce
    check_ajax_referer('user_signup', 'signup_nonce');

    // 验证数学答案
    $math_answer  = intval($_POST['math_answer'] ?? 0);
    $math_check   = intval($_POST['math_check']  ?? 0);

    if ($math_answer !== $math_check) {
        wp_send_json_error(['message' => '数学验证答案错误']);
    }

    /* 下面继续你原来的注册逻辑即可 … */
});

function add_recommended_meta_box() {
    add_meta_box(
        'recommended_meta_box',           
        '推荐设置',                       
        'recommended_meta_box_callback',  
        'post',                          
        'side',                          
        'default'                         
    );
}
add_action('add_meta_boxes', 'add_recommended_meta_box');


function recommended_meta_box_callback($post) {
    
    $recommended_category = get_post_meta($post->ID, '_recommended_category', true);


    $options = [
        '推荐栏目 1',
        '推荐栏目 2',
        '推荐栏目 3',
        '推荐栏目 4',
        '取消推荐'  
    ];

    if (empty($recommended_category)) {
        $recommended_category = '取消推荐';
    }

    echo '<label for="recommended_category">选择推荐栏目</label>';
    echo '<select name="recommended_category" id="recommended_category" class="postbox">';
    foreach ($options as $option) {
        echo '<option value="' . esc_attr($option) . '" ' . selected($recommended_category, $option, false) . '>' . esc_html($option) . '</option>';
    }
    echo '</select>';
}


function save_recommended_meta_box($post_id) {
  
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
    if (!isset($_POST['recommended_category'])) return $post_id;


    $recommended_category = sanitize_text_field($_POST['recommended_category']);


    $current_recommended_category = get_post_meta($post_id, '_recommended_category', true);

   
    if ($recommended_category === '取消推荐') {
        delete_post_meta($post_id, '_recommended_category'); 
    } else {
        
        if ($recommended_category !== $current_recommended_category) {
             $args = array(
                'post_type' => 'post',
                'meta_key' => '_recommended_category',
                'meta_value' => $recommended_category,
                'posts_per_page' => 1 
            );
            $existing_query = new WP_Query($args);

            if ($existing_query->have_posts()) {
                
                while ($existing_query->have_posts()) : $existing_query->the_post();
                    delete_post_meta(get_the_ID(), '_recommended_category'); 
                endwhile;
                wp_reset_postdata();
            }

          
            update_post_meta($post_id, '_recommended_category', $recommended_category); 
        }
    }

    return $post_id;
}
add_action('save_post', 'save_recommended_meta_box');

//添加站点统计小工具 
include("widget-websitestat.php");
//添加禁止插件模块
include("jz-function.php");


function register_software_app_post_type() {
    $args = array(
        'label'               => '软件应用',
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'capability_type'     => 'post',
        'hierarchical'        => false,
        'rewrite'             => array( 'slug' => 'software-app' ),
        'supports'            => array( 'title', 'editor', 'thumbnail' ),
        'taxonomies'          => array( 'category' ), 
        'show_in_rest'        => true, 
    );
    register_post_type( 'software_app', $args );
}
add_action( 'init', 'register_software_app_post_type' );

function add_software_app_meta_boxes() {
    add_meta_box( 'software_app_details', '软件应用详情', 'render_software_app_meta_boxes', 'software_app', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'add_software_app_meta_boxes' );


function render_software_app_meta_boxes($post) {

    $download_url = get_post_meta($post->ID, '_download_url', true);
    $app_icon = get_post_meta($post->ID, '_app_icon', true);

    ?>
    <p>
        <label for="download_url">下载地址：</label>
        <input type="text" name="download_url" id="download_url" value="<?php echo esc_attr($download_url); ?>" class="widefat">
    </p>
    <p>
        <label for="app_icon">应用图标：</label>
        <input type="text" name="app_icon" id="app_icon" value="<?php echo esc_attr($app_icon); ?>" class="widefat">
    </p>
    <?php
}


function save_software_app_meta_boxes($post_id) {
    if (array_key_exists('download_url', $_POST)) {
        update_post_meta($post_id, '_download_url', sanitize_text_field($_POST['download_url']));
    }
    if (array_key_exists('app_icon', $_POST)) {
        update_post_meta($post_id, '_app_icon', sanitize_text_field($_POST['app_icon']));
    }
}
add_action('save_post', 'save_software_app_meta_boxes');

function create_software_app_taxonomy() {
    $args = array(
        'hierarchical' => true, // 类似分类（可以有子分类）
        'label'         => '应用分类',
        'show_ui'        => true,
        'show_in_rest'   => true, // 支持 Gutenberg 编辑器
        'show_admin_column' => true,
        'query_var'      => true,
        'rewrite'        => array( 'slug' => 'app-category' ),
    );
    register_taxonomy( 'software_category', 'software_app', $args );
}
add_action( 'init', 'create_software_app_taxonomy' );