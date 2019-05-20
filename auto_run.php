<?php

function projectdb_download( $args ) {
  $projectdb = NULL;
  if ( $args['url'] && $args['key'] ) {
    $url = $args['url'];
    if ( preg_match( '/\/$/', $url ) != 1 ) {
      $url .= '/';
    }
    // q={"filters":[{"name":"venues__venue_id","op":"any","val":100}]}
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
      . 'q=' . urlencode( json_encode( $filters ) ) . '&'
      . 'results_per_page=300' . '&'
      . 'key=' . $args['key'];

    $results_json = file_get_contents( $url );
    $results = json_decode( $results_json, TRUE );
  }
  return $results;
}

function projectdb_format_content( $project ) {
  $students = array();
  foreach ( $project['people'] as $p ) {
    $person = itpdir_lookup( array(
        'netid' => $p['netid'],
        'url' => get_option( 'itpdir_api_url' ),
        'key' => get_option( 'itpdir_api_key' )
      ) );
    if ( isset( $person['objects'] ) ) {
      $person = $person['objects'][0];
      $name = html_entity_decode(
        utf8_decode( $person['preferred_firstname'] . ' ' . $person['preferred_lastname'] ),
        NULL, 'UTF-8'
      );
      array_push( $students, $name );
    }
  }

  $post_content = '<h2><em>' . implode( ', ', $students ) . "</em></h2>\n";
  $post_content .= '<p>' . $project['elevator_pitch'] . "</p>\n";
  if ( isset( $project['url'] ) && ( $project['url'] != '' ) && ( $project['url'] != 'http://' ) ) {
    $post_content .= '<p><a href="' . $project['url'] . '">' . $project['url']. "</a></p>\n";
  }

  // image here
  $post_content .= "[gallery size=\"medium\" columns=\"0\" link=\"file\"]\n";

  if ( isset( $project['description'] ) ) {
    $post_content .= "<h3>Description</h3>\n" . htmlspecialchars_decode( $project['description'] );
  }


  //echo ("<b>hfhfghf".$project['people']['thesis_video_url']."</b>");
  //$post_content .= $project['video_url'];

  // classes
  $post_content .= "\n<h3>Classes</h3>\n";
  $classes = array();
  foreach ( $project['classes'] as $c ) {
    array_push( $classes, $c['class_name'] );
  }
  $post_content .= implode( ', ', $classes );

  return $post_content;
}

function projectdb_category( $args ) {
  $projectdb_cat_id = NULL;
  $cat_args = array(
    'cat_name' => $args['name']
  );
  if ( isset( $args['parent'] ) ) {
    $cat_args['category_parent'] = $args['parent'];
  }
  if ( !isset( $args['slug'] ) ) {
    $args['slug'] = sanitize_title( $args['name'] );
  }
  $projectdb_cat_id = get_category_by_slug( $args['slug'] );
  if ( $projectdb_cat_id != false ) {
    $cat_args['cat_ID'] = $projectdb_cat_id;
  }
  $cat_args['category_nicename'] = $args['slug'];

  if ( isset( $projectdb_cat_id ) && ( $projectdb_cat_id != false ) ) {
    $cat_args['cat_ID'] = $projectdb_cat_id->cat_ID;
  }

  // TODO: does this clobber stuff like the category description?
  $projectdb_cat_id = wp_insert_category( $cat_args );
  return $projectdb_cat_id;
}

