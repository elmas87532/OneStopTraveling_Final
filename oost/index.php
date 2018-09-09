<?php
session_start();
include 'connect.php'; //MySQL連接
require_once('settings.php'); //google登入設定
$input_place_from_txt = ""; //使用者輸入出發地點
$input_place_to_txt = ""; //使用者輸入抵達地點
$input_place_from = ""; //使用者輸入出發地點台鐵編號
$input_place_to = ""; //使用者輸入抵達地點台鐵編號
$input_time = ""; //使用者輸時間
$input_date = ""; //使用者輸入地點
$select_type = "t"; //t:出發 f:抵達
$HSR_fromto = "";
if (isset($_SESSION['FBID'])) {
	$id = $_SESSION['FBID'];
	echo "<img class='ib' src='https://graph.facebook.com/$id/picture?type=large'>";
}

$ifPtIsSet = 0;
if (isset($_POST['place-from']) && isset($_POST['place-to']) && isset($_POST['select-type'])) {
	//設定使用者輸入的值，包含出發地點、到達地點、日期、時間、出發或抵達方式

	$ifPtIsSet = 1;
	$select_type = $_POST['select-type'];
	$_SESSION['select_type'] = $select_type;
	//echo $_POST['place-from'];
	$input_place_from_txt = $_POST['place-from'];
	$_SESSION['input_place_from_txt'] = $input_place_from_txt;
	$input_place_to_txt = $_POST['place-to'];
	$_SESSION['input_place_to_txt'] = $input_place_to_txt;
	$input_time = $_POST['input-time'];
	$_SESSION['input-time'] = $input_time;
	$input_date = $_POST['date'];
	$_SESSION['input-date'] = $input_date;
	if($input_place_from_txt == "台北市"){
		$input_place_from = "1008";
		$HSR_fromto = "N";
	}elseif($input_place_from_txt == "台中市"){
		$input_place_from = "1319";
		$HSR_fromto = "C";
	}elseif($input_place_from_txt == "高雄市"){
		$input_place_from = "1238";
		$HSR_fromto = "S";
	}

	if($input_place_to_txt == "台北市"){
		$input_place_to = "1008";
		$HSR_fromto = $HSR_fromto."N";
	}elseif($input_place_to_txt == "台中市"){
		$input_place_to = "1319";
		$HSR_fromto = $HSR_fromto."C";
	}elseif($input_place_to_txt == "高雄市"){
		$input_place_to = "1238";
		$HSR_fromto = $HSR_fromto."S";
	}
	$_SESSION['HSR_fromto'] = $HSR_fromto;
}

//登出功能(此處之FB_userID實為Google的ID)
if (isset($_GET['logout'])) {
	unset($_SESSION['FB_userID']); 
	unset($_SESSION['name']);
	unset($_SESSION['userImgLink']);
}

if (isset($_POST['hsr']) && isset($_SESSION['HSR_fromto']) && isset($_SESSION['input-time']) && isset($_SESSION['select_type'])) {
	//設定高鐵時刻查詢相關資訊(資料已存入資料庫)
	$query = "SELECT * FROM hsr WHERE fromto = '".$_SESSION['HSR_fromto']."' AND datetype = 2";
	$result = mysqli_query($conn, $query);
	$innerHTML = "<div class='train-info-table' name='train-info-table'><table><tr><td colspan='3' style='background:#404040;color:white;text-align:left;font-weight:bold;'>高鐵&nbsp;&nbsp;".$_SESSION['input_place_from_txt']." → ".$_SESSION['input_place_to_txt']." ".$_SESSION['input-date']."</td><td style='background:#404040;color:white;'><a href='https://irs.thsrc.com.tw/IMINT/' target='_blank' class='ticket-button'>點我訂票</a></td></tr><tr class='train-info-col'><td>車次</td><td>出發時間</td><td>到達時間</td><td>行車時間</td></tr>";
	
	while ($row = mysqli_fetch_row($result)) {
		if ($_SESSION['select_type'] == "t") { //若使用者輸日之「出發/抵達」方式為「出發」，則設定選擇之資料庫欄位為出發
			$depTime = substr($row[4],0,2);
		}else{
			$depTime = substr($row[5],0,2); //若使用者輸日之「出發/抵達」方式為「抵達」，則設定選擇之資料庫欄位為抵達
		}
		if ($depTime == $_SESSION['input-time']) {
			$innerHTML = $innerHTML."<tr class='train-info-content'><td>$row[3]</td><td>$row[4]</td><td>$row[5]</td><td>$row[6]</td></tr>";
		}
		
	}
	$innerHTML = $innerHTML."</table></div>";
	echo $innerHTML;
	exit();

}

