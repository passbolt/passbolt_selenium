<?php
/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SARL (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Passbolt SARL (https://www.passbolt.com)
 * @license   https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link      https://www.passbolt.com Passbolt(tm)
 * @since     2.0.0
 */
namespace App\Assertions;

use App\Lib\Image;
use PHPUnit_Framework_Assert;

trait ImageAssertionsTrait
{
    /**
     * Assert that 2 images are same.
     * To compare images, it uses the ImageCompare library.
     * this library compare the colors of the 2 resized images, and see if the
     * average color is the same.
     * The method is described here :
     * http://www.hackerfactor.com/blog/index.php?/archives/432-Looks-Like-It.html
     *
     * @param $image1Path
     * @param $image2Path
     * @param float      $tolerance
     */
    public function assertImagesAreSame($image1Path, $image2Path, $tolerance = 0.05) 
    {
        $image1 = Image::fromFile($image1Path);
        $image2 = Image::fromFile($image2Path);
        $diff = $image1->difference($image2);
        $scoreMin = 1 - $tolerance;
        PHPUnit_Framework_Assert::assertTrue($diff >= $scoreMin);
    }
}