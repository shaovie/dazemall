<?php
/**
 * @Author shaowei
 * @Date   2015-12-01
 */

namespace src\common;

class JsCssLoader
{
    private static $_CONFIG_PATH    = JS_CSS_CONFIG_PATH;
    private static $_JS_PATH        = 'js_map.php';
    private static $_CSS_PATH       = 'css_map.php';
    private static $_jsArray        = array();
    private static $_cssArray       = array();

    private static function loadJs()
    {
        if (empty(self::$_jsArray)) {
            self::$_jsArray = include self::$_CONFIG_PATH . DIRECTORY_SEPARATOR . self::$_JS_PATH;
        }
    }

    private static function loadCss()
    {
        if (empty(self::$_cssArray)) {
            self::$_cssArray = include self::$_CONFIG_PATH . DIRECTORY_SEPARATOR . self::$_CSS_PATH;
        }
    }

    public static function outJs($key)
    {
        self::loadJs();
        $key = trim($key);
        if (isset(self::$_jsArray[$key])) {
            $uri = self::$_jsArray[$key];
            $host = Util::getAssetsUrlBase($uri);
            echo "<script type=\"text/javascript\" charset=\"utf-8\" src=\"" . $host . $uri . "\"></script>\n";
        }
    }

    public static function outCss($key)
    {
        self::loadCss();
        $key = trim($key);
        if (isset(self::$_cssArray[$key])) {
            $uri = self::$_cssArray[$key];
            $host = Util::getAssetsUrlBase($uri);
            echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . $host . $uri . "\">\n";
        }
    }
}
