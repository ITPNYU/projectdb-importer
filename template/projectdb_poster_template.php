<!DOCTYPE html>
<html>
  <head>
<style type="text/css" media="print,screen">
@page {
  size: 8.5in 11in;  /* width height */
  margin-left: .5in;
  margin-right: .5in;
  margin-top: .5in;
  margin-bottom: .5in;
}

body{
  margin:0;
  padding: 0;
  font-family: Helvetica;
  font-size: 12pt;
  font-weight:normal;
  background-color: #fff;	
  color: #000;
  text-align: center;
}
	
#wrapper {
  padding:0;
  margin:0;
  width: 6.5in; 
  height: 10in;
  margin-top: .5in;
  margin-left: auto;
  margin-right: auto;
  border:2px solid #088889;
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
  font-size: 30pt;
  color:#000;
} /* TITLE   */ 

#names	{
  font-size: 18pt; 
  font-weight: bold;
  color:#088889;
  line-height: 30pt;
} /* NAMES   */
#names_sm	{
  font-size: 16pt; 
  font-weight: bold;
  color:#e32f4a;
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
  font-size: 15pt;
  text-align: center;
} /* PITCH   */
	
#class	{
  font-size: 12pt;
} /* CLASSES */
	
#url {  
  color:#088889;     
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
  $meta = get_post_meta($post_id->ID);
  //setup_postdata($post_id); // check for null/error
?>
    <div id="wrapper">
      <div id="header1">
        <img width="100%" src="//itp.nyu.edu/shows/spring2014/files/2014/03/copy-cropped-show-banner.jpg">
      </div><!-- #header1 -->
      <div id="title">
        <h1><?php echo get_the_title($post_id) ?></h1>
      </div><!-- #title -->
      <div id="names">
        <p><?php var_dump($meta); ?></p>
      </div><!-- #names -->
<?php
}
?>

    </div><!-- #wrapper -->
  </body>
</html>
