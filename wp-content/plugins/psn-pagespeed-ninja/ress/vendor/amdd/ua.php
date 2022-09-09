<?php

/**
 * Advanced Mobile Device Detection
 *
 * @version     ###VERSION###
 * @license     ###LICENSE###
 * @copyright   ###COPYRIGHT###
 * @date        ###DATE###
 */
class AmddUA
{
    /**
     * Get real User-Agent string from HTTP headers
     * @static
     * @param array $headers
     * @return string
     */
    public static function getUserAgentFromRequest($headers = null)
    {
        static $userAgentHeaders = array(
            'HTTP_DEVICE_STOCK_UA',       // Opera proposal https://github.com/operasoftware/Device-Stock-UA-RFC
            'HTTP_X_DEVICE_USER_AGENT',   // Content Transformation Proxies http://www.w3.org/TR/ct-guidelines/
            'HTTP_X_ORIGINAL_USER_AGENT', // Google Wireless Transcoder
            'HTTP_X_OPERAMINI_PHONE_UA',  // Opera Mini browser
            'HTTP_X_SKYFIRE_PHONE',       // Skyfire browser
            'HTTP_X_BOLT_PHONE_UA',       // Bolt browser
            'HTTP_X_MOBILE_UA',           // Mowser transcoder
            'HTTP_X_UCBROWSER_DEVICE_UA', // UC Browser
            'HTTP_USER_AGENT'
        );

        if ($headers === null) {
            $headers = $_SERVER;
        }

        foreach ($userAgentHeaders as $header) {
            if (isset($headers[$header])) {
                return $headers[$header];
            }
        }

        return '';
    }

    // \x80-\xFF sequence
    const ASCII_UP = "\x80\x81\x82\x83\x84\x85\x86\x87\x88\x89\x8A\x8B\x8C\x8D\x8E\x8F\x90\x91\x92\x93\x94\x95\x96\x97\x98\x99\x9A\x9B\x9C\x9D\x9E\x9F\xA0\xA1\xA2\xA3\xA4\xA5\xA6\xA7\xA8\xA9\xAA\xAB\xAC\xAD\xAE\xAF\xB0\xB1\xB2\xB3\xB4\xB5\xB6\xB7\xB8\xB9\xBA\xBB\xBC\xBD\xBE\xBF\xC0\xC1\xC2\xC3\xC4\xC5\xC6\xC7\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1\xD2\xD3\xD4\xD5\xD6\xD7\xD8\xD9\xDA\xDB\xDC\xDD\xDE\xDF\xE0\xE1\xE2\xE3\xE4\xE5\xE6\xE7\xE8\xE9\xEA\xEB\xEC\xED\xEE\xEF\xF0\xF1\xF2\xF3\xF4\xF5\xF6\xF7\xF8\xF9\xFA\xFB\xFC\xFD\xFE\xFF";
    // 128 '?' characters
    const ASCII_QQ = '????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????';

