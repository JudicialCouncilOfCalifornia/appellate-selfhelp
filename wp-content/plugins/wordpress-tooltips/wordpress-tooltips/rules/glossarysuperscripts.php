<?php
if (!defined('ABSPATH'))
{
	exit;
}


function glossarysuperscriptsfree()
{
	$selectsignificantdigitalsuperscripts = get_option('selectsignificantdigitalsuperscripts');
	if ('no' == strtolower($selectsignificantdigitalsuperscripts))
	{
		?>
<script type="text/javascript">
jQuery("document").ready(function()
		{
			jQuery('.tooltiplist_count').css('vertical-align','top');
		});
</script>
<?php 		
	}
	$hidecountnumberitem = get_option("hidecountnumberitem");
	if ('yes' == strtolower($hidecountnumberitem))
	{
	    ?>
	<script type="text/javascript">
	jQuery("document").ready(function()
			{
				jQuery('.tooltiplist_count').css('display','none'); 
			});
	</script>
	<?php 		
	}
}
add_action('wp_footer','glossarysuperscriptsfree');


