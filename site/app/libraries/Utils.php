<?php

namespace app\libraries;

use app\models\User;
use Ds\Set;

/**
 * Class Utils
 */
class Utils {

    /**
     * Defines a new default str_pad that's useful for things like parts of a datetime
     *
     * @param mixed  $string
     * @param int    $pad_width  [optional]
     * @param string $pad_string [optional]
     * @param int    $pad_type   [optional]
     *
     * @return string
     */
    public static function pad($string, $pad_width = 2, $pad_string = '0', $pad_type = STR_PAD_LEFT) {
        return str_pad($string, $pad_width, $pad_string, $pad_type);
    }

    /**
     * Removes the trailing comma at the end of any JSON block. This means that if you had:
     * [ "element": { "a", "b", }, ]
     * this function would return:
     * [ "element": { "a", "b" } ]
     *
     * We do this as we have the potential of trailing commas in the JSON files that are generated by
     * the submission server
     *
     * @param string $json
     *
     * @return string
     */
    public static function removeTrailingCommas(string $json): string {
        $json = preg_replace('/,\s*([\]}])/m', '$1', $json);
        return $json;
    }

    /**
     * Generates a pseudo-random string that should be cryptographically secure for use
     * as tokens and other things where uniqueness is of absolute importance. The generated
     * string is twice as long as the given number of bytes as the parameter.
     *
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @param int $bytes
     *
     * @return string
     */
    public static function generateRandomString(int $bytes = 16): string {
        /** @noinspection PhpUnhandledExceptionInspection */
        return bin2hex(random_bytes($bytes));
    }

    /**
     * Given a string, convert all newline characters to "<br />" while also performing htmlentities on all elements
     * that are not for the new lines
     *
     * @param string $string
     *
     * @return string
     */
    public static function prepareHtmlString(string $string): string {
        $string = str_replace("<br>", "<br />", nl2br($string));
        $string = explode("<br />", $string);
        return implode("<br />", array_map("htmlentities", $string));
    }

    /**
     * Gets the last element of an array. As PHP arrays are technically ordered maps, this will return the last
     * element that was inserted into that map regardless of how the keys might be ordered. This is useful especially
     * for associative arrays that do not have numeric keys or the keys are out of order and we can't just use indices
     * as in other languages.
     *
     * @return mixed|null
     */
    public static function getLastArrayElement(array $array) {
        $temp = array_slice($array, -1);
        return (count($temp) > 0) ? array_pop($temp) : null;
    }

    /**
     * Gets the first element of an array. This can be used for associate arrays like the above
     * getLastArrayElement defined above.
     *
     * @return mixed|null
     */
    public static function getFirstArrayElement(array $array) {
        foreach ($array as $value) {
            return $value;
        }
        return null;
    }

