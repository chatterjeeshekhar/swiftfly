<title>Hotel Search Results</title>
<script src="node_modules/jquery/dist/jquery.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300italic,300,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
<script src="node_modules/jquery/dist/jquery.min.js"></script>
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css" />
<script type="text/javascript" src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
<link rel="stylesheet" href="node_modules/sweetalert2/dist/sweetalert2.min.css">
<style type="text/css">
body {
	padding: 15px;
}
#modifyRow {
	font-size: 15px;	
	margin-left: 5px;
	margin-right: 5px;
}
#modifyRow span {
	vertical-align: middle;
    line-height: normal;
}
#modifyRow .col-lg-2, .col-lg-1 {
	//padding: 0px;
	//margin: 0px;
}
#modifyRow .form-control {
	margin: 0px;
}
</style>
<style type="text/css">
.loadingBox {
    background-color: #ffffff;
    background-image: url("images/buffer.gif");
    background-size: 25px 25px;
    background-position:right 30px center;
    background-repeat: no-repeat;
}
</style>
<script type="text/javascript">
function getCityCode(id){
			document.getElementById(id).value = document.getElementById(id).value.toUpperCase();
			var select = document.getElementById(id);
			var str = select.value;
			if(str.length>=3){
				document.getElementById(id).classList.add("loadingBox");
				$.get("ajax/hotels/autoComplete.php?q="+str, function(data){
					//alert(data);
					//alert("Checkpoint1");
					var j = JSON.parse(data);
					if(j["status"]=="300"){
						//alert("Checkpoint2");
						//alert(j["msg"]);
						clearCityOptions(id);
						addCityOptions(id, j["msg"]);
					} else {
						clearCityOptions(id);
					}
					document.getElementById(id).classList.remove("loadingBox");
				});
			}
		}
		function addCityOptions(id, data){
			//alert("Checkpoint4");
			str = id+"list";
			$("#"+str).find("option").remove();
              $("#"+str).append($("<option/>").attr("value", "").text("---Select City---"));
              var j = JSON.parse(JSON.stringify(data));
              //alert(j["count"]);
              //alert(j[0]);
              if(j["count"]==0){
              	Swal.fire({
				  title: 'Error!',
				  text: 'City not found',
				  icon: 'error',
				  confirmButtonText: 'Ok'
				});
              }
              for($i=0; $i<j["count"]; $i++) {
                $("#"+str).append($("<option/>").attr({"value":j["rows"][$i].dest_id}).text(j["rows"][$i].label));
              }             
		}
		function clearCityOptions(id){
			//alert(id);
			//alert("Checkpoint3");
			document.getElementById(id+'list').innerHTML = "";
		}
		function submitHotel(){
			var hotel = document.getElementById("hotel").value;
			var purpose = document.getElementById("purpose").value;
			var arrival_date = document.getElementById("arrival_date").value;
			var departure_date = document.getElementById("departure_date").value;
			var adult = parseInt(document.getElementById("adult").value);
			var child = parseInt(document.getElementById("child").value);
			var room = parseInt(document.getElementById("rooms").value);

			if(hotel=="" || arrival_date=="Check-in" || departure_date=="Check-out" || adult==0){
				Swal.fire({
				  title: 'Error!',
				  text: 'All fields are mandatory',
				  icon: 'error',
				  confirmButtonText: 'Ok'
				});
			} else {
				
				if(hotel.match(/[a-z]/i)){
					Swal.fire({
					  title: 'Error!',
					  text: 'Select the city from the list',
					  icon: 'error',
					  confirmButtonText: 'Ok'
					});
				} else {
					document.getElementById("form3").submit();
				}
				
			}
		}
	

</script>
<script>
						  $(function() {
							$( "#departure_date, #arrival_date" ).datepicker();
						  });

				  </script>
