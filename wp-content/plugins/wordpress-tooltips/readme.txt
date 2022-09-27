=== Wordpress Tooltips ===
Contributors: zhuyi
Author URI: https://tooltips.org/
Donate link: https://paypal.me/sunpayment
Tags:tooltip,glossary,dictionary,woocommerce,knowledge base
Requires at least: 3.8
Tested up to: 6.0
Stable tag:trunk
License: GPLv3 or later

Add custom tooltip automatically for post's content/title/tag/excerpt/gallery/menu, easily add image / video / audio / social/link tooltips

== Description ==
Responsive Wordpress Tooltips:

Wordpress Tooltip

[youtube http://www.youtube.com/watch?v=3PFnHXl1h34]

Wordpress Glossary

[vimeo https://player.vimeo.com/video/239822819]

When user hovers the pointer to over an item, a tooltip box will appear -- you can add text, image, video, radio, audio, social links... in tooltip box, you can add tooltip in post title / post content / post excerpt / post tags / wordpress archive / wordpress menu items / gallery... and so on.

Wordpress Tooltip supports glossary too, just insert shortcode [glossary] in any page or post, screenshot can be found at here https://ps.w.org/wordpress-tooltips/assets/screenshot-6.png.

Wordpress Tooltip is a simple & quick & light & powerful jQuery tooltip solution, it is very easy to use, you can easily add any HTML content via wordpress standard WYSWYG editor, for example, pdf download link, video, audio, image, social link... and so on.

You can manage all tooltip keyword / tooltip content centrally in one admin panel easily and quickly. Just input keyword in keyword field and video / radio / text/ image/ links in wordpress tooltip WYSWYG editor, wordpress tooltip plugin will detect your tooltip keyword at front end: post content, post title, post tags.. and so on, and add tooltip effect on tooltip terms automatically.

In general, you do not need a tooltip shortcode, because wordpress tooltips plugin will add tooltips effect for tooltip terms automatically, but you can use tooltip and glossary shortcode like this:
Shortcode [tooltips]:
`[tooltips keyword="wordpress tooltips" content="Wordress Tooltips is a rich featured wordpress tooltip plugin."]`
or
`[tooltips content="WordPress Tooltips is a rich featured wordpress tooltip plugin"]wordpress tooltips[/tooltips]`

Shortcode [tooltip_by_id]:
`[tooltip_by_id tooltip_id='222']` 
‘222’ is the postid of tooltip, you can find the id in WYSWYG tooltips editor, by this way, you can edit multimedia content in wordpress tooltip WYSWYG editor, and insert tooltip effect with multimedia into the page builder ( gutenberg / elementor / beaver / visual composer...), or wordpress posts, or other amazing plugins (woocommerce, wpform... ) through shortcode [tooltip_by_id] 

Shortcode [glossary] and [tooltiplist] -- you can insert this shortcode into any wordpress post to build a glossary quickly 

More details please check <a href='https://tooltips.org/how-to-use-wordpress-tooltip-shortcode-tooltips-to-add-tooltips-manually/' target='blank'>document of wordpress tooltip shortcode</a> 

For gallery users for example next-gen, you do not need to do anything, our wordpress tooltip plugin will detect next-gen description and show it as a tooltip when user hovering on next-gen gallery images, also our plugin support many other gallery/slideshow plugins too, you can set up tooltip manage options in Tooltips Global Settings panel.

Wordpress tooltips support many other amazing plugins too, users told us they are adding wordpress tooltips effect for elementor page builder, tablepress, wpform, pricing table, contact form 7, woocommerce... and so on :)  

<h4>Live Demo of Wordpress Tooltips Pro:</h4>

<li><a href='http://tooltips.org/wordpress-image-tooltips-demo/' target='blank'>WordPress Image Tooltip Demo</a></li>
<li><a href='http://tooltips.org/wordpress-audio-tooltips-demo/' target='blank'>WordPress Audio Tooltip Demo</a></li>
<li><a href='http://tooltips.org/wordpress-video-tooltips-demo/' target='blank'>WordPress Video Tooltip Demo</a></li>
<li><a href='https://tooltips.org/how-to-customize-the-style-for-each-tooltip-with-wordpress-tooltips-pro/' target='blank'>Customize Different Style For Each Tooltip</a></li>
<li><a href='https://tooltips.org/contact-us/' target='blank'>Tooltip For Each Form Fields Demo, Just Hover Over Form Fields</a></li>
<li><a href='http://tooltips.org/wordpress-image-tooltips-demo/' target='blank'>Tooltip To Menu Item Demo, Check "Try Demo" Menu Item</a></li>
<li><a href='https://tooltips.org/product/show-tooltips-in-woocommerce-products/' target='blank'>Tooltip in WooCommerce Product Demo</a></li>
<li><a href='https://tooltips.org/how-to-add-multimedia-tooltip-to-an-image-using-tooltip-shortcode/' target='blank'>Use Video / Video / Image As Tooltip For An Image</a></li>
<li><a href='http://tooltips.org/add-wordpress-tooltips-in-wordpress-post-title-demo/' target='blank'>WordPress Add Tooltip To Post Title Demo</a></li>
<li><a href='http://tooltips.org/add-wordpress-tooltips-in-wordpress-post-tag/' target='blank'>WordPress Add Tooltip To Post Tag Demo</a></li>
<li><a href='https://tooltips.org/wordpress-glossary-demo/' target='blank'>WordPress Glossary Demo</a></li>
<li><a href='https://tooltips.org/add-tooltips-for-table/' target='blank'>Add Tooltip In Table Demo</a></li>
<li><a href='https://tooltips.org/tooltips-for-pricing-table/' target='blank'>Add Tooltip In Pricing Table Demo</a></li>
<li><a href='https://tooltips.org/tooltips-for-button/' target='blank'>Add Tooltip In Button Demo</a></li>
<li><a href='https://tooltips.org/bullet-screen/' target='blank'>Bullet Screen Demo</a></li>
<li><a href='https://tooltips.org/qr-code-tooltip/' target='blank'>QR Code Tooltip</a></li>

Our wordpress tooltip plugin has tooltip customization API so it has ability to integrate other amazing themes / plugins / platforms with our wordpress tooltip plugin

> #### There are more amazing features in our wordpress tooltip plugin, for example:
> * 4 preset tooltip color schemes: Yellow, Light, Dark, Green. In tooltip settings panel in admin, support one click to select tooltip color schemes
> * By default, wordpress tooltip plugin will add tooltip effect in post automatically, also the tooltip plugin support inserts tooltip in post manually via tooltip shortcode too.
> * Responsive tooltip, our wordpress tooltip plugin works well in mobile devices
> * Options to show tooltip animation effects
> * Options to enable/disable Tooltip for images
> * Options to enable/disable Tooltip for excerpt
> * Options to enable/disable tooltip in post tags
> * Options to enable / disable "Tooltip Close Button"
> * Options to set up tooltip z-index Value via range slider 
> * Options to set up tooltip Hook Priority Value via range slider, by this way wordpress tooltips can support functionality of other wordpress plugins better, and show content which generated by other plugins(which followed WP the_content API) in tooltips popup window
> * Tooltip keyword Matching Mode option: You can select show tooltip to the first matching tooltip terms in the same page or add tooltip effect to all match keyword in the same page
> * Wordpress tooltip will auto detect tooltip position -- tooltip will not out Of screen, for example, if the tooltip word's position is on the head of the page and some section of tooltip content is out of screen, our tooltip will shown on the bottom center of the tooltip word and tooltip content will be truncated or out of screen.
> * Tooltip Synonyms, you can enter all synonyms in tooltip editor at one time, and our tooltip plugin will detect all these synonyms and add same tooltip contents for these synonyms
> * When mouse hovers the tooltip terms, show highlight color with a transition effect on tooltip terms
> * Show Tooltip to only one single category, you can set up to show tooltip only in 1 category from a category dropdown, or show tooltip in whole site wide. tooltip shortcode is not limited so there are still having a chance to customize it manually
> * Options to disable specific tooltip effect in html tags, for example, h1, h2, h3, a, p.... and more, so you can disable tooltip in widget, or disable tooltip in links... and so on
> * Options to enable / disable tooltips in entire site
> * Options to move wordpress tooltip inline javascript code to wordpress footer to increate page speed of wordpress pages.
> * Drag the tooltip widget to appear in the sidear and we will list all tooltip in wordpress tooltip widget.
> * Support tooltip categories
> * Support category archive tooltip
> * Create tooltip manually via shortcode [tooltips] -- by this way, you can add any tooltips which are not in content of post, or not in wordpress database. It is very easy to use: [tooltips keyword="wordpress" content="Wordpress is great system"], also you can use img tag to add images to tooltips content manually
> * Build-In Tooltip Global Settings
> * Create tooltip list page easily with shortcode [tooltipslist]
> * Create glossary page easily with shortcode [glossary]
> * Options to enable / disable images in glossary page
> * Options to enable glossary index page to add links for each tooltip term and improve SEO rank, by default, we will show glossary index page and each glossary term has their own links for improve SEO rank, you will find glossary index page at http://yourdomain.com/glossary, but you can disable this glossary index page and hide links for each glossary term by select "Disable glossary index page".
> * Options to select glossary index page, by default, the glossay index page will be "glossary", but you can select one wordpress page as your "Glossary index page", from a selectbox which listed all pages.
> * Options to enable / disable tooltip in glossary index page and glossary term pages.
> * Glossary index page, **SEO friendly**:By default, our plugin will generate a powerful glossary index page at http://yourdomain.com/glossary/, you can change it to any your pages, just one click in glossary setting panel, glossary index page support page navigation, it is SEO friendly and help you get better SEO rank, also you can enable / disable tooltip links in tooltip popup box to improve SEO rank
> * Glossary / Directory / List support multi-language: English, Swedish, German, French, Finnish,Spanish..., you can chose your language in optional settings
> * In glossary index page, options to "Significant Display of Digital Superscripts on Navigation Bar" or Not
> * Option to hide images in glossary list page
> * Option to show or hide numbers in glossary navigation bar: by default, users will find the glossary navigation bar at the top of the glossary page, users can click letters and numbers to select words or phrases that start with a specific letter or number ny clicking on those letters or numbers. And you can set up to show or hide numbers in glossary settings panel.
> * Option to disable wordpress tooltips in wordpress glossary page
> * Option to decide WordPress glossary searchable or not
> * Tooltip glossary language addon to help you translate glossary word from English to your mother language
> * Option to disable tooltip on mobile devices
> * Support many amazing theme, for example, enfold, divi... and so on, for some advanced theme, we offer tooltip addons, for example: Tooltip for OceanWP Theme addon
> * Create unlimited tooltip as much as you like
> * Support multiple tooltip on single page.
> * Admin-friendly -- we added guide tips for each setting option, when you hover the question mark of a setting option, a text description will be shown to tell you the usage of this setting option.
> * Super easy implementation
> * Minified css and javascript codes to speed up wordpress glossage pages load time
> * Knowledge Base menu item to help you uderstand how to use wordpress tooltips quickly. 
> * Support translate wordpress tooltips plugin in content and launch localized versions, .po files can be found in languages folder
> * more...

---

Just one minute test, you will find our plugin is easy to use and user friendly, you are very welcome to comment to request new requirement / features at: 
<a href='https://tooltips.org/features-of-wordpress-tooltips-plugin/' target='blank'>WordPress Tooltips Features</a>  :)

