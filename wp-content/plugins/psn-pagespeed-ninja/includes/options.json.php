[
  {
    "title": "<?php _e('General'); ?>",
    "items": [
      {
        "name": "enablelogged",
        "title": "<?php _e('Enable for Logged Users'); ?>",
        "tooltip": "<?php _e('It\'s possible to enable optimization of pages for logged users too. Note that in this case page cache is disabled and other optimizations (HTML, styles, scripts, images) are enabled.'); ?>",
        "type": "checkbox",
        "default": 0,
        "presets": {
          "ultra": 1,
          "experimental": 1
        }
      },
      {
        "name": "htmloptimizer",
        "title": "<?php _e('HTML Parser'); ?>",
        "tooltip": "<?php _e('Choose between performance and optimal HTML code: Switch to a new libxml HTML parser or fast page optimizer with full JavaScript, CSS, and images optimization, but with limited subset of HTML optimizations (only supporting the removal of HTML comments and IE conditional comments).'); ?>",
        "type": "select",
        "values": [
          {
            "pharse": "<?php _e('Use Standard full HTML parser'); ?>"
          },
          {
            "stream": "<?php _e('Use Fast simple HTML parser'); ?>"
          },
          {
            "dom": "<?php _e('Use libxml HTML parser'); ?>"
          }
        ],
        "class": "streamoptimizer",
        "default": "stream",
        "presets": {
          "safe": "pharse",
          "experimental": "dom"
        }
      },
      {
        "name": "distribmode",
        "title": "<?php _e('Distribute method'); ?>",
        "tooltip": "<?php _e('Distribution method of the compressed JS and CSS files to the client device. Different methods perform better on different server setup: \'Direct\' method distributes them in the default method of the webserver (like any other files), but note that gzip compression and caching may be disabled (i.e. those are controlled by the webserver and PSN is not able to affect the settings nor to check is they are currently enabled or not.) \'Apache mod_rewrite + mod_headers\' is the fastest method, but requires Apache with both mod_rewrite and mod_headers modules enabled. \'Apache mod_rewrite\' and \'PHP\' are identical from the performance point of view; the only difference is that \'Apache mod_rewrite\' requires Apache webserver, while \'PHP\' generates not-so-beautiful URLs like /s/get.php?abcdef.js instead of just /s/abcdef.js.'); ?>",
        "type": "select",
        "values": [
          {
            "direct": "<?php _e('Direct'); ?>"
          },
          {
            "apache": "<?php _e('Apache mod_rewrite+mod_headers'); ?>"
          },
          {
            "rewrite": "<?php _e('Apache mod_rewrite'); ?>"
          },
          {
            "php": "<?php _e('PHP'); ?>"
          }
        ],
        "default": "direct"
      },
      {
        "name": "staticdir",
        "title": "<?php _e('Static files directory'); ?>",
        "tooltip": "<?php _e('Directory path for the stored combined JS and CSS files (relative to WordPress installation directory).'); ?>",
        "type": "text",
        "default": "/s"
      },
      {
        "name": "http2",
        "title": "<?php _e('HTTP/2 Server Push'); ?>",
        "tooltip": "<?php _e('Support HTTP/2 Server Push by using HTTP Link header (according to W3C Preload Working Draft). For webservers with HTTP/2 support, allows sending JS/CSS files to the browser without waiting for a direct request.'); ?>",
        "type": "checkbox",
        "default": 0
      },
      {
        "name": "footer",
        "title": "<?php _e('Support badge'); ?>",
        "tooltip": "<?php _e('Displays a small text link to the PageSpeed Ninja website in the footer (\'Optimized with PageSpeed Ninja\'). A BIG thank you if you use this! :).'); ?>",
        "type": "checkbox",
        "default": 0
      },
      {
        "name": "allow_ext_atfcss",
        "title": "<?php _e('Remote critical CSS generation'); ?>",
        "tooltip": "<?php _e('Allow the use of PageSpeed.Ninja critical CSS generation service on the PageSpeed Ninja server. When this setting is disabled, this plugin contains a simplified version of the generation tool that works directly in the browser, but using it requires you to manually visit the PageSpeed settings page to regenerate the critical CSS after each change to the website. Enabling this setting allows the use of the PageSpeed Ninja server to have the critical CSS regenerated automatically.'); ?>",
        "type": "checkbox",
        "default": 1
      },
      {
        "name": "allow_ext_stats",
        "title": "<?php _e('Send anonymous statistics'); ?>",
        "tooltip": "<?php _e('Send anonymous usage data to PageSpeed Ninja to help us further optimize the plugin for best performance.'); ?>",
        "type": "checkbox",
        "default": 1
      }
    ]
  },
  {
    "title": "<?php _e('Troubleshooting'); ?>",
    "items": [
      {
        "name": "errorlogging",
        "title": "<?php _e('Error logging'); ?>",
        "tooltip": "<?php _e('Log all PHP\'s errors, exceptions, warnings, and notices. Please check the content of this file and send it to us if there are messages related to PageSpeed Ninja plugin.'); ?>",
        "type": "errorlogging",
        "default": 0,
        "presets": {
        }
      },
      {
        "name": "do_clear_images",
        "title": "<?php _e('Images'); ?>",
        "tooltip": "<?php _e('Remove optimized images.'); ?>",
        "type": "do_clear_images",
        "default": ""
      },
      {
        "name": "do_view_static",
        "title": "<?php _e('Static Files'); ?>",
        "tooltip": "<?php _e('View size of static files (optimized JavaScript, CSS, and other generated files). To remove them, use Cache Clear Expired or Clear All buttons below.'); ?>",
        "type": "do_view_static",
        "default": ""
      },
      {
        "name": "do_clear_cache",
        "title": "<?php _e('Cache'); ?>",
        "tooltip": "<?php _e('Clear the internal cache files.'); ?>",
        "type": "do_clear_cache",
        "default": ""
      },
      {
        "name": "do_clear_pagecache",
        "title": "<?php _e('Page Cache'); ?>",
        "tooltip": "<?php _e('Clear the cache of optimized HTML pages.'); ?>",
        "type": "do_clear_pagecache",
        "default": ""
      },
      {
        "name": "do_clear_amdd",
        "title": "<?php _e('AMDD Database'); ?>",
        "tooltip": "<?php _e('Clear the cache of the mobile device database. AMDD is our proprietary mobile device database containing information on the capabilities of almost all mobile devices in the market, helping PageSpeed Ninja to deliver the best experience tailored for each client device.'); ?>",
        "type": "do_clear_amdd",
        "default": ""
      },
      {
        "name": "exclude_js",
        "title": "<?php _e('Manage Javascript URLs'); ?>",
        "tooltip": "<?php _e('Exclude the marked URLs from being merged and minified.'); ?>",
        "type": "exclude_js",
        "default": ""
      },
      {
        "name": "exclude_css",
        "title": "<?php _e('Manage CSS URLs'); ?>",
        "tooltip": "<?php _e('Exclude the marked URLs from being merged and minified.'); ?>",
        "type": "exclude_css",
        "default": ""
      }
    ]
  },
  {
    "id": "AvoidLandingPageRedirects",
    "title": "<?php _e('Avoid landing page redirects'); ?>",
    "type": "speed"
  },
  {
    "id": "EnableGzipCompression",
    "title": "<?php _e('Enable compression'); ?>",
    "type": "speed",
    "items": [
      {
        "name": "html_gzip",
        "title": "<?php _e('Gzip compression'); ?>",
        "tooltip": "<?php _e('Compress mobile pages using Gzip for better performance. Recommended.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
          "safe": 0
        }
      },
      {
        "name": "htaccess_gzip",
        "title": "<?php _e('Gzip compression in .htaccess'); ?>",
        "tooltip": "<?php _e('Update .htaccess files in wp-includes, wp-content, and uploads directories for better front-end performance (for Apache webserver).'); ?>",
        "type": "checkbox",
        "default": 0,
        "presets": {
          "compact": 1,
          "optimal": 1,
          "ultra": 1,
          "experimental": 1
        }
      },
      {
        "name": "html_sortattr",
        "title": "<?php _e('Reorder attributes'); ?>",
        "tooltip": "<?php _e('Reorder HTML attributes for better gzip compression. Recommended. Disable if there is a conflict with another extension that rely on an exact HTML attribute order.'); ?>",
        "type": "checkbox",
        "class": "streamdisabled",
        "default": 0,
        "presets": {
          "ultra": 1,
          "experimental": 1
        }
      }
    ]
  },
  {
    "id": "LeverageBrowserCaching",
    "title": "<?php _e('Leverage browser caching'); ?>",
    "type": "speed",
    "items": [
      {
        "name": "htaccess_caching",
        "title": "<?php _e('Caching in .htaccess'); ?>",
        "tooltip": "<?php _e('Update .htaccess files in wp-includes, wp-content, and uploads directories for better front-end performance (for Apache webserver).'); ?>",
        "type": "checkbox",
        "default": "auto",
        "presets": {
          "safe": 0,
          "compact": 1,
          "optimal": 1,
          "ultra": 1,
          "experimental": 1
        }
      },
      {
        "name": "css_loadurl",
        "title": "<?php _e('Load external stylesheets'); ?>",
        "tooltip": "<?php _e('Load external files for optimization and merging. Disable if you use external dynamically generated CSS files.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
          "safe": 0,
          "compact": 0
        }
      },
      {
        "name": "js_loadurl",
        "title": "<?php _e('Load external scripts'); ?>",
        "tooltip": "<?php _e('Load external files for optimization and merging. Disable if you use external dynamically generated JavaScript files.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
          "safe": 0,
          "compact": 0
        }
      },
      {
        "name": "img_loadurl",
        "title": "<?php _e('Load external images'); ?>",
        "tooltip": "<?php _e('Load external files for optimization. Disable if you use external dynamically generated JavaScript files.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
          "safe": 0,
          "compact": 0
        }
      }
    ]
  },
  {
    "id": "MainResourceServerResponseTime",
    "title": "<?php _e('Reduce server response time'); ?>",
    "type": "speed",
    "items": [
      {
        "name": "caching",
        "title": "<?php _e('Caching'); ?>",
        "tooltip": "<?php _e('Enable server-side page caching.'); ?>",
        "type": "cachingcheckbox",
        "default": 1,
        "presets": {
          "compact": 0
        }
      },
      {
        "name": "caching_processed",
        "title": "<?php _e('Experimental Caching'); ?>",
        "tooltip": "<?php _e('Extra caching level for optimized pages (may require more disk space).'); ?>",
        "type": "checkbox",
        "default": 0,
        "presets": {
          "ultra": 1,
          "experimental": 1
        }
      },
      {
        "name": "ress_caching_ttl",
        "type": "hidden",
        "default": 43200
      },
      {
        "name": "caching_ttl",
        "title": "<?php _e('Caching time-to-live'); ?>",
        "tooltip": "<?php _e('Caching time-to-live in minutes. Cached data will be invalidated after specified time interval. PageSpeed Ninja automatically invalidates cache when a new comment is added, a new post is published, theme is changed, etc. (I.e., this affects how frequently comments should be updated for unauthorized users.) 15 minutes is a reasonable time in most cases, but if commenting is disabled, this could be increased to one day (1440 mins).'); ?>",
        "type": "number",
        "units": "<?php _e('min'); ?>",
        "default": 1440,
        "presets": {
          "safe": 15,
          "ultra": 10080,
          "experimental": 10080
        }
      },
      {
        "name": "dnsprefetch",
        "title": "<?php _e('DNS Prefetch'); ?>",
        "tooltip": "<?php _e('Use DNS pre-fetching for external domain names. Disable if there is another plugin doing the same thing and there is a conflict with PageSpeed Ninja.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
          "safe": 0
        }
      }
    ]
  },
  {
    "id": "MinifyCss",
    "title": "<?php _e('Minify CSS'); ?>",
    "type": "speed",
    "items": [
      {
        "name": "css_merge",
        "title": "<?php _e('Merge CSS files'); ?>",
        "tooltip": "<?php _e('Merge several CSS files into single one for faster loading. Disable different pages load different CSS files.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
        }
      },
      {
        "name": "css_excludelist",
        "title": "<?php _e('Exclude files list'); ?>",
        "tooltip": "<?php _e('Exclude listed files (one per line) from being merged. Partial matching is used, so typing \'debug\' in this field will exclude all files containing \'debug\' as a part of their URL. It is recommended to list CSS files that are used only on one or a few pages (so that the single merge file containing CSS files included on every page stays smaller).'); ?>",
        "type": "textarea",
        "default": ""
      },
      {
        "name": "css_mergeinline",
        "title": "<?php _e('Merge embedded styles'); ?>",
        "tooltip": "<?php _e('Merge embedded CSS styles in &lt;style&gt;...&lt;/style&gt; blocks. Disable for dynamically-generated embedded CSS styles - though if the dynamic CSS is the same on all pages, this feature may be kept enabled. But if different pages have different embedded CSS, this feature should be disabled.'); ?>",
        "type": "select",
        "values": [
          {
            "0": "<?php _e('Disable'); ?>"
          },
          {
            "head": "<?php _e('In &lt;head&gt; only'); ?>"
          },
          {
            "1": "<?php _e('Everywhere'); ?>"
          }
        ],
        "default": "head",
        "presets": {
          "safe": "0",
          "compact": "0",
          "ultra": "1",
          "experimental": "1"
        }
      },
      {
        "name": "css_di_cssMinify",
        "title": "<?php _e('Minify CSS Method'); ?>",
        "tooltip": "<?php _e('Optimizes CSS for better performance. This optimizes CSS correspondingly (removes unnecessary whitespaces, unused code etc.). If there are any CSS issues, disable the minification (and wait for a plugin update).'); ?>",
        "type": "select",
        "values": [
          {
            "none": "<?php _e('None'); ?>"
          },
          {
            "ress": "<?php _e('RESS'); ?>"
          },
          {
            "csstidy": "<?php _e('CSS Tidy'); ?>"
          },
          {
            "both": "<?php _e('RESS + CSSTidy'); ?>"
          }
        ],
        "default": "ress",
        "presets": {
          "safe": "none",
          "ultra": "both",
          "experimental": "both"
        }
      },
      {
        "name": "css_minifyattribute",
        "title": "<?php _e('Minify style attributes'); ?>",
        "tooltip": "<?php _e('Optimizes CSS styles within \'style\' attributes. (Usually these attributes are short, and as such have insignificant effect on the HTML size, however the processing takes time and that may affect the total page generation time.)'); ?>",
        "type": "checkbox",
        "default": 0,
        "presets": {
          "ultra": 1,
          "experimental": 1
        }
      },
      {
        "name": "css_inlinelimit",
        "title": "<?php _e('Inline limit'); ?>",
        "tooltip": "<?php _e('Inline limit allows to inline small CSS (up to the specified limit) into the page directly in order to avoid sending additional requests to the server (i.e. speeds up loading). 1024 bytes is likely optimal for most cases, allowing inlining of small files while not inlining large ones.'); ?>",
        "type": "number",
        "units": "<?php _e('bytes'); ?>",
        "default": 4096,
        "presets": {
        }
      },
      {
        "name": "css_crossfileoptimization",
        "title": "<?php _e('Cross-files optimization'); ?>",
        "tooltip": "<?php _e('Optimize the generated combined CSS file.'); ?>",
        "type": "checkbox",
        "default": 0,
        "presets": {
          "ultra": 1,
          "experimental": 1
        }
      },
      {
        "name": "css_checklinkattributes",
        "title": "<?php _e('Keep extra link tag attributes'); ?>",
        "tooltip": "<?php _e('Don\'t merge a stylesheet if its \'link\' tag contains extra attribute(s) (e.g. \'id\', in rare cases it might mean that JavaScript code may refer to this stylesheet HTML node).'); ?>",
        "type": "checkbox",
        "default": 0,
        "presets": {
          "safe": 1
        }
      },
      {
        "name": "css_checkstyleattributes",
        "title": "<?php _e('Keep extra style tag attributes'); ?>",
        "tooltip": "<?php _e('Don\'t merge a stylesheet if its \'style\' tag contains extra attribute(s) (e.g. \'id\', in rare cases it might mean that javascript code may refer to this stylesheet HTML node)'); ?>",
        "type": "checkbox",
        "default": 0,
        "presets": {
          "safe": 1,
          "compact": 1,
          "optimal": 1
        }
      }
    ]
  },
  {
    "id": "MinifyHTML",
    "title": "<?php _e('Minify HTML'); ?>",
    "type": "speed",
    "items": [
      {
        "name": "html_mergespace",
        "title": "<?php _e('Merge whitespaces'); ?>",
        "tooltip": "<?php _e('Removes empty spaces from the HTML code for faster loading. Recommended. Disable if there is a conflict with the rule \'white-space: pre\' in CSS. (This is rarely needed, as usually the &lt;pre&gt; element is used for this behaviour, and PSN processes &lt;pre&gt; correctly by keeping all spaces in place.)'); ?>",
        "type": "checkbox",
        "class": "streamdisabled",
        "default": 1,
        "presets": {
          "safe": 0
        }
      },
      {
        "name": "html_removecomments",
        "title": "<?php _e('Remove comments'); ?>",
        "tooltip": "<?php _e('Removes comments from the HTML code for faster loading. Disable if there is a conflict with another plugin (e.g. a plugin which uses JavaScript to extract content of comments).'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
        }
      },
      {
        "name": "html_minifyurl",
        "title": "<?php _e('Minify URLs'); ?>",
        "tooltip": "<?php _e('Replaces absolute URLs (http://www.website.com/link) with relative URLs (/link) to reduce page size. Disable if there is a conflict with another plugin (e.g. plugin which uses JavaScript that depends on having the full URL in certain href attributes.).'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
        }
      },
      {
        "name": "html_removedefattr",
        "title": "<?php _e('Remove default attributes'); ?>",
        "tooltip": "<?php _e('Remove attributes with default values, e.g. type=\'text\' in &lt;input&gt; tag. It reduces total page size. Disable in the case of conflicts with CSS (e.g. \'input[type=text]\' selector doesn\'t match \'input\' element without \'type\' attribute).'); ?>",
        "type": "checkbox",
        "class": "streamdisabled",
        "default": 0,
        "presets": {
          "ultra": 1,
          "experimental": 1
        }
      },
      {
        "name": "html_removeiecond",
        "title": "<?php _e('Remove IE conditionals'); ?>",
        "tooltip": "<?php _e('Remove IE conditional commenting tags for non-IE browsers. Disable if there is a conflict with another plugin that relies on these tags.'); ?>",
        "type": "checkbox",
        "default": 0,
        "presets": {
          "ultra": 1,
          "experimental": 1
        }
      }
    ]
  },
  {
    "id": "MinifyJavaScript",
    "title": "<?php _e('Minify JavaScript'); ?>",
    "type": "speed",
    "items": [
      {
        "name": "js_merge",
        "title": "<?php _e('Merge script files'); ?>",
        "tooltip": "<?php _e('Merge several JavaScript files into a single one for faster loading. Recommended.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
        }
      },
      {
        "name": "js_excludelist",
        "title": "<?php _e('Exclude files list'); ?>",
        "tooltip": "<?php _e('Exclude listed files (one per line) from being merged. Partial matching is used, so \'tinymce\' line affects all URLs containing \'tinymce\' as a substring. If you have a JS file that is used on one or a few pages only and nowhere else, you can add it here so that PSN will not attempt to merge it with the other files.'); ?>",
        "type": "textarea",
        "default": ""
      },
      {
        "name": "js_mergeinline",
        "title": "<?php _e('Merge embedded scripts'); ?>",
        "tooltip": "<?php _e('Merge embedded JavaScript code in &lt;script&gt;...&lt;/script&gt; code blocks. Disable for dynamically-generated embedded JavaScript code.'); ?>",
        "type": "select",
        "values": [
          {
            "0": "<?php _e('Disable'); ?>"
          },
          {
            "head": "<?php _e('In &lt;head&gt; only'); ?>"
          },
          {
            "1": "<?php _e('Everywhere'); ?>"
          }
        ],
        "default": "head",
        "presets": {
          "safe": "0",
          "compact": "0",
          "ultra": "1",
          "experimental": "1"
        }
      },
      {
        "name": "js_autoasync",
        "title": "<?php _e('Auto async'); ?>",
        "tooltip": "<?php _e('Allows to relocate script tags for more effienct merging. Blocking scripts generates \'inplace\' HTML content and in general should not be relocated. Disable if you use blocking scripts, e.g. synchronous Google Adsense ad code.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
          "safe": 0
        }
      },
      {
        "name": "js_skipinits",
        "title": "<?php _e('Skip initialization scripts'); ?>",
        "tooltip": "<?php _e('Allows to skip short inlined initialization-like scripts (e.g. &lt;script&gt;var x=&quot;zzz&quot;&lt;/script&gt;) from merging and optimization.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
          "safe": 0
        }
      },
      {
        "name": "js_di_jsMinify",
        "title": "<?php _e('Minify Javascript Method'); ?>",
        "tooltip": "<?php _e('Optimizes JavaScript for better performance. This optimizes JavaScript correspondingly (removes unnecessary whitespaces, unused code etc.).'); ?>",
        "type": "select",
        "values": [
          {
            "none": "<?php _e('None'); ?>"
          },
          {
            "jsmin": "<?php _e('JsMin'); ?>"
          }
        ],
        "default": "none",
        "presets": {
          "ultra": "jsmin",
          "experimental": "jsmin"
        }
      },
      {
        "name": "js_minifyattribute",
        "title": "<?php _e('Minify event attributes'); ?>",
        "tooltip": "<?php _e('Optimizes JavaScript in event attributes (e.g. \'onclick\' or \'onsubmit\').'); ?>",
        "type": "checkbox",
        "class": "streamdisabled",
        "default": 0,
        "presets": {
          "ultra": 1,
          "experimental": 1
        }
      },
      {
        "name": "js_inlinelimit",
        "title": "<?php _e('Inline limit'); ?>",
        "tooltip": "<?php _e('Inline limit allows to inline small JavaScript (up to the specified limit) into the page directly in order to avoid sending additional requests to the server (i.e. speeds up loading)1024 bytes is likely optimal for most cases, allowing inlining of small JavaScript files while not inlining large files like jQuery.'); ?>",
        "type": "number",
        "units": "<?php _e('bytes'); ?>",
        "default": 4096,
        "presets": {
        }
      },
      {
        "name": "js_crossfileoptimization",
        "title": "<?php _e('Cross-files optimization'); ?>",
        "tooltip": "<?php _e('Optimize the generated combined JavaScript file. This option doubles the JavaScript optimization time (though the good news is that it is done only once) and should be enabled only if you really want to get the JS size down to as small as possible.'); ?>",
        "type": "checkbox",
        "default": 0,
        "presets": {
          "ultra": 1,
          "experimental": 1
        }
      },
      {
        "name": "js_wraptrycatch",
        "title": "<?php _e('Wrap to try/catch'); ?>",
        "tooltip": "<?php _e('Browsers stop the execution of JavaScript code if a parsing or execution error is found, meaning that merged JavaScript files may be stopped in the case of an error in one of the source files. This option enables the wrapping of each merged JavaScript files into a try/catch block to continue the execution after a possible error, but note that enabling this may reduce the performance in some browsers.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
        }
      },
      {
        "name": "js_checkattributes",
        "title": "<?php _e('Keep extra script tag attributes'); ?>",
        "tooltip": "<?php _e('Don\'t merge JavaScript if its \'script\' tag contains extra attributes (e.g. \'id\', in rare cases it might mean that JavaScript code may refer to this stylesheet HTML node).'); ?>",
        "type": "checkbox",
        "default": 0,
        "presets": {
          "safe": 1
        }
      },
      {
        "name": "js_widgets",
        "title": "<?php _e('Optimize integrations (Facebook. Google Plus, etc.)'); ?>",
        "tooltip": "<?php _e('Optimize the loading of popular JavaScript widgets like integrations with Facebook, Twitter, Google Plus, Gravatar etc.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
          "safe": 0
        }
      },
      {
        "name": "wp_mergewpemoji",
        "title": "<?php _e('Optimize Emoji loading'); ?>",
        "tooltip": "<?php _e('Change the way the WP Emoji script is loaded.'); ?>",
        "type": "select",
        "values": [
          {
            "default": "<?php _e('Default Wordpress behaviour'); ?>"
          },
          {
            "merge": "<?php _e('Merge with other scripts'); ?>"
          },
          {
            "disable": "<?php _e('Don\'t load'); ?>"
          }
        ],
        "default": "merge",
        "presets": {
        }
      }
    ]
  },
  {
    "id": "MinimizeRenderBlockingResources",
    "title": "<?php _e('Eliminate render-blocking JavaScript and CSS in above-the-fold content'); ?>",
    "type": "speed",
    "items": [
      {
        "name": "css_abovethefold",
        "title": "<?php _e('Above-the-fold CSS'); ?>",
        "tooltip": "<?php _e('Use auto-generated above-the-fold CSS styles. Disable it if the above-the-fold CSS is generated incorrectly, or the page is rendered with the aid of a lot of JavaScript and above-the-fold CSS has no effect on the rendering.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
          "safe": 0
        }
      },
      {
        "name": "css_abovethefoldcookie",
        "title": "<?php _e('Above-the-fold CSS cookie'); ?>",
        "tooltip": "<?php _e('Use a cookie to embed above-the-fold CSS styles for first-time visitors only. Using this cookie allows not sending the above-the-fold CSS with every request (as all necessary CSS files will be cached by the browser), but the setting may be disabled if PageSpeed Ninja is used with a 3rd party caching plugin.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
        }
      },
      {
        "name": "css_abovethefoldlocal",
        "title": "<?php _e('Local above-the-fold generation'); ?>",
        "tooltip": "<?php _e('Above-the-fold CSS styles may be generated either locally (directly in your browser), or externally using PageSpeed Ninja\'s service. \'Local\' uses the current browser to generate the CSS (in some cases the result may be different depending on browser: Chrome-based ones are recommended), \'External\' uses PageSpeed Ninja\'s unique service with extra improvements and minification.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
        }
      },
      {
        "name": "css_abovethefoldstyle",
        "title": "<?php _e('Above-the-fold CSS styles:'); ?>",
        "tooltip": "<?php _e('Above-the-fold CSS styles. It is generated automatically, but you may insert custom styling or edit the auto-generated version below.'); ?>",
        "type": "abovethefoldstyle",
        "default": ""
      },
      {
        "name": "css_abovethefoldautoupdate",
        "title": "<?php _e('Auto update Above-the-fold CSS'); ?>",
        "tooltip": "<?php _e('Update above-the-fold CSS styles daily.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
        }
      },
      {
        "name": "css_googlefonts",
        "title": "<?php _e('Google Fonts loading'); ?>",
        "tooltip": "<?php _e('Used to optimize the loading of Google Fonts. \'Flash of invisible text\': load fonts in a standard way at the beginning of a HTML page - most browsers do not display text until the font is loaded. \'Flash of unstyled text\': load fonts asynchronouslty and switch from default font to the loaded one when ready. \'WebFont Loader\': load fonts asynchronously using the webfont.js library. \'None\': disable optimization.'); ?>",
        "type": "select",
        "values": [
          {
            "none": "<?php _e('None'); ?>"
          },
          {
            "foit": "<?php _e('Flash of invisible text'); ?>"
          },
          {
            "fout": "<?php _e('Flash of unstyled text'); ?>"
          },
          {
            "async": "<?php _e('WebFont Loader'); ?>"
          }
        ],
        "default": "fout",
        "presets": {
          "safe": "none"
        }
      },
      {
        "name": "css_nonblockjs",
        "title": "<?php _e('Non-blocking Javascript'); ?>",
        "tooltip": "<?php _e('Load JavaScript asynchronously with a few seconds\' delay after the webpage is displayed in the browser. This speeds up the page rendering by defrering the loading of all JS. May significantly improve the loading time (and the PageSpeed Insight score), but leads to a flash of unstyled text, may affect stats in Google Analytics, and some other side effects.'); ?>",
        "experimental": 1,
        "type": "checkbox",
        "default": 0,
        "presets": {
          "experimental": 1
        }
      }
    ]
  },
  {
    "id": "OptimizeImages",
    "title": "<?php _e('Optimize images'); ?>",
    "type": "speed",
    "items": [
      {
        "name": "img_minify",
        "title": "<?php _e('Optimization'); ?>",
        "tooltip": "<?php _e('Reduce the size of the images for faster loading and less bandwidth needed using the selected rescaling quality. The original image will be backed up with suffix \'.orig\' (image.jpg->image.orig.jpg).'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
          "safe": 0,
          "compact": 0
        }
      },
      {
        "name": "img_driver",
        "title": "<?php _e('Images handler'); ?>",
        "tooltip": "<?php _e('Method to deal with images. By default PHP supports GD2 only, but may be configured to support ImageMagick API as well.'); ?>",
        "type": "imgdriver",
        "values": [
          {
            "gd2": "<?php _e('GD2'); ?>"
          },
          {
            "imagick": "<?php _e('ImageMagick'); ?>"
          }
        ],
        "default": "gd2"
      },
      {
        "name": "img_jpegquality",
        "title": "<?php _e('JPEG quality'); ?>",
        "tooltip": "<?php _e('You can set the image rescaling quality between 0 (low) and 100 (high). Higher means better quality. The recommended level is 80%-95%.'); ?>",
        "type": "number",
        "units": "<?php _e('%'); ?>",
        "default": 85,
        "presets": {
          "safe": 95
        }
      },
      {
        "name": "img_scaletype",
        "title": "<?php _e('Scale large images'); ?>",
        "tooltip": "<?php _e('By default images are rescaled on mobile for faster loading.'); ?>",
        "type": "select",
        "values": [
          {
            "none": "<?php _e('None'); ?>"
          },
          {
            "fit": "<?php _e('Fit'); ?>"
          },
          {
            "prop": "<?php _e('Fixed Ratio'); ?>"
          },
          {
            "remove": "<?php _e('Remove'); ?>"
          }
        ],
        "default": "fit",
        "presets": {
          "safe": "none",
          "compact": "none"
        }
      },
      {
        "name": "img_bufferwidth",
        "type": "hidden",
        "default": 0
      },
      {
        "name": "img_templatewidth",
        "title": "<?php _e('Template width (reference)'); ?>",
        "tooltip": "<?php _e('Desktop template width. Required for \'Fixed Ratio\' image option.'); ?>",
        "type": "number",
        "units": "<?php _e('px'); ?>",
        "default": 960,
        "presets": {
        }
      },
      {
        "name": "img_wrapwide",
        "title": "<?php _e('Wrap wide images'); ?>",
        "tooltip": "<?php _e('Wrap images that are wider than half of the screen into a centered &lt;span&gt; This makes a floating (align=right or align=left attribute) image to fill full horizontal size if it\'s wider than 50% of the screen width (otherwise there is a narrow column of text near the image).'); ?>",
        "type": "checkbox",
        "default": 0,
        "presets": {
        }
      },
      {
        "name": "img_wideimgclass",
        "title": "<?php _e('Wide image wrapper class'); ?>",
        "tooltip": "<?php _e('Value of \'class\' attribute for wrapped wide images.'); ?>",
        "type": "text",
        "default": "wideimg"
      }
    ]
  },
  {
    "id": "PrioritizeVisibleContent",
    "title": "<?php _e('Prioritize visible content'); ?>",
    "type": "speed",
    "items": [
      {
        "name": "img_lazyload",
        "title": "<?php _e('Lazy Load Images'); ?>",
        "tooltip": "<?php _e('Lazy load images with the Lazy Load XT script. Significantly speeds up the loading of image and/or video-heavy webpages.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
          "safe": 0
        }
      },
      {
        "name": "img_lazyload_iframe",
        "title": "<?php _e('Lazy Load Iframes'); ?>",
        "tooltip": "<?php _e('Lazy load iframes with the Lazy Load XT script.'); ?>",
        "type": "checkbox",
        "default": 1,
        "presets": {
          "safe": 0
        }
      },
      {
        "name": "img_lazyload_lqip",
        "title": "<?php _e('Low-quality image placeholders'); ?>",
        "tooltip": "<?php _e('Use low-quality image placeholders instead of empty areas.'); ?>",
        "type": "checkbox",
        "default": 0,
        "presets": {
        }
      },
      {
        "name": "img_lazyload_edgey",
        "title": "<?php _e('Vertical lazy loading threshold'); ?>",
        "tooltip": "<?php _e('Expand the visible page area (viewport) in vertical direction by specified amount of pixels, so that images start to load even if they are not actually visible yet.'); ?>",
        "type": "number",
        "units": "<?php _e('px'); ?>",
        "default": 0,
        "presets": {
        }
      },
      {
        "name": "img_lazyload_skip",
        "title": "<?php _e('Skip first images'); ?>",
        "tooltip": "<?php _e('Skip lazy loading of specified number of images from the beginning of an HTML page (useful for logos and other images that are always visible in the above-the-fold area).'); ?>",
        "type": "number",
        "default": 3,
        "presets": {
          "safe": 10,
          "ultra": 1,
          "experimental": 0
        }
      },
      {
        "name": "img_lazyload_noscript",
        "title": "<?php _e('Noscript position'); ?>",
        "tooltip": "<?php _e('Position to insert the original image wrapped in a noscript tag for browsers with disabled JavaScript (may be useful if your image styles rely on CSS selectors :first or :last). To not generate noscript tags, set this option to \'None\'.'); ?>",
        "type": "select",
        "values": [
          {
            "after": "<?php _e('After'); ?>"
          },
          {
            "before": "<?php _e('Before'); ?>"
          },
          {
            "none": "<?php _e('None'); ?>"
          }
        ],
        "default": "after",
        "presets": {
        }
      },
      {
        "name": "img_lazyload_addsrcset",
        "title": "<?php _e('Generate srcset'); ?>",
        "tooltip": "<?php _e('Automatically generate the srcset attribute for rescaled images.'); ?>",
        "type": "checkbox",
        "default": 0,
        "presets": {
          "ultra": 1,
          "experimental": 1
        }
      }
    ]
  },
  {
    "id": "AvoidPlugins",
    "title": "<?php _e('Avoid plugins'); ?>",
    "type": "usability",
    "items": [
      {
        "name": "remove_objects",
        "title": "<?php _e('Remove embedded plugins'); ?>",
        "tooltip": "<?php _e('Remove all embedded plugins like Flash, ActiveX, Silverlight, etc.'); ?>",
        "type": "checkbox",
        "default": 1
      }
    ]
  },
  {
    "id": "ConfigureViewport",
    "title": "<?php _e('Configure the viewport'); ?>",
    "type": "usability",
    "items": [
      {
        "name": "viewport_width",
        "title": "<?php _e('Viewport width'); ?>",
        "tooltip": "<?php _e('Viewport width in pixels. Set to 0 (zero) to use the device screen width (default).'); ?>",
        "type": "number",
        "units": "<?php _e('px'); ?>",
        "default": 0,
        "presets": {
        }
      }
    ]
  },
  {
    "id": "SizeContentToViewport",
    "title": "<?php _e('Size content to viewport'); ?>",
    "type": "usability"
  },
  {
    "id": "SizeTapTargetsAppropriately",
    "title": "<?php _e('Size tap targets appropriately'); ?>",
    "type": "usability"
  },
  {
    "id": "UseLegibleFontSizes",
    "title": "<?php _e('Use legible font sizes'); ?>",
    "type": "usability"
  }
]