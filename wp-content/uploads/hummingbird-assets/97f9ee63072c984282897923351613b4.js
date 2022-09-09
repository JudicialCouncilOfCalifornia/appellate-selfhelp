/**handles:custom-script**/
var currentTallest=0,currentRowStart=0,chatCookie="",sticky_navigation_offset_top,rowDivs=new Array;function knowledge_center_video_icon(){var e=jQuery(".carousel-grid__item > a > img").height(),t=jQuery(".carousel-grid__item > a > img").width();jQuery(".carousel-grid__item > a > .overlay-icon").each(function(){jQuery(this).css({height:e+"px",width:t+"px"})}),jQuery(".carousel-slider__item > a > img").height("100%").width("100%"),setTimeout(function(){var e=jQuery(".carousel-slider__item > a > img").height(),t=jQuery(".carousel-slider__item > a > img").width();jQuery(".carousel-slider__item > a > .overlay-icon").each(function(){jQuery(this).css({height:e+"px",width:t+"px"})})},1e3)}
function setConformingHeight(e,t){console.log(t+":"+e.data("originalHeight")+":"+e.height()),e.data("originalHeight",void 0==e.data("originalHeight")||""==e.data("originalHeight")?e.height():e.data("originalHeight")),e.height(t)}
function getOriginalHeight(e){return void 0==e.data("originalHeight")||""==e.data("originalHeight")?e.height():e.data("originalHeight")}
function columnConform(e){for(currentTallest=0,rowDivs.length=0,e.height("100%"),e.data("originalHeight",""),e.each(function(){var e=jQuery(this);rowDivs.push(e),currentTallest=currentTallest<getOriginalHeight(e)?getOriginalHeight(e):currentTallest}),currentDiv=0;currentDiv<rowDivs.length;currentDiv++)setConformingHeight(rowDivs[currentDiv],currentTallest)}
function comman(){jQuery(".tooltipsall").length>0&&(jQuery(".tooltipsall .tooltipsall").each(function(){jQuery(this).qtip("destroy").removeAttr("style class")}),jQuery("h1 .tooltipsall,h2 .tooltipsall,.formsiconbox .tooltipsall,.img-videolightbox .tooltipsall,.avia-resource-list-container .tooltipsall, .timeline .tooltipsall").each(function(){jQuery(this).qtip("destroy").removeAttr("style class")}))}
function setCookie(e,t,i){var a=new Date;a.setTime(a.getTime()+24*i*60*60*1e3);var n="expires="+a.toUTCString();document.cookie=e+"="+t+";"+n+";path=/"}
function getCookie(e){for(var t=e+"=",i=decodeURIComponent(document.cookie).split(";"),a=0;a<i.length;a++){for(var n=i[a];" "==n.charAt(0);)n=n.substring(1);if(0==n.indexOf(t))return n.substring(t.length,n.length)}
return""}
var sticky_navigation=function(){var scroll_top=jQuery(window).scrollTop();if(scroll_top>sticky_navigation_offset_top){jQuery('aside.sidebar_left').css({'position':'fixed','top':110});}else{jQuery('aside.sidebar_left').css({'position':'relative','top':0});}};
jQuery(document).ready(function(){	
	if(jQuery(".jbe-notification").length > 0){ 
		jQuery('.notification-popup').magnificPopup({ type: 'iframe' }); 
		 var h = jQuery(".jbe-notification").innerHeight();
		 jQuery("body").css("padding-top",h+"px");
		jQuery(".jbe-notification-toggle").on("click", function(){
			if(jQuery(".jbe-notification").hasClass('close')){
			    jQuery(".jbe-notification").removeClass('close');
			    jQuery("body").css("padding-top",h+"px");
			}else{
			    jQuery(".jbe-notification").addClass('close');
			    jQuery("body").css("padding-top",0);
			} 
		})
	}
	if(jQuery("body").hasClass('en')){
	    jQuery("span[lang='es-AR']").closest("div.tooltips_list").remove();
	}else{
	    jQuery("span[lang='en-AR']").closest("div.tooltips_list").remove();
	}
	
	if((jQuery(".knowledge-center").length > 0 && jQuery(".knowledge-center article .av_three_fourth").length > 0) || (jQuery(".timeline-inner").length > 0 && jQuery(".timeline-inner article .av_three_fourth").length > 0) || (jQuery(".pdfprnt-buttons").next('.av_three_fourth').length > 0)){
		jQuery('.pdfprnt-buttons').addClass("av_three_fourth");
	}chatCookie=setTimeout(function(){""==getCookie("chat-hide")&&(jQuery("#chat-container").css("height","auto"),jQuery("#chat-initiator").addClass("hidden"),jQuery("#qns-bot").removeClass("hidden"))},2000),jQuery("#chat-initiator").on("click",function(){jQuery("#chat-container").css("height","auto"),jQuery(this).addClass("hidden"),jQuery("#qns-bot").removeClass("hidden")}),knowledge_center_video_icon(),columnConform(jQuery(".timeline .timeline-main .av_one_fourth > article > div")),comman();console.log("new code");if(jQuery('.jbe-anchor').length>0){var links='<ul class="sub-menu">';jQuery('.jbe-anchor').each(function(){var id=jQuery(this).attr("id");var name=jQuery(this).attr("data-name");links+='<li><a href="#'+id+'">'+name+'</a></li>';});links+='</ul>';jQuery("#menu-knowledge-center-menu .current-menu-item").append(links);console.log(links);}
if(jQuery(".page-template-template-timeline").length>0){jQuery("a.iconbox_icon").each(function(){var title = jQuery(this).attr("title");title = title.replace(/<br[^>]*>/g, ""); jQuery(this).attr("title",title)});}
if(jQuery('aside.sidebar_left').length>0){sticky_navigation_offset_top=jQuery('aside.sidebar_left').offset().top;}}),jQuery(window).load(function(){
	if((jQuery(".pdfprnt-buttons").next('.av_three_fourth').length > 0)){
		jQuery('.pdfprnt-buttons').addClass("av_three_fourth");
	}
	//ajax call 
    console.log(post_id);
    jQuery.ajax( 
        ajaxurl,
        {
			type: 'POST',
			data: { action: 'set_post_view', id: post_id},
			dataType: 'json',
            complete : function ( response ) {}, 
			success: function ( response ) {     // to develop in case of correct AJAX call
                             if ( response.success ) {
                                  console.log(response);

                             } else {
                                  console.log(response);
                             }
                        },
			error: function ( errorThrown ) {   // to develop in case of AJAX call error
                             console.log( errorThrown ); 
            },
	});
	jQuery(".youtube").on("click",function(e){e.preventDefault(),e.stopPropagation(),console.log("clicked")}),jQuery("#qns-bot .wc-header").on("click",function(){jQuery("#chat-container").css("height","0"),jQuery("#qns-bot").addClass("hidden"),jQuery("#chat-initiator").removeClass("hidden"),""==getCookie("chat-hide")&&(setCookie("chat-hide","chat-hide",1),clearTimeout(chatCookie))}),jQuery("body #header #header_meta .sub_menu > ul > li:first-child > a").attr("target","_blank"),columnConform(jQuery(".timeline .timeline-main .av_one_fourth > article > div")),console.log("Window load called..."),knowledge_center_video_icon(),jQuery(".owl-prev").html('<span class="av-icon-char" style="font-size:40px;line-height:40px;" aria-hidden="true" data-av_icon="" data-av_iconfont="entypo-fontello"></span><span class="av-icon-char" style="font-size:40px;line-height:40px;" aria-hidden="true" data-av_icon="" data-av_iconfont="entypo-fontello"></span>'),jQuery(".owl-next").html('<span class="av-icon-char" style="font-size:40px;line-height:40px;" aria-hidden="true" data-av_icon="" data-av_iconfont="entypo-fontello"></span><span class="av-icon-char" style="font-size:40px;line-height:40px;" aria-hidden="true" data-av_icon="" data-av_iconfont="entypo-fontello"></span>'),jQuery("body .timeline .timeline-main .av_one_fourth > article,#home-knowledge-center .av_one_fourth > article").each(function(){jQuery(this).find(".iconbox_content").append("<div class='triangle'></div>")}),jQuery(".main_menu li.menu-item-mega-parent > a .avia-menu-text label").append('<span class="av-icon-char custom-search" aria-hidden="true" data-av_icon="" data-av_iconfont="entypo-fontello"></span>'),jQuery(".knowledge-center .widget_nav_menu ul li.menu-item-type-custom > a").append('<span class="av-icon-char" aria-hidden="true" data-av_icon="" data-av_iconfont="entypo-fontello"></span>'),jQuery(".knowledge-center .widget_nav_menu ul li.menu-item-type-custom > a").on("click",function(){jQuery(".knowledge-center .widget_nav_menu ul li.menu-item-type-custom > .sub-menu").slideToggle()}),jQuery(window).scroll(function(){}),jQuery("ul.tt-filter > li").on("click",function(){jQuery("ul.tt-filter > li").removeClass("active"),jQuery(this).addClass("active").focus();var e=jQuery(this).attr("data-filter");jQuery(".iconbox").removeClass("filter-inactive"),"all"!=e&&jQuery(".iconbox").not("."+e).addClass("filter-inactive")}),jQuery(".ls-wp-container h1, .av_icon_caption,.iconbox_content_container > ul > li,.helpful-heading > div,.helpful-controls > div,.helpful-exists > div,.avia_textblock p,.avia_textblock h3,.avia_textblock h1,.avia_textblock h2").each(function(){jQuery(this).attr("tabindex","0")}),jQuery("body").hasClass("es")?jQuery(".av-burger-menu-main > a").attr("aria-label","Main Menu"):jQuery(".av-burger-menu-main > a").attr("aria-label","Menú principal"),jQuery("[role=listbox]").on("focus",function(){jQuery(this).find("[aria-selected=true]").length?jQuery(this).find("[aria-selected=true]").focus():jQuery(this).find("[role=option]:first").attr("aria-selected","true").focus()}),jQuery("[role=listbox]").on("keydown",function(e){var t=jQuery(this).find("[aria-selected=true]");switch(e.keyCode){case 37:t.prev().length&&(t.attr("aria-selected","false"),t.prev().attr("aria-selected","true").focus()),e.preventDefault();break;case 39:t.next().length&&(t.attr("aria-selected","false"),t.next().attr("aria-selected","true").focus()),e.preventDefault()}}),jQuery("[role=option]").on("mousedown",function(e){jQuery(this).parent().find("[aria-selected=true]").attr("aria-selected","false"),jQuery(this).attr("aria-selected","true"),e.preventDefault()}),comman()}),jQuery(window).on("resize",function(){knowledge_center_video_icon(),columnConform(jQuery(".timeline .timeline-main .av_one_fourth > article > div"))});