More amazing features? Do you want to customize a beautiful style for your tooltips? Get <a href='http://tooltips.org' target='blank'>Wordpress Tooltips Pro</a>  now.

<h4>More Features of Wordpress Tooltips Pro:</h4>

> #### Pro Version Features
>
> [Pro Version Detailed Feature List](<a href='https://tooltips.org/features-of-wordpress-tooltips-plugin/' target='blank'>WordPress Tooltips Features</a>)
>
>
&#9989;&nbsp; * Build pretty tooltip quickly, **Fine-grained custom tooltips style**, just a few click to custom tooltips style in panel: font family, font color, background color, border color, opacity, width, position, shadow, tooltips underline style, animation effects, margin, padding, title, close button..., more than 30 customization options help you build awesome tooltips. 
&#9989;&nbsp; * Custom unique pretty style for each tooltip, each tooltip can have their own "Tooltip Box Background","Tooltip Box Width", "Tooltip Font Color","Tooltip Text Align", "Tooltip Box Padding", "Tooltip Class Name", "Tooltip Border Radius", "Border Width", "Tooltip Border Color", "Tooltips Border Bottom", "Tooltip Underline Color", "Tooltips Shadow", "Tooltip Font Size", "Tooltips Line Height", "Tooltip Term Color", "Tooltips Popup Animation", "Title Background Color", "Tooltip Title Font Size", "Title Font Color", "Close Button Background","Close Button Radius", "Close Button Font Color"... and more.
&#9989;&nbsp; * Easily add any HTML content via wordpress standard WYSWYG editor, Support unlimited number of tooltips and tooltips categories
&#9989;&nbsp; [Build a colorful and varied and graceful tooltips site super easy and fast](https://tooltips.org/features-of-wordpress-tooltips-plugin/)
&#9989;&nbsp; * Responsive, Mobile devices friendly: wordpress tooltips works well on Android, iOS,Tablet and other mobile devices.
&#9989;&nbsp; * Show rich media content in tooltip box: video, audio/song, image/photo, advertising, links/text, google map, QR code..., also you can insert shortcode in tooltip to expand more amazing features via 3rd plugins
&#9989;&nbsp; * Support **show tooltips in many famouse wordpress plugins**, for example show tooltips on WooCommerce shop, show tooltips in contact form 7 fields, tooltips in Pricing Tables, tooltips in Tables, ACF Tooltip support show tooltips for ACF ( Advanced Custom Fields ) fields in front end, support tooltips in HTML5 Responsive FAQ, tooltips in Buttons, tooltips in Buddypress activity, tooltips in bbPress forum, works well with Visual Composer(VC), tooltips for NinjaForms, tooltips for woocommerce and tooltips for a few 3rd woocommerce plugins ...
&#9989;&nbsp; * Image Tooltips: [image tooltip](https://tooltips.org/wordpress-image-tooltips-demo/) support add tooltip effect to slideshow / gallery plugins, in back end, you setup image / gallery / slideshow  keyword matching mode, for example next-gen gallery mode or ALT attribute mode or Title attribute mode or REL attribute mode... and so on
&#9989;&nbsp; * Video tooltip & Audio tooltip: in tooltips editor, you can insert [videos tooltip](https://tooltips.org/wordpress-video-tooltips-demo/) directory, a more easier method is use 'Insert video into tooltips' metabox, you can insert youtube video by ID, insert video by URLs, set up video height, width... and so on
&#9989;&nbsp; * Menu tooltips:You can add tooltips into menu items, sub menu items, [please check our menu on ](http://tooltips.org).
&#9989;&nbsp; * Form Tooltips: support [Tooltips for Forms](https://tooltips.org/hire-us/), You Can add tooltip for each form elements, please check [form tooltips demo](https://tooltips.org/contact-us/), when hover over any forms fields, you will get notes about that form field in tooltip popup box, and each tooltip popup box has their unique style
&#9989;&nbsp; * WooCommerce tooltips: opt to add [WooCommerce tooltip](https://tooltips.org/product/show-tooltips-in-woocommerce-products/) effect on WooCommerce product page, WooCommerce shop page, woocommerce product title, WooCommerce product attribute tab... and so on automatically, also support some woocommerce 3rd plugins for example, "Product Specifications for WooCommerce", "WooCommerce Product Bundles"... and more. Also with advanced woocommerce tooltip addon, support one click to enable / disable Tooltips for WooCommerce Product Title, enable / disable  Tooltips for WooCommerce Product Short Description,enable / disable  Tooltips for WooCommerce Product Attribute,enable / disable  Tooltips for WooCommerce Product Bundles Plugin,enable / disable  Tooltips for WooCommerce Product Specifications Plugin.
&#9989;&nbsp; * WooCommerce glossary: Addon to display / hide glossary in woocommerce product tabs, opt to display all glossary terms or glossary terms from specified glossary category, to help users better understand the characteristics and uses of the woocommerce product 
&#9989;&nbsp; * Bullet Screen Tooltip: support [Bullet Screen](https://tooltips.org/bullet-screen/) effect for tooltips in post content
&#9989;&nbsp; * WikiPedia Tooltip: allow the automatic introduction of Wikipedia content as content of tooltip terms, just input wikipedia keyword, wordpress tooltip will pull content from wikipedia terms via wikipedia standard API automatically,  by this way you can build Lexicon / Vocabulary / Wiki / encyclopedia / dictionary / Translate / Encyclopaedia / Knowledge Base site or pages very fast and easily
&#9989;&nbsp; * SVG Tooltip: support [SVG Icon Tooltip](https://tooltips.org/how-to-build-a-svg-tooltip/) effect, SVG icons defined vector-based icons in XML format, it is super easy to edit all things for the SVG icons, for example,  color, animate… and so on, SVG Icons, is  a block of code, so the sizes of SVG icons is very small, with our SVG tooltip addon, you can add tooltip effects to SVG icons.   
&#9989;&nbsp; * Option to add tooltip in post title/archive: please check demo at http://tooltips.org/add-wordpress-tooltips-in-wordpress-post-title-demo/
&#9989;&nbsp; * Option to add tooltip in post content/excerpt/post tags: please check demo at http://tooltips.org/add-wordpress-tooltips-in-wordpress-post-tag/
&#9989;&nbsp; * Option to only add tooltips for specified post types: for example, opt to add tooltips for wordpress post, and add tooltips in faq post type in faq plugin, but don't show tooltips for woocommerce product post type, and so on.
&#9989;&nbsp; * Support tooltips for WordPress comment
&#9989;&nbsp; * Multi language / UTF8 supported for tooltips: support any alphabets / language.
&#9989;&nbsp; * Multilingual: support Polylang multilingual plugin, add tooltips in each Polylang language automatically, support WPML multilingual plugin, in WPML String Translation, you can translate each tooltips pro setting to your languages.
&#9989;&nbsp; * Quick load speed:everything is CSS, no image are used.
&#9989;&nbsp; * 7 preset stylesheet and beautiful tooltip color schemes: White, Blue, Light, Dark, Red, Cream, Green.
&#9989;&nbsp; * 5 preset Glossary stylesheet and beautiful color schemes:Blue, Blonde, Dark, Red, Green, change your glossary template via one click in glossary setting panel.
&#9989;&nbsp; * Support multiple tooltips popup animation effects, for example: wiggle, scale, 360 degree rotation,rotateY vertical Y-axis, you can set each tooltip to have its own animation effect.  
&#9989;&nbsp; * Multi trigger method: You can select show/hide your tooltips when:  Mouse Over, Double Click, Click,  Mouse Leave,  Mouse Enter,  Mouse Out,  Mouse Move,  Mouse Up,  Mouse Down... and more
&#9989;&nbsp; * Opt to enable / disable wordpress tooltip for images
&#9989;&nbsp; * Support use image as 'image tooltip' for another image, you can add an image as tooltips on another image, also you can custom many attributes of image tooltips, for example,  image tooltip underline color, hide advance image tooltips border bottom .... and so on
&#9989;&nbsp; * Very easy to choose the color intuitively from color picker, support tooltips box backgroud color, tooltips font color, tooltips border color
&#9989;&nbsp; * Option to set up tooltip z-index Value via range slider
&#9989;&nbsp; * Option to set up tooltip Hook priority value via range slider, by this way wordpress tooltips can support other wordpress plugins better, to add tooltip effect on content which generated by other plugins.
&#9989;&nbsp; * Option to show excerpt or full content of tooltip terms in tooltip pop up box
&#9989;&nbsp; * Option to enable tooltips case sensitive, case sensitive supprot synonyms tooltips too
&#9989;&nbsp; * Option to enable / disable tooltips for specified pages, when you edit posts, you can opt to not adding tooltips from database into the content of the post automatically. But you still can add tooltips manually via shortcode [tooltips], by this way, you can add different tooltips content to the same tooltip term.
&#9989;&nbsp; * Option to disable specific wordpress tooltip in specific page: when you edit posts, you will find post meta box which allow users disable any specific tooltips in this page. This tooltip meta box suppport autocomplete effect, time saver.
&#9989;&nbsp; * Option to show wordpress tooltip to specific category: you can opt to show tooltips only in specific category from category select box, or show tooltips in entire site-wide.
&#9989;&nbsp; * Option to enable / disable wordpress tooltip in entire site-wide.
&#9989;&nbsp; * Option to custom the display time of tooltip Pop-ups
&#9989;&nbsp; * Option to disable specific tooltip effect in specific html tags, for example, h1, h2, h3, a, p.... and more, by this way, you can opt to disable wordpress tooltip from H2 title, or disable tooltips in links... and so on
&#9989;&nbsp; * Option to enable / disable close button in tooltip popup box 
&#9989;&nbsp; * Option to "Close Button Background", you can setting close button Background color from color picker
&#9989;&nbsp; * Option to "Close Button Font Color", you can setting close button Font color from color picker
&#9989;&nbsp; * Option to "Close Button Radius", you can setting close button radius from color picker
&#9989;&nbsp; * Option to display / hide tooltip title bar in tooltip popup window, at tooltips style customize panel   
&#9989;&nbsp; * Option to "Title Background Color", via color picker. The color will be used as tooltip title backgound color in tooltip popup window
&#9989;&nbsp; * Options to "Tooltip Title Font Size", in tooltips style customize panel
&#9989;&nbsp; * Options to "Title Font Color" via color picker.
&#9989;&nbsp; * Options to regenerate all tooltips style automatically via one click, include global tooltip template or customized templates for each tooltip
&#9989;&nbsp; * Option to Enable or Disable tooltips in site home page: Some themes show recent posts in home page, for keep the home page more cleaner, you can stop show tooltips in home page automatically. Of course we still keep the ability to add a few specified tooltips in home page, you can still use Tooltips Shortcode to insert tooltips manually in the home page
&#9989;&nbsp; * Detect and fix your error automatically: Our plugin will detect what you filled in the customize style panel, for example, if you forget input "px" for the width or "#" in your color, WordPress Tooltips would still work well.
&#9989;&nbsp; * have customization API so it has ability to integrate other amazing themes/plugins/platforms for example wiki, google translate and so on.
&#9989;&nbsp; * Change underline style of tooltips terms: For example double line, dotted, dashed, single line... and so on.
&#9989;&nbsp; * Change underline color of tooltips terms: Via color picker, so you can customize underline color to match your theme and color schemes/
&#9989;&nbsp; * Show Tooltips Shadow of tooltips box: users can setting show shadow for tooltips popup box or not
&#9989;&nbsp; * Keyword Matching Mode: You can select "Add tooltips to the first matching keyword in the same page" or "Add tooltips to all match keyword in the same page".
&#9989;&nbsp; * Support tooltip synonyms:you can enter all synonyms in tooltips editor at one time, and our tooltips plugin will detect all these synonyms and add same tooltips content for these synonyms automatically
In wordpress tooltips standard editor, you can find synonyms setting meta box at top right of the editor
&#9989;&nbsp; * Multiple tooltip positioning options: bottomRight, bottomLeft, topRight, topLeft, topMiddle, bottomMiddle, rightMiddle, leftMiddle... 
&#9989;&nbsp; * Automatic positioning: tooltip position is calculated automatically, for example, if you select "topMiddle" as tooltip box position, our tooltips box will show at top center of the tooltip keyword in front end, or let's say if you select "bottomMiddle" as tooltip box position, our tooltips box will show at the bottom center of the tooltip keyword.
&#9989;&nbsp; * Auto detects tooltip position -- tooltips will not out of screen, for example, if the tooltip word's position is on the head of the page and some section of tooltip content is out of screen, our tooltip will shown on the bottom center of the tooltip word and tooltip content will be truncated or out of screen.
&#9989;&nbsp; * Any post types and pages which follows wordpress standard the_content API will be supported automatically
&#9989;&nbsp; * Tooltips Stats Report -- In back end, you can see how many hists for each tooltip, you will know which tooltip on your sites is most popular on user side.
&#9989;&nbsp; * Multiple browsers supported.
&#9989;&nbsp; * Easy to use and install tooltips plugin: Dim-witted system, all things just need 5 seconds, you don't need to edit older posts because tooltips works automatically, just input the content in tooltip management page one time and it will works on all articles automatically, this is really cool if you have an older blog with lots of articles,you need to do is just active it and it will works well automatically,
&#9989;&nbsp; * Easy to manage tooltips: In plugin setting panel, you can add / edit / delete any tooltips, you can add new tooltips categories, assign tooltips to categories, add image, video, audio, poll, links... and so on for each tooltips, you can trace each tooltips' hit stats and you will know which tooltips is most popular in your site... and so on
&#9989;&nbsp; * Detailed wordpress tooltip documentation and Video Tutorial 
&#9989;&nbsp; * Support wordpress text tooltips and wordpress image tooltips for Elementor Page Builder automatically
&#9989;&nbsp; * Support wordpress tooltip for WordPress Beaver Page Builder automatically
&#9989;&nbsp; * User-friendly: pretty dotted keywords will catch your readers eye and keep the style of your site and not harass your readers.
&#9989;&nbsp; * Import & Export your tooltips from csv:our import function will detect duplicated tooltis terms autimatically, so you do not need filter and remove tooltips terms in csv file. If you need a sample tooltips csv file, please check the file "sample.csv" in tooltips-pro folder.
&#9989;&nbsp; * Create tooltips manually via **shortcode [tooltips]** to display the tooltip [Sitewide Manually](https://tooltips.org/how-to-use-wordpress-tooltip-shortcode-tooltips-to-add-tooltips-manually/) , you can add tooltips for [WooCommerce Product](https://tooltips.org/product/show-tooltips-in-woocommerce-products/) , [in Table Cell](https://tooltips.org/add-tooltips-for-table/) , [on Button](https://tooltips.org/tooltips-for-button/) , [in Pricing Table](https://tooltips.org/tooltips-for-pricing-table/) ... and more
&#9989;&nbsp; * Options to enable advance wordpress tooltips shortcode mode, for shortcode [[tooltips]], you can enable advance tooltips shortcode in post editor, then our advance tooltips shortcode will support multimedia content, for example, image / audio / video... and so on in tooltips shorcode
&#9989;&nbsp; * Options to enable advance wordpress tooltip shortcode globally, so users do not need to manually enable the enable advance tooltip shortcode in metaobox of in the wordpress editor
&#9989;&nbsp; * Options to Display / Hide Tooltips Underline or not
&#9989;&nbsp; * Options to [Delay Pop-up like Wikipedia](https://tooltips.org/wordpress-tooltips-glossary-plugin-13-2-8-released-option-to-have-a-delay-on-the-wordpress-tooltip-popup-as-wikipedia-does/)
&#9989;&nbsp; * Shortcode [tooltip_by_id]: you can add tooltips in front end by id like this: [tooltip_by_id tooltip_id='222'], 222 is tooltip id
&#9989;&nbsp; * Create tooltips list page easily with **shortcode [tooltipslist]**
&#9989;&nbsp; * Create tooltips Glossary page easily with **shortcode [glossary]**
&#9989;&nbsp; * Glossary shortcode: it is very easy to use, just enter the shortcode [glossary] in any page or post, you will own a glossary system. [[glossary]] shortcode support enable / disable glossary search form on the top of glossary list
&#9989;&nbsp; * Support Multiple Glossary in Your Site: You can  show glossary of specified glossary categories in glossary page, by glossary category id or by glossary categories name, it looks like this: [glossary catid='1,2,3'], or [glossary catname='classmate,family,school']  
&#9989;&nbsp; * Easy to **custom Glossary style**:In Glossary settings panel, you can customize font size of glossary navigation bar item, font size of glossary navigation bar selected item, font size of glossary term..., one click to custom the wordpress glossary background color of glossary term or glossary content from color picker,  customize color of wordpress glossary border, glossary term underline color, background color of glossary title, glossary term color, glossary content color...  and so on
&#9989;&nbsp; * Easy to manage Glossary Pages:In Glossary settings panel, you can enable / disable glossary index page and glossary term links by one click
&#9989;&nbsp; * Glossary Page supports list style or table style: you can use shortcode [tooltipstable] to generate a glossary page, this shortcode will generate a glossary which has two-column table, left is term, right is content of the term. Also you can use shortcode [tooltipslist] to generate a glossary page which with a tooltips list format
&#9989;&nbsp; * Glossary category filter / selector: In glossary index page, at the top of glossary items, user can select different glossary category from glossary category dropdown box, when user selected a category, glossary index page will only show related glossary in specified glossary category
&#9989;&nbsp; * Glossary Pagination: option to enable or disable glossary paginate navigation, and custom the numbers of glossary items in each glossary page
&#9989;&nbsp; * Options to enable / disable tooltips effect on glossary pages: In Glossary Settings panel, you can enable or disable tooltips effect on glossary pages by one click
&#9989;&nbsp; * Options to enable / disable tooltips effect on glossary content column or glossary term column in glossary table
&#9989;&nbsp; * Options to enable / disable glossary searchable or not, if you disable glossary searchable, it means glossary terms / tooltip terms will not show in wordpress standard search result.
&#9989;&nbsp; * Options to "Significant Display of Digital Superscripts on Navigation Bar" or Not
If you enable glossary searchable,it means glossary terms / tooltip terms will show in wordpress standard search result. 
&#9989;&nbsp; * Option to show excerpt or full content of glossary term in glossary list
&#9989;&nbsp; * Option to show glossary category filter in glossary terms archive page
&#9989;&nbsp; * Option to include or exclude numbers in glossary navigation bar
&#9989;&nbsp; * Option to bulk remove glossary terms from glossary directory
&#9989;&nbsp; * Option to hide the letters in the Glossary Nav Bar that don't have any tooltips associated with them, i.e., where the number of tooltips is zero...
&#9989;&nbsp; * Glossary / Directory / List support multi-language: English, Swedish, German, French, Finnish,Spanish,Russian, you can chose your language in optional settings
&#9989;&nbsp; * Tooltip Glossary Language addon: You can use language alphabet generator to generate your language alphabet, or just custom your own alphabet based on your application scenarios, also you can generate numbers based on your language or application scenarios, or you can replace words in glossary bar for example replace the label "ALL", the label "More Details", the label "Home" in glossary bread crumb, the label "Glossary Category" in glossary filter...and so on to your own language 
&#9989;&nbsp; * Glossary index page, **SEO friendly**:By default, our plugin will generate a powerful glossary index page at http://yourdomain.com/glossary/, you can change it to any your pages, just one click in glossary setting panel, glossary index page support page navigation, it is SEO friendly and help you get better SEO rank, also you can enable / disable tooltip links in tooltip popup box to improve SEO rank
&#9989;&nbsp; * Glossary term pages, SEO friendly:Each glossary item have their own glossary page, the link structure looks like this: http://yourdomain.com/glossary/terms1/ , http://yourdomain.com/glossary/terms2/ , also in glossary term pages, you can opt to enable breadcrumbs...and so on, all things SEO-Friendly
&#9989;&nbsp; * Glossary Support Responsive:Our glossary works well on mobile devices
&#9989;&nbsp; * Via "How To Use Wordpress Tooltips" Panel, you can watch video tutorial and text document to learn how to use wordpress tooltip plugin, with detailed tooltip Knowledge Base.
&#9989;&nbsp; * When tooltips new version released, you will get plugin update notify on admin top bar
&#9989;&nbsp; * Optimized wordpress tooltips performance, speed up your site by: move all wordpress tooltips javascript function to the footer,  Generate tooltips css file, not just add inline css codes in the browser page, minified css and javascript codes
&#9989;&nbsp; * Optimized whole site performance, for example: remove nearly all tooltips javascript or css codes to speed up frontend pages
&#9989;&nbsp; * Support Custom WordPress Tooltip & Glossary & Bullet Screen Template CSS Coder in Tooltips Coder panel
&#9989;&nbsp; * All features includes in wordpress tooltip free version
&#9989;&nbsp; * **Lifetime Upgrades**, Unlimited Download ,Ticket Support: **only $9**, build a powerful and pretty tooltip and glossary system in 5 minutes
> * more...

