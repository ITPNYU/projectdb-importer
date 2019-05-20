<?php
/**
 * Plugin Name: ProjectDB Importer
 * Plugin URI: http://github.com/ITPNYU/projectdb-importer
 * Description: Wordpress plugin for importing ITP projects as posts
 * Version: 1.0
 * Author: NYU ITP
 * Author URI: http://itp.nyu.edu
 * License: GPLv3
 */

require 'projectdb-util.php';

register_activation_hook( __FILE__, 'projectdb_setup');
add_action('admin_init', 'projectdb_settings');
add_action('admin_menu', 'projectdb_menu');

add_filter('template_redirect', 'projectdb_template_filter');

function projectdb_menu() {
  $page_hook = add_management_page( 'ProjectDB Importer', 'ProjectDB Importer', 'manage_options', 'projectdb-importer', 'projectdb_page');
}

function projectdb_page() {
  include plugin_dir_path(__FILE__) . '/template/projectdb_page.php';
}

function projectdb_setup() {
  add_option('projectdb_api_url');
  add_option('projectdb_api_key');
  add_option('projectdb_venue');
  add_option('itpdir_api_url');
  add_option('itpdir_api_key');
  add_option('slug_student_name');
  //Yen: password for feedback
  add_option('feedback_password');
}

function projectdb_setting_callback($arg) {
  $option_name = $arg[0];
  $option_data = get_option($option_name);
  echo "<input type=\"text\" name=\"$option_name\" value=\"$option_data\" />";
}

function projectdb_setting_checkbox_callback($arg) {
  $option_name = $arg[0];
  $option_data = get_option($option_name);
  echo "<input type=\"checkbox\" name=\"$option_name\" value=\"1\" " .
    checked(1, $option_data, false) . " />";
}

function projectdb_settings() {
  add_settings_section('projectdb_section',
    'ProjectDB Importer Settings',
    'projectdb_section',
    'general'
  );

  add_settings_field('projectdb_api_url',
    'ProjectDB API URL',
    'projectdb_setting_callback',
    'general',
    'projectdb_section',
    array('projectdb_api_url')
  );

  add_settings_field('projectdb_api_key',
    'ProjectDB API Key',
    'projectdb_setting_callback',
    'general',
    'projectdb_section',
    array('projectdb_api_key')
  );

  add_settings_field('projectdb_venue',
    'ProjectDB Venue Number',
    'projectdb_setting_callback',
    'general',
    'projectdb_section',
    array('projectdb_venue')
  );

  add_settings_field('itpdir_api_url',
    'ITPDir API URL',
    'projectdb_setting_callback',
    'general',
    'projectdb_section',
    array('itpdir_api_url')
  );

  add_settings_field('itpdir_api_key',
    'ITPDir API Key',
    'projectdb_setting_callback',
    'general',
    'projectdb_section',
    array('itpdir_api_key')
  );

  add_settings_field('slug_student_name',
    'Slug Student Name',
    'projectdb_setting_checkbox_callback',
    'general',
    'projectdb_section',
    array('slug_student_name')
  );

  add_settings_field('feedback_password',
    'Feedback Auth Passcode',
    'projectdb_setting_callback',
    'general',
    'projectdb_section',
    array('feedback_password')
  );

  register_setting('general', 'projectdb_api_url');
  register_setting('general', 'projectdb_api_key');
  register_setting('general', 'projectdb_venue');

  register_setting('general', 'itpdir_api_url');
  register_setting('general', 'itpdir_api_key');

  register_setting('general', 'slug_student_name');
  register_setting('general', 'feedback_password');
}

function projectdb_template_filter() {
  if (is_page('posterprint')) {
    $location = plugin_dir_path(__FILE__) . '/template/' . 'projectdb_poster_template.php';
    if (file_exists($location)) {
      load_template($location);
      exit();
    }
  }
  else if (is_page('posterlist')) {
    $location = plugin_dir_path(__FILE__) . '/template/' . 'projectdb_posterlist_template.php';
    if (file_exists($location)) {
      load_template($location);
      exit();
    }
  }
  else if (is_page('posterprint_new')) {
    $location = plugin_dir_path(__FILE__) . '/template/' . 'projectdb_poster_template2.php';
    if (file_exists($location)) {
      load_template($location);
      exit();
    }
  }
}
