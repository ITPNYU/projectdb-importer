<h2>ProjectDB Importer</h2>
<div id="progress" style="width:500px;border:1px solid #ccc;"></div>
<div id="information" style="width"></div>

<?php
//error_reporting( E_ALL );
//ini_set( 'display_errors', '1' );
// YEN: or timeout!
set_time_limit( 0 );
$goodrest = 1;

if ( ob_get_level() == 0 ) ob_start();

if ( get_option( 'projectdb_api_url' ) && get_option( 'projectdb_api_key' ) ) {
  $projectdb = projectdb_download( array(
      'url' => get_option( 'projectdb_api_url' ),
      'key' => get_option( 'projectdb_api_key' ),
      'venue' => get_option( 'projectdb_venue' )
    ) );

  $projectdb_count = count( $projectdb['objects'] );
  //print_r(json_encode($projectdb['objects'][0]));
  if ( $projectdb_count > 0 ) {
    echo ' retrieved ' . $projectdb_count . ' projects.<br />';
    $all_posts = get_posts( array( 'numberposts' => -1 ) );
    $all_posts_id = array();
    foreach ( $all_posts as $p ) {
      array_push( $all_posts_id, get_post_meta( $p->ID, 'project_id', TRUE ) );
    }
    $all_projects_id = array();
    foreach ( $projectdb['objects'] as $p ) {
      array_push( $all_projects_id, $p['project_id'] );
    }
    echo "all_posts_id: ";
    echo "<ul>\n";
    foreach ( $projectdb['objects'] as $p ) {
      if ( !in_array( $p['project_id'], $all_projects_id ) ) {
        $posts = get_posts( array( 'numberposts' => 1, 'meta_key' => 'project_id', 'meta_value' => $p['project_id'] ) );
        if ( count( $posts ) > 0 ) {
          $attach = get_posts( array( 'post_type' => 'attachment', 'post_parent' => $posts[0]->ID ) );
          foreach ( $attach as $a ) {
            $ret = wp_delete_attachment( $a->ID, TRUE );
            if ( $ret ) {
              echo 'attachment deleted: ';
            }
            else {
              echo 'attachment deletion failure: ';
            }
            echo $posts[0]->ID . ' for project ' . $p['project_id'] . "\n";
          }
          $ret = wp_delete_post( $posts[0]->ID, TRUE );
          if ( $ret ) {
            echo 'post deleted: ';
          }
          else {
            echo 'post deletion failure: ';
          }
          echo $posts[0]->ID . ' for project ' . $p['project_id'] . "<br />\n";
        }
      }
      else {
        $post_id = projectdb_post( $p );
        echo '<li>';
        if ( is_wp_error( $post_id ) ) {
          echo $post_id->get_error_message();
        }
        else {
          echo $post_id . ': ' . get_post( $post_id )->post_title;
        }

        $percent = round( $goodrest/$projectdb_count*100 );
        echo str_pad( '', 4096 )."\n";
        // YEN: Quick BUT very Dirty!!!
        echo '<script language="javascript">
    document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.'%;background-color:#ddd;\">&nbsp;</div>";
    document.getElementById("information").innerHTML="'.$goodrest.' project(s) processed. ('.$percent.'%)";
    </script>';

        ob_flush();
        flush();

        echo "</li>\n";

        if ( $goodrest!=0 && $goodrest%5==0 ) sleep( 1 );

        $goodrest++;
      }

    }
    echo "</ul>\n";
  }
  else {
    echo 'cannot read projectdb.';
  }
  echo '<br />';
}
else {
  echo "ProjectDB API URL not set";
}

echo '<script language="javascript">document.getElementById("information").innerHTML="Process completed"</script>';
ob_end_flush();
?>
