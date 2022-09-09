=== Standalone RESS code ===


--- Installation ---

1. Unpack ress.zip to website's /ress directory.

2. Visit ress/setup/precheck.php to check compatibility and current settings.

3. (optionally) Adjust settings in ress/config.php file.

4. Visit ress/setup/update_amdd.php to install AMDD database.

5. If you use Apache webserver, rename ress/s/sample.htaccess to ress/s/.htaccess,
   otherwise set "fileloader" parameter in ress/config.php to "php" (instead of "file" by default)


--- RESS with PHP ---

Include ress/init.php on the top of your index.php file (and/or other requested php files):

<?php include_once 'ress/init.php'; ?>


--- RESS with static HTML @ Apache ---

Append following rules to .htaccess file in the website's root directory (or create file if it doesn't exist):

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} -f
    RewriteCond %{REQUEST_URI} \.html?$ [OR]
    RewriteCond %{REDIRECT_URL} \.html?$
    RewriteRule . ress/optimize.php [L]


--- RESS with static HTML @ Nginx ---

Add following rules to Nginx's config file:

    location ~ \.html?$ {
        if (-f $request_filename) {
            rewrite ^ /ress/optimize.php last;
        }
    }


--- RESS with static HTML @ IIS ---

Check that IIS URL Rewrite module is installed and create following rule under IIS URL Rewrite:

    Pattern field: \.html?$
    Ignore case: ON
    Action type: Rewrite
    Rewrite URL: /ress/optimize.php


--- Settings ---

RESS has a lot of settings stored in ress/config.php file:

- autostart: true to optimize output content automatically (without call to Ressio->run)
- webrootpath: path to the root of website (without trailing slashes), detected automatically if empty
- webrooturi: don't procees js/css files outside of this (sub)directory (with both leading and trailing slashes), corresponds to webrootpath directory
- staticdir: path to stored combined files (./ - relative to ressio directory)
- fileloader: way to load optimized js/css files ("file" for direct URLs or "php" for fetch.php loader)
- fileloaderphppath: path to fetch.php file (./ - relative to ressio directory)
- filehashsize: length of filenames for generated files
- cachedir: path to cache directory (./ - relative to ressio directory)
- cachettl: time-to-live for cache entries

# html: settings for HTML optimization

- gzlevel: gzip compression level for html pages
- forcehtml5: add HTML5 doctype tag (if not presented)
- mergespace: replace sequence of whitespaces into single one
- removecomments: remove html comments
- urlminify: replace absolute URLs by relative ones

# css: settings for CSS optimization

- merge: merge files
- mergeheadbody: allow to merge css styles/files from <head> and <body> sections, otherwise they are optimized separately
- crossfileoptimization: optimize generated combined css file
- inlinelimit: embed css styles up to this limit directly into html page using <style> tag

# js: settings for JavaScript optimization

- merge: merge files
- mergeheadbody: allow to merge javascripts from <head> and <body> sections, otherwise they are optimized separately
- autoasync: load javascripts asynchromously
- crossfileoptimization: optimize generated combined javascript file
- inlinelimit: embed javascripts up to this limit directly into html page
- wraptrycatch: wrap original javascript files into try{...}catch(e){} block in the combined file

# img: settings for images optimization

- rescale: rescale large images to fit mobile screen
- bufferwidth: buffer width extracted from screen width
- hiresimages: generate retina images
- hiresjpegquality: jpeg quality for retina images
- jpegquality: jpeg quality for rescaled images
- keeporig: keep original image URL in data-orig attribute
- scaletype: method to calclate rescaled image size (fit, ...)
- setdimension: add width and height attributes
- templatewidth: webpage width used in ... rescale method
- wideimgclass: value of class attribute for wrapped wide images
- wrapwideimg: wrap wide images (wider than half of screen width) into <span class="wideimgclass">...</span> tag
- lazyload: lazy load images using Lazy Load XT library
- lazyloaddomtastic: load DOMtastic library (disable if jQuery or Zepto is loaded)

# amdd: settings for local device database (AMDD)

- handler" => "plaintext",
- cacheSize" => 1000,
- dbPath" => "./vendor/amdd/devices",
- dbUser" => "...",
- dbPassword" => "...",
- dbHost" => "localhost",
- dbDatabase" => "...",
- dbTableName" => "amdd",
- dbDriver" => "pgsql:host=localhost;port=5432;dbname=...",
- dbDriverOptions" => array()

# rddb: settings for remote device database (RDDB)

- timeout" => 3,
- proxy" => false,
- proxy_url" => "tcp://127.0.0.1:3128",
- proxy_login" => false,
- proxy_pass" => ""

# plugins: list of enabled plugins with configuration parameters (null if not used)

- Ressio_Plugin_Rescale
- Ressio_Plugin_Lazyload

# di: dependency injection container (used to override most of Ressio's internal classes)

- deviceDetector" => "Ressio_DeviceDetector_Rddb", // Amdd, Base
- cssMinify" => "Ressio_CssMinify_Ress", // CssTidy / Chain / None
- jsMinify" => "Ressio_JsMinify_Jsmin", // none, chain
- filelock" => "Ressio_FileLock_flock", // link, mkdir

