try{(function($){"use strict";const HelpfulPlugin={el:".helpful",vote:"helpful_save_vote",feedback:"helpful_save_feedback",initPlugin:function(){const self=this;if(self.el.length<1){return;}
$(document).on("click",".helpful .helpful-controls button",function(e){if(e.target!==e.currentTarget){return;}
var currentButton=$(this);var currentForm=$(currentButton).closest('.helpful');var ajaxData={};console.log($(currentButton).data());$.extend(ajaxData,helpful.ajax_data);$.extend(ajaxData,$(currentButton).data());ajaxData.action=self.vote;self.ajaxRequest(ajaxData).done(function(response){$(currentForm).find(".helpful-header").remove();$(currentForm).find(".helpful-controls").remove();$(currentForm).find(".helpful-footer").remove();$(currentForm).find(".helpful-content").html(response);self.feedbackForm(currentForm);});});},feedbackForm:function(currentForm){var self=this;$(currentForm).find('.helpful-cancel').click(function(e){e.preventDefault();var ajaxData=[{name:'action',value:'helpful_save_feedback'},{name:'cancel',value:1},{name:'type',value:$(currentForm).find('[name="type"]').val()},{name:'_wpnonce',value:$(currentForm).find('[name="_wpnonce"]').val()},];self.ajaxRequest(ajaxData).done(function(response){$(currentForm).find(".helpful-content").html(response);});});$(currentForm).on("submit",".helpful-feedback-form",function(e){e.preventDefault();var ajaxData=$(this).serializeArray();self.ajaxRequest(ajaxData).done(function(response){$(currentForm).find(".helpful-content").html(response);});});},ajaxRequest:function(data){return $.ajax({url:helpful.ajax_url,data:data,method:"POST",});},};HelpfulPlugin.initPlugin();})(jQuery)}catch(e){console.log(e)}try{!function(a,b){"use strict";function c(){if(!e){e=!0;var a,c,d,f,g=-1!==navigator.appVersion.indexOf("MSIE 10"),h=!!navigator.userAgent.match(/Trident.*rv:11\./),i=b.querySelectorAll("iframe.wp-embedded-content");for(c=0;c<i.length;c++){if(d=i[c],!d.getAttribute("data-secret"))f=Math.random().toString(36).substr(2,10),d.src+="#?secret="+f,d.setAttribute("data-secret",f);if(g||h)a=d.cloneNode(!0),a.removeAttribute("security"),d.parentNode.replaceChild(a,d)}}}var d=!1,e=!1;if(b.querySelector)if(a.addEventListener)d=!0;if(a.wp=a.wp||{},!a.wp.receiveEmbedMessage)if(a.wp.receiveEmbedMessage=function(c){var d=c.data;if(d)if(d.secret||d.message||d.value)if(!/[^a-zA-Z0-9]/.test(d.secret)){var e,f,g,h,i,j=b.querySelectorAll('iframe[data-secret="'+d.secret+'"]'),k=b.querySelectorAll('blockquote[data-secret="'+d.secret+'"]');for(e=0;e<k.length;e++)k[e].style.display="none";for(e=0;e<j.length;e++)if(f=j[e],c.source===f.contentWindow){if(f.removeAttribute("style"),"height"===d.message){if(g=parseInt(d.value,10),g>1e3)g=1e3;else if(~~g<200)g=200;f.height=g}if("link"===d.message)if(h=b.createElement("a"),i=b.createElement("a"),h.href=f.getAttribute("src"),i.href=d.value,i.host===h.host)if(b.activeElement===f)a.top.location.href=d.value}else;}},d)a.addEventListener("message",a.wp.receiveEmbedMessage,!1),b.addEventListener("DOMContentLoaded",c,!1),a.addEventListener("load",c,!1)}(window,document)}catch(e){console.log(e)}try{function userFormTabs(evt,tab){var i,tabcontent,tablinks;tabcontent=document.getElementsByClassName("tabcontent");for(i=0;i<tabcontent.length;i++){tabcontent[i].style.display="none";}
tablinks=document.getElementsByClassName("tablinks");for(i=0;i<tablinks.length;i++){tablinks[i].className=tablinks[i].className.replace(" active","");}
document.getElementById(tab).style.display="block";evt.currentTarget.className+=" active";}
function formSearch(inputId,ulId){var input,filter,ul,li,a,i,txtValue;input=document.getElementById(inputId);filter=input.value.toUpperCase();ul=document.getElementById(ulId);li=ul.getElementsByTagName("li");for(i=0;i<li.length;i++){a=li[i].getElementsByTagName("a")[0];txtValue=a.textContent||a.innerText;if(txtValue.toUpperCase().indexOf(filter)>-1){li[i].style.display="";}else{li[i].style.display="none";}}}
function formsSearch(inputId,id){var input,filter,parent,child,a,i,txtValue;input=document.getElementById(inputId);filter=input.value.toUpperCase();parent=document.getElementById(id);child=parent.getElementsByTagName("div");for(i=0;i<child.length;i++){a=child[i].getElementsByTagName("a")[0];txtValue=a.textContent||a.innerText;if(txtValue.toUpperCase().indexOf(filter)>-1){child[i].style.display="";}else{child[i].style.display="none";}}}
jQuery(function($){$(document).ready(function(){$('.magnific-popup').magnificPopup({type:'iframe',mainClass:'mfp-fade',preloader:true,});var w=$(".intro .magnific-popup img").width();var h=$(".intro .magnific-popup img").height();$(".intro .magnific-popup .overlay-icon").css({width:w+'px',height:h+'px'});$(".showAddcase").on("click",function(){$(".prepare-doc-intro,.prepare-doc-case-details").hide(10,function(){$(".prepare-doc-case").fadeIn(600);});});$(".add-case a").on("click",function(e){e.preventDefault();$(".prepare-doc-case").fadeOut(10,function(){$(".prepare-doc-case-details").fadeIn(600);});});$(".case-options .edit").on("click",function(){$(".case-details-container").fadeIn(600);});$(".prepare-doc-forms .cancel").on("click",function(){$(".case-details-container").fadeOut(400);});$('#formcase').submit(function(e){var cname=$('#case-name').val();var utype=$('input[name="userType"]:checked');console.log("calling");$(".error").remove();if(cname.length<1){$('#case-name').after('<span class="error">This field is required</span>');return false;}else if(utype.length<1){$('.button-container').before('<span class="error">This field is required</span>');return false;}
return true;});if(document.getElementById("defaultOpen"))document.getElementById("defaultOpen").click();if($(".saveGuideDraft").length>0){$(".saveGuideDraft > .guideButton").removeAttr("data-disabled");$(".saveGuideDraft button.save").removeAttr("disabled").removeAttr("aria-disabled");}
$('.form-delete').on("click",function(e){e.preventDefault();e.stopImmediatePropagation();var userdataID=$(this).attr("userdataID");var id=$(this).attr("id");var data={"action":"delete","id":id,"userdataID":userdataID};var that=$(this);$.ajax({type:'POST',url:'https://selfhelp.appellate.courts.ca.gov/wp-content/plugins/jbe-prepare-doc/includes/jbe-case-forms-functions.php',data:data,success:function(result){console.log(result);if(result=="YES"){$(that).parent().parent().remove();}}});});$('.save').on("click",function(){var interval=setInterval(function(){if($("#saveAF_message_box").is(":visible")){clearInterval(interval);var data={"action":"draft"};$.ajax({type:'POST',url:'https://selfhelp.appellate.courts.ca.gov/wp-content/plugins/jbe-prepare-doc/includes/jbe-case-forms-functions.php',data:data,success:function(result){if(result=="YES"){console.log("Draft Saved.");}}});}},100);});$('select[name=aem-form]').on('change',function(){$(".form-error").hide();});$("#addForm").on("click",function(){var href=$('select[name=aem-form]').val();if(href=="javascript:void(0)"){$(".form-error").show();return;}
window.open(href,'_blank');});});})}catch(e){console.log(e)}try{var currentTallest=0,currentRowStart=0,chatCookie="",sticky_navigation_offset_top,rowDivs=new Array;function knowledge_center_video_icon(){var e=jQuery(".carousel-grid__item > a > img").height(),t=jQuery(".carousel-grid__item > a > img").width();jQuery(".carousel-grid__item > a > .overlay-icon").each(function(){jQuery(this).css({height:e+"px",width:t+"px"})}),jQuery(".carousel-slider__item > a > img").height("100%").width("100%"),setTimeout(function(){var e=jQuery(".carousel-slider__item > a > img").height(),t=jQuery(".carousel-slider__item > a > img").width();jQuery(".carousel-slider__item > a > .overlay-icon").each(function(){jQuery(this).css({height:e+"px",width:t+"px"})})},1e3)}
function setConformingHeight(e,t){e.data("originalHeight",void 0==e.data("originalHeight")||""==e.data("originalHeight")?e.height():e.data("originalHeight")),e.height(t)}
function getOriginalHeight(e){return void 0==e.data("originalHeight")||""==e.data("originalHeight")?e.height():e.data("originalHeight")}
function columnConform(e){var currentDiv=0;for(currentTallest=0,rowDivs.length=0,e.height("100%"),e.data("originalHeight",""),e.each(function(){var e=jQuery(this);rowDivs.push(e),currentTallest=currentTallest<getOriginalHeight(e)?getOriginalHeight(e):currentTallest});currentDiv<rowDivs.length;currentDiv++)setConformingHeight(rowDivs[currentDiv],currentTallest)}
function comman(){jQuery(".tooltipsall").length>0&&(jQuery(".tooltipsall .tooltipsall").each(function(){jQuery(this).qtip("destroy").removeAttr("style class")}),jQuery("h1 .tooltipsall,h2 .tooltipsall,.formsiconbox .tooltipsall,.img-videolightbox .tooltipsall,.avia-resource-list-container .tooltipsall, .timeline .tooltipsall").each(function(){jQuery(this).qtip("destroy").removeAttr("style class")}))}
function setCookie(e,t,i){var a=new Date;a.setTime(a.getTime()+24*i*60*60*1e3);var n="expires="+a.toUTCString();document.cookie=e+"="+t+";"+n+";path=/"}
function getCookie(e){for(var t=e+"=",i=decodeURIComponent(document.cookie).split(";"),a=0;a<i.length;a++){for(var n=i[a];" "==n.charAt(0);)n=n.substring(1);if(0==n.indexOf(t))return n.substring(t.length,n.length)}
return""}
var sticky_navigation=function(){var scroll_top=jQuery(window).scrollTop();if(scroll_top>sticky_navigation_offset_top){jQuery('aside.sidebar_left').css({'position':'fixed','top':110});}else{jQuery('aside.sidebar_left').css({'position':'relative','top':0});}};jQuery(document).ready(function(){if(jQuery(".jbe-notification").length>0){jQuery('.notification-popup').magnificPopup({type:'iframe'});var h=jQuery(".jbe-notification").innerHeight();jQuery("body").css("padding-top",h+"px");jQuery(".jbe-notification-toggle").on("click",function(){if(jQuery(".jbe-notification").hasClass('close')){jQuery(".jbe-notification").removeClass('close');jQuery("body").css("padding-top",h+"px");}else{jQuery(".jbe-notification").addClass('close');jQuery("body").css("padding-top",0);}})}
var querystring=window.location.href.slice(window.location.href.indexOf('?')+1);var mainManu=jQuery(".av-main-nav > li.menu-item-language a").attr("href");var subManu=jQuery(".sub-menu > li.menu-item-language a").attr("href");if(jQuery("body").hasClass('en')){jQuery(".av-main-nav > li.menu-item-language a").attr("href",mainManu+"?"+querystring);jQuery(".sub-menu > li.menu-item-language a").attr("href",subManu+"&"+querystring);jQuery("span[lang='es-AR']").closest("div.tooltips_list").remove();}else{jQuery("span[lang='en-AR']").closest("div.tooltips_list").remove();if(jQuery(".tooltips_list").length>0){jQuery(".tooltips_list").each(function(){console.log(jQuery(this).find(".tooltips_table_content > span[lang='es-AR']").length);if(jQuery(this).find(".tooltips_table_content > span[lang='es-AR']").length==0){jQuery(this).remove();}});}}
if((jQuery(".knowledge-center").length>0&&jQuery(".knowledge-center article .av_three_fourth").length>0)||(jQuery(".timeline-inner").length>0&&jQuery(".timeline-inner article .av_three_fourth").length>0)||(jQuery(".pdfprnt-buttons").next('.av_three_fourth').length>0)){jQuery('.pdfprnt-buttons').addClass("av_three_fourth");}
chatCookie=setTimeout(function(){""==getCookie("chat-hide")&&(jQuery("#chat-container").css("height","auto"),jQuery("#chat-initiator").addClass("chat_hidden"),jQuery("#qns-bot").removeClass("chat_hidden"))},2000),jQuery("#chat-initiator").on("click",function(){jQuery("#chat-container").css("height","auto"),jQuery(this).addClass("chat_hidden"),jQuery("#qns-bot").removeClass("chat_hidden")}),knowledge_center_video_icon(),columnConform(jQuery(".timeline .timeline-main .av_one_fourth > article > div")),comman();console.log("new code");if(jQuery("body").hasClass('home')){comman();}
if(jQuery('.jbe-anchor').length>0){var links='<ul class="sub-menu">';jQuery('.jbe-anchor').each(function(){var id=jQuery(this).attr("id");var name=jQuery(this).attr("data-name");links+='<li><a href="#'+id+'">'+name+'</a></li>';});links+='</ul>';jQuery("#menu-knowledge-center-menu .current-menu-item,#menu-knowledge-center-menu-es .current-menu-item").append(links);console.log(links);}
if(jQuery(".page-template-template-timeline").length>0){jQuery("a.iconbox_icon").each(function(){var title=jQuery(this).attr("title");title=title.replace(/<br [^>]*>/g," ");jQuery(this).attr("title",title);});jQuery(".iconbox_content_title a").each(function(){var title=jQuery(this).attr("title");title=title.replace(/<br [^>]*>/g," ");jQuery(this).attr("title",title);});}
if(jQuery('aside.sidebar_left').length>0){sticky_navigation_offset_top=jQuery('aside.sidebar_left').offset().top;}}),jQuery(window).load(function(){if((jQuery(".pdfprnt-buttons").next('.av_three_fourth').length>0)){jQuery('.pdfprnt-buttons').addClass("av_three_fourth");}
jQuery(".youtube").on("click",function(e){e.preventDefault(),e.stopPropagation(),console.log("clicked")}),jQuery("#qns-bot .wc-header").on("click",function(){jQuery("#chat-container").css("height","0"),jQuery("#qns-bot").addClass("chat_hidden"),jQuery("#chat-initiator").removeClass("chat_hidden"),""==getCookie("chat-hide")&&(setCookie("chat-hide","chat-hide",1),clearTimeout(chatCookie))}),jQuery("body #header #header_meta .sub_menu > ul > li:first-child > a").attr("target","_blank"),columnConform(jQuery(".timeline .timeline-main .av_one_fourth > article > div")),console.log("Window load called..."),knowledge_center_video_icon(),jQuery(".owl-prev").html('<span class="av-icon-char" style="font-size:40px;line-height:40px;" aria-hidden="true" data-av_icon="" data-av_iconfont="entypo-fontello"></span><span class="av-icon-char" style="font-size:40px;line-height:40px;" aria-hidden="true" data-av_icon="" data-av_iconfont="entypo-fontello"></span>'),jQuery(".owl-next").html('<span class="av-icon-char" style="font-size:40px;line-height:40px;" aria-hidden="true" data-av_icon="" data-av_iconfont="entypo-fontello"></span><span class="av-icon-char" style="font-size:40px;line-height:40px;" aria-hidden="true" data-av_icon="" data-av_iconfont="entypo-fontello"></span>'),jQuery("body .timeline .timeline-main .av_one_fourth > article,#home-knowledge-center .av_one_fourth > article").each(function(){jQuery(this).find(".iconbox_content").append("<div class='triangle'></div>")}),jQuery(".main_menu li.menu-item-mega-parent > a .avia-menu-text label").append('<span class="av-icon-char custom-search" aria-hidden="true" data-av_icon="" data-av_iconfont="entypo-fontello"></span>'),jQuery(".knowledge-center .widget_nav_menu ul li.menu-item-type-custom > a").append('<span class="av-icon-char" aria-hidden="true" data-av_icon="" data-av_iconfont="entypo-fontello"></span>'),jQuery(".knowledge-center .widget_nav_menu ul li.menu-item-type-custom > a").on("click",function(){jQuery(".knowledge-center .widget_nav_menu ul li.menu-item-type-custom > .sub-menu").slideToggle()}),jQuery(window).scroll(function(){}),jQuery("ul.tt-filter > li").on("click",function(){jQuery("ul.tt-filter > li").removeClass("active"),jQuery(this).addClass("active").focus();var e=jQuery(this).attr("data-filter");jQuery(".iconbox").removeClass("filter-inactive"),"all"!=e&&jQuery(".iconbox").not("."+e).addClass("filter-inactive")}),jQuery(".ls-wp-container h1, .av_icon_caption,.iconbox_content_container > ul > li,.helpful-heading > div,.helpful-controls > div,.helpful-exists > div,.avia_textblock p,.avia_textblock h3,.avia_textblock h1,.avia_textblock h2").each(function(){jQuery(this).attr("tabindex","0")}),jQuery("body").hasClass("es")?jQuery(".av-burger-menu-main > a").attr("aria-label","Main Menu"):jQuery(".av-burger-menu-main > a").attr("aria-label","Menú principal"),jQuery("[role=listbox]").on("focus",function(){jQuery(this).find("[aria-selected=true]").length?jQuery(this).find("[aria-selected=true]").focus():jQuery(this).find("[role=option]:first").attr("aria-selected","true").focus()}),jQuery("[role=listbox]").on("keydown",function(e){var t=jQuery(this).find("[aria-selected=true]");switch(e.keyCode){case 37:t.prev().length&&(t.attr("aria-selected","false"),t.prev().attr("aria-selected","true").focus()),e.preventDefault();break;case 39:t.next().length&&(t.attr("aria-selected","false"),t.next().attr("aria-selected","true").focus()),e.preventDefault()}}),jQuery("[role=option]").on("mousedown",function(e){jQuery(this).parent().find("[aria-selected=true]").attr("aria-selected","false"),jQuery(this).attr("aria-selected","true"),e.preventDefault()}),comman(),knowledge_center_video_icon(),jQuery.ajax(ajaxurl,{type:'POST',data:{action:'set_post_view',id:post_id},dataType:'json',complete:function(response){},success:function(response){if(response.success){console.log(response);}else{console.log(response);}},error:function(errorThrown){console.log(errorThrown);},});if(jQuery("body").hasClass('es')){jQuery("#qns-bot .wc-header span").html("Chatear");}}),jQuery(window).on("resize",function(){knowledge_center_video_icon(),columnConform(jQuery(".timeline .timeline-main .av_one_fourth > article > div"))})}catch(e){console.log(e)}