<?php
if (! defined ( 'WPINC' )) {
	exit ( 'Please do not access our files directly.' );
}
function tooltips_free_howto_setting() 
{
	global $wpdb, $wp_roles;
	echo "<br />";

	$setting_panel_head = 'How To Use Wordpress Tooltips:';
	tooltips_free_setting_panel_head ( $setting_panel_head );

	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_1';
	$tooltips_free_how_to_bar_title = 'How to Install Wordpress Tooltip';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<h2>How to Install Wordpress Tooltip</h2>';
	$tooltips_free_how_to_bar_content .= '#1 Download Wordpress tooltips from <a href="https://wordpress.org/plugins/wordpress-tooltips/"  target="_blank">wordpress pligin page</a>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '#2 Upload the WordPress Tooltips plugin zip file to your site via <a href="'. get_option('siteurl').'/wp-admin/plugins.php" target="_blank">' .' plugins menu</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '#3 Activate the plugin "Tooltips" in '.'<a href="'. get_option('siteurl').'/wp-admin/plugins.php' .'"  target="_blank">' .' plugins page</a>';
	$tooltips_free_how_to_bar_content .= '</p>';	
	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );

	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_2';
	$tooltips_free_how_to_bar_title = 'How to Add / Edit / Delete Wordpress Tooltip?';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<h2>How to Add / Edit / Delete Wordpress Tooltip</h2>';
	$tooltips_free_how_to_bar_content .= 'It is very easy: ';
	$tooltips_free_how_to_bar_content .= '<p>';	
	$tooltips_free_how_to_bar_content .= '#1 If you want to add/edit/delete tooltips, please log in wordpress admin panel.';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '#2 Click '.'<a href="'. get_option('siteurl').'/wp-admin/edit.php?post_type=tooltips" target="_blank">'.'“Tooltips”'. '</a> Menu, You can editor/delete all existed tooltips in “All Tooltips” Sub Menu';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '#3 You can add new tooltip in “Add New” sub menu.';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '#4 Please check our video tutorial at: '. ' <a href="https://tooltips.org/wordpress-tooltips-video-tutorial-4-how-to-create-your-first-tooltips-in-wordpress-tooltips-pro-plugin/" target="_blank">'. 'WordPress Tooltips Video Tutorial 4: How to Create Your First Tooltips'.'</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';	
	$tooltips_free_how_to_bar_content .= '</div>';

	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );

	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_3';
	$tooltips_free_how_to_bar_title = 'How Wordpress Tooltips Works?';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<h2>How Wordpress Tooltips Works?</h2>';
	$tooltips_free_how_to_bar_content .= 'In back end, you can '.'<a href="'. get_option('siteurl').'/wp-admin/post-new.php?post_type=tooltips" target="_blank">'.'“add new tooltip”'. '</a> in wordpress standard tooltip editor.';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'We will use the title of the tooltip post as the keyword of your tooltips, and use the content of the tooltip post as the content of your tooltips.';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'For example: If you use “wordpress” as post title, and use “we love wordpress” as the post content, when users view your post, they will find the word “wordpress” with a dotted line under it, and when user move over the word “wordpress”, the tooltip box will popup and show the tooltip content “we love wordpress”';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'In Frontend, our plugin will scan content of wordpress posts / pages, and add tooltips effect on tooltip keyword automatically';
	$tooltips_free_how_to_bar_content .= '</p>';	
	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );


	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_2021082001';
	$tooltips_free_how_to_bar_title = 'WordPress Tooltips Video Tutorial 4: How to Create Your First Tooltip?';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<h2>How to Create Your First Tooltip</h2>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'You can find full text description with video / images / screenshots of WordPress Tooltips Video Tutorial 4: How to Create Your First Tooltip in here:';
	$tooltips_free_how_to_bar_content .= '<br />';
	$tooltips_free_how_to_bar_content .= '<a href="https://tooltips.org/wordpress-tooltips-video-tutorial-4-how-to-create-your-first-tooltips-in-wordpress-tooltips-pro-plugin/" target="_blank">https://tooltips.org/wordpress-tooltips-video-tutorial-4-how-to-create-your-first-tooltips-in-wordpress-tooltips-pro-plugin/</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );
	
	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_4';
	$tooltips_free_how_to_bar_title = 'Wordpress Tooltips Plugin Settings';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<h2>Wordpress Tooltips Plugin Settings</h2>';
	$tooltips_free_how_to_bar_content .= '#1 Please log in wordpress admin panel.';
	$tooltips_free_how_to_bar_content .= '<p>';	
	$tooltips_free_how_to_bar_content .= '#2 Please click "Tooltips" Menu, then click  '.'<a href="'. get_option('siteurl').'/wp-admin/edit.php?post_type=tooltips&page=tooltipglobalsettings" target="_blank">'.'“Global Settings”'. '</a> sub menu item.';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '#3 You will open "Tooltips Global Settings" panel, you will find we have added detailed tips for each options, just mouse hover each tips, you will finish all settings quickly.';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );
	
	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_5';
	$tooltips_free_how_to_bar_title = 'How to use [tooltips] shortcode';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<h2>How to use [tooltips] shortcode</h2>';
	
	$tooltips_free_how_to_bar_content .= '#1 Just like we reported at above, wordpress tooltip will sacn your wordpress posts and add tooltips effect on tooltip terms automatically.';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '#2 But you can create tooltips manually via shortcode [tooltips] too, by this way, you can add any tooltips which is not in content of post, or not in wordpress database';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '#3 It is very easy to use the shortcode, for example: [tooltips keyword=”wordpress” content=”WordPress is great system”]';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';	
	$tooltips_free_how_to_bar_content .= '#4 You can find demo and more detailed description at '.'<a href="https://tooltips.org/how-to-use-wordpress-tooltip-shortcode-tooltips-to-add-tooltips-manually/" target="_blank">'.'“How to use wordpress tooltip shortcode [tooltips] to add tooltips manually?”'. '</a> sub menu item.';
	$tooltips_free_how_to_bar_content .= '<br />';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );
	
	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_6';
	$tooltips_free_how_to_bar_title = 'How to use [tooltipslist] shortcode';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<h2>How to use [tooltipslist] shortcode</h2>';
	$tooltips_free_how_to_bar_content .= '#1 You can insert the [tooltipslist] shortcode into any wordpress standard posts / pages to list all of tooltips in one page.';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '#2 WIth this free version, you can '. '<a href="https://tooltips.org/how-can-i-customize-the-tooltip-list-page-glossary-page/" target="_blank">'. 'customize the tooltip list page / glossary page in css files'.'</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );
	
