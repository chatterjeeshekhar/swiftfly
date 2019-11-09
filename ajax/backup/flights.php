<?php
#error_reporting(E_ALL);
#ini_set('display_errors', 1);
function makeJSON($status, $msg){
	return json_encode(array('status' => $status, 'msg' => $msg));
}
$return = "";
//$return = "2020-09-10";
$date = "2020-02-04";
$source = "DEL";
$destination = "SIN";
$class = "economy"; //business, economy, first
$children = 0;
$adults = 2;
$infants = 0;
$currency = "USD";
$country = "US";
$sessionId = createSession($return, $date, $source, $destination, $class, $children, $adults, $infants, $currency, $country);
//$sessionId = '{"status":200,"msg":{"session":"ee325d2a-d982-4330-ad1a-9c690654f6e1"}}';
$sessionId = json_decode($sessionId);

if($sessionId->status==200){
	$sessionId = $sessionId->msg->session;
	$response = getFlights($sessionId);
	$finalArr = (processData($sessionId, $response));
	$finalArr = cleanFlights($finalArr);
	print_r($finalArr);
	
}
function cleanFlights($finalArr){
	$airportCodeArr = getPlaces($finalArr["places"]);
	$flightCodeArr = getFlightCodes($finalArr["carriers"]);
	for($i=0; $i<count($finalArr["flights"]); $i++){
		$finalArr["flights"][$i]["source"] = $airportCodeArr[$finalArr["flights"][$i]["source"]];
		$finalArr["flights"][$i]["destination"] = $airportCodeArr[$finalArr["flights"][$i]["destination"]];
		if(count($finalArr["flights"][$i]["stops"])>=0){
			for($j=0; $j<count($finalArr["flights"][$i]["stops"]); $j++){
				$finalArr["flights"][$i]["stops"][$j] = $airportCodeArr[$finalArr["flights"][$i]["stops"][$j]];
			}
			for($j=0; $j<=count($finalArr["flights"][$i]["stops"]); $j++){
				$finalArr["flights"][$i]["carrier"][$j]->flight_code = $flightCodeArr[$finalArr["flights"][$i]["carrier"][$j]->CarrierId]["code"]."-".$finalArr["flights"][$i]["carrier"][$j]->FlightNumber;
				$finalArr["flights"][$i]["carrier"][$j]->carrier_name = $flightCodeArr[$finalArr["flights"][$i]["carrier"][$j]->CarrierId]["name"];
				$finalArr["flights"][$i]["carrier"][$j]->carrier_logo = $flightCodeArr[$finalArr["flights"][$i]["carrier"][$j]->CarrierId]["logo"];
				
			}
		}
		
		if($i<10){
			$finalArr["flights"][$i]["price"] = $finalArr["planners"][$i]->PricingOptions;
		}
	}
	$finalArr["query"]->OriginPlace = $airportCodeArr[$finalArr["query"]->OriginPlace];
	$finalArr["query"]->DestinationPlace = $airportCodeArr[$finalArr["query"]->DestinationPlace];
	unset($finalArr["carriers"]);
	unset($finalArr["places"]);
	unset($finalArr["planners"]);
	return $finalArr;
}

function getFlightCodes($arr){
	$flightCodeArr = array();
	for($i=0; $i<count($arr); $i++){
		$flightCodeArr[$arr[$i]->Id] = array("code" => $arr[$i]->Code, "name" => $arr[$i]->Name, "logo" => $arr[$i]->ImageUrl);
	}
	return $flightCodeArr;
}

function getPlaces($arr){
	$placeArr = array();
	for($i=0; $i<count($arr); $i++){
		$placeArr[$arr[$i]->Id] = $arr[$i]->Code."|".$arr[$i]->Name;
	}
	return $placeArr;
}


function processData($sessionId, $response){
	$flightArr = array();
	$response = json_decode($response);
	$legs = ($response->Legs);
	$cnt = count($legs);
	for($i=0; $i<count($legs); $i++){
		$r = $legs[$i];
		$tempArr = array("id" => $r->Id, "source" => $r->OriginStation, "destination" => $r->DestinationStation, "departure" => $r->Departure, "arrival" => $r->Arrival, "duration" => $r->Duration, "stop_count" => count($r->Stops), "stops" => $r->Stops,  "carrier" => $r->FlightNumbers, "direction" => $r->Directionality);
		array_push($flightArr, $tempArr);
	}
	$finalArr = array("session" => $sessionId, "flight_count" => $cnt, "query" => $response->Query, "carriers" => $response->Carriers, "planners" => $response->Itineraries, "places" => $response->Places, "flights" => $flightArr);
	return $finalArr;
}

function getFlights($sessionId){
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://skyscanner-skyscanner-flight-search-v1.p.rapidapi.com/apiservices/pricing/uk2/v1.0/".$sessionId."?sortType=duration&sortOrder=asc&pageIndex=0&pageSize=10",
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
		return makeJSON(203, "Failed to get flights");
	} else {
		//echo $response;
		return ($response);
	}
}

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
?>