---

<h4>Wordpress Tooltips Glossary Video Tutorial:</h4>

> #### Video Tutorials
>
>
>[WordPress Tooltips Video Tutorial 1: Download wordpress tooltips pro plugin](https://tooltips.org/wordpress-tooltips-video-tutorial-1-download-wordpress-tooltips-pro-plugin/)
>[WordPress Tooltips Video Tutorial 2: Deactivate tooltips free plugin](https://tooltips.org/wordpress-tooltips-video-tutorial-2-deactivate-tooltips-free-plugin/)
>[WordPress Tooltips Video Tutorial 3: How to upload and activate wordpress tooltips pro plugin](https://tooltips.org/wordpress-tooltips-video-tutorial-4-how-to-create-your-first-tooltips-in-wordpress-tooltips-pro-plugin/)
>[WordPress Tooltips Video Tutorial 4: How to Create Your First Tooltips In WordPress Tooltips Pro Plugin](https://tooltips.org/wordpress-tooltips-video-tutorial-5-how-to-use-7-preset-stylesheet-and-beautiful-color-schemes/)
>[WordPress Tooltips Video Tutorial 5: How to use 7 preset stylesheet and beautiful color schemes](https://tooltips.org/wordpress-tooltips-video-tutorial-6-custom-tooltip-box-color-font-underline-shadow-and-more/)
>[WordPress Tooltips Video Tutorial 6: custom tooltip box color, font, underline, Shadow...and more](https://tooltips.org/wordpress-tooltips-video-tutorial-7-wordpress-tooltip-keyword-matching-mode/)
>[WordPress Tooltips Video Tutorial 7: WordPress Tooltip Keyword Matching Mode](https://tooltips.org/wordpress-tooltips-video-tutorial-8-enabledisable-wordpress-tooltips-for-images/)
>[WordPress Tooltips Video Tutorial 8: Enable/Disable WordPress Tooltips for Images](https://tooltips.org/wordpress-tooltips-video-tutorial-9-wordpress-tooltips-for-image-setting/)
>[WordPress Tooltips Video Tutorial 9: WordPress tooltips for image setting](https://tooltips.org/wordpress-tooltips-video-tutorial-10-import-wordpress-tooltips-from-csv/)
>[WordPress Tooltips Video Tutorial 10: Import WordPress Tooltips From csv](https://tooltips.org/wordpress-tooltips-video-tutorial-11-wordpress-glossary-settings/)
>[WordPress Tooltips Video Tutorial 11: WordPress Glossary Settings](https://tooltips.org/wordpress-tooltips-video-tutorial-11-wordpress-glossary-settings/)
> * more...

---

More amazing features are being developed and you can upgrade unlimited, you are very welcome to submit your feature request at https://tooltips.org/contact-us/

== Installation ==

1:Upload the Wordpress Tooltips plugin to your blog
2:Activate it 
3:edit keyword and content in tooltips menu, it is very easy, If you want to add/edit/delete tooltips, please log in admin panel, Under "Tooltips" Menu, You can editor/delete all existed tooltips in "All Tooltips" Sub Menu, also you can add new tooltip in "Add New" sub menu.
we will use the title of the post as the keyword of your tooltips, and use the content of the post as the content of your tooltips, for example: If you use "wordpress" as post title, and use "we love wordpress" as the 
post content, when users view your post, they will find the word "wordpress" with a dotted line under it, and when user move over the word "wordpress", the tooltip box will popup and show the tooltip content "we love wordpress"
4: You can setting your tooltips in Tooltips Global Settings page at admin area, for example you can choose " Add tooltips to all matching keyword in the same page " or " Add tooltips to the first matching keyword in the same page" and so on
5: If you want to show glossary / list, just insert shortcode [tooltipslist] into your page or post.
1, 2, 3, 4, 5: You're done!

== Frequently Asked Questions ==
FAQs can be found here: https://tooltips.org/features-of-wordpress-tooltips-plugin/

== Screenshots ==

1. Image Tooltips, our WordPress Tooltip content can be video, audio/song, image, advertising, links/text.
2. Text Tooltips, Tooltip content with text and links
3. Easy To Add New Tooltips In Back End
4. Global Setting In Back End
5. Tooltips Edit/Delete/Update Panel In Back End
6. Shortcode [tooltipslist] Suport Glossary.
7. Tooltips Widget On Sidebar
8. Tooltips pro style setting options panel
9. Add tooltips widget in back end
10. Tooltips Categories in back end
11. Users hit count for each tooltips
12. Support WooCommerce product, also you can use Wordpress Tooltip to promote your products, such as embed an product link in the tooltip.
13. Plain text tooltip
14. Color picker, our tooltips plugin is easy to customize pretty tooltip style, just a few clicks.
15. Video Tooltips

== Changelog ==
= Version 7.9.9 =
please check change log at:
>[wordpress tooltip change log:](https://tooltips.org/change-log-of-wordpress-tooltips-free-plugin/)  

== Upgrade Notice ==


== Download ==

