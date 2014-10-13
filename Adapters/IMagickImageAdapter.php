<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 13.10.14 08:14
 */

namespace Solve\Graphics;


class IMagickImageAdapter extends AbstractImageAdapter {

    /**
     * @var \Imagick
     */
    protected $_image;

    public function process($operations) {
        $this->_image = new \Imagick($this->_sourcePath);
        parent::process($operations);
        return $this;
    }

    protected function buildCrop($params) {
        $this->_image->cropImage($params[0], $params[1], !empty($params[2]) ? $params[2] : 0, !empty($params[3]) ? $params[3] : 0);
    }

    protected function buildFitOut($params) {
        $newWidth = $params[0];
        $newHeight = $params[1];
        $originalWidth = $this->_image->getImageWidth();
        $originalHeight = $this->_image->getImageHeight();

        $this->_image->setGravity(!empty($params[2]) ? $params[2] : \Imagick::GRAVITY_CENTER);

        $tmpWidth = $newWidth;
        $tmpHeight = $originalHeight * ($newWidth / $originalWidth);

        if ($tmpHeight < $newHeight) {
            $tmpHeight = $newHeight;
            $tmpWidth = $originalWidth * ($newHeight / $originalHeight);
        }
        $this->_image->thumbnailImage($tmpWidth, $tmpHeight);
        $offset = self::detectGravityXY($this->_image->getGravity(),
            $tmpWidth, $tmpHeight,
            $params[0], $params[1]);
        $this->_image->cropImage($newWidth, $newHeight, $offset[0], $offset[1]);
    }

    protected function buildFitIn($params) {
        $this->_image->scaleImage($params[0], $params[1], 1);
    }

}