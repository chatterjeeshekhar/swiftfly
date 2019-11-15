<?php
include 'ajax/main.php';
$source = $d["source"];
$destination = $d["destination"];
$today = date_format(date_create(""),"Y-m-d");
$forward = date_format(date_create($d["forward"]),"Y-m-d");
$comeback = "";
if($d["comeback"]=="Return Date"){
	$comeback = "";
}
if($d["comeback"]!=""){
	$comeback = date_format(date_create($d["comeback"]),"Y-m-d");
}
$class = $d["myclass"];
$adult = $d["adult"];
$child = $d["child"];
$infant = $d["infant"];
if($source=="" || $destination=="" || $forward=="" || $class=="" || $adult=="" || $child=="" || $infant=="" || ($comeback!="" && strtotime($comeback)<strtotime($forward)) || (strtotime($forward)<strtotime($today))){
	die(header("location: /"));
}
$vars = "'$source', '$destination', '$forward', '$comeback', '$class', '$infant', '$child', '$adult'";

?>

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
.loadingBox {
    background-color: #ffffff;
    background-image: url("images/buffer.gif");
    background-size: 25px 25px;
    background-position:right 30px center;
    background-repeat: no-repeat;
}
</style>
<script>
	function getCode(id){
			document.getElementById(id).value = document.getElementById(id).value.toUpperCase();
			var select = document.getElementById(id);
			var str = select.value;
			if(str.length>=3){
				document.getElementById(id).classList.add("loadingBox");
				$.get("ajax/flights/airportCode.php?q="+str, function(data){
					var j = JSON.parse(data);
					if(j["status"]=="200"){
						//alert(j["msg"]);
						clearOptions(id);
						addOptions(id, j["msg"]);
					} else {
						clearOptions(id);
					}
					document.getElementById(id).classList.remove("loadingBox");
				});
			}
		}
		function addOptions(id, data){
			str = id+"list";
			$("#"+str).find("option").remove();
              $("#"+str).append($("<option/>").attr("value", "").text("---Select Airport---"));
              var j = JSON.parse(JSON.stringify(data));
              //alert(j["count"]);
              //alert(j[0]);
              if(j["count"]==0){
              	Swal.fire({
				  title: 'Error!',
				  text: 'Airport not found',
				  icon: 'error',
				  confirmButtonText: 'Ok'
				});
              }
              for($i=0; $i<j["count"]; $i++) {
                $("#"+str).append($("<option/>").attr({"value":j["rows"][$i].airportCode}).text(j["rows"][$i].placename));
              }             
		}
		function clearOptions(id){
			//alert(id);
			document.getElementById(id+'list').innerHTML = "";
		}
$(function() {
	$( "#onward,#comeback").datepicker();
});
function myFormButton(){
	var source = document.getElementById("source").value;
	var destination = document.getElementById("destination").value;
	var onward = document.getElementById("onward").value;
	var comeback = document.getElementById("comeback").value;
	var adult = parseInt(document.getElementById("adult").value);
	var child = parseInt(document.getElementById("child").value);
	var infant = parseInt(document.getElementById("infant").value);
	//alert(onward);
	if(source=="" || destination=="" || onward=="" || adult==0 || onward=="Onward Date"){
		Swal.fire({
		  title: 'Error!',
		  text: 'All fields are mandatory',
		  icon: 'error',
		  confirmButtonText: 'Ok'
		});
	} else {
		if(source.length!=3 || destination.length!=3){
			Swal.fire({
			  title: 'Error!',
			  text: 'Select the airport from the list',
			  icon: 'error',
			  confirmButtonText: 'Ok'
			});
		} else {
			document.getElementById("myform").submit();
		}
	}
}
</script>
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

