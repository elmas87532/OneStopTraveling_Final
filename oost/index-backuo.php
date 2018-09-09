<?php
session_start();
include 'connect.php';
$input_place_from = "";
$input_place_to = "";
if (isset($_SESSION['FBID'])) {
	$id = $_SESSION['FBID'];
	echo "<img class='ib' src='https://graph.facebook.com/$id/picture?type=large'>";
}

if (isset($_POST['place-from']) && isset($_POST['place-to'])) {
	//echo $_POST['place-from'];
	$input_place_from = $_POST['place-from'];
	$input_place_to = $_POST['place-to'];
	if($input_place_from == "台北市"){
		$input_place_from = "1008";
	}elseif($input_place_from == "新北市"){
		$input_place_from = "1011";
	}elseif($input_place_from == "桃園市"){
		$input_place_from = "1015";
	}elseif($input_place_from == "台中市"){
		$input_place_from = "1113";
	}elseif($input_place_from == "台南市"){
		$input_place_from = "1228";
	}elseif($input_place_from == "高雄市"){
		$input_place_from = "1238";
	}

	if($input_place_to == "台北市"){
		$input_place_to = "1008";
	}elseif($input_place_to == "新北市"){
		$input_place_to = "1011";
	}elseif($input_place_to == "桃園市"){
		$input_place_to = "1015";
	}elseif($input_place_to == "台中市"){
		$input_place_to = "1113";
	}elseif($input_place_to == "台南市"){
		$input_place_to = "1228";
	}elseif($input_place_to == "高雄市"){
		$input_place_to = "1238";
	}

}
if (isset($_POST['num'])&&isset($_SESSION['pt'])) {
	$pt = $_SESSION['pt'];
	$item = $_POST['num'];
	if ($item == 1) {
		$query = "SELECT name,address,image,id FROM location";
		$result = mysqli_query($conn,$query);
		echo "<div class='item-operate-wrap'>
				<div class='item-operate'>
					<div class='search'>
						<input type='text' name='search-item' id='search-text'>
						<button id='search-btn' onclick='doSearch()'></button>
					</div>
					<div class='item-sort'>
						<div class='p-sort'><a href='#'>依人氣高到低</a>&nbsp;|&nbsp;<a href='#'>依距離遠到近</a>&nbsp;|&nbsp;<a href='#'>依價格低到高</a></div>&nbsp;
						<select id='district' onchange='doDistrictChange()'>
							<option>文山區</option><option>板橋區</option><option>大同區</option><option>大安區</option>
							<option>信義區</option><option>萬華區</option><option>北區</option><option>松山區</option>
						</select>
						
					</div>
					<div class='m_sort'><a href='#'>依人氣高到低</a>&nbsp;|&nbsp;<a href='#'>依距離遠到近</a>&nbsp;|&nbsp;<a href='#'>依價格低到高</a></div>
			</div></div>";
		while ($row = mysqli_fetch_row($result)) {
			$city = substr($row[1],0,9);
			$id = $row[3];
			if ($city == $pt) {
				$name = $row[0];
				$imgurl = $row[2];
				if (strlen($name)>=9 && substr($name, 0,9)=="已歇業") {
					continue;
				}
				if ($imgurl == "undefined") {
					continue;
				}
				echo "<div class='content-block'><img src='$imgurl'/><p><a href='detail.php?id=$id' target='_blank'>$name</a></p></div>";
			}	
		}
		exit();
	}elseif ($item == 2) {
		$query = "SELECT name,photo,address,id FROM hotel";
		$result = mysqli_query($conn,$query);
		echo "<div class='item-operate-wrap'>
				<div class='item-operate'>
					<div class='search'>
						<input type='text' name='search-item' id='search-text'>
						<button id='search-btn' onclick='doSearch()'></button>
					</div>
					<div class='item-sort'>
						<div class='p-sort'><a href='#'>依人氣高到低</a>&nbsp;|&nbsp;<a href='#'>依距離遠到近</a>&nbsp;|&nbsp;<a href='#'>依價格低到高</a></div>&nbsp;
						<select id='district' onchange='doDistrictChange()'>
							<option>文山區</option><option>板橋區</option><option>大同區</option><option>大安區</option>
							<option>信義區</option><option>萬華區</option><option>北區</option><option>松山區</option>
						</select>
						
					</div>
					<div class='m_sort'><a href='#'>依人氣高到低</a>&nbsp;|&nbsp;<a href='#'>依距離遠到近</a>&nbsp;|&nbsp;<a href='#'>依價格低到高</a></div>
			</div></div>";
		while ($row = mysqli_fetch_row($result)) {

			$city = substr($row[2],0,9);

			if ($city == $pt) {
				$name = $row[0];
				$photo = $row[1];
				$id = $row[3];
				echo "<div class='content-block'><img src='$photo' height='280'/><p><a href='h_detail.php?id=$id' target='_blank'>$name</a></p></div>";
			}
		}
		exit();
	}elseif ($item == 3) {
		$query = "SELECT Tran_Name,Tran_MRT,Tran_Bus,Tran_Taxi,Tran_Bike,Tran_Rental FROM transportation";
		$result = mysqli_query($conn,$query);
		while ($row = mysqli_fetch_row($result)) {
			$city = $row[0]."市";
			if ($city == $pt) {
				$name = $row[0];
				$mrt = $row[1];
				$bus = $row[2];
				$taxi = $row[3];
				$bike = $row[4];
				$rental = $row[5];
				echo "<div class='traffic-op-wrap'><div class='traffic-op'>
				<li><a href='javascript:gettraffic(1)'><img src='img/mrt.png'/><p>捷運</p></a></li>
				<li><a href='$bus' target='_blank'><img src='img/bus.png'/><p>公車</p></a></li>
				<li><a href='javascript:gettraffic(3)' target='_blank'><img src='img/taxi.png'/><p>計程車</p></a></li>
				<li><a href='$bike' target='_blank'><img src='img/bike.png'/><p>腳踏車</p></a></li>
				<li><a href='javascript:gettraffic(5)'><img src='img/rent.png'/><p>租車</p></a></li>
				</div><div id='ctraffic'></div></div>";
			}	
		}
		exit();
	}elseif ($item == 4) {
		$query = "SELECT id,name,image,address FROM event1";
		$result = mysqli_query($conn,$query);
		while ($row = mysqli_fetch_row($result)) {
			$city = substr($row[3],0,9);
			$name = $row[1];
			$imgurl = $row[2];
			$id = $row[0];
			if ($imgurl == "undefined") {
				continue;
			}
			if ($city == $pt) {
				echo "<div class='content-block'><img src='$imgurl'/><p><a href='e_detail.php?id=$id' target='_blank'>$name</a></p></div>";
			}	
		}
		exit();
	}elseif ($item == 5) {
		$query = "SELECT Wea_Name,Wea_URL FROM weather";
		$result = mysqli_query($conn,$query);
		while ($row = mysqli_fetch_row($result)) {
			$city = $row[0]."市";
			if ($city == $pt) {
				$name = $row[0];
				$website = $row[1];
				echo "<iframe src='$website'><p>您的瀏覽器不支援iframe!</p></iframe>";
			}	
		}
		exit();
	}elseif ($item == 0) {
		echo "<div class='train-info-table' name='train-info-table'>
				<table>
					<tr><td colspan='4' style='background:#404040;color:white;text-align:left;font-weight:bold;'>高雄 → 台北 11/19 (星期日)</td></tr>
					<tr class='train-info-col'><td>車次種</td><td>出發時間</td><td>到達時間</td><td>行車時間</td></tr>
					<tr class='train-info-content'><td>116</td><td>09:00</td><td>13:50</td><td>04時50分</td></tr>
					<tr class='train-info-content'><td>510</td><td>09:06</td><td>15:18</td><td>06時12分</td></tr>
					<tr class='train-info-content'><td>118</td><td>09:57</td><td>14:49</td><td>04時52分</td></tr>
				</table>
			</div>";
	}

	exit();
}
if (isset($_POST['trafficnum'])&&isset($_SESSION['pt'])) {
	$trafficnum = $_POST['trafficnum'];
	$pt = $_SESSION['pt'];
	$pt = substr($pt,0,6);
	if ($trafficnum == 1) {

		$query = "SELECT Tran_MRT FROM transportation WHERE Tran_Name='$pt'";
		//echo "<h1>$query</h1>";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_row($result);
		echo "<iframe src='$row[0]'><p>您的瀏覽器不支援iframe!</p></iframe>";
		exit();
	}elseif ($trafficnum == 3) {
		$query = "SELECT Tran_Taxi FROM transportation WHERE Tran_Name='$pt'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_row($result);
		echo "<iframe src='$row[0]'><p>您的瀏覽器不支援iframe!</p></iframe>";
		exit();
	}elseif ($trafficnum == 5) {
		$query = "SELECT Tran_Rental FROM transportation WHERE Tran_Name='$pt'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_row($result);
		echo "<iframe src='$row[0]'><p>您的瀏覽器不支援iframe!</p></iframe>";
		exit();
	}
	exit();
}

