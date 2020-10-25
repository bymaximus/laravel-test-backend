<?php

namespace App\Containers\Geral\Helpers;

use stdClass;

class StringHelper
{
    public static function addslashes($str)
    {
        $result = '';
        if (!get_magic_quotes_gpc()) {
            $result = addslashes($str);
        } else {
            $result = $str;
        }

        return $result;
    }

    public static function urlEncode($str)
    {
        return urlencode($str);
    }

    public static function urlDecode($str)
    {
        return urldecode($str);
    }

    public static function urlRawEncode($str)
    {
        return rawurlencode($str);
    }

    public static function urlRawDecode($str)
    {
        return rawurldecode($str);
    }

    public static function stripslashes($str)
    {
        return stripslashes($str);
    }

    public static function removeLineBreak($str)
    {
        return str_replace([
            "\n",
            "\r"
        ], '', $str);
    }

    public static function removeExtraSpaces($str)
    {
        $str = str_replace("\t", ' ', $str);
        while (strpos($str, '  ') > -1) {
            $str = str_replace('  ', ' ', $str);
        }

        return self::trim($str);
    }

    public static function clearString($str)
    {
        $str = self::removeLineBreak($str);

        return self::removeExtraSpaces($str);
    }

    public static function trim($str, $chars = " ,\t,\n,\r,\0,\x0B")
    {
        return trim($str, $chars);
    }

    public static function normalize($str)
    {
        $result = '';
        if (get_magic_quotes_gpc()) {
            $result = stripslashes($str);
        } else {
            $result = $str;
        }

        return $result;
    }

    public static function tohtml($str, $quote_style = null)
    {
        return htmlentities($str, $quote_style);
    }

    public static function CorectMBSize($size)
    {
        // less than 1 kb
        if ($size < 1024) {
            return number_format($size, 2) . ' bytes';
        } elseif ($size < 1048576) { // less than 1 mb.
            return number_format($size / 1024, 2) . ' kb';
        } elseif ($size >= 1048576) {
            return number_format($size / 1048576, 2) . ' mb';
        }
    }

    public static function cleanUrl($url)
    {
        $result = trim($url);

        $result = preg_replace('/\\\\/', '/', $result);
        $pattern = "/\/[^\.\/]+\/\.\.\//";

        while (preg_match($pattern, $result)) {
            $result = preg_replace($pattern, '/', $result);
        }
        $result = preg_replace('/(?<!:)\/+/', '/', $result);
        $result = preg_replace('/^\/+/', '', $result);
        $result = preg_replace('/\/$/', '', $result);

        return $result;
    }

    /**
     *
     *
     * Truncate a string to a certain length if necessary,
     * optionally splitting in the middle of a word, and
     * appending the $etc string.
     *
     * @param string $string
     * @param int $length
     * @param string $etc
     * @param bool $break_words
     */
    public static function truncate($string, $length = 80, $etc = '...', $break_words = false)
    {
        $result = '';
        if ($length == 0) {
            return '';
        }
        $string_length = mb_strlen($string);
        if ($string_length > $length) {
            $length -= strlen($etc);
            if (!$break_words) {
                $pos = mb_strpos($string, ' ', $length + 1);
                $_length = ($pos !== false) ? $pos : $string_length;
                $result = mb_substr($string, 0, $_length, 'UTF-8');
            } else {
                $result = mb_substr($string, 0, $length, 'UTF-8') . $etc;
            }
        } else {
            $result = $string;
        }

        return $result;
    }

    /**
     * delete dublicate slashes
     * and fixes relative paths like:
     * /path/../file.png -> /file.png
     * /path/path/../file.png -> /path/file.png
     *
     * @param
     *            $url
     * @return unknown_type
     */
    public static function normalizeURL($url)
    {
        $result = trim($url);

        $result = preg_replace('/\\\\/', '/', $result);
        $pattern = "/\/[^\.\/]+\/\.\.\//";

        while (preg_match($pattern, $result)) {
            $result = preg_replace($pattern, '/', $result);
        }

        $result = preg_replace('/^http:\/\/|^https:\/\//', '', $result);
        $result = preg_replace('/\/+/', '/', $result);

        return $result;
    }

    public static function urlTrim($url)
    {
        $result = trim($url);

        $pattern = "/\/[^\.\/]+\/\.\.\//";

        while (preg_match($pattern, $result)) {
            $result = preg_replace($pattern, '/', $result);
        }

        $result = preg_replace('/http:\/\/|https:\/\//', '', $result);
        $result = preg_replace('/\/+/', '/', $result);

        return $result;
    }

