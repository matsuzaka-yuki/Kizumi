<?php
/**
 * @link https://www.mysqil.com
 * @package Kizumi
 */
//www.mysqil.com===安全设置=阻止直接访问主题文件
if(!defined('ABSPATH')){echo'Look your sister';exit;}
get_header(); 
get_template_part('page/template/blog-page');
get_sidebar();
get_footer();
?>
