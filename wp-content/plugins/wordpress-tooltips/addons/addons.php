<?php
if (!defined('ABSPATH'))
{
	exit;
}

$enableLanguageCustomization = get_option("enableLanguageCustomization");
if ('YES' == $enableLanguageCustomization)
{
	if (file_exists(TOOLTIPS_ADDONS_PATH.'tooltips_languages.php'))
	{
		require_once TOOLTIPS_ADDONS_PATH.'tooltips_languages.php';
	}
}

//!!!start
$enableTooltipsForOceanWP = get_option("enableTooltipsForOceanWP");
if ('YES' == $enableTooltipsForOceanWP)
{
	if (file_exists(TOOLTIPS_ADDONS_PATH.'tooltips_for_oceanwp.php'))
	{
		require_once TOOLTIPS_ADDONS_PATH.'tooltips_for_oceanwp.php';
	}
}
//!!!end
