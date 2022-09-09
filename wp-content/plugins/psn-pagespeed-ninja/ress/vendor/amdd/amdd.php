<?php
/**
 * Advanced Mobile Device Detection
 *
 * @version        ###VERSION###
 * @license        ###LICENSE###
 * @copyright    ###COPYRIGHT###
 * @date        ###DATE###
 */

require_once dirname(__FILE__) . '/config.php';
require_once dirname(__FILE__) . '/ua.php';
require_once dirname(__FILE__) . '/database/database.php';

class Amdd
{
    /**
     * Status of last device detecting:
     * 0 no detection
     * 1 isDesktop
     * 2 exact match
     * 3 from cache
     * 4 prefix match
     * 5 levenshtein match
     * 6 unknown device
     * @var int
     */
    public static $matchType = 0;

    /**
     * Get capabilities for given UA
     * @static
     * @param string $ua User-Agent (will be auto-detected if it's null)
     * @param bool $exact
     * @param array $options
     * @return stdClass
     * @throws AmddDatabaseException
     */
    public static function getCapabilities($ua = null, $exact = false, $options = null)
    {
        self::$matchType = 0;

        if ($ua === null) {
            $ua = AmddUA::getUserAgentFromRequest();
        }

        $ua = AmddUA::normalize($ua);

        self::$matchType = 1;
        if (AmddUA::isDesktop($ua)) {
            return self::makeDesktop();
        }

        $ua = substr($ua, 0, 255);
        $data = self::getDevice($ua, $exact, $options);

        if ($data !== null) {
            $data = json_decode($data);
        }

        if ($data === null) {
            self::$matchType = 6;
            $data = self::makeDesktop();
        }

        return $data;
    }

    /**
     * @return stdClass
     */
    private static function makeDesktop()
    {
        $data = new stdClass;
        $data->type = 'desktop';
        $data->markup = '';
        //$data->screenWidth = 0;
        //$data->screenHeight = 0;
        //$data->imageFormats = array('gif', 'jpg', 'png');
        return $data;
    }

    /**
     * @param string $ua
     * @param bool $exact
     * @param array $options
     * @return null|string
     * @throws AmddDatabaseException
     */
    private static function getDevice($ua, $exact = false, $options = null)
    {
        if ($options === null) {
            $options = get_class_vars('AmddConfig');
        }
        $db = AmddDatabase::getInstance($options);

        self::$matchType = 2;
        // load matched device
        $data = $db->getDevice($ua);
        if ($data !== null) {
            return $data;
        }

        if ($exact) {
            return null;
        }

        // fast test for bots and desktop browsers
        $group = AmddUA::getGroup($ua);
        if ($group === 'bot') {
            return null;
        }

        // load device from cache
        if (AmddConfig::$cacheSize !== 0) {
            self::$matchType = 3;
            $data = $db->getDeviceFromCache($ua);
            if ($data !== null) {
                return empty($data) ? null : $data;
            }
        }

        // find closest device
        $devices = $db->getDevices($group);

        if (preg_match('#^(Mozilla|Opera|NetFront)/#', $ua)) {
            self::$matchType = 5;
            $data = self::findByLevenshtein($ua, $devices);

            if ($data === null) {
                $pos = strpos($ua, ')');
                if (!$pos) {
                    $pos = strlen($ua);
                }

                self::$matchType = 4;
                $data = self::findByPrefix($ua, $devices, $pos);
            }
        } else {
            preg_match('#^(DoCoMo|portalmmm)/.*?(\(|$)#', $ua, $match)
            or preg_match('#^.{5,}?[ /]#', $ua, $match);
            $pos = isset($match[0]) ? strlen($match[0]) : strlen($ua);

            self::$matchType = 4;
            $data = self::findByPrefix($ua, $devices, $pos);

            if ($data === null) {
                self::$matchType = 5;
                $data = self::findByLevenshtein($ua, $devices);
            }
        }

        // save to cache
        $db->putDeviceToCache($ua, empty($data) ? '' : $data, AmddConfig::$cacheSize);

        return $data;
    }

    /**
     * @param string $ua
     * @param stdClass[] $devices
     * @param int $best
     * @return null|string
     */
    private static function findByPrefix($ua, $devices, $best)
    {
        $ua_size = strlen($ua);
        $data = null;

        foreach ($devices as $device) {
            $dev_ua = $device->ua;
            if (strncmp($ua, $dev_ua, $best) !== 0) {
                continue;
            }

            $min_size = min($ua_size, strlen($dev_ua));

            while ($best < $min_size && $ua[$best] === $dev_ua[$best]) {
                $best++;
            }

            $data = $device->data;
        }

        return $data;
    }

    /**
     * @param string $ua
     * @param stdClass[] $devices
     * @return null|string
     */
    private static function findByLevenshtein($ua, $devices)
    {
        $ua_size = strlen($ua);
        $data = null;

        $best = min(24, $ua_size); // maximum number of changes in UA string
        foreach ($devices as $device) {
            if (abs(strlen($device->ua) - $ua_size) > $best) {
                continue;
            }

            $current = levenshtein($ua, $device->ua, 1, 2, 2);
            if ($current <= $best) {
                $best = $current;
                $data = $device->data;
            }
        }

        return $data;
    }

    /**
     * @param string $filename
     * @param array $options
     * @throws AmddDatabaseException
     */
    public static function updateDatabaseFromFile($filename, $options = null)
    {
        if ($options === null) {
            $options = get_class_vars('AmddConfig');
        }
        $db = AmddDatabase::getInstance($options);

        $stream = fopen('compress.zlib://' . $filename, 'rb');
        $db->updateDatabase($stream);
        fclose($stream);
    }

    /**
     * @param array $options
     * @return bool
     * @throws AmddDatabaseException
     */
    public static function checkDatabase($options = null)
    {
        if ($options === null) {
            $options = get_class_vars('AmddConfig');
        }
        $db = AmddDatabase::getInstance($options);

        return $db->checkDatabase();
    }

    /**
     * @param array $options
     * @throws AmddDatabaseException
     */
    public static function dropDatabase($options = null)
    {
        if ($options === null) {
            $options = get_class_vars('AmddConfig');
        }
        $db = AmddDatabase::getInstance($options);

        $db->dropDatabase();
    }
}