<?php
include '../main.php';
$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => "https://skyscanner-skyscanner-flight-search-v1.p.rapidapi.com/apiservices/autosuggest/v1.0/UK/GBP/en-GB/?query=".$d['q'],
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => array(
		"x-rapidapi-host: skyscanner-skyscanner-flight-search-v1.p.rapidapi.com",
		"x-rapidapi-key: FUtTTNdLztmsh6S1nSNSqa78mgO5p1xZXFMjsnsVQl6Hlw3Nvz"
	),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo makeJSON(203, "Could not retrieve airport IATA code");
} else {
	echo makeJSON(200, processCode($response));
}

function processCode($response){
	$arr = array();
	$response = json_decode($response)->Places;
	for($i=0; $i<count($response); $i++){
		$tmp = array("airportCode" => rtrim($response[$i]->PlaceId, "-sky"), "placename" => $response[$i]->PlaceName, "country" => $response[$i]->CountryName);
		array_push($arr, $tmp);
	}
	return array("count" => count($arr), "rows" => $arr);
}