function projectdb_post( $project ) {
  //print_r($project);

  $post_id = NULL;
  $instructor_meta = array();
  $class_meta = array();
  $student_meta = array();

  $cat_list = array( projectdb_category( array( 'name' => 'Projects', 'slug' => 'projects' ) ) );
  $existing = get_posts( array(
      'meta_key' => 'project_id',
      'meta_value' => $project['project_id']
    ) );
  if ( ( count( $existing ) > 0 ) && ( $project['project_id'] == get_post_meta( $existing[0]->ID, 'project_id', true ) ) ) {
    $post_id = $existing[0]->ID;
    //$cat_list = wp_get_post_categories($post_id);
  }

  $class_cat = projectdb_category( array( 'name' => 'Related Classes', 'slug' => 'class' ) );
  $instructor_cat = projectdb_category( array( 'name' => 'Instructor', 'slug' => 'instructor' ) );
  foreach ( $project['classes'] as $c ) {
    $cat = projectdb_category( array(
        'name' => $c['class_name'],
        'parent' => $class_cat
      ) );
    array_push( $cat_list, $cat );
    array_push( $class_meta, $c['class_name'] );
    $person = itpdir_lookup( array(
        'netid' => $c['instructor_id'],
        'url' => get_option( 'itpdir_api_url' ),
        'key' => get_option( 'itpdir_api_key' )
      ) );
    //Yen: $person['objects'] may be an empty array.
    //if ( isset( $person['objects'] ) ) {
    if ( !empty( $person['objects'] ) ) {
      //var_dump($person);
      $person = $person['objects'][0];

      $name = $person['preferred_firstname'] . ' ' . $person['preferred_lastname'];
      $cat = projectdb_category( array(
          'name' => $name,
          'parent' => $instructor_cat
        ) );
      array_push( $cat_list, $cat );
      array_push( $instructor_meta, $name );
    }
  }

  $student_cat = projectdb_category( array(
      'name' => 'Student',
      'slug' => 'student'
    ) );
  foreach ( $project['people'] as $p ) {
    $person = itpdir_lookup( array(
        'netid' => $p['netid'],
        'url' => get_option( 'itpdir_api_url' ),
        'key' => get_option( 'itpdir_api_key' )
      ) );
    if ( isset( $person['objects'] ) ) {
      $person = $person['objects'][0];
      $name = $person['preferred_firstname'] . ' ' . $person['preferred_lastname'];
      $cat = projectdb_category( array(
          'name' => $name,
          'parent' => $student_cat
        ) );
      array_push( $cat_list, $cat );
      array_push( $student_meta, $name );
    }
  }

  $post_args = array(
    'post_title' => $project['project_name'],
    'post_status' => 'publish',
    'post_content' => projectdb_format_content( $project ),
    'post_category' => $cat_list
  );

  $slug_student_name = get_option( 'slug_student_name' );
  if ( $slug_student_name === '1' ) {
    $post_args['post_name'] = sanitize_title( $person['preferred_firstname'] . ' ' . $person['preferred_lastname'] );
  }

  if ( isset( $post_id ) ) {
    $post_args['ID'] = $post_id;
    // Yen: $post_id (int)
    //echo 'updating post ' . $post_id . ': ' . $post_id->post_title .  "<br />\n";
    echo 'updating post ' . $post_id . ': ' .  "<br />\n";
    $attach = get_posts( array( 'post_type' => 'attachment', 'post_parent' => $post_id ) );
    foreach ( $attach as $a ) {
      $ret = wp_delete_attachment( $a->ID, TRUE );
      if ( $ret ) {
        echo 'attachment deleted: ';
      }
      else {
        echo 'attachment deletion failure: ';
      }
      echo $post_id . "\n";
    }
    wp_update_post( $post_args );
  }
  else {
    $post_id = wp_insert_post( $post_args );
  }

  foreach ( array( 'audience', 'background', 'conclusion', 'personal_statement', 'elevator_pitch', 'user_scenario', 'project_name', 'project_id', 'url' ) as $meta ) {
    update_post_meta( $post_id, $meta, $project[$meta] );
  }
  update_post_meta( $post_id, 'student', implode( ', ', $student_meta ) );
  update_post_meta( $post_id, 'instructor', implode( ', ', $instructor_meta ) );
  update_post_meta( $post_id, 'class', implode( ', ', $class_meta ) );

  // pull in the image
  foreach ( $project['documents'] as $d ) {
    if ( ( $d['main_image'] == true ) && ( $d['secret'] == false ) ) {
      $base = 'http://itp.nyu.edu/projects_documents/';
      $att_id = media_sideload_image( $base . $d['document'], $post_id, $d['document_name'],'id' );
      //add_post_meta($att_id, '_wp_attachment_image_alt', 'MY TEXT');
      update_post_meta( $att_id, '_wp_attachment_image_alt', 'Main Project Image' );
      /*
      $args = array(
        'post_type' => 'attachment',
        'numberposts' => 1,
        'post_status' => null,
        'post_parent' => $post_id
      );
      $attachments = get_posts($args);
      if (isset($attachments) && (count($attachments) > 0)) {
        // if you want to set featured image, this is how:
        update_post_meta($post_id, '_thumbnail_id', $attachments[0]->ID);
      }*/
    }
  }

  return $post_id;
}