if (isset($_POST['stext'])&&isset($_SESSION['pt'])) {
	$stext = $_POST['stext'];
	$pt = $_SESSION['pt'];
	$query = "SELECT name,address,image FROM location WHERE name='$stext'";
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_row($result);
	$city = substr($row[1],0,9);
	if ($city == $pt) {
		$name = $row[0];
		$imgurl = $row[2];
		echo "<div class='item-operate-wrap'>
				<div class='item-operate'>
					<div class='search'>
						<input type='text' name='search-item' id='search-text'>
						<button id='search-btn' onclick='doSearch()'></button>
					</div>
					<div class='item-sort'>
						<a href='#'>依人氣高到低</a>&nbsp;|&nbsp;<a href='#'>依距離遠到近</a>&nbsp;|&nbsp;<a href='#'>依價格低到高</a>&nbsp;
						<select id='district' onchange='doDistrictChange()'>
							<option>文山區</option><option>板橋區</option><option>大同區</option><option>大安區</option>
							<option>信義區</option><option>萬華區</option><option>北區</option><option>松山區</option>
						</select>
					</div>
			</div></div>";
		echo "<div class='content-block'><img src='$imgurl'/><p><a href='#'>$name</a></p></div>";
	}
	exit();
}

if (isset($_POST['district'])&&isset($_SESSION['pt'])) {
	$district = $_POST['district'];
	$pt = $_SESSION['pt'];
	$query = "SELECT name,address,image FROM location";
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_row($result);
	$city = substr($row[1],0,9);
	$city_d = substr($row[1],9,9);
	if ($city == $pt && $city_d == $district) {
		$name = $row[0];
		$imgurl = $row[2];
		echo "<div class='content-block'><img src='$imgurl'/><p><a href='#'>$name</a></p></div>";
	}
	exit();
}
		?>