<body onload="onload()" style="font-family:Arial;">
<div id="searchBody"> 
	<script type="text/javascript">
	var startval = getRandomInt(7, 12);
	var midval = getRandomInt(42, 57);
	var finalval = getRandomInt(96, 100);
	function onload(){
		document.getElementById("searchBody").style.display = "block";
		document.getElementById("resultBody").style.display = "none";
		getSession(<?php echo $vars; ?>);
	}
	function getRandomInt(min, max) {
	    min = Math.ceil(min);
	    max = Math.floor(max);
	    return Math.floor(Math.random() * (max - min + 1)) + min;
	}
	function getSession(source, destination, forward, comeback, myclass, infant, child, adult){
		document.title = "Searching flights from <?php echo $_REQUEST['source'];?> to <?php echo $_REQUEST['destination']; ?>";
		var vars = "source="+source+"&destination="+destination+"&forward="+forward+"&comeback="+comeback+"&myclass="+myclass+"&infant="+infant+"&child="+child+"&adult="+adult;
		$.get("ajax/flights/createSession?"+vars, function(data){
			document.getElementById("bodyarea").innerHTML = "";
			var j = JSON.parse(data);
			if(j["status"]==200){
				addMSG("Session created", 1);
				var session = j["msg"]["session"];
				move(startval, midval);
				getFlights(session);
			} else {
				addMSG("Session could not be created", 0);
			}
		});
	}
	function getFlights(session){
		$.get("ajax/flights/getFlights?session="+session, function(data){
			var j = JSON.parse(data);
			if(j["status"]==200){
				addMSG("Flights retrieved", 1);
				//var flights = j["msg"]["flights"];
				move(midval, finalval);
				setTimeout(function(){ listData(j["msg"]); 
					document.title = "Flights from <?php echo $_REQUEST['source'];?> to <?php echo $_REQUEST['destination']; ?>";
				}, 2500);
			} else {
				addMSG("Flights could not be retrived", 0);
			}
		});
	}
	function addMSG(msg, t){
		if(t==0){
			color = "red";
		} else {
			color = "green";
		}
		var str = "<font color="+color+"><h2>"+msg+"</h2></font>";
		//document.getElementById("bodyarea").innerHTML = document.getElementById("bodyarea").innerHTML+str;
	}
	function listData(data){
		document.getElementById("searchBody").style.display = "none";
		document.getElementById("resultBody").style.display = "block";
		processData(data);

	}
</script>
	<div id="bodyarea"></div>
	<style>
	#myProgress {
	  border-radius: 1em;
	  width: 100%;
	  background-color: #ddd;
	}

	#myBar {
	  border-radius: 1em;
	  width: 10%;
	  height: 30px;
	  background-color: #C4122F;
	  text-align: center;
	  line-height: 30px;
	  color: white;
	  font-family: Arial;
	}
	.loadingStyle{ 
		text-align: center;
		margin: auto;
		width: 50%;
		height: 50%;
	}
	</style>
	<div class="loadingStyle">
		<img src="images/loading.gif">
		<div id="myProgress">
		  <div id="myBar">10%</div>
		</div>
	</div>
	<script>
	var i = 0;
	function move(w, x) {
	  if (i == 0) {
	    i = 1;
	    var elem = document.getElementById("myBar");
	    var width = w;
	    var id = setInterval(frame, 20);
	    function frame() {
	      if (width >= x) {
	        clearInterval(id);
	        i = 0;
	      } else {
	        width++;
	        elem.style.width = width + "%";
	        elem.innerHTML = width  + "%";
	      }
	    }
	  }
	}
	</script>