    /**
     * Remove redundant data from User-Agent string
     * @static
     * @param string $ua
     * @return string
     */
    public static function normalize($ua)
    {
        // Remove non-ascii characters
        //$ua = preg_replace('#[^ -~]+#', '', $ua);
        $ua = strtr($ua, self::ASCII_UP, self::ASCII_QQ);
        $ua = str_replace(array("\n", "\r", "\t"), ' ', $ua);

        // Fix possible proxy bugs
        $ua = ltrim($ua, ':= \'"');
        $ua = preg_replace('#^(?>User-Agent[:= ]*)+#i', '', $ua);
        if (strpos($ua, '+') !== false && strpos($ua, ' ') === false) {
            $chars = count_chars($ua, 1);
            if ($chars[ord('+')] >= 4) {
                $ua = str_replace('+', ' ', $ua);
            }
        }
        $ua = str_replace(
            array('AppleWebkit', ')AppleWebKit',  ')Version/',  ' http://internet.tigo.com.gt; '),
            array('AppleWebKit', ') AppleWebKit', ') Version/', ' '),
            $ua);
        $ua = trim($ua, " '\"\\");

        if (($pos = strpos($ua, ',gzip(gfe)')) !== false) {
            $ua = substr($ua, 0, $pos);
        }

        // Beautify
        $ua = preg_replace('#(?<= ) +#', '', $ua);
        $ua = str_replace(' ;', ';', $ua);
        $ua = preg_replace('#(?<=;);+#', '', $ua);
        $ua = preg_replace('#[; ]+(?=\))#', '', $ua);

        // Remove serial numbers
        $ua = preg_replace('# BMID/[0-9A-F]{10,}|(?:(?:[/;]SN| IMEI/)(?:\d{14,15}|X{14,15})|\[(?:NT|ST|TF)?(?:\d+|X+)\])#', '', $ua);

        // Replace locale id by xx
        $ua = preg_replace('#(?<=[/;\[ ])(?:[A-Za-z][a-z]|haw)(?:[_-][A-Za-z]{2})?(?=[);\] ])#', 'xx', $ua);
        $ua = preg_replace('#(?<=; )[a-z]{2}-(?=;)#', 'xx', $ua); //buggy strings
        // Remove locale id
        $ua = preg_replace('#; *xx *(?=[);])#', '', $ua);
        $ua = str_replace(' [xx]', '', $ua);

        // Remove security level
        $ua = preg_replace('#; ?[UIN](?=[;)])#i', '', $ua);

        // Remove browser prefix (Android)
        $ua = preg_replace('#^(?:i|MQQ|One|Zing)Browser/\d\.?\d[/ ](?=Mozilla/5\.0 \(Linux; Android )#', '', $ua);

        // Remove AppleWebKit, Safari, etc. versions
        $ua = preg_replace('#( (?:1Password|AppleNews|AppleWebKit|NintendoBrowser|Safari|webOSBrowser|YaApp_iOS|YaApp_iOS_Browser)/)[\w./+-]+#', '\1*', $ua);
        $ua = preg_replace('#( (?:Chrome|OPR|SamsungBrowser|Silk))/[\w./+-]+#', '\1', $ua);
        $ua = preg_replace('#(?<= Chrome)-\d{10}(?= )#', '', $ua); // temporary
        $ua = preg_replace('# (?:AlohaBrowser|BaiduHD|BdMobile|Coast|CriOS|EdgiOS|FlyFlow|Focus|FocusiOS|FxiOS|GSA|Klar|LinkedIn|Mobile Crosswalk|MobileIron|MQQBrowser|MZBrowser|OPiOS|SVN|TBS|UWS|Version|YaBrowser)/[\w./+-]+#', '', $ua);
        $ua = str_replace(' SamsungBrowser Chrome ', ' Chrome ', $ua);


        if (strpos($ua, 'Vodafone') !== false) {
            // Remove Vodafone/1.0/ prefix
            $ua = preg_replace('#^Vodafone/(\d+\.\d+/)?#', '', $ua);
            // Remove Vodafone suffix
            $ua = str_replace(array('-Vodafone ', '-Vodafone/'), array(' ', '/'), $ua);
        }

        // Normalize Blackberry
        if (stripos($ua, 'BlackBerry') !== false) {
            $ua = str_ireplace('blackberry', 'BlackBerry', $ua);
            $ua = preg_replace('#(?<= VendorID/)(?:\d+|-1)#', '100', $ua);
        }

        // Normalize Nokia
        if (stripos($ua, 'Nokia') !== false) {
            $ua = str_ireplace('nokia', 'Nokia', $ua);
            // Remove Nokia build version
            $ua = preg_replace('#(?<=^Nokia)([\w./-]+ )\([\d.a-z_]+\) #', '\1', $ua);

            // remove Browser versions
            $ua = preg_replace('#( BrowserNG| NokiaBrowser| Series\s?\d\d|OviBrowser|SymbianOS)/[\d.]+(?:gpp-gba)?#', '\1/*', $ua);

            $ua = preg_replace('#((?:^|\s)Nokia[\w./-]+)(?>/[\d.a-z_]+)+(?=[ ;)])#', '\1', $ua);
            $ua = preg_replace('#(?<=\bNokia)([\w.-]+/\d+)\.\d{4,}(?= )#', '\1', $ua);
            $ua = preg_replace('#(?<=^Mozilla/[45]\.0 \()(.*?Nokia ?[\w.-]+)/[\d.a-z_]+(?=;)#', '\1', $ua);
        }

        // Remove Motorola version
        if (strpos($ua, 'Blur_Version') !== false) {
            $ua = preg_replace('#(?<=/)Blur_Version\.[^ )]+(?= )#', '', $ua);
        }
        if (strncmp($ua, 'MOT-', 4) === 0) {
            $ua = preg_replace('#(?<=^MOT-)([\w-]+)/[\w.]+(?= )#', '\1', $ua);
        }

        // Remove Samsung build numbers
        if (stripos($ua, 'samsung') !== false || strpos($ua, 'GT') !== false) {
            $ua = preg_replace('#((?:^|; )(?:SAMSUNG|Samsung|GT|SAMSUNG GT|SAMSUNG SM)-[\w-]+)/[\w./-]+#', '\1', $ua);
        }

        // Remove SonyEricsson build numbers
        if (strpos($ua, 'SonyEricsson') !== false) {
            $ua = preg_replace('#(?<=SonyEricsson)([\w-]+)/[\w./-]+#', '\1', $ua);
        }

        // Remove Pantech build numbers
        if (strncmp($ua, 'Pantech', 7) === 0) {
            $ua = preg_replace('#(?<=^Pantech)([\w-]+)/[\w./-]+#', '\1', $ua);
        }

        // Remove PlayStation Vita subversion
        if (stripos($ua, 'playstation') !== false) {
            $ua = preg_replace('#(?<=\(playstation \d )(\d+\.)\d+(?=\))#i', '\1*', $ua);
            $ua = preg_replace('#(?<=\(PlayStation Vita )(\d+\.)\d+(?=\))#', '\1*', $ua);
        }

        // Convert Dalvik to Mozilla header
        // Note: Dalvik may be prefixed [e.g. by "Callpod Keeper for Android 1.0 (10.5.0/264) "]
        if (strpos($ua, 'Dalvik/') !== false && preg_match('#Dalvik/[\d.]+ (\(.*?\))#', $ua, $match)) {
            $ua = "Mozilla/5.0 {$match[1]} AppleWebKit/* (KHTML, like Gecko) Mobile Safari/*";
        }

        if (strpos($ua, 'Android') !== false) {
            // Remove Android subversion
            $ua = preg_replace('#(?<=Android)( ?(?>\d+\.))[\w.-]+#', '\1*', $ua);
            // Remove Android build version
            $ua = preg_replace('#(Android .*?) Build/(?:[^()]|\([^()]*\))+(?=\))#', '\1', $ua);
            $ua = preg_replace('#(Android .*?) Build/[^;)]+#', '\1', $ua);
            // remove suffixes after Safari/*
            if (($pos = strpos($ua, ' Safari/*')) !== false) {
                $ua = substr($ua, 0, $pos + 9);
            }
            // merge BacaBerita App
            $ua = preg_replace('#(?<=^BacaBerita App)/[\d\.]+ \(Linux; Android \d+\)(?= Mobile Safari$)#', ' (Linux; Android)', $ua);
        }

        if (strpos($ua, ' like Mac OS X') !== false) {
            // Remove iPhone revision version
            $ua = preg_replace('#(?<= OS )(\d+)_\d+(?:_\d+)?(?= like Mac OS X)#', '\1', $ua);
        }
        // Remove iPhone build version
        if (strpos($ua, ' Mobile/') !== false) {
            $ua = preg_replace('#( \(KHTML, like Gecko\).*? Mobile/)\w+#', '\1*', $ua);
            $ua = str_replace(' 1Password/* (like', '', $ua);
            // remove suffixes after Safari/* or Mobile/*
            $pos1 = strpos($ua, ' Safari/*');
            $pos2 = strpos($ua, ' Mobile/*');
            $pos = max($pos1, $pos2);
            if ($pos !== false) {
                $ua = substr($ua, 0, $pos + 9);
            }
        }

        if (strpos($ua, 'Windows Phone') !== false) {
            // Remove WP's mimic (Note: kept for historical reason)
            $ua = str_replace(array(
                '(Mobile; Windows Phone 8.1; Android 4.0; ',
                '(Mobile; Windows Phone 8.1; Android 4.*; '
            ), '(Windows Phone 8.1; ', $ua);
            if (($pos = strpos($ua, ' like iPhone OS ')) !== false) {
                $ua = substr($ua, 0, $pos + 12); // keep just "like iPhone"
            }
        }

        // Remove long numbers series
        $ua = preg_replace('#(\D\d+\.\d+)[_.][\w.-]+#', '\1', $ua);
        $ua = preg_replace('#(?<=/)\d{8}[\w.-]*#', '*', $ua);

        // Remove Opera Mini/Mobile/Tablet version
        if (strpos($ua, 'Opera ') !== false) {
            $ua = preg_replace('#(?<=Opera )(Mini|Mobi|Mobile|Tablet)/[^;)]+#', '\1', $ua);
        }
        if (strncmp($ua, 'OperaMini/', 10) === 0) {
            $ua = preg_replace('#(?<=OperaMini)/[\d.]+#', '', $ua);
        }

        // Remove Silk suffix
        if (strpos($ua, ' Silk') !== false) {
            $ua = preg_replace('# Silk-Accelerated=(?:true|false)#', '', $ua);
        }

        // Fennec browser
        if (strpos($ua, ' Firefox') !== false) {
            // remove revision
            $ua = preg_replace('#; rv:(?:[^()]|\([^()]+\))+(?=\))#', '', $ua);
            // normalize Firefox/*** and Fennec/***
            // @todo Is it necessary to consider Fennec as a separate browser?
            $ua = preg_replace('#(?<=^Mozilla/5\.0 \()([^)]+\)) Gecko/[\d.]+ Firefox/[\d.]+ Fennec/\d.*$#', '\1 Fennec/*', $ua);
            $ua = preg_replace('#(?<=^Mozilla/5\.0 \()([^)]+\)) Gecko/[\d.]+ Firefox/\d.*$#', '\1 Firefox/*', $ua);
            $ua = preg_replace('# Firefox/\d.*$#', ' Firefox/*', $ua);
        }

        // Remove Maxthon fingerprint
        if (strpos($ua, 'Maxthon') !== false) {
            $ua = str_replace(')Maxthon ', ') ', $ua);
            $ua = preg_replace('# Maxthon(?>/[\d.]+)?$#', '', $ua);
        }

        // Remove UCWEB/UCBrowser suffix
        if (strpos($ua, 'UC') !== false) {
            $ua = preg_replace('#(?: \(|[ /]|\b)UC(?: ?Browser|WEB)/?\d.*$#', '', $ua);
        }

        // Remove UP.Link version of Openwave WAP Gateway
        if (($pos = strpos($ua, 'UP.Link')) !== false) {
            $ua = rtrim(substr($ua, 0, $pos));
        }

        // Remove common suffixes
        if (strpos($ua, ' [') !== false) {
            $ua = preg_replace('# \[(?:FBAN|FB_IAB|Pinterest)/.*$#', '', $ua);
        }
        $ua = str_replace(array(
            ' 3gpp-gba',
            ' MMS/LG-Android-MMS-V1.0',
            ' MMS/LG-Android-MMS-V1.0/V1.2)',
            ' MMS/LG-Android-MMS-V1.0/1.2)',
            ' MMS/LG-Android-MMS-V1.2',
            ' MMS/ZTE-Android-MMS-V2.0',
            ' Mobitest',
            ' Twitter for iPhone',
            ' Twitter for iPad',
            ' UNTRUSTED/1.0',
            ' Untrusted/1.0',
        ), '', $ua);
        if (strpos($ua, ' baidu') !== false) {
            $ua = preg_replace('# baidu(?:browser|voice|boxapp)/.*$#', '', $ua);
        }

        $ua = preg_replace('#(?:'
            . '(?: FirePHP| BingWeb|flameblur)/[\d\.]+'
            . '|; [\w\.-]+-user-\d+' // Garmin
            . ')$#', '', $ua);

        // Feed readers
        $ua = preg_replace('#\d+(?= (?:reader|subscriber)s?)#i', '1', $ua);
        if (strpos($ua, 'feedID: ') !== false) {
            $ua = preg_replace('#(?<=feedID: )\d+#', '0', $ua);
        }
        if (strpos($ua, ' feed-id=') !== false) {
            $ua = preg_replace('#(?<= feed-id=)[a-z\d]+#', '0', $ua);
        }

        // Beautify again
        $ua = preg_replace('#(?<= ) +#', '', $ua);
        $ua = str_replace(' ;', ';', $ua);
        $ua = preg_replace('#(?<=;);+#', '', $ua);
        $ua = preg_replace('#[; ]+(?=\))#', '', $ua);
        // Remove locale id (it may appear again after removing of all suffixes, etc.)
        $ua = preg_replace('#; *xx *(?=[);])#', '', $ua);

        if (preg_match('#^\W#', $ua)) {
            $ua = '';
        }
        $ua = substr($ua, 0, 255);
        $ua = trim($ua);

        return $ua;
    }