//!!!
	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_17';
	$tooltips_free_how_to_bar_title = 'How to use [glossary] shortcode';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<h2>How to use [tooltipslist] shortcode</h2>';
	
	$tooltips_free_how_to_bar_content .= '#1 Our wordpress glossary plugin have a shortcode [glossary], just insert this wordpress golssary shortcode into any wordpress post or page, you will make a glossary page quickly';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '#2 You can check glossary demo at our '. '<a href="https://tooltips.org/glossary-demo/" target="_blank">'. 'wordpress glossary plugin demo page'.'</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );
//!!!
	
	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_7';
	$tooltips_free_how_to_bar_title = 'Wordpress Tooltip Keyword Matching Mode';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<h2>Wordpress Tooltip Keyword Matching Mode</h2>';
	$tooltips_free_how_to_bar_content .= '<p>';	
	$tooltips_free_how_to_bar_content .= 'Please check: '. '<a href="https://tooltips.org/wordpress-tooltips-video-tutorial-7-wordpress-tooltip-keyword-matching-mode/" target="_blank">'. 'Wordpress Tooltips Video Tutorial 7:  Wordpress Tooltip Keyword Matching Mode'.'</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );

	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_8';
	$tooltips_free_how_to_bar_title = 'Enable/Disable Wordpress Tooltips for Images';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<h2>How to enable or disable wordpress tooltips on images</h2>';
	$tooltips_free_how_to_bar_content .= '<p>';	
	$tooltips_free_how_to_bar_content .= 'Please check: '. '<a href="https://tooltips.org/wordpress-tooltips-video-tutorial-8-enabledisable-wordpress-tooltips-for-images/" target="_blank">'. 'Wordpress Tooltips Video Tutorial 8:  Enable/Disable Wordpress Tooltips for Images'.'</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );
	

	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_9';
	$tooltips_free_how_to_bar_title = 'Wordpress Tooltips For Image Setting';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<h2>Wordpress Tooltips For Image Setting</h2>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'Please check: '. '<a href="https://tooltips.org/wordpress-tooltips-video-tutorial-9-wordpress-tooltips-for-image-setting/" target="_blank">'. 'Wordpress Tooltips Video Tutorial 9:  Wordpress Tooltips For Image Setting'.'</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );
	
	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_10';
	$tooltips_free_how_to_bar_title = 'Image Tooltip Guide: How to Use “REL” As Image Tooltip Content via WordPress GutenBerg Editor';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'Please check: '. '<a href="https://tooltips.org/image-tooltip-guide-how-to-use-rel-as-image-tooltip-content-via-wordpress-gutenberg-editor/" target="_blank">'. 'Image Tooltip Guide: How to Use “REL” As Image Tooltip Content via WordPress GutenBerg Editor'.'</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );

