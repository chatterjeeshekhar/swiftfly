<?php
include 'ajax/main.php';
$source = $d["source"];
$destination = $d["destination"];
$forward = date_format(date_create($d["forward"]),"Y-m-d");
if($d["comeback"]!=""){
	$comeback = date_format(date_create($d["comeback"]),"Y-m-d");
}
$class = $d["myclass"];
$adult = $d["adult"];
$child = $d["child"];
$infant = $d["infant"];
if($source=="" || $destination=="" || $forward=="" || $class=="" || $adult=="" || $child=="" || $infant==""){
	die(header("location: /"));
}
$vars = "'$source', '$destination', '$forward', '$comeback', '$class', '$infant', '$child', '$adult'";
?>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script type="text/javascript">
	var startval = getRandomInt(7, 12);
	var midval = getRandomInt(42, 57);
	var finalval = getRandomInt(96, 100);
	function onload(){
		getSession(<?php echo $vars; ?>);
	}
	function getRandomInt(min, max) {
	    min = Math.ceil(min);
	    max = Math.floor(max);
	    return Math.floor(Math.random() * (max - min + 1)) + min;
	}
	function getSession(source, destination, forward, comeback, myclass, infant, child, adult){
		document.title = "Processing";
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
				var flights = j["msg"]["flights"];
				move(midval, finalval);
				setTimeout(function(){ document.getElementById("mainBody").innerHTML = data; document.title = "Results";}, 2500);
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
</script>
<body onload="onload()" id="mainBody" style="font-family:Arial">
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