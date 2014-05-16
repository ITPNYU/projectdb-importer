<!DOCTYPE html>
<html>
  <head>
<script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
  </head>
  <body>
<?php
$posts = get_posts(array('numberposts' => -1));
?>
<h1>links to poster and webpage for each project</h1>
<?php echo count($posts); ?> projects found
<div id="content">
  <table class="table table-striped">
    <tr>
      <th>Project title</th>
      <th>Poster</th>
      <th>PDF of Poster</th>
      <th>Project Post</th>
      <th>Edit Project</th>
      <th>Missing</th>
    </tr>
<?php
foreach ($posts as $p) {
  $posterlink = get_site_url() . '/posterprint?post=' . $p->ID;
  $project_id = get_post_meta($p->ID, 'project_id', TRUE);
  $fields = array('student', 'instructor', 'class', 'url', 'elevator_pitch');
  $missing = array();
  foreach ($fields as $f) {
    $v = get_post_meta($p->ID, $f, TRUE);
    if (!isset($v) || ($v == '')) {
      array_push($missing, $f);
    }
  }
  // check for empty URL
  if (!in_array('url', $missing)) {
    $url = get_post_meta($p->ID, 'url', TRUE);
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
      array_push($missing, 'url');
    }
  }
  // check for image attachment
  $args = array(
    'post_parent' => $p->ID,
    'numberposts' => 1,
    'post_status' => 'any',
    'post_type' => 'attachment'
  );
  $attach = get_posts($args);
  if (count($attach) != 1) {
    array_push($missing, 'image');
  }
?>
    <tr <?php if (count($missing) > 0) { echo "class=\"warning\""; } ?>>
      <td><?php echo $p->post_title; ?></td>
      <td><a href="<?php echo $posterlink; ?>">poster</a></td>
      <td><a href="/makepdf/?url=<?php echo $posterlink; ?>">PDF</a></td>
      <td><a href="<?php echo get_permalink($p); ?>">project post</a></td>
      <td><a href="https://itp.nyu.edu/projects/projectinfo.php?project_id=<?php echo $project_id; ?>">Edit Project</a></td>
      <td><?php echo implode(', ', $missing); ?></td>
    </tr>
<?php
}
?>
  </table>
  </body>
</html>
