<?php
include '../main.php';
$sessionId = $d['session'];
$response = getFlights($sessionId);
$response = json_decode($response);
$response = $response->msg->flights;
$finalArr = (processData($sessionId, $response));
$finalArr = cleanFlights($finalArr);
echo makeJSON(200, array("rows" => $finalArr));
//print_r($finalArr);
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
			"x-rapidapi-key: 4e29a7917fmsha100132be880c55p1409edjsn01cb4c45395f"
		),
	));
	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);
	if ($err) {
		return makeJSON(203, "Failed to get flights");
	} else {
		//echo $response;
		return makeJSON(200, array("flights" => $response));
	}
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