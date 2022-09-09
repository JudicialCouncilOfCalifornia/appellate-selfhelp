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

include_once RESSIO_PATH . '/vendor/amdd/amdd.php';

echo "Updating...<br>\n";
flush();

// @todo check timestamp
// @todo get update from ressio server

$options = (array)$config->amdd;
Amdd::updateDatabaseFromFile(RESSIO_PATH . '/setup/amdd_data.gz', $options);

echo "Done.\n";
