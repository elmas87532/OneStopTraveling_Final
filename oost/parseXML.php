<?php
include 'connect.php';
$locations = array();
$query = "SELECT address FROM location";
$result = mysqli_query($conn,$query);
$cnt = 0;
$cntLoc = 0;
$pcnt = 0;
while ($row = mysqli_fetch_row($result)) {
	if ($pcnt < 138) {
		$pcnt++;
		continue;
	}

	$locations[$cnt] = $row[0];
	//echo $locations[$cnt]."----".$cnt."**";
	
	if ($locations[$cnt] != "") {//使用googlemap API以地址查詢經緯度
		$XMLfile = "https://maps.googleapis.com/maps/api/geocode/xml?address=".$locations[$cnt]."&key=AIzaSyDTRPS4EWS5n9dKpzItqe3eVyqjHYein_o";
		$data = simplexml_load_file($XMLfile);
		//以下為經過很多層的xml包裝後目的是要進入「location」層中的「lng」以及「lat」
		foreach($data->children() as $child){
		  if ($child->getName() != "result") {
		  	continue;
		  }
		  foreach($child->children() as $gchild){
		  	//echo $gchild->getName() . ": " . $gchild . "<br />";
		  	if ($gchild->getName() != "geometry") {
			  continue;
			}
			foreach($gchild->children() as $ggchild){
				if ($ggchild->getName() != "location"){
					continue;
				}
				
				foreach($ggchild->children() as $loc)
					//最終到達需要的經緯度層
					if ($loc->getName() != "lng") {//若不為lng則為lat
						//將經緯度加入資料庫
						$query = "UPDATE location SET lat = '$loc' WHERE address = '$locations[$cnt]'";
						mysqli_query($conn, $query);
						$cntLoc++;
					}else{
						$query = "UPDATE location SET lng = '$loc' WHERE address = '$locations[$cnt]'";
						mysqli_query($conn, $query);
						$cntLoc = 0;
					}
					/*echo $loc->getName() . ": " . $loc . "<br />";
					echo $locations[$cnt];*/
					

					
			}
		  }
		}
	}
	

	$cnt++;

}


?>