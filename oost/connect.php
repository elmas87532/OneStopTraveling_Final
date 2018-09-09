<?php
	echo "<meta charset='UTF-8' />";

	$a="140.127.220.198";
	//$a="localhost";
	//$conn=mysqli_connect($a,"root","","one_stop_traveling");
	$conn=mysqli_connect($a,"root","calltheshot","one_stop_traveling");
	mysqli_query($conn,"set names utf8");
	mysqli_select_db($conn,"one_stop_traveling");

?>