<?php
if (!defined('ABSPATH'))
{
	exit;
}

function disableTooltipsFreeForGlossaryPage($post_id)
{
	$enableTooltipsForGlossaryPage = get_option("enableTooltipsForGlossaryPage");
	if (empty($enableTooltipsForGlossaryPage))
	{
		$enableTooltipsForGlossaryPage = 'YES';
	}

	if ($enableTooltipsForGlossaryPage == 'NO')
	{
		$now_posttype = get_post_type($post_id);
		if ('tooltips' == $now_posttype)
		{
			return true;
		}
	}
	return false;
}