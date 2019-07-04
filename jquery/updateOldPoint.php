<?php
include_once('../connection.php');
$ID = filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_STRING);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$lat = filter_input(INPUT_POST, 'lat', FILTER_VALIDATE_FLOAT);
$lng = filter_input(INPUT_POST, 'lng', FILTER_VALIDATE_FLOAT);

if(!empty($name) && !empty($lat) && !empty($lng) && !empty($ID)){
	$sql = "UPDATE google_maps SET name='" . $name . "', latitude=" . $lat . ", longitude= " . $lng . " WHERE id=" . $ID;

	if($mysqli->query($sql)){
		exit(json_encode([
			'status' => 1,
			'ID' => $ID
		]));
	}
}	

exit(json_encode([
	'status' => 0,
	'message' => 'Cannot Insert Empty Data!',
	'sql' => $sql,
	'ID' => $ID,
	'name' => $name,
	'lat' => $lat,
	'long' => $lng
]));