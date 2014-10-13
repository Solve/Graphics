<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 11.10.14 09:48
 */

namespace Solve\Graphics;

use Solve\Utils\FSService;

/**
 * Class ImageProcessor
 * @package Solve\Graphics
 *
 * Class ImageProcessor is a standalone image processing tool and a part of solve graphics component
 *
 * @method crop($width, $height, $x = 0, $y = 0) Crop the image
 * @method fitOut($width, $height, $gravity = ImageProcessor::GRAVITY_CENTER) Fit image on area
 * @method fitIn($width, $height) Fit image in area
 *
 * @version 1.0
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 */
class ImageProcessor {

    const GRAVITY_NORTHWEST = 1;
    const GRAVITY_NORTH     = 2;
    const GRAVITY_NORTHEAST = 3;
    const GRAVITY_WEST      = 4;
    const GRAVITY_CENTER    = 5;
    const GRAVITY_EAST      = 6;
    const GRAVITY_SOUTHWEST = 7;
    const GRAVITY_SOUTH     = 8;
    const GRAVITY_SOUTHEAST = 9;

    /**
     * @var AbstractImageAdapter
     */
    private static $_activeImageAdapter;
    private        $_sourcePath;
    private        $_operationsQueue = array();
    private static $_imageAdapters   = array();
    private        $_allowedMethods  = array(
        'crop', 'fitOut', 'fitIn'
    );

    public function __construct($imagePath = null) {
        if (!empty($imagePath)) $this->_sourcePath = $imagePath;

        if (empty(self::$_activeImageAdapter)) {
            self::setActiveImageAdapter('IMagick');
        }
    }

    public static function setActiveImageAdapter($adapterName) {
        $adapterClass = $adapterName;

        if (!empty(self::$_imageAdapters[$adapterName])) {
            self::$_activeImageAdapter = self::$_imageAdapters[$adapterName];
            return true;
        }

        if (strpos($adapterName, '\\') === false) {
            $adapterClass = '\\Solve\\Graphics\\' . $adapterName . 'ImageAdapter';
        }
        if (!class_exists($adapterClass)) throw new \Exception('Adapter class not found ' . $adapterClass);
        self::$_imageAdapters[$adapterName] = new $adapterClass();
        self::$_activeImageAdapter          = self::$_imageAdapters[$adapterName];
    }

    public static function getActiveImageAdapter() {
        return self::$_activeImageAdapter;
    }

    public function resetOperationsQueue() {
        $this->_operationsQueue = array();
    }

    public function process() {
        if (!empty($this->_operationsQueue)) {
            self::$_activeImageAdapter->setSourcePath($this->_sourcePath);
            self::$_activeImageAdapter->process($this->_operationsQueue);
            $this->resetOperationsQueue();
        }
    }

    public function saveAs($path) {
        $this->process();
        if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
            $dirPath = substr($path, 0, strrpos($path, DIRECTORY_SEPARATOR));
        }
        FSService::makeWritable($dirPath);
        file_put_contents($path, $this->getImageData());
    }

    public function getImageData() {
        return self::$_activeImageAdapter->getImageData();
    }

    public function __call($method, $params) {
        if (in_array($method, $this->_allowedMethods)) {
            $this->_operationsQueue[] = array(
                'method' => $method,
                'params' => $params
            );
        } else {
            throw new \Exception('Method ' . $method . ' is not allowed in this version of ImageProcessor');
        }
        return $this;
    }

    public static function getImageInfo($path) {
        if (!is_file($path)) return null;

        $image_info = array();
        list($width, $height, $type, $attr) = getimagesize($path, $image_info);

        $info           = FSService::getFileInfo($path);
        $info['width']  = $width;
        $info['height'] = $height;
        return $info;
    }

} 