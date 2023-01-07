<?php
if (!defined('ABSPATH'))
{
	exit;
}

function tooltipGlobalSettings()
{
	if (isset($_POST['onlyFirstKeywordsetting']))
	{
		if (isset($_POST['onlyFirstKeyword']))
		{
		    update_option("onlyFirstKeyword", sanitize_text_field($_POST['onlyFirstKeyword']));
			//7.9.3 update_option("onlyFirstKeyword",$_POST['onlyFirstKeyword']);
		}
		$tooltipsMessageString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageString);
	}
	
	
	$onlyFirstKeyword = get_option("onlyFirstKeyword");
	
	if (isset($_POST['enableTooltipsForImageSubmit']))
	{
		if (isset($_POST['enableTooltipsForImage']))
		{
		    update_option("enableTooltipsForImage",sanitize_text_field($_POST['enableTooltipsForImage']));
			//7.9.3 update_option("enableTooltipsForImage",$_POST['enableTooltipsForImage']);
		}
		$tooltipsMessageString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageString);		
	}

	$enableTooltipsForImage = get_option("enableTooltipsForImage");

	if (isset($_POST['enableTooltipsForExcerptSubmit']))
	{
		if (isset($_POST['enableTooltipsForExcerpt']))
		{
		    
		    update_option("enableTooltipsForExcerpt",sanitize_text_field($_POST['enableTooltipsForExcerpt']));
			//7.9.3 update_option("enableTooltipsForExcerpt",$_POST['enableTooltipsForExcerpt']);
		}

		$tooltipsMessageString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageString);		
	}
	$enableTooltipsForExcerpt = get_option("enableTooltipsForExcerpt");
	if (empty($enableTooltipsForExcerpt)) $enableTooltipsForExcerpt = 'NO';
	
	
	if (isset($_POST['disableInHomePageSubmit']))
	{
		if (isset($_POST['disableInHomePage']))
		{
		    update_option("disableInHomePage", sanitize_text_field($_POST['disableInHomePage']));
		    //7.9.3 update_option("disableInHomePage",$_POST['disableInHomePage']);
		}
		$tooltipsMessageString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageString);
	}
	$disableInHomePage = get_option("disableInHomePage");
	if (empty($disableInHomePage)) $disableInHomePage = 'YES';
	


	if (isset($_POST['showOnlyInSingleCategorySubmit']))
	{
		if (isset($_POST['cat']))
		{
		    update_option("showOnlyInSingleCategory",sanitize_text_field($_POST['cat']));
			// 7.9.3 update_option("showOnlyInSingleCategory",$_POST['cat']);
		}

		$tooltipsMessageString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageString);
	}
	$showOnlyInSingleCategory = get_option("showOnlyInSingleCategory");
	if (empty($showOnlyInSingleCategory)) $showOnlyInSingleCategory = 0;
	
	if (isset($_POST['showTooltipPopupAnimationSubmit']))
	{
		if (isset($_POST['toolstipsAnimationClass']))
		{
		    update_option("toolstipsAnimationClass", sanitize_text_field($_POST['toolstipsAnimationClass']));
		    //7.9.3 update_option("toolstipsAnimationClass",$_POST['toolstipsAnimationClass']);
		}
		$tooltipsMessageString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageString);
	}
	$toolstipsAnimationClass = get_option("toolstipsAnimationClass");
	if (empty($toolstipsAnimationClass)) $toolstipsAnimationClass = 'tipnoanimation';
	
	if (isset($_POST['enableTooltipsForTagSubmit']))
	{
		if (isset($_POST['enableTooltipsForTag']))
		{
		    update_option("enableTooltipsForTags",sanitize_text_field($_POST['enableTooltipsForTag']));
			//7.9.3  update_option("enableTooltipsForTags",$_POST['enableTooltipsForTag']);
		}
		$tooltipsMessageString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageString);
	}
	$enableTooltipsForTag = get_option("enableTooltipsForTags");
	if (empty($enableTooltipsForTag)) $enableTooltipsForTag = 'NO';
	
	/* moved to glossary setting panel
	if (isset($_POST['showImageinglossarySubmit']))
	{
		if (isset($_POST['showImageinglossary']))
		{
			update_option("showImageinglossary",$_POST['showImageinglossary']);
		}
		$tooltipsMessageString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageString);
	}
	$showImageinglossary = get_option("showImageinglossary");
	if (empty($showImageinglossary)) $showImageinglossary = 'YES';
	*/
	if (isset($_POST['toolstipsstylesetting']))
	{
		if (isset($_POST['selectedTooltipStyle']))
		{
		    update_option("selectedTooltipStyle",sanitize_text_field($_POST['selectedTooltipStyle']));
			// 7.9.3 update_option("selectedTooltipStyle",$_POST['selectedTooltipStyle']);
		}
		$selectedDefaultTooltipStyle = sanitize_text_field($_POST['selectedTooltipStyle']);
		//7.9.3 $selectedDefaultTooltipStyle = $_POST['selectedTooltipStyle'];
		$selectedDefaultTooltipStyle = str_ireplace('qtip-', '', $selectedDefaultTooltipStyle);
	
		$tooltipsMessageString =  __('You have selected ', 'wordpress-tooltips' ).$selectedDefaultTooltipStyle. __(' style, ', 'wordpress-tooltips' ) .__( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageString);
	}
	
	$selectedTooltipStyle = get_option("selectedTooltipStyle");
	if (empty($selectedTooltipStyle))
	{
		$selectedTooltipStyle = 'qtip-dark';
	}
	
	if (isset($_POST['toolstipsclosebuttonsetting']))
	{
		if (isset($_POST['showToolstipsCloseButtonSelect']))
		{
		    update_option("showToolstipsCloseButtonSelect",sanitize_text_field($_POST['showToolstipsCloseButtonSelect']));
			//7.9.3 update_option("showToolstipsCloseButtonSelect",$_POST['showToolstipsCloseButtonSelect']);
		}
		$tooltipsMessageString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageString);
	}
	$showToolstipsCloseButtonSelect = get_option("showToolstipsCloseButtonSelect");
	if (empty($showToolstipsCloseButtonSelect))
	{
		$showToolstipsCloseButtonSelect = 'no';
	}
	
	if (isset($_POST['tooltipZindexValueSetting']))
	{
		if (isset($_POST['tooltipZindexValue']))
		{
		    update_option("tooltipZindexValue",sanitize_text_field($_POST['tooltipZindexValue']));
			//7.9.3 update_option("tooltipZindexValue",$_POST['tooltipZindexValue']);
		}
		$tooltipsMessageString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageString);
	}
	$tooltipZindexValue = get_option("tooltipZindexValue");
	if (empty($tooltipZindexValue))
	{
		$tooltipZindexValue = '15001';
	}
	
	
	
	if (isset($_POST['tooltipHookPriorityValueSetting']))
	{
		if (isset($_POST['tooltipHookPriorityValue']))
		{
		    update_option("tooltipHookPriorityValue",sanitize_text_field($_POST['tooltipHookPriorityValue']));
			// 7.9.3 update_option("tooltipHookPriorityValue",$_POST['tooltipHookPriorityValue']);
		}
		$tooltipsMessageString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageString);
	}
	$tooltipHookPriorityValue = get_option("tooltipHookPriorityValue");
	if (empty($tooltipHookPriorityValue))
	{
		$tooltipHookPriorityValue = '20000';
	}	
	
	if (isset($_POST['disableTooltipInHtmlTagSubmit']))
	{
		if (isset($_POST['disabletooltipinhtmltag']))
		{
		    update_option("disabletooltipinhtmltag",sanitize_text_field($_POST['disabletooltipinhtmltag']));
			//7.9.3 update_option("disabletooltipinhtmltag",$_POST['disabletooltipinhtmltag']);
		}
	
		$tooltipsMessageProString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageProString);
	}
	$disabletooltipinhtmltag = get_option("disabletooltipinhtmltag");
	if (empty($disabletooltipinhtmltag)) $disabletooltipinhtmltag = '';

	if (isset($_POST['enabletooltipsPopupCreditLinkInPopupWindowSetting']))
	{
		if (isset($_POST['enabletooltipsPopupCreditLinkInPopupWindow']))
		{
		    update_option("enabletooltipsPopupCreditLinkInPopupWindow",sanitize_text_field($_POST['enabletooltipsPopupCreditLinkInPopupWindow']));
			//7.9.3 update_option("enabletooltipsPopupCreditLinkInPopupWindow",$_POST['enabletooltipsPopupCreditLinkInPopupWindow']);
			$enabletooltipsPopupCreditLinkInPopupWindow = get_option("enabletooltipsPopupCreditLinkInPopupWindow");
		}
		$tooltipsMessageProString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageProString);
	}
	$enabletooltipsPopupCreditLinkInPopupWindow = get_option("enabletooltipsPopupCreditLinkInPopupWindow");

	if (isset($_POST['selectdisabletooltipentiresite']))
	{
		if (isset($_POST['selectdisabletooltipentiresite']))
		{
		    update_option("disabletooltipentiresite",sanitize_text_field($_POST['selectdisabletooltipentiresite']));
			//7.9.3 update_option("disabletooltipentiresite",$_POST['selectdisabletooltipentiresite']);
		}
		$tooltipsMessageProString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageProString);
	}
	$disabletooltipentiresite = get_option('disabletooltipentiresite');
	
	//!!!start
	// 6.93
	if (isset($_POST['disabletooltipmobileoptionSetting']))
	{
		if (isset($_POST['selectdisabletooltipmobile']))
		{
		    update_option("disabletooltipmobile",sanitize_text_field($_POST['selectdisabletooltipmobile']));
			//7.9.3 update_option("disabletooltipmobile",$_POST['selectdisabletooltipmobile']);
		}
		$tooltipsMessageProString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageProString);
	}
	$disabletooltipmobile = get_option("disabletooltipmobile");
	//!!!end
	
	//!!! 7.6.9
	
	if (isset($_POST['enableMoveInlineJsToFooter']))
	{
		if (isset($_POST['enableMoveInlineJsToFooter']))
		{
		    update_option("enableMoveInlineJsToFooter",sanitize_text_field($_POST['enableMoveInlineJsToFooter']));
			//7.9.3 update_option("enableMoveInlineJsToFooter",$_POST['enableMoveInlineJsToFooter']);
		}
		$tooltipsMessageProString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageProString);
	}
	$disabletooltipmobile = get_option("disabletooltipmobile");	
	//!!! end 7.6.9
	
	//!!! 7.2.5
	if (isset($_POST['enableTooltipsForCategoryTitleSubmit']))
	{
		if (isset($_POST['enableTooltipsForCategoryTitle']))
		{
		    update_option("enableTooltipsForCategoryTitle",sanitize_text_field($_POST['enableTooltipsForCategoryTitle']));
			//7.9.3 update_option("enableTooltipsForCategoryTitle",$_POST['enableTooltipsForCategoryTitle']);
		}
	
		$tooltipsMessageProString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageProString);
	}
	$enableTooltipsForCategoryTitle = get_option("enableTooltipsForCategoryTitle");
	if (empty($enableTooltipsForCategoryTitle)) $enableTooltipsForCategoryTitle = 'NO';
	//!!! end 7.2.5
	
	
	//!!! 7.5.7
	
	if (isset($_POST['toolstipsFontSizeSubmit']))
	{
		if (isset($_POST['toolstipsFontSize']))
		{
		    $toolstipsFontSize = sanitize_text_field($_POST['toolstipsFontSize']);
			//7.9.3 $toolstipsFontSize = $_POST['toolstipsFontSize'];
			$toolstipsFontSize = str_ireplace("px","",$toolstipsFontSize);
			
			update_option("toolstipsFontSize",$toolstipsFontSize);
		}
	
		$tooltipsMessageProString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageProString);
	}
	$toolstipsFontSize = get_option("toolstipsFontSize");	
	//!!! end 7.5.7
	
	//!!!start
	if (isset($_POST['disabletooltipforclassandidsSubmit']))
	{
		if (isset($_POST['disabletooltipforclassandids']))
		{
		    $disabletooltipforclassandids = sanitize_text_field($_POST['disabletooltipforclassandids']);
			//7.9.3 $disabletooltipforclassandids = $_POST['disabletooltipforclassandids'];
			if (!empty(trim($disabletooltipforclassandids)))
			{
			    update_option("disabletooltipforclassandids",sanitize_text_field($_POST['disabletooltipforclassandids']));
				//7.9.3 update_option("disabletooltipforclassandids",$_POST['disabletooltipforclassandids']);
			}
			else
			{
				delete_option("disabletooltipforclassandids");
			}
		}
	
		$tooltipsMessageProString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageProString);
	}
	
	$disabletooltipforclassandids = get_option("disabletooltipforclassandids");
	//!!!end
	//!!! start 7.7.1
	if (isset($_POST['enableDeferTooltipOptionSubmit']))
	{
	    if (isset($_POST['enableDeferTooltip']))
	    {
	        $enableDeferTooltip = sanitize_text_field($_POST['enableDeferTooltip']);
	        update_option("enableDeferTooltip",$enableDeferTooltip);
	        //7.9.3 update_option("enableDeferTooltip",$_POST['enableDeferTooltip']);
	        $enableDeferTooltip = get_option("enableDeferTooltip");
	    }
	    
	    $tooltipsMessageProString =  __( 'Changes saved', 'wordpress-tooltips' );
	    tooltipsMessage($tooltipsMessageProString);
	}
	$enableDeferTooltip = get_option("enableDeferTooltip");
	//!!! end 7.7.1
	
	// 7.7.3
	if (isset($_POST['seletEnableJqueryMigrate']))
	{
	    if (isset($_POST['seletEnableJqueryMigrate']))
	    {
	        update_option("seletEnableJqueryMigrate", sanitize_text_field($_POST['seletEnableJqueryMigrate']));
	        // 7.9.3 update_option("seletEnableJqueryMigrate",$_POST['seletEnableJqueryMigrate']);
	    }
	    $tooltipsMessageProString =  __( 'Changes saved', 'wordpress-tooltips' );
	    tooltipsMessage($tooltipsMessageProString);
	}
	
	$seletEnableJqueryMigrate = get_option('seletEnableJqueryMigrate');
	//end 7.7.3
	
	//!!! start 8.1.1
	if (isset($_POST['disableTooltipandEnableGlossarySubmit']))
	{
	    if (isset($_POST['disableTooltipandEnableGlossary']))
	    {
	        $enableAsyncTooltip = sanitize_text_field($_POST['disableTooltipandEnableGlossary']);
	        update_option("disableTooltipandEnableGlossary",$enableAsyncTooltip);
	        $disableTooltipandEnableGlossary = get_option("disableTooltipandEnableGlossary");
	    }
	    
	    $tooltipsMessageProString =  __( 'Changes saved', 'wordpress-tooltips' );
	    tooltipsMessage($tooltipsMessageProString);
	}
	$disableTooltipandEnableGlossary = get_option("disableTooltipandEnableGlossary");
	//!!! end 8.1.1
