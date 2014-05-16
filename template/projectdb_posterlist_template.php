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
  </table>
  </body>
</html>
