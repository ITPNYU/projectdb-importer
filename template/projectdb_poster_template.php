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
  setup_postdata($post_id); // check for null/error
  the_title('<h1>', "</h1\n");
}
?>

  </body>
</html>
