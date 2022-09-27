=== PageSpeed Ninja ===

Contributors: pagespeed
Tags: page speed, optimizer, minification, gzip, render blocking css
Requires at least: 4.0.1
Tested up to: 5.5
Stable tag: 0.9.43
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Make your site load faster & fix PageSpeed issues with one click: Gzip compression, render blocking critical CSS/JavaScript, browser caching & more.


== Description ==

PageSpeed Ninja is the ultimate Wordpress performance plugin. You can make your site load faster on desktop and mobile, fixing Google PageSpeed Insights issues with one click.

* Easily enable Gzip file compression
* Fix render blocking CSS and JavaScript
* Improve Critical Rendering Path and auto-generate above-the-fold critical CSS
* Minify HTML, JavaScript and CSS files
* Combine and inline Javascript and CSS
* Defer loading of Javascript and CSS
* Optimize style / script order
* Compress all images to optimize size
* Defer images by lazy loading with optional low-quality image placeholders
* Leverage browser caching and server-side caching
* Optimize your images accurately for nearly 10,000 different mobile browsers thanks to the included AMDD database – one of the most comprehensive mobile device databases available.
* And MUCH more, based on 10+ years of experience in mobile-optimizing over 200 000 websites.

## Why PageSpeed Ninja?

We’ve been optimizing web on mobile for over a decade now (you might know one of our popular projects, [Lazy Load XT](https://github.com/ressio/lazy-load-xt) on Github). PageSpeed Ninja for Wordpress is the result of 10+ years of experience in optimizing the performance over 200 000 websites on mobile. We believe you won't find a similar, easy to use, all-in-one package of performance boosting features anywhere else.

We’ve built heaps of unique features to make sure your site loads super fast, like the above-the-fold critical CSS generation method, not seen in any other plugins.

We’d love your feedback – always feel free to send us your questions, thoughts and suggestions.

## Before you install

According to our stats, our plugin improves speed of 4 out of 5 sites. However in some cases, certain theme and plugin combinations (particularly related to caching and optimization) cause incompatibility issues. Therefore our plugin might not be suitable for everyone. In order for you to see how PageSpeed Ninja could work on your site, we created a simple tool where you can test your site before installation. **We highly recommend** you visit [PageSpeed.Ninja](http://pagespeed.ninja) and test your site beforehand.

## Uninstallation

When the plugin is deleted, it will automatically revert all settings on your site back to way they were before installing this plugin. It restores all optimized images and removes /s directory with optimized JS and CSS files. Also all changes in .htaccess files are reverted back.
Please note that this restoration may not work reliably if in the meanwhile there have been any conflicts with other plugins, e.g. if the other plugins dynamically create/edit/remove files (including the files backed up by this plugin).

## Feedback, Bug reports, Logging possible issues

We welcome all questions, comments, suggestions, issue reports. Contact us to be added to our private tester Facebook group.

PageSpeed Ninja logs all php errors in wp-content/plugins/pagespeedninja/includes/error_log.php file (see Troubleshooting section in Advanced tab of the PageSpeed Ninja settings page)
If you find a problem, would be great if you can also send that file along.


== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/psn-pagespeed-ninja` directory, or install the plugin through the WordPress plugins screen directly. We recommend taking a backup of your site first, just like with any other new plugin.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->PageSpeed Ninja screen to configure the plugin
4. After you have installed the plugin, navigate to Settings->PageSpeed Ninja and enable optimization levels suggested by Google's PageSpeed Insights. By default all optimizations are disabled. The plugin then optimizes images, JS and CSS files, and modifies .htaccess files as required to fix issues identified by Google PageSpeed Insights.


== Frequently Asked Questions ==

= Does this plugin have any conflicts with Yoast or any of the other SEO plugins out there? =

The PageSpeed Ninja plugin should work pretty well with most other plugins without issues. However, if some SEO plugins try to do some of the same things as this plugin, then conflicts could be possible especially if gzip compression is enabled. However that is pretty unlikely.


== Screenshots ==

1. See improvement suggestions in one place and fix with single click
2. Fine tune to get the best performance using advanced settings


== Upgrade Notice ==

None


== Changelog ==

= 0.9.43 Beta Release [29 August 2020]
- Fixed possible error during deactivation

= 0.9.42 Beta Release [29 August 2020]
- Fixed conflict with AMP plugin
- Fixed issue with infinite loading animation for local websites

= 0.9.41 Beta Release [6 July 2020]
- Fixed loading of PageSpeed Insights scores via API v5 (usability score is set to 100)
- Changed Google Fonts loading for "Flash of unstyled text" mode via display=swap

= 0.9.40 Beta Release [1 December 2019]
- Fixed compatibility with PHP 7.4 in CSSTidy minifier

= 0.9.39 Beta Release [13 November 2019]
- Fixed compatibility with WP 5.3
- Fixed URL parsing in "Optimize integrations"
- Fixed lazy image loading

= 0.9.38 Beta Release [30 April 2019]
- Fixed issue with exclusion of JavaScript files
- Fixed issue with priority of template_redirect action handler (resulted in conflict with Smart Slider 3)
- Fixed issue with processing of AJAX requests
- Fixed issue with page caching for logged users
- Fixed issue with file cache cleaner in the case of large time-to-live value
- Fixed work of "Configure the viewport" setting
- Fixed work of libxml-based HTML optimizer
- Added new setting to enable/disable optimization for logged users
- Added file exclusion in "Non-blocking Javascript", "Optimize integrations", "Load external stylesheets", and "Load external scripts"
- Registering of new WP images sizes is applied to the "Fit" image rescaling method only

= 0.9.37 Beta Release [12 February 2019]
- Fixed issue with Distribute Method: PHP

= 0.9.36 Beta Release [12 February 2019]
- Fixed file permissions

= 0.9.35 Beta Release [12 February 2019]
- Fixed issue with possible incorrect markup generation in DNS Prefetch and Google Fonts optimizations
- Fixed issue with WooCommerce caching
- Fixed issue with open_basedir enabled
- Updated AMDD device database for "Scale large images" feature
- Improved atomic file operations

= 0.9.34 Beta Release [21 December 2018]
- Fixed version number in WordPress repository

= 0.9.33 Beta Release [21 December 2018]
- Fixed issue in URL parser
- Fixed processing of inlined scripts in libxml-based HTML parser

= 0.9.32 Beta Release [29 November 2018] =
- Fixed processing of xml (e.g. in sitemap)
- Removed copyright headers from minified Lazy Load XT files
- Improvement of "Skip initialization scripts" setting

= 0.9.31 Beta Release [13 September 2018] =
- Fixed gzip compression for "headers sent" issue
- Fixed displaying of active preset name
- Fixed removing of empty directories in cache cleaner
- Fixed libxml HTML parser
- Added support of DONOTCACHEPAGE and DONOTMINIFY constants
- Improved performance of the Standard full HTML parser (Pharse library)
- Few minor fixes

= 0.9.30 Beta Release [16 July 2018] =
- Fixed conflict of "Manage CSS/Javascript URLs" and "Load external stylesheets/scripts" settings
- Fixed "Gzip compression" feature for cached pages
- Fixed internal caching TTL (detached from "Caching time-to-live" parameter)
- Automatic detection of gzip support during initial activation of the plugin

= 0.9.29 Beta Release [02 July 2018] =
- Fixed invalidation of expired page cache after clearing fragment cache
- Fixed invalidation of page cache after saving settings
- Fixed work with Beaver Builder and Massive Dynamic Builder
- Changed default cache time-to-live in presets
- "Generate srcset" feature is moved from experimental to stable

= 0.9.28 Beta Release [27 June 2018] =
- Fixed issue with image rescaling
- "Generate srcset" feature is moved from experimental to stable

= 0.9.27 Beta Release [25 June 2018] =
- Fixed external Above-the-fold CSS generation in backend
- Fixed issue with merging of non-existing files
- Fixed merging of JS/CSS URLs with hash-part in URL
- Fixed conflict with ob-handlers ordering
- Added experimental caching of optimized pages
- Added HTTPS support for all requests to pagespeed.ninja
- Added tooltips displaying for touch screens in Advanced tab
- Updated TidyCSS to ver. 1.5.7 from https://github.com/airyland/CSSTidy
- Updated presets
- Performance optimizations

= 0.9.26 Beta Release [14 June 2018] =
- Fixed "Flash of unstyled text" mode of Google Fonts loading
- Fixed position of the Support badge
- Fixed conflict with plugins that do ob_start() in 'template_redirect' action (by setting priority to 5)
- Fixed generation of absolute URLs in merged CSS files

= 0.9.25 Beta Release [09 May 2018] =
- Fixed javascript order with "Skip initialization scripts" option

= 0.9.24 Beta Release [08 May 2018] =
- Fixed URL quoting in CSS minification

= 0.9.23 Beta Release [07 May 2018] =
- Fixed Fast stream and libxml parsers
- Fixed work of Above-the-fold CSS with libxml parser

= 0.9.22 Beta Release [06 May 2018] =
- Fixed "Configure the viewport" feature
- Fixed "Load external files" feature
- Fixed clearing of Page Cache
- Fixed generation of above-the-fold CSS in the Advanced tab
- Fixed check for AMP pages
- Fixed processing of inlined <script> tags with CDATA wrapping
- Added new optimization feature: Skip initialization scripts
- Added support of Cache-Control:immutable header for generated files
- Updated AMDD database
- Default JPEG quality level is set to 85 (95 in Safe preset)
- Options to load external CSS and JS are moved to "Leverage browser caching" section

= 0.9.21 Beta Release [20 March 2018] =
- Fixed issue with editing of theme files
- Fixed loading and caching of external files
- Fixed backend rendering issues
- Fixed issue with onload/onerror attributes and async javascript loading
- Fixed issue with onload/onerror attributes and lazy image loading
- Fixed libxml HTML parser
- Fixed CSS parser
- Fixed URL minification in rel attribute of <link> tag (rel=stylesheet allowed only)
- Fixed gzip compression in the case of enabled ob_gzhandler
- Fixed uninstallation of advanced-cache.php
- Fixed issue with initialization of lazy image loading
- Fixed processing of "id" attribute in <script> tags
- Added select of preset in the after-install popup
- Added new settings preset: "Compact"
- Added descriptions for presets
- Improved compression of JPEG images
- Improved Troubleshooting section in Advanced settings
- Improved detection of the "Distribute method" after initial plugin activation
- Improved cleaning up of outdated cache files and directories
- Disabled optimization of AMP pages
- Disabled optimizations prior to apply profile preset

= 0.9.20 Beta Release [22 February 2018] =
- Fixed pre-check of free memory amount in GD image rescaling and optimizing
- Improved Imagick image compression
- Minor performance improvements
- Updated tooltips in Advanced settings

= 0.9.19 Beta Release [15 January 2018] =
- Fixed rebasing of CSS in the "Load external files" mode
- Fixed conflict of http and https caches
- Added option to merge embedded scripts and styles in <head> section only
- Added warning about conflict of advanced caching and WooCommerce

= 0.9.18 Beta Release [03 January 2018] =
- Fixed blank screen issue
- Fixed issue with incorrect URLs in optimized css files
- Improved Google Fonts loading

= 0.9.17 Beta Release [07 December 2017] =
- Fixed issue with Google Fonts loading

= 0.9.16 Beta Release [06 December 2017] =
- Caching of PageSpeed Insights scores
- Improved Google Fonts loading
- Fixed javascript processing in "Optimize integrations" feature
- Fixed lazy loading with some slider plugins
- Fixed issues with above-the-fold css and async css loading

= 0.9.15 Beta Release [15 November 2017] =
- Fixed issues with nonblocking CSS loader

= 0.9.14 Beta Release [14 November 2017] =
- Fixed merging of JavaScript
- Fixed merging of CSS
- Fixed CSS parser
- Fixed processing of @import in CSS optimizer
- Fixed parsing of <menu> tag in HTML5 parser
- Fixed nonblocking css and js in IE6-8
- Fixed lazy image loading in IE8
- Fixed conflict with few plugins that use lazy image loading
- Fixed issue with hidden switches in backend settings page
- Fixed conflict of the Masonry library and asynchronous css loading
- Added Autogeneration of srcset attribute for lazy image loading
- Added cache reset after post/page/attachment/theme changes
- Disabled optimization of comment feeds

= 0.9.13 Beta Release [10 October 2017] =
- Fixed backend interface
- Enabled optimizations by default
- Reset js/css cache after update

= 0.9.12 Beta Release [10 October 2017] =
- Fixed processing of @import rules in css files
- Fixed error in config reading
- Fixed Fatal error in libxml HTML parser
- Fixed Fatal error in loadATFCSS()

= 0.9.11 Beta Release [09 October 2017] =
- Fixed error message during uninstallation
- Fixed warning message in the case of disabled js and css minification
- Added lazy loading of iframes
- Updated presets
- Updated AMDD database
- Changed configuration file format to allow plugin to be translated to other languages

= 0.9.10 Beta Release [30 September 2017] =
- Fixed text domain slug
- Fixed issue with quoted keyframe name in css parser
- Fixed disabling of caching for logged-in users
- Fixed disabling of non-blocking js mode
- Improved estimation of required memory in image processing
- Reduced memory usage by css optimizer
- Switched remote connections to use download_url function

= 0.9.9 Beta Release [27 September 2017] =
- Marked as tested with WordPress 4.8.2
- Fixed undefined index in abovethefoldcss.php
- Removed unused jQLight option

= 0.9.8 Beta Release [27 September 2017] =
- Fixed render blocking issues
- Fixed image lazy loading with Fast simple HTML parser
- Fixed Google Fonts loading
- Added check of memory limit in image optimization and rescaling
- Added new lazy loading script (Lazy Load XT 2.0)
- Minor backend changes

= 0.9.7 Beta Release [06 September 2017] =
- Marked as tested with WordPress 4.8.1

= 0.9.6 Beta Release [05 September 2017] =
- Switched to native updating

= 0.9.5 Beta Release [27 August 2017] =
- Added optimization of srcset attribute in images
- Added support of HTTP/2 Server Push
- Fixed "Viewport width" feature
- Fixed "DNS prefetch" feature in the "Fast simple" HTML parser mode
- Fixed Google Font optimization

= 0.9.4 Beta Release [23 July 2017] =
- Added request to allow using of external pagespeed.ninja critical CSS service and to send usage data
- Removed update from versions prior to 0.8.23 (first public alpha release)
- Moved "Optimize Emoji loading" option to "Minify JavaScript" section

= 0.9.3 Beta Release [03 July 2017] =
- Fixed lazy image loading in the "stream" optimizer mode
- Improved settings page for small/medium screen width
- Colors of switches depend on diference between original and current scores
- Updated AMDD database

= 0.9.2 Beta Release [20 June 2017] =
- Added preview of results (without affecting website for other users)
- Added "Optimize Emoji Loading" feature
- Added "Google Fonts loading" feature
- Added "Skip first images" and "Noscript position" features to fine tune lazy image loading
- Added support of ImageMagick PHP extensions for image optimization
- Fixed processing of non-standard JPEG and PNG images
- Fixed CSS parser
- Fixed issue with merging of subsequent javascripts before </body>
- Fixed merging of Javascript and CSS in the "stream" optimizer mode
- Fixed merging of Javascript and CSS with "onload" attribute
- Fixed processing of <noscript> tags
- Fixed dnsprefetch generation
- Fixed timeout issue in the plugin activation
- Added set width and height attributes for lazy loading images
- Fixed loading of URLs starting with "//"
- Fixed settings page in older browsers
- Fixed several minor issues
- Improved performance of local above-the-fold css generation
- Google fonts are loaded synchronously by default
- Excluded Google Analytics from "Non-blocking Javascript" feature
- Default limits of inlined Javascript and CSS are set to 4096 bytes

= 0.9.1 Beta Release [07 April 2017] =
- Added "Clear Cache" and "Clear Database Cache" button to the Troubleshooting section
- Fixed Manage URLs feature in Troubleshooting section
- Fixed automatical cache clearing

= 0.9.0 Beta Release [04 April 2017] =
- New backend design
- "Troubleshooting" section in Advanced settings
- Fixed image lazy loading in "stream" html optimizer
- Fixed in-browser generation of above-the-fold css
- Fixed "Exclude files list" feature
- Added notification about unsaved changes
- Added notification about generated above-the-fold css
- Minor performance improvements
- Updated AMDD database

= 0.8.27 Alpha Release [01 December 2016] =
- Added server-side page caching implementation
- Fixed activation of image optimization and lazy loading settings
- Minor performance improvements
- Updated AMDD database

= 0.8.26 Alpha Release [08 November 2016] =
- Significant code refactoring
- Performance improvements
- Added "Experimental" preset
- Added loading animation for Google's Page Speed scores in backend
- Fixed PHP warnings in plugin activation/deactivation
- Fixed few Windows-related issues
- Removed "Avoid app install interstitials that hide content" section (removed by Google's Page Speed service)
- Moved image lazy loading settings to "Prioritize visible content" section

= 0.8.25 Alpha Release [07 October 2016] =
- Fixed few PHP warnings and notices
- Enabled logging for backend settings page and frontend pages only
- Added compatibility with caching plugins
- Added "Auto" option for "Load jQLight library" setting
- Other minor changes

= 0.8.24 Alpha Release [04 October 2016] =
- Added notice about compatibility with caching plugins
- Uninstall of the plugin deletes generated low-quality image placeholders and gzipped svg images

= 0.8.23 Alpha Release [21 September 2016] =
- Significant code refactoring
- Added error logging to includes/error_log.php
- Added "Low-quality image placeholders" setting
- Added "Vertical lazy loading threshold" setting
- Updated AMDD database
- Other minor changes

= 0.8.22 Alpha Release [14 July 2016] =
- First pre-public alpha release. Distributed privately.
