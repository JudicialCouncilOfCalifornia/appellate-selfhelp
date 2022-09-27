<?php
if (! defined ( 'WPINC' )) {
	exit ( 'Please do not access our files directly.' );
}


function glossarysettingsfree()
{
	?>
<div class="wrap tooltipsaddonclass">
<div id="icon-options-general" class="icon32"><br></div>
<h2>Glossary Settings</h2>
</div>
<?php
if (isset($_POST['toolstipsCustomizedsubmit']))
{
    //!!! 7.7.7
    $glossaryIndexPageTermFontSize = sanitize_text_field($_POST['glossaryIndexPageTermFontSize']);
    $glossaryIndexPageTermFontSize = str_ireplace("px","",$glossaryIndexPageTermFontSize);
    // end 7.7.7
    
    
	if (isset($_POST['tooltipsGlossaryIndexPage']))
	{
	    //7.8.7
	    $filtertooltipsGlossaryIndexPage = sanitize_text_field($_POST['tooltipsGlossaryIndexPage']);
	    update_option("tooltipsGlossaryIndexPage",$filtertooltipsGlossaryIndexPage);
		// update_option("tooltipsGlossaryIndexPage",$_POST['tooltipsGlossaryIndexPage']);
		flush_rewrite_rules();
	}

	if (isset($_POST['enabGlossaryIndexPage']))
	{
	    //7.8.7
	    $filterenabGlossaryIndexPage = sanitize_text_field($_POST['enabGlossaryIndexPage']);
	    update_option("enabGlossaryIndexPage",$filterenabGlossaryIndexPage);
		// update_option("enabGlossaryIndexPage",$_POST['enabGlossaryIndexPage']);
	}

	if (isset($_POST['enableLanguageForGlossary']))
	{
	    //7.8.7
	    $filterenableLanguageForGlossary = sanitize_text_field($_POST['enableLanguageForGlossary']);
	    update_option("enableLanguageForGlossary",$filterenableLanguageForGlossary);
		// update_option("enableLanguageForGlossary",$_POST['enableLanguageForGlossary']);
	}


	if (isset($_POST['selectsignificantdigitalsuperscripts']))
	{
	    //7.8.7 
	    $filterselectsignificantdigitalsuperscripts = sanitize_text_field($_POST['selectsignificantdigitalsuperscripts']);
	    update_option('selectsignificantdigitalsuperscripts',$filterselectsignificantdigitalsuperscripts);
	    
		// update_option('selectsignificantdigitalsuperscripts',$_POST['selectsignificantdigitalsuperscripts']);
	}


	if (isset($_POST['showImageinglossary']))
	{
	    //7.8.7
	    $filtershowImageinglossary = sanitize_text_field($_POST['showImageinglossary']);
	    update_option("showImageinglossary",$filtershowImageinglossary);
		// update_option("showImageinglossary",$_POST['showImageinglossary']);
	}
	$showImageinglossary = get_option("showImageinglossary");
	if (empty($showImageinglossary)) $showImageinglossary = 'YES';	

	if (isset($_POST['enableTooltipsForGlossaryPage']))
	{
		$enableTooltipsForGlossaryPage = sanitize_text_field($_POST['enableTooltipsForGlossaryPage']);
		update_option("enableTooltipsForGlossaryPage",$enableTooltipsForGlossaryPage);
	}
	
	if (isset($_POST['enableGlossarySearchable']))
	{
		$enableGlossarySearchable = sanitize_text_field($_POST['enableGlossarySearchable']);
		//7.8.7
		update_option("enableGlossarySearchable",$enableGlossarySearchable);
		// update_option("enableGlossarySearchable",$_POST['enableGlossarySearchable']);
	}
	
	//7.3.1
	if (isset($_POST['glossaryNumbersOrNot']))
	{
		//update_option("glossaryNumbersOrNot",$_POST['glossaryNumbersOrNot']);
		$glossaryNumbersOrNot = sanitize_text_field($_POST['glossaryNumbersOrNot']);
		update_option("glossaryNumbersOrNot",$glossaryNumbersOrNot);
	}	
	
	//7.3.9
	if (isset($_POST['glossaryExcerptOrContentSelect']))
	{
		//update_option("glossaryExcerptOrContentSelect",$_POST['glossaryExcerptOrContentSelect']);
		$glossaryExcerptOrContentSelect = sanitize_text_field($_POST['glossaryExcerptOrContentSelect']);
		update_option("glossaryExcerptOrContentSelect",$glossaryExcerptOrContentSelect);
	}
	
	//!!!start 7.5.1
	if (isset($_POST['bulkremovetermfromglossarylist']))
	{
		$bulkremovetermfromglossarylist = sanitize_text_field($_POST['bulkremovetermfromglossarylist']);
		update_option("bulkremovetermfromglossarylist",$bulkremovetermfromglossarylist);
	}
	$bulkremovetermfromglossarylist = get_option("bulkremovetermfromglossarylist");
	//!!!end 7.5.1

	//!!!7.6.5
	if (isset($_POST['glossaryNavItemFontSize']))
	{
		$glossaryNavItemFontSize = sanitize_text_field($_POST['glossaryNavItemFontSize']);
		$glossaryNavItemFontSize = str_ireplace("px","",$glossaryNavItemFontSize);
		if (!(empty($glossaryNavItemFontSize)))
		{
			update_option("glossaryNavItemFontSize",$glossaryNavItemFontSize);
		}		
	}
	//!!!7.6.5
	//!!!7.8.1
	if (isset($_POST['hidecountnumberitem']))
	{
	    $hidecountnumberitem = sanitize_text_field($_POST['hidecountnumberitem']);
	    update_option("hidecountnumberitem", $hidecountnumberitem);
	}
	//end 7.8.1
	//!!! 7.7.7
	if (!(empty($glossaryIndexPageTermFontSize)))
	{
	    update_option("glossaryIndexPageTermFontSize",$glossaryIndexPageTermFontSize);
	}
	// end 7.7.7
	
	$tooltipsMessageProString =  __( 'Changes saved', 'wordpress-tooltips' );
	tooltipsMessage($tooltipsMessageProString);
}
	$enabGlossaryIndexPage =  get_option("enabGlossaryIndexPage");
	$tooltipsGlossaryIndexPage = get_option('tooltipsGlossaryIndexPage');

	$enableLanguageForGlossary = get_option("enableLanguageForGlossary");
	$enableTooltipsForGlossaryPage = get_option("enableTooltipsForGlossaryPage");
	$showImageinglossary = get_option("showImageinglossary");
	$enableGlossarySearchable =	get_option("enableGlossarySearchable");
	$glossaryExcerptOrContentSelect = get_option("glossaryExcerptOrContentSelect");
	
	if (empty($enableGlossarySearchable)) $enableGlossarySearchable = 'yes';
	$bulkremovetermfromglossarylist = get_option('bulkremovetermfromglossarylist'); // !!! 7.5.1

	$glossaryNavItemFontSize = get_option("glossaryNavItemFontSize"); //7.7.7
	$glossaryIndexPageTermFontSize = get_option("glossaryIndexPageTermFontSize");  //7.7.7
	
	// 7.3.1
	$glossaryNumbersOrNot =  get_option("glossaryNumbersOrNot");
	$hidecountnumberitem = get_option("hidecountnumberitem"); // !!! 7.8.1
?>
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:90%;">
								<div class="postbox">
									<div class="inside" style='padding-left:5px;'>
										<form class="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%">
										<tr style="text-align:left;">
										<td style='width:25%'>
										<?php
											echo __( 'Enable Glossary Index Page: ', 'wordpress-tooltips' ).'<span class="questionindexforglossary">?</span>';
										?>
										<?php
										$admin_tip = __('By default, we will show glossary index page and each glossary term have links, you will find glossary index page at http://yourdomain.com/glossary, but you can disable this glossary index page and hidden links for each glossary terms by select "Disable glossary index page"', "wordpress-tooltips");
										?>
										<script type="text/javascript"> 
										jQuery(document).ready(function () {
										  jQuery("span.questionindexforglossary").hover(function () {
										    jQuery(this).append('<div class="glossary22"><p><?php echo $admin_tip; ?></p></div>');
										  }, function () {
										    jQuery("div.glossary22").remove();
										  });
										});
										</script>
										</td>
										<td style='width:25%'>
										<select id="enabGlossaryIndexPage" name="enabGlossaryIndexPage" style="width:98%;">
										<option id="enabGlossaryIndexPageOption" value="YES"  <?php if ($enabGlossaryIndexPage == 'YES') echo "selected";   ?>> <?php echo __('Enable Glossary Index Page', "wordpress-tooltips") ?> </option>
										<option id="enabGlossaryIndexPageOption" value="NO" <?php if ($enabGlossaryIndexPage == 'NO') echo "selected";   ?>>  <?php echo __('Disable Glossary Index Page', "wordpress-tooltips") ?> </option>
										</select>
										</td>
										
										<td style='width:25%'>
										<?php
											echo __( "Glossary Index Page: ", 'wordpress-tooltips' ).'<span class="questionglossaryindexpageselect">?</span>';
											$admin_tip = __('Select your glossary index page', "wordpress-tooltips");
										?>
										<script type="text/javascript"> 
										jQuery(document).ready(function () {
										  jQuery("span.questionglossaryindexpageselect").hover(function () {
										    jQuery(this).append('<div class="glossary24"><p><?php echo $admin_tip; ?></p></div>');
										  }, function () {
										    jQuery("div.glossary24").remove();
										  });
										});
										</script>										
										</td>
										<td style='width:25%'>
										<?php 
											echo wp_dropdown_pages( array('name' => 'tooltipsGlossaryIndexPage', 'echo' => false, 'selected' => !empty( $tooltipsGlossaryIndexPage ) ? $tooltipsGlossaryIndexPage : false)); 
										?>										
										</td>										
										</tr>

										<tr style="text-align:left;">
										<td style='width:25%'>
										<?php
											$languageAddonUrl = get_option('siteurl').'/wp-admin/edit.php?post_type=tooltips&page=tooltipsFreeLanguageMenu';
											echo __( 'Language: ', 'wordpress-tooltips' ).'<span class="questionlanguageforglossary">?</span>'." <a href='$languageAddonUrl' target='_blank' >enable custom language</a>";
										?>
										<?php
										$admin_tip = __('By default, language for glossary is  English, you can choose your language from here, or you can choose "Custom My Language" option to generate your glossary language by yourself ', "wordpress-tooltips");
										
										$admin_tip_enable_language_addon = __(' -- please remember to <a style="color:yellow !important;" href="', "wordpress-tooltips") . $languageAddonUrl.__('" target="_blank" >enable custom language first</a> ', "wordpress-tooltips");
										$admin_tip = $admin_tip.$admin_tip_enable_language_addon;
										?>
										<script type="text/javascript"> 
										jQuery(document).ready(function () {
										  jQuery("span.questionlanguageforglossary").hover(function () {
										    jQuery(this).append('<div class="glossary22"><p><?php echo $admin_tip; ?></p></div>');
										  }, function () {
										    jQuery("div.glossary22").remove();
										  });
										});
										</script>
										</td>
										<td style='width:25%'>
										<select id="enableLanguageForGlossary" name="enableLanguageForGlossary" style="width:98%;">
										<option id="enableLanguageForGlossaryOption" value="en" <?php if ($enableLanguageForGlossary == 'en') echo "selected";   ?>>  <?php echo __('English', "wordpress-tooltips") ?> </option>
										<option id="enableLanguageForGlossaryOption" value="es" <?php if ($enableLanguageForGlossary == 'es') echo "selected";   ?>>  <?php echo __('Spanish', "wordpress-tooltips") ?> </option>
										<option id="enableLanguageForGlossaryOption" value="fi" <?php if ($enableLanguageForGlossary == 'fi') echo "selected";   ?>>  <?php echo __('Finnish', "wordpress-tooltips") ?> </option>
										<option id="enableLanguageForGlossaryOption" value="sv"  <?php if ($enableLanguageForGlossary == 'sv') echo "selected";   ?>> <?php echo __('Swedish', "wordpress-tooltips") ?> </option>
										<option id="enableLanguageForGlossaryOption" value="de" <?php if ($enableLanguageForGlossary == 'de') echo "selected";   ?>>  <?php echo __('German', "wordpress-tooltips") ?> </option>
										<option id="enableLanguageForGlossaryOption" value="fr" <?php if ($enableLanguageForGlossary == 'fr') echo "selected";   ?>>  <?php echo __('French', "wordpress-tooltips") ?> </option>
										<option id="enableLanguageForGlossaryOption" value="custom" <?php if ($enableLanguageForGlossary == 'custom') echo "selected";   ?>>  <?php echo __('Custom My Language', "wordpress-tooltips") ?> </option>
										</select>
										</td>

										<td style='width:25%'>
										<?php
											echo __( 'Significant Display of Digital Superscripts on Navigation Bar: ', 'wordpress-tooltips' ).'<span class="removenavbaringlossarypage">?</span>';
											$selectsignificantdigitalsuperscripts = get_option('selectsignificantdigitalsuperscripts');
										?>
										<?php
											$admin_tip = __('By default, we will significant display of digital superscripts on navigation bar, also you can select to dislay normal mode too.', "wordpress-tooltips");
										?>
										<script type="text/javascript"> 
										jQuery(document).ready(function () {
										  jQuery("span.removenavbaringlossarypage").hover(function () {
										    jQuery(this).append('<div class="glossaryremovenavbaringlossarypage"><p><?php echo $admin_tip; ?></p></div>');
										  }, function () {
										    jQuery("div.glossaryremovenavbaringlossarypage").remove();
										  });
										});
										</script>
										</td>
										<td style='width:25%'>
										<select id="selectsignificantdigitalsuperscripts" name="selectsignificantdigitalsuperscripts" style="width:98%;">
										<option id="optionsignificantdigitalsuperscripts" value="yes"  <?php if ($selectsignificantdigitalsuperscripts == 'yes') echo "selected";   ?>> <?php echo __('YES', "wordpress-tooltips") ?> </option>
										<option id="optionsignificantdigitalsuperscripts" value="no" <?php if ($selectsignificantdigitalsuperscripts == 'no') echo "selected";   ?>>  <?php echo __('NO', "wordpress-tooltips") ?> </option>
										</select>
										</td>
										</tr>

										<tr style="text-align:left;">
										<td style='width:25%'>
										<?php 
											$addtipto = 'span.hiddenimageinglossary';
											$questiontip = '<div class="tooltip"><p>"Hide Image in Glossary List Page" option will not show images in glossary page</p><p>"Display Image in Glossary List Page" option will show images in glossary page</p></div>';
											$tipadsorbent = '.questionsinglecat';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltip',$tipadsorbent);
											echo $adminTip;
											echo __( 'Hide Image in Glossary List: ', 'wordpress-tooltips' ).'<span class="hiddenimageinglossary">?</span>';
										
										?>	
										</td>
										<td style='width:25%'>
										<select id="showImageinglossary" name="showImageinglossary">
										<option id="showImageinglossaryOption" value="YES" <?php if ($showImageinglossary == 'YES') echo "selected";   ?>> Display Image in Glossary List Page </option>
										<option id="showImageinglossaryOption" value="NO" <?php if ($showImageinglossary == 'NO') echo "selected";   ?>>   Hide Image in Glossary List Page </option>
										</select>										
										</td>
										
										<td style='width:25%'>
										<?php 
											$addtipto = 'span.questiondisabletooltipsforglossary';
											$questiontip = '<div class="glossarydisabletooltipsforglossarydiv"><p>By default, in glossary index page and glossary term pages, our plugin will show tooltips effect on glossary page, you can disable tooltips effect on glossary pages from here.</p></div>';
											$tipadsorbent = '.glossarydisabletooltipsforglossary';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.glossarydisabletooltipsforglossarydiv',$tipadsorbent);
											echo $adminTip;
											echo __( 'Disable Tooltips in Glossary Page: ', 'wordpress-tooltips' ).'<span class="questiondisabletooltipsforglossary">?</span>';
										?>
										</td>
										
										<td style='width:25%'>
										<select id="enableTooltipsForGlossaryPage" name="enableTooltipsForGlossaryPage" style="width:98%;">
										<option id="enableTooltipsForGlossaryPageOption" value="YES"  <?php if ($enableTooltipsForGlossaryPage == 'YES') echo "selected";   ?>> <?php echo __('Enable Tooltips in Glossary Pages', "wordpress-tooltips") ?> </option>
										<option id="enableTooltipsForGlossaryPageOption" value="NO" <?php if ($enableTooltipsForGlossaryPage == 'NO') echo "selected";   ?>>  <?php echo __('Disable Tooltips in Glossary Pages', "wordpress-tooltips") ?> </option>
										</select>										
										</td>										
										</tr>
<?php // 7.2.1 ?>
										<tr style="text-align:left;">
										<td style='width:25%'>
										<?php
										$addtipto = 'span.spanquestionglossarysearchable';
										$questiontip = '<div class="glossarysearchablediv"><p>Before use this feature, you must enable "Enable Glossary Index Page" option first. <br /> If you disable glossary searchable, glossary terms / tooltip terms will not shown in wordpress standard search result. <br /> If you enable glossary searchable,glossary terms / tooltip terms will shown in wordpress standard search result.  <br /> By default, we enabled glossary searchable.</p></div>';
										$tipadsorbent = '.glossarysearchablediv';
										$adminTip = showAdminTip($addtipto,$questiontip,'div.glossarysearchablediv',$tipadsorbent);
										echo $adminTip;
										
											echo __( 'Enable Glossary Searchable: ', 'wordpress-tooltips' ).'<span class="spanquestionglossarysearchable">?</span>';
										?>
										</td>
										<td style='width:25%'>
										<select id="enableGlossarySearchable" name="enableGlossarySearchable" style="width:98%;">
										<option id="enableGlossarySearchableOption" value="yes"  <?php if ($enableGlossarySearchable == 'yes') echo "selected";   ?>> <?php echo __('YES', "wordpress-tooltips") ?> </option>
										<option id="enableGlossarySearchableOption" value="no" <?php if ($enableGlossarySearchable == 'no') echo "selected";   ?>>  <?php echo __('NO', "wordpress-tooltips") ?> </option>
										</select>
										</td>
<?php  // end 7.2.1 ?>										
<?php // start 7.3.1 ?>
										<td style='width:25%'>
										<?php
											$addtipto = 'span.questionglossarynumbersornot';
											$questiontip = '<div class="glossary23"><p>Show numbers (1,2...9) in nav bar or not</p></div>';
											$tipadsorbent = '.glossary23';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.glossary23',$tipadsorbent);
											echo $adminTip;
											
											echo __( 'Show Numbers in Nav bar: ', 'wordpress-tooltips' ).'<span class="questionglossarynumbersornot">?</span>';
											
										?>
										</td>
										<td style='width:25%'>
										<select id="glossaryNumbersOrNot" name="glossaryNumbersOrNot">
										<option id="glossaryNumbersOrNotOption" value="YES"  <?php if ($glossaryNumbersOrNot == 'YES') echo "selected";   ?>> <?php echo __('Show Numbers In Glossary Nav Bar', "wordpress-tooltips") ?> </option>
										<option id="glossaryNumbersOrNotOption" value="NO" <?php if ($glossaryNumbersOrNot == 'NO') echo "selected";   ?>>  <?php echo __('Remove Numbers In Glossary Nav Bar', "wordpress-tooltips") ?> </option>
										</select>										
										</td>
<?php // end 7.3.1 ?>
										</tr>
<?php //7.3.9 ?>
									<tr style="text-align:left;">
										<td style='width:25%'>
										<?php 
											$addtipto = 'span.questionglossaryexcerptorcontent';
$questiontip = '<div class="glossaryexcerptorcontent"><p>Show excerpt of the glossary content or full content of glossary in glossary index page. If you chose to show glossary excerpt, In general, wordpress will show a "Continue reading" link in the excerpt, which link to the glossary page of this tooltip, if your theme or 3rd plugin disabled this link,  it is better to enable "Add tooltips links in tooltip popup box" option in "Optinal Settings" menu.</p></div>';
											$tipadsorbent = '.questionglossaryexcerptorcontent';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.glossaryexcerptorcontent',$tipadsorbent);
											echo $adminTip;
											echo __( 'Glossary Excerpt or Full Content:', 'wordpress-tooltips' ).' <span class="questionglossaryexcerptorcontent">?</span>';
										?>							
										</td>

										<td style='width:25%'>
										<select id="glossaryExcerptOrContentSelect" name="glossaryExcerptOrContentSelect">
										<option id="glossaryExcerptOrContentOption" value="glossaryexcerpt" <?php if ($glossaryExcerptOrContentSelect == 'glossaryexcerpt') echo "selected";   ?>> Use Glossary Excerpt in Glossary Pages</option>
										<option id="glossaryExcerptOrContentOption" value="glossarycontent" <?php if ($glossaryExcerptOrContentSelect == 'glossarycontent') echo "selected";   ?>> Use Full Glossary Content in Glossary Pages</option>
										</select>										
										</td>
										
										<td style='width:25%'>
										<?php
											$bulkremovetermfromglossarylist = get_option('bulkremovetermfromglossarylist');
											
											$addtipto = 'span.questionbulkremovetermfromglossarylist';
											$admintipcontent = __('By default, we will show all terms in glossary directory, you can opt to remove some glossary terms by glossary id from glossary directory pages.', "wordpress-tooltips");
											$questiontip = '<div class="glossarybulkremovetermfromglossarylist"><p>'. $admintipcontent .'</p></div>';
											$tipadsorbent = '.glossarybulkremovetermfromglossarylist';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.glossarybulkremovetermfromglossarylist',$tipadsorbent);
											echo $adminTip;
											
											echo __( "Bulk remove terms from glossary directory: ", 'wordpress-tooltips' ).'<span class="questionbulkremovetermfromglossarylist">?</span>';
											
										?>										
										</td>

										<td style='width:25%'>
										<?php 
										/*
										 * 7.9.3
										 * <input type="text" id="bulkremovetermfromglossarylist" name="bulkremovetermfromglossarylist" value="<?php echo $bulkremovetermfromglossarylist;  ?>" placeholder="<?php echo __('for example: 3,22,58,126,583', "wordpress-tooltips");; ?>">
										 */
										?>
										<input type="text" id="bulkremovetermfromglossarylist" name="bulkremovetermfromglossarylist" value="<?php echo esc_attr($bulkremovetermfromglossarylist);  ?>" placeholder="<?php echo __('for example: 3,22,58,126,583', "wordpress-tooltips");; ?>">
										</td>
										
									</tr>
<?php //end 7.3.9 ?>
<?php //!!! start 7.6.5 ?>
										<tr>
										<td style='width:25%'>
										<?php
											echo __( 'Nav Bar font size, all items: ', 'wordpress-tooltips' ).'<span class="questionfontsize">?</span>';
										?>
										<?php
										$admin_tip = __('Font size of glossary letters in navigation bar,  Required field, for example: 14', "wordpress-tooltips");
										?>
										<script type="text/javascript"> 
										jQuery(document).ready(function () {
										  jQuery("span.questionfontsize").hover(function () {
										    jQuery(this).append('<div class="glossary28"><p><?php echo $admin_tip; ?></p></div>');
										  }, function () {
										    jQuery("div.glossary28").remove();
										  });
										});
										</script>
										</td>
										<td style='width:25%'>
										<?php 
										/*
										 * 7.9.3
										 * <input type="text" id="glossaryNavItemFontSize" name="glossaryNavItemFontSize" value="<?php echo $glossaryNavItemFontSize;  ?>" required placeholder="<?php echo __( 'for example:14', 'wordpress-tooltips' ) ?>">
										 */
										?>
										<input type="text" id="glossaryNavItemFontSize" name="glossaryNavItemFontSize" value="<?php echo esc_attr($glossaryNavItemFontSize);  ?>" required placeholder="<?php echo __( 'for example:14', 'wordpress-tooltips' ) ?>">
										</td>
										
<?php //7.7.7 ?>
										<td style='width:25%'>
										<?php
											echo __( 'Glossary Term Font Size: ', 'wordpress-tooltips' ).'<span class="questionglossaryfontsize">?</span>';
										?>
										<?php
										$admin_tip = __('Required field, for example: 14', "wordpress-tooltips");
										?>
										<script type="text/javascript"> 
										jQuery(document).ready(function () {
										  jQuery("span.questionglossaryfontsize").hover(function () {
										    jQuery(this).append('<div class="divquestionglossaryfontsize"><p><?php echo $admin_tip; ?></p></div>');
										  }, function () {
										    jQuery("div.divquestionglossaryfontsize").remove();
										  });
										});
										</script>										
										</td>
										<td style='width:25%'>
										<?php 
										/*
										 * 7.9.3
										 * <input type="text" id="glossaryIndexPageTermFontSize" name="glossaryIndexPageTermFontSize" value="<?php echo $glossaryIndexPageTermFontSize;  ?>" required placeholder="<?php echo __( 'for example:14', 'wordpress-tooltips' ) ?>">
										 * 
										 */
										?>
										<input type="text" id="glossaryIndexPageTermFontSize" name="glossaryIndexPageTermFontSize" value="<?php echo esc_attr($glossaryIndexPageTermFontSize);  ?>" required placeholder="<?php echo __( 'for example:14', 'wordpress-tooltips' ) ?>">
										</td>
										</tr>
<?php //!!! end 7.6.5 ?>
<?php //!!! start 7.8.1 ?>
										<tr>
										<td style='width:25%'>
										<?php
											echo __( 'Hide count number of glossary items?: ', 'wordpress-tooltips' ).'<span class="spanhidecountnumberitemforglossary">?</span>';
										?>
										<?php
											$admin_tip = __('By default, we will display count number of each letter items in glossary Nav bar, you can select hide / display count numbers of letters.', "wordpress-tooltips");
										?>
										<script type="text/javascript"> 
										jQuery(document).ready(function () {
										  jQuery("span.spanhidecountnumberitemforglossary").hover(function () {
										    jQuery(this).append('<div class="divhidecountnumberitemforglossary"><p><?php echo $admin_tip; ?></p></div>');
										  }, function () {
										    jQuery("div.divhidecountnumberitemforglossary").remove();
										  });
										});
										</script>
										</td>
										<td style='width:25%'>
										<select id="hidecountnumberitem" name="hidecountnumberitem" style="width:98%;">
										<option id="hidecountnumberitemOption" value="no"  <?php if ($hidecountnumberitem == 'no') echo "selected";   ?>> <?php echo __('NO', "wordpress-tooltips") ?> </option>
										<option id="hidecountnumberitemOption" value="yes" <?php if ($hidecountnumberitem == 'yes') echo "selected";   ?>>  <?php echo __('YES', "wordpress-tooltips") ?> </option>
										</select>
										</td>
										
										<td style='width:25%'>
										</td>
										<td style='width:25%'>
										</td>										
										</tr>
<?php //!!! end 7.8.1 ?>
										</table>
										<br />
										<input type="submit" class="button-primary" id="toolstipsCustomizedsubmit" name="toolstipsCustomizedsubmit" value=" <?php echo __( 'Save Changes', 'wordpress-tooltips' ) ?> ">
										</form>
										
										<br />
									</div>
								</div>
							</div>
						</div>
					</div>
		    	</div>
			</div>
		</div>
		<div style="clear:both"></div>
		<br />
		<a class=""  target="_blank" href="https://paypal.me/sunpayment">
		<span>
		Buy me a coffee 								
		</span>
		</a>
		?
		<span style="margin-right:20px;">
		Thank you :)
		</span>
<?php
}