    /**
     * Wrapper around the PHP function setcookie that deals with figuring out if we should be setting this cookie
     * such that it should only be accessed via HTTPS (secure) as well as allow easily passing an array to set as
     * the cookie data. This will also set the value in the $_COOKIE superglobal so that it's available without a
     * page reload.
     *
     * @param string        $name name of the cookie
     * @param string|array  $data data of the cookie, if array, will json_encode it
     * @param int           $expire when should the cookie expire
     *
     * @return bool true if successfully able to set the cookie, else false
     */
    public static function setCookie(string $name, $data, int $expire = 0): bool {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] !== 'off';
        $_COOKIE[$name] = $data;
        return setcookie($name, $data, $expire, "/", "", $secure);
    }

    /**
     * Given a filename, determine if it is an image.
     * TOOD: Make this a stronger check than just on the appended file extension to the naem
     *
     * @param string $filename
     *
     * @return bool true if filename references an image else false
     */
    public static function isImage(string $filename): bool {
        return (substr($filename, -4) == ".png") ||
            (substr($filename, -4) == ".jpg") ||
            (substr($filename, -5) == ".jpeg") ||
            (substr($filename, -4) == ".gif");
    }

    public static function checkUploadedImageFile($id) {
        if (isset($_FILES[$id])) {
            foreach ($_FILES[$id]['tmp_name'] as $file_name) {
                if (file_exists($file_name)) {
                    $mime_type = mime_content_type($file_name);
                    if (substr($mime_type, 0, strrpos($mime_type, "/")) !== "image" || getimagesize($file_name) === false) {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Compares two potentially null values using greater-than comparison.
     * @param mixed $gt_left Left operand for greater-than comparison
     * @param mixed $gt_right Righ operand for greater-than comparison
     * @return bool True if $dtL > $dtR and neither are null, otherwise false
     */
    public static function compareNullableGt($gt_left, $gt_right) {
        return $gt_left !== null && $gt_right !== null && $gt_left > $gt_right;
    }

    /**
     * Calculates a percent value (0 to 1) for the given dividend and divisor.
     *  This method will suppress divide-by-zero warnings and just return NAN
     * @param int $dividend The number being divided
     * @param int $divisor The number to divide with
     * @param bool $clamp If the result should be at most 1 (more than 100% impossible)
     * @return float
     */
    public static function safeCalcPercent($dividend, $divisor, $clamp = false) {
        if (intval($divisor) === 0) {
            return NAN;
        }

        // Convert dividend to float so we don't truncate the quotient
        $result = floatval($dividend) / $divisor;

        if ($result > 1.0 && $clamp === true) {
            return 1.0;
        }
        elseif ($result < 0.0 && $clamp === true) {
            return 0.0;
        }
        return $result;
    }

    /**
     * Gets a function to compare two objects by reference for functions like 'array_udiff'
     *  Credit to method: https://stackoverflow.com/a/27830923/2972004
     * As noted in the comments (and observed), simply comparing two references using `===` will not work
     * @return \Closure
     */
    public static function getCompareByReference() {
        return function ($a, $b) {
            return strcmp(spl_object_hash($a), spl_object_hash($b));
        };
    }

    /*
     * Given an array of students, returns a json object of formated student names in the form:
     * First_Name Last_Name <student_id>
     * Students in the null section are at the bottom of the list in the form:
     * (In null section) First_Name Last_Name <student_id>
     * Optional param to show previous submission count
     * students_version is an array of user and their highest submitted version
     */

    public static function getAutoFillData($students, $students_version = null, $append_numeric_id = false): string {
        $students_full = new Set();
        $null_students = new Set();
        foreach ($students as $student) {
            $student_entry = [
                'value' => $student->getId(),
                'label' => $student->getDisplayedFirstName() . ' ' . $student->getDisplayedLastName() . ' <' . $student->getId() . '>'
            ];

            if ($append_numeric_id) {
                $student_entry['label'] .= ' <' . $student->getNumericId() . '>';
            }

            if ($students_version !== null) {
                if ($student->getRegistrationSection() !== null && array_key_exists($student->getId(), $students_version)) {
                    if ($students_version[$student->getId()] !== 0) {
                        $student_entry['label'] .= ' (' . $students_version[$student->getId()] . ' Prev Submission)';
                    }
                }
                $students_full->add($student_entry);
            }
            elseif ($students_version === null) {
                if ($student->getRegistrationSection() === null) {
                    $student_entry['label'] = '[NULL section] ' . $student_entry['label'];
                    $null_students->add($student_entry);
                }
                else {
                    $students_full->add($student_entry);
                }
            }
        }
        return json_encode(array_merge($students_full->toArray(), $null_students->toArray()));
    }

   /**
    * Convert the shorthand byte notation in php.ini to bytes.
    * E.g : php returnBytes(ini_get('post_max_size'))
    * Src : https://www.php.net/manual/en/function.ini-get.php
    * @param string $size_str
    * @return int
    */
    public static function returnBytes(string $size_str): int {
        switch (strtolower(substr($size_str, -1))) {
            case 'm':
                return (int) $size_str * 1048576;
            case 'k':
                return (int) $size_str * 1024;
            case 'g':
                return (int) $size_str * 1073741824;
            default:
                return (int) $size_str;
        }
    }

    /**
     * Convert bytes to a specified format thats human readable
     * E.g : MB, 10485760 => 10MB
     * @param string $format
     * @param int $bytes
     * @param bool $round should the result be rounded to the nearest number
     * @return string
     */
    public static function formatBytes(string $format, int $bytes, bool $round = false): string {
        $formats = ['b' => 0, 'kb' => 1, 'mb' => 2];
        $result = $bytes / pow(1024, floor($formats[strtolower($format)]));
        if ($round) {
            $result = round($result);
        }

        return $result . (strtoupper($format));
    }

    /**
     * Multibyte safe version of {@see str_split}.
     *
     * @param string $string
     * @param int $length
     * @return string[]
     */
    public static function mb_str_split(string $string, int $length = 1): array {
        $arr = [];
        $str_length = mb_strlen($string, 'UTF-8');
        for ($i = 0; $i < $str_length; $i += $length) {
            $arr[] = mb_substr($string, $i, $length, 'UTF-8');
        }
        return $arr;
    }

    /**
     * Remove comments from the given string.
     * This function will remove any comments that are considered valid comments in the c programming language including
     * single line, end of line, or multi-line comments.
     *
     * It will not work for html, python, etc comments.
     *
     * @param string $str
     * @return string|null Original string with comments removed, or null on failure.
     */
    public static function stripComments(string $str): ?string {
        return preg_replace('/\/\*[\s\S]*?\*\/|\/\/.*/', '', $str);
    }

    /**
     * Escape double quotes in the given string.
     *
     * @param string $str
     * @return string|null Original string with double quotes escaped, or null on failure.
     */
    public static function escapeDoubleQuotes(string $str): ?string {
        return preg_replace('["]', '\"', $str);
    }
}