    /**
     * makes urlTrim and will delete www.
     *
     * @param string $url
     * @return string
     */
    public static function urlShort($url)
    {
        $tmp = self::urlTrim($url);
        $tmp = preg_replace("/^www\./", '', $tmp);

        return $tmp;
    }

    public static function pathinfo($path)
    {
        $first_pos = strrpos($path, '://');

        $last_slash_pos = strrpos($path, '/', $first_pos + 3);
        if (!$last_slash_pos) {
            $last_slash_pos = strlen($path);
        }
        $dirname = substr($path, 0, $last_slash_pos);

        $filename = substr($path, $last_slash_pos + 1, strlen($path) - $last_slash_pos - 1);

        // cal ext
        if ($filename) {
            $dotpos = strpos($filename, '.');
            if ($dotpos) {
                $ext = substr($filename, $dotpos + 1, strlen($filename) - $dotpos);
            }
        }

        $result = [];
        $result['dirname'] = trim($dirname);
        $result['filename'] = trim($filename);
        $result['extension'] = trim($ext);

        return $result;
    }

    public static function wordwrap($text, $width, $nl = "<br />\n", $break_words = true)
    {
        return wordwrap($text, $width, $nl, $break_words);
    }

    public static function implode($separator, $items, $except = [])
    {
        $res = '';
        $counter = 0;
        if ($except) {
            foreach ($items as $key => $value) {
                if (!in_array($key, $except)) {
                    if ($counter) {
                        $res .= $separator . $value;
                    } else {
                        $res .= $value;
                    }
                    $counter++;
                }
            }
        } else {
            $res = implode($separator, $items);
        }

        return $res;
    }

    public static function numberFormat($number, $show_empty_decimals = 2)
    {
        $number = trim($number);
        $res = 0;

        if (is_numeric($number)) {
            if ($show_empty_decimals != false && preg_match('/\.0.*/', $number)) {
                // show decimals always
                $res = number_format($number, $show_empty_decimals);
            } elseif (preg_match('/\..*/', $number)) {
                // is float
                $res = number_format($number, 2);
            } elseif ($show_empty_decimals == true) {
                $res = number_format($number, (int) $show_empty_decimals);
            } else {
                // int
                $res = number_format($number);
            }
        }

        return $res;
    }


    public static function replace_safe_chars($matches)
    {
        return '{' . ord($matches[1]) . '}';
    }

    /**
     * url encode string, but encode all not safe chars by {char code} syntax
     *
     * @return unknown_type
     */
    public static function urlSafeEncode($string)
    {
        $string = preg_replace_callback("/(\/|\||\%)/", [
            'String',
            'replace_safe_chars'
        ], $string);
        $string = urlencode($string);

        return $string;
    }

    public static function formatTimeLengthShort($timelength, $show_parts = 1, $prefixes = [])
    {
        if (!$prefixes) {
            $prefixes = [
                'hours' => ' hours',
                'minutes' => ' mins',
                'seconds' => ' secs',
                'years' => ' years',
                'months' => ' months',
                'days' => ' days'
            ];
        }

        return self::formatTimeLength($timelength, $prefixes['hours'], $prefixes['minutes'], $prefixes['seconds'], $prefixes['years'], $prefixes['months'], $prefixes['days'], $show_parts);
    }

