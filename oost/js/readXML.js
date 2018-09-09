// 首先會先寫ㄧ個函式來判斷瀏覽器是否支援 javascript 讀取 XML的功能

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

//瀏覽器載入後執行

window.onload=function() { 
      var date
      xmlFile="../xml/20171001.xml";
      xmlData=loadXMLFile(xmlFile);

      //var TrainInfo = xmlData.getElementsByTagName("TrainInfo")[0].firstChild.nodeValue;
      var TrainInfo = xmlData.getElementsByTagName("TrainInfo");
      for (var i = 0; i > TrainInfo.length; i++) {
         var TimeInfo = TrainInfo[i].childNodes;
         for (var j = 0; j >= TimeInfo.length; j++) {
            console.log(TimeInfo[j].getAttribute("Station"));
         }
      }
      document.getElementById('content').innerHTML +='userid = '+userid+'<br>';
      document.getElementById('content').innerHTML +='userid2 = '+userid2+'<br>';
      document.getElementById('content').innerHTML +='username = '+username+'<br>';
      document.getElementById('content').innerHTML +='email = '+email+'<br>';

} 