?>
<div style='margin:20px 5px 10px 5px;'>
	<div style='float:left;margin-right:10px;'>
		<img src='<?php echo get_option('siteurl');  ?>/wp-content/plugins/wordpress-tooltips/images/new.png' style='width:30px;height:30px;'>
	</div> 
	<div style='padding-top:5px; font-size:22px;'>
		<i>
		<?php
			echo  __( 'Tooltips Global Settings', 'wordpress-tooltips' );
		?>
		</i>
	</div>
	<div style='clear:both'></div>
</div>
<?php if (function_exists('is_rtl'))
{
	if (is_rtl())
	{
		echo '<div class="" style="width:70%;float:right;">';
	}
	else
	{
		echo '<div class="" style="width:70%;float:left;">';
	}
}
else 
{
	echo '<div class="" style="width:70%;float:left;">';
}
?>
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
										<?php 
										echo __( 'Tooltip Keyword Matching Mode', 'wordpress-tooltips' );
										 ?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										
										<td width="30%"  style="text-align:left;">
										<?php 
											$addtipto = 'span.question';
$questiontip = '<div class="tooltip"><p>"Add tooltips to all matching keyword in the same page" means when a post have many matched tooltip terms, we will add tooltips effect on all terms in this page.</p><p>"Add tooltips to the first matching keyword in the same page" means only add tooltip effect on the first matching tooltip term in the same page.</p></div>';
											$tipadsorbent = '.question';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltip',$tipadsorbent);
											echo $adminTip;
											echo __( 'Keyword Matching Mode:', 'wordpress-tooltips' ).' <span class="question">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;">
										<select id="onlyFirstKeyword" name="onlyFirstKeyword" style="width:400px;">
										<option id="firstKeywordSetting" value="all" <?php if ($onlyFirstKeyword == 'all') echo "selected";   ?>> Add tooltips to all matching keyword in the same page </option>
										<option id="firstKeywordSetting" value="first" <?php if ($onlyFirstKeyword == 'first') echo "selected";   ?>> Add tooltips to the first matching keyword in the same page </option>
										</select>
										</td>
										<td width="10%"  style="text-align:right;">
										<input type="submit"  class="button-primary"  id="onlyFirstKeywordsetting" name="onlyFirstKeywordsetting" value="<?php  echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>

										</table>
										</form>
										
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

<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
										<?php 
										echo __( 'Custom Tooltip Z-index Value', 'wordpress-tooltips' );
										 ?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										
										<td width="30%"  style="text-align:left;">
										<?php 
											$addtipto = 'span.questionzindex';
$questiontip = '<div class="tooltip"><p>Some plugins or themes have a very high z-index value, it caused the tooltip hidden behind their flyout, you can increase tooltips z-index to solve this kind of problem.</p></div>';
											$tipadsorbent = '.questionzindex';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltip',$tipadsorbent);
											echo $adminTip;
											echo __( 'Tooltip z-index Value:', 'wordpress-tooltips' ).' <span class="questionzindex">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;">
										<input type='text' id="tooltipZindexValue" name="tooltipZindexValue" value = '<?php echo $tooltipZindexValue;  ?>' style="width:400px;"> 
										</td>
										<td width="10%"  style="text-align:right;">
										<input type="submit" id="tooltipZindexValueSetting" name="tooltipZindexValueSetting"  class="button-primary" value="<?php  echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>

										</table>
										</form>
										
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
		
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
									<?php
									echo __( "Enable/Disable Tooltips for Image setting", 'wordpress-tooltips' )."<i> <font color='Gray'> (".__('Tooltips shown when mouse hovers over the image', 'wordpress-tooltips' ).')</font></i>';
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										<td width="30%"  style="text-align:left;">
										<?php 
											$addtipto = 'span.questionimage';
											$questiontip = '<div class="tooltip"><p>The option --  "I want to enable tooltips for image" means if you hover a image, the text in alt attribute will show as the tooltip content.</p><p>The option --  "    I want to disable tooltips for image " means if you hover a image, the tooltip box will not be shown.</p></div>';
											$tipadsorbent = '.questionimage';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltip',$tipadsorbent);
											echo $adminTip;										
										echo __( 'Enable Image Tooltips: ', 'wordpress-tooltips' ).'<span class="questionimage">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;">
										<select id="enableTooltipsForImage" name="enableTooltipsForImage" style="width:400px;">
										<option id="enableTooltipsForImageOption" value="YES" <?php if ($enableTooltipsForImage == 'YES') echo "selected";   ?>>  I want to enable tooltips for image </option>
										<option id="enableTooltipsForImageOption" value="NO" <?php if ($enableTooltipsForImage == 'NO') echo "selected";   ?>>   I want to disable tooltips for image </option>
										</select>
										</td>
										<td width="10%"  style="text-align:right;">
										<input type="submit" id="enableTooltipsForImageSubmit" name="enableTooltipsForImageSubmit"  class="button-primary" value="<?php echo __(' Update Now ', 'wordpress-tooltips'); ?>">
										</td>
										</tr>

										</table>
										</form>
										
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

