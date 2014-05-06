<?php

function projectdb_format_content($project) {
  $students = 'names here';
  $post_content = '<h2><em>' . $students . "</em></h2>\n" 
    . $project['elevator_pitch'];
  if (isset($project['url']) && filter_var($project['url'], FILTER_VALIDATE_URL)) {
    $post_content .= '<a href="' . $project['url'] . '">' . $project['url']. "</a><br />\n";
  }
  if (isset($project['description'])) {
    $post_content .= "<h3>Description</h3>\n" . $project['description'];
  }
  return $post_content;
}

function projectdb_category($args) { #name, $slug = NULL, $parent = NULL) {
  $projectdb_cat_id = NULL;
  $cat_args = array(
    'cat_name' => $args['name'],
    'category_parent' => $args['parent']
  );

  if (!isset($args['slug'])) {
    $args['slug'] = sanitize_title($args['name']);
  }
  $projectdb_cat_id = get_category_by_slug($args['slug']);
  if ($projectdb_cat_id != false) {
    $cat_args['cat_ID'] = $projectdb_cat_id;
  }
  $cat_args['category_nicename'] = $args['slug'];

  if (isset($projectdb_cat_id) && ($projectdb_cat_id != false)) {
    $cat_args['cat_ID'] = $projectdb_cat_id->cat_ID;
  }

  # TODO: does this clobber stuff like the category description?
  $projectdb_cat_id = wp_insert_category($cat_args);
    
  return $projectdb_cat_id;
}

function projectdb_post($project, $post_id = NULL) {
  $projectdb_cat_list = NULL;

  $class_cat = projectdb_category('Related Classes', 'class');
  $instructor_cat = projectdb_category('Instructor', 'instructor');
  foreach ($project['classes'] as $c) {
    projectdb_category(array(
      'name' => $c['class_name'],
      'parent' => $class_cat
    ));
    projectdb_category(array(
      'name' => $c['instructor'],
      'parent' => $instructor_cat
    ));
  }

  $student_cat = projectdb_category('Student', 'student');
  foreach ($project['people'] as $p) {
    projectdb_category(array(
      'name' => $p['netid'],
      'parent' => $student_cat
    ));
  }

  $post_args = array(
    'post_title' => $project['project_name'],
    'post_status' => 'publish',
    'post_content' => projectdb_format_content($s),
    'post_category' => $projectdb_cat_list
  );

  if (isset($post_id)) {
    $post_args['ID'] = $post_id->ID;
    $post_id = wp_update_post($post_args);
  }
  else {
    $post_id = wp_insert_post($post_args);
  }

  update_post_meta($post_id, 'project_id', $project['project_id']);

  # media_sideload_image();

  return $post_id;
}

?>
