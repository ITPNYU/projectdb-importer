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
      <th>project title</th>
      <th style='text-align:right'>links to:</th>
      <th>poster</th>
      <th>pdf of poster</th>
      <th>webpage</th>
      <th>edit project</th>
      <th>missing</th>
    </tr>
<?php
foreach ($posts as $p) {
  $posterlink = get_site_url() . '/posterprint?post=' . $p->ID;
  $project_id = get_post_meta($p->ID, 'project_id', TRUE);
?>
    <tr>
      <td colspan="2"><?php echo $p->post_title; ?></td>
      <td><a href="<?php echo $posterlink; ?>">poster</a></td>
      <td><a href="/makepdf/?url=<?php echo $posterlink; ?>">PDF</a></td>
      <td><a href="<?php echo get_permalink($p); ?>">project post</a></td>
      <td><a href="https://itp.nyu.edu/projects/projectinfo.php?project_id=<?php echo project_id; ?>">Edit Project</a></td>
    </tr>
<?php
}
?>
  </table>
  </body>
</html>
