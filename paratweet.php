<?php
/*
Plugin Name: Paratweet
Plugin URI: https://bitbucket.com/cftp/paratweet
Description: Make any text shareable with a simple [tweetable] shortcode tag.
Version: 1.0
Author: Scott Evans (Code For The People)
Author URI: http://codeforthepeople.com
Text Domain: paratweet
Domain Path: /assets/languages/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright © 2014 Code for the People ltd

                _____________
               /      ____   \
         _____/       \   \   \
        /\    \        \___\   \
       /  \    \                \
      /   /    /          _______\
     /   /    /          \       /
    /   /    /            \     /
    \   \    \ _____    ___\   /
     \   \    /\    \  /       \
      \   \  /  \____\/    _____\
       \   \/        /    /    / \
        \           /____/    /___\
         \                        /
          \______________________/


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

class paratweet {

	/**
	* Paratweet
	*
	* @author Scott Evans
	*/
	function __construct() {

		add_shortcode( 'tweetable', array( $this, 'tweetable' ) );
		add_action( 'admin_init', array( $this, 'init' ) );

		if (! is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'paratweet_js' ) );
		}
	}

	/**
	 * tweetable
	 *
	 * Shortcode for converting [tweetable][/tweetable] into a shareable paragraph
	 *
	 * @param  string $atts
	 * @param  string $content
	 * @author Scott Evans
	 * @return string
	 */
	function tweetable($atts, $content = "") {

		global $wpdb, $post;

		extract( shortcode_atts(
			array(
				'alt' => '',
				'hashtag' => ''
			),
		$atts, 'tweetable' ) );

		$post_id = $post->ID;
		$permalink = get_permalink($post_id);

		// this is a little aggressive - however it is not destructive
		$tweetcontent = strip_tags($content);

		if ($alt != '') $tweetcontent = $alt;
		if ($hashtag != '') $tweetcontent .= " " . $hashtag;

		$ret  = "<span class='tweetable'>";
		$ret .= "<a target='_blank' href='https://twitter.com/intent/tweet?original_referer=".esc_url_raw($permalink)."&source=tweetbutton&text=".rawurlencode(esc_attr($tweetcontent)) ."&url=".esc_url_raw($permalink)."'>$content&thinsp;<i class='icon-twitter'></i>";
		$ret .= "</a>";
		$ret .= "</span>";

		return $ret;

	}

	/**
	 * init
	 *
	 * Hook into admin init - add tinymce button
	 *
	 * @author Scott Evans
	 * @return void
	 */
	function init() {

		# hooks
		add_action( 'admin_enqueue_scripts', array( $this, 'paratweet_mce_css' ) );
		add_action( 'admin_head', array( $this, 'paratweet_mce' ) );
	}

	/**
	 * paratweet_js
	 *
	 * Load the twitter intent JS locally for opening links in small twitter window
	 *
	 * @author Scott Evans
	 * @return void
	 */
	function paratweet_js() {
		wp_register_script('paratweet-js', plugins_url( '/assets/js/paratweet.js' , __FILE__ ), array(), 1);
		wp_enqueue_script('paratweet-js');
	}

	/**
	 * paratweet_mce_css
	 *
	 * Style the editor button
	 *
	 * @author Scott Evans
	 * @return void
	 */
	function paratweet_mce_css() {
		wp_enqueue_style( 'paratweet', plugins_url( '/assets/css/paratweet.css' , __FILE__ ), 'dashicons', 1, 'screen');
	}

	/**
	 * paratweet_mce
	 *
	 * Load the required tools for adding button, check conditions first
	 *
	 * @author Scott Evans
	 * @return void
	 */
	function paratweet_mce() {

		// check user permissions
		if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
			return;
		}

		// check if WYSIWYG is enabled
		if ( 'true' == get_user_option( 'rich_editing' ) ) {

			# filters
			add_filter( 'mce_buttons', array( $this, 'paratweet_mce_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'paratweet_mce_plugin' ) );
		}
	}

	/**
	 * paratweet_mce_button
	 *
	 * Push a new button on to TinyMCE buttons array
	 *
	 * @author Scott Evans
	 * @param  array $buttons
	 * @return array
	 */
	function paratweet_mce_button( $buttons ) {
		array_push( $buttons, 'paratweet' );
		return $buttons;
	}

	/**
	 * paratweet_mce_plugin
	 *
	 * Add paratweet TinyMCE plugin JS
	 *
	 * @author Scott Evans
	 * @param  array $plugins
	 * @return array
	 */
	function paratweet_mce_plugin( $plugins ) {
		$plugins['paratweet'] = plugins_url( '/assets/js/paratweet-mce.js' , __FILE__ );
		return $plugins;
	}
}

global $paratweet;
$paratweet = new paratweet();
