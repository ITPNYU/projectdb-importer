<!DOCTYPE html>
<html>
  <head>
  <script src="//code.jquery.com/jquery.min.js"></script>
  <script>
    jQuery(document).ready(function() {
      jQuery(function() {
        while( $('#pitch p').height() > $('#pitch_wrap').height() ) {
          $('#pitch p').css('font-size', (parseInt($('#pitch p').css('font-size')) - 1) + "px" );
        }
        while( $('#names p').height() > $('#names').height() ) {
          $('#names p').css('font-size', (parseInt($('#names p').css('font-size')) - 1) + "px" );
        }
      });
    });
  </script>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
<style type="text/css" media="print,screen">

body {
  background: rgb(204,204,204); 
}
page {
  background: white;
  display: block;
  margin: 0 auto;
  margin-bottom: 0.5cm;
  box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
}

@media print {
    @page {
        size: letter;
        margin: 0.5cm;
    }
}

/*@media print {
  body, page {
    margin: 0;
    box-shadow: 0;
  }
}
@page {
  size: 8.5in 11in;   width height 
  margin-left: .25in;
  margin-right: .25in;
  margin-top: .25in;
  margin-bottom: .25in;

}*/

body{
  margin:0;
  padding: 0;
  font-family: Lato, sans-serif;
  font-size: 12pt;
  font-weight:normal;
  background-color: #fff;
  color: #000;
  text-align: center;
  position: absolute;
  height: 100%;
}

#wrapper {
  /*padding:0.25in;*/

  margin:0.5in;
  width: 7.25in;
  /*height: 9.38in;*/
  height: 10.5in;/*margin-top: .25in;;*/
  margin-top: auto;/*margin-top: .25in;;*/
  margin-left: auto;
  margin-right: auto;
  border:2px solid #814fa0;
  /*	border-top:1px solid #000;*/
  position: relative;
}

.header1{
  float:center;
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

#qrcode_img{

  position: relative;

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
  font-size: 40pt;
  font-weight: bold;
  color:#000;
  max-height: 1.5in;
} /* TITLE   */

#names	{
  font-size: 18pt;
  font-weight: bold;
  color: #814fa0;
  line-height: 20pt;
  max-height: 1in;
} /* NAMES   */

#projectimg img {
  padding: 5pt;
  /*border:1pt solid #ccc;*/
  max-height: 2.5in;
  object-fit: contain;
}

#pitch_wrap {
  height: 1.5in;
  max-height: 1.5in;
  width: 100%;
}
#pitch	{
  padding:0;
  margin:0;
  width: 90%;
  margin-left: auto;
  margin-right: auto;
  height: 1.5in;
  max-height: 1.5in;
  font-size: 22pt;
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
  <page size="A4">
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
      <div id="header1" >
        <h1><? echo get_bloginfo('name'); ?></h1>

       <!--  <img width="696px" src="<? //echo get_header_image(); ?>"> -->
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

    <!-- <div> -->

      <?php

      $googer = new GoogleURLAPI();


// Test: Shorten a URL
//google form url


$comment_url = get_post_permalink($post_id)."&showfeedback=".get_option('feedback_password')."#respond";
//echo $comment_url;
//print_r($post_id);

$post_url_s = get_site_url() . '/?p=' . $post_id->ID ;
//echo get_post_permalink();

$google_form_url = "https://docs.google.com/forms/d/1jAqpuM7iMVbf07HWRYmNpyZQkAhj6sc2W65-BYaon8w/viewform?entry.1352385952=".utf8_decode(get_the_title($post_id));
$shortDWName = $googer->shorten($comment_url);
//Starting March 30, 2018, we will be turning down support for goo.gl URL shortener. Please see this blog post for detailed timelines and alternatives.
//print_r($shortDWName);
//echo $shortDWName; // returns http://goo.gl/i002

echo "<img src='https://chart.googleapis.com/chart?cht=qr&chs=120x120&chl=$post_url_s' />";
//echo $googer->qrcode($comment_url);
//echo "<br><br>"."<b>Leave feedback on this project</b>";



      ?>

    <!-- </div> --><!-- #feedback  -->

    </div><!-- #wrapper -->
  </body>
  </page>
</html>

<?php

class GoogleUrlApi {
  
  // Constructor
  function GoogleURLAPI($key ='AIzaSyA5MBEqg88FalLs_0nKeS3UclYyMvXdJ6M' ,$apiURL = 'https://www.googleapis.com/urlshortener/v1/url') {
    // Keep the API Url
    $this->apiURL = $apiURL.'?key='.$key;
  }
  
  // Shorten a URL
  function shorten($url) {


    // Send information along
    $response = $this->send($url);

    //print_r($response);
    // Return the result
    return isset($response['id']) ? $response['id'] : false;
  }
  
  // Expand a URL
  function expand($url) {
    // Send information along
    $response = $this->send($url,false);
    // Return the result
    return isset($response['longUrl']) ? $response['longUrl'] : false;
  }
  
  // Send information to Google
  function send($url,$shorten = true) {
    // Create cURL
    $ch = curl_init();
    // If we're shortening a URL...
    if($shorten) {
      curl_setopt($ch,CURLOPT_URL,$this->apiURL);
      curl_setopt($ch,CURLOPT_POST,1);
      curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode(array("longUrl"=>$url)));
      curl_setopt($ch,CURLOPT_HTTPHEADER,array("Content-Type: application/json"));
    }
    else {
      curl_setopt($ch,CURLOPT_URL,$this->apiURL.'&shortUrl='.$url);
    }
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    // Execute the post
    $result = curl_exec($ch);
    // Close the connection
    curl_close($ch);
    // Return the result
    return json_decode($result,true);
  }

  function qrcode($url,$size = 80){

    $img_url = "<div id=\"qrcode_img\" style=\"float: center; position: relative;bottom: -50px; overflow:hidden;\" ><img alt=\"leave a feedback by qr code\" src=\"https://chart.googleapis.com/chart?cht=qr&chs={$size}x{$size}&chl={$url}\" style=\"margin:0;padding:0;\"><p style=\"float: center;margin:0;padding:0;font-size: small;\">$url<p></div>";


    return $img_url;

  }   

}


?>
