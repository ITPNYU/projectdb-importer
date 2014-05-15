<!DOCTYPE html>
<html>
  <head></head>
  <body>
<?php 
$post_id = null;
if (!isset($_REQUEST['post'])) {
?>
    <h2>No poster selected</h2>
<?php
}
else {
  $post_id = get_post($_REQUEST['post']);
  //setup_postdata($post_id); // check for null/error
?>
    <h1><?php echo get_the_title($post_id) ?></h1>
<?php
}
?>

  </body>
</html>
