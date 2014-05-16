<h2>ProjectDB Importer</h2>
<?php 
if (get_option('projectdb_api_url') && get_option('projectdb_api_key')) {
  $projectdb = projectdb_download(array(
    'url' => get_option('projectdb_api_url'),
    'key' => get_option('projectdb_api_key'),
    'venue' => get_option('projectdb_venue')
  ));
  if (count($projectdb['objects']) > 0) {
    echo ' retrieved ' . count($projectdb['objects']) . ' projects.<br />';
    $all_posts = get_posts(array('numberposts' => -1));
    $all_posts_id = array();
    foreach ($all_posts as $p) {
      array_push($all_posts_id, get_post_meta($p->ID, 'project_id', TRUE));
    }
    echo "<ul>\n";
    foreach ($projectdb['objects'] as $p) {
      if (!in_array($p['project_id'], $all_posts_id)) {
        $attach = get_posts(array('post_type' => 'attachment', 'post_parent' => $p->ID));
        foreach ($attach as $a) {
          $ret = wp_delete_attachment($a->ID, TRUE);
          if ($ret) {
            echo 'attachment deleted: ';
          }
          else {
            echo 'attachment deletion failure: ';
          }
          echo $p->ID . ' for project ' . $p['project_id'] . "\n";
        }
        $ret = wp_delete_post($p->ID, TRUE);
        if ($ret) {
          echo 'post deleted: ';
        }
        else {
          echo 'post deletion: ';
        }
        echo $p->ID . ' for project ' . $p['project_id'] . "\n";
      }
      else {
        $post_id = projectdb_post($p);
        echo '<li>';
        if ( is_wp_error($post_id) ) {
          echo $post_id->get_error_message();
        }
        else {
          echo $post_id . ': ' . get_post($post_id)->post_title;
        }
        echo "</li>\n";
      }
    }
    echo "</ul>\n";
  }
  else {
    echo 'cannot read projectdb.';
  }
  echo '<br />';
}
else {
  echo "ProjectDB API URL not set";
}
?>