<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<title></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" type="text/css" href="css/clean.css">
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
var xhr;

function runAjax(name, data, outputArea){
	var sendStr = name+"="+data;
	try{
			xhr=new XMLHttpRequest();
			if(xhr.overrideMimeType)
				xhr.overrideMimeType('text/xml');
		}catch(e){
			try{
				xhr=new ActiveXObject("Msxml2.XMLHTTP");
			}catch(e){
				try{
					xhr=new ActiveXObject("Microsoft.XMLHTTP");
				}catch(e){
					alter("您的瀏覽器不支援ＸＭＬＨＴＴＰ");
					return false;
				}
			}
		}
	xhr.onreadystatechange = getData;
	xhr.open("post","<?php echo $_SERVER['PHP_SELF']; ?>",true);
	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	xhr.send(sendStr);
	function getData(){
		if(xhr.readyState==4)
			if(xhr.status==200)
				document.getElementById(outputArea).innerHTML=xhr.responseText;
	}

}

function getcontent(num){
	var name = "num";
	var data = num;
	var outputArea = "content";
	runAjax(name, data, outputArea);
}

function gettraffic(trafficnum){
	var name = "trafficnum";
	var data = trafficnum;
	var outputArea = "ctraffic";
	runAjax(name, data, outputArea);
}

