function userFormTabs(evt, tab) {
	var i, tabcontent, tablinks;
	tabcontent = document.getElementsByClassName("tabcontent");
	for (i = 0; i < tabcontent.length; i++) {
			tabcontent[i].style.display = "none";
	}
	tablinks = document.getElementsByClassName("tablinks");
	for (i = 0; i < tablinks.length; i++) {
		tablinks[i].className = tablinks[i].className.replace(" active", "");
	}
	document.getElementById(tab).style.display = "block";
	evt.currentTarget.className += " active";
}
function formSearch(inputId,ulId) {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById(inputId);
    filter = input.value.toUpperCase();
    ul = document.getElementById(ulId);
    li = ul.getElementsByTagName("li");
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}
function formsSearch(inputId, id) {
    var input, filter, parent, child, a, i, txtValue;
    input = document.getElementById(inputId);
    filter = input.value.toUpperCase();
    parent = document.getElementById(id);
    child = parent.getElementsByTagName("div");
    for (i = 0; i < child.length; i++) {
        a = child[i].getElementsByTagName("a")[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            child[i].style.display = "";
        } else {
            child[i].style.display = "none";
        }
    }
}
jQuery(function($) {
$(document).ready(function(){	
	$('.magnific-popup').magnificPopup({
        type: 'iframe',
        mainClass: 'mfp-fade',
        preloader: true,
    });
	var w = $(".intro .magnific-popup img").width();
	var h = $(".intro .magnific-popup img").height();
	$(".intro .magnific-popup .overlay-icon").css({ width:w+'px',height:h+'px'});
	$(".showAddcase").on("click", function(){
		$(".prepare-doc-intro,.prepare-doc-case-details").hide(10, function(){
			$(".prepare-doc-case").fadeIn(600);
		});
	});
	$(".add-case a").on("click", function(e){
		e.preventDefault();
		$(".prepare-doc-case").fadeOut(10, function(){
			$(".prepare-doc-case-details").fadeIn(600);
		});
	});
	/*function validateForm(){
		var cname = document.forms["addCase"]["caseName"].value;
		if (cname == "") {
			alert("Case Name must be filled out");
			return false;
		  }
	}*/
    $(".case-options .edit").on("click", function(){
        $(".case-details-container").fadeIn(600);
    });
    $(".prepare-doc-forms .cancel").on("click", function(){
        $(".case-details-container").fadeOut(400);
    });
	$('#formcase').submit(function(e) {	
		var cname = $('#case-name').val();
		var utype = $('input[name="userType"]:checked');
		console.log("calling");
		$(".error").remove(); 
		if (cname.length < 1) {
			$('#case-name').after('<span class="error">This field is required</span>');
			return false;
		}else if(utype.length < 1){
			$('.button-container').before('<span class="error">This field is required</span>');
			return false;
		}
		return true;
	});	
	// Get the element with id="defaultOpen" and click on it
	if(document.getElementById("defaultOpen"))document.getElementById("defaultOpen").click();
	if($(".saveGuideDraft").length > 0){
		$(".saveGuideDraft > .guideButton").removeAttr("data-disabled");
		$(".saveGuideDraft button.save").removeAttr("disabled").removeAttr("aria-disabled");
	}
    $('.form-delete').on("click", function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        var userdataID = $(this).attr("userdataID");
        var id = $(this).attr("id");
        var data = {"action":"delete", "id":id, "userdataID":userdataID};
        var that = $(this);
        $.ajax({
            type:'POST',
            url:'https://selfhelp.appellate.courts.ca.gov/wp-content/plugins/jbe-prepare-doc/includes/jbe-case-forms-functions.php',
            data:data,
            success: function(result){
                console.log(result);
                 if(result=="YES"){
                     $(that).parent().parent().remove();
                 }
            }
        });    
    });
    $('.save').on("click",function(){
        var interval = setInterval(function(){
            if( $("#saveAF_message_box").is(":visible") ){                
                clearInterval(interval);
                var data = {"action":"draft"};
                $.ajax({
                    type:'POST',
                    url:'https://selfhelp.appellate.courts.ca.gov/wp-content/plugins/jbe-prepare-doc/includes/jbe-case-forms-functions.php',
                    data:data,
                    success: function(result){
                         if(result=="YES"){
                             console.log("Draft Saved.");
                         }
                    }
                });
            }
        }, 100);
    });
	$('select[name=aem-form]').on('change', function() {
		//$("#addForm").attr("href",this.value);
		$(".form-error").hide();
	});
	$("#addForm").on("click", function(){
		var href = $('select[name=aem-form]').val();
		if(href == "javascript:void(0)"){
			$(".form-error").show();
			return;
		}
		//location.href = href;
		window.open( href, '_blank' );
	});

});
});