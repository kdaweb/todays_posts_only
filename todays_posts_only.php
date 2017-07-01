<?php
/**
 * Adds 'todays_posts_only' shortcode to display only today's posts
 *
 * This plugin adds a shortcode that displays posts posted on a given day.
 * Originally, this plugin would only display posts posted on the current day;
 * however, this was expanded such that the query still looks for posts on a
 * given day (defaulting to today), it also accepts a date string that can be
 * parsed as per PHP's "date_parse" function:
 * 
 * http://php.net/manual/en/function.date-parse.php
 * 
 * Additionally, functionality was added to restrict posts to a specified
 * category name or tag.
 *
 * This was originally based on a post by 'Trevor' on StackExchange at:
 * 
 * wordpress.stackexchange.com/questions/226980/show-only-posts-from-todays-date
 *
 * Changelog:
 *   v1.0.0: initial release
 *
 * PHP Version 5.2.4
 * @category   TodaysPostsOnly
 * @package    TodaysPostsOnly
 * @author     KDA Web Technologies, Inc. <info@kdaweb.com>
 * @copyright  2017 KDA Web Technologies, Inc.
 * @license    http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 * @version    GIT: $Id$
 * @link       http://kdaweb.com/ KDA Web Technologies, Inc.
 *
 * @wordpress-plugin
 * Plugin Name:       TodaysPostsOnly
 * Plugin URI:        http://kdaweb.com/
 * Description:       shortcode that shows only to
 * Version:           1.0.0
 * Author:            KDA Web Technologies, Inc.
 * Author URI:        http://kdaweb.com/
 * License:           Modified BSD (3-Clause) License
 * License URI:       http://directory.fsf.org/wiki/License:BSD_3Clause
 * Text Domain:       TodaysPostsOnly
 * Domain Path:       /languages
 */

  // If this file is called directly, abort.
  if (! defined('WPINC')) {
      die;
  }
  
  // allow updating of plugin from Github
  // see: https://github.com/YahnisElsts/plugin-update-checker#github-integration
  require 'plugin-update-checker/plugin-update-checker.php';
  
  $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/wesley-dean/todays_posts_only/',
    __FILE__,
    'TodaysPostsOnly'
  );

  if (! function_exists ('todays_posts_only')) {
    /**
     * function to retrieve posts posted on a given day (default is 'today')
     * 
     * todays_posts_only accepts an array of zero or more strings that serve
     * as filters for what posts to retrieve:
     * 
     * category: eliminate posts not in this category (the default, NULL, 
     * doesn't filter posts by category); the filter is on category NAME, not ID
     * 
     * tag: eliminate posts that do not have this tag (the default, NULL,
     * doesn't filter posts by tag); the filter is on tag NAME, not ID
     * 
     * date: eliminate posts not on this date; the default is today; the date 
     * must be a string that PHP's date_parse can parse; see here:
     * 
     * http://php.net/manual/en/function.date-parse.php
     * 
     * @param string[] attributes filters for the query
     * @return boolean TRUE if posts were found; FALSE otherwise
     **/
    function todays_posts_only($attributes) {
      // see: https://codex.wordpress.org/Function_Reference/shortcode_atts
      
      $today = getdate();
      
      $atts = shortcode_atts(
        array(
          'tag'      => NULL,
          'category' => NULL,
          'date'     => 'today'
        ),
        $attributes,
        'todays_posts_only'
      );
      
      $parsed_date = date_parse ($atts['date']);
      
      $args = array(
        'date_query' => array(
          array(
            'year'  => $atts['year'],
            'month' => $atts['mon'],
            'day'   => $atts['day'],
          ),
        ),
      );
      
      if ($atts['category'] != NULL) {
        $args['category_name'] = $atts['category'];
      }
      
      if ($atts['tag'] != NULL) {
        $args['tag'] = $atts['tag'];
      }
    
      $custom_query = new WP_Query($args);
    
     if ($custom_query->have_posts()) {
       while ($custom_query->have_posts()) {
          $custom_query->the_post();
          get_template_part('content', get_post_format());
        }// end custom loop
        wp_reset_postdata();
        
        return TRUE;
        
      } else {
      
        wp_reset_postdata();
        return FALSE;
        
      }// end if
    }
  }
  
  // register the shortcode
  // see: https://codex.wordpress.org/Function_Reference/add_shortcode
  add_shortcode('todays_posts_only', 'todays_posts_only');

?>