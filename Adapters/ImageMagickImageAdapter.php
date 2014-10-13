<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 13.10.14 08:14
 */

namespace Solve\Graphics;


class ImageMagicImageAdapter extends AbstractImageAdapter {

    private static $_binRoot       = '/usr/bin/';
    private        $_commandString = '';

    /**
     * @return string
     */
    public static function getBinRoot() {
        return self::$_binRoot;
    }

    /**
     * @param string $binRoot
     */
    public static function setBinRoot($binRoot) {
        self::$_binRoot = $binRoot;
    }

    private static function getConvertPath() {
        $path = self::$_binRoot . 'convert';
        if (!is_file($path)) {
            if (is_file('/usr/local/bin/convert')) {
                $path = '/usr/local/bin/convert';
            } else {
                throw new \Exception('Executable ImageMagick not found: ' . self::$_binRoot);
            }
        }

        return $path;
    }

    public function process($operations, $source, $destination = null) {
        $this->_commandString = '';
        parent::process($operations, $source, $destination);
    }

    private function buildCrop($params) {
        $dimension = $params[0] . '.' . $params[1];
        $gravity = $params[2];
        $this->_commandString .= ' -resize '.$dimension.'^ -gravity '.$gravity.' -crop '.$dimension.'+0+0 +repage ';
    }

} 