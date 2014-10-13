<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 11.10.14 10:18
 */

namespace Solve\Graphics;


abstract class AbstractImageAdapter {

    protected $_sourcePath;
    protected $_image;

    public function __construct($sourcePath = null) {
        $this->_sourcePath = $sourcePath;
    }

    /**
     * @return mixed
     */
    public function getSourcePath() {
        return $this->_sourcePath;
    }

    /**
     * @param mixed $sourcePath
     */
    public function setSourcePath($sourcePath) {
        $this->_sourcePath = $sourcePath;
    }

    public function getImageData() {
        return (string)$this->_image;
    }

    public function process($operations) {
        foreach ($operations as $info) {
            $methodName = 'build' . ucfirst($info['method']);
            if (method_exists($this, $methodName)) {
                $this->$methodName($info['params']);
            }

        }
        return $this;
    }

    public static function detectGravityXY($gravity, $originalWidth, $originalHeight, $newWidth, $newHeight) {
        $res = array(0, 0);
        switch ($gravity) {
            case ImageProcessor::GRAVITY_CENTER:
                $res[0] = ($originalWidth / 2) - ($newWidth / 2);
                $res[1] = $originalHeight / 2 - ($newHeight / 2);
                break;
            case ImageProcessor::GRAVITY_NORTHWEST:
                break;
            case ImageProcessor::GRAVITY_NORTH:
                $res[0] = ($originalWidth / 2) - ($newWidth / 2);
                break;
            case ImageProcessor::GRAVITY_NORTHEAST:
                $res[0] = ($originalWidth) - $newWidth;
                break;
            case 4: // 'west'
                $res[1] = ($originalHeight / 2) - ($newHeight / 2);
                break;
            case 6: // 'east'
                $res[0] = $originalWidth - $newWidth;
                $res[1] = ($originalHeight / 2) - ($newHeight / 2);
                break;
            case 7: // 'southwest'
                $res[1] = $originalHeight - $newHeight;
                break;
            case 8: // 'south'
                $res[0] = ($originalWidth / 2) - ($newWidth / 2);
                $res[1] = $originalHeight - $newHeight;
                break;
            case 9: // 'southeast'
                $res[0] = $originalWidth - $newWidth;
                $res[1] = $originalHeight - $newHeight;
                break;
        }
        return $res;
    }
}