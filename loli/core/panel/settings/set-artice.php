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

$options[] = array(
    'name' => __('文章设置', 'ui_kizumi_com'),
    'icon' => 'dashicons-admin-post',
    'type' => 'heading');

    $options[] = array(
        'name' => __('文章新窗口打开开关', 'ui_kizumi_com'),
        'id' => 'kizumi_article_new_window_switch',
        'type' => "checkbox",
        'std' => false,
        'desc' => __('若开启则文章新窗口打开', 'ui_kizumi_com'),
        );
    $options[] = array(
        'name' => __('开启所有文章形式支持', 'ui_kizumi_com'),
        'id' => 'kizumi_article_support_switch',
        'type' => "checkbox",
        'std' => false,
        'desc' => __('若开启则开启所有文章形式支持', 'ui_kizumi_com'),
        );
    $options[] = array(
        'group' => 'start',
        'group_title' => '缩略图尺寸自定义设定',
        'name' => __('缩略图尺寸自定义开关', 'ui_kizumi_com'),
        'id' => 'kizumi_article_thumbnail_size_switch',
        'type' => "checkbox",
        'std' => false,
        );
    $options[] = array(
        'name' => __('缩略图宽度', 'ui_kizumi_com'),
        'id' => 'kizumi_article_thumbnail_width',
        'type' => "text",
        'std' => '300',
        'class' => 'mini',
        );
    $options[] = array(
        'group' => 'end',
        'name' => __('缩略图高度', 'ui_kizumi_com'),
        'id' => 'kizumi_article_thumbnail_height',
        'type' => "text",
        'std' => '200',
        'class' => 'mini',
        );
    $options[] = array(
        'group' => 'start',
        'group_title' => '文章缩略图随机API',
        'name' => __('文章缩略图随机API', 'ui_kizumi_com'),
        'id' => 'kizumi_article_thumbnail_random_api',
        'type' => "checkbox",
        'std' => false,
        'desc' => __('文章缩略图随机API仅在文章没有设置缩略图时生效', 'ui_kizumi_com'),
        );  
    $options[] = array(
        'group' => 'end',
        'name' => __('文章缩略图随机API URL', 'ui_kizumi_com'),
        'id' => 'kizumi_article_thumbnail_random_api_url',
        'type' => "text",
        'class' => '',
        'std' => 'https://api.www.mysqil.com/random.php?size=small',
        'desc' => __('文章缩略图随机API URL', 'ui_kizumi_com'),
        );  
    $options[] = array(
	    'name' => __('文章列表分页模式', 'ui_kizumi_com'),
	    'id' => 'kizumi_article_paging_type',
	    'std' => "multi",
	    'type' => "radio",
	    'options' => array(
		    'next' => __('上一页 和 下一页', 'ui_kizumi_com'),
		    'multi' => __('页码  1 2 3 ', 'ui_kizumi_com'),
            //'loadmore' => __('点击加载更多(未使用)', 'ui_kizumi_com'),
	    ));
    $options[] = array(    
        'group' => 'start',
        'group_title' => '文章打赏&点赞设置',
        'name' => __('点赞开关', 'ui_kizumi_com'),
        'id' => 'kizumi_like_switch',
        'type' => "checkbox",
        'std' => false,
        );        
    $options[] = array(
        'name' => __('文章打赏开关', 'ui_kizumi_com'),
        'id' => 'kizumi_reward_switch',
        'type' => "checkbox",
        'std' => false,
        'desc' => __('若开启则显示打赏按钮', 'ui_kizumi_com'),
        );

    $options[] = array(
        'name' => __('打赏二维码-微信', 'ui_kizumi_com'),
        'id' => 'kizumi_reward_qrcode_weixin',
        'type' => "text",
        'std' => '',
        'desc' => __('打赏二维码-微信二维码地址', 'ui_kizumi_com'),
        );
    $options[] = array(    
        'group' => 'end',
        'name' => __('打赏二维码-支付宝', 'ui_kizumi_com'),
        'id' => 'kizumi_reward_qrcode_alipay',
        'type' => "text",
        'std' => '',
        'desc' => __('打赏二维码-支付宝二维码地址', 'ui_kizumi_com'),
        );

