<?php
include 'connect.php';
session_start();

$FB_userID = $_SESSION['FB_userID'];
$name = "";
$cost = "";
$intro = "";
$phone = "";
$address = "";
$img = "";
$time = "";
$c_city = "";
$id = 0;

$query = "SELECT * FROM google_users WHERE google_id='$FB_userID'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_row($result);
$userImg = substr($row[2],0,strlen($row[2])-6);

//將評論加入資料庫
if (isset($_POST['cmt-title']) && isset($_GET['id'])) {
	$title = $_POST['cmt-title'];
	$content = $_POST['cmt-content'];
	$level = (int)$_POST['cmt-level'];
	$id = (int)$_GET['id'];
	$query = "INSERT INTO ecomment(FB_userID,C_title,C_content,E_id,C_level) VALUES('$FB_userID','$title','$content',$id,$level)";
	//echo $query;
	mysqli_query($conn,$query);
}

//取得該地點的相關資訊
if (isset($_GET['id'])) {
	$id = $_GET['id'];
	$query = "SELECT * FROM event1 WHERE id=$id";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_row($result);
	$name = $row[1];
	$date = $row[2];
	$cost = $row[3];
	$address = $row[4];
	$local = $row[5];
	$note = $row[6];
	$phone = $row[7];
	$image = $row[8];

	$c_city = substr($address,0,9);
}

//加入收藏功能
if (isset($_GET['fav']) && isset($_GET['id'])) {
	$insertid = $_GET['id'];
	$query = "SELECT L_id FROM favorite WHERE FB_userID='$FB_userID' AND Fav_Type=3";
	$result = mysqli_query($conn, $query);
	$cnt = 0;
	while ($row = mysqli_fetch_row($result)) {
		if ($row[0] == $insertid) {
			$cnt++;
		}
	}
	if ($cnt) {
		echo "<script>alert('此活動已加過收藏!');</script>";
	}else{
		$query = "INSERT INTO favorite(FB_userID,L_id,Fav_Type) VALUES('$FB_userID',$insertid,3)";
		mysqli_query($conn, $query);
		echo "<script>alert('收藏成功!');</script>";
	}
}

//尋找此地點的googlemap，並記錄下網址以供後面iframe使用
$iframeSrc = "https://www.google.com/maps/embed/v1/place?key=AIzaSyAG7Mbp0e9dzz07GecvlZN7po30yXODJqI&q=".$address;
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" type="text/css" href="css/clean.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script>
	//以下為開啟/關閉評論的視窗
function doComment() {
	$(".cmt-hover").css("display","block");
}
function closeComment(){
	$(".cmt-hover").css("display","none");
}

//評論標題輸入框的浮水印
$(function() {
  var tra,val
  val="打一個吸引人的標題吧!";
  $("#cmt-title").css("color","#bfbfbf");
  $("#cmt-title").focus(function() {
    $("#cmt-title").val("");  
    $("#cmt-title").removeAttr("style");
    $("#cmt-title").val(tra);
    var e = event.srcElement;
    var r =e.createTextRange();
    r.moveStart("character",e.value.length);
    r.collapse(true);
    r.select();
  });
  $("#cmt-title").blur(function() {
    tra=$("#cmt-title").val();
    if($("#cmt-title").val()==""){
      $("#cmt-title").val(val);
      $("#cmt-title").css("color","#bfbfbf");
    }
  });
});

	</script>
<style type="text/css">


