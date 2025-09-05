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
    'name' => __( 'Banner设置', 'ui_kizumi_com' ),
    'icon' => 'dashicons-format-gallery',
    'desc' => __( '（导航下的图片设置）', 'ui_kizumi_com' ),
    'type' => 'heading'
);
    $options[] = array(
        'group' => 'start',
		'group_title' => 'Banner欢迎语一言设置',
		'name' => __( 'Banner欢迎语', 'ui_kizumi_com' ),
		'desc' => __('（留空则不显示）', 'ui_kizumi_com'),
		'id' => 'kizumi_banner_font',
		'std' => 'Hello! Beautiful Kizumi！',
		'type' => 'text');
    $options[] = array(
		'name' => __('banner一言开关', 'ui_kizumi_com'),
		'id' => 'kizumi_banner_hitokoto_switch',
		'type' => "checkbox",
		'std' => false,
		);
        $hitokoto_array = array(
			'a' => __('动画', 'ui_kizumi_com'),
			'b' => __('漫画', 'ui_kizumi_com'),
			'c' => __('游戏', 'ui_kizumi_com'),
			'd' => __('文学', 'ui_kizumi_com'),
			'e' => __('原创', 'ui_kizumi_com'),
			'f' => __('来自网络', 'ui_kizumi_com'),	
			'g' => __('其他', 'ui_kizumi_com'),
			'h' => __('影视', 'ui_kizumi_com'),
			'i' => __('诗词', 'ui_kizumi_com'),
			'j' => __('网易云', 'ui_kizumi_com'),
			'k' => __('哲学', 'ui_kizumi_com'),
		);
    $options[] = array(
        'group' => 'end',
		'name' => __('选择一言句子类型', 'ui_kizumi_com'),
		'id' => 'kizumi_banner_hitokoto_text',
		'type' => 'select',
		'options' => $hitokoto_array);
    $options[] = array(
        'group' => 'start',
		'group_title' => '自定义banner高度开关',
		'id' => 'kizumi_banner_height_switch',
		'type' => "checkbox",
		'std' => false,
		);
    $options[] = array(
		'name' => __( '[PC端]Banner高度 留空则默认580', 'ui_kizumi_com' ),
		'id' => 'kizumi_banner_height',
		'std' => '580',
		'class' => 'mini',
		'type' => 'text');
	$options[] = array(
		'name' => __( '[手机端]Banner高度 留空默认480', 'ui_kizumi_com' ),
		'id' => 'kizumi_banner_height_m',
		'std' => '480',
		'class' => 'mini',
		'group' => 'end',
		'type' => 'text');	
    $options[] = array(
		'name' => __('自定义Banner背景图', 'ui_kizumi_com'),
		'id' => 'kizumi_banner_url',
		'std' => $image_path.'banner.jpg',
		'type' => 'upload');
    $options[] = array(
		'group' => 'start',
		'group_title' => 'Banner随机图片',
		'name' => __('Banner开启本地随机图片', 'ui_kizumi_com'),
		'desc' => __('（自动检索主题目录/assets/images/banner/random的图片资源）', 'ui_kizumi_com'),
		'id' => 'kizumi_banner_rand_switch',
		'class' => 'mini',
        'std' => false,
		'type' => 'checkbox');
    $options[] = array(
		'name' => __('使用外链APi-Banner图片', 'ui_kizumi_com'),
		'desc' => __('（开启后上方本地设置图片功能全失效）', 'ui_kizumi_com'),		
		'id' => 'kizumi_banner_api_switch',
		'type' => "checkbox",
		'std' => false,
		);
	$options[] = array(
        'group' => 'end',
		'name' => __('图片外链APi链接', 'ui_kizumi_com'),
		'id' => 'kizumi_banner_api_url',
		'std' => 'https://github.com/matsuzaka-yuki/PicFlow-API',
		'type' => 'text');