if (isset($_POST['bus']) && isset($_SESSION['HSR_fromto']) && isset($_SESSION['input-time'])) {
	//設定客運時刻查詢相關資訊(資料已存入資料庫)
	$query = "SELECT * FROM bus WHERE fromto = '".$_SESSION['HSR_fromto']."'";
	$result = mysqli_query($conn, $query);
	$innerHTML = "<div class='train-info-table' name='train-info-table'><table><tr><td style='background:#404040;color:white;text-align:left;font-weight:bold;'>客運&nbsp;&nbsp;".$_SESSION['input_place_from_txt']." → ".$_SESSION['input_place_to_txt']." ".$_SESSION['input-date']."</td><td style='background:#404040;color:white;'><a href='http://ordertickets.ubus.com.tw/' target='_blank' class='ticket-button'>點我訂票</a></td></tr><tr class='train-info-col'><td colspan='2'>統聯客運</td></tr>";
	
	if ($_SESSION['select_type'] == "t"){ //因為客運只能查看出發時間，所以客運時刻表中無抵達功能
		while ($row = mysqli_fetch_row($result)){
			$depTime = substr($row[2],0,2);
			if ($depTime == $_SESSION['input-time']) {
				$innerHTML = $innerHTML."<tr class='train-info-content'><td colspan='2'>$row[2]</td></tr>";
			}
		}
		$innerHTML = $innerHTML."</table></div>";
	}else{
		$innerHTML = "客運不適用「抵達」功能哦！";
	}

	echo $innerHTML;
	exit();

}

//切換使用者點選的功能(吃喝玩樂、住宿、交通、活動、天氣)
if (isset($_POST['num'])&&isset($_SESSION['pt'])) {
	$pt = $_SESSION['pt'];
	$item = $_POST['num'];
	//item: 1>>吃喝玩樂 2>>住宿 3>>交通 4>>活動 5>>天氣

	if ($item == 1) {//吃喝玩樂
		$query = "SELECT name,address,image,id FROM location";
		$result = mysqli_query($conn,$query);

		while ($row = mysqli_fetch_row($result)) {
			$city = substr($row[1],0,9); //判斷城市：擷取地址的頭三個字，例如：「高雄市」楠梓區高雄到學路700號
			$id = $row[3];
			if ($city == $pt) { //在資料庫中讀取資料，若與使用者目前選擇之目的地城市相符則顯示資料
				$name = $row[0];
				$imgurl = $row[2];
				if (strlen($name)>=9 && substr($name, 0,9)=="已歇業") {
					continue;
				}
				if ($imgurl == "undefined") {
					continue;
				}
				/*echo strlen($name);
				echo "-*";*/
				if (strlen($name) > 32) { //若景點名稱過長，可能超出版面寬度，因此擷取頭10個字為限，若過程則以「...」取代後面字
					$name = substr($name, 0,30)."...";
				}
				echo "<div class='content-block'><img src='$imgurl'/><p><a href='javascript:callDetail($id)' target='_blank'>$name</a></p></div>";
			}	
		}
		exit();
	}elseif ($item == 2) {//住宿
		switch ($_SESSION['pt']) {
			//顯示輸入地點的tripadvisor iframe
			case '高雄市':
				echo "<iframe src='https://www.tripadvisor.com.tw/Hotels-g297908-Kaohsiung-Hotels.html'></iframe>";
				break;
			case '台中市':
				echo "<iframe src='https://www.tripadvisor.com.tw/Hotels-g297910-Taichung-Hotels.html'></iframe>";
				break;
			case '台北市':
				echo "<iframe src='https://www.tripadvisor.com.tw/Hotels-g293913-Taipei-Hotels.html'></iframe>";
				break;

			default:
				echo "<iframe src='https://www.tripadvisor.com.tw/Hotels-g293913-Taipei-Hotels.html'></iframe>";
				break;
		}
		
		exit();
	}elseif ($item == 3) {//當地交通
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

				//交通的選項
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
	}elseif ($item == 4) {//活動
		$query = "SELECT id,name,image,address FROM event1";
		$result = mysqli_query($conn,$query);
		while ($row = mysqli_fetch_row($result)) {
			$city = substr($row[3],0,9); //判斷城市：擷取地址的頭三個字，例如：「高雄市」楠梓區高雄到學路700號
			$name = $row[1];
			$imgurl = $row[2];
			$id = $row[0];
			if ($imgurl == "undefined") {
				continue;
			}
			if (strlen($name) > 32) {
					$name = substr($name, 0,30)."..."; //若景點名稱過長，可能超出版面寬度，因此擷取頭10個字為限，若過程則以「...」取代後面字
				}
			if ($city == $pt) {//若與使用者目前選擇之目的地城市相符則顯示資料
				echo "<div class='content-block'><img src='$imgurl'/><p><a href='javascript:callDetaile($id)' target='_blank'>$name</a></p></div>";
			}	
		}
		exit();
	}elseif ($item == 5) {//天氣
		//accuweather iframe
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
	}

	exit();
}