//!!!start

	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_11';
	$tooltips_free_how_to_bar_title = 'How to Enable / Disable Tooltip Glossary Index Page';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'Enable glossary index page will improve your SEO rank, a demo of glossary index please check: '. '<a href="https://tooltips.org/glossary/page/2/" target="_blank">'. 'Glossary Index Page Demo'.'</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'You can enable glossary index page by select "Enable Glossary Index Page" in "Enable Glossary Index Page" option via <a href="'. get_option('siteurl').'/wp-admin/edit.php?post_type=tooltips&page=glossarysettingsfree" target="_blank">' .'"Glossary Settings" plugins menu</a>';
	$tooltips_free_how_to_bar_content .= '</p>';	

	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'Then we will generate glossary index page automatically, each glossary term have their own link, by default, you will find glossary index page at <a href="'. get_option('siteurl').'/glossary" target="_blank">' .'Glossary Index Page</a>';  
	$tooltips_free_how_to_bar_content .= '</p>';
	
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'you can disable this glossary index page and hidden links of each glossary terms by select "Disable glossary index page" in "Enable Glossary Index Page" option via <a href="'. get_option('siteurl').'/wp-admin/edit.php?post_type=tooltips&page=glossarysettingsfree" target="_blank">' .'"Glossary Settings" plugins menu</a> ';
	$tooltips_free_how_to_bar_content .= '</p>';
	
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );	
	
	
	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_12';
	$tooltips_free_how_to_bar_title = 'How to Select My Own Glossary Index Page, How to Delete Default Glossary Index Page';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'You can select any wordpress page as glossary index page from select box in "Glossary Index Page" option via <a href="'. get_option('siteurl').'/wp-admin/edit.php?post_type=tooltips&page=glossarysettingsfree" target="_blank">' .'"Glossary Settings" plugins menu</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'In general, at the first time when you enabled glossary index page in "Enable Glossary Index Page" option, we will generate a new wordpress page "glossary" at <a href="'. get_option('siteurl').'/glossary" target="_blank">' .'Glossary Index Page</a>, if you do not like it, you can find it and delete it manually at <a href="'. get_option('siteurl').'/wp-admin/edit.php?post_type=page" target="_blank">' .'WordPress Page List</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );	
	
