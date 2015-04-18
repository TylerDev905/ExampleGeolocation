<?php
// I William Taylor-Holubeshen, 000305063 certify that this material is my original work. No other person's work has been used without due acknowledgement. I have not made my work available to anyone else.
	
	//opens the scv file
	$file = fopen('hamRecCenter.csv','r');
	$flag = true;
	
	//set some default variables
	$centerName = "";
	$centers = array();
	$cities = array();
	$json = array();
	
	//Set the first city in the array to All
	$cities[] = "All";
	
	//while the file pointer is not the end 
	while ( ($data = fgetcsv($file) ) !== FALSE ) {
		
		//set a flag
		if($flag){
			$flag = false;
		}
		else{
			//trim the data and place it in the cities array
			$flag = true;
			$cities[] = rtrim(ltrim($data[1]));
		}
	}
	
	//close the file
	fclose($file);
	
	//for each city
	foreach($cities as $city){
		
		//is the file already cached?
		if(!file_exists("cache/".trim($city).".json")){
			cache($city, $cities);
		}
	}
	
	//Display Success message
	echo 'All files were cached and are now up-to-date.';
	
	
//Create and cache the json file for a city
function cache($newCity, $cities){
	
	//open the csv file
	$file = fopen('hamRecCenter.csv','r');
	
	//set some default variables
	$flag = true;
	$centerName = "";
	$centers = array();
	$json = array();
	
	//while the file pointer is not the end
	while ( ($data = fgetcsv($file) ) !== FALSE ) {
		
		//if true get the center name and store for next iteration for a key in the associate array
		if($flag){
			$centerName = $data[0];
			$flag = false;
		}
		
		//Setup the center object
		else{
			$data = array(
				"Name"=>rtrim(ltrim($centerName)),
				"Address"=>rtrim(ltrim($data[0])),
				"City"=>rtrim(ltrim($data[1])),
				"Province"=>"Ontario",
				"Country"=>"Canada",
				"Phone"=>rtrim(ltrim($data[3]))
			);
			
			//if a newCity is set urldecode it
			if(isset($newCity)){
				$city = urldecode($newCity);
			}
			
			//if not set the city to a default map
			else{
				$city = "Hamilton";
			}
			
			//get the location of the city and add it to each object in the object array
			if($data['City'] == $city || $city == "All"){
				$data = getLocation($data);
				$centers[] = $data;
				sleep(1);
			}
			$flag = true;
		}
	}
	
	//create a holder associate array for the centers and the cities associate arrays
	$json = array("Centers"=> $centers, "Cities" => array_keys(array_flip($cities)));
	
	//close the csv file
	fclose($file);
	
	//save the .json file to the cache directory.
	file_put_contents("cache/".trim($city).".json", json_encode($json));
}


//retrieves the location for the current city and extends the city object with the properties.
function getLocation($data){
	$fullAddress = $data['Address'].", ".$data['City'].", ".$data['Province'].", ".$data['Country'];
	$xml = file_get_contents("http://maps.googleapis.com/maps/api/geocode/xml?address=".urlencode($fullAddress)."&sensor=false");
	
	//create a dom object from the xml file
	$xmlObj = new SimpleXMLElement($xml);
	
	//parse the lat and lng from the xml file
	$data['lng'] =  $xmlObj->result->geometry->location->lng->__toString();
	$data['lat'] =  $xmlObj->result->geometry->location->lat->__toString();
	
	//convert the lat and lng to float values
	$data['lng'] = (float)$data['lng'];
	$data['lat'] = (float)$data['lat'];
	
	//return the updated data object
	return $data;
}
?>