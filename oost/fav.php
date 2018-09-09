<?php
include 'connect.php';
session_start();
require_once('settings.php');
$FB_userID = $_SESSION['FB_userID'];

//刪除收藏
if (isset($_GET['del'])) {
	$del = $_GET['del'];
	$query = "DELETE FROM favorite WHERE L_id=$del AND FB_userID='$FB_userID'";
	mysqli_query($conn, $query);
}

//判斷選擇的是吃喝玩樂的收藏或活動的收藏，1:吃喝玩樂，3:活動
if (isset($_POST['type'])){
	$item = $_POST['type'];
	if ($item == 1) {
		$query = "SELECT L_id FROM favorite WHERE FB_userID='$FB_userID' AND Fav_Type=1";
		$result = mysqli_query($conn,$query);
		$arr=array();
		$cnt=0;
		while ($row = mysqli_fetch_row($result)) {
			$id = $row[0];
			array_push($arr,$id);
			$cnt++;
		}
		for ($i=0; $i < count($arr); $i++) { 
			$fid = $arr[$i];
			$query = "SELECT id,name,image FROM location WHERE id=$fid";
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_row($result);		
			$id = $row[0];
			$name = $row[1];
			$imgurl = $row[2];
			echo "<div class='content-block'><img src='$imgurl'/><p style='width:85%;'><a href='javascript:callDetail($id)'>$name</a></p><p class='item-delete' style='width:15%;'><a href='fav.php?del=$fid'><img src='img/delete.png'></a></p></div>";
		}
		exit();
	}elseif ($item == 3) {
		$query = "SELECT L_id FROM favorite WHERE FB_userID='$FB_userID' AND Fav_Type=3";
		$result = mysqli_query($conn,$query);
		$arr=array();
		$cnt=0;
		while ($row = mysqli_fetch_row($result)) {
			$id = $row[0];
			array_push($arr,$id);
			$cnt++;
		}
		for ($i=0; $i < count($arr); $i++) { 
			$fid = $arr[$i];
			$query = "SELECT id,name,image FROM event1 WHERE id=$fid";
			$result = mysqli_query($conn,$query);
			$row = mysqli_fetch_row($result);		
			$id = $row[0];
			$name = $row[1];
			$imgurl = $row[2];
			echo "<div class='content-block'><img src='$imgurl'/><p style='width:85%;'><a href='javascript:callDetaile($id)'>$name</a></p><p class='item-delete' style='width:15%;'><a href='fav.php?del=$fid'><img src='img/delete.png'></a></p></div>";
		}
		exit();
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/clean.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script>
	//取得使用者選擇的收藏項目(吃喝玩樂/活動)，以ajax方式傳送
function getcontent(type){
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
	xhr.send("type="+type);
	function getData(){
		if(xhr.readyState==4)
			if(xhr.status==200)
				document.getElementById("content").innerHTML=xhr.responseText;
	}
}

//設定RWD的側欄選單
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

//以下為開啟關閉側欄選單
function closeSide(){
	$(".side-menu").slideLeftHide(300);
}
function callSide(){
	$(".side-menu").slideLeftShow(300);
}

//以下為開啟收藏的內容
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
	</script>
<style type="text/css">
body{
	font-family: "微軟正黑體";
	text-align: center;
}
.main{
	float: left;
	width: 100%;
	margin: 0 auto;
}
.login-info{color: white;}
.login-info a{
	color: #0094FF;
	font-weight: bold;
}
.login-info a:hover{
	color: white;
	transition: 0.4s;
}
.content-block{
	float: left;
	width: 280px;
	height: 340px;
	text-align: center;
	margin: 40px 20px 40px 20px;
	font-size: 16px;
}
.content-block img{
	width: 280px;
	height: 280px; 
	opacity:1;
	transition:opacity linear .2s;
}
.content-block img:hover{opacity:0.8;}
.content-block p{
	width: 280px;
	height: 40px;
	background-color: #404040;
	text-align: center;
	padding-top: 10px;
	padding-bottom: 10px;
	float: left;
}
.content-block p a{
	color: white;
	font-weight: bold;
	text-decoration: none;
}
.input-item,.items li,.items li p,.contnet,.input-item td span,.search,.traffic-op-wrap,.traffic-op li,.fav-op{float: left;}
.user-info{float: right;}
.wrap{
	margin: 0 auto;
	width: 100%;
}
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
	width: 80%;
	margin: 0 auto;
}
.top-options{
	font-size: 20px;
	float: right;
	padding: 15px;margin-right: 10px;
}
.top-options img{
	margin-top: -10px;
	margin-left: 0px;
	margin-right: 10px;
}


#content{
	width: 1300px;
	text-align: center;
	margin: 0 auto;
}
.item-menu{width: 1300px;height: 50px; margin: 0 auto;}


.fav-op{
	width: 100%;
	height: 100px;
	margin-top: 100px;
}
.fav-op h1{
	text-align: left;
	font-size: 40px;
	font-weight: bold;
	color: #7F7F7F;
}
.item-menu img{
	float: left;
	width: 40px;
	padding-right: 50px;
	opacity: 0.7;
}
.item-menu li{
	float: left;
	color: #3399FF;
	width: 150px;
	padding: 10px;
	border-right: 1px solid #3399FF;
}
.item-menu li:hover{
	color: white;
	background-color: #3399FF;
}

.item-delete img{width: 30px;height: 30px;}
.content-wrap{
	padding: 0% 10% 10% 10%;
}
.top{height: 60px;}
iframe{
	border: 5px solid #404040;
}
.cmt-hover{
	width: 100%;
	height: 100%;
	z-index: 100;
	background:rgba(0,0,0,0.7);
	position: fixed;
	display: none;
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
.side-menu{display: none;}
#menu_btn{display: none;}
@media screen and (max-width: 835px) {/**----835----**/
	.top{height: 90px;}
	.top img{margin: 0 auto;margin-top: 18px;}
	.fade-pic-wrap{
		width: 100%;
		height: 85%;
		float: left;
	}
	.logo{width: 105px;margin: 0 auto;}
	.fav-op{height: 50px;}
	.fav-op h1{font-size: 32px;padding-left: 20px;}
	.login-info{display: none;}
	#fade_pic {height: 85%;}
	#fade_pic img{min-width: 375px;clip:rect(0px,50px,200px,0px);height: 1185px;}
	.main{min-width: 375px;margin-top: 0px;}
	.content-wrap{min-width: 300px;padding: 0px;}
	.item-menu li{width: 42%; font-size: 24px;}
	#content{width: 95%; margin: 0 auto;}
	.content-block p a{font-size: 16px;}
	.content-block p{height: 25px;}
	.content-block{width: 44%; height:160px;margin: 10px;}
	.content-block img{width: 100%; height: 100%;}
	.item-menu{width: 100%}
	#menu_btn{display: block;}
	.side-menu{display:none;width: 80%;height: 100%; background-color: white;position: absolute;z-index: 109;}
	.side-title-before{width:90%;padding:20px 0px 20px 0px; background-color:#0094FF;
		color:white;font-weight:bold;font-size:30px;float: left;}
	.side-title-before a{text-decoration: none;color: white; font-weight: bold;}
}/**----835END----**/
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
	<div class="wrap">
		<div class="side-menu">
			<p class="side-title-before">操作項目</p><p class="side-title-before" style="width:10%;"><a href="javascript:closeSide()"><</a></p>
<?php
if (isset($_SESSION['FB_userID'])){
	$FB_userID = $_SESSION['FB_userID']; //actual google
	$uname = $_SESSION['name'];
	$userImgLink = $_COOKIE['userImgLink'];
	$userImgLinkLarge = substr($userImgLink, 0, strlen($userImgLink)-6);
echo <<<END
	<p><img src="$userImgLinkLarge" style="border-radius: 50%; width:200px;height:200px;margin-top:15px;"></p>
	<p>Hi! $uname</p>
	<p><a href = "fav.php" style="color:#0094FF;font-weight:bold;">我的收藏</a>&nbsp;&nbsp;<a href = "index.php?logout=1" style="color:#0094FF;font-weight:bold;">登出</a></p>
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
				<a href="javascript:callSide()"><img id="menu_btn" src="img/wmenu.png"  style="left:5px;position:absolute;"/></a>
				<div class="logo" style="margin-top:5px;">
					<a href="index.php"><img src="img/logo.png"></a>
				</div>

				<div class="top-options">
					<span class="login-info">
						
<?php 


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
	<span style="display:block;float:left;margin-right:20px;">Hi! $uname</span>
						<span style="display:block;float:left;" class="top-a"><a href="index.php?logout=1" style="text-decoration:none;">登出</a></span>
END;
						}else{
echo <<<END
	<span style="display:block;float:left;"><a href="$gLoginSrc"><span><img src="img/login.png" style="width:35px;height:35px;float:left;"/><span style="display:block;color:#DC4A38; font-weight:bold;width:200px; height:35px;float:left;">Login with google</span></span></a></span>
END;
						}

						?>

						
													
					</span>
				</div>
			</div>
		</div>
		<div class="content-wrap">
			<div class="fav-op">
				<h1>我的收藏</h1>
			</div>
			<div class="main">
				<div class='item-menu'>
					<ul>
						<a href='javascript:getcontent(1)' id='dining'><li>吃喝玩樂</li></a>
						<a href='javascript:getcontent(3)' id='dining'><li style="border-right:0px;">活動</li></a>
					</ul>
				</div>
				<div id='content'>
	<?php
	//預設進來時式顯示吃喝玩樂的收藏
			$query = "SELECT L_id FROM favorite WHERE FB_userID='$FB_userID' AND Fav_Type=1";
			$result = mysqli_query($conn,$query);
			$arr=array();
			$cnt=0;
			while ($row = mysqli_fetch_row($result)) {
				$id = $row[0];
				array_push($arr,$id);
				$cnt++;
			}

			for ($i=0; $i < count($arr); $i++) { 
				$fid = $arr[$i];
				$query = "SELECT id,name,image FROM location WHERE id=$fid";
				$result = mysqli_query($conn,$query);
				$row = mysqli_fetch_row($result);		
				$id = $row[0];
				$name = $row[1];
				$imgurl = $row[2];
				echo "<div class='content-block'><img src='$imgurl'/><p style='width:85%;'><a href='javascript:callDetail($id)'>$name</a></p><p class='item-delete' style='width:15%;'><a href='fav.php?del=$fid'><img src='img/delete.png'></a></p></div>";

			}

		

	?>
				</div>
			</div>

		</div>
		
		
	
		
	</div>
	
	
</body>

</html>