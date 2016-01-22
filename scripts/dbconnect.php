<?php
if (!(DB_USERNAME === null || DB_PASSWORD === null)) {
	$GLOBALS['data'] = [
		'host' => DB_HOST,
		'username' => DB_USERNAME,
		'password' => DB_PASSWORD
	];
} else {
	$GLOBALS['data'] = json_decode(file_get_contents(SETTINGS_ROOT . "/db.json"), true);
}
function DBConnect($db = null)
{
	$data = $GLOBALS['data'];
	if(class_exists('mysqli')) {
		if ($db != null) {
			$conn = new mysqli($data['host'], $data['username'], $data['password'], $db);
		} else {
			$conn = new mysqli($data['host'], $data['username'], $data['password']);
		}
	}else{
		return null;
	}
	return $conn;
}

function DBCheck($conn)
{
	if($conn===null){
		return -1;
	}
	return $conn->connect_errno;
}