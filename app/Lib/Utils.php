<?php
/** @noinspection PhpUnused */

namespace App\Lib;

/**
 *
 */
class Utils
{

    const bool LOGGING = false;
    const string LOG_FILE = '../upload/debug.log';
    const int CACHE_VALUE = 1;

    const string CLIENT_KEY_SALT = '';

    /**
     * @return bool
     */
    public static function isDeveloper(): bool
    {
        return defined('IS_DEVELOPMENT');
    }

    /**
     * @param string $filePath
     * @return string
     */
    public static function getScriptCache(string $filePath = ''): string
    {
        if (preg_match('#^/?(js|css)/.*\.(css|js)$#', $filePath) && is_file($filePath)) {
            return '?' . (self::isDeveloper() ? filemtime($filePath) : self::CACHE_VALUE);
        }

        return '';
    }

    /**
     * @param array $data
     * @param string $type
     * @return void
     */
    public static function log(array $data, string $type): void
    {

        if (!self::LOGGING) {
            return;
        }

        $date = date('d.m.Y H:i:s');
        @file_put_contents(self::LOG_FILE, "\n---- $type - $date ----\n" . print_r($data, true) . "\n ----------- \n", FILE_APPEND);
    }

    /**
     * @param string $target
     */
    public static function redirect(string $target = ''): void
    {
        if ($target) {
            header('location: ' . $target);
            exit;
        }
    }

    /**
     * @param int $bytes
     * @return string
     */
    public static function formatBytes(int $bytes = 0): string
    {
        $units = [
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB',
            'EB',
            'ZB',
            'YB'
        ];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        return number_format($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    /**
     * @return string
     */
    public static function getServerUrl(): string
    {

        $port = $_SERVER['SERVER_PORT'] ?? 80;
        $protocol = 'http';

        if ((int)$port == 80) {
            $port = '';
        }

        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $protocol = 'https';
        }

        return $protocol . '://' . $_SERVER['SERVER_NAME'] . ((empty($port)) ? '' : ':' . $port);
    }

    /**
     * @param string $url
     * @param string $text
     * @param string $target
     * @return string
     */
    public static function createLink(string $url = '', string $text = '', string $target = ''): string
    {
        return '<a href="' . $url . '"' . (($target) ? ' target="' . $target . '"' : '') . '>' . ($text ?: $url) . '</a>';
    }


    /**
     * @return string
     */
    public static function getDomain(): string
    {
        return preg_replace('/^(([a-z0-9-_]+)\.)?(.*)$/', '\\3', $_SERVER['HTTP_HOST'] ?? '');
    }

    /**
     * @return bool
     */
    public static function isLocalhost(): bool
    {
        // if this is localhost
        $serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
        return ($serverAddr == '127.0.0.1' || $serverAddr == '::1');
    }

    /**
     * @return string
     */
    public static function getClientIP(): string
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        return ($ip === '::1') ? '127.0.0.1' : $ip;
    }

    /**
     * @return bool
     */
    public static function isAjaxCall(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') == 'xmlhttprequest';
    }

    /**
     * @param mixed $input
     * @return array|string
     */
    public static function Char2Utf8(mixed $input): array|string
    {
        if (is_array($input)) {

            $output = [];

            foreach ($input as $key => $value) {
                $output[$key] = self::Char2Utf8($value);
            }

            return $output;
        } else {
            return (gettype($input) == 'string') ? mb_convert_encoding(str_replace(chr(194), '', $input), 'UTF-8', 'ISO-8859-1') : $input;
        }
    }

