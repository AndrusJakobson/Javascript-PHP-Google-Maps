<?php
require_once('connection.php');
$select = "SELECT * FROM google_maps";
$query = $mysqli->query($select);

$markers = [];

$data = $query->fetch_all(MYSQLI_ASSOC);
if(!empty($data)){
	foreach($data as $point){
		$markers[] = [
			'ID' => $point['ID'],
			'name' => $point["name"],
			'latitude' => $point["latitude"],
			'longitude' => $point["longitude"]
		];
	}
	exit(json_encode([
			'status' => 1,
			'markers' => $markers
		]));
}else{
	exit(json_encode([
		'status' => 0,
		'message' => 'Punkte ei leitud mitte ühtegi!'
	]));
}