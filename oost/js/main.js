(function($) {
	
$(document).ready(function() {
  //選單高度	
	menu_listh=((window.innerHeight-127)/7);
	$("#menu_p_list a").css({'height':menu_listh,'line-height':menu_listh+'px'});
	
	//開啟/關閉選單
	$("#menu_btn").click(function(){
		//if($("#menu_p").css('margin-left')=='-270px'){
	       $("#menu_p").animate({'margin-left':0},250,'swing');
		   $("#pho").prepend("<div id='coverframe'></div>");
		   window.ontouchmove=function(e){//禁止滑動
                e.preventDefault && e.preventDefault();
                e.returnValue=false;
                e.stopPropagation && e.stopPropagation();
                return false;
           }	 
   })
   $("#back_btn").click(function(){
	   		$("#coverframe").remove();
	        $("#menu_p").animate({'margin-left':-270},250,'swing');
		    window.ontouchmove=function(e){
                e.preventDefault && e.preventDefault();
                e.returnValue=true;
                e.stopPropagation && e.stopPropagation();
                return true;
			}
   })
	
    $('#top-banner').click(function(){
		// 讓捲軸移動到 0 的位置
        var $body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
		$body.animate({
			scrollTop: 0
		}, 300);
		return false;
	});	
	
})

$(window).resize(function() {
  //選單高度	
	menu_listh=((window.innerHeight-127)/7);
	$("#menu_p_list a").css({'height':menu_listh,'line-height':menu_listh+'px'});
 
 })

 var jud=0;
 $(window).scroll(function(){
		if($(this).scrollTop()>=80){
			if(jud!=1){
		   $("#top-banner").fadeTo(500,0.7);
			jud=1;
			}		  
			
		}else if($(this).scrollTop()<235){
			if(jud!=2){
		   $("#top-banner").fadeTo(500,1);
		    jud=2;
			}
			 
		}
  });
 
})(jQuery);