<?php
include '../main.php';
$return = "";
$source = $d["source"];
$destination = $d["destination"];
$date = date_format(date_create($d["forward"]),"Y-m-d");
if($d["comeback"]!=""){
	$return = date_format(date_create($d["comeback"]),"Y-m-d");
}
$class = $d["myclass"];
$adult = $d["adult"];
$child = $d["child"];
$infants = $d["infant"];
$currency = "INR";
$country = "IN";

echo createSession($return, $date, $source, $destination, $class, $children, $adults, $infants, $currency, $country);
function createSession($return, $date, $source, $destination, $class, $children, $adults, $infants, $currency, $country){
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://skyscanner-skyscanner-flight-search-v1.p.rapidapi.com/apiservices/pricing/v1.0",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HEADER => 1,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => "inboundDate=".$return."&cabinClass=".$class."&children=".$children."&infants=".$infants."&country=".$country."&currency=".$currency."&locale=en-US&originPlace=".$source."-sky&destinationPlace=".$destination."-sky&outboundDate=".$date."&adults=".$adults,
		CURLOPT_HTTPHEADER => array(
			"content-type: application/x-www-form-urlencoded",
			"x-rapidapi-host: skyscanner-skyscanner-flight-search-v1.p.rapidapi.com",
			"x-rapidapi-key: FUtTTNdLztmsh6S1nSNSqa78mgO5p1xZXFMjsnsVQl6Hlw3Nvz"
		),
	));
	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);
	$headers = [];
	$output = rtrim($response);
	$data = explode("\n",$output);
	$headers['status'] = $data[0];
	array_shift($data);
	foreach($data as $part){
	    $middle = explode(":",$part,2);
	    if(!isset($middle[1])) { 
	    	$middle[1] = null; 
	    }
	    $headers[trim($middle[0])] = trim($middle[1]);
	}
	$headers = json_decode(json_encode($headers));
	$sessionId = substr($headers->Location, 64);
	if ($err || $sessionId==false) {
		return makeJSON(203, "Invalid Search");
	} else {
		return makeJSON(200, array("session" => $sessionId));
	}
}