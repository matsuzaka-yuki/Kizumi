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
    'name' => __('静态加速', 'ui_kizumi_com'),
    'icon' => 'dashicons-performance',
    'type' => 'heading'); 
    $options[] = array(
        'group' => 'start',
        'group_title' => '静态资源加速设置项',
        'name' => __('静态资源加速开关', 'ui_kizumi_com'),
        'id' => 'kizumi_cdn_assets_switch',
        'type' => "checkbox",
        'std' => false,
        );
    $options[] = array(
        'group' => 'end',
        'name' => __('静态资源加速url', 'ui_kizumi_com'),
        'id' => 'kizumi_cdn_assets_url',
        'type' => "text",
        'std' => '',
        'desc' => __('(如https://domain.com/Kizumi/assets)，链接结尾不要带"/"', 'ui_kizumi_com'),
        );
	$gravatar_array = array(
		'cravatar' => __('cravatar源', 'ui_kizumi_com'),
        'weavatar' => __('cravatar备用源', 'ui_kizumi_com'),
		'qiniu' => __('七牛源', 'ui_kizumi_com'),
		'geekzu' => __('极客源', 'ui_kizumi_com'),
		'v2excom' => __('v2ex源', 'ui_kizumi_com'),
		'cn' => __('默认CN源', 'ui_kizumi_com'),
		'ssl' => __('默认SSL源', 'ui_kizumi_com'),
	);
    $options[] = array(
        'group' => 'start',
        'group_title' => '前端头像加速服务器',
        'name' => __('Gravatar头像', 'ui_kizumi_com'),
        'desc' => __('（通过镜像服务器可对gravatar头像进行加速）', 'ui_kizumi_com'),
        'id' => 'kizumi_gravatar_url',       
        'std' => 'lolinet',
        'type' => 'select',
        'class' => 'mini', //mini, tiny, small
        'options' => $gravatar_array);
    
    $qqravatar_array = array(
		'Q1' => __('QQ官方服务器1', 'ui_kizumi_com'),
		'Q2' => __('QQ官方服务器2', 'ui_kizumi_com'),
		'Q3' => __('QQ官方服务器3', 'ui_kizumi_com'),
		'Q4' => __('QQ官方服务器4', 'ui_kizumi_com'),	
	);    
    $options[] = array(
        'name' => __('QQ头像', 'ui_kizumi_com'),
        'desc' => __('（如果用户是QQ邮箱则调用QQ头像）', 'ui_kizumi_com'),
        'id' => 'kizumi_qqavatar_url',
        'group' => 'end',
        'std' => 'Q2',
        'type' => 'select',
        'class' => 'mini', //mini, tiny, small
        'options' => $qqravatar_array);	