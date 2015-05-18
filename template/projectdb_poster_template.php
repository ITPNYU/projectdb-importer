<!DOCTYPE html>
<html>
  <head>
<style type="text/css" media="print,screen">
@page {
  size: 8.5in 11in;  /* width height */
  margin-left: .25in;
  margin-right: .25in;
  margin-top: .25in;
  margin-bottom: .25in;
}

body{
  margin:0;
  padding: 0;
  font-family: Lato, sans-serif;
  font-size: 12pt;
  font-weight:normal;
  background-color: #fff;
  color: #000;
  text-align: center;
}

#wrapper {
  padding:0;
  margin:0;
  width: 7.5in;
  height: 10.5in;
  margin-top: .25in;
  margin-left: auto;
  margin-right: auto;
  border:2px solid #814fa0;
  /*	border-top:1px solid #000;*/
}

.header1{
  float:left;
  width:50%;
  font-size: 30px;
  font-weight: bold;
  letter-spacing: -.25px;
  line-height: 38px;
  text-align:center;
  color: #088889;
  margin:0;
  padding: 10px 0px 0px 0px;
}

#img1 {
  float:left;
}
#img2 {
  float:right;
}
#content {
  clear: both;
  margin:0;
  padding:0;
  width: 100%;
  height: 100%;
  vertical-align: text-bottom;
}

#content div {
  width: 95%;
  vertical-align: text-bottom;
  margin: auto;
  border:1px solid #fff;
}

#content div p {
  padding:0;
  margin:0;
  vertical-align: text-bottom;
  margin-bottom: 2pt;
}

#title	{
  font-size: 32pt;
  font-weight: bold;
  color:#000;
} /* TITLE   */

#names	{
  font-size: 18pt;
  font-weight: bold;
  color: #814fa0;
  line-height: 20pt;
} /* NAMES   */
#names_sm	{
  font-size: 16pt;
  font-weight: bold;
  color: #814fa0;
  line-height: 18pt;
} /* NAMES small   */

#projectimg img {
  padding: 5pt;
  border:1pt solid #ccc;
}

#pitch_wrap {
  width: 100%;
}
#pitch	{
  padding:0;
  margin:0;
  width: 90%;
  margin-left: auto;
  margin-right: auto;
  font-size: 28pt;
  text-align: center;
} /* PITCH   */

#class	{
  font-size: 12pt;
  font-style: italic;
} /* CLASSES */

#url {
  color: #814fa0;
  font-size: 14pt;
} /* URL     */

#spacer {
  width: 100%;
  margin:0;
  padding: 0;
  height: 25pt;
} /* space btwn divs     */

@media print {
  #wrapper {
    height: 100% !important;
  }
}

</style>
  </head>
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
    <div id="wrapper">
      <div id="header1">
        <img width="100%" src="<? echo get_header_image(); ?>">
      </div><!-- #header1 -->

      <div id="title">
        <p><?php echo utf8_decode(get_the_title($post_id)); ?></p>
      </div><!-- #title -->

      <div id="names">
        <p><?php echo utf8_decode(get_post_meta($post_id->ID, 'student', TRUE)); ?></p>
      </div><!-- #names -->

<?php
$args = array(
  'post_parent' => $post_id->ID,
  'numberposts' => 1,
  'post_status' => 'any',
  'post_type' => 'attachment'
);
$attach = get_posts($args);
?>
        <?php
if (count($attach) > 0) {
  $image = wp_get_attachment_image($attach[0]->ID, 'medium');
?>
      <div id="projectimg">
<?php
  echo $image;
?>
      </div><!-- #projectimg -->
<?php
}
?>

      <div id="pitch_wrap">
        <div id="pitch">
          <p><?php echo (htmlspecialchars_decode(get_post_meta($post_id->ID, 'elevator_pitch', TRUE))); ?></p>
        </div><!-- #pitch -->
      </div><!-- #pitch_wrap -->

      <div id="class">
        <p>Classes: <?php echo (get_post_meta($post_id->ID, 'class', TRUE)); ?></p>
      </div><!-- #class -->

      <div id="url">
        <p><?php
$url = get_post_meta($post_id->ID, 'url', TRUE);
if (!preg_match('/^http:\/\/$/', $url)) {
  echo $url;
}
?></p>
      </div><!-- #url -->

<?php
}
?>

    </div><!-- #wrapper -->
  </body>
</html>