<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
									<?php
									echo __( "Enable/Disable Tooltips for Post Excerpt", 'wordpress-tooltips' )."<i> <font color='Gray'></font></i>";
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										<td width="30%"  style="text-align:left;">
										<?php 
											$addtipto = 'span.questionexcerpt';
											$questiontip = '<div class="tooltip"><p>The option --  "Enable Tooltips for Post Excerpt" means show tooltips in your post excerpt</p><p>The option --  "    Disable Tooltips for Post Excerpt " means do not show tooltips in your post excerpt</p><p>This option is helpful for some advance themes</p></div>';
											$tipadsorbent = '.questionexcerpt';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltip',$tipadsorbent);
											echo $adminTip;
											echo __( 'Tooltips for Excerpt: ', 'wordpress-tooltips' ).'<span class="questionexcerpt">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;">
										<select id="enableTooltipsForExcerpt" name="enableTooltipsForExcerpt" style="width:400px;">
										<option id="enableTooltipsForExcerptOption" value="YES" <?php if ($enableTooltipsForExcerpt == 'YES') echo "selected";   ?>> Enable Tooltips for Post Excerpt </option>
										<option id="enableTooltipsForExcerptOption" value="NO" <?php if ($enableTooltipsForExcerpt == 'NO') echo "selected";   ?>>   Disable Tooltips for Post Excerpt </option>
										</select>
										</td>
										<td width="10%"  style="text-align:right;">
										<input type="submit" id="enableTooltipsForExcerptSubmit" name="enableTooltipsForExcerptSubmit"  class="button-primary" value="<?php echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>
										</table>
										</form>
										
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

<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
									<?php
									echo __( 'Enable/Disable Tooltips For Post Tag', 'wordpress-tooltips' )."<i> <font color='Gray'></font></i>";
									?>
									</span>
									</h3>
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										<td width="30%"  style="text-align:left;">
										<?php 
											$addtipto = 'span.questiontags';
											$questiontip = '<div class="tooltip"><p>The option --  "Enable Tooltips For Post Tag" means show tooltips on your post tags</p><p>The option --  "    Disable Tooltips For Post Tag " means do not show tooltips on your post tags</p><p>By default, tooltips for tag will be disabled </p></div>';
											$tipadsorbent = '.questiontags';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltip',$tipadsorbent);
											echo $adminTip;
											echo __( 'Tooltips For Tag: ', 'wordpress-tooltips' ).'<span class="questiontags">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;">
										<select id="enableTooltipsForTag" name="enableTooltipsForTag" style="width:400px;">
										<option id="enableTooltipsForTagOption" value="YES" <?php if ($enableTooltipsForTag == 'YES') echo "selected";   ?>> Enable Tooltips For Post Tag </option>
										<option id="enableTooltipsForTagOption" value="NO" <?php if ($enableTooltipsForTag == 'NO') echo "selected";   ?>>   Disable Tooltips For Post Tag </option>
										</select>
										</td>
										<td width="10%"  style="text-align:right;">
										<input type="submit" id="enableTooltipsForTagSubmit" name="enableTooltipsForTagSubmit"  class="button-primary" value="<?php echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>

										</table>
										</form>
										
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



<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
									<?php
									echo __( "Show Tooltips to Only One Single Category", 'wordpress-tooltips' )."<i> <font color='Gray'></font></i>";
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										<td width="30%"  style="text-align:left;">
										<?php 
											$addtipto = 'span.questionsinglecat';
											$questiontip = '<div class="tooltip"><p>The option --  "Show Tooltips to Only One Single Category" means only show tooltips in single category or show tooltips in site wide</p><p>If you did not setting this option, default option will be "All categories"</p></div>';
											$tipadsorbent = '.questionsinglecat';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltip',$tipadsorbent);
											echo $adminTip;
											echo __( 'Tooltips Only in One Category: ', 'wordpress-tooltips' ).'<span class="questionsinglecat">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;">
										<?php 
										wp_dropdown_categories( array( 'show_option_all' => __('All categories','wordpress-tooltips'), 'hide_empty'=> 0, 'name' => 'cat', 'selected' => $showOnlyInSingleCategory ) );
										?>
										</td>
										<td width="10%"  style="text-align:right;">
										<input type="submit" id="showOnlyInSingleCategorySubmit" name="showOnlyInSingleCategorySubmit"  class="button-primary" value="<?php echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>
										</table>
										</form>
										
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

<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
									<?php
									echo __( "Tooltips Popup Animation Effects", 'wordpress-tooltips' )."<i> <font color='Gray'></font></i>";
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">
										<tr style="text-align:left;">
										<td width="30%"  style="text-align:left;">
										<?php 
											$addtipto = 'span.questiontooltippopupanimation';
											$questiontip = '<div class="tooltip"><p>The option --  "Tooltips Popup Animation Effects" means show scale animation effect on tooltips popup box</p></div>';
											$tipadsorbent = '.questiontooltippopupanimation';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltip',$tipadsorbent);
											echo $adminTip;
											echo __( 'Tooltips Animation Effects: ', 'wordpress-tooltips' ).'<span class="questiontooltippopupanimation">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;">
										<select id="toolstipsAnimationClass" name="toolstipsAnimationClass" style="width:200px;">
										<option id="toolstipsAnimationOption" value="tipnoanimation" <?php if ($toolstipsAnimationClass == 'tipnoanimation') echo "selected";   ?>> <?php echo __( 'No Animation effects', 'wordpress-tooltips' ); ?> </option>
										<option id="toolstipsAnimationOption" value="tipscale" <?php if ($toolstipsAnimationClass == 'tipscale') echo "selected";   ?>> <?php echo __( 'Scale', 'wordpress-tooltips' ); ?> </option>
										</select>
										</td>
										<td width="10%"  style="text-align:right;">
										<input type="submit" id="showTooltipPopupAnimationSubmit" name="showTooltipPopupAnimationSubmit"  class="button-primary" value="<?php echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>
										</table>
										</form>
										
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
<?php 
/*
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
									<?php
									echo __( "Hidden Image in Glossary List Page", 'wordpress-tooltips' )."<i> <font color='Gray'></font></i>";
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										<td width="30%"  style="text-align:left;">
										<?php 
											$addtipto = 'span.hiddenimageinglossary';
											$questiontip = '<div class="tooltip"><p>"Hide Image in Glossary List Page" option will not show images in glossary page</p><p>"Display Image in Glossary List Page" option will show images in glossary page</p></div>';
											$tipadsorbent = '.questionsinglecat';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltip',$tipadsorbent);
											echo $adminTip;
											echo __( 'Hidden Image in Glossary List: ', 'wordpress-tooltips' ).'<span class="hiddenimageinglossary">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;">
										<select id="showImageinglossary" name="showImageinglossary" style="width:400px;">
										<option id="showImageinglossaryOption" value="YES" <?php if ($showImageinglossary == 'YES') echo "selected";   ?>> Display Image in Glossary List Page </option>
										<option id="showImageinglossaryOption" value="NO" <?php if ($showImageinglossary == 'NO') echo "selected";   ?>>   Hide Image in Glossary List Page </option>
										</select>
										</td>
										<td width="10%"  style="text-align:right;">
										<input type="submit" id="showImageinglossarySubmit" name="showImageinglossarySubmit"  class="button-primary" value="<?php echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>
										</table>
										</form>
										
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
		*/