    public static function formatTimeLength($timelength, $str_hours = ' hours', $str_minutes = ' mins', $str_seconds = ' secs', $str_years = ' years', $str_months = ' months', $str_days = ' days', $show_parts = 6)
    {
        $result = '';
        $aParts = [];
        if ($timelength < 60) {
            // less than minute
            $aParts[] = intval($timelength) . $str_seconds;
        } elseif ($timelength > 60 && $timelength <= 3600) {
            // more than minute

            $minutes = floor($timelength / 60);
            $seconds = floor(($timelength - $minutes * 60));

            $aParts[] = intval($minutes) . $str_minutes;
            $aParts[] = intval($seconds) . $str_seconds;
        } elseif ($timelength > 3600 && $timelength <= 86400) {
            // more than hour
            $hours = floor($timelength / 3600);
            $minutes = floor(($timelength - $hours * 3600) / 60);
            $seconds = floor(($timelength - ($hours * 3600 + $minutes * 60)));

            $aParts[] = intval($hours) . $str_hours;
            $aParts[] = intval($minutes) . $str_minutes;
            $aParts[] = intval($seconds) . $str_seconds;
        } elseif ($timelength > 86400 && $timelength <= 2678400) {
            // more than day
            $days = floor($timelength / 86400);
            $hours = floor(($timelength - $days * 86400) / 3600);
            $minutes = floor(($timelength - $days * 86400 - $hours * 3600) / 60);
            $seconds = floor(($timelength - $days * 86400 - $hours * 3600 - $minutes * 60) / 60);

            $aParts[] = intval($days) . $str_days;
            $aParts[] = intval($hours) . $str_hours;
            $aParts[] = intval($minutes) . $str_minutes;
            $aParts[] = intval($seconds) . $str_seconds;
        } elseif ($timelength > 2678400 && $timelength <= 32140800) {
            // more than month
            $months = floor($timelength / 2678400);
            $days = floor(($timelength - $months * 2678400) / 86400);
            $hours = floor($timelength - $months * 2678400 - $days * 86400) / 3600;
            $minutes = floor(($timelength - $months * 2678400 - $days * 86400 - $hours * 3600) / 60);
            $seconds = floor(($timelength - ($hours * 3600 + $minutes * 60)));

            $aParts[] = intval($months) . $str_months;
            $aParts[] = intval($days) . $str_days;
            $aParts[] = intval($hours) . $str_hours;
            $aParts[] = intval($minutes) . $str_minutes;
            $aParts[] = intval($seconds) . $str_seconds;
        } elseif ($timelength > 32140800) {
            // more than year
            $years = floor($timelength / 32140800);
            $months = floor(($timelength - $years * 32140800) / 2678400);
            $days = floor(($timelength - $years * 32140800 - $months * 2678400) / 86400);
            $hours = floor(($timelength - $years * 32140800 - $months * 2678400 - $days * 86400) / 3600);
            $minutes = floor(($timelength - $years * 32140800 - $months * 2678400 - $days * 86400 - $hours * 3600) / 60);
            $seconds = floor(($timelength - $years * 32140800 - $months * 2678400 - $days * 86400 - $hours * 3600 - $minutes * 60));

            $aParts[] = intval($years) . $str_years;
            $aParts[] = intval($months) . $str_months;
            $aParts[] = intval($days) . $str_days;
            $aParts[] = intval($hours) . $str_hours;
            $aParts[] = intval($minutes) . $str_minutes;
            $aParts[] = intval($seconds) . $str_seconds;
        }
        $i_max = count($aParts);
        if ($show_parts < $i_max) {
            $i_max = $show_parts;
        }
        for ($i = 0; $i < $i_max; $i++) {
            $result .= $aParts[$i] . ' ';
        }

        return $result;
    }

    public static function toLine($string)
    {
        $string = preg_replace("/\r\n/", ' ', $string);
        $string = preg_replace("/\n/", ' ', $string);
        $string = preg_replace('/ +/', ' ', $string);

        return $string;
    }

    public static function stripNoNumbers($string)
    {
        $ret = preg_replace("/\D/", '', $string);

        return $ret;
    }

    public static function isUrl($url)
    {
        $tmp = explode('?', $url);
        $url = $tmp[0];
        if (preg_match("/\||,| |\"|'|`|\#|\@|\!|\%|\^|\&|\*|\(|\)|>|<|\?|;|~/", urldecode($url))) {
            return false;
        }
        // if (!preg_match("/((http:\/\/)|(https:\/\/))?(www\.)?[^\.(www)]+\..+/",$url)){
        //
        // return false;
        // }
        return true;
    }

    public static function replaceContentVars($arr_params, $content, $preg = true)
    {
        if (is_array($arr_params)) {
            if ($preg == true) {
                foreach ($arr_params as $key => $value) {
                    $content = preg_replace('/' . $key . '/', $value, $content);
                }
            } else {
                foreach ($arr_params as $key => $value) {
                    $content = str_replace($key, $value, $content);
                }
            }
        }

        return $content;
    }

    // Check if string contains only alpha chars, spaces allowed
    // Params: $data - string to check
    // Returns: true - string is alpha
    // false - string is NOT alpha
    public static function is_alpha_space($data)
    {
        return preg_match('/^\D+$/', $data);
    }

    /**
     * Format json string to normal view
     *
     * @param string $json
     */
    public static function json_format($json)
    {
        $tab = '  ';
        $new_json = '';
        $indent_level = 0;
        $in_string = false;

        /*
         * commented out by monk.e.boy 22nd May '08 because my web server is PHP4, and json_ are PHP5 functions... $json_obj = json_decode($json); if($json_obj === false) return false; $json = json_encode($json_obj);
         */
        $len = strlen($json);

        for ($c = 0; $c < $len; $c++) {
            $char = $json[$c];
            switch ($char) {
                case '{':
                case '[':
                    if (!$in_string) {
                        $new_json .= $char . "\n" . str_repeat($tab, $indent_level + 1);
                        $indent_level++;
                    } else {
                        $new_json .= $char;
                    }
                    break;
                case '}':
                case ']':
                    if (!$in_string) {
                        $indent_level--;
                        $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
                    } else {
                        $new_json .= $char;
                    }
                    break;
                case ',':
                    if (!$in_string) {
                        $new_json .= ",\n" . str_repeat($tab, $indent_level);
                    } else {
                        $new_json .= $char;
                    }
                    break;
                case ':':
                    if (!$in_string) {
                        $new_json .= ': ';
                    } else {
                        $new_json .= $char;
                    }
                    break;
                case '"':
                    if ($c > 0 && $json[$c - 1] != '\\') {
                        $in_string = !$in_string;
                    }
                    // no break
                default:
                    $new_json .= $char;
                    break;
            }
        }

        return $new_json;
    }

