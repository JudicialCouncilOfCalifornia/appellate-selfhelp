<html>
<head>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script>
    var options = {path:"https://judca-stage1.adobemsbasic.com/content/forms/af/forms/app-002-notice-of-appeal.html", dataRef:"", themepath:"", CSS_Selector:".aem-form"};//http://localhost/wordpress/wp-content/uploads/jbe_global_xmls/prefill-sample1.xml
    var data = { "dataRef": options.dataRef,wcmmode : "disabled" };//wcmmode : "disabled", 
</script>
</head>
<body>
<div class="aem-form">
    <p>Please wait for form loading...</p> 
</div>	
<script>  	
(function($) {
	var loadAdaptiveForm = function(options){   
    if(options.path) {
        var path = options.path;
        path += "";
        $.ajax({
            url  : path ,
			crossdomain: true,
            type : "GET",
            data : data,
            async: false,
            success: function (form) {			
				
				form = form.replace(/\/content\//gi,"https://judca-stage1.adobemsbasic.com/content/");
				form = form.replace(/\/libs\//gi,"https://judca-stage1.adobemsbasic.com/libs/");
				form = form.replace(/\/etc\//gi,"https://judca-stage1.adobemsbasic.com/etc/");
				form = form.replace(/\/etc.clientlibs\//gi,"https://judca-stage1.adobemsbasic.com/etc.clientlibs/");
                if(window.$ && options.CSS_Selector){
                    $(options.CSS_Selector).html(form);
                }
            },
            error: function (form) {
                // any error handler
            }
        });
    } else {
        if (typeof(console) !== "undefined") {
            console.log("Path of Adaptive Form not specified to loadAdaptiveForm");
        }
    }
    }(options);			
})(jQuery);    
</script>
</body>
</html>
