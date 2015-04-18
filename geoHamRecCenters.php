<?php
//If there is no city specified set hamilton to default.
if(!isset($_GET['City'])){
	$_GET['City'] = "Hamilton";
}
?>
<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> 

<html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="css/normalize.min.css">
        <link rel="stylesheet" href="css/main.css">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/bootstrap-theme.min.css">
		<link rel="stylesheet" href="highlightJs/styles/default.css">
		<style>
		#canvas {
			width:400px; 
			height:400px; 
			margin:0 auto;
		}
		.center {
			text-align:center;
		}
		tr{
			text-align:center;
		}
		.btn { 
			margin: 2px;	
		}
		h2 {
			margin:0;
		}
		#jsonDataWell {
			display:none;
		}
		#authorWell{
			display:block;
		}
		.header-container {
			background-color:#DDD;
		}
		.footer-container {
			background-color:#DDD;
		}
		.title {
			color:white;
		}
		blockquote{
			font-size:14px;
			border-left:solid 5px #31708f;
			
		}
		#json {
			display:none;
		}
		.hljs {
			overflow:hidden;
		}
		</style>
        <script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
		<script src="js/vendor/jquery-1.11.2.min.js"></script>
		<script src="js/vendor/bootstrap.min.js"></script>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA6qKD1rH-vLaa-ijUQNY77jjHiMI7C1Z4"></script>
		<script src="js/gmaps.js"></script>
		<script src="highlightJs/highlight.js"></script>
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="header-container">
            <header class="wrapper clearfix">
                <h1 class="title"><strong>Comp10133 - </strong> Lab 3 Geolocation Test and Google Maps</h1>
            </header>
        </div>

        <div class="main-container">
            <div class="main wrapper clearfix">
				
				
				<div id="StatusMsg"></div>
				<div id="StatusMsg2"></div>
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<td><h2>Recreational Centers</h2></th>
						<tr>
					</thead>
					<tbody>
					<tr>
						<td class="center">
						<div class="btn-group">
							
							
							<a class="btn dropdown-toggle btn-info" data-toggle="dropdown" href="#">City
								<span class="caret"></span>
							</a>
							
							<ul class="dropdown-menu" id="citylist"></ul>
						</div>
							<button id="jsonData" class="btn btn-danger">Json Data</button>
							<a href="cache.php" class="btn btn-warning">Cache</a>
						</div>
						</td>
					</tr>
					<tr>
						<td>
							<div id="canvas"></div>
						</td>
					</tr>
					<tr>
						<td id="jsonDataWell">
							<h4>Json Object</h4>
							<pre><code><?php echo file_get_contents("cache/".trim($_GET['City']).".json"); ?></code></pre>
							<pre id="json"><?php echo file_get_contents("cache/".trim($_GET['City']).".json"); ?></pre>
						</td>
					</tr>
					</tbody>
				</table>
				<div id="authorWell" class="alert alert-info">
					<h3>Author: Tyler H</h3>
					<h4>Mohawk College</h4>
					<h5><strong>Useage:</strong></h5>
					<blockquote>
						Clicking the <strong>City</strong> will display all the recreational centers located in the selected city</br></br>
						Clicking <strong>Json Data</strong> Will show/hide the syntax highlighted json object. </br></br>
						Clicking <strong>Cache</strong> Will create a json file for each location found in the .csv
					</blockquote>
				</div>
				
				
            </div> <!-- #main -->
        </div> <!-- #main-container -->

        <div class="footer-container">
            <footer class="wrapper">
                <h3>Comp10133: Lab 3 Geolocation Test and Google Maps</h3>
            </footer>
        </div>
		
        <script src="js/main.js"></script>
		<script>
		
		
	var map;
	var clientLat;
	var clientLng;
	
	//execute the syntax highlighter
	hljs.initHighlightingOnLoad();	
		
		$("#jsonData").click(function(e){
			$("#jsonDataWell").fadeToggle();
		});
		
		
		$(window).load(function(){
			
			//Parses the json object
			data = jQuery.parseJSON($("#json").html());
			centers = data['Centers'];
			cities = data['Cities'];
			console.log(cities);
			
			//setup map
			var coords =  new google.maps.LatLng(43.3435114,-79.982431);
			var mapOptions = {
				center: coords,
				zoom: 9
			};
			map = new google.maps.Map(document.getElementById('canvas'), mapOptions);
			
			//for each center display marker
			$.each(centers, function(index, value){
				var coord = new google.maps.LatLng(centers[index].lat,centers[index].lng);
				var marker = new google.maps.Marker({
					position: coord,
					title:centers[index]['Name'],
					map: map
				});
				
				//Builds the infowindow content
				var content = '%Name%</br>%Address%</br> %City%, %Province%</br>%Phone%</br>Lat: %Lat%</br>Lng:%Lng%</br><a href="https://www.google.com/maps/@'+centers[index]['lat']+','+centers[index]['lng']+',13z?hl=en-US">Google Maps</a>';
				content = content.replace("%Name%", centers[index]['Name']);
				content = content.replace("%Address%", centers[index]['Address']);
				content = content.replace("%City%", centers[index]['City']);
				content = content.replace("%Province%", centers[index]['Province']);
				content = content.replace("%Phone%", centers[index]['Phone']);
				content = content.replace("%Lat%", centers[index]['lat']);
				content = content.replace("%Lng%", centers[index]['lng']);
				
				//create the infowindow
				var infowindow = new google.maps.InfoWindow({
					content:content 
				});
				
				//add a listener to the infowindow for an on click event
				google.maps.event.addListener(marker, 'click', function() {
					infowindow.open(map,marker);
				});
				
				//set the center of the map
				map.setCenter(coord);
			});
			
			//for each city display a button
			$.each(cities, function(index, value){
				$('#citylist').append('<li><a href="geoHamRecCenters.php?City='+cities[index]+'" class="btn">'+cities[index]+'</a></li>');
			});
			
		});
		
		</script>
    </body>
</html>
