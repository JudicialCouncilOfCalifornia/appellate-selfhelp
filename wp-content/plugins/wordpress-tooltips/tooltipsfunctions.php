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
			update_option("onlyFirstKeyword",$_POST['onlyFirstKeyword']);
		}
		$tooltipsMessageString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageString);
	}
	
	
	$onlyFirstKeyword = get_option("onlyFirstKeyword");
	
	if (isset($_POST['enableTooltipsForImageSubmit']))
	{
		if (isset($_POST['enableTooltipsForImage']))
		{
			update_option("enableTooltipsForImage",$_POST['enableTooltipsForImage']);
		}
		$tooltipsMessageString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageString);		
	}

	$enableTooltipsForImage = get_option("enableTooltipsForImage");

	if (isset($_POST['enableTooltipsForExcerptSubmit']))
	{
		if (isset($_POST['enableTooltipsForExcerpt']))
		{
			update_option("enableTooltipsForExcerpt",$_POST['enableTooltipsForExcerpt']);
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
			update_option("disableInHomePage",$_POST['disableInHomePage']);
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
			update_option("showOnlyInSingleCategory",$_POST['cat']);
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
			update_option("toolstipsAnimationClass",$_POST['toolstipsAnimationClass']);
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
			update_option("enableTooltipsForTags",$_POST['enableTooltipsForTag']);
		}
		$tooltipsMessageString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageString);
	}
	$enableTooltipsForTag = get_option("enableTooltipsForTags");
	if (empty($enableTooltipsForTag)) $enableTooltipsForTag = 'NO';
	
	
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
	
	if (isset($_POST['toolstipsstylesetting']))
	{
		if (isset($_POST['selectedTooltipStyle']))
		{
			update_option("selectedTooltipStyle",$_POST['selectedTooltipStyle']);
		}
	
		$selectedDefaultTooltipStyle = $_POST['selectedTooltipStyle'];
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
			update_option("showToolstipsCloseButtonSelect",$_POST['showToolstipsCloseButtonSelect']);
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
			update_option("tooltipZindexValue",$_POST['tooltipZindexValue']);
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
			update_option("tooltipHookPriorityValue",$_POST['tooltipHookPriorityValue']);
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
			update_option("disabletooltipinhtmltag",$_POST['disabletooltipinhtmltag']);
		}
	
		$tooltipsMessageProString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageProString);
	}
	$disabletooltipinhtmltag = get_option("disabletooltipinhtmltag");
	if (empty($disabletooltipinhtmltag)) $disabletooltipinhtmltag = '';
//!!!start
	if (isset($_POST['enabletooltipsPopupCreditLinkInPopupWindowSetting']))
	{
		if (isset($_POST['enabletooltipsPopupCreditLinkInPopupWindow']))
		{
			update_option("enabletooltipsPopupCreditLinkInPopupWindow",$_POST['enabletooltipsPopupCreditLinkInPopupWindow']);
			$enabletooltipsPopupCreditLinkInPopupWindow = get_option("enabletooltipsPopupCreditLinkInPopupWindow");
		}
		$tooltipsMessageProString =  __( 'Changes saved', 'wordpress-tooltips' );
		tooltipsMessage($tooltipsMessageProString);
	}
	$enabletooltipsPopupCreditLinkInPopupWindow = get_option("enabletooltipsPopupCreditLinkInPopupWindow");
//!!!end
	
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
		<div style="clear:both"></div>
		<br />
<?php //!!!end ?>	 
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
				$m_toolstipskeyword = $wpdb->escape($_POST['toolstipskeyword']);
			}

			if (isset($_POST['toolstipscontent']))
			{
				$m_toolstipscontent = $wpdb->escape($_POST['toolstipscontent']);
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
				$m_toolstipskeyword = $wpdb->escape($_POST['toolstipskeyword']);
			}

			if (isset($_POST['toolstipscontent']))
			{
				$m_toolstipscontent = $wpdb->escape($_POST['toolstipscontent']);
			}
			
			if ((!(empty($m_toolstipscontent))) && (!(empty($m_toolstipskeyword))))
			{
				$m_added = false;
				$m_toolstipskeywordsubmithideen = $wpdb->escape($_POST['toolstipskeywordsubmithideen']);
				$m_tooltipsArray[$m_toolstipskeywordsubmithideen]['keyword'] = $m_toolstipskeyword;
				$m_tooltipsArray[$m_toolstipskeywordsubmithideen]['content'] = $m_toolstipscontent;  
				update_option('tooltipsarray',$m_tooltipsArray);
			}

			$tooltipsMessageString =  __( 'Changes saved', 'wordpress-tooltips' );
			tooltipsMessage($tooltipsMessageString);			
		}

		if (isset($_POST['toolstipskeywordsubmitdelete']))
		{
			$m_toolstipskeywordsubmithideen = $wpdb->escape($_POST['toolstipskeywordsubmithideen']);

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
		update_option('titleTooltipsControl',$wpdb->escape($_POST['HiddenTooltipsControl']));
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
	if (isset($_POST['tooltipsGlossaryIndexPage']))
	{
		update_option("tooltipsGlossaryIndexPage",$_POST['tooltipsGlossaryIndexPage']);
		flush_rewrite_rules();
	}

	if (isset($_POST['enabGlossaryIndexPage']))
	{
		update_option("enabGlossaryIndexPage",$_POST['enabGlossaryIndexPage']);
	}

	if (isset($_POST['enableLanguageForGlossary']))
	{
		update_option("enableLanguageForGlossary",$_POST['enableLanguageForGlossary']);
	}

	//!!!start
	if (isset($_POST['selectsignificantdigitalsuperscripts']))
	{
		update_option('selectsignificantdigitalsuperscripts',$_POST['selectsignificantdigitalsuperscripts']);
	}
	//!!!end
	$tooltipsMessageProString =  __( 'Changes saved', 'wordpress-tooltips' );
	tooltipsMessage($tooltipsMessageProString);
}
	$enabGlossaryIndexPage =  get_option("enabGlossaryIndexPage");
	$tooltipsGlossaryIndexPage = get_option('tooltipsGlossaryIndexPage');

	$enableLanguageForGlossary = get_option("enableLanguageForGlossary");

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
<?php //!!! start ?>
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
<?php //!!! end ?>
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

