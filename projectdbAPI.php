<?php

require_once("db.inc");

$pdo_pdb = new PDO( "mysql:host=$mysql_hostname;dbname=project_db_test", $mysql_web_username, $mysql_web_password );

$pdo_ppl = new PDO( "mysql:host=$mysql_hostname;dbname=itpdir", $mysql_web_username, $mysql_web_password );

function getWallLinks() {

	global $pdo_pdb, $pdo_ppl;

	//if empty($netid) return array();


	//$sql = " select * from userProject, venueProject ,project where userProject.user_id in (".implode(',', $netid).") and venueProject.venue_id = '165' and userProject.project_id = project.project_id and venueProject.project_id = project.project_id;"

	$sql = "select user_id, url from userProject, venueProject ,project where venueProject.venue_id = '165' and userProject.project_id = project.project_id and venueProject.project_id = project.project_id;";



	$result = sqlQuery($pdo_pdb, $sql);

	return $result;


}

function getZoom($project_ids_array = array(), $venue_id = 157) {

	global $pdo_pdb, $pdo_ppl;

	if (!$project_ids_array) return array();

	if ( $project_ids_array[0] == -1){

		$sql ="SELECT project.project_id, zoom_link, zoom_status, room_id, position_id From project LEFT JOIN venueProject ON venueProject.project_id = project.project_id WHERE venue_id = $venue_id ORDER BY zoom_status;";

		
	}else{

		$sql = "SELECT project.project_id, zoom_link, zoom_status, room_id, position_id From project LEFT JOIN venueProject ON venueProject.project_id = project.project_id WHERE venue_id = $venue_id and project.project_id in (".implode(',', $project_ids_array).") ORDER BY zoom_status";

	}



	$result = sqlQuery($pdo_pdb, $sql);

	return $result;


}

function isThesisThere($venue_id,$user_id) {

	global $pdo_pdb, $pdo_ppl;

	$sql = "select userProject.project_id from userProject, venueProject where venueProject.venue_id = ? and userProject.project_id = venueProject.project_id and userProject.user_id = ?;";


	$result = sqlQuery($pdo_pdb, $sql,array($venue_id,$user_id));

	return $result;


}

function getDocs($document_id) {

	global $pdo_pdb, $pdo_ppl;

	$sql = 'select document, main_image, vslideshow, alt_text from projectDocuments where secret = 0 and document_id in ('.$document_id.');';


	$documents = sqlQuery($pdo_pdb, $sql);

	return $documents;


}

function getClassInfo($class_id) {

	global $pdo_pdb, $pdo_ppl;

	$sql = 'select class_name, description, instructor, course_id, refno from class where class_id in ('.$class_id.');';
	$classes = sqlQuery($pdo_pdb, $sql);

	return $classes;



}


function sqlQuery($pdo, $query, $params = array()) {


	$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
	$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

	//echo var_dump($query);

	$query_p = $pdo->prepare($query);

	if (!$query_p) {

		$error_msg = $pdo->errorInfo();

		//die(var_dump($error_msg));

		die('ITP::PDO::SERVER_MIGRATION::SQL_ERROR::->'.$error_msg[2]);

		
	}

	$query_p->execute($params);

	$results = $query_p->fetchAll(PDO::FETCH_ASSOC);
	//$results = $query_p->fetchAll();


	return $results;
}

function projectdb_download( $venue_id ) {

	global $pdo_pdb, $pdo_ppl;

	// Step 1: find all project_id of an venue

	$sql = 'select project_id from venueProject where venue_id = ? and approved = ?;';

	$project_ids = sqlQuery($pdo_pdb,$sql,array( $venue_id,1));

	$project_ids = array_column($project_ids, 'project_id');

	//$project_ids = implode(",", $project_ids);

	// Step 2:

	//$sql = 'select project.project_id, project_name,elevator_pitch, description,url,keywords,video,zoom_link, GROUP_CONCAT(DISTINCT class_id) as class_id, GROUP_CONCAT(DISTINCT document_id) as document_id, GROUP_CONCAT(DISTINCT user_id) as user_id from project, classProject, projectDocuments, userProject where project.project_id in ('.implode(',', $project_ids).') and project.project_id = classProject.project_id and project.project_id = projectDocuments.project_id and project.project_id = userProject.project_id Group by project.project_id;';

	$sql = 'SELECT project.project_id, project.project_name, project.elevator_pitch, project.description, project.url, project.keywords, project.video, project.zoom_link, GROUP_CONCAT(DISTINCT classProject.class_id) AS `class_id`, GROUP_CONCAT(DISTINCT projectDocuments.document_id) AS `document_id`, GROUP_CONCAT(DISTINCT userProject.user_id) AS `user_id` FROM project LEFT JOIN classProject ON project.project_id = classProject.project_id LEFT JOIN projectDocuments ON project.project_id = projectDocuments.project_id LEFT JOIN userProject ON project.project_id = userProject.project_id where project.project_id in ('.implode(",", $project_ids).');';


	//$sql = 'SELECT project.project_id, project.project_name, project.elevator_pitch, project.description, project.url, project.keywords, project.video, project.zoom_link FROM project where project.project_id in ('.implode(",", $project_ids).');';

	echo $sql;

	//exit($sql );

	$projects = sqlQuery($pdo_pdb,$sql);




	return $projects;
}

function itpdir_lookup( $project_id ) {

	global $pdo_pdb, $pdo_ppl;

	// find $creators
	$sql = 'select DISTINCT user_id from userProject where project_id = ?';
	$creator_ids = sqlQuery($pdo_pdb,$sql,array($project_id));
	$creators = array();

	foreach ($creator_ids as $person) {

		$sql = "select CONCAT(preferred_firstname,\" \", preferred_lastname) as name from nyu_official where netid = ? ;";
		$person_r = sqlQuery($pdo_ppl,$sql,array($person['user_id']));

		$creators[] = ($person_r) ? $person_r[0] : $person['user_id'];
		// code...
	}

	return $creators;
}





?>
