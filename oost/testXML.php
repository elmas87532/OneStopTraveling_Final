<?php
$input_place_from = "1228";
$input_place_to = "1238";
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
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

window.onload=function() { 
      var input_time = 9;
      var input_From = "1238"; //tainan
      var input_To = "1008"; //Kaohsiung

      xmlFile="js/xml/20171119.xml";
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
</head>
<body>
<div id="content">
</div>

</body>
</html>