</div>
<div id="resultBody" style="display:none">
	<script type="text/javascript">
	function processData(data){
		var j = JSON.parse(JSON.stringify(data));
		j = JSON.parse(JSON.stringify(j["rows"]));
		//j = JSON.parse(JSON.stringify(j));
		flight_count = j["flight_count"];
		head_info = j["query"];
		r = j["flights"];
		makeHeadBar(head_info);
		makeCard(flight_count, r);
		//r = JSON.parse(JSON.stringify(j["flights"]));
		//d = j["status"];
		//console.log(j["flights"]);
		//document.getElementById("resultCards").innerHTML = j->flights;
	}
	function makeHeadBar(r){
		var j = JSON.parse(JSON.stringify(r));
		document.getElementById("adult").value = <?php echo $_REQUEST['adult'];?>;
		document.getElementById("child").value = <?php echo $_REQUEST['child'];?>;
		document.getElementById("infant").value = <?php echo $_REQUEST['infant'];?>;
		document.getElementById("source").value = j["OriginPlace"].split("|")[0];
		document.getElementById("destination").value = j["DestinationPlace"].split("|")[0];
		document.getElementById("myclass").value = j["CabinClass"];
		document.getElementById("onward").value = j["OutboundDate"];
		if(j["InboundDate"]!=null){
			document.getElementById("comeback").value = j["InboundDate"];
		}
	}
	function splitTime(n){
		var hr = parseInt(n/60);
		var m = n%60;
		return hr+" h "+m+" m";
	}
	function makeCard(n, r){
		var j = JSON.parse(JSON.stringify(r));
		var source_code, source_name, destination_code, destination_name, departure, duration, arrival, stops, price;
		for ($i=0; $i<n; $i++){
			source = j[$i]["source"].split("|");
			source_code = source[0];
			source_name = source[1];
			destination = j[$i]["destination"].split("|");
			destination_code = destination[0];
			destination_name = destination[1];
			departure = j[$i]["departure"];
			departure = departure.substring(departure.length - 8, departure.length).substring(0, 5);
			arrival = j[$i]["arrival"];
			duration = splitTime(j[$i]["duration"]);
			stops = j[$i]["stop_count"];
			price = "NaN";
			if(j[$i]["price"]!=null){
				price = JSON.parse(JSON.stringify(j[$i]["price"]));
				price = price[0]["Price"];
			}
			arrival = arrival.substring(arrival.length - 8, arrival.length).substring(0, 5);
			var card = '<div class="card">'+cardBody(source_code, source_name, destination_code, destination_name, departure, duration, arrival, stops, price);
			card = card+cardFooter(stops+1, j[$i]["carrier"], j[$i]["stops"], source_code, source_name, destination_code, destination_name);
			card = card+'</div>';
			document.getElementById("resultCards").innerHTML = document.getElementById("resultCards").innerHTML+card+"<br><Br>";
		}
		//console.log(j[0]["destination"]);
	}
	function cardFooter(n, r, s, source_code, source_name, destination_code, destination_name){
		var j = JSON.parse(JSON.stringify(r));
		var s = JSON.parse(JSON.stringify(s));
		stopArr = [];
		stopArr.push(source_code+"|"+source_name);
		for ($x=0; $x<n-1; $x++){
			stopArr.push(s[$x]);
		}
		stopArr.push(destination_code+"|"+destination_name);
		//console.log(stopArr);
		var str = "";
		//console.log(s);
		for ($x=0; $x<n; $x++){
			source = stopArr[$x].split("|");
			source_code = source[0];
			source_name = source[1];
			destination = stopArr[$x+1].split("|");
			destination_code = destination[0];
			destination_name = destination[1];
			var card_footer = '<div class="card-footer"><div class="row"><div class="col-lg-2"><h4><span id="source_code">'+source_code+'</span></h4><span id="source_name_sub1" style="font-size:12">'+source_name+'</span></div><div class="col-lg-2"><h4><span id="destination_code">'+destination_code+'</span></h4><span id="destination_name_sub1" style="font-size:12">'+destination_name+'</span></div><div class="col-lg-2" style="text-align:right"><h4><span id="flight_code1">'+j[$x]["flight_code"]+'</span></h4><span style="font-size:12">Aircraft Code</span></div><div class="col-lg-1"><img src="'+j[$x]["carrier_logo"]+'" width="100%" align="middle"></div><div class="col-lg-4"><h3 id="carrier1">'+j[$x]["carrier_name"]+'</h3><span style="font-size:12">Airlines</span></div></div></div>';
				str = str+card_footer;
		}
		return str;

	}
	function cardBody(source_code, source_name, destination_code, destination_name, departure, duration, arrival, stops, price){
		var card_body = '<div class="card-body"><div class="row"><div class="col-lg-2"><font size="1">Origin</font><h2><span id="source_code">'+source_code+'</span></h2><span id="source_name">'+source_name+'</span></div><div class="col-lg-2" style="border-right:1px solid #000"><font size="1">Destination</font><h2><span id="destination_code">'+destination_code+'</span></h2><span id="destination_name">'+destination_name+'</span></div><div class="col-lg-2" style="text-align:right;"><font size="1">Time (HH:mm)</font><h2><span id="departure">'+departure+'</span></h2>Departure</div><div class="col-lg-1" style="text-align:center;"><Br><b><span id="duration">'+duration+'</span></b><font size="2"><br>Duration</font></div><div class="col-lg-2"><font size="1">Time (HH:mm)</font><h2><span id="arrival">'+arrival+'</span></h2>Arrival</div><div class="col-lg-1"><font size="1">Layovers</font><h2><span id="stops">'+stops+'</span></h2>Stop(s)</div><div class="col-lg-2"><font size="1">Indian Rupees</font><h2>&#8377; <span id="price">'+price+'</span></h2>Price</div></div></div>';
		return card_body;
	}
	</script>
	<div class="container">
		<div class="card bg-danger text-white">
			<form action="flights" method="post" onsubmit="return false;" id="myform" autocomplete="off">
			<div class="card-header">
				Modify Search
			</div>
			<div class="card-body">
				<div class="row" id="modifyRow">
					<div class="col-lg-3">
						<input type="text" class="form-control" placeholder="Origin" list="sourcelist" id="source" name="source" onKeyUp="getCode(this.id)" required="">
						<datalist id="sourcelist"></datalist>
					</div>
					<div class="col-lg-3">
						<input type="text" class="form-control" placeholder="Destination" list="destinationlist" id="destination" name="destination" onKeyUp="getCode(this.id)" required="">
						<datalist id="destinationlist"></datalist>
					</div>
					<div class="col-lg-2">
						<input id="onward" class="form-control" type="text" name="onward" value="Onward Date" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Onward Date';}" required="">
					</div>
					<div class="col-lg-2">
						<input id="comeback" class="form-control" type="text" name="comeback" value="Return Date" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Return Date';}" required="">
					</div>
					<div class="col-lg-2" style="text-align:right">
						<select class="form-control" id="myclass" name="myclass" required="">
							<option value="economy">Economy</option>  
							<option value="premiumeconomy">Premium Economy</option>   
							<option value="business">Business</option>   
							<option value="first">First class</option>  
						</select>
					</div>
				<br><Br>
					<div class="col-lg-4">
						
					</div>
					<div class="col-lg-1">
						<span>Adults</span>
					</div>
					<div class="col-lg-1">
						<input type="number" class="form-control" value="1" placeholder="Adult" id="adult" name="adult" min="1" step="1" required="">
					</div>
					<div class="col-lg-1">
						<span>Children</span>
					</div>
					<div class="col-lg-1">
						<input type="number" class="form-control" value="0" placeholder="Child" id="child" name="child" min="0" step="1" required="">
					</div>
					<div class="col-lg-1">
						<span>Infants</span>
					</div>
					<div class="col-lg-1">
						<input type="number" class="form-control" value="0" placeholder="Infant" id="infant" name="infant" min="0" step="1" required="">
					</div>
					<div class="col-lg-2">
						<button onclick="myFormButton()" class="btn btn-warning" style="width:100%">Search</button>
					</div>
					
				</div>
			</div>
			</form>
		</div>
	</div>
	<br><BR>
		<div id="resultCards">
		</div>
		

</div>