?>				
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
									<?php
									echo __( "Tooltip Box Style ", 'wordpress-tooltips' )."<i> <font color='Gray'></font></i>";
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										<td width="30%"  style="text-align:left;">
										<?php 
											$addtipto = 'span.questionstyle';
											$questiontip = '<div class="tooltip"><p>There are 3 preset stylesheet, Dark, Light, if you choose these preset stylesheet, tooltip box style will changed in front end.</p><p>You can customize and add more amazing tooltips effects in pro version at https://tooltips.org</p></div>';
											$tipadsorbent = '.questionstyle';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltip',$tipadsorbent);
											echo $adminTip;
											echo __( 'Tooltip Style: ', 'wordpress-tooltips' ).'<span class="questionstyle">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;">
										<select id="selectedTooltipStyle" name="selectedTooltipStyle" style="width:200px;">
										<option id="toptionstyle" value="qtip-default" <?php if ($selectedTooltipStyle == 'qtip-default') echo "selected";   ?>> <?php echo __( 'Yellow', 'wordpress-tooltips' ); ?> </option>
										<option id="toptionstyle" value="qtip-dark" <?php if ($selectedTooltipStyle == 'qtip-dark') echo "selected";   ?>> <?php echo __( 'Dark', 'wordpress-tooltips' ); ?> </option>
										<option id="toptionstyle" value="qtip-light" <?php if ($selectedTooltipStyle == 'qtip-light') echo "selected";   ?>> <?php echo __( 'Light', 'wordpress-tooltips' ); ?> </option>
										<option id="toptionstyle" value="qtip-green" <?php if ($selectedTooltipStyle == 'qtip-green') echo "selected";   ?>> <?php echo __( 'Green', 'wordpress-tooltips' ); ?> </option>										
										</select>
										</td>
										<td width="10%"  style="text-align:right;">
										<input type="submit" id="toolstipsstylesetting" name="toolstipsstylesetting"  class="button-primary" value="<?php echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>
										</table>
										</form>
										
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
		
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
									<?php
									echo __( "Tooltip Close Button ", 'wordpress-tooltips' )."<i> <font color='Gray'></font></i>";
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										<td width="30%"  style="text-align:left;">
										<?php 
											$addtipto = 'span.questionshowtooltipclosebutton';
											$questiontip = '<div class="tooltip"><p>Show tooltip close button in tooltip popup window, it is helpful for user to close tooltip window in some mobile device </p></div>';
											$tipadsorbent = '.questionshowtooltipclosebutton';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltip',$tipadsorbent);
											echo $adminTip;
											echo __( 'Tooltip Close Button: ', 'wordpress-tooltips' ).'<span class="questionshowtooltipclosebutton">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;">
										<select id="showToolstipsCloseButtonSelect" name="showToolstipsCloseButtonSelect" style="width:100%;">
										<option id="showToolstipsCloseButtonOption" value="no" <?php if ($showToolstipsCloseButtonSelect == 'no') echo "selected";   ?>> <?php echo __( 'Hide tooltip close button in tooltips popup window', 'wordpress-tooltips' ); ?> </option>
										<option id="showToolstipsCloseButtonOption" value="yes" <?php if ($showToolstipsCloseButtonSelect == 'yes') echo "selected";   ?>> <?php echo __( 'Show tooltip close button in tooltips popup window', 'wordpress-tooltips' ); ?> </option>
										</select>
										</td>
										<td width="10%"  style="text-align:right;">
										<input type="submit" id="toolstipsclosebuttonsetting" name="toolstipsclosebuttonsetting"  class="button-primary" value="<?php echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>
										</table>
										</form>
										
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
		

<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
										<?php 
										echo __( 'Custom Wordpress Hook Priority Value', 'wordpress-tooltips' );
										 ?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										
										<td width="30%"  style="text-align:left;">
										<?php 
											$addtipto = 'span.questionzindex';
$questiontip = '<div class="tooltip"><p>If you want tooltips can support functionality of other wordpress plugins better, and show content which generated by other plugins in tooltips popup window, you need let other plugins(which followed WP the_content API) run first. You can try to change tooltip hook priority in here, heigher value means lower tooltip hook priority and that means let more WP plugins run first, if there are any plugin conflicts, you can just try to change tooltip hook priority lower or higher, then check result in the front end.</p></div>';
											$tipadsorbent = '.questionzindex';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltip',$tipadsorbent);
											echo $adminTip;
											echo __( 'Tooltip Hook Priority Value:', 'wordpress-tooltips' ).' <span class="questionzindex">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;">
										<input type='text' id="tooltipHookPriorityValue" name="tooltipHookPriorityValue" value = '<?php echo $tooltipHookPriorityValue;  ?>' style="width:400px;"> 
										</td>
										<td width="10%"  style="text-align:right;">
										<input type="submit" id="tooltipHookPriorityValueSetting" name="tooltipHookPriorityValueSetting"  class="button-primary" value="<?php  echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>

										</table>
										</form>
										
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
		
		


<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
									<?php
										echo __( "Disable Tooltips in H1,H2..., Link ", 'wordpress-tooltips' )."<i> <font color='Gray'> (".__('Note: support h1,h2,h3,h4,h5,h6,a, because there are too many html tags, we can not support all of them, other html tags may or may not works, you can test by yourself, just enter tags you want, split with comma "," ', 'wordpress-tooltips' ).')</font></i>';
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">

										<td width="30%"  style="text-align:left;">
										<?php 
											$addtipto = 'span.questionremoveontag';
											$questiontip = '<div class="tooltip"><p>Disable tooltips effects in html tags like h1,h2..., a, split with comma ","<br /><br /> for example: h1,h2,h3,h4,h5,h6,a </p></div>';
											$tipadsorbent = '.questionremoveontag';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltip',$tipadsorbent);
											echo $adminTip;
											echo __( 'Tooltips in Tags Like h1, h2..., a : ', 'wordpress-tooltips' ).'<span class="questionremoveontag">?</span>';
										?>
										</td>

										<td width="60%"  style="text-align:left;">
										<input type="text" id="disabletooltipinhtmltag" name="disabletooltipinhtmltag" value="<?php echo $disabletooltipinhtmltag;  ?>" placeholder="<?php echo __('for example:a,h1,h2,h3', "wordpress-tooltips");; ?>">
										</td>
										<td width="10%"  style="text-align:left;">
										<input type="submit" class="button-primary" id="disableTooltipInHtmlTagSubmit" name="disableTooltipInHtmlTagSubmit" value=" <?php echo __('Update Now', "wordpress-tooltips");?> ">
										</td>
										</tr>

										</table>
										</form>
										
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
<?php //!!! start ?>
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
									<?php
										echo __( "Disable Tooltips for Selected Classes and IDs", 'wordpress-tooltips' );
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form class="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%">

										<tr style="text-align:left;">
										<td width="30%"  style="text-align:left;">
										<?php
										$addtipto = 'span.questiondisabletooltipforclassandids';
										$questiontip = '<div class="tooltip"><p>Disable tooltips for selected classed and ids, split with comma ","<br /><br /> for example: .sky,#removetooltip,#myclass,.checkout,...and so on </p></div>';
										$tipadsorbent = '.questiondisabletooltipforclassandids';
										$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltip',$tipadsorbent);
										echo $adminTip;
										
											$disabletooltipforclassandids = get_option('disabletooltipforclassandids');
											echo __( 'Your Classes and IDs, split with comma "," : ', 'wordpress-tooltips' ).'<span class="questiondisabletooltipforclassandids">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;">
										<input type="text" id="disabletooltipforclassandids" name="disabletooltipforclassandids" value="<?php echo $disabletooltipforclassandids;  ?>" placeholder="<?php echo __('for example:.sky,#removetooltip,#myclass,.checkout', "wordpress-tooltips");; ?>">
										</select>
										</td>
										<td width="10%"  style="text-align:left;">
										<input type="submit" class="button-primary" id="disabletooltipforclassandidsSubmit" name="disabletooltipforclassandidsSubmit" value=" <?php echo __('Update Now', "wordpress-tooltips");?> ">
										</td>
										</tr>

										</table>
										</form>
										
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
<?php //!!! end ?>		
<?php //!!!start ?>
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
									<?php
									echo __( 'Give a Gray "Tooltip Support" Credit Link at the Bottom of Tooltip Box?', 'wordpress-tooltips' );
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">

										<td width="30%"  style="text-align:left;">
										<?php 
											$enabletooltipsPopupCreditLinkInPopupWindow = get_option("enabletooltipsPopupCreditLinkInPopupWindow");
											$addtipto = 'span.questiontooltipsPopupCreditLinkInPopupWindow';
											$questiontip = '<div class="tooltip"><p>By default, the option is disabled, in tooltip popup box have no credit link. Only when you enabled credit link option in optional settings panel, the gray "tooltip support" credit link will be shown at the bottom of tooltip popup window.</p></div>';
											$tipadsorbent = '.questiontooltipcasesensitive';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltip',$tipadsorbent);
											echo $adminTip;
											echo __( '"Tooltip Support" Credit Link at Bottom of Tooltip:', 'wordpress-tooltips' ).' <span class="questiontooltipsPopupCreditLinkInPopupWindow">?</span>';
										?>
										</td>



										<td width="60%"  style="text-align:left;">
										<select id="enabletooltipsPopupCreditLinkInPopupWindow" name="enabletooltipsPopupCreditLinkInPopupWindow">
										<option id="enabletooltipsPopupCreditLinkInPopupWindowOption" value="YES" <?php if ($enabletooltipsPopupCreditLinkInPopupWindow == 'YES') echo "selected";   ?>> Yes </option>
										<option id="enabletooltipsPopupCreditLinkInPopupWindowOption" value="NO" <?php if ($enabletooltipsPopupCreditLinkInPopupWindow == 'NO') echo "selected";   ?>> No </option>
										</select> 
										</td>
										<td width="10%"  style="text-align:left;">
										<input type="submit" class="button-primary" id="enabletooltipsPopupCreditLinkInPopupWindowSetting" name="enabletooltipsPopupCreditLinkInPopupWindowSetting" value=" <?php echo __('Update Now', "wordpress-tooltips");?> ">
										</td>
										</tr>

										</table>
										</form>
										
									</div>
								</div>
							</div>
						</div>
					</div>
		    	</div>
			</div>
		</div>
<?php //!!! start ?>
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
										<?php 
										echo __( 'Disable Tooltip in Entire Site', 'wordpress-tooltips' );
										 ?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										
										<td width="30%"  style="text-align:left;">
										<?php 
											$disabletooltipentiresite = get_option('disabletooltipentiresite');
											$addtipto = 'span.questiondisabletooltipentiresite';
