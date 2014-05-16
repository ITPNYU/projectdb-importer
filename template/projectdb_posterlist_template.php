<!DOCTYPE html>
<html>
  <head>
  </head>
  <body>
<?php
$posts = get_posts(array('numberposts' => -1));
?>
<h1>links to poster and webpage for each project</h1>
<?php echo count($posts); ?> projects found
<div id="content">
  <table>
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
?>
    <tr>
      <td colspan="2"><?php echo $p->post_title; ?></td>
      <td><a href="<?php echo $posterlink; ?>">poster</a></td>
      <td><a href="/makepdf/?url=<?php echo $posterlink; ?>">PDF</a></td>
      <td><a href="<?php echo get_permalink($p); ?>">project post</a></td>
      <td><a href="https://itp.nyu.edu/projects/projectinfo.php?project_id=<?php echo get_post_meta($p->ID, 'project_id', TRUE); ?>">Edit Project</a></td>
    </tr>
<?php
}
?>
  </table>
  </body>
</html>