//!!!end
//!!!start
	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_13';
	$tooltips_free_how_to_bar_title = 'How to Use Language Addon to Custom Language of Your Glossary';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '#1 Please open "Tooltips" menu item, then click "Addons" sub menu item or click <a href="'. get_option('siteurl').'/wp-admin/edit.php?post_type=tooltips&page=tooltipsfreeaddonmanager" target="_blank">' .'"Addons" plugins menu</a>, you will find "Enable/Disable Tooltips Language Customization Addon" Panel, please just select "Enable Tooltips Language Customization Addon"  option and click "Update Now" button to enable "Languages" addon, then please follow note on notice bar or please click  <a href="'. get_option("siteurl") .'/wp-admin/edit.php?post_type=tooltips&page=tooltipsFreeLanguageMenu">click here to customize languages for glossary</a> ';
	$tooltips_free_how_to_bar_content .= '</p>';
	
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '#2 Then you will open <a href="'. get_option("siteurl") .'/wp-admin/edit.php?post_type=tooltips&page=tooltipsFreeLanguageMenu">Custom Language of Tooltip and Glossary Panel</a>, you can custom the word "ALL" on glossary Nav Bar, and we will add more features in here to help you custom glossary language better ';
	$tooltips_free_how_to_bar_content .= '</p>';

	$languageselectboxURL = get_option('siteurl'). '/wp-admin/edit.php?post_type=tooltips&page=glossarysettingsfree';
	//7.9.3 $title = "please select '<a href='$languageselectboxURL' target='_blank'>custom my language</a>' option in <a href='$languageselectboxURL' target='_blank'>language selectbox</a> first )</i></p>";
	$title = "please select '<a href='".esc_url($languageselectboxURL)."' target='_blank'>custom my language</a>' option in <a href='".esc_url($languageselectboxURL)."' target='_blank'>language selectbox</a> first )</i></p>";
	
	$tooltips_free_how_to_bar_content .= '<p>';
	//7.9.3 $tooltips_free_how_to_bar_content .= '#3 Please note, for use the function of "Custom Language of Tooltip and Glossary " you need setting your language from "English" to <a href="'.$languageselectboxURL.'" target="_blank">custom my language</a> option in <a href="'.$languageselectboxURL.'" target="_blank">language selectbox</a> first, in "language selectbox", we will support more and more language later. ';
	$tooltips_free_how_to_bar_content .= '#3 Please note, for use the function of "Custom Language of Tooltip and Glossary " you need setting your language from "English" to <a href="'.esc_url($languageselectboxURL).'" target="_blank">custom my language</a> option in <a href="'.$languageselectboxURL.'" target="_blank">language selectbox</a> first, in "language selectbox", we will support more and more language later. ';
	$tooltips_free_how_to_bar_content .= '</p>';	
	
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '#4 You can find more glossary settings via our video tutorial with detailed text explanation at: '. ' <a href="https://tooltips.org/wordpress-tooltips-video-tutorial-11-wordpress-glossary-settings/" target="_blank">'. 'WordPress Tooltips Video Tutorial 11: WordPress Glossary Settings'.'</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	
	
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );
	
//!!!end	
	//!!!start
	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_2020053001';
	$tooltips_free_how_to_bar_title = 'How to Enable / Disable WordPress Tooltips in WordPress Glossary Page?';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'Please check: '. '<a href="https://tooltips.org/how-to-enable-disable-wordpress-tooltips-in-wordpress-glossary-page/" target="_blank">'. 'How to Enable / Disable WordPress Tooltips in WordPress Glossary Page?'.'</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';

	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );
	
	//!!!end
	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_2020072001';
	$tooltips_free_how_to_bar_title = 'WordPress Tooltips Video Tutorial 10: Import Wordpress Tooltips From csv';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<h2>Import Wordpress Tooltips From csv</h2>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'You can find full text description with video / images / screenshots of WordPress Tooltips Video Tutorial 10: Import Wordpress Tooltips From csv in here:';
	$tooltips_free_how_to_bar_content .= '<br />';
	$tooltips_free_how_to_bar_content .= '<a href="https://tooltips.org/wordpress-tooltips-video-tutorial-10-import-wordpress-tooltips-from-csv/" target="_blank">https://tooltips.org/wordpress-tooltips-video-tutorial-10-import-wordpress-tooltips-from-csv/</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';	
	$tooltips_free_how_to_bar_content .= '</div>';

	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );
	//!!!start
	
	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_20220503';
	$tooltips_free_how_to_bar_title = 'WordPress Tooltip Plugin Document Page?';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<h2>WordPress Tooltip Plugin Document Page at https://tooltips.org?</h2>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'Please check our detailed step by step <b>document</b> with video / screenshot at: '. '<a href="https://tooltips.org/wordpress-tooltip-plugin/wordpress-tooltip-plugin-document/" target="_blank">'. 'WordPress Tooltip Plugin Document Page'.'</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'Also you can search tooltip tips in top search bar :)';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );
	
	//!!!end//tooltips_pro_howto_setting_panel ( $tooltips_pro_default_how_to_bar_id,$tooltips_pro_how_to_bar_title,$tooltips_pro_how_to_bar_content );


	
//!!!start

	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_16';
	$tooltips_free_how_to_bar_title = 'WordPress Tooltip Plugin F.A.Q Page?';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<h2>WordPress Tooltip Plugin F.A.Q Page at https://tooltips.org?</h2>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'Please check it at: '. '<a href="https://tooltips.org/faq/" target="_blank">'. 'WordPress Tooltip Plugin F.A.Q Page'.'</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'You can find detailed step by step <b>document</b> with video / screenshot at: '. '<a href="https://tooltips.org/wordpress-tooltip-plugin/wordpress-tooltip-plugin-document/" target="_blank">'. 'WordPress Tooltip Plugin Document Page'.'</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '</div>';
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );
	
