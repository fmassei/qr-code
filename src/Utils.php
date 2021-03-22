<?php
declare(strict_types=1);

namespace fmassei\QrCode;

use Exception;

class Utils {
    protected static function strComponentToHex(string $hex, int $length, int $n) : int {
        return hexdec($length == 6 ? substr($hex, $n*2, 2)
                                             : ($length == 3 ? str_repeat(substr($hex, $n, 1), 2)
                                                             : 0));
    }
    public static function hexToRgb(string $hex, int|false $alpha = false) : array {
        $hex      = str_replace('#', '', $hex);
        $length   = strlen($hex);
        $rgb['r'] = self::strComponentToHex($hex, $length, 0);
        $rgb['g'] = self::strComponentToHex($hex, $length, 1);
        $rgb['b'] = self::strComponentToHex($hex, $length, 2);
        if ($alpha)
            $rgb['a'] = $alpha;
        return $rgb;
    }

    /**
     * @param string $path
     * @throws Exception
     */
    public static function getMimeType(string $path): string
    {
        $ret = mime_content_type($path);
        if (!is_string($ret))
            throw new Exception('Could not determine mime type');
        if (!preg_match('#^image/#', $ret))
            throw new Exception('Logo path is not an image');
        return $ret;
    }
}
