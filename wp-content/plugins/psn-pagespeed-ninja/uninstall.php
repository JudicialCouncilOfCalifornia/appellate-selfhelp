<?php

defined('WP_UNINSTALL_PLUGIN') || die;

// uninstalling may take some time
set_time_limit(0);

// Restore .htaccess
$homeDir = rtrim(ABSPATH, '/');
$marker = 'Page Speed Ninja';
insert_with_markers($homeDir . '/wp-includes/.htaccess', $marker, '');
insert_with_markers($homeDir . '/wp-content/.htaccess', $marker, '');
if (is_dir($homeDir . '/uploads')) {
    insert_with_markers($homeDir . '/uploads/.htaccess', $marker, '');
}

// Drop AMDD database
include_once dirname(__FILE__) . '/includes/class-pagespeedninja-amdd.php';
PagespeedNinja_Amdd::dropDatabase();

// Drop URLs database
global $wpdb;
$delete = $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}psninja_urls`");

// Restore images
$ress_dir = dirname(__FILE__) . '/ress';
include_once $ress_dir . '/ressio.php';
$ressio = new Ressio();
$ressio->di->imgOptimizer->restore();

/** @var array $config */
$config = get_option('pagespeedninja_config');

// Drop plugin settings
delete_option('pagespeedninja_config');

// Remove advanced-cache.php
if ($config['caching']) {
    $advancedCache = WP_CONTENT_DIR . '/advanced-cache.php';
    $content = file_get_contents($advancedCache);
    if (strpos($content, 'PAGESPEEDNINJA_CACHE_DIR') !== false) {
        @unlink($advancedCache);
    }
}

// Drop static files
$staticdir = rtrim(ABSPATH, '/') . $config['staticdir'];
if (is_dir($staticdir)) {
    $files = scandir($staticdir, @constant('SCANDIR_SORT_NONE'));
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && preg_match('#^[0-9a-f]{6}\.(?:js|css)(?:\.gz)?$#', $file)) {
            @unlink($staticdir . '/' . $file);
        }
    }
    @rmdir($staticdir);
}