    /**
     * @param array|object $input
     * @return array|object
     */
    public static function object2array(array|object $input): array|object
    {

        $return = [];

        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $key = str_replace(':protected', '', $key);
                $return[$key] = self::object2array($value);
            }
        } else {
            $varList = (is_object($input)) ? get_object_vars($input) : [];

            if ($varList) {
                foreach ($varList as $key => $value) {
                    $key = str_replace(':protected', '', $key);
                    $return[$key] = ($key && !$value) ? null : self::object2array($value);
                }
            } else {
                return $input;
            }
        }

        return $return;
    }

    /**
     * @param array $array
     * @param string $index
     * @param int $order
     * @param int $orderType
     * @return array
     */
    public static function arraySortByIndex(array $array = [], string $index = '', int $order = SORT_ASC, int $orderType = SORT_REGULAR): array
    {

        if (is_array($array) && sizeof($array)) {
            $keys = array_keys($array);
            $arrayMultiSort = array_column($array, $index);
            array_multisort($arrayMultiSort, $order, $orderType, $array, $keys);
            $array = array_combine($keys, $array);
        }

        return $array;
    }

    /**
     * @param string $cookieName
     * @param string $cookieValue
     * @param int $timeout
     * @return void
     */
    public static function setCookie(string $cookieName = '', string $cookieValue = '', int $timeout = 0): void
    {

        if (!$cookieName) {
            return;
        }

        $domain = '.' . self::getDomain();
        setcookie($cookieName, '', TIME_NOW - $timeout, '/', $domain);

        if ($timeout > 0) {
            setcookie($cookieName, $cookieValue, $timeout, '/', $domain, true, true);
        }
    }

    /**
     * @param string $salt
     * @return string
     */
    public static function getRequestToken(string $salt = ''): string
    {
        return self::getClientAccessToken($salt);
    }

    /**
     * @param string $salt
     * @return string
     */
    public static function getClientAccessToken(string $salt = ''): string
    {
        $date = date('Y-m-d H', TIME_NOW);

        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $hash_1 = md5($date . self::getClientIP() . $userAgent . $salt);

        $hash_3 = substr($hash_1, 12, 5);
        $hash_1 = substr($hash_1, 6, 6);

        $hash_2 = substr(md5(self::CLIENT_KEY_SALT . self::getClientIP() . $userAgent . $date . $salt), 5, 14);

        return strtoupper($hash_1 . '-' . $hash_2 . '-' . $hash_3);
    }

    /**
     * TODO: Try not tu use this ... Switch it to smtp
     * TODO: Add content validation to prevent spam / hacks
     *
     * @param string $recipient
     * @param string $subject
     * @param string $content
     * @param string $type
     * @return bool
     */
    public static function sendMail(string $recipient = '', string $subject = '', string $content = '', string $type = 'plain'): bool
    {

        $cfg = Registry::get('config')['site'];

        if (!($recipient && $subject && $content && $cfg['email_sender'])) {
            return false;
        }

        // Additional header
        $sHeader = 'MIME-Version: 1.0' . "\r\n";
        $sHeader .= 'Content-type: text/' . (($type == 'html') ? 'html' : 'plain') . '; charset=utf-8' . "\r\n";
        $sHeader .= 'From: ' . $cfg['site']['name'] . ' <' . $cfg['email_sender'] . '>' . "\r\n";
        $sHeader .= "Subject: $subject\r\n";
        $sHeader .= "X-Mailer: PHP/" . PHP_VERSION;

        if ($type == 'html') {
            $content = nl2br($content);
        }

        return @mail($recipient, $subject, $content, $sHeader);
    }

    /**
     * @param string $fileName
     * @return string
     */
    public static function getSafeFileName(string $fileName = ''): string
    {

        $newFileName = str_replace('\\', '', strip_tags(urldecode($fileName)));
        $newFileName = str_replace('/', '', $newFileName);
        $newFileName = str_replace('..', '', $newFileName);

        $newFileName = self::cleanup($newFileName);

        $newFileName = str_replace('\\', '', $newFileName);
        $newFileName = str_replace('/', '', $newFileName);
        $newFileName = str_replace('..', '', $newFileName);
        $newFileName = str_replace(' ', '-', $newFileName);
        $newFileName = preg_replace('/[^A-Za-z0-9-+.]/', '', $newFileName);

        return preg_replace('/-\+/', '-', $newFileName);
    }

    /**
     * @param int $timestamp
     * @return string
     */
    public static function formatTime(int $timestamp = 0): string
    {
        $aDate = [];

        $secondsInAMinute = 60;
        $secondsInAnHour = 60 * $secondsInAMinute;
        $secondsInADay = 24 * $secondsInAnHour;

        // extract days
        $days = floor($timestamp / $secondsInADay);

        // extract hours
        $hourSeconds = $timestamp % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        // extract minutes
        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        // extract the remaining seconds
        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);

        if ($days >= 1) {
            $aDate[] = $days . 'd';
        }

        $timeList = [];

        if ($hours > 0) {
            $timeList[] = $hours;
        }
        if ($minutes > 0) {
            $timeList[] = $minutes;
        }
        if ($hours > 0) {
            $timeList[] = $seconds;
        }

        $aDate[] = implode(':', $timeList);

        return implode(' ', $aDate);
    }

    /**
     * @var array|string[]
     */
    private static array $strictRegex = [
        'integer' => '[^0-9]',
        'string' => '[^ a-zA-Z0-9_]',
        true => '[^ a-zA-Z0-9,\[\]@\-_.:+*%()!?]' // default
    ];

    /**
     * Clean up a string from XSS and other attacks
     * Original function by Reto @ 2008
     *
     * @param string $input
     * @param bool $strict
     * @param string $allowedTags
     * @return string
     */
    public static function cleanup(string $input = '', bool $strict = false, string $allowedTags = ''): string
    {
        $input = strip_tags($input, $allowedTags);

        if ($strict) {
            return preg_replace('#' . self::$strictRegex[$strict] . '#', '', $input);
        }

        // don't use empty $replaceString because then no XSS-remove will be done
        $replaceString = '<x>';

        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        $input = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x19])/', '', $input);

        // straight replacements, the user should never need these since they're normal characters
        // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
        $searchHexEncodings = '/&#[xX]0{0,8}(21|22|23|24|25|26|27|28|29|2a|2b|2d|2f|30|31|32|33|34|35|36|37|38|39|3a'
            . '|3b|3d|3f|40|41|42|43|44|45|46|47|48|49|4a|4b|4c|4d|4e|4f|50|51|52|53|54|55|56|57|58|59|5a|5b|5c|5d|5e'
            . '|5f|60|61|62|63|64|65|66|67|68|69|6a|6b|6c|6d|6e|6f|70|71|72|73|74|75|76|77|78|79|7a|7b|7c|7d|7e);?/i';
        $searchUnicodeEncodings = '/&#0{0,8}(33|34|35|36|37|38|39|40|41|42|43|45|47|48|49|50|51|52|53|54|55|56|57|58|59'
            . '|61|63|64|65|66|67|68|69|70|71|72|73|74|75|76|77|78|79|80|81|82|83|84|85|86|87|88|89|90|91|92|93|94|95'
            . '|96|97|98|99|100|101|102|103|104|105|106|107|108|109|110|111|112|113|114|115|116|117|118|119|120|121|122'
            . '|123|124|125|126);?/i';
        while (preg_match($searchHexEncodings, $input) || preg_match($searchUnicodeEncodings, $input)) {
            $input = preg_replace_callback($searchHexEncodings, function ($m) {
                return chr(hexdec($m[1]));
            }, $input);
            $input = preg_replace_callback($searchUnicodeEncodings, function ($m) {
                return chr($m[1]);
            }, $input);
        }

        // now the only remaining whitespace attacks are \t, \n, and \r
        $ra1 = [
            'javascript',
            'vbscript',
            'expression',
            'applet',
            'meta',
            'xml',
            'blink',
            'link',
            'style',
            'script',
            'embed',
            'object',
            'iframe',
            'frame',
            'frameset',
            'ilayer',
            'layer',
            'bgsound',
            'title',
            'base',
            'onabort',
            'onactivate',
            'onafterprint',
            'onafterupdate',
            'onbeforeactivate',
            'onbeforecopy',
            'onbeforecut',
            'onbeforedeactivate',
            'onbeforeeditfocus',
            'onbeforepaste',
            'onbeforeprint',
            'onbeforeunload',
            'onbeforeupdate',
            'onblur',
            'onbounce',
            'oncellchange',
            'onchange',
            'onclick',
            'oncontextmenu',
            'oncontrolselect',
            'oncopy',
            'oncut',
            'ondataavailable',
            'ondatasetchanged',
            'ondatasetcomplete',
            'ondblclick',
            'ondeactivate',
            'ondrag',
            'ondragend',
            'ondragenter',
            'ondragleave',
            'ondragover',
            'ondragstart',
            'ondrop',
            'onerror',
            'onerrorupdate',
            'onfilterchange',
            'onfinish',
            'onfocus',
            'onfocusin',
            'onfocusout',
            'onhelp',
            'onkeydown',
            'onkeypress',
            'onkeyup',
            'onlayoutcomplete',
            'onload',
            'onlosecapture',
            'onmousedown',
            'onmouseenter',
            'onmouseleave',
            'onmousemove',
            'onmouseout',
            'onmouseover',
            'onmouseup',
            'onmousewheel',
            'onmove',
            'onmoveend',
            'onmovestart',
            'onpaste',
            'onpropertychange',
            'onreadystatechange',
            'onreset',
            'onresize',
            'onresizeend',
            'onresizestart',
            'onrowenter',
            'onrowexit',
            'onrowsdelete',
            'onrowsinserted',
            'onscroll',
            'onselect',
            'onselectionchange',
            'onselectstart',
            'onstart',
            'onstop',
            'onsubmit',
            'onunload'
        ];
        $raTags = [
            'applet',
            'meta',
            'xml',
            'blink',
            'link',
            'style',
            'script',
            'embed',
            'object',
            'iframe',
            'frame',
            'frameset',
            'ilayer',
            'layer',
            'bgsound',
            'title',
            'base'
        ];
        $raAttributes = [
            'style',
            'onabort',
            'onactivate',
            'onafterprint',
            'onafterupdate',
            'onbeforeactivate',
            'onbeforecopy',
            'onbeforecut',
            'onbeforedeactivate',
            'onbeforeeditfocus',
            'onbeforepaste',
            'onbeforeprint',
            'onbeforeunload',
            'onbeforeupdate',
            'onblur',
            'onbounce',
            'oncellchange',
            'onchange',
            'onclick',
            'oncontextmenu',
            'oncontrolselect',
            'oncopy',
            'oncut',
            'ondataavailable',
            'ondatasetchanged',
            'ondatasetcomplete',
            'ondblclick',
            'ondeactivate',
            'ondrag',
            'ondragend',
            'ondragenter',
            'ondragleave',
            'ondragover',
            'ondragstart',
            'ondrop',
            'onerror',
            'onerrorupdate',
            'onfilterchange',
            'onfinish',
            'onfocus',
            'onfocusin',
            'onfocusout',
            'onhelp',
            'onkeydown',
            'onkeypress',
            'onkeyup',
            'onlayoutcomplete',
            'onload',
            'onlosecapture',
            'onmousedown',
            'onmouseenter',
            'onmouseleave',
            'onmousemove',
            'onmouseout',
            'onmouseover',
            'onmouseup',
            'onmousewheel',
            'onmove',
            'onmoveend',
            'onmovestart',
            'onpaste',
            'onpropertychange',
            'onreadystatechange',
            'onreset',
            'onresize',
            'onresizeend',
            'onresizestart',
            'onrowenter',
            'onrowexit',
            'onrowsdelete',
            'onrowsinserted',
            'onscroll',
            'onselect',
            'onselectionchange',
            'onselectstart',
            'onstart',
            'onstop',
            'onsubmit',
            'onunload'
        ];
        $raProtocols = [
            'javascript',
            'vbscript',
            'expression'
        ];

        // remove the potential &#xxx; stuff for testing
        $inputNext = preg_replace('/(&#[xX]?0{0,8}(9|10|13|a|b);)*\s*/i', '', $input);
        $ra = [];

        foreach ($ra1 as $ra1word) {
            // stripos() is faster than the regular expressions used later
            // and because the words we're looking for only have chars < 0x80
            // we can use the non-multibyte safe version
            if (stripos($inputNext, $ra1word) !== false) {
                // keep list of potential words that were found
                if (in_array($ra1word, $raProtocols)) {
                    $ra[] = [
                        $ra1word,
                        'raProtocol'
                    ];
                }
                if (in_array($ra1word, $raTags)) {
                    $ra[] = [
                        $ra1word,
                        'raTag'
                    ];
                }
                if (in_array($ra1word, $raAttributes)) {
                    $ra[] = [
                        $ra1word,
                        'raAttribute'
                    ];
                }
                // some keywords appear in more than one array
                // these get multiple entries in $ra, each with the appropriate type
            }
        }
        // only process potential words
        if (sizeof($ra)) {
            // keep replacing as long as the previous round replaced something
            $found = true;
            while ($found == true) {
                $input_before = $input;
                $sfRa = sizeof($ra);
                for ($i = 0; $i < $sfRa; ++$i) {
                    $pattern = '';
                    $stRa = strlen($ra[$i][0]);
                    for ($j = 0; $j < $stRa; ++$j) {
                        if ($j > 0) {
                            $pattern .= '((&#[xX]0{0,8}([9ab]);)|(&#0{0,8}(9|10|13);)|\s)*';
                        }
                        $pattern .= $ra[$i][0][$j];
                    }
                    // handle each type a little different (extra conditions to prevent false positives a bit better)
                    switch ($ra[$i][1]) {
                        case 'raProtocol':
                            // these take the form of e.g. 'javascript:'
                            $pattern .= '((&#[xX]0{0,8}([9ab]);)|(&#0{0,8}(9|10|13);)|\s)*(?=:)';
                            break;
                        case 'rTag':
                            // these take the form of e.g. '<SCRIPT[^\da-z] ....';
                            $pattern = '(?<=<)' . $pattern . '((&#[xX]0{0,8}([9ab]);)|(&#0{0,8}(9|10|13);)|\s)*(?=[^\da-z])';
                            break;
                        case 'raAttribute':
                            // these take the form of e.g. 'onload='  Beware that a lot of characters are allowed
                            // between the attribute and the equal sign!
                            $pattern .= '[\s\!\#\$\%\&\(\)\*\~\+\-\_\.\,\:\;\?\@\[\/\|\\\\\]\^\`]*(?==)';
                            break;
                    }
                    $pattern = '/' . $pattern . '/i';
                    // add in <x> to nerf the tag
                    $replacement = substr_replace($ra[$i][0], $replaceString, 2, 0);
                    // filter out the hex tags
                    $input = preg_replace($pattern, $replacement, $input);
                    if ($input_before == $input) {
                        // no replacements were made, so exit the loop
                        $found = false;
                    }
                }
            }
        }

        return $input;
    }

}
