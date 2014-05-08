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
    echo "<ul>\n";
    foreach ($projectdb['objects'] as $p) {
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


