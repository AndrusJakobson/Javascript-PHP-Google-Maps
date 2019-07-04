<?php
include_once('../connection.php');
$ID = filter_input(INPUT_POST, 'ID', FILTER_SANITIZE_STRING);

if(!empty($ID)){
	$sql = "DELETE FROM google_maps WHERE id=" . $ID;

	if($mysqli->query($sql)){
		exit(json_encode([
			'status' => 1,
			'ID' => $ID
		]));
	}
}	

exit(json_encode([
	'status' => 0,
	'message' => 'Cannot delete non-existent marker!',
]));