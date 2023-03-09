<?php

require "projectdbAPI.php";

//$projects = projectdb_download(160);

//echo var_dump( $projects);

// $project =  $projects[1];

// $creators = itpdir_lookup($project['project_id']);

// $classes = getClassInfo($project['class_id']);

// $documents = getDocs($project['document_id']);

// $school = array(1=>"IMA/ITP New York",2=>"IMA/IMB Shanghai",3=>"IM Abu Dhabi");


// echo var_dump($project);

function projectdb_format_content( $project, $creators, $classes,  $documents ) {

  //$school = array(1=>"IMA/ITP New York", 2=>"IMA/IMB Shanghai", 3=>"IM Abu Dhabi");
  
  //echo exit(var_dump($project));

  $students = array();
  foreach ( $creators as $s ) {
    $students[] = utf8_encode($s['name']);
  }

  $elevator_pitch = html_entity_decode(
    utf8_decode( $project['elevator_pitch'] ),
    NULL, 'UTF-8'
  );

  //heading
  $post_content = '<p id="project-pitch">' . htmlspecialchars_decode( $project['elevator_pitch'] ) . "</p>";
  $post_content .= '<h2 id="project-students">' . implode( ', ', $students ) . "</h2>\n";

  // image here
  $post_content .= "[gallery size=\"medium\" columns=\"0\" link=\"file\"]\n";

  // video
  if (!empty($project['video'])) {
    $post_content .= '<div id="project-video">' . $project['video'] . '</div>';
  }

  //description
  $post_content .= '<div id="project-description">';
  if ( isset( $project['description'] ) ) {
    $post_content .= '<h3>Description</h3>' . htmlspecialchars_decode( $project['description']);
  }
  if ( isset( $project['url'] ) && ( $project['url'] != '' ) && ( $project['url'] != 'http://' ) ) {
    $post_content .= '<div id="project-link"><a target="_blank" href="' . $project['url'] . '">' . 'Learn More'. "</a></div>";
  }
  $post_content .= '</div>';

  //school
  $school = array(1=>"IMA/ITP New York", 2=>"IMA/IMB Shanghai", 3=>"IM Abu Dhabi");
  //$post_content .= '<div id="project-school">' . $school[$classes[0]['refno']] . '</div>';

  //echo ("<b>hfhfghf".$project['people'][0]['thesis_video_url']."</b>");
  //print_r($project['people']);
  // classes
  $post_content .= '<div id="project-course-ids">';
  $classes_r1 = array();
  if ($classes) {
    foreach ( $classes as $c ) {
      array_push( $classes_r1, $c['course_id']);
    }
  }
  $post_content .= implode( ', ', $classes_r1 );
  $post_content .= '</div>';


  $post_content .= '<div id="project-classes">';
  $classes_r = array();
  if ($classes) {
    foreach ( $classes as $c ) {
      array_push( $classes_r, $c['class_name'] );
    }
  }
  $post_content .= implode( ', ', $classes_r );
  $post_content .= '</div>';

  //keywords
  $post_content .= '<div id="project-keywords">' . $project['keywords'] . '</div>';

  // if (!empty($project['zoom_link'])) {
  //   $post_content .= "\n<h3>Zoom link</h3>\n{$project['zoom_link']}";
  // }

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

  $post_id = NULL;
  $instructor_meta = array();
  $class_meta = array();
  $student_meta = array();
  $school = array(1=>"IMA/ITP New York", 2=>"IMA/IMB Shanghai", 3=>"IM Abu Dhabi");
  $keywords = explode(',', $project['keywords']);

  $creators = itpdir_lookup($project['project_id']);

  if($project['class_id']){

    $classes = getClassInfo($project['class_id']);

  }else {$classes = null;}

  if($project['document_id']){

    $documents = getDocs($project['document_id']);

  } else {$documents = null;}





  $cat_list = array( projectdb_category( array( 'name' => 'Projects', 'slug' => 'projects' ) ) );
  $existing = get_posts( array(
      'meta_key' => 'project_id',
      'meta_value' => $project['project_id']
    ) );
  if ( ( count( $existing ) > 0 ) && ( $project['project_id'] == get_post_meta( $existing[0]->ID, 'project_id', true ) ) ) {
    $post_id = $existing[0]->ID;
    //$cat_list = wp_get_post_categories($post_id);
  }

  $class_cat = projectdb_category( array( 'name' => 'Classes', 'slug' => 'class' ) );
  $instructor_cat = projectdb_category( array( 'name' => 'Instructor', 'slug' => 'instructor' ) );

  foreach ($keywords as $w) {

    array_push( $cat_list, projectdb_category(array( 'name' => $w ) ) );
    // code...
  }

  $school_meta = array();

  if($classes){

    foreach ( $classes as $c ) {
      $cat = projectdb_category( array(
          'name' => $c['class_name'],
          'parent' => $class_cat
        ) );

      if($c['refno']){

        array_push( $cat_list, projectdb_category(array( 'name' => $school[$c['refno']] ) ) );
        array_push( $school_meta, $school[$c['refno']] );

      }

      array_push( $cat_list, $cat );
      array_push( $class_meta, $c['class_name']."|".$c['course_id'] );

      $name = explode(",", $c['instructor']);

      $name = trim($name[1]) . ' ' . trim($name[0]);

      $cat = projectdb_category( array(
          'name' => $name,
          'parent' => $instructor_cat
        ) );


      // array_push( $cat_list, projectdb_category( array( 'name' => $school[$c['refno']] ) ));
      array_push( $cat_list, $cat );
      array_push( $instructor_meta, $name );

    }



  }



  $student_cat = projectdb_category( array(
      'name' => 'Student',
      'slug' => 'student'
    ) );
  foreach ( $creators as $p ) {

    $name = $p['name'];
    $cat = projectdb_category( array(
        'name' => $name,
        'parent' => $student_cat
      ) );
    array_push( $cat_list, $cat );
    array_push( $student_meta, $name );

  }

  $post_args = array(
    'post_title' => $project['project_name'],
    'post_status' => 'publish',
    'post_content' => projectdb_format_content( $project, $creators, $classes,  $documents ),
    'post_category' => $cat_list
  );

  // $slug_student_name = get_option( 'slug_student_name' );
  // if ( $slug_student_name === '1' ) {
  //   $post_args['post_name'] = sanitize_title( $person['preferred_firstname'] . ' ' . $person['preferred_lastname'] );
  // }

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
	var_dump($post_args);
    $post_id = wp_insert_post( $post_args, true);
	var_dump($post_id);
  }

  foreach ( array( 'project_id','project_name','elevator_pitch','description','url','keywords','video','zoom_link','class_id','document_id','user_id' ) as $meta ) {
    update_post_meta( $post_id, $meta, $project[$meta] );
  }
  update_post_meta( $post_id, 'student', implode( ', ', $student_meta ) );
  update_post_meta( $post_id, 'instructor', implode( ', ', $instructor_meta ) );
  update_post_meta( $post_id, 'class', implode( ', ', $class_meta ) );
  update_post_meta( $post_id, 'school', implode( ', ', $school_meta ) );

  $vslideshow_num = 0;
  // pull in the image

  if($documents){

    foreach ( $documents as $d ) {

      //$vslideshow_num = 0;

      if ( ( $d['main_image'] == true ) ) {
        $base = 'https://itp.nyu.edu/projects_documents/';
        //media_sideload_image( $base . $d['document'], $post_id, $d['document_name'] );
        $att_id = media_sideload_image( $base . $d['document'], $post_id, 'main_image','id' );
        update_post_meta( $att_id, '_wp_attachment_image_alt', $d['alt_text'] );
        // update_post_meta($post_id, '_thumbnail_id', $att_id);
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

      if ( ( $d['vslideshow'] == true ) ) {
        $base = 'https://itp.nyu.edu/projects_documents/';
        //media_sideload_image( $base . $d['document'], $post_id, $d['document_name'] );
        $att_id = media_sideload_image( $base . $d['document'], $post_id, 'slideshow_'.$vslideshow_num ,'id' );
        update_post_meta( $att_id, '_wp_attachment_image_alt', $d['alt_text'] );

        $vslideshow_num = $vslideshow_num + 1;

        //echo $vslideshow_num;

      }

      //usleep(500000);

    }


  }


  return $post_id;
}

add_action( 'before_delete_post', function( $id ) {
    $attachments = get_attached_media( '', $id );
    foreach ($attachments as $attachment) {
      wp_delete_attachment( $attachment->ID, 'true' );
    }
  } );


?>
