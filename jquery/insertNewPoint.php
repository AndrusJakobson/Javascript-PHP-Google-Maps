<?php
include_once('../connection.php');
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$lat = filter_input(INPUT_POST, 'lat', FILTER_VALIDATE_FLOAT);
$lng = filter_input(INPUT_POST, 'lng', FILTER_VALIDATE_FLOAT);

if(!empty($name) && !empty($lat) && !empty($lng)){
	$sql = "INSERT INTO google_maps (name, latitude, longitude) VALUES ('".$name."', '".$lat."', '".$lng."')";
	$getNewPoint = "SELECT * FROM google_maps WHERE name='" . $name . "' AND latitude=" . $lat . " AND longitude=" . $lng . " ORDER  BY ID DESC LIMIT 1";

	if($mysqli->query($sql)){
		$id = "";
		$query = $mysqli->query($getNewPoint);
		$point = $query->fetch_all(MYSQLI_ASSOC);
		$pointID = "";
		if(!empty($point)){
			$pointID = $point[0]['ID'];
		}

		exit(json_encode([
			'status' => 0,
			'ID' => $pointID,
			'message' => 'Point <b>' . $name . '</b> saved_successfully'
		]));
	}
	exit(json_encode([
		'name' => $name,
		'lat' => $lat,
		'long' => $lng
	]));	
}	

exit(json_encode([
	'status' => 0,
	'message' => 'Cannot Insert Empty Data!',
	'name' => $name,
	'lat' => $lat,
	'long' => $lng
]));