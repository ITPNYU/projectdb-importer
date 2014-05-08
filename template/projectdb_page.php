<h2>ProjectDB Importer</h2>
<?php 
$export_json = '';
$export = array();
if (get_option('projectdb_api_url') && get_option('projectdb_api_key')) {
  $projectdb_path = get_option('projectdb_api_url');
  if (preg_match('/\/$/', $projectdb_path) != 1) {
    $projectdb_path .= '/';
  }
#  $projectdb_path .= 'venue' . '/' . get_option('projectdb_venue')
#    . '/' . 'projects'
#    . '?'
#    . 'results_per_page=300' . '&'
#    . 'key=' . get_option('projectdb_api_key');

  $filters = array('filters' => array( 'name' => 'venue', 'op' => 'in', 'value' => 100 ) );

  $projectdb_path .= 'project'
    . '?'
    . 'q=' . urlencode(json_encode($filters)) . '&'
    . 'results_per_page=300' . '&'
    . 'key=' . get_option('projectdb_api_key');

  echo 'Importing from ' . $projectdb_path . '... ';
  $projectdb_json = file_get_contents($projectdb_path);
  $projectdb = json_decode($projectdb_json, TRUE);
  if (count($projectdb['objects']) > 0) {
    echo ' retrieved ' . count($projectdb['objects']) . ' projects.<br />';
    echo "<ul>\n";
    foreach ($projectdb['objects'] as $p) {
      echo '<li>' . $p['project']['project_name'] . ' - ' 
        . $p['project']['elevator_pitch'];
      echo "<ul>Students\n"; 
        foreach ($projectdb['students'] as $s) {
          echo '<li>' . $s['netid'] . "</li>\n";
        }
      echo "</li>\n";
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