//!!!end
	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_14';
	$tooltips_free_how_to_bar_title = 'How to Get Support and Demos?';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<h2>How to Get Support From Our Official Site: tooltips.org?</h2>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'Please submit ticket at: '. '<a href="https://tooltips.org/support-ticket/" target="_blank">'. 'Support Ticket'.'</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'Demos can be found at: '. '<a href="https://tooltips.org/wordpress-tooltip-plugin/wordpress-tooltips-demo/" target="_blank">'. 'Try Demo'.'</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= '<font color="gray"><i>(Might include description of free and pro features)</i></font>';
	$tooltips_free_how_to_bar_content .= '</p>';	
	$tooltips_free_how_to_bar_content .= '</div>';

	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );
	
//!!!start
	$tooltips_free_default_how_to_bar_id = 'tooltips_knowledge_15';
	$tooltips_free_how_to_bar_title = 'How to Upgrade to the WordPress Tooltips Pro?';
	
	$tooltips_free_how_to_bar_content = '';
	$tooltips_free_how_to_bar_content .= '<div style="padding: 30px 20px 20px 20px;">';
	$tooltips_free_how_to_bar_content .= '<h2>How to Upgrade to the WordPress Tooltips Pro?</h2>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'It is always safe to update from tooltip free to tooltip pro, you can just upload wordpress tooltip pro plugin via  "Plugins" > "Add New" in WordPress admin area, then activate it,  tooltips pro  will detect your version and upgrade it safely automatically, then deactivate your tooltips free version automatically';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '<p>';
	$tooltips_free_how_to_bar_content .= 'If you want to ensure you will in zero risk, please read our document at: '. '<a href="https://tooltips.org/how-to-upgrade-to-the-newest-wordpress-tooltips-pro/" target="_blank">'. 'How to Upgrade to the Newest WordPress Tooltips Pro'.'</a>';
	$tooltips_free_how_to_bar_content .= '</p>';
	$tooltips_free_how_to_bar_content .= '</div>';
//!!!end
	
	tooltips_free_howto_setting_panel ( $tooltips_free_default_how_to_bar_id,$tooltips_free_how_to_bar_title,$tooltips_free_how_to_bar_content );

}
function tooltips_free_howto_setting_panel($tooltips_free_how_to_bar_id, $tooltips_free_how_to_bar_title = '',$tooltips_free_how_to_bar_content = '') 
{
	global $wpdb, $wp_roles;
	?>
<div class="wrap">
	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder">
			<div id="post-body">
				<div id="dashboard-widgets-main-content">
					<div class="postbox-container" style="width: 90%;">
						<div class="postbox tooltips-pro-how-to-each-bar" id="tooltips-pro-how-to-each-bar-id" data-user-role="<?php echo esc_attr($tooltips_free_how_to_bar_id) ?>">
							<?php 
							/*
							 * 7.9.3
							 * <div class="postbox tooltips-pro-how-to-each-bar" id="tooltips-pro-how-to-each-bar-id" data-user-role="<?php echo $tooltips_free_how_to_bar_id ?>">
							 * <span id='bp-members-pro-compent-plus-<?php echo $tooltips_free_how_to_bar_id; ?>'>+</span>
							 */
							?>
							<span id='bp-members-pro-compent-plus-<?php echo esc_attr($tooltips_free_how_to_bar_id); ?>'>+</span>
							<h3 class='hndle'
								style='padding: 10px; ! important; border-bottom: 0px solid #eee !important;'>
	<?php
	echo $tooltips_free_how_to_bar_title;
	?>
									</h3>

						</div>
						<?php 
						/*
						 *7.9.3 
						 * 						<div class="inside tomas-tooltips-howto-settings postbox"
							style='padding-left: 10px; border-top: 1px solid #eee;'
							id=<?php echo $tooltips_free_how_to_bar_id ?>>
						 */
						?>
						<div class="inside tomas-tooltips-howto-settings postbox" style='padding-left: 10px; border-top: 1px solid #eee;' id=<?php echo esc_attr($tooltips_free_how_to_bar_id) ?>>
							<?php echo $tooltips_free_how_to_bar_content; ?>
							<br />
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div style="clear: both"></div>
<?php
}

function tooltips_free_setting_panel_head($title)
{
	?>
		<div style='padding-top:5px; font-size:22px;'><?php echo $title; ?></div>
		<div style='clear:both'></div>
<?php 
}

tooltips_free_howto_setting();