    public static function insertChar($string, $char, $pos)
    {
        return preg_replace('/^(.{' . ($pos) . '})/', '$1' . $char, $string);
    }

    public static function ucFirst($string, $encoding = 'UTF-8')
    {
        $fc = mb_convert_case(mb_substr($string, 0, 1, $encoding), MB_CASE_UPPER, $encoding);
        $res = $fc . mb_substr($string, 1, mb_strlen($string, $encoding) - 1, $encoding);

        return $res;
    }

    public static function lcFirst($string, $encoding = 'UTF-8')
    {
        $fc = mb_convert_case(mb_substr($string, 0, 1, $encoding), MB_CASE_LOWER, $encoding);
        $res = $fc . mb_substr($string, 1, mb_strlen($string, $encoding) - 1, $encoding);

        return $res;
    }

    public static function ucWords($string, $encoding = 'UTF-8')
    {
        $words = explode(' ', $string);
        foreach ($words as &$w) {
            $w = self::ucFirst($w, $encoding);
        }

        return implode(' ', $words);
    }

    public static function lcWords($string, $encoding = 'UTF-8')
    {
        $words = explode(' ', $string);
        foreach ($words as &$w) {
            $w = self::lcFirst($w, $encoding);
        }

        return implode(' ', $words);
    }

    public static function toLower($string, $encoding = 'UTF-8')
    {
        return mb_strtolower($string, $encoding);
    }

    public static function toUpper($string, $encoding = 'UTF-8')
    {
        return mb_strtoupper($string, $encoding);
    }

    public static function getCharsetList()
    {
        return [
            'US-ASCII' => 'American Standard Code for Information Interchange',
            'windows-1250' => 'Windows Eastern European',
            'windows-1251' => 'Windows Cyrillic',
            'windows-1252' => 'Windows Latin-1',
            'windows-1253' => 'Windows Greek',
            'windows-1254' => 'Windows Turkish',
            'windows-1257' => 'Windows Baltic',
            'ISO-8859-1' => 'Latin Alphabet No. 1',
            'ISO-8859-2' => 'Latin Alphabet No. 2',
            'ISO-8859-4' => 'Latin Alphabet No. 4',
            'ISO-8859-5' => 'Latin/Cyrillic Alphabet',
            'ISO-8859-7' => 'Latin/Greek Alphabet',
            'ISO-8859-9' => 'Latin Alphabet No. 5',
            'ISO-8859-13' => 'Latin Alphabet No. 7',
            'ISO-8859-15' => 'Latin Alphabet No. 9',
            'KOI8-R' => 'KOI8-R, RussianUTF-8',
            'UTF-8' => 'Eight-bit UCS Transformation Format'
        ];
    }

    public static function procentCmpNumbers($num1, $num2)
    {
        $main_num = max($num1, $num2);
        $less_num = min($num1, $num2);

        $diff = $main_num - $less_num;
        if ($main_num > 0) {
            $res = $less_num / $main_num * 100;
            $res = 100 - $res;
        } elseif ($main_num != 0) {
            if ($main_num < 100) {
                $res = $main_num;
            } else {
                $res = $main_num;
            }
        } else {
            $res = 0;
        }
        if ($num1 > $num2) {
            $res = -1 * $res;
        }

        return $res;
    }

    /**
     * Check if a string ends with other string
     * Faster than preg_match
     *
     * @param string $string
     * @param string $test
     * @return boolean
     */
    public static function endsWith($string, $test)
    {
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen) {
            return false;
        }