function getsearch(stext){
	var name = "stext";
	var data = stext;
	var outputArea = "content";
	runAjax(name, data, outputArea);
}

function getDistrictChange(district){
	var name = "district";
	var data = district;
	var outputArea = "content";
}

function doSearch(){
	var stext = $('#search-text').val(); 
	getsearch(stext);
}

function doDistrictChange(){
	alert("FF");
	var district = $('#district').val();
	getDistrictChange(district);
}
</script>
<script>
$(function(){
	var $block = $('#fade_pic'), 
		$ad = $block.find('.ad'),
		showIndex = 0,			// 預設要先顯示那一張
		fadeOutSpeed = 2000,	// 淡出的速度
		fadeInSpeed = 3000,		// 淡入的速度
		defaultZ = 10,			// 預設的 z-index
		isHover = false,
		timer, speed = 2000;	// 計時器及輪播切換的速度
 
	// 先把其它圖片的變成透明
	$ad.css({
		opacity: 0,
		zIndex: defaultZ - 1
	}).eq(showIndex).css({
		opacity: 1,
		zIndex: defaultZ
	});
 
	// 組出右下的按鈕
	var str = '';
	for(var i=0;i<$ad.length;i++){
		str += '<a href="#">' + (i + 1) + '</a>';
	}
	var $controlA = $('#fade_pic').append($('<div class="control">' + str + '</div>')).find('.control a');
 
	// 當按鈕被點選時
	// 若要變成滑鼠滑入來切換時, 可以把 click 換成 mouseover
	$controlA.click(function(){
		// 取得目前點擊的號碼
		showIndex = $(this).text() * 1 - 1;
 
		// 顯示相對應的區域並把其它區域變成透明
		$ad.eq(showIndex).stop().fadeTo(fadeInSpeed, 1, function(){
			if(!isHover){
				// 啟動計時器
				timer = setTimeout(autoClick, speed + fadeInSpeed);
			}
		}).css('zIndex', defaultZ).siblings('a').stop().fadeTo(fadeOutSpeed, 0).css('zIndex', defaultZ - 1);
		// 讓 a 加上 .on
		$(this).addClass('on').siblings().removeClass('on');
 
		return false;
	}).focus(function(){
		$(this).blur();
	}).eq(showIndex).addClass('on');
 
	$ad.hover(function(){
		isHover = true;
		// 停止計時器
		clearTimeout(timer);
	}, function(){
		isHover = false;
		// 啟動計時器
		timer = setTimeout(autoClick, speed);
	})
 
	// 自動點擊下一個
	function autoClick(){
		if(isHover) return;
		showIndex = (showIndex + 1) % $controlA.length;
		$controlA.eq(showIndex).click();
	}
 
	// 啟動計時器
	timer = setTimeout(autoClick, speed);
});
	</script>
	<script>
function loadXMLFile(file){
   var xmlDoc;
// FOR IE
   if (window.ActiveXObject){
      xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
      xmlDoc.async = false;
      xmlDoc.load(file);
      return xmlDoc;
   }
// !IE
   else if (document.implementation && document.implementation.createDocument){
      var xmlInfo = new XMLHttpRequest();  
      xmlInfo.open("GET", file, false);
      xmlInfo.send(null); 
      xmlDoc = xmlInfo.responseXML;
      return xmlDoc;
   }
   else{
      alert("您的瀏覽器不支援Javascript! ");
   }
}

