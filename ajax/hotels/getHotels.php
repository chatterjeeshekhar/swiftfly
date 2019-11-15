<?php

$travel_purpose = $_REQUEST['travel_purpose'];
$order_by = $_REQUEST['order_by'];
$children = $_REQUEST['children_qty'];
$dest_id = $_REQUEST['dest_ids'];
$arrival_date = $_REQUEST['arrival_date'];
$departure_date = $_REQUEST['departure_date'];
$rooms = $_REQUEST['rooms'];

$curl = curl_init();
$url1 = "https://apidojo-booking-v1.p.rapidapi.com/properties/list?travel_purpose=".$travel_purpose."&search_id=none&order_by=".$order_by."&children_qty=".$children."&languagecode=en-us&children_age=&search_type=city&offset=0&dest_ids=".$dest_id."&guest_qty=".$adults."&arrival_date=".$arrival_date."&departure_date=".$departure_date."&room_qty=".$rooms;

//$url2 = "https://apidojo-booking-v1.p.rapidapi.com/properties/list?travel_purpose=leisure&search_id=none&order_by=popularity&children_qty=0&languagecode=en-us&children_age=&search_type=city&offset=0&dest_ids=-73635&guest_qty=2&arrival_date=2019-11-16&departure_date=2019-11-16&room_qty=1";
//echo $url1."<br>";
//echo $url2."<br>";
curl_setopt_array($curl, array(
	CURLOPT_URL => $url1,
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
	$res = processData(json_decode($response));
}

function processData($r){
	$arr = array();
	$r = $r->result;
	for($i=0; $i<count($r); $i++){
		$tempArr = array("name" => $r[$i]->hotel_name, "address" => $r[$i]->address_trans, "score" => $r[$i]->review_score, "free_cancel" => $r[$i]->is_free_cancellation, "img" => $r[$i]->main_photo_url);
		array_push($arr, $tempArr);
	}
	print_r($arr);
}