$questiontip = '<div class="divclasstooltipentiresite"><p>Enable or Disable tooltips effect in entire site.</p></div>';
											$tipadsorbent = '.questiondisabletooltipentiresite';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.divclasstooltipentiresite',$tipadsorbent);
											echo $adminTip;
											echo __( 'Disable Tooltip in Entire Site:', 'wordpress-tooltips' ).' <span class="questiondisabletooltipentiresite">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;padding-right:30px;">
										<select id="selectdisabletooltipentiresite" name="selectdisabletooltipentiresite" style="width:400px;">
										<option id="selectdisabletooltipentiresiteoption" value="YES" <?php if ($disabletooltipentiresite == 'YES') echo "selected";   ?>> <?php echo __( 'Enable Tooltip in Entire Site', 'wordpress-tooltips' ); ?> </option>
										<option id="selectdisabletooltipentiresiteoption" value="NO" <?php if ($disabletooltipentiresite == 'NO') echo "selected";   ?>>   <?php echo __( 'Disable Tooltip in Entire Site', 'wordpress-tooltips' ); ?> </option>
										</select> 
										</td>
										<td width="10%"  style="text-align:left;">
										<input type="submit" class="button-primary" id="disabletooltipentiresiteSetting" name="disabletooltipentiresiteSetting" value="<?php  echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>

										</table>
										</form>
										
									</div>
								</div>
							</div>
						</div>
					</div>
		    	</div>
			</div>
		</div>
<?php //!!! start 6.9.3 ?>
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
										<?php 
										echo __( 'Disable Tooltip on Mobile Devices', 'wordpress-tooltips' );
										 ?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										
										<td width="30%"  style="text-align:left;">
										<?php 
											$disabletooltipmobile = get_option('disabletooltipmobile');
											$addtipto = 'span.questiondisabletooltipmobile';
$questiontip = '<div class="tooltipdisabletooltipmobile"><p>Enable or Disable tooltips effect on mobile device.</p></div>';
											$tipadsorbent = '.questiondisabletooltipmobile';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltipdisabletooltipmobile',$tipadsorbent);
											echo $adminTip;
											echo __( 'Disable Tooltip on Mobile Devices:', 'wordpress-tooltips' ).' <span class="questiondisabletooltipmobile">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;padding-right:30px;">
										<select id="selectdisabletooltipmobile" name="selectdisabletooltipmobile" style="width:400px;">
										<option id="selectdisabletooltipmobileoption" value="NO" <?php if ($disabletooltipmobile == 'NO') echo "selected";   ?>> <?php echo __( 'Enable Tooltip on Mobile Devices', 'wordpress-tooltips' ); ?> </option>
										<option id="selectdisabletooltipmobileoption" value="YES" <?php if ($disabletooltipmobile == 'YES') echo "selected";   ?>>   <?php echo __( 'Disable Tooltip on Mobile Devices', 'wordpress-tooltips' ); ?> </option>
										</select> 
										</td>
										<td width="10%"  style="text-align:left;">
										<input type="submit" class="button-primary" id="disabletooltipmobileoptionSetting" name="disabletooltipmobileoptionSetting" value="<?php  echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>

										</table>
										</form>
										
									</div>
								</div>
							</div>
						</div>
					</div>
		    	</div>
			</div>
		</div>
<?php //!!! end ?>
<?php //!!! 7.2.5  ?>
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
										<?php 
										echo __( 'Enable/Disable Tooltips for Category Title ', 'wordpress-tooltips' );
										 ?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										
										<td width="30%"  style="text-align:left;">
										<?php 
											$enableTooltipsForCategoryTitle = get_option("enableTooltipsForCategoryTitle");
											$addtipto = 'span.spanquestioncategorytitle';
$questiontip = '<div class="divtooltipforcategorytitle"><p>Enable tooltips effects in category archive title or not.</p></div>';
											$tipadsorbent = '.spanquestioncategorytitle';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.divtooltipforcategorytitle',$tipadsorbent);
											echo $adminTip;
											echo __( 'Tooltips for Category Title:', 'wordpress-tooltips' ).' <span class="spanquestioncategorytitle">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;padding-right:30px;">
										<select id="enableTooltipsForCategoryTitle" name="enableTooltipsForCategoryTitle" style="width:300px;">
										<option id="enableTooltipsForCategoryTitleOption" value="YES" <?php if ($enableTooltipsForCategoryTitle == 'YES') echo "selected";   ?>> <?php echo __('Enable Tooltips for Category Title', "wordpress-tooltips");?> </option>
										<option id="enableTooltipsForCategoryTitleOption" value="NO" <?php if ($enableTooltipsForCategoryTitle == 'NO') echo "selected";   ?>>   <?php echo __('Disable Tooltips for Category Title', "wordpress-tooltips");?> </option>
										</select>
										</td>
										<td width="10%"  style="text-align:left;">
										<input type="submit" class="button-primary" id="enableTooltipsForCategoryTitleSubmit" name="enableTooltipsForCategoryTitleSubmit" value="<?php  echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>

										</table>
										</form>
										
									</div>
								</div>
							</div>
						</div>
					</div>
		    	</div>
			</div>
		</div>
<?php //!!! end 7.2.5 ?>
		<div style="clear:both"></div>
<?php //!!! start 7.5.7 ?>
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
										<?php 
										echo __( 'Tooltip Content Font Size', 'wordpress-tooltips' );
										 ?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										<td width="30%"  style="text-align:left;">
										<?php 
											$toolstipsFontSize = '';
											$toolstipsFontSize = get_option("toolstipsFontSize");
											
											$addtipto = 'span.questionfontsize';
											$questiontip = '<div class="tooltip28"><p>'. __('By default, the font size will be decided by your theme, but you can custom it in here, for example: 14, or just ignore this option', "wordpress-tooltips"). '</p></div>';
											
											$tipadsorbent = '.questionfontsize';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltip28',$tipadsorbent);
											echo $adminTip;

												
											echo __( 'Tooltip Font Size: ', 'wordpress-tooltips' ).'<span class="questionfontsize">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;padding-right:30px;">
										<input type="text" size="7" id="toolstipsFontSize" name="toolstipsFontSize" value="<?php echo $toolstipsFontSize;  ?>" placeholder="<?php echo __( 'for example:14 or just ignore it', 'wordpress-tooltips' ) ?>"> PX
										</td>
										<td width="10%"  style="text-align:left;">
										<input type="submit" class="button-primary" id="toolstipsFontSizeSubmit" name="toolstipsFontSizeSubmit" value="<?php  echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>

										</table>
										</form>
										
									</div>
								</div>
							</div>
						</div>
					</div>
		    	</div>
			</div>
		</div>
<?php //!!! end 7.5.7 ?>
		<div style="clear:both"></div>
		<br />
<?php //!!!end ?>	 
<?php //!!! start 7.6.9 ?>
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
							
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
										<?php 
										echo __( 'Move inline javascripts to the footer', 'wordpress-tooltips' );
										 ?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										<td width="30%"  style="text-align:left;">
										<?php 
											$enableMoveInlineJsToFooter = '';
											$enableMoveInlineJsToFooter = get_option("enableMoveInlineJsToFooter");
											
											$addtipto = 'span.questionmovejstofooter';
											$questiontip = '<div class="tooltipmovejstofooter"><p>'. __('By default, tooltips will generate some inline javascript codes, you can opt to move these inline js codes to the wordpress footer to speed up page speeds, also when you install a few cache plugins, maybe some optisons in these cache plugin will break js codes in tooltip plugin, you can avoid confict by enable this option', "wordpress-tooltips"). '</p></div>';
											
											$tipadsorbent = '.questionmovejstofooter';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.tooltipmovejstofooter',$tipadsorbent);
											echo $adminTip;

												
											echo __( 'Move inline js to footer, speed up pages: ', 'wordpress-tooltips' ).'<span class="questionmovejstofooter">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;padding-right:30px;">
										<select id="enableMoveInlineJsToFooter" name="enableMoveInlineJsToFooter" style="width:300px;">
										<option id="enableMoveInlineJsToFooterOption" value="NO" <?php if ($enableMoveInlineJsToFooter == 'NO') echo "selected";   ?>> <?php echo __('NO, let it be', "wordpress-tooltips");?></option>
										<option id="enableMoveInlineJsToFooterOption" value="YES" <?php if ($enableMoveInlineJsToFooter == 'YES') echo "selected";   ?>>   <?php echo __('YES, move inline javascripts to the footer to speed up page speed', "wordpress-tooltips");?>  </option>
										</select>
										</td>
										<td width="10%"  style="text-align:left;">
										<input type="submit" class="button-primary" id="toolstipsFontSizeSubmit" name="toolstipsFontSizeSubmit" value="<?php  echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>

										</table>
										</form>
										
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
<?php //!!! start 7.7.3 ?>
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
										<?php 
										echo __( 'Enable / Disable JQuery-Migrate', 'wordpress-tooltips' );
										 ?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										
										<td width="30%"  style="text-align:left;">
										<?php 
											$seletEnableJqueryMigrate = get_option('seletEnableJqueryMigrate');
											$addtipto = 'span.questionEnableJqueryMigrate';
											$questiontip = '<div class="divEnableJqueryMigrate"><p>Enable or Disable Jquery-Migrate, by default, latest wordpress do not support JQuery-Migrate, this may caused some problems, for example, when users mouse hover tooltip terms, no tooltips pup-up window, enable jQuery-Migrate will solve this kind of problem. Also you can just install and activate enable jQuery-Migrate plugin which developed by WP offical</p></div>';
											$tipadsorbent = '.questionEnableJqueryMigrate';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.divEnableJqueryMigrate',$tipadsorbent);
											echo $adminTip;

											echo __( 'Enable / Disable JQuery-Migrate:', 'wordpress-tooltips' ).' <span class="questionEnableJqueryMigrate">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;padding-right:30px;">
										<select id="seletEnableJqueryMigrate" name="seletEnableJqueryMigrate" style="width:300px;">
										<option id="seletEnableJqueryMigrateoption" value="NO" <?php if ($seletEnableJqueryMigrate == 'NO') echo "selected";   ?>>   <?php echo __( 'No, let it be', 'wordpress-tooltips' ); ?> </option>
										<option id="seletEnableJqueryMigrateoption" value="YES" <?php if ($seletEnableJqueryMigrate == 'YES') echo "selected";   ?>> <?php echo __( 'Enable JQuery-Migrate for WordPress', 'wordpress-tooltips' ); ?> </option>
										</select> 
										</td>
										<td width="10%"  style="text-align:left;">
										<input type="submit" class="button-primary" id="disableStatisticsoptionSetting" name="disableStatisticsoptionSetting" value="<?php  echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>

										</table>
										</form>
										
									</div>
								</div>
							</div>
						</div>
					</div>
		    	</div>
			</div>
		</div>