function getTrainInfo() { 
      var input_time = 11;
      var input_From = "1228"; //tainan
      var input_To = "1238"; //Kaohsiung

      xmlFile="js/xml/20171001.xml";
      xmlData=loadXMLFile(xmlFile);

      var TrainInfo = xmlData.getElementsByTagName("TrainInfo");
      for (var i = 0; i < TrainInfo.length; i++) {
         var TimeInfo = TrainInfo[i].childNodes;
         var time = TimeInfo[0].getAttribute("DEPTime");
         time = time.split(":")[0];     
         if (time > input_time) {continue;}
   
         var fromRef = false;
         var toRef = false;
         var result_fromTime = "";
         var result_toTime = "";

         for (var j = 0; j < TimeInfo.length; j++) {

         	var timeRef = TimeInfo[j].getAttribute("DEPTime").split(":")[0];	
         	if (TimeInfo[j].getAttribute("Station") == input_From && timeRef == input_time && !toRef) {
         		fromRef = true;
         		result_fromTime = TimeInfo[j].getAttribute("DEPTime");
         	}
         	if (TimeInfo[j].getAttribute("Station") == input_To && fromRef) {
         		toRef = true;
         		result_toTime = TimeInfo[j].getAttribute("ARRTime");
         	}

         }

         if (fromRef && toRef) {
         	console.log(TrainInfo[i].getAttribute("Train")+"--DEP--"+result_fromTime+"--ARR--"+result_toTime);
         }
      }
     

} 
	</script>
<style type="text/css">
body{
	font-family: "微軟正黑體";
	text-align: center;
}
.main{
	float: left;
	width: 100%;
	min-width: 1280px;
	height: 20px;
	box-shadow: 
	        5px 0 10px -5px transparent inset, /*右边阴影*/
			0 -5px 10px -5px transparent inset, /*底部阴影*/
			0 8px 10px -5px #555 inset, /*顶部阴影*/
			-5px 0 10px -5px transparent inset; /*左边阴影*/
}
.content-block{
	float: left;
	width: 280px;
	height: 340px;
	text-align: center;
	font-size: 16px;
	margin: 40px 20px 40px 20px;
}
.content-block img{
	width: 280px;
	height: 280px; 
	opacity:0.8;
	transition:opacity linear .2s;
}
.content-block img:hover{opacity:1;}
.content-block p{
	width: 280px;
	height: 40px;
	background-color: #404040;
	text-align: center;
	padding-top: 10px;
	padding-bottom: 10px;
}
.content-block p a{
	color: white;
	font-weight: bold;
	text-decoration: none;
}
.logo{position: absolute; top:70;}
.logo img{width: 400px;}
.control{display: none;}
.items li,.items li p,.contnet,.search,.traffic-op-wrap,.traffic-op li,#fade_pic{float: left;}
.user-info{float: right;}
.top{
	background-color:rgba(0,0,0,0.7);
	width: 100%;
	height: 50px;
	position: fixed;
	z-index: 99;
}
.top img{
	height: 40px;
	float: left;
	margin-left: 5px;
	margin-top: 5px;
}
.container{
	margin: 0 auto;
}
.top-options{
	font-size: 20px;
	float: right;
	padding: 15px;margin-right: 10px;
}
.top-options select{
	font-family: "微軟正黑體";
	font-size: 20px;
	border: 0px;
	font-weight: bold;
	background-color: #0094FF;
	color: white;
}
.top-options img{
	margin-top: -10px;
	margin-left: 0px;
	margin-right: 10px;
	border-radius: 50%;
}