function itpdir_lookup( $args ) {
  $results = NULL;
  if ( isset( $args['netid'] ) ) {
    $url = $args['url'];
    if ( preg_match( '/\/$/', $url ) != 1 ) {
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
      . 'q=' . urlencode( json_encode( $filters ) ) . '&'
      . 'results_per_page=300' . '&'
      . 'key=' . $args['key'];

    $results_json = file_get_contents( $url );
    $results = json_decode( $results_json, TRUE );
  }
  return $results;
}




set_time_limit( 0 );
$goodrest = 1;

if ( ob_get_level() == 0 ) ob_start();

if ( get_option( 'projectdb_api_url' ) && get_option( 'projectdb_api_key' ) ) {
  $projectdb = projectdb_download( array(
      'url' => get_option( 'projectdb_api_url' ),
      'key' => get_option( 'projectdb_api_key' ),
      'venue' => get_option( 'projectdb_venue' )
    ) );

  $projectdb_count = count( $projectdb['objects'] );
  if ( $projectdb_count > 0 ) {
    //print_r($projectdb['objects']);
    //echo ' retrieved ' . $projectdb_count . ' projects.<br />';
    $all_posts = get_posts( array( 'numberposts' => -1 ) );
    $all_posts_id = array();
    foreach ( $all_posts as $p ) {
      array_push( $all_posts_id, get_post_meta( $p->ID, 'project_id', TRUE ) );
    }
    $all_projects_id = array();
    foreach ( $projectdb['objects'] as $p ) {
      array_push( $all_projects_id, $p['project_id'] );
    }
    //echo "all_posts_id: ";
    //echo "<ul>\n";
    foreach ( $projectdb['objects'] as $p ) {
      if ( !in_array( $p['project_id'], $all_projects_id ) ) {
        $posts = get_posts( array( 'numberposts' => 1, 'meta_key' => 'project_id', 'meta_value' => $p['project_id'] ) );
        if ( count( $posts ) > 0 ) {
          $attach = get_posts( array( 'post_type' => 'attachment', 'post_parent' => $posts[0]->ID ) );
          foreach ( $attach as $a ) {
            $ret = wp_delete_attachment( $a->ID, TRUE );
            if ( $ret ) {
              //echo 'attachment deleted: ';
            }
            else {
              //echo 'attachment deletion failure: ';
            }
            //echo $posts[0]->ID . ' for project ' . $p['project_id'] . "\n";
          }
          $ret = wp_delete_post( $posts[0]->ID, TRUE );
          if ( $ret ) {
            //echo 'post deleted: ';
          }
          else {
            //echo 'post deletion failure: ';
          }
          //echo $posts[0]->ID . ' for project ' . $p['project_id'] . "<br />\n";
        }
      }
      else {
        $post_id = projectdb_post( $p );
        //echo '<li>';
        if ( is_wp_error( $post_id ) ) {
          //echo $post_id->get_error_message();
        }
        else {
          //echo $post_id . ': ' . get_post( $post_id )->post_title;
        }

        $percent = round( $goodrest/$projectdb_count*100 );
        //echo str_pad( '', 4096 )."\n";
        // YEN: Quick BUT very Dirty!!!
    //     echo '<script language="javascript">
    // document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.'%;background-color:#ddd;\">&nbsp;</div>";
    // document.getElementById("information").innerHTML="'.$goodrest.' project(s) processed. ('.$percent.'%)";
    // </script>';

        ob_flush();
        flush();

        //echo "</li>\n";

        if ( $goodrest!=0 && $goodrest%5==0 ) sleep( 1 );

        $goodrest++;
      }

    }
    //echo "</ul>\n";
  }
  else {
    //echo 'cannot read projectdb.';
  }
  //echo '<br />';
}
else {
  //echo "ProjectDB API URL not set";
}

//echo '<script language="javascript">document.getElementById("information").innerHTML="Process completed"</script>';
ob_end_flush();

echo "end";