//在功能選項「當地交通」切換交通選項(捷運、公車、計程車、腳踏出、租車)後進入以下
if (isset($_POST['trafficnum'])&&isset($_SESSION['pt'])) {
	$trafficnum = $_POST['trafficnum'];
	$pt = $_SESSION['pt'];
	$pt = substr($pt,0,6);
	if ($trafficnum == 1) {//捷運
		if ($pt == "台南") {
			echo "<p style='width:100%;float:left;margin-top:50px;'>台南沒有捷運噢!</p>";
		}elseif ($pt == "台中") {
			echo("<p style='width:100%;float:left;margin-top:50px;'>台中沒有捷運噢!</p>");
		}else{
			$query = "SELECT Tran_MRT FROM transportation WHERE Tran_Name='$pt'";
			//echo "<h1>$query</h1>";
			//echo $pt;
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_row($result);
			echo "<iframe src='$row[0]'><p>您的瀏覽器不支援iframe!</p></iframe>";
		}
		exit();
	}elseif ($trafficnum == 3) {//計程車
		$query = "SELECT Tran_Taxi FROM transportation WHERE Tran_Name='$pt'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_row($result);
		echo "<iframe src='$row[0]'><p>您的瀏覽器不支援iframe!</p></iframe>";
		exit();
	}elseif ($trafficnum == 5) {//租車
		$query = "SELECT Tran_Rental FROM transportation WHERE Tran_Name='$pt'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_row($result);
		echo "<iframe src='$row[0]'><p>您的瀏覽器不支援iframe!</p></iframe>";
		exit();
	}
	exit();
}


