<h2>ProjectDB Importer</h2>
<?php 
$export_json = '';
$export = array();
if (get_option('projectdb_api_url') && get_option('projectdb_api_key')) {
  $filters = array(
    'name' => 'venues',
    'op' => 'any',
    'value' => array(
      'name' => 'venue_id',
      'op' => '==',
      'value' => get_option('projectdb_venue')
    ) 
  );
  $projectdb_path = get_option('projectdb_api_url');
  if (preg_match('/\/$/', $projectdb_path) != 1) {
    $projectdb_path .= '/';
  }
  $projectdb_path .= 'project?'
    . $filters . '&'
    . 'results_per_page=300' . '&'
    . 'key=' . get_option('projectdb_api_key');

  echo 'Importing from ' . $projectdb_path . '... ';
  $projectdb_json = file_get_contents($projectdb_path);
  $projectdb = json_decode($projectdb_json, TRUE);
  if (count($projectdb) > 0) {
    echo ' retrieved ' . count($projectdb) . ' projects.<br />';
    echo "<ul>\n";
    foreach ($projectdb as $p) {
      echo '<li>' . $p['project_name'] . "</li>\n";
    }
    echo "</ul>\n";

    foreach ($projectdb as $p) {
      $existing = get_posts(array(
        'meta_key' => 'project_id',
        'meta_value' => $p['project_id']
      ));
/*      if (count($existing) > 0) {
        $post_id = $existing[0];
        echo "updating ";
      }
      else {
        $post_id = null;
        echo "creating ";
      }
      $post_id = projectdb_post($student, $post_id);
      if ( is_wp_error($post_id) ) {
        echo $post_id->get_error_message();
      }
      else {
        echo 'post ID ' . $post_id . "<br />\n";
      } */
    }
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


