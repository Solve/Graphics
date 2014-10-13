<?php
/*
 * This file is a part of Solve framework.
 *
 * @author Alexandr Viniychuk <alexandr.viniychuk@icloud.com>
 * @copyright 2009-2014, Alexandr Viniychuk
 * created: 12.10.14 12:48
 */
namespace Solve\Graphics\Tests;

require_once __DIR__ . '/../ImageProcessor.php';
require_once __DIR__ . '/../Adapters/AbstractImageAdapter.php';
require_once __DIR__ . '/../Adapters/IMagickImageAdapter.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Solve\Graphics\ImageProcessor;
use Solve\Utils\FSService;

class ImageProcessorTest extends \PHPUnit_Framework_TestCase {

    public function testBasic() {
        $assetsRoot = __DIR__ . '/assets/';
        $flower = $assetsRoot . 'flower.jpg';
        $im = new ImageProcessor($flower);
        $im->fitOut(100, 100, ImageProcessor::GRAVITY_EAST);
        $im->saveAs($assetsRoot . 'flower-100x100.jpg');

        $info = ImageProcessor::getImageInfo($assetsRoot . 'flower-100x100.jpg');
        $this->assertEquals(100, $info['width'], 'width of fitOut is ok');
        $this->assertEquals(100, $info['height'], 'height of fitOut is ok');

        $im->fitIn(100, 100);
        $im->saveAs($assetsRoot . 'flower-100x62.jpg');
    }


}
 