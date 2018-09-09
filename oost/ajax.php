<?php
if (isset($_POST['num1']) && isset($_POST['num2'])) {
	echo $_POST['num1']+$_POST['num2'];
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title></title>
	<script type="text/javascript">
	var xhr;

	function run_ajax(){
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

		var value1=document.getElementById("num1").value;
		var value2=document.getElementById("num2").value;
		xhr.send("num1="+value1+"&num2="+value2);

		function getData(){
			if(xhr.readyState==4)
				if(xhr.status==200)
					document.getElementById("answer").innerHTML=xhr.responseText;
		}
	}
	</script>
</head>
<body>
	<input type="text" size="3" name="num1" id="num1">+
	<input type="text" size="3" name="num2" id="num2">

	<button type="button" onclick="run_ajax();">=</button>
	<span id="answer">?</span>
</body>
</html>