    /**
     * Check that User-Agent string corresponds to one of popular desktop browsers
     * @static
     * @param string $ua
     * @return bool
     */
    public static function isDesktop($ua)
    {
        if (empty($ua)) {
            return true;
        }

        // fast check for Mobile Safari
        if (strpos($ua, ' Mobile Safari/') !== false) {
            return false;
        }

        if (strpos($ua, '</') !== false || strpos($ua, '<?php') !== false) {
            return true; // spam (tags in UA)
        }

        $windows_platforms = '(?:Windows (?:NT|Vista|XP|2000|ME|98|95|3\.)|Win ?[39])';
        $x11_platforms = '(?:(?:Ubuntu|compatible); ?)?X11;(?: ?Ubuntu;)? ?(?:Linux|SunOS|FreeBSD|OpenBSD|NetBSD|Arch Linux|CrOS|Fedora|BeOS|Mageia|IRIX64)[ ;)]';
        $other_platforms = '(?:Fedora Core \d|Konqueror/\d)';
        $desktop_platforms = "(?:Macintosh; |(?:Windows; ?)?$windows_platforms|$x11_platforms|$other_platforms)";

//        // test Windows Phone in desktop mode
//        if(preg_match('#^Mozilla/5\.0 \(compatible; MSIE (9|10)\.0; Windows NT[^)]* Trident/[56]\.0.* ZuneWP7#', $ua))
//            return false;

//        // test IE 5+
//        if(preg_match('#^Mozilla/[45]\.0 \(compatible; MSIE \d+\.[\dab]+; '.$windows_platforms.'#', $ua)) {
//            if(preg_match('#(?:Google Wireless Transcoder|PalmSource|Windows Phone 6\.5)#i', $ua))
//                return false;
//            return true;
//        }

        // test IE-based browsers for windows
        if (preg_match('#^Mozilla/\d\.\d+ \((?:compatible|Windows); (?:.*; ?)?' . $windows_platforms . '#', $ua)) {
            if (preg_match('#(?:Google Wireless Transcoder|PalmSource|Windows Phone 6\.5)#i', $ua)) {
                return false;
            }
            return true;
        }

        // test IE 10
        if (preg_match('#^Mozilla/\d\.\d+ \((?:compatible; )?(?:MS)?IE \d+\.\d+.*; ?' . $windows_platforms . '#', $ua)) {
            return true;
        }

        // test Firefox/Chrome/Safari/IE 11+
        if (preg_match('#^Mozilla/\d\.\d (?:(?:ArchLinux|Slackware|Fedora)(?:/[\d\.]+)? )?\(' . $desktop_platforms . '#i', $ua)) {
            if (preg_match('#(?:Maemo Browser|Novarra-Vision|Tablet browser)#', $ua)) {
                return false;
            }
            return true;
        }

        // test Opera
        if (preg_match('#^Opera/\d\.\d\d? \(' . $desktop_platforms . '#i', $ua)) {
            if (preg_match('#Opera ?(?:Mini|Mobi|Tablet)#i', $ua)) {
                return false;
            }
            return true;
        }

        $regexp = '#^(?:Mozilla/5\.0 \(compatible; Konqueror/\d.*\)' // test Konqueror
            . '|AppEngine-Google|Apple-PubSub/|check_http/|curl/|ELinks/|facebookexternalhit\b|Feedfetcher-Google;|GoogleEarth/'
            . '|HTMLParser|ia_archiver|iTunes/|Java[/_]|Liferea/|Links |Lynx/|Microsoft Office/|NSPlayer|Outlook-Express/|Outlook-iOS/'
            . '|PHP|php|PycURL/|python-|Python[ -]|Reeder/|SpamBayes/|VLC/|WhatsApp/|Wget|WordPress|WWW\-' // wget, php, java, etc
            . ')#';
        if (preg_match($regexp, $ua)) {
            return true;
        }

        $regexp = '#(?: (?:AOL|America Online Browser) ' // test AOL
            . '|CFNetwork/[\d\.]+ Darwin/\d' // test iOS download library
            . '|[Dd]etector|\.NET CLR|GTB\d|GoogleToolbar'
            . '|HttpClient|HTTPClient|HttpStream|Http_Client|HTTP_Request'
            . '|crontab|K-Meleon/|libwww-perl|[Mm]onitor|multi_get|/Nutch-|WinHttp|\s(?:Win|WOW)64\b|::'
            . ')#';
        if (preg_match($regexp, $ua)) {
            return true;
        }

        return false;
    }

