<?php
/*
Plugin Name: AmazonPress
Plugin URI: http://cj7.co.uk/amazonpress-premium-wp-plugin/
Description: Automatically advertise related products from Amazon.com and Amazon partner sites (US,CN,UK,IT,ES,DE,JP,FR,CA), showing products  related to the topic of your posts, focus on blogging and make extra money in the process. New Amazon API 2012-06-05 从亚马逊自动广告合作伙 伴网站（美国， 署署长， 太平绅士， 阻燃， CA）的显示产品， 具体涉及到的相关帖子主题产品。 轻松添加额外的内容类别或标签上的产品为基础， 并在这个过程中多余的钱。 亚马逊API的要求。
Version: 9.1.1 
Author:  Tom Makela (Fork +8.6: CN,IT,ES,DE,FR,CA,JP + New Amazon API), Warkior
Author URI: http://cj7.co.uk/amazonpress-premium-wp-plugin/
Requires: WordPress Version 2.3 and PHP 5.0.x
Donate link: http://cj7.co.uk/amazonpress-wp-plugin/
Tags: amazon, affiliate amazon, amazon affiliate program, amazon affiliate plugin, automatic content, amazon product, amazon content, monetize related content, amazon widget plugin, related amazon products, amazonpress plugin, amazonfeed, sidebar widget, affiliate products, amazon widget wordpress, amazon product feed, amazon product api, amazon product advertising, wordpress plugin amazon, amazon wordpress plugin, amazon affiliate site, amazon advertising api, automatic amazon products, multisite, make money, Money Maker, afiliate
FAQ link: http://cj7.co.uk/amazonpress-wp-plugin/
****************************************
AmazonPress 8.6.x and forward is a fork of Amazonfeed 2.0.1
Improved by: Tom Makela - https://www.facebook.com/Amazon.Press - AmazonPress Lite http://wordpress.org/extend/plugins/amazonpress/
Version - 9.1 -   Includes China, Italy and Spain, and Amazon API Change, API change.
Version - 9.01 -  Includes Amazon partner sites .com, Canada, Germany, Japan and France
Version - 9.1.1 -  Includes new Amazon partner sites and New Api credentials
Updates and this download can be found at: http://cj7.co.uk/amazonpress-premium-wp-plugin/ or https://www.facebook.com/Amazon.Press
Original source -  http://www.warkensoft.com/php-downloads/amazonfeed-wordpress-plugin/
*/

	include_once("php/amazonpress.class.php");

	if(class_exists("AmazonPress") AND !isset($amazonPressObj))
	{
		$amazonPressObj = new AmazonPress();
		
		/*
		 * Debugging Controls:
		 * Only change these if the plugin is not working properly for you and you want
		 * to try and find out why.
		 * 
		 * debug_mode: choose what level of debugging to use
		 *   options are:
		 *   off 	- nothing is sent to the log
		 *   basic	- basic messages are logged
		 *   all	- all possible debug messages are logged.
		 * 
		 * debug_visible: set this to true if you wish debugging messages to be
		 * visible on the live website as they occur.
		 */
		$amazonPressObj->debug_mode = 'off';
		$amazonPressObj->debug_visible = false;
		
		/**
		 * You shouldn't need to edit anything beyond this point.
		 */
		 
		$amazonPressObj->basePath = dirname(__FILE__);
		$amazonPressObj->baseFileName = basename(__FILE__);
		$amazonPressObj->urlPath = get_option('siteurl') . str_replace(ABSPATH, "/", $amazonPressObj->basePath);

		add_action('admin_menu', array(&$amazonPressObj, 'wp_admin_init'));
		add_action('admin_menu', array(&$amazonPressObj, 'add_custom_box'));
		add_action('admin_notices', array(&$amazonPressObj, 'wp_admin_notices'));
		add_action('save_post',  array(&$amazonPressObj, 'save_postdata'));
		add_action('edit_post',  array(&$amazonPressObj, 'save_postdata'));
		add_action('publish_post',  array(&$amazonPressObj, 'save_postdata'));
		add_action('wp_head', array(&$amazonPressObj, 'wp_head'));
		add_action('admin_head', array(&$amazonPressObj, 'admin_head'));
		add_action('widgets_init', array(&$amazonPressObj, 'widget_init'));	
		register_deactivation_hook( __FILE__, array(&$amazonPressObj, 'unInstall') );
		
		if($amazonPressObj->live)
		{
			add_action('the_content', array(&$amazonPressObj, 'wp_content'));
		}
	}
	
	