<div class="container">
		<div class="card bg-danger text-white">
			<form action="hotels" method="post" onsubmit="return false;" id="form3" autocomplete="off">
			<div class="card-header">
				Modify Search
			</div>
			<div class="card-body">
				<div class="row" id="modifyRow">
					<div class="col-lg-3">
						<input type="text" id="hotel" list="hotellist" class="form-control" name="dest_ids" onKeyUp="getCityCode(this.id)" placeholder="City" required="">
						<datalist id="hotellist"></datalist>
					</div>
					<div class="col-lg-3">
						<select name="purpose" id="purpose" class="form-control">
							<option value="leisure">Leisure</option>
							<option value="business">Business</option>
						</select>
					</div>
					<div class="col-lg-2">
						<input id="arrival_date" type="text" name="arrival_date" value="Check-in" class="form-control" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Check-in';}" required="">
					</div>
					<div class="col-lg-2">
						<input id="departure_date" type="text" name="departure_date" class="form-control" value="Check-out" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Check-out';}" required="">
					</div>
					<div class="col-lg-2" style="text-align:right">
						<select id="order_by" name="order_by" class="form-control" required>
							<option value="popularity">Popularity</option>    
							<option value="price">Price</option> 
							<option value="distance">Distance from Centre</option>   
							<option value="deals">Deals</option> 
							<option value="review_score">Score</option>  						
						</select>
					</div>
				<br><Br>
					<div class="col-lg-4">
						
					</div>
					<div class="col-lg-1">
						<span>Adults</span>
					</div>
					<div class="col-lg-1">
						<input type="number" class="form-control" value="1" placeholder="Adult" id="adult" name="guest_qty" min="1" step="1" required="">
					</div>
					<div class="col-lg-1">
						<span>Children</span>
					</div>
					<div class="col-lg-1">
						<input type="number" class="form-control" value="0" placeholder="Child" id="child" name="children_qty" min="0" step="1" required="">
					</div>
					<div class="col-lg-1">
						<span>Infants</span>
					</div>
					<div class="col-lg-1">
						<input type="number" class="form-control" value="0" placeholder="Rooms" id="rooms" name="rooms" min="0" step="1" required="">
					</div>
					<div class="col-lg-2">
						<button onclick="submitHotel()" class="btn btn-warning" style="width:100%">Search</button>
					</div>
					
				</div>
			</div>
			</form>
		</div>
	</div>
	<br><BR>
<?php
#error_reporting(E_ALL);
#ini_set('display_errors', 1);
$travel_purpose = $_REQUEST['purpose'];
$order_by = $_REQUEST['order_by'];
$children = $_REQUEST['children_qty'];
$adults = $_REQUEST['guest_qty'];
$dest_id = $_REQUEST['dest_ids'];
$arrival_date = date_format(date_create($_REQUEST['arrival_date']),"Y-m-d");
$departure_date = date_format(date_create($_REQUEST['departure_date']),"Y-m-d");
$rooms = $_REQUEST['rooms'];

$curl = curl_init();
$url = "https://apidojo-booking-v1.p.rapidapi.com/properties/list?price_filter_currencycode=INR&travel_purpose=".$travel_purpose."&search_id=none&order_by=".$order_by."&children_qty=".$children."&languagecode=en-us&children_age=&search_type=city&offset=0&dest_ids=".$dest_id."&guest_qty=".$adults."&arrival_date=".$arrival_date."&departure_date=".$departure_date."&room_qty=".$rooms;
//echo $url;
curl_setopt_array($curl, array(
	CURLOPT_URL => $url,
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
	processData(json_decode($response));
}

function processData($r){
	$arr = array();
	$r = $r->result;
	for($i=0; $i<count($r); $i++){
		$price = "";
		if($r[$i]->price_breakdown->all_inclusive_price!=null){
			$price =  $r[$i]->price_breakdown->all_inclusive_price;
		} else {
			$price = "NaN";
		}
		$tempArr = array("name" => $r[$i]->hotel_name_trans, "address" => $r[$i]->address_trans, "price" => $price, "currency" => $r[$i]->currency_code, "score" => $r[$i]->review_score, "free_cancel" => $r[$i]->is_free_cancellable, "img" => $r[$i]->main_photo_url);
		//print_r($tempArr);
		//print_r($r[$i]->price_breakdown->all_inclusive_price);
		array_push($arr, $tempArr);
	}
	print_data($arr);
}
function print_data($r){
	//echo $r[0]->name;
	$r = json_encode($r);
	//print_r($r);
	$r = json_decode($r);
	for($i=0; $i<count($r); $i++){
		$color = "ffffff";
		if($i%2==1){
			$color = "e2e2e2";
		}
		$card = "";
		$card = $card.'<div class="card"><div class="card-body" style="background-color:#'.$color.'"><div class="row"><div class="col-lg-1"><img src="'.$r[$i]->img.'" width="100%"></div><div class="col-lg-6"><h3>'.$r[$i]->name.'</h3><b>Address</b>: '.$r[$i]->address.'</div><div class="col-lg-1" style="background-color:#3b5998;text-align:center; padding-top:12px;margin:4px;"><font color="white"><h2>'.$r[$i]->score.'</h2></font></div>';
		if($r[$i]->free_cancel==1){
			$card = $card.'<div class="col-lg-1" style="background-color:green;text-align:center;margin:4px;padding-top:7px;display: table-cell;vertical-align: middle"><font color="white">Free Cancel</font></div>';
		} else {
			$card = $card.'<div class="col-lg-1" style="background-color:#'.$color.';text-align:center;margin:4px;padding-top:20px;display: table-cell;vertical-align: middle"><font color="white"></font></div>';
		}

		$card = $card.'<div class="col-lg-2" style="text-align:right;"><h3>'.$r[$i]->currency.' '.$r[$i]->price.'</h3> per night</div></div></div></div><Br>';
		echo $card;
	}
}
?>