    /**
     * Check that User-Agent string is iPhone or iPod
     * @param string $ua
     * @return bool
     */
    public static function isIphone($ua)
    {
        static $iphone_list = array(
            'Mozilla/5.0 (iPod;',
            'Mozilla/5.0 (iPod touch;',
            'Mozilla/5.0 (iPhone;',
            'Apple iPhone ',
            'Mozilla/5.0 (iPhone Simulator;',
            'Mozilla/5.0 (Aspen Simulator;',
            'Mozilla/5.0 (device; CPU iPhone OS'
        );
        foreach ($iphone_list as $part) {
            if (strpos($ua, $part) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * @static
     * @param string $ua
     * @return bool
     * @deprecated
     */
    public static function isCompactHTML($ua)
    {
        //TODO: use imode_spec.pdf
        return false;
    }

    /**
     * Get group name for User-Agent string
     * @static
     * @param string $ua
     * @return string
     */
    public static function getGroup($ua)
    {
        static $cache = array();
        if (!isset($cache[$ua])) {
            $cache[$ua] = self::_getGroup($ua);
        }
        return $cache[$ua];
    }

    private static function _getGroup($ua)
    {
        if (empty($ua)) {
            return '';
        }

        $ua_lc = strtolower($ua);

        if (strpos($ua, 'Nintendo') !== false) {
            return 'nintendo';
        }

        switch ($ua_lc[0]) {
            case '0':
                if (strncmp($ua, '0Vodafone', 9) === 0) {
                    return 'vodafone';
                }

                break;
            case 'a':
                if (strncmp($ua, 'ACS-', 4) === 0) {
                    return 'nec_acs';
                }

                if (strncmp($ua_lc, 'alcatel', 7) === 0) {
                    return 'alcatel';
                }

                if (strncmp($ua_lc, 'amoi', 4) === 0) {
                    return 'amoi';
                }

                if (strncmp($ua, 'Apple', 5) === 0) {
                    return 'apple';
                }

                if (strncmp($ua, 'ASTRO', 5) === 0) {
                    return 'astro';
                }

                if (strncmp($ua, 'ASUS-', 5) === 0) {
                    return 'asus';
                }

                if (strncmp($ua_lc, 'audiovox', 8) === 0) {
                    return 'audiovox';
                }

                break;
            case 'b':
                if (strncmp($ua_lc, 'benq', 4) === 0) {
                    return 'benq';
                }

                if (strncmp($ua_lc, 'bird', 4) === 0) {
                    return 'bird';
                }

                if (strncmp($ua_lc, 'blackberry', 10) === 0) {
                    return 'blackberry';
                }

                break;
            case 'c':
                if (strncmp($ua, 'Casio', 5) === 0) {
                    return 'casio';
                }

                if (strncmp($ua_lc, 'cdm', 3) === 0) {
                    return 'audiovox_cdm';
                }

                if (strncmp($ua, 'Compal', 6) === 0) {
                    return 'compal';
                }

                break;
            case 'd':
                if (strncmp($ua, 'DoCoMo/', 7) === 0) {
                    return 'imode_docomo';
                }

                break;
            case 'e':
                if (strncmp($ua, 'Ericsson', 8) === 0) {
                    return 'ericsson';
                }

                break;
            case 'f':
                if (strncmp($ua_lc, 'fly', 3) === 0) {
                    return 'fly';
                }

                break;
            case 'g':
                if (strncmp($ua, 'GF-', 3) === 0) {
                    return 'pantech_gf';
                }

                if (strncmp($ua, 'GT-', 3) === 0) {
                    return 'samsung_gt';
                }

                if (strncmp($ua, 'Gradiente', 9) === 0) {
                    return 'gradiente';
                }

                if (strncmp($ua_lc, 'grundig', 7) === 0) {
                    return 'grundig';
                }

                break;
            case 'h':
                if (strncmp($ua, 'Haier', 5) === 0) {
                    return 'haier';
                }

                if (strncmp($ua, 'HTC', 3) === 0) {
                    return 'htc';
                }

                if (strncmp($ua_lc, 'huawei', 6) === 0) {
                    return 'huawei';
                }

                break;
            case 'i':
                if (strncmp($ua, 'i-mobile', 8) === 0) {
                    return 'i-mobile';
                }

                break;
            case 'j':
                if (strncmp($ua, 'J-PHONE', 7) === 0) {
                    return 'softbank_jphone';
                }

                if (strncmp($ua, 'jBrowser', 8) === 0) {
                    return 'jbrowser';
                }

                if (strncmp($ua, 'JUC', 3) === 0) {
                    return 'juc';
                }

                break;
            case 'k':
                if (strncmp($ua, 'Karbonn', 7) === 0) {
                    return 'karbonn';
                }

                if (strncmp($ua, 'KGT/', 4) === 0) {
                    return 'imode_nec_kgt';
                }

                if (strncmp($ua, 'KDDI', 4) === 0) {
                    return 'kddi';
                }

                if (strncmp($ua, 'KWC-', 4) === 0) {
                    return 'kyocera_kwc';
                }
                if (strncmp($ua_lc, 'kyocera', 7) === 0) {
                    return 'kyocera';
                }

                break;
            case 'l':
                if (strncmp($ua_lc, 'lava', 4) === 0) {
                    return 'lava';
                }

                if (strncmp($ua_lc, 'lenovo', 6) === 0) {
                    return 'lenovo';
                }

                if (strncmp($ua, 'LGE-', 4) === 0) {
                    return 'lg_lge';
                }
                if (strncmp($ua, 'LG', 2) === 0) {
                    return 'lg';
                }
                if (strncmp($ua_lc, 'lg-', 3) === 0) {
                    return 'lg';
                }

                break;
            case 'm':
                // Note: "Mozilla/5.0 (Linux; Android" will be tested later, after this switch-case block
                if (strncmp($ua, 'MERIDIAN', 8) === 0) {
                    return 'fly_meridian';
                }

                if (strncmp($ua_lc, 'micromax', 8) === 0) {
                    return 'micromax';
                }

                if (strncmp($ua, 'Mitsu', 5) === 0) {
                    return 'mitsubishi';
                }

                if (strncmp($ua_lc, 'moto', 4) === 0) {
                    return 'motorola';
                }
                if (strncmp($ua_lc, 'mot-', 4) === 0) {
                    return 'motorola_mot';
                }

                if (strncmp($ua, 'Mozilla/5.0 (BlackBerry; ', 25) === 0
                    || strncmp($ua, 'Mozilla/5.0 (BB10; ', 19) === 0
                ) {
                    return 'blackberry_mozilla';
                }

                if (strncmp($ua, 'Mozilla/5.0 (LG-', 16) === 0) {
                    return 'lg_mozilla';
                }

                if (strncmp($ua, 'Mozilla/4.0 (compatible; MSIE 6.0; KDDI-', 40) === 0) {
                    return 'kddi_mozilla';
                }

                if (strncmp($ua, 'Mozilla/5.0 (PlayBook; ', 23) === 0) {
                    return 'playbook';
                }

                if (strpos($ua, 'Windows Phone ') !== false) {
                    if (preg_match('#^Mozilla/5\.0 \(Windows Phone 10\.0; Android \d\.\*; #', $ua)
                        || strncmp($ua, 'Mozilla/5.0 (Windows Phone 8.1; ARM; Trident/7.0; Touch; rv:11.0; IEMobile/11.0; ', 81) === 0
                        || strncmp($ua, 'Mozilla/5.0 (Mobile; Windows Phone 8.1; Android 4.', 50) === 0
                        || strncmp($ua, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows Phone 8.0; Trident/6.0; IEMobile/10.0; ', 83) === 0
                        || strncmp($ua, 'Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5; Trident/5.0; IEMobile/9.0; ', 84) === 0
                        || strncmp($ua, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows Phone OS 7.0; Trident/3.1; IEMobile/7.0; ', 84) === 0
                        || strncmp($ua, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; ', 51) === 0 // Windows Phone 6.5
                    ) {
                        return 'windowsphone';
                    }
                }

                if (strncmp($ua, 'Mozilla/4.0 (MobilePhone ', 25) === 0) {
                    return 'sanyo_mobilephone';
                }

                if (strncmp($ua_lc, 'mozilla/5.0 (playstation ', 25) === 0
                    || strncmp($ua_lc, 'mozilla/4.0 (ps2; ', 18) === 0
                    || strncmp($ua_lc, 'mozilla/4.0 (psp ', 17) === 0
                ) {
                    return 'playstation';
                }

                if (strncmp($ua, 'Mozilla/5.0 (webOS/', 19) === 0) {
                    return 'webos';
                }

                if (strncmp($ua, 'Mozilla/5.0 (SAMSUNG; ', 22) === 0) {
                    return 'samsung_mozilla';
                }

                if (strncmp($ua, 'Mozilla/5.0 (SMART-TV; ', 23) === 0
                    || strpos($ua, ' SmartTV/') !== false
                    || strpos($ua, ' TV Safari/') !== false
                ) {
                    return 'smarttv';
                }

                if (strncmp($ua, 'Mozilla/5.0 (Linux; Tizen ', 26) === 0
                    && strpos($ua, ' Mobile Safari/') !== false
                ) {
                    return 'tizen_mobile';
                }

                if (strncmp($ua, 'Mozilla/5.0 (Mobile; LYF/', 25) === 0) {
                    return 'jiophone';
                }

                break;
            case 'n':
                if (strncmp($ua, 'NativeOperaMini', 15) === 0) {
                    return 'opera_native';
                }

                if (strncmp($ua, 'NEC-', 4) === 0) {
                    return 'nec';
                }

                if (strncmp($ua, 'Nexian', 6) === 0) {
                    return 'nexian';
                }

                break;
            case 'o':
                if (strncmp($ua, 'o2imode/', 8) === 0) {
                    return 'imode_o2';
                }

                if (strncmp($ua, 'Opera/', 6) === 0) {
                    if (strpos($ua, 'Opera Mobile/') !== false) {
                        return 'opera_mobile';
                    }
                    if (strpos($ua, 'Opera Mini/') !== false) {
                        return 'opera_mini';
                    }
                    return 'opera';
                }

                if (strncmp($ua, 'OperaMini', 9) === 0) {
                    return 'opera_mini';
                }

                break;
            case 'p':
                if (strncmp($ua, 'Panasonic', 9) === 0) {
                    return 'panasonic';
                }

                if (strncmp($ua_lc, 'pantech', 7) === 0) {
                    return 'pantech';
                }
                if (strncmp($ua, 'PT-', 3) === 0) {
                    return 'pantech_pt';
                }
                if (strncmp($ua, 'PG-', 3) === 0) {
                    return 'pantech_pg';
                }

                if (strncmp($ua_lc, 'philips', 7) === 0) {
                    return 'philips';
                }

                if (strncmp($ua, 'POLARIS', 7) === 0) {
                    return 'lg_polaris';
                }

                if (strncmp($ua, 'portalmmm/', 10) === 0) {
                    return 'imode_portalmmm';
                }

                break;
            case 'q':
                if (strncmp($ua, 'QC-', 3) === 0) {
                    return 'kyocera_qc';
                }

                if (strncmp($ua, 'Qtek', 4) === 0) {
                    return 'qtek';
                }

                break;
            case 'r':
                if (strncmp($ua, 'Reksio', 6) === 0) {
                    return 'reksio';
                }

                if (strncmp($ua_lc, 'rim', 3) === 0) {
                    return 'blackberry_rim';
                }

                if (strncmp($ua, 'Rover', 5) === 0) {
                    return 'rover';
                }

                break;
            case 's':
                if (strncmp($ua_lc, 'sagem', 5) === 0) {
                    return 'sagem';
                }

                if (strncmp($ua_lc, 'sanyo', 5) === 0) {
                    return 'sanyo';
                }

                if (strncmp($ua_lc, 'samsung-gt', 10) === 0 || strncmp($ua_lc, 'samsung gt', 10) === 0) {
                    return 'samsung_s_gt';
                }
                if (strncmp($ua_lc, 'samsung-sch', 11) === 0) {
                    return 'samsung_s_sch';
                }
                if (strncmp($ua_lc, 'samsung-sec', 11) === 0) {
                    return 'samsung_s_sec';
                }
                if (strncmp($ua_lc, 'samsung-sgh', 11) === 0) {
                    return 'samsung_s_sgh';
                }
                if (strncmp($ua_lc, 'samsung-sph', 11) === 0) {
                    return 'samsung_s_sph';
                }
                if (strncmp($ua_lc, 'samsungsgh', 10) === 0) {
                    return 'samsung_ssgh';
                }
                if (strncmp($ua_lc, 'samsung', 7) === 0) {
                    return 'samsung';
                }
                if (strncmp($ua_lc, 'sam', 3) === 0) {
                    return 'samsung_sam';
                }
                if (strncmp($ua_lc, 'sch-', 4) === 0) {
                    return 'samsung_sch';
                }
                if (strncmp($ua, 'SGH-', 4) === 0) {
                    return 'samsung_sgh';
                }
                if (strncmp($ua, 'SPH-', 4) === 0) {
                    return 'samsung_sph';
                }
                if (strncmp($ua, 'SEC-', 4) === 0) {
                    return 'samsung_sec';
                }

                if (strncmp($ua, 'Sendo', 5) === 0) {
                    return 'sendo';
                }

                if (strncmp($ua_lc, 'sharp', 5) === 0) {
                    return 'sharp';
                }

                if (strncmp($ua, 'SIE-', 4) === 0) {
                    return 'siemens';
                }

                if (strncmp($ua, 'SkyBee', 6) === 0) {
                    return 'skybee';
                }

                if (strncmp($ua_lc, 'sonyericsson', 12) === 0) {
                    return 'sonyericsson';
                }

                if (strncmp($ua, 'Spice', 5) === 0) {
                    return 'spice';
                }

                break;
            case 't':
                if (strncmp($ua, 'Telit', 5) === 0) {
                    return 'telit';
                }

                if (strncmp($ua, 'TSM', 3) === 0) {
                    return 'vitelcom_tsm';
                }

                if (strncmp($ua, 'Toshiba', 7) === 0) {
                    return 'toshiba';
                }

                break;
            case 'u':
                break;
            case 'v':
                if (strncmp($ua, 'Vertu', 5) === 0) {
                    return 'vertu';
                }

                if (strncmp($ua, 'Videocon', 8) === 0) {
                    return 'videocon';
                }

                if (strncmp($ua, 'Vodafone', 8) === 0) {
                    return 'vodafone';
                }

                if (strncmp($ua, 'VX', 2) === 0) {
                    return 'lg_vx';
                }

                break;
            case 'w':
                if (strncmp($ua, 'WinWAP', 6) === 0) {
                    return 'winwap';
                }

                break;
            case 'x':
                break;
            case 'y':
                break;
            case 'z':
                if (strncmp($ua_lc, 'zmax', 4) === 0) {
                    return 'zonda';
                }

                if (strncmp($ua, 'ZTE', 3) === 0) {
                    return 'zte';
                }
                if (strncmp($ua_lc, 'zte-', 4) === 0) {
                    return 'zte';
                }

                break;
        }

        if (strncmp($ua, 'Mozilla/5.0 (Linux; Android ', 28) === 0
            && preg_match('#Mozilla/5\.0 \(Linux; Android [^;]+; ?([^)]+)#', $ua, $match)
        ) {
            $model = trim($match[1]);
            $model_lc = strtolower($model);

            if (strncmp($model, 'ADR', 3) === 0
                || strncmp($model_lc, 'pcdadr', 6) === 0
            ) {
                return 'android_htc_adr';
            }
            if (strncmp($model_lc, 'alcatel', 7) === 0) {
                return 'android_alcatel';
            }
            if (strncmp($model, 'Andromax', 8) === 0
                || strncmp($model, 'New Andromax', 12) === 0
                || strncmp($model, 'Smartfren', 9) === 0
            ) {
                return 'android_smartfren';
            }
            if (strncmp($model, 'Aquaris', 7) === 0) {
                return 'android_aquaris';
            }
            if (strncmp($model, 'Archos', 6) === 0) {
                return 'android_archos';
            }
            if (strncmp($model, 'ASUS', 4) === 0
                || strncmp($model, 'Transformer', 11) === 0
            ) {
                return 'android_asus';
            }
            if (strncmp($model, 'Avvio', 5) === 0) {
                return 'android_avvio';
            }

            if (strncmp($model, 'BLU ', 4) === 0) {
                return 'android_blu';
            }

            if (strncmp($model, 'Coolpad', 7) === 0) {
                return 'android_coolpad';
            }
            if (strncmp($model, 'CPH', 3) === 0) {
                return 'android_oppo_cph';
            }

            if (strncmp($model_lc, 'fly', 3) === 0
                && (!isset($model[3]) || $model[3] === ' ' || $model[3] === '_')
            ) {
                return 'android_fly';
            }

            if (strncmp($model, 'GFIVE', 5) === 0) {
                return 'android_gfive';
            }

            if (strncmp($model, 'Hisense', 7) === 0) {
                return 'android_hisense';
            }
            if (strncmp($model, 'HTC', 3) === 0
                || strncmp($model, 'Desire', 6) === 0
                || strncmp($model, 'Sensation', 9) === 0
            ) {
                return 'android_htc';
            }
            if (strncmp($model_lc, 'huawei', 6) === 0
                || strncmp($model, 'HW-HUAWEI', 9) === 0
            ) {
                return 'android_huawei';
            }

            if ($model_lc[0] === 'i') {
                if (strncmp($model, 'iBall', 5) === 0) {
                    return 'android_iball';
                }
                if (strncmp($model, 'IdeaTab', 7) === 0) {
                    return 'android_lenovo_ideatab';
                }
                if (strncmp($model, 'i-mobile', 8) === 0) {
                    return 'android_imobile';
                }
                if (strncmp($model, 'Infinix', 7) === 0) {
                    return 'android_infinix';
                }
                if (strncmp($model, 'iris', 4) === 0) {
                    return 'android_iris';
                }
                if (strncmp($model, 'itel', 4) === 0) {
                    return 'android_itel';
                }
                if (strncmp($model, 'Ixion', 5) === 0) {
                    return 'android_ixion';
                }
            }

            if (strncmp($model, 'Karbonn', 7) === 0) {
                return 'android_karbonn';
            }
            if (strncmp($model, 'KENEKSI', 7) === 0) {
                return 'android_keneksi';
            }

            if (strncmp($model, 'Lenovo', 6) === 0) {
                return 'android_lenovo';
            }
            if (strncmp($model, 'LG', 2) === 0) {
                return 'android_lg';
            }
            if (strncmp($model, 'LIFETAB', 7) === 0) {
                return 'android_medion_lifetab';
            }

            if ($model[0] === 'M') {
                if (strncmp($model, 'MI ', 3) === 0) {
                    return 'android_xiaomi_mi';
                }
                if (strncmp($model, 'Micromax', 8) === 0) {
                    return 'android_micromax';
                }
                if (strncmp($model, 'MID', 3) === 0) {
                    return 'android_mid';
                }
                if (strncmp($model, 'MITO', 4) === 0) {
                    return 'android_mito';
                }
                if (strncmp($model, 'Mobiistar', 9) === 0) {
                    return 'android_mobiistar';
                }
                if (strncmp($model, 'MB', 2) === 0
                    || strncmp($model, 'MOT-ME', 6) === 0
                    || strncmp($model, 'Moto', 4) === 0
                    || strncmp($model, 'Milestone', 9) === 0
                    || (strncmp($model, 'ME', 2) === 0 && !preg_match('#^ME\d{3}[A-Z]+$#', $model))
                ) {
                    return 'android_motorola';
                }
                if (strncmp($model, 'MTC', 3) === 0) {
                    return 'android_mtc';
                }
            }
            if (strncmp($model_lc, 'xoom', 4) === 0
                || strncmp($model, 'MOT-XT', 6) === 0
                || (strncmp($model, 'XT', 2) === 0 && substr($model, 2, 1) !== 'A')
            ) {
                return 'android_motorolax';
            }

            if (strncmp($model, 'Nexus', 5) === 0) {
                return 'android_nexus';
            }
            if (strncmp($model, 'NOOK', 4) === 0
                || strncmp($model, 'BNTV', 4) === 0
            ) {
                return 'android_nook';
            }

            if (strncmp($model, 'OPPO', 4) === 0) {
                return 'android_oppo';
            }

            if ($model[0] === 'P') {
                if (strncmp($model, 'Philips', 7) === 0) {
                    return 'android_philips';
                }
                if (strncmp($model, 'PMP', 3) === 0 || strncmp($model, 'PMT', 3) === 0) {
                    return 'android_prestigio_pm';
                }
                if (strncmp($model, 'PSP', 3) === 0) {
                    return 'android_prestigio_psp';
                }
                if (strncmp($model, 'PTAB', 4) === 0) {
                    return 'android_polaroid';
                }
            }

            if (strncmp($model, 'QMobile', 7) === 0) {
                return 'android_qmobile';
            }

            if (strncmp($model, 'Redmi', 5) === 0) {
                return 'android_xiaomi_redmi';
            }

            if ($model[0] === 'S' || $model[0] === 'G') {
                if (preg_match('#^(?:SAMSUNG[ -])?(?:SCH|SHW|SPH|SHV)-#', $model)) {
                    return 'android_samsung';
                }
                if (preg_match('#^(?:SAMSUNG[ -])?(GT|SGH|SM)-(.)#', $model, $s_match)) {
                    $submodel = strtolower($s_match[1]);
                    $kind = strtolower($s_match[2]);
                    if ($submodel === 'sm' && strpos('acegjnpst', $kind) !== false) {
                        return 'android_samsung_' . $submodel . '_' . $kind;
                    }
                    return 'android_samsung_' . $submodel;
                }
                if (strncmp($model_lc, 'sony', 4) === 0) {
                    return 'android_sony';
                }
                if (strncmp($model, 'Sprint', 6) === 0) {
                    return 'android_sprint';
                }
            }
            if (strncmp($model, 'Galaxy', 6) === 0
                || preg_match('#^[iI]9\d{3}\b#', $model)
            ) {
                return 'android_samsung_gt';
            }
            if (preg_match('#^(?:[LMSW][KT]|E|U|X|R8)\d\d[aiphw]#', $model)) {
                return 'android_sony';
            }

            if (strncmp($model_lc, 'tab', 3) === 0) {
                return 'android_tablets';
            }
            if (strncmp($model, 'T-Mobile', 8) === 0) {
                return 'android_tmobile';
            }
            if (strncmp($model, 'TECNO', 5) === 0) {
                return 'android_tecno';
            }

            if (strncmp($model_lc, 'droid', 5) === 0) {
                return 'android_verizon';
            }
            if (strncmp($model, 'vivo ', 5) === 0) {
                return 'android_vivo';
            }
            if (strncmp($model, 'Vodafone', 8) === 0) {
                return 'android_vodafone';
            }

            if (strncmp($model, 'ZTE', 3) === 0) {
                return 'android_zte';
            }
        }

        if (strpos($ua, 'Android 3.') !== false || strpos($ua, 'Android/3.') !== false) {
            return 'android3';
        }
        if (strpos($ua, 'Android') !== false) {
            if (strncmp($ua, 'Mozilla/5.0 (Linux; Android', 27) === 0) {
                return 'android_mozilla';
            }
            return 'android';
        }

        if (strncmp($ua, 'Mozilla/5', 9) === 0) {
            if (strpos($ua, '(iPhone') !== false || strpos($ua, '(Aspen Simulator') !== false) {
                return 'apple_iphone';
            }
            if (strpos($ua, '(iPad') !== false) {
                return 'apple_ipad';
            }
            if (strpos($ua, '(iPod') !== false) {
                return 'apple_ipod';
            }
        }

        if (strpos($ua, 'ZuneWP7') !== false) {
            return 'windowsphone_desktop';
        }
        if (strpos($ua, 'Windows Phone OS') !== false) {
            return 'windowsphone';
        }
        if (preg_match('#\(compatible; MSIE \d\.\d+; Windows CE; #', $ua)) {
            return 'windowsce';
        }

        if (strpos($ua, 'Maemo') !== false) {
            return 'maemo';
        }
        if (strpos($ua, 'Nokia') !== false) {
            if (strpos($ua, 'Symbian/3') !== false) {
                return 'nokias3';
            }
            if (strpos($ua, 'Series90') !== false) {
                return 'nokia90';
            }
            if (strpos($ua, 'Series80') !== false) {
                return 'nokia80';
            }
            if (strpos($ua, 'Series60') !== false) {
                return 'nokia60';
            }
            if (strpos($ua, 'Series40') !== false) {
                return 'nokia40';
            }
            if (strncmp($ua, 'Mozilla/', 8) === 0) {
                return 'nokia_mozilla';
            }
            return 'nokia';
        }

        if (strpos($ua, 'BlackBerry') !== false) {
            return 'blackberry_general';
        }

        if (strpos($ua, 'PalmOS') !== false || strpos($ua, 'Blazer') !== false) {
            return 'palm';
        }

        if (strpos($ua, 'Danger hiptop ') !== false) {
            return 'hiphop';
        }

        if (strpos($ua, 'FOMA;') !== false) {
            return 'imode_foma';
        }

        if (strpos($ua, 'SoftBank') !== false) {
            return 'softbank';
        }

        if (strpos($ua, 'UP.Browser') !== false) {
            return 'upbrowser';
        }

        if (preg_match('#(?:\baddon\b|agent\b|\bajax|analyzer\b|archive|\bapi\b|^blitz\.io;|\bblog|bot\b|bot[@_]|\bcatalog\b|capture|check|crawl|dddd|download|extractor|\bfeed|feed\b|fetch|index\b|indexer\b|livecategory|mail|manager|^mozilla/3\.|multi_get|news\b|\bnews|\bnode\.js\b|parser\b|phantomjs|\bping|ping\b|plugin\b|proxy|resolver\b|\brss|rss\b|ruby\b|scanner\b|search|\bseo|server|service|sitemap|slurp|spider|subscriber|test|tracker|upload|\burl|url\b|validat|\bw3c|webclient|website|xml-?rpc|www\.|yahoo|yandex|\bgoo\.gl/|\bbit\.ly/)#', $ua_lc)
            || preg_match('#^(?>[a-z0-9][a-z0-9-]{0,61}[a-z0-9]\.)+[a-z]{2,9}(?>/[\d.]+)?$#', $ua_lc)
            || preg_match('#\b[\w.]+@(?>[a-z0-9][a-z0-9-]{0,61}[a-z0-9]\.)+[a-z]{2,9}\b#', $ua_lc)
            || (strpos($ua_lc, 'http') !== false && strpos($ua_lc, 'mre') === false)
            || (preg_match('#^(?:[A-Za-z?.\s-]+|[A-Za-z?_]+|[A-Za-z\s-]+/?\d+\.[\d.]*[A-Za-z]?)$#', $ua)
                && !preg_match('#(?:android|brew|browser|j2me|maui|meego|mobile|nook|openwave|opera|phone|symb|tablet|trusted|uc browser|ucweb|wap)#', $ua_lc))
        ) {
            return 'bot';
        }

        return '';
    }
}