//全站搜尋功能
if (isset($_POST['stext'])&&isset($_SESSION['pt'])) {
	$stext = $_POST['stext'];
	$pt = $_SESSION['pt'];
	$query = "SELECT name,address,image,id FROM location WHERE name LIKE '%$stext%'";
	$result = mysqli_query($conn,$query);
	$row = mysqli_fetch_row($result);
	$city = substr($row[1],0,9);
	if ($city == $pt) {
		$id = $row[3];
		$name = $row[0];
		$imgurl = $row[2];
		echo "<div class='content-block'><img src='$imgurl'/><p><a href='javascript:callDetail($id)'>$name</a></p></div>";

	}
	if($row[0] == ""){
		echo "<p>查無資料</p>";
		echo "<div style='width:200px;height:50px;margin:0 auto;text-align:center;'><a href='javascript:getcontent(1)'>回前頁</a></div>";
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
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script type="text/javascript" src="js/main.js"></script>
	
 
<script>

//AJAX傳送
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

//取得使用者切換的功能選項(吃喝玩樂、住宿、交通、活動、天氣)並進行AJAX傳值
function getcontent(num){
	var ispt = <?php echo $ifPtIsSet;?>;
	if (ispt == 0) {
		alert("尚未輸入資料!");
	}else{
		//若使用者選擇1(吃喝玩樂)或4(活動)則顯示周邊景點之Google map地標
		if (num == 1 || num == 4) {
			$('#map').css('display','block');
		}else{
			$('#map').css('display','none');
		}
		var name = "num";
		var data = num;
		var outputArea = "content";
		runAjax(name, data, outputArea);
	}
}

//取得高鐵資料並進行AJAX傳值
function getState_hsr(){
	var name = "hsr";
	var data = 1;
	var outputArea = "content-train";
	runAjax(name, data, outputArea);
}

//取得客運資料並進行AJAX傳值
function getState_bus(){
	var name = "bus";
	var data = 1;
	var outputArea = "content-train";
	runAjax(name, data, outputArea);
}

//取得當地交通選項資料並進行AJAX傳值
function gettraffic(trafficnum){
	var name = "trafficnum";
	var data = trafficnum;
	var outputArea = "ctraffic";
	runAjax(name, data, outputArea);
}

//取得全站搜尋input並進行AJAX傳值
function getsearch(stext){
	var name = "stext";
	var data = stext;
	var outputArea = "content";
	runAjax(name, data, outputArea);
}

//取得地區切換資料並進行AJAX傳值
function getDistrictChange(district){
	var name = "district";
	var data = district;
	alert(district);
	var outputArea = "content";
}

//啟動全站搜尋
function doSearch(){
	var stext = $('#search-text').val(); 
	getsearch(stext);
}

//啟動地區切換
function doDistrictChange(){
	var district = $('#district').val();
	getDistrictChange(district);
}
</script>
<script>
$(function(){
	//首頁搜尋欄位背後的圖片輪播
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


//「全站搜尋」輸入框的浮水印
$(function() {
  var tra,val
  val="全站搜尋";
  $("#search-text").css("color","#bfbfbf");
  $("#search-text").focus(function() {
    $("#search-text").val("");  
    $("#search-text").removeAttr("style");
    $("#search-text").val(tra);
    var e = event.srcElement;
    var r =e.createTextRange();
    r.moveStart("character",e.value.length);
    r.collapse(true);
    r.select();
  });
  $("#search-text").blur(function() {
    tra=$("#search-text").val();
    if($("#search-text").val()==""){
      $("#search-text").val(val);
      $("#search-text").css("color","#bfbfbf");
    }
  });
});
	</script>
	<script>
	//台鐵查詢時刻功能

//讀取xml格式之開放資料檔案
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

//取得火車時刻表內容
function getState(){
	var tmpFrom = "<?php echo $input_place_from_txt;?>";
	var tmpTo = "<?php echo $input_place_to_txt;?>";
	var tmpDate = "<?php echo $input_date;?>";
	var trains = getTrainInfo(); //呼叫製作火車時表的function，將傳回一物件，包含火車之車次、出發/到達時間、行車時間
	if (tmpFrom == tmpTo || trains.length == 0) {
		var innerContent = "<p style='padding:30px;'>查無乘車資訊!</p>";
		document.getElementById("content-train").innerHTML=innerContent;
	}else if (tmpFrom != "") {
		var innerContent = "<div class='train-info-table' name='train-info-table'><table><tr><td colspan='3' style='background:#404040;color:white;text-align:left;font-weight:bold;'>台鐵&nbsp;&nbsp;"+tmpFrom+" → "+tmpTo +"&nbsp;&nbsp;"+ tmpDate + "</td><td style='background:#404040;color:white;'><a href='http://railway.hinet.net/' target='_blank' class='ticket-button'>點我訂票</a></td></tr><tr class='train-info-col'><td>車次種</td><td>出發時間</td><td>到達時間</td><td>行車時間</td></tr>";
		for (var i = 0; i <trains.length; i++) {
			innerContent+= "<tr class='train-info-content'><td>"+trains[i].type+"</td><td>"+trains[i].fromTime+"</td><td>"+trains[i].toTime+"</td><td>"+trains[i].time+"</td></tr>";
		}
		innerContent+= "</table></div>";
		document.getElementById("content-train").innerHTML=innerContent;
	}else{
		alert("尚未輸入資料!");
	}
}

//製作火車時刻表(For 依抵達時間搜尋)
function getTrainInfoT(){
	  var input_time = "<?php echo $input_time;?>"; //取得使用者輸入之出發時間
      var input_From = "<?php echo $input_place_from;?>";//取得使用者輸入之出發地
      var input_To = "<?php echo $input_place_to;?>"; //取得使用者輸入之目的地
      xmlFile="js/xml/train.xml";
      xmlData=loadXMLFile(xmlFile); //讀取時刻表xml
      var trains = new Array();//用以存放result之火車時刻資訊
      var TrainInfo = xmlData.getElementsByTagName("TrainInfo"); //火車時刻表中的項目欄位，traininfo內容為某一車次的所有途經地點資料
      for (var i = 0; i < TrainInfo.length; i++) {
         var TimeInfo = TrainInfo[i].childNodes;//讀取traininfo中的子點(經過之各地點)
         var time = TimeInfo[0].getAttribute("DEPTime");//紀錄各地點的發車時間
         time = time.split(":")[0]; //因為等一下要比較時間且查詢是一小時為單位，所以先去掉冒號，取得「小時」的部分
         if (time > input_time) {continue;}   //因為是由上往下讀(時間是升序排列)，所以最上面代表該車最早的出發時間，若其最早時間比所輸入時間還要晚，則可直接continue不看
         var fromRef = false; //紀錄是否已得到出發的正確時刻
         var toRef = false; //紀錄是否已得到抵達的正確時刻
         var result_fromTime = ""; //紀錄result的出發時刻(正確)
         var result_toTime = ""; //紀錄result的抵達時刻(正確)

         for (var j = 0; j < TimeInfo.length; j++) { //正式進入時刻表中尋找，資料中timinfo為各traininfo的子點，即個地點的到達與發車時間

         	var timeRef = TimeInfo[j].getAttribute("DEPTime").split(":")[0];	//將讀到的發車時間拆出小時的部分
         	if (TimeInfo[j].getAttribute("Station") == input_From && timeRef == input_time && !toRef) {
         	//若符合搜尋條件且抵達尚未被設定則設定出發時間
         		fromRef = true;
         		result_fromTime = TimeInfo[j].getAttribute("DEPTime");
         		result_fromTime = result_fromTime.substr(0,5);
         	}
         	if (TimeInfo[j].getAttribute("Station") == input_To && fromRef) {
         		//若符合搜尋條件則設定抵達時間
         		toRef = true;
         		result_toTime = TimeInfo[j].getAttribute("ARRTime");
         		result_toTime = result_toTime.substr(0,5);
         	}

         }
         var train_number = TrainInfo[i].getAttribute("Train"); //取得結果之車次種
         if (fromRef && toRef) { //判斷出發與抵達皆被設定，則下面進行行車時間計算
         	console.log(train_number+"--DEP--"+result_fromTime+"--ARR--"+result_toTime);
         	var tmp_from_time = result_fromTime.split(":");
         	var tmp_to_time = result_toTime.split(":");
         	if (tmp_to_time[1] < tmp_from_time[1]) {
         		tmp_to_time[0] = parseInt(tmp_to_time[0])-1;
         		tmp_to_time[1] = parseInt(tmp_to_time[1])+60;
         	}
         	var due_h = parseInt(tmp_to_time[0])-parseInt(tmp_from_time[0]);
         	var due_m = parseInt(tmp_to_time[1])-parseInt(tmp_from_time[1]);

         	//將結果寫入物件
         	var train = {
         		type: train_number,
         		fromTime: result_fromTime,
         		toTime: result_toTime,
         		time: due_h+"小時"+due_m+"分"
         	};
         	trains.push(train);
         }
     }
      //傳回結果之火車時刻資料物件
     return trains;
}

function getTrainInfoF(){
	  var input_time = "<?php echo $input_time;?>";//取得使用者輸入之時間
      var input_From = "<?php echo $input_place_from;?>";//取得使用者輸入之出發地
      var input_To = "<?php echo $input_place_to;?>"; //取得使用者輸入之目的地
      xmlFile="js/xml/train.xml";
      xmlData=loadXMLFile(xmlFile);//讀取時刻表xml
      var trains = new Array();//用以存放result之火車時刻資訊
      var TrainInfo = xmlData.getElementsByTagName("TrainInfo");//火車時刻表中的項目欄位，traininfo內容為某一車次的所有途經地點資料
      for (var i = 0; i < TrainInfo.length; i++) {
         var TimeInfo = TrainInfo[i].childNodes;//讀取traininfo中的子點(經過之各地點)
               
         var fromRef = false;//紀錄是否已得到出發的正確時刻
         var toRef = false;//紀錄是否已得到抵達的正確時刻
         var result_fromTime = "";//紀錄result的出發時刻(正確)
         var result_toTime = "";//紀錄result的抵達時刻(正確)

         for (var j = TimeInfo.length-1; j >= 0; j--) {//正式進入時刻表中尋找，資料中timinfo為各traininfo的子點，即個地點的到達與發車時間
         	var timeRef = TimeInfo[j].getAttribute("ARRTime").split(":")[0];	//將讀到的到達時間拆出小時的部分
         	if (TimeInfo[j].getAttribute("Station") == parseInt(input_To) && timeRef == input_time && !fromRef) {
         		//因為此處是以抵達時間為搜尋點，所以改為降序搜尋，而先被設定的會改為抵達時間
         		toRef = true;
         		result_toTime = TimeInfo[j].getAttribute("ARRTime");
         		result_toTime = result_toTime.substr(0,5);
         	}
         	if (TimeInfo[j].getAttribute("Station") == parseInt(input_From) && toRef) {
         		fromRef = true;
         		console.log(TimeInfo[j].getAttribute("Station")+"***"+input_From);
         		result_fromTime = TimeInfo[j].getAttribute("DEPTime");
         		result_fromTime = result_fromTime.substr(0,5);
         	}

         }
         var train_number = TrainInfo[i].getAttribute("Train"); //取得車次種
         if (fromRef && toRef) {//判斷出發與抵達皆被設定，則下面進行行車時間計算
         	console.log(train_number+"--DEP--"+result_fromTime+"--ARR--"+result_toTime);
         	var tmp_from_time = result_fromTime.split(":");
         	var tmp_to_time = result_toTime.split(":");
         	if (tmp_to_time[1] < tmp_from_time[1]) {
         		tmp_to_time[0] = parseInt(tmp_to_time[0])-1;
         		tmp_to_time[1] = parseInt(tmp_to_time[1])+60;
         	}
         	var due_h = parseInt(tmp_to_time[0])-parseInt(tmp_from_time[0]);
         	var due_m = parseInt(tmp_to_time[1])-parseInt(tmp_from_time[1]);
         	//將結果寫入物件
         	var train = {
         		type: train_number,
         		fromTime: result_fromTime,
         		toTime: result_toTime,
         		time: due_h+"小時"+due_m+"分"
         	};
         	trains.push(train);
         }
     }
     //傳回火車時刻物件
     return trains;
}

function getTrainInfo() { //判斷要選擇的是「出發」方式或「抵達」方式，選擇使用哪一個function製作火車物件
      var select_Type = "<?php echo $select_type;?>";
      var trains = new Array();
      if (select_Type == "t") {//出發方式
      	trains = getTrainInfoT();
      	return trains;

      }else{//抵達方式
      	trains = getTrainInfoF();
      	return trains;
      }
} 

//響應式網頁側欄
jQuery.fn.slideLeftHide = function( speed, callback ) {
this.animate({
width : "hide",
paddingLeft : "hide",
paddingRight : "hide",
marginLeft : "hide",
marginRight : "hide"
}, speed, callback );
};
jQuery.fn.slideLeftShow = function( speed, callback ) {
this.animate({
width : "show",
paddingLeft : "show",
paddingRight : "show",
marginLeft : "show",
marginRight : "show"
}, speed, callback );
};

function closeSide(){
	$(".side-menu").slideLeftHide(300);
}
function callSide(){
	$(".side-menu").slideLeftShow(300);
}

	</script>
<style type="text/css">
#map {
        height: 400px;
        width: 100%;
        /*display: none;*/
       }
iframe{
	border: 5px solid #404040;
}
.cmt-hover{
	width: 100%;
	height: 100%;
	z-index: 100;
	background:rgba(0,0,0,0.7);
	position: fixed;
	/*display: none;*/
}
.cmt-content{
	width: 100%;
	height: 100%;
}
.cmt-content iframe{margin-top:0px;}
.cancel-hover{
	width: 100%;
	height: 100%;
	position: fixed;
	z-index: 15;
}
.cmt-hover-content{
	width: 60%;
	height: 100%;
	margin: 0 auto;
	border-radius: 5px;
	background:white;
	padding: 0% 5% 0% 5%;
}
#lt2{display: none;margin: 0 auto; border: 5px solid #404040;}
#lt1{display: block;margin: 0 auto; border: 5px solid #404040;}
.cross{width: 30px;height: 30px;float: right;}
.cross a{padding:5px;background-color: #0094FF;color:white;text-decoration: none;}
.cross a:hover{background-color: white;color:#0094FF;transition: 0.4s;}
.operations-menu li{border-right: 2px solid white;}
.m-logo{display: none;}
.hotel-table td{width:120px;text-align: left;}
.side-menu{display: none;}
#menu_btn{display: none;}
@media screen and (max-width: 835px) {
	#map{height: 300px;}
	.intro-p{display: none;}
	.search{display: none;}
	.top{background-color: white;height: 80px;}
	.container{width: 105px;margin-top: 20px;}
	.item-operate-wrap{width: 100%;}
	.fade-pic-wrap{width: 100%;}
	#fade_pic img{min-width: 300px; height: 540px;}
	.table-bg{top:20px; left: 0px; min-width: 300px;width: 90%;padding: 5%;min-height: 300px;border-radius: 0px;background-color: rgba(0,0,0,0.3); height: 452px; margin-top: 30px;}
	.table-bg form{min-width: 300px;}
	.operations-menu{width: 100% !important;}
	#operation-w{height: 230px;}
	.main{min-width: 300px;box-shadow:0px 0px 0px 0px;}
	.operations-menu li{width: 98%;background-color: white;color: #0094FF;font-weight: bold;border-right: 0px;padding-top: 0px;margin-top: 0px;}
	.input-item-m, .input-item-m-s{margin-top: 5px;margin-bottom: 5px;font-weight: bold;width: 100%;}
	.operations{margin-top: 540px; height: 150px; min-width: 300px;}
	.top-options{display: none;}
	.m-logo{display: block;}
	.table-bg select,.table-bg input[type="date"]{border: 4px white solid;width: 250px;}
	.train-info-table table{width: 90%;}
	.content-wrap{width: 100%; min-width: 300px;}
	.train-info-table td{font-size: 14px;}
	.item-operate{width: 100%;}
	.content-block img{width: 100%; height: 100%; opacity: 1;}
	.content-block p{width: 100%;height: 20px;}
	.content-block{width: 45%; height:140px;margin: 30px 8px 30px 8px;font-size: 14px;}
	.item-sort{display: none;}
	.item-operate{width: 95%;margin-left: 5%;}
	iframe{width: 90%;height: 500px;}
	.traffic-op{width: 90%;}
	.traffic-op li{width: 90px;}
	.traffic-op li img{width: 80px;}
	.table-bg form{
		width: 100%;
	}
	#menu_btn{display: block;}
	.side-menu{display:none;width: 80%;height: 100%; background-color: white;position: absolute;z-index: 109;}
	.side-title-before{width:90%;padding:20px 0px 20px 0px; background-color:#0094FF;
		color:white;font-weight:bold;font-size:30px;float: left;}
	.side-title-before a{text-decoration: none;color: white; font-weight: bold;}
}

.hotel-img{width: 300px;float: left;}
.hotel-img img{width: 100%;}
.hotel-content{width: 700px;float: right;padding: 30px;}
.hotel-content table{float: left;}
.hotel-content h2{font-weight: bold;font-size: 36px;text-align: left;margin-bottom: 20px;}
.check-hotel-btn{width: 120px;float: left;margin:35px 0px 0px 20px;}
.check-hotel-btn a{
	padding: 20px 10px 20px 10px;
	color: white;
	background-color: #FFC000;
	font-weight: bold;
	text-decoration: none;
	font-size: 24px;
	margin: 20px 10px 20px 10px;
}
.check-hotel-btn a:hover{color: #FFC000;background-color: white;transition: 0.4s;}
.hotel li{width: 1100px;float: left;margin: 10px;padding-bottom:10px;border-bottom: 1px solid black; }
.hotel{width: 1100px;margin: 0 auto;}
.ticket-button{
	color: #404040;
	background-color: white;
	padding: 5px 8px 5px 8px;
	text-decoration: none;
	font-weight: bold;
}
.ticket-button:hover{
	color: white;
	background-color: #404040;
	transition: 0.4s;
}

</style>

</head>
<body>
<div class="cmt-hover">
	<div class="cmt-hover-content">		
		<div class="cross"><a href="javascript:closeComment()">X</a></div>
		<div class="cmt-content" id="cmt-content">
		</div>
	</div>
</div>
<?php
//google登入設定
$gLoginSrc = 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode('https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/plus.me') . '&redirect_uri=' . urlencode(CLIENT_REDIRECT_URL) . '&response_type=code&client_id=' . CLIENT_ID . '&access_type=online';
?>
	<div class="wrap">
		<div class="side-menu">
			<p class="side-title-before">操作項目</p><p class="side-title-before" style="width:10%;"><a href="javascript:closeSide()"><</a></p>
<?php
//設定取得登入後的使用者資料
if (isset($_SESSION['FB_userID'])){
	$FB_userID = $_SESSION['FB_userID']; //actual google
	$uname = $_SESSION['name'];
	$userImgLink = $_COOKIE['userImgLink'];
	$userImgLinkLarge = substr($userImgLink, 0, strlen($userImgLink)-6);
echo <<<END
	<p><img src="$userImgLinkLarge" style="border-radius: 50%; width:200px;height:200px;margin-top:15px;"></p>
	<p>Hi! $uname</p>
	<p><a href = "fav.php" style="color:#0094FF;font-weight:bold;">我的最愛</a>&nbsp;&nbsp;<a href = "#" style="color:#0094FF;font-weight:bold;">登出</a></p>
END;
}else{
echo <<<END
	<span style="display:block;float:left;margin:20px 0px 0px 20px;"><a href="$gLoginSrc"><span><img src="img/blogin.png" style="width:35px;height:35px;float:left;"/><span style="display:block;color:#737373; font-weight:bold;width:200px; height:35px;float:left;">Login with google</span></span></a></span>
END;
}

?>
		</div>
		<div class="top">
			<div class="container">
				<a href="javascript:callSide()"><img id="menu_btn" src="img/menu.png"  style="left:5px;position:absolute;"/></a>
				<div class="logo" style="margin-top:5px;">
					<a href="index.php"><img src="img/logo-blue.png"></a>
				</div>
				<div class='search' style="margin:15px 0px 0px 130px;">
					<input type='text' name='search-item' id='search-text' value="全站搜尋">
					<button id='search-btn' onclick='doSearch()'></button>
				</div>
				<div class="top-options">
					<span class="login-info">
						
<?php 

						//判斷以前是否登入過，若沒有則將會員資料加入會員資料庫
						if (isset($_SESSION['FB_userID'])) {
							$FB_userID = $_SESSION['FB_userID']; //actual google
							$uname = $_SESSION['name'];
							$userImgLink = $_COOKIE['userImgLink'];
							$check = mysqli_query($conn, "SELECT * FROM google_users WHERE google_id='$FB_userID'");
							$check = mysqli_num_rows($check);
							if (empty($check)) { // if new user . Insert a new record		
							$query = "INSERT INTO google_users (google_id,google_name,google_picture_link) VALUES ('$FB_userID','$uname','$userImgLink')";
							mysqli_query($conn, $query);	
							}
echo <<<END
	<span style="display:block;float:left;"><a href="#"><img src="$userImgLink" style="border-radius: 50%;"></a>&nbsp;&nbsp;&nbsp;&nbsp;</span>
	<span style="display:block;float:left;">Hi! $uname</span>
	<span style="display:block;float:left;" class="top-btn"><a href="fav.php">我的收藏</a>&nbsp;&nbsp;&nbsp;</span>
						<span style="display:block;float:left;" class="top-a"><a href="index.php?logout=1">登出</a></span>
END;
						}else{
echo <<<END
	<span style="display:block;float:left;"><a href="$gLoginSrc"><span><img src="img/login.png" style="width:35px;height:35px;float:left;"/><span style="display:block;color:#0094FF; font-weight:bold;width:200px; height:35px;float:left;">Login with google</span></span></a></span>
END;
						}

						?>

						
													
					</span>
				</div>
			</div>
		</div>

		<div class="fade-pic-wrap">
		<div id="fade_pic"> 
			<a href="#" class="ad"><img src="img/banner2.png" /></a> 
			<a href="#" class="ad"><img src="img/banner3.png" /></a> 
			<a href="#" class="ad"><img src="img/banner4.png" /></a> 
		</div>
		</div>
		<div class="table-bg">
					<form action="index.php" method="post">
						<p style="font-size:32px;margin-bottom:50px;" class="intro-p">一站解決旅遊需求的旅遊網站</p>

						<span class="input-item-m"><span class="m-span">起點：</span>
						<select id="place-from" name="place-from">
							<option value="台北市" selected="selected">台北市</option>
							<option value="台中市">台中市</option>
							<option value="高雄市">高雄市</option></select></span>
						<span class="input-item-m"><span class="m-span">終點：</span>
						<select id="place-to" name="place-to">
							<option value="台北市" selected="selected">台北市</option>
							<option value="台中市">台中市</option>
							<option value="高雄市">高雄市</option></select></span>
						
						<span class="input-item-m"><span class="m-span">&nbsp;日期：</span>&nbsp;<input type="date" name="date" min="2017-12-10" value="2017-12-10"></span>
						<span class="input-item-m"><span class="m-span">時間：</span>&nbsp;
							<select id="input-time" name="input-time">
								<option value="06">06:00</option>
								<option value="07">07:00</option>
								<option value="08">08:00</option>
								<option value="09">09:00</option>
								<option value="10">10:00</option>
								<option value="11">11:00</option>
								<option value="12">12:00</option>
								<option value="13">13:00</option>
								<option value="14">14:00</option>
								<option value="15">15:00</option>
								<option value="16">16:00</option>
								<option value="17">17:00</option>
								<option value="18">18:00</option>
								<option value="19">19:00</option>
								<option value="20">20:00</option>
								<option value="21">21:00</option>
								<option value="22">22:00</option>
								<option value="23">23:00</option>
								<option value="00">00:00</option>
							</select>
						</span>
						<span class="input-item-m"><span><input type="radio" name="select-type" value="t" checked="checked"><span class="m-span">出發</span></span><span><input type="radio" name="select-type" value="f"><span class="m-span">抵達</span></span></span>
						<span class="input-item-m-s" >&nbsp;&nbsp;<input type="submit" value="查詢"></span>
					</form>
				</div>


			
		<?php
if (isset($_POST['place-to'])) {
	echo "<div class='operations'>
			<div class='operations-menu' style='width:610px;padding-top:20px;'>
			<ul>
				<a href='javascript:getState_hsr()'><li class='op-l1'>高鐵時刻表</li></a>
				<a href='javascript:getState()'><li class='op-l2'>台鐵時刻表</li></a>
				<a href='javascript:getState_bus()'><li class='op-l3'>客運時刻表</li></a>
			</ul>
			</div>
		</div>";
	echo "<div class='content-wrap'><div id='content-train'>
		<p style='padding:30px;'>↑↑↑請選擇搭乘車種↑↑↑</p>
	</div></div>";
	echo "<div class='operations' id='operation-w' style='margin-top:0px;'>
			<div class='operations-menu' style='width:1020px;'>
			<ul>
				<a href='javascript:getcontent(1)'><li class='op-l0'>吃喝玩樂</li></a>
				<a href='javascript:getcontent(2)'><li class='op-l1'>飯店旅館</li></a>
				<a href='javascript:getcontent(3)'><li class='op-l2'>當地交通</li></a>
				<a href='javascript:getcontent(4)'><li class='op-l3'>戶外活動</li></a>
				<a href='javascript:getcontent(5)'><li class='op-l4'>天氣預報</li></a>
			</ul>
			</div>
		</div>";
	$place_to = $_POST['place-to'];
	$_SESSION['pt'] = $_POST['place-to'];
//以下為大地圖與景點地標的設定，callDetail為吃喝玩樂，callDetaile為活動
echo <<<END
<div id='map' style='width:100%;float:left;'></div>
    <script>
    function callDetail(id){
    	innerContent = "<iframe src='detail.php?id="+id+"' style='width:834px;height:100%;' ></iframe>";
    	$('.cmt-hover').css('display','block');
    	document.getElementById('cmt-content').innerHTML=innerContent;
    }
    function callDetaile(id){
    	innerContent = "<iframe src='edetail.php?id="+id+"' style='width:834px;height:100%;' ></iframe>";
    	$('.cmt-hover').css('display','block');
    	document.getElementById('cmt-content').innerHTML=innerContent;
    }
    function closeComment(){
		$('.cmt-hover').css('display','none');
	}

	function initMap() {
		var markers=new Array();
END;
	$center = "center: {lat: 22.663177, lng: 120.2872375}";
	$query = "SELECT name,address,lat,lng,id FROM location";
	$result = mysqli_query($conn, $query);
	while ($row = mysqli_fetch_row($result)) {//自資料庫讀取目前目的地的景點資料(使用parseXML.php內容取得經緯度資料加入資料庫，若不加入資料庫可能使搜尋時間過長且因搜尋次數過多而使得API暫時停用)，若符合則設定其經緯度位置並push進地標裡面
		$city = substr($row[1],0,9);
		if ($city != $_SESSION['pt']) {
			continue;
		}
		echo "markers.push({lat: ".$row[2].", lng: ".$row[3]."});";

	}
	if ($_SESSION['pt'] == "高雄市") {//判斷目前目的城市為何，並設定其地圖的中心位置
		$center = "center: {lat: 22.663177, lng: 120.2872375}";
	}elseif ($_SESSION['pt'] == "台北市") {
		$center = "center: {lat: 25.0511301, lng: 121.5085911}";
	}elseif ($_SESSION['pt'] == "台中市") {
		$center = "center: {lat: 24.1367808, lng: 120.6828198}";
	}
echo <<<END
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 13,
END;
		echo $center;
echo <<<END
        });
        var infowindow = null;
        infowindow = new google.maps.InfoWindow({
		  content: 'holding...'
		});
        var names = new Array();
END;
	$query = "SELECT name,address,lat,lng,id FROM location";
	$result = mysqli_query($conn, $query);
	while ($row = mysqli_fetch_row($result)) { //設定各地標的名稱與編號(用以標明開啟「詳細資訊」後的資料)
		$city = substr($row[1],0,9);
		if ($city != $_SESSION['pt']) {
			continue;
		}
		echo "names.push({name: '".$row[0]."',id:".$row[4]."});";
	}
echo <<<END
        for (var i = 0; i <16; i++) {
        	var contentStr='<p style="font-family:微軟正黑體">'+names[i].name+'<p>'
        					+'<a href="javascript:callDetail('+names[i].id+')" >詳細資訊</a>';
        	 var marker = new google.maps.Marker({
	          position: markers[i],
	          map: map,
	          html: contentStr
	        });
        	 google.maps.event.addListener(marker, 'click', function () {
				// where I have added .html to the marker object.
				infowindow.setContent(this.html);
				infowindow.open(map, this);
			});
        	
        }
        
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDSnHy1CRAQdENR6Ixhh4DpzUc01LLd6c&callback=initMap">
    </script>
END;

	
	
	echo "<div class='content-wrap'><div id='content'></div></div>";
}else{

	
}
?>

	</div>
	
</body>

</html>