@media screen and (max-width: 835px) {/**----835----**/
	.main{box-shadow:0px 0px 0px 0px;}
	.top{background-color: white;height: 80px;}
	.container{width: 105px;margin-top: 20px;}
	.top-options{display: none;}
	.m-logo{display: block;}
	.logo{display: none;}
	.main{min-width: 300px;}
	.main-intro{width: 90%; padding: 5%;}
	.main-img-wrap{margin: 0 auto;width: 350px;float: right;}
	.comment{width: 100%;}
	.comment-option{width: 100%;}
	.comment-content {width: 70%;}
	.comment-split{width: 100%;}
	.comment-content p{width: 100%;}
	.do-comment{width: 100%;text-align: center;}
	.comment-option p{width: 100%;margin-bottom: 20px;}
	.title{width: 80%;}
	.main-img{width: 80%;max-width: 500px;}
	.fav-op{width: 45%;margin-left: 0px;}
	.fav-op h1{font-size: 30px;}
	.detail-box li{width: 50%;}
	.comment-content-img{width: 25%;float: left;}
	.comment-content-img img{width: 100%;border-radius: 50%;}
	.cmt-content input[type="text"], .cmt-content textarea, .cmt-content select{width: 350px;}
	.cmt-content p{width: 350px;}
	.cmt-hover-content{height: 65%;}
	.cmt-content img{width:30px; height: 30px;}
	.cmt-content select{border: 2px solid #DCDCDC;}
}
</style>

</head>
<body>

<div class="cmt-hover">

	<div class="cmt-hover-content">
		<form action="detail.php?id=<?php echo $id;?>" method="post">
		<div class="cmt-content">
			<p>評論標題</p>
			<p><input type="text" name="cmt-title" id="cmt-title" value="打一個吸引人的標題吧!"></p>
			<p>整體評價</p>
			<p><select name="cmt-level">
				<option value="5">5:非常好</option>
				<option value="4">4:不錯</option>
				<option value="3">3:普通</option>
				<option value="2">2:不太好</option>
				<option value="1">1:差</option>
			</select></p>
			<p>評論內容</p>
			<p><textarea rows="5" name="cmt-content" id="cmt-content"></textarea></p>
		</div>
		<div class="cmt-submit-wrap"><input type="submit" value="發表評論">&nbsp;&nbsp;<input type="button" onclick="closeComment();" value="關閉"></div>
		</form>
	</div>
</div>


	<div class="wrap">
		<div class="main">
			
			
			<div class="main-intro">
				<div class="detail-box">
					<div class="main-img"><img src="<?php echo $image;?>"></div>
					<div class="fav-op">
						<h1><?php echo $name;?></h1>
						<div class="fav-btn"><a href="edetail.php?fav=t&id=<?php echo $id;?>">加入收藏</a></div>
						<p class="detail" style="padding-top:0px;padding-bottom:10px;">日期：<?php echo $date;?></p>
						<p class="detail" style="padding-top:0px;padding-bottom:10px;">價格：<?php echo $cost;?></p>
						<p class="detail" style="padding-top:0px;padding-bottom:10px;"><?php echo $phone;?></p>
						<p class="detail" style="padding-top:0px;padding-bottom:10px;">地址：<?php echo $address;?>&nbsp;<?php echo $local;?></p>
						
					</div>
				</div>		
				
				
				<iframe
				  width="600"
				  height="450"
				  style="border:0;height:350px;"
				  frameborder="0" style="border:0"
				  src="<?php echo $iframeSrc;?>" allowfullscreen>
				</iframe>	
				
				<div class="t"><div class="title-front"></div><div class="title">周邊</div></div>
				<div class="detail-box">
					<ul>
				<?php
				//取得該地點的周邊資料(以同地區，例如高雄市楠梓區，內的景點為主)
	$query = "SELECT name,address,image,id FROM location";
	$result = mysqli_query($conn,$query);
	$cnt = 0;
	while ($row = mysqli_fetch_row($result)) {
		$city = substr($row[1],0,9);
		$id = $row[3];
		if ($cnt >= 4) {
			break;
		}
		if ($city == $c_city) {
			$in_name = $row[0];
			$imgurl = $row[2];
			if($in_name == $name){
				continue;
			}
			if (strlen($in_name)>=9 && substr($in_name,0,9)=="已歇業") {
				continue;
			}
			if ($imgurl == "undefined") {
				continue;
			}
			echo "<li><a href='detail.php?id=$id'><img src='$imgurl'/></a><p><a href='detail.php?id=$id' >$in_name</a></p></li>";
			$cnt++;
		}	
	}
				?>
					</ul>
				</div>
				
				<div class="t"><div class="title-front"></div><div class="title">評論</div></div>
				<div class="detail-box">
					<div class="comment-option">
						<p>7則評論</p>
						<div class="do-comment"><a href="javascript:doComment()">發表評論</a></div>
					</div>
					
					
					<?php
					//以下為顯示此地點的評論紀錄
$id = $_GET['id'];
$query = "SELECT * FROM ecomment WHERE FB_userID='$FB_userID' AND E_id=$id ORDER BY C_id DESC";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_row($result)){
	$title = $row[1];
	$content = $row[2];
	$level = $row[5];
	echo '<div class="comment">
						<div class="comment-content-img"><img src="'.$userImg.'"></div>
						<div class="comment-content">
							<p>今天</p>
							<p style="font-weight:bold;">'.$title.'</p>
							<p style="font-weight:bold; color:#2BB673;"><img src="img/star.png" style="width:30px;height:30px;vertical-align:middle;"/>&nbsp;'.$level.'</p>
							<p>'.$content.'</p>
						</div>
						<div class="comment-split"></div>
					</div>';
}
					?>
					<div class="comment">
						<div class="comment-content-img"><img src="img/rem.png"></div>
						<div class="comment-content">
							<p>6天前</p>
							<p style="font-weight:bold;">Great</p>
							<p>很推薦~~</p>
						</div>
						<div class="comment-split"></div>
					</div>
				</div>
			</div>
			
		</div>
	</div>
</body>
</html>