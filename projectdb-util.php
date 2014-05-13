<?php

function projectdb_download($args) {
  $projectdb = NULL;
  if ($args['url'] && $args['key']) {
    $url = $args['url'];
    if (preg_match('/\/$/', $url) != 1) {
      $url .= '/';
    }
    # q={"filters":[{"name":"venues__venue_id","op":"any","val":100}]}
    $filters = array(
      'filters' => array(
        array(
          'name' => 'venues__venue_id',
          'op' => 'any',
          'val' => $args['venue']
        ) 
      ) 
    );
  
    $url .= 'project' . '?'
      . 'q=' . urlencode(json_encode($filters)) . '&'
      . 'results_per_page=300' . '&'
      . 'key=' . $args['key'];

    $results_json = file_get_contents($url);
    $results = json_decode($results_json, TRUE);
  }
  return $results;
}

function projectdb_format_content($project) {
  $students = array();
  foreach ($project['people'] as $p) {
    $person = itpdir_lookup(array(
      'netid' => $p['netid'],
      'url' => get_option('itpdir_api_url'),
      'key' => get_option('itpdir_api_key')
    ));
    if (isset($person['objects'])) {
      $person = $person['objects'][0];
      $name = $person['preferred_firstname'] . ' ' . $person['preferred_lastname'];
      array_push($students, $name);
    }
  }
  
  $post_content = '<h2><em>' . implode(', ', $students) . "</em></h2>\n" 
    . $project['elevator_pitch'];
  if (isset($project['url']) && filter_var($project['url'], FILTER_VALIDATE_URL)) {
    $post_content .= '<p><a href="' . $project['url'] . '">' . $project['url']. "</a></p>\n";
  }
  if (isset($project['description'])) {
    $post_content .= "<h3>Description</h3>\n" . $project['description'];
  }
  return $post_content;
}

function projectdb_category($args) {
  $projectdb_cat_id = NULL;
  $cat_args = array(
    'cat_name' => $args['name']
  );
  if (isset($args['parent'])) {
    $cat_args['category_parent'] = $args['parent'];
  }
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

function projectdb_post($project) {
  $post_id = NULL;

  $cat_list = array(projectdb_category(array('name' => 'Projects', 'slug' => 'projects')));
  $existing = get_posts(array(
    'meta_key' => 'project_id',
    'meta_value' => $project['project_id']
  ));
  if ((count($existing) > 0) && ($project['project_id'] == get_post_meta($existing[0]->ID, 'project_id'))) {
    $post_id = $existing[0]->ID;
    #$cat_list = wp_get_post_categories($post_id);
  }

  $class_cat = projectdb_category(array('name' => 'Related Classes', 'slug' => 'class'));
  $instructor_cat = projectdb_category(array('name' => 'Instructor', 'slug' => 'instructor'));
  foreach ($project['classes'] as $c) {
    $cat = projectdb_category(array(
      'name' => $c['class_name'],
      'parent' => $class_cat
    ));
    array_push($cat_list, $cat);
    $person = itpdir_lookup(array(
      'netid' => $c['instructor_id'],
      'url' => get_option('itpdir_api_url'),
      'key' => get_option('itpdir_api_key')
    ));
    if (isset($person['objects'])) {
      $person = $person['objects'][0];
      $name = $person['preferred_firstname'] . ' ' . $person['preferred_lastname'];
      $cat = projectdb_category(array(
        'name' => $name,
        'parent' => $instructor_cat
      ));
      array_push($cat_list, $cat);
    }
  }

  $student_cat = projectdb_category(array(
    'name' => 'Student',
    'slug' => 'student'
  ));
  foreach ($project['people'] as $p) {
    $person = itpdir_lookup(array(
      'netid' => $p['netid'],
      'url' => get_option('itpdir_api_url'),
      'key' => get_option('itpdir_api_key')
    ));
    if (isset($person['objects'])) {
      $person = $person['objects'][0];
      $name = $person['preferred_firstname'] . ' ' . $person['preferred_lastname'];
      $cat = projectdb_category(array(
        'name' => $name,
        'parent' => $student_cat
      ));
      array_push($cat_list, $cat);
    }
  }

  $post_args = array(
    'post_title' => $project['project_name'],
    'post_status' => 'publish',
    'post_content' => projectdb_format_content($project),
    'post_category' => $cat_list
  );

  if (isset($post_id)) {
    $post_args['ID'] = $post_id->ID;
    echo 'updating post ' . $post_id . "<br />\n";
    $post_id = wp_update_post($post_args);
  }
  else {
    $post_id = wp_insert_post($post_args);
  }

  foreach (array('audience', 'background', 'conclusion', 'personal_statement', 'elevator_pitch', 'user_scenario', 'project_name', 'project_id', 'url') as $meta) {
    update_post_meta($post_id, $meta, $project[$meta]);
  }

  # media_sideload_image();

  return $post_id;
}

function itpdir_lookup($args) {
  $results = NULL;
  if (isset($args['netid'])) {
    $url = $args['url'];
    if (preg_match('/\/$/', $url) != 1) {
      $url .= '/';
    }
    $filters = array(
      'filters' => array(
        array(
          'name' => 'netid',
          'op' => 'eq',
          'val' => $args['netid']
        )
      )
    );
    $url .= 'person' . '?'
      . 'q=' . urlencode(json_encode($filters)) . '&'
      . 'results_per_page=300' . '&'
      . 'key=' . $args['key'];
 
    $results_json = file_get_contents($url);
    $results = json_decode($results_json, TRUE);
  }
  return $results;
}

?>