.table-bg{
	background-color: #1F3442;
	background-color:rgba(0,0,0,0.5);
	width: 50%;
	min-width: 750px;
	height: 180px;
	padding: 25px;
	margin-top: 100px;
	color: white;
	border-radius: 5px;
	position: absolute;
	z-index: 29;
	left: 25%;
	top: 20%;
}
..table-bg form{min-width: 750px;}
.table-bg input[type="text"],.table-bg input[type="date"],.table-bg input[type="time"],.table-bg input[type="submit"]{
	border: 4px #404040 solid;
	vertical-align:middle;
	height: 38px;
	font-size: 20px;
	font-family: "微軟正黑體";
	width: 160px;
	margin-right: 10px;
}
.input-item-m-s{width: 100%; text-align: center;}
.table-bg input[type="radio"]{width: 25px;height: 25px;vertical-align: middle;}
.table-bg input[type="submit"]{
	background-color: #0094FF;
	color: white;
	border: 0;
	width: 120px;
	font-weight: bold;
	height: 40px;
}
.table-bg input[type="submit"]:hover{
	background-color: white;
	color: #0094FF;
	transition: background-color 0.4s;
}
.table-bg input[type="button"] {
	width: 149px;
	height: 79px;
	background-color: white;
	background-image: url('../img/switch.png');
	border: 0px;
	vertical-align:middle;
}
.table-bg select{
	height: 36px;
	border: 4px #404040 solid;
	font-family: "微軟正黑體";
	font-size: 20px;
	width: 250px ;
	margin-right: 10px;
}
.input-item-m,.input-item-m-s{margin-top: 15px;margin-bottom: 15px;display:block; float: left;}
.item-menu,.item-operate{
	width: 905px;
	padding-top: 30px;
	padding-bottom: 30px;
	margin: 0 auto;
}
.items{
	width: 100%;
	margin: 0 auto;
	margin-bottom: 30px;
}
.items li{
	background-color: white;
	width: 180px;
	padding: 10px 0px 10px 0px;
	font-weight: bold;
	font-size: 28px;
	color: #3399FF;
}
.items li:hover{color: white;background-color: #3399FF;}
.items p{
	width: 100%;
	text-align: center;
}
.item-menu li{
	border-right: 1px solid #0094FF;
}
iframe{ width: 900px; height: 700px; margin: 0 auto; margin-top: 50px;}
#content{
	text-align: center;
}
#search-text{ 
	width: 200px;height: 39px;border: 2px solid #7F7F7F; 
	vertical-align: middle;
	border-right: 0px;
}
#search-btn{ 
	background-image: url(img/search.png); 
	background-color: white;
	width: 41px; height: 39px; 
	vertical-align: middle; 
	border: 2px solid #7F7F7F; 
	margin-left: -10px;
	border-left: 0px;
}
#district{
	width: 200px;height: 39px;border: 2px solid #7F7F7F;float: right; 
}
.item-operate-wrap{width: 1200px; margin: 0 auto;}
.item-operate{width: 1200px;float: left;}
.item-sort{float: right;color: #7F7F7F;width: 600px;}
.p-sort{float: left;}
.item-sort a{
	color: #7F7F7F;
	font-weight: bold;
	text-decoration: none;
}
.item-sort a:hover{color: #B7B7B7;}
.item-sort select{
	width: 100px;
	height: 30px;
	font-size: 20px;
	font-family: "微軟正黑體";
}
.traffic-op-wrap{width: 100%;margin-bottom: 200px;}
.traffic-op { width: 1100px;margin:0 auto;}
.traffic-op li img{ width: 150px; }
.traffic-op li{
	list-style-type: none;
	width: 200px;
	margin: 10px;
	text-align: center;
}
.traffic-op a{color: #7F7F7F; text-decoration: none;font-weight: bold;}
.traffic-op a:hover{color: #7F7F7F;}
.content-wrap{width: 1280px; margin: 0 auto; min-width: 1280px;}
.m_sort{display: none;}

/**************************/

#fade_pic img{
	width: 100%;
	height: 630px;
	min-width: 1280px;
}
#fade_pic a.ad {
	position: absolute;	/* 讓圖片疊在一起 */
}
#fade_pic .control {
	position: absolute;
	right: 10px;
	bottom: 10px;
}
#fade_pic .control a {
	display: inline-block;
	padding: 3px;
	margin: 0 3px;
	width: 16px;
	color: #fff;
	background: #000;
	text-align: center;
	font-size: 16px;
	text-decoration: none;
}
#fade_pic .control a.on {
	font-weight: bold;
	color: #f00;
}
.train-info-table{width: 100%;float: left;}
.train-info-table table{
	width: 950px;
	margin: 0 auto;
	margin-top: 30px;
	margin-bottom: 30px;
}
.train-info-table td{
	height: 40px;
	padding: 11px;
	font-size: 18px;
	text-align: center;
}
.train-info-col td{background-color: #B7B7B7;border: 1px solid white;}

@media screen and (max-width: 835px) {/**----835----**/
.top{height: 50px;}
.top img{height: 45px;}
.fade-pic-wrap{
	width: 100%;
	height: 85%;
	float: left;
}
#fade_pic {height: 80%;}
#fade_pic img{min-width: 300px;clip:rect(0px,50px,200px,0px);height: 80%;}
.main{min-width: 300px;margin-top: 0px;}
.table-bg{
	width: 100%;
	min-width: 300px;
	height: 60%;
	margin-top: 20%;
	font-size: 40px;
	text-align: left;
	left:0;
	top:0;
}
.table-bg select,.table-bg input[type="date"],#input-time{width: 100%;height: 50px;}
.table-bg form{min-width: 280px;}
.input-item-m,.input-item-m-s{
	margin-top: 8px;
	margin-bottom: 8px;
	width: 100%;
}
.table-bg input[type="submit"]{
	width: 100px;
	height: 50px;
	font-size: 24px;
	margin-top: 50px;
}
.table-bg input[type="radio"]{width: 40px; height: 40px;}
.m-span{text-align: left;font-weight: bold;}
.items{width: 100%;float: left;height: 225px;}
.item-menu{width: 100%;}
.item-operate{width: 100%;float: left;}
#district{float: right;}
.item-sort{width: 400px;float: left;}
.items li{ width: 50%; font-size: 40px;border: 0px;}
.train-info-table table{width: 90%;}
.train-info-table td{font-size: 34px;}
.p-sort{display: none;}
.m_sort{display: block;width: 100%;height: 50px;float: left;text-align: center;margin-top: 20px;font-size: 28px;}
.content-wrap{width: 100%;min-width: 300px;}
#content{width: 650px; margin: 0 auto;}
.m_sort a{
	color: #7F7F7F;
	font-weight: bold;
	text-decoration: none;
}
.m_sort a:hover{color: #B7B7B7;}
.content-block p a{font-size: 20px;}
.traffic-op{width: 100%;}
.traffic-op li{width: 280px;}
.traffic-op li img{width: 250px;height: 250px;}
.traffic-op li a{font-size: 34px;}
iframe{width: 90%;}
}/**----835END----**/


.login-info{color: white;}
.login-info a{
	color: #0094FF;
	font-weight: bold;
}
.login-info a:hover{
	color: white;
	transition: 0.4s;
}
.operations{width: 100%;height: 90px;margin-top: 630px;background-color: black;float: left;}
.operations-menu {width: 1200px;margin: 0 auto;}
.operations-menu li{float: left;width: 200px;color: white;padding:35px 0px 35px 0px;}
.operations-menu li:hover{color: #0094FF;transition: 0.4s;}
</style>

</head>
<body>
 
	<div class="wrap">
		<div class="top">
			<div class="container">
				<a href="index.php"><img src="img/logo.png"></a>
				<div class="top-options">
					<span class="login-info">Hi! 芊慧 &nbsp;<a href="fav.php">我的收藏</a>&nbsp;|&nbsp;<a href="#">登出</a></span>
					<a href="#"><img src="img/sp.jpg"></a>
					
				</div>
			</div>
		</div>
		<div class="fade-pic-wrap">
		<div id="fade_pic"> 
			<a href="#" class="ad"><img src="img/banner1.png" /></a> 
			<a href="#" class="ad"><img src="img/banner2.jpg" /></a> 
			<a href="#" class="ad"><img src="img/banner3.png" /></a> 
		</div>
		</div>
		<div class="table-bg">
					<form action="index.php" method="post">
						<span class="input-item-m"><span class="m-span">起點：</span>
						<select id="place-from" name="place-from">
							<option value="台北市">台北市</option>
							<option value="新北市">新北市</option>
							<option value="高雄市">高雄市</option>
							<option value="台中市">台中市</option>
							<option value="台南市">台南市</option>
							<option value="桃園市">桃園市</option></select></span>
						<span class="input-item-m"><span class="m-span">終點：</span>
						<select id="place-to" name="place-to">
							<option value="台北市">台北市</option>
							<option value="新北市">新北市</option>
							<option value="高雄市">高雄市</option>
							<option value="台中市">台中市</option>
							<option value="台南市">台南市</option>
							<option value="桃園市">桃園市</option></select></span>
						
						<span class="input-item-m"><span class="m-span">日期：</span>&nbsp;<input type="date" name="date"></span>
						<span class="input-item-m"><span class="m-span">時間：</span>&nbsp;
							<select id="input-time">
								<option value="">06:00</option>
								<option value="">06:30</option>
								<option value="">07:00</option>
								<option value="">07:30</option>
								<option value="">08:00</option>
								<option value="">08:30</option>
								<option value="">09:00</option>
								<option value="">09:30</option>
								<option value="">10:00</option>
								<option value="">10:30</option>
								<option value="">11:00</option>
								<option value="">11:30</option>
								<option value="">12:00</option>
								<option value="">12:30</option>
								<option value="">13:00</option>
								<option value="">13:30</option>
								<option value="">14:00</option>
								<option value="">14:30</option>
								<option value="">15:00</option>
								<option value="">15:30</option>
								<option value="">16:00</option>
								<option value="">16:30</option>
								<option value="">17:00</option>
								<option value="">17:30</option>
								<option value="">18:00</option>
								<option value="">18:30</option>
								<option value="">19:00</option>
								<option value="">19:30</option>
								<option value="">20:00</option>
								<option value="">20:30</option>
								<option value="">21:00</option>
								<option value="">21:30</option>
								<option value="">22:00</option>
								<option value="">22:30</option>
								<option value="">23:00</option>
								<option value="">23:30</option>
								<option value="">00:00</option>
							</select>
						</span>
						<span class="input-item-m"><span><input type="radio" name="select-type"><span class="m-span">出發</span></span><span><input type="radio" name="select-type"><span class="m-span">抵達</span></span></span>
						<span class="input-item-m-s" >&nbsp;&nbsp;<input type="submit" value="查詢"></span>
					</form>
				</div>
		<div class="operations">
			<div class='operations-menu'>
			<ul>
				<a href='javascript:getcontent(0)' id='dining'><li>火車時刻表</li></a>
				<a href='javascript:getcontent(1)' id='dining'><li>吃喝玩樂</li></a>
				<a href='javascript:getcontent(2)' id='dining'><li>住宿</li></a>
				<a href='javascript:getcontent(3)' id='dining'><li>交通</li></a>
				<a href='javascript:getcontent(4)' id='dining'><li>活動</li></a>
				<a href='javascript:getcontent(5)' id='dining'><li style='border-right:0px;'>天氣</li></a>
			</ul>
			</div>
		</div>
		<div class="main">
			
		<?php
if (isset($_POST['place-to'])) {
	$place_to = $_POST['place-to'];

	$_SESSION['pt'] = $_POST['place-to'];
	//echo $_SESSION['pt'];
	/*echo "<div class='train-info-table' name='train-info-table'>
				<table>
					<tr><td colspan='4' style='background:#404040;color:white;text-align:left;font-weight:bold;'>高雄 → 台北 11/19 (星期日)</td></tr>
					<tr class='train-info-col'><td>車次種</td><td>出發時間</td><td>到達時間</td><td>行車時間</td></tr>
					<tr class='train-info-content'><td>116</td><td>09:00</td><td>13:50</td><td>04時50分</td></tr>
					<tr class='train-info-content'><td>510</td><td>09:06</td><td>15:18</td><td>06時12分</td></tr>
					<tr class='train-info-content'><td>118</td><td>09:57</td><td>14:49</td><td>04時52分</td></tr>
				</table>
			</div>";
	echo "<div class='items'>				
			<div class='item-menu'>
			<ul>
				<a href='javascript:getcontent(1)' id='dining'><li>吃喝玩樂</li></a>
				<a href='javascript:getcontent(2)' id='dining'><li>住宿</li></a>
				<a href='javascript:getcontent(3)' id='dining'><li>交通</li></a>
				<a href='javascript:getcontent(4)' id='dining'><li>活動</li></a>
				<a href='javascript:getcontent(5)' id='dining'><li style='border-right:0px;'>天氣</li></a>
			</ul>
			</div>
		*/	
		echo "</div><div class='content-wrap'><div id='content'></div></div>";
}?>

		</div>
	</div>
	
</body>

</html>