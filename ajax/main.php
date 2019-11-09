<?php
#error_reporting(E_ALL);
#ini_set('display_errors', 1);
$d = $_REQUEST;
function makeJSON($status, $msg){
	return json_encode(array('status' => $status, 'msg' => $msg));
}