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
 *   v1.0.1: added support for specifying date, category, tags, and
 *           template part; moved shortcode-parsing functionality to a new
 *           function so that todays_posts_only can be called directly
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
 * Version:           1.0.1
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
  // https://github.com/YahnisElsts/plugin-update-checker#github-integration
  require 'plugin-update-checker/plugin-update-checker.php';

  $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/kdaweb/todays_posts_only/',
    __FILE__,
    'TodaysPostsOnly'
  );

  if (! function_exixts ('todays_posts_only_shortcode')) {


    /**
     * function called by the shortcode to extract atts and call main function
     *
     * This function is designed to be called by the shortcode.  It extracts
     * the attributes provided when the shortcode was called, builds an 'atts'
     * array, and calls the main todays_posts_only() function with the extracted
     * attributes.  This way, we can use todays_posts_only() in a template or
     * such elsewhere.
     *
     * @param string[] $attributes passed to the shortcode
     * @return boolean TRUE if todays_posts_only succeeded; FALSE, otherwise
     */
    function todays_posts_only_shortcode ($attributes = array()) {
      // obtain an array based on the attributes passed to the shortcode
      // see: https://codex.wordpress.org/Function_Reference/shortcode_atts
      $atts = shortcode_atts(
        array(
          'tag'           => NULL,
          'category'      => NULL,
          'date'          => NULL,
          'template_part' => NULL
        ),
        $attributes,
        'todays_posts_only'
      );

      $return_value = todays_posts_only ($atts);

      return $return_value;
    }


  }

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
     * @param string[] $atts elements to filter posts
     * @return boolean TRUE if posts were found; FALSE otherwise
     **/
    function todays_posts_only($atts) {

      $DEFAULT_DATE          = 'today';
      $DEFAULT_TEMPLATE_PART = 'content';

      // if we were not passed a date, use the default date instead
      if ((! array_key_exists ('date', $atts))
          ||  (is_null ($atts['date']))
      ) {
        $post_date = $DEFAULT_DATE;
      } else {
        $post_date = $atts['date'];
      }

      // if we were not passed a template part, use the default instead
      if ((! array_key_exists ('template_part', $atts))
          ||  (is_null ($atts['template_part']))
      ) {
        $template_part = $DEFAULT_TEMPLATE_PART;
      }

      // start with an empty query filter
      $query_filter = array();

      // parse the date string we were passed into an array
      // http://php.net/manual/en/function.date-parse.php
      $parsed_date = date_parse ($post_date);

      // add a query filter based on the specified date
      // https://codex.wordpress.org/Class_Reference/WP_Query#Date_Parameters
      $query_filter['date_query'] = array(
        array(
          'year'  => $parsed_date['year'],
          'month' => $parsed_date['mon'],
          'day'   => $parsed_date['day'],
        ),
      );

      // if we were passed a category (name), add that as a filter
      // http://codex.wordpress.org/Class_Reference/WP_Query#Category_Parameters
      if ((array_key_exists ('category', $atts))
          && (! is_null ($atts['category']))
      ) {
        $query_filter['category_name'] = $atts['category'];
      }

      // if we were passed a tag, add that as a filter
      // https://codex.wordpress.org/Class_Reference/WP_Query#Tag_Parameters
      if ((array_key_exists ('tag', $atts))
          &&  (! is_null ($atts['tag']))
      ) {
        $query_filter['tag'] = $atts['tag'];
      }

      // instantiate a new query based on the filter we've constructed
      // https://codex.wordpress.org/Class_Reference/WP_Query
      $custom_query = new WP_Query($query_filter);

      // if the query returned any posts, process them
      if ($custom_query->have_posts()) {

        // loop through all of the posts the query returned
        while ($custom_query->have_posts()) {
          $custom_query->the_post();
          get_template_part($template_part, get_post_format());
        }// end custom loop

        // restore the global $post from the main query loop
        // https://codex.wordpress.org/Function_Reference/wp_reset_postdata
        wp_reset_postdata();

        return TRUE;

      } else {

        // restore the global $post from the main query loop
        // https://codex.wordpress.org/Function_Reference/wp_reset_postdata
        wp_reset_postdata();

        return FALSE;

      }// end if
    }


  }

  // register the shortcode
  // see: https://codex.wordpress.org/Function_Reference/add_shortcode
  // add_shortcode(shortcode, function)
  add_shortcode('todays_posts_only', 'todays_posts_only_shortcode');

?>