<?php //!!! end 7.7.3 ?>		
		<div style="clear:both"></div>
		<br />
<?php //!!! start 8.1.1 ?>
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
										<?php 
										echo __( "Disable Tooltips Functionality on Front-End and Allow Glossary Functionality<i>(in general, you do not need to set this option)</i>", "wordpress-tooltips");
										 ?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%" style="font-size: 11px;">

										<tr style="text-align:left;">
										
										<td width="30%"  style="text-align:left;">
										<?php 
    										$admin_tip_content = __('Opt to disable tooltips functions on front-end, but allow glossary functions, or enable tooltips functions and enable glossary functions',  "wordpress-tooltips");
										    $disableTooltipandEnableGlossary = get_option('disableTooltipandEnableGlossary');
											$addtipto = 'span.questiondisableTooltipandEnableGlossary';
											$questiontip = '<div class="divdisableTooltipandEnableGlossary"><p>'.$admin_tip_content.'</p></div>';
											$tipadsorbent = '.questiondisableTooltipandEnableGlossary';
											$adminTip = showAdminTip($addtipto,$questiontip,'div.divdisableTooltipandEnableGlossary',$tipadsorbent);
											echo $adminTip;

											echo __( 'Disable Tooltips and Enable Glossary:', 'wordpress-tooltips' ).' <span class="questiondisableTooltipandEnableGlossary">?</span>';
										?>
										</td>
										<td width="60%"  style="text-align:left;padding-right:30px;">
										<select id="disableTooltipandEnableGlossary" name="disableTooltipandEnableGlossary" style="width:300px;">
										<option id="disableTooltipandEnableGlossaryOption" value="NO" <?php if ($disableTooltipandEnableGlossary == 'NO') echo "selected";   ?>>   <?php echo __( 'NO,Enable Tooltip and Enable Glossary', 'wordpress-tooltips' ); ?> </option>
										<option id="disableTooltipandEnableGlossaryOption" value="YES" <?php if ($disableTooltipandEnableGlossary == 'YES') echo "selected";   ?>> <?php echo __( 'Yes,Disable Tooltip and Enable Glossary', 'wordpress-tooltips' ); ?> </option>
										</select> 
										</td>
										<td width="10%"  style="text-align:left;">
										<input type="submit" class="button-primary" id="disableTooltipandEnableGlossarySubmit" name="disableTooltipandEnableGlossarySubmit" value="<?php  echo __( ' Update Now ', 'wordpress-tooltips' ); ?>">
										</td>
										</tr>

										</table>
										</form>
										
									</div>
								</div>
							</div>
						</div>
					</div>
		    	</div>
			</div>
		</div>
<?php //!!! end 8.1.1 ?>		
<?php //!!! end 7.6.9 ?>
<?php /* //!!! start 7.7.1 ?>
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:90%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
									<?php
										echo __( "Enable Defer for Tooltip Javascript Files? ", 'wordpress-tooltips' );
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<form class="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%">

										<tr style="text-align:left;">
										<td width="25%"  style="text-align:left;">
										<?php
											$enableDeferTooltip =	get_option("enableDeferTooltip");
											if (empty($enableDeferTooltip)) $enableDeferTooltip = 'no';
											echo __( "Enable Defer for Tooltip Javascript Files", 'wordpress-tooltips' ).' <span class="spanquestionenableDeferTooltip">?</span>';
										?>
										<?php
											$admin_tip = __('If you enable this option, we will add defer attribute for all javascript files of tooltips plugin, this will increase page load speed', "wordpress-tooltips");
										?>
										<script type="text/javascript"> 
										jQuery(document).ready(function () {
										  jQuery("span.spanquestionenableDeferTooltip").hover(function () {
										    jQuery(this).append('<div class="divquestionenableDeferTooltip"><p><?php echo $admin_tip; ?></p></div>');
										  }, function () {
											  jQuery("div.divquestionenableDeferTooltip").remove();
										  });
										});
										</script>
										</td>
										<td width="30%"  style="text-align:left;">
										<select id="enableDeferTooltip" name="enableDeferTooltip">
										<option id="enableDeferTooltipOption" value="no" <?php if ($enableDeferTooltip == 'no') echo "selected";   ?>>  <?php echo __('NO', "wordpress-tooltips") ?> </option>
										<option id="enableDeferTooltipOption" value="yes"  <?php if ($enableDeferTooltip == 'yes') echo "selected";   ?>> <?php echo __('YES', "wordpress-tooltips") ?> </option>
										</select>										</td>
										<td width="30%"  style="text-align:left;">
										<input type="submit" class="button-primary" id="enableDeferTooltipOptionSubmit" name="enableDeferTooltipOptionSubmit" value=" <?php echo __('Update Now', "wordpress-tooltips");?> ">
										</td>
										</tr>

										</table>
										</form>
										
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
<?php //!!! end 7.7.1 */ ?>		
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

</div>
<div class="" style="width:28%;float:left;">	
<div style='clear:both'></div>		
		<div class="">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="" style="width:100%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
									<?php
									echo __( 'Features & Demos of Tooltips Pro', 'wordpress-tooltips' )."<i> <font color='Gray'></font></i>";
									?>
									</span>
									</h3>
									<div class="inside" style='padding-left:5px;'>
									
							<div class="inside">
									<p>
									<span style="margin-left:0px;"><b><a class="" target="_blank" href="https://tooltips.org/features-of-wordpress-tooltips-plugin/">Features And Demos:</a></b></span>
									</p>
									<ul>
									<li>
										* Custom an unique pretty style for each tooltip, each tooltip can have their own "Tooltip Box Background","Tooltip Box Width", "Tooltip Font Color","Tooltip Text Align", "Tooltip Box Padding:", "Tooltip Class Name", "Tooltip Border Radius", "Border Width", "Tooltip Border Color", "Tooltips Border Bottom", "Tooltip Underline Color", "Tooltips Shadow", "Tooltip Font Size", "Tooltips Line Height", "Tooltip Term Color", "Tooltips Popup Animation", "Title Background Color", "Tooltip Title Font Size", "Title Font Color", "Close Button Background",
"Close Button Radius", "Close Button Font Color"... and so on
									</li>
									<li>
										<a href='https://tooltips.org/features-of-wordpress-tooltips-plugin/' target='_blank'>Build a colorful and varied and graceful tooltips site super easy and fast</a> 
									</li>									
									<li>
										* Support tooltips for many popular plugins, for example, contact form 7, <a href="https://tooltips.org/product/show-tooltips-in-woocommerce-products/">WooCommerce Product</a>, buddypress, bbpress, <a href="https://tooltips.org/add-tooltips-for-table/">Tables</a>, <a href="https://tooltips.org/tooltips-for-pricing-table/" >Pricing Table</a>, <a href="https://tooltips.org/tooltips-for-button/">Buttons</a>, HTML5 FAQ, ACF, bbPress, BuddyPress ... 
									</li>
									<li>
										* Build pretty tooltip quickly, Fine-grained custom tooltips style, choose the color intuitively from color picker, Just a few click and easy to custom whole tooltips style in panel: color, border, opacity, width, position, shadow, font, tooltips underline style, color of tooltips terms, close button, title bar..., custom more than 28 tooltips elements via one click panel
									</li>
									<li>										
										* Multi trigger method: show/hidden tooltips when: Mouse Over,Click,Mouse Leave..., Multi tooltips positioning options: topMiddle, bottomMiddle, topRight, leftMiddle..., Multi tooltips for Image Keyword Matching Mode: next-gen gallery mode or ALT attribute mode or Title attribute mode or REL attribute mode, also you can use one image as tooltip image for another image</li>									
									<li>										
										* Add Tooltips to menu items, post title, archive, post tags, post types..., options to only add tooltips for specified post types, options to disable tooltips for specified pages, support <a href="https://tooltips.org/contact-us/" target="_blank">form tooltips</a>, add tooltip for each form elements
									</li>
									<li>
										* Responsive, Mobile devices friendly
									</li>									
									<li>
										* Support Polylang & WPML Multilingual
									</li>
									<li>
										* Support <a href='https://tooltips.org/bullet-screen'>Bullet Screen effects</a>
									</li>
									<li>
										* Custom tooltips popup animation effects: wiggle, scale, 360 degree rotation,rotateY vertical Y-axis... and so on.
									</li>
									<li>										 
										* 5 preset glossary beautiful color schemes, 7 preset tooltips stylesheet and beautiful color schemes, poweful and easy to use custom style panels 
	 								</li>									
									<li>
										* Enable or Disable tooltips in site home page
									</li>
									<li>
										* Enable or Disable tooltips effect in html tags, for example h1, h2. h3, a, div ...and more  
									</li>									
									<li>
										* Option to disable specific tooltips in the page for specified pages, only add Tooltips for specified post types, option for easy to add video tooltips in tooltips editor...
									</li>
									<li>
										* Tooltips Stats Report,see how many hists for each tooltips
									</li>
									<li>
										* One click to import your tooltips from csv
									</li>
									<li>
										* One click to change your glossary template from 5 preset Glossary stylesheet and beautiful color schemes, easy to custom Glossary style in glossary settings panel, glossary page support list style or table style
									</li>
									<li>
										* One click to enable / disable glossary index page and glossary term links
									</li>
									<li>
										* Generate powerful SEO friendly & responsive glossary index page and glossary term pages automatically.
									</li>
									<li>
										* Options to enable / disable glossary searchable or not
									</li>
									<li>
										* Use glossary shortcode [[glossary]] to add glossary in any page or post
									</li>
									<li>
										* Powerfule & easy to use shortcodes, you can use [glossary catid='1,2,3'] to show tooltips of specified 
tooltips categories in glossary page, by category id, or use  [glossary catname = 'classmate, family, school'] to only show tooltips of specified categories 
in glossary page, by category name, or you can get tooltip by id [tooltip_by_id tooltip_id='222'], or you can enable advance tooltip shortcode mode to insert video, image, link, audio in tooltip shortcode   
									</li>
									<li>
										* One click to chose your language from English, Swedish, German, French, Finnish,Spanish,Russian... for Glossary / Directory / List.
									</li>
									<li>
										* Glossary Language addon: You can use language alphabet generator to generate your language alphabet, or just custom your own alphabet based on your application scenarios, also you can generate numbers based on your language or application scenarios, or you can replace words in glossary bar for example replace the "ALL" to your own language... and so on
									</li>
									<li>
										* Via "How To Use Wordpress Tooltips" Panel, you can watch video tutorial and text document to learn how to use wordpress tooltip plugin									
									</li>	
									<li>
										* Optimized tooltip performance and whole site performance 									
									</li>																										
									<li>	
										* and more...
									</li>
									<li>
									<span style="margin-left:10px;"><b><a class="" target="_blank" href="https://tooltips.org/features-of-wordpress-tooltips-plugin/">Check Demos Now</a></b> -- Only $9, Lifetime Upgrades, Unlimited Download, Ticket Support </span>
									</li>																			
									</ul>
								</div>

									</div>
								</div>
								
								<div class="postbox">
									<h3 class='hndle' style='padding: 20px 0px; !important'>
									<span>
									<?php 
										echo  __( 'Wordpress tooltips Tips Feed:', 'wordpress-tooltips');
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:10px;'>
						<?php 
							wp_widget_rss_output('https://tomas.zhu.bz/feed/', array(
							'items' => 3, 
							'show_summary' => 0, 
							'show_author' => 0, 
							'show_date' => 1)
							);
						?>
										<br />
									* <a class=""  target="_blank" href="https://tooltips.org/contact-us/">Suggest a Feature, Report a Bug? Need Customize Plugin? --> Contact me</a>										
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
</div>
<?php
}

function editTooltips()
{
		
		global $wpdb;
		$m_tooltipsArray = get_option('tooltipsarray');
		$m_toolstipskeyword= '';
		$m_toolstipscontent= '';
		if (empty($m_tooltipsArray))
		{
			$m_tooltipsArray = array();
		}
		
		if (isset($_POST['toolstipskeywordsubmitnew']))
		{
			if (isset($_POST['toolstipskeyword']))
			{
				//$m_toolstipskeyword = $wpdb->escape($_POST['toolstipskeyword']);
				$m_toolstipskeyword = sanitize_text_field($_POST['toolstipskeyword']); // 7.6.7 
			}

			if (isset($_POST['toolstipscontent']))
			{
				//$m_toolstipscontent = $wpdb->escape($_POST['toolstipscontent']);
				$m_toolstipscontent = sanitize_text_field($_POST['toolstipscontent']); //7.6.7
				
			}
			
			if ((!(empty($m_toolstipscontent))) && (!(empty($m_toolstipskeyword))))
			{
				$m_added = false;
				if ((is_array($m_tooltipsArray)) && (count($m_tooltipsArray) > 0))
				{
					$i = 0;
					foreach ($m_tooltipsArray as $m_tooltipsSingle)
					{
						if ($m_tooltipsSingle['keyword'] == $m_toolstipskeyword)
						{
							$m_tooltipsSingle['content'] = $m_toolstipscontent;
							$m_tooltipsArray[$i]['content'] = $m_toolstipscontent;
							$m_added = true;
							break;
						}
						$i++;
					}
				}

				if ($m_added  == false)
				{
					$m_tooltipsTempArray = array();
					$m_tooltipsTempArray['keyword'] = $m_toolstipskeyword;
					$m_tooltipsTempArray['content'] = $m_toolstipscontent;
					$m_tooltipsArray[] = $m_tooltipsTempArray;					
				}
				
				update_option('tooltipsarray',$m_tooltipsArray);
			}

			$tooltipsMessageString =  __( 'Tooltips added', 'wordpress-tooltips' );
			tooltipsMessage($tooltipsMessageString);
		}
		


		if (isset($_POST['toolstipskeywordsubmitedit']))
		{
			if (isset($_POST['toolstipskeyword']))
			{
				//$m_toolstipskeyword = $wpdb->escape($_POST['toolstipskeyword']);
				$m_toolstipskeyword = sanitize_text_field($_POST['toolstipskeyword']); // 7.6.7
				
			}

			if (isset($_POST['toolstipscontent']))
			{
				//$m_toolstipscontent = $wpdb->escape($_POST['toolstipscontent']);
				$m_toolstipscontent = sanitize_text_field($_POST['toolstipscontent']); //7.6.7
				
			}
			
			if ((!(empty($m_toolstipscontent))) && (!(empty($m_toolstipskeyword))))
			{
				$m_added = false;
				//$m_toolstipskeywordsubmithideen = $wpdb->escape($_POST['toolstipskeywordsubmithideen']);
				$m_toolstipskeywordsubmithideen = sanitize_text_field($_POST['toolstipskeywordsubmithideen']); //7.6.7
				$m_tooltipsArray[$m_toolstipskeywordsubmithideen]['keyword'] = $m_toolstipskeyword;
				$m_tooltipsArray[$m_toolstipskeywordsubmithideen]['content'] = $m_toolstipscontent;  
				update_option('tooltipsarray',$m_tooltipsArray);
			}

			$tooltipsMessageString =  __( 'Changes saved', 'wordpress-tooltips' );
			tooltipsMessage($tooltipsMessageString);			
		}

		if (isset($_POST['toolstipskeywordsubmitdelete']))
		{
			//$m_toolstipskeywordsubmithideen = $wpdb->escape($_POST['toolstipskeywordsubmithideen']);
			$m_toolstipskeywordsubmithideen = sanitize_text_field($_POST['toolstipskeywordsubmithideen']); //7.6.7
			

			{
				array_splice($m_tooltipsArray,$m_toolstipskeywordsubmithideen,1);
				update_option('tooltipsarray',$m_tooltipsArray);
			}

			$tooltipsMessageString =  __( 'Tooltips deleted', 'wordpress-tooltips' );
			tooltipsMessage($tooltipsMessageString);

		}
				
		echo "<br />";
		?>

<div style='margin:10px 5px;'>
<div style='float:left;margin-right:10px;'>
<img src='<?php echo get_option('siteurl');  ?>/wp-content/plugins/wordpress-tooltips/images/new.png' style='width:30px;height:30px;'>
</div> 
<div style='padding-top:5px; font-size:22px;'> <i></>Add/Edit Tooltips</i></div>
</div>
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
										Add new Tooltips 
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<br />
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<table id="toolstipstable" width="100%">

										<tr>
										<td width="100%">
										Please input your keyword/sentence of the tooltip:
										<br />
										<br />										
										<input type="text" id="toolstipskeyword" name="toolstipskeyword" value=""  style="width:600px;">
										<br />
										<br />
										<br />
										<br />
										Please input content/tips/image/video of the tooltip <i><font color="Gray">(HTML tag supported)</font></i>:
										<br />
										<br />
										<textarea style="width:600px;" rows="2" cols="40" name='toolstipscontent'></textarea>
										
										</td>
										</tr>

										</table>
										<br />
										<input type="submit" id="toolstipskeywordsubmitnew" name="toolstipskeywordsubmitnew" value="Add Now">
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
		
<!--  edit  -->
<?php 

$m_tooltipsArray = get_option('tooltipsarray');

	if ((is_array($m_tooltipsArray)) && (count($m_tooltipsArray)>0))
	{		
?>
<div style='margin:20px 5px;'>

<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:98%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 10px 0px;'><span>
										Edit Existed Tooltips 
									</span>
									</h3>
								
									<div class="inside" style='padding-left:5px;'>
										<br />
										
										<table id="toolstipstable" width="100%">
										<?php
										$i = 0; 
										foreach ($m_tooltipsArray as $m_tooltipsNow)
										{

										?>
										<form id="toolstipsform" name="toolstipsform" action="" method="POST">
										<tr>
										<td width="10%">
										Keyword:
										</td>
										<td width="20%">
										<input type="text" id="toolstipskeyword" name="toolstipskeyword" value="<?php echo stripslashes(stripslashes($m_tooltipsNow['keyword'])); ?>">
										</td>
										<td width="10%">
										Content:
										</td>
										<td width="35%">
										<textarea rows="2" cols="35" name='toolstipscontent'><?php echo stripslashes(stripslashes($m_tooltipsNow['content'])); ?></textarea>
										</td>
										
										<td width="12%" style='align:right;text-align:right;padding-left:3px;'>
											<input type="hidden" id="toolstipskeywordsubmithideen" name="toolstipskeywordsubmithideen" value="<?php echo $i; ?>">
											<input type="submit" class="toolstipskeywordsubmitedit button-primary" name="toolstipskeywordsubmitedit" value="Update Now">										
										</td>
										
										<td width="13%" style='align:right;text-align:right;'>
											<input type="submit" class="toolstipskeywordsubmitdelete button-primary" name="toolstipskeywordsubmitdelete" value="Delete Now">										
										</td>										
										</tr>
										</form>
										<?php
										$i++;
										}

										?>
										</table>
										<br />
										
										
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

		<?php
		}				
}
	
function TooltipsWidgetInit()
{	
	wp_register_sidebar_widget('Tooltips', 'Tooltips', 'tooltipsSidebar');
	wp_register_widget_control('Tooltips','Tooltips', 'tooltipsControl', 300, 200);
}

function tooltipsControl()
{
	global $wpdb,$table_prefix,$g_content;
    $options = get_option('titleTooltipsControl');

    if (empty($options))
    {
    	$m_title = '';
    }
    else 
    {
		$m_title = $options;
    }
    echo $m_title;
    if (isset($_POST['HiddenTooltipsControl'])) 
    {
		//update_option('titleTooltipsControl',$wpdb->escape($_POST['HiddenTooltipsControl']));
    	update_option('titleTooltipsControl',sanitize_text_field($_POST['HiddenTooltipsControl'])); //7.6.7
		
    }

    echo '<div style="width:250px">';
    echo 'Input Title Here:';
    echo '<br />';
    echo '<input  type="text" id="HiddenTooltipsControl" name="HiddenTooltipsControl" value="'.$m_title.'" style="margin:5px 5px;width:200px" />';
	echo '</div>';
}


function tooltipsSidebar($argssidebarsidebar = null)
{
	global $wpdb,$table_prefix,$g_content;
	$before_widget = '';
	$after_widget = '';
	if (!empty($argssidebar))
	{
		extract($argssidebar);
	}

    $options = get_option('titleTooltipsControl');

    if (empty($options))
    {
    	$m_title = '';
    }
    else 
    {
		$m_title = $options;
    }
    
    
    echo $before_widget;
    echo '<div class="sidebarTooltips">';
    if (!empty($m_title))
    {
    	echo "<h1>" . $m_title . "</h1>";
    }

	global $table_prefix,$wpdb,$post;

	$args = array( 'post_type' => 'tooltips', 'post_status' => 'publish' );
	$loop = new WP_Query( $args );
	$return_content = '';
	$return_content .= '<div class="tooltips_widget">';
	while ( $loop->have_posts() ) : $loop->the_post();
		$return_content .= '<div class="tooltips_list">'.get_the_title().'</div>';
	endwhile;
	$return_content = tooltipsInContent($return_content);
	$return_content = showTooltipsInShorcode($return_content);

	$return_content .= '</div>';
    echo "</div>";
	echo $return_content;
}

function tooltipsMessage($p_message)
{

	echo "<div id='message' class='updated fade' style='padding: 10px;'>";

	echo $p_message;

	echo "</div>";
}

function tooltips_unique_id()
{
	$tooltips_unique_id = md5(uniqid(mt_rand(),1));
	return $tooltips_unique_id;
}

function showAdminTip($addtipto,$tip,$placeclass,$tipadsorbent)
{
	$tipScript = '';
	if (!empty($tip))
	{
		$tipScript .= '<script type="text/javascript">';
		$tipScript .= "jQuery(document).ready(function () {jQuery('$addtipto').hover(function (e) {";
		$tipScript .= "jQuery(this).append('$tip');";
		$tipScript .= "jQuery('$placeclass').css('pageY',jQuery('$tipadsorbent').css('pageY'));";
		$tipScript .= "}, function () {jQuery('$placeclass').remove();});});";
		$tipScript .= '</script>';
	}
	return $tipScript;
	
}

//8.0.9
function show_bullet_screen_for_one_tooltips_free($tooltip_post_id,$m_keyword_id,$type)
{

    if (tooltips_pro_disable_tooltip_in_mobile_free())
    {
        return '';
    }
    
    $m_bulletscreen_result = '';
    $get_post_meta_bulletscreen_value_for_this_page = get_post_meta ( $tooltip_post_id, 'toolstipbulletscreentag', true );
    
    $tooltsip_get_post_meta_bulletscreen_value_for_this_page = '';
    if (! (empty ( $get_post_meta_bulletscreen_value_for_this_page ))) {
        $tooltsip_get_post_meta_bulletscreen_value_for_this_page = explode ( ',', $get_post_meta_bulletscreen_value_for_this_page );
    }
    
    
    if ((!(empty($tooltsip_get_post_meta_bulletscreen_value_for_this_page))) && (is_array($tooltsip_get_post_meta_bulletscreen_value_for_this_page)) && (count($tooltsip_get_post_meta_bulletscreen_value_for_this_page) > 0))
    {
        $tooltsip_get_post_meta_bulletscreen_value_for_this_page = array_filter($tooltsip_get_post_meta_bulletscreen_value_for_this_page);
    }
    //end 16.0.2
    
    if ((! (empty ( $tooltsip_get_post_meta_bulletscreen_value_for_this_page ))) && (is_array ( $tooltsip_get_post_meta_bulletscreen_value_for_this_page )) && (count ( $tooltsip_get_post_meta_bulletscreen_value_for_this_page ) > 0))
    {
        $bulletsSpeed =	get_option("bulletsSpeed");
        if (empty($bulletsSpeed))
        {
            $bulletsSpeed = '2500';
        }
        $bulletsShowSelect = get_option("bulletsShowSelect");
        //$bulletsClassName = get_option("bulletsClassName");
        $bulletsClassName = 'bulletsClassName';
        $bulletsScreenWordsColor = get_option("bulletsScreenWordsColor");
        $bulletsOpacity = get_option("bulletsOpacity");
        $bulletsFontSize = get_option("bulletsFontSize");
        
        if (empty($bulletsShowSelect))
        {
            $bulletsShowSelect = 'mouseout';
        }
        
        if (empty($bulletsScreenWordsColor))
        {
            $bulletsScreenWordsColor = '#bf316f';
        }
        
        if ((strpos($bulletsScreenWordsColor,'#')) === false)
        {
            $bulletsScreenWordsColor = '#'.$bulletsScreenWordsColor;
        }
        
        if (empty($bulletsOpacity))
        {
            $bulletsOpacity = '1';
        }
        
        if (empty($bulletsFontSize))
        {
            $bulletsFontSize = '24px';
        }
        
        if ((strpos($bulletsFontSize,'px')) === false)
        {
            $bulletsFontSize = $bulletsFontSize.'px';
        }
        
        $m_bulletscreen_result = '<script type="text/javascript">';
        $m_bulletscreen_result .= 'jQuery("document").ready(function(){';  //!!!
        $m_bulletscreen_result .= " if (typeof(bulletscreentoolTips$m_keyword_id$type) == 'undefined')";
        $m_bulletscreen_result .= '{';
        $m_bulletscreen_result .= " var bulletscreentoolTips$m_keyword_id$type  = " . json_encode ( $tooltsip_get_post_meta_bulletscreen_value_for_this_page ) . ' ; ';
        $m_content = '';
        $m_bulletscreen_result .= "var bulletscreenindex$m_keyword_id$type = 0;";
        $m_bulletscreen_result .= 'jQuery(document).ready(function($) {';
        $m_bulletscreen_result .= "	$('.classtoolTips$m_keyword_id').".$bulletsShowSelect."(function(e) {";
        $m_bulletscreen_result .= "		var x = e.pageX,";
        $m_bulletscreen_result .= '		y = e.pageY;';
        $m_bulletscreen_result .= " bulletscreenindex$m_keyword_id$type = (bulletscreenindex$m_keyword_id$type + 1) % bulletscreentoolTips$m_keyword_id$type.length;";
        $m_bulletscreen_result .= ' $("<span class=\'bulletscreenfortoolTips\' />")' . ".text(bulletscreentoolTips$m_keyword_id$type" . "[bulletscreenindex$m_keyword_id$type])." . 'css({"z-index": "555555","top": y - 10,"left": x+10,"color": "'.$bulletsScreenWordsColor.'","font-size": "'.$bulletsFontSize.'",  "font-weight": "bolder","position": "absolute"}).appendTo("body").animate({"top": y - 380,"opacity": '.$bulletsOpacity.'},'.$bulletsSpeed.',function() {this.remove();});});});';
        $m_bulletscreen_result .= '}';
        $m_bulletscreen_result .= '});';  //!!!
        $m_bulletscreen_result .= ' </script>';
        
    }
    return $m_bulletscreen_result;
}
//end 8.0.9