        return substr_compare($string, $test, -$testlen) === 0;
    }

    /**
     * Check if a string start withs other string
     * Faster than preg_match
     *
     * @param string $string
     * @param string $test
     * @return boolean
     */
    public static function startsWith($string, $test)
    {
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen) {
            return false;
        }

        return substr_compare($string, $test, 0, $testlen) === 0;
    }

    public static function objectToArray($d)
    {
        return (array) $d;
        /*
        if (is_object($d)) {
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            return array_map(array('StringHelper', __FUNCTION__), $d);
        } else {
            return $d;
        }
        */
    }

    public static function arrayToObject($array)
    {
        $object = new stdClass();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $object->{$key} = self::arrayToObject($value);
            } else {
                $object->{$key} = $value;
            }
        }

        return $object;
    }

    public static function getVisitorIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    public static function br2nl($string)
    {
        return preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $string);
    }

    public static function removeAccents($string)
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        $chars = [
            // Decompositions for Latin-1 Supplement
            chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
            chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
            chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
            chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
            chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
            chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
            chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
            chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
            chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
            chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
            chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
            chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
            chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
            chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
            chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
            chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
            chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
            chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
            chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
            chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
            chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
            chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
            chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
            chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
            chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
            chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
            chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
            chr(195) . chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
            chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
            chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
            chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
            chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
            chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
            chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
            chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
            chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
            chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
            chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
            chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
            chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
            chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
            chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
            chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
            chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
            chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
            chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
            chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
            chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
            chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
            chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
            chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
            chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
            chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
            chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
            chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
            chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
            chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
            chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
            chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
            chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
            chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
            chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
            chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
            chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
            chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
            chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
            chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
            chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
            chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
            chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
            chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
            chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
            chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
            chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
            chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
            chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
            chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
            chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
            chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
            chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
            chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
            chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
            chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
            chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
            chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
            chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
            chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
            chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
            chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
            chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
            chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's'
        ];

        $string = strtr($string, $chars);

        return $string;
    }

    public static function validaCNPJ($cnpj = null)
    {

        // Verifica se um número foi informado
        if (empty($cnpj)) {
            return false;
        }

        // Elimina possivel mascara
        $cnpj = preg_replace("/[^0-9]/", "", $cnpj);
        $cnpj = str_pad($cnpj, 14, '0', STR_PAD_LEFT);

        // Verifica se o numero de digitos informados é igual a 11
        if (strlen($cnpj) != 14) {
            return false;
        }

        // Verifica se nenhuma das sequências invalidas abaixo
        // foi digitada. Caso afirmativo, retorna falso
        elseif ($cnpj == '00000000000000' ||
            $cnpj == '11111111111111' ||
            $cnpj == '22222222222222' ||
            $cnpj == '33333333333333' ||
            $cnpj == '44444444444444' ||
            $cnpj == '55555555555555' ||
            $cnpj == '66666666666666' ||
            $cnpj == '77777777777777' ||
            $cnpj == '88888888888888' ||
            $cnpj == '99999999999999') {
            return false;

        // Calcula os digitos verificadores para verificar se o
        // CPF é válido
        } else {
            $j = 5;
            $k = 6;
            $soma1 = 0;
            $soma2 = 0;

            for ($i = 0; $i < 13; $i++) {
                $j = $j == 1 ? 9 : $j;
                $k = $k == 1 ? 9 : $k;

                $soma2 += ($cnpj[$i] * $k);

                if ($i < 12) {
                    $soma1 += ($cnpj[$i] * $j);
                }

                $k--;
                $j--;
            }

            $digito1 = $soma1 % 11 < 2 ? 0 : 11 - $soma1 % 11;
            $digito2 = $soma2 % 11 < 2 ? 0 : 11 - $soma2 % 11;

            return (($cnpj[12] == $digito1) and ($cnpj[13] == $digito2));
        }
    }

    public static function validaCPF($cpf = null)
    {
        // Verifica se um número foi informado
        if (empty($cpf)) {
            return false;
        }

        // Elimina possivel mascara
        $cpf = preg_replace("/[^0-9]/", "", $cpf);
        $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);

        // Verifica se o numero de digitos informados é igual a 11
        if (strlen($cpf) != 11) {
            return false;
        }
        // Verifica se nenhuma das sequências invalidas abaixo
        // foi digitada. Caso afirmativo, retorna falso
        elseif ($cpf == '00000000000' ||
            $cpf == '11111111111' ||
            $cpf == '22222222222' ||
            $cpf == '33333333333' ||
            $cpf == '44444444444' ||
            $cpf == '55555555555' ||
            $cpf == '66666666666' ||
            $cpf == '77777777777' ||
            $cpf == '88888888888' ||
            $cpf == '99999999999') {
            return false;
        // Calcula os digitos verificadores para verificar se o
        // CPF é válido
        } else {
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf[$c] * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf[$c] != $d) {
                    return false;
                }
            }
            return true;
        }
    }
}
