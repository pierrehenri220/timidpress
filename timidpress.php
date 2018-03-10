<?php
  if ( !defined('ABSPATH')){ exit; } // Exit if accessed directly
   /*
   Plugin Name: Timid Press
   Plugin URI: https://github.com/pierrehenri220/timidpress
   Version: 0.1
   Author: Pierre-Henri Lavigne
   Author URI: https://une.grenouille220.com
   License: GPL2
   */

function timidpress_init() {

  global $wp;

  remove_action( 'wp_head', 'wp_resource_hints', 2 );
  remove_action( 'wp_head', 'feed_links', 2 );
  remove_action( 'wp_head', 'feed_links_extra', 3 );
  remove_action( 'wp_head', 'wlwmanifest_link' );
  remove_action( 'wp_head', 'wp_shortlink_wp_head' );
  remove_action( 'wp_head', 'wp_generator' );
  remove_action( 'wp_head', 'rel_canonical' );

  ####
  # Disable Emojis
  # Borrow from Ryan Hellyer's Disable Emojis plugin
  ###
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );

  ####
  # Disable XML RPC
  # Borrow from Philip Erb's Disable XML-RPC
  ###
  add_filter( 'xmlrpc_enabled', '__return_false' );
  remove_action( 'wp_head', 'rsd_link' );

  ####
  # Remove wp version param from any enqueued scripts
  # Borrow from Virendra's TechTalk
  ###
  function vc_remove_wp_ver_css_js( $src ) {
      if ( strpos( $src, 'ver=' ) )
          $src = remove_query_arg( 'ver', $src );
      return $src;
  }
  add_filter( 'style_loader_src', 'vc_remove_wp_ver_css_js', 9999 );
  add_filter( 'script_loader_src', 'vc_remove_wp_ver_css_js', 9999 );

  ###
  # Disable Embeds
  # Borrow from Pascal Birchler's Disable Embeds
  ###
	$wp->public_query_vars = array_diff( $wp->public_query_vars, array(
		'embed',
	) );
	remove_action( 'rest_api_init', 'wp_oembed_register_route' );
	add_filter( 'embed_oembed_discover', '__return_false' );
	remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	add_filter( 'tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin' );
	add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
	remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );

}
add_action( 'init', 'timidpress_init' );

function timidpress_activated() {
  update_option( 'TIMIDPRESS_extra_head', '' );
  update_option( 'TIMIDPRESS_delimiter', '' );
}
register_activation_hook( __FILE__, 'timidpress_activated' );

function disable_emojis_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) {
    return array_diff( $plugins, array( 'wpemoji' ) );
  } else {
    return array();
  }
}

function disable_embeds_tiny_mce_plugin( $plugins ) {
	return array_diff( $plugins, array( 'wpembed' ) );
}

function disable_embeds_rewrites( $rules ) {
	foreach ( $rules as $rule => $rewrite ) {
		if ( false !== strpos( $rewrite, 'embed=true' ) ) {
			unset( $rules[ $rule ] );
		}
	}
	return $rules;
}


####
# Timid Press Options
###
add_action( 'admin_menu', function() {
  add_options_page( 'Timid Press', 'Timid Press', 'manage_options', 'timid_press_settings', function() {
    include( __DIR__ . "/admin.php" );
  } );
});

####
# Timid Press Displays
###
add_action('wp_head', function() {
  $delimiter = get_option( 'TIMIDPRESS_delimiter' );
  $head_extras = get_option( 'TIMIDPRESS_extra_head' );
  if ( isset( $head_extras ) && ! empty( $head_extras ) ):
    if ( isset( $delimiter ) && ! empty( $delimiter ) ):
      echo "<!-- " . $delimiter . " -->\n";
    endif;
    echo html_entity_decode( stripslashes( $head_extras ) );
    echo "\n";
    if ( isset( $delimiter ) && ! empty( $delimiter ) ):
      echo "<!-- " . $delimiter . " -->\n";
    endif;
  endif;
});

?>
