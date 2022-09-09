<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2019 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

define('RESSIO_PATH', dirname(dirname(__FILE__)));
include_once RESSIO_PATH . '/ressio.php';
$config = Ressio::loadConfig();

$cache_dir = $config->cachedir;
$cache_dir_writeable = is_writable($cache_dir);

$static_dir = $config->webrootpath . $config->staticdir;
$static_dir_writeable = is_writable($static_dir);

$gd_installed = function_exists('imagecreatefromstring');

try {
    include_once RESSIO_PATH . '/vendor/amdd/amdd.php';
    $amdd_working = Amdd::checkDatabase();
} catch (Exception $e) {
    $amdd_working = $e->getMessage();
}

function dump_value($value)
{
    if (is_bool($value)):
        ?><code>
        <small>(bool)</small>
        <b><?php echo $value ? 'true' : 'false'; ?></b></code><?php
    elseif (is_int($value)):
        ?><code>
        <small>(int)</small>
        <b><?php echo $value; ?></b></code><?php
    elseif (is_float($value)):
        ?><code>
        <small>(float)</small>
        <b><?php echo $value; ?></b></code><?php
    elseif (is_string($value)):
        ?><code>
        <small>(string)</small>
        '<b><?php echo $value; ?></b>'</code><?php
    elseif (is_array($value)):
        ?><code>
        <small>(array)</small>
        <b><?php echo json_encode($value); ?></b></code><?php
    else:
        ?><code>
        <small>(object)</small>
        <b><?php echo json_encode($value); ?></b></code><?php
    endif;
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>RESSIO Pre-check</title>
</head>
<body>
<p>Cache directory <code><?php echo $cache_dir; ?></code> is
    <b><?php echo $cache_dir_writeable ? 'Writeable' : 'Not Writeable'; ?></b></p>

<p>Static files directory <code><?php echo $static_dir; ?></code> is
    <b><?php echo $static_dir_writeable ? 'Writeable' : 'Not Writeable'; ?></b></p>

<p><code>GD</code> library is <b><?php echo $gd_installed ? 'Installed' : 'Not Installed'; ?></b></p>

<p><code>AMDD</code> database is
    <b><?php echo ($amdd_working === true) ? 'Created' : 'Not Created'; ?></b><?php echo is_string($amdd_working) ? " ($amdd_working)" : ''; ?>
</p>

<h2>Current Config</h2>
<ul>
    <?php foreach ($config as $property => $value): ?>
        <li>
            <?php echo $property; ?>:
            <?php if (!is_object($value)): ?>
                <?php dump_value($value); ?>
            <?php endif; ?>
        </li>
        <?php if (is_object($value)): ?>
            <ul>
                <?php foreach ($value as $subproperty => $subvalue): ?>
                    <li>
                        <?php echo $subproperty; ?>:
                        <?php dump_value($subvalue); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
</body>
</html>