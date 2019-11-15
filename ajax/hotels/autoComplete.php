<?php
include '../main.php';
$q = $_REQUEST['q'];
if(ctype_alnum($q)){} else {die();}
$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => "https://apidojo-booking-v1.p.rapidapi.com/locations/auto-complete?languagecode=en-us&text=".$q,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => array(
		"x-rapidapi-host: apidojo-booking-v1.p.rapidapi.com",
		"x-rapidapi-key: FUtTTNdLztmsh6S1nSNSqa78mgO5p1xZXFMjsnsVQl6Hlw3Nvz"
	),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
	echo processData(json_decode($response));
}

function processData($r){
	$arr = array();
	for ($i=0; $i<count($r); $i++){
		if($r[$i]->dest_type=="city"){
			$tempArr = array("label" => $r[$i]->label, "dest_id" => $r[$i]->dest_id);
			array_push($arr, $tempArr);
		}
	}
	$arr = makeJSON(300, array("count" => count($arr), "rows" => $arr));
	return ($arr);
}