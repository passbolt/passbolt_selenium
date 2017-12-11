<?php
/**
 * Image Comparing Function
 * (C) 2011-2014 Robert Lerner, All Rights Reserved
 */
namespace App\lib;

class Image
{

    /**
     * $image1                     STRING/RESOURCE          Filepath and name to PNG or passed image resource handle
     * $image2                    STRING/RESOURCE          Filepath and name to PNG or passed image resource handle
     * $RTolerance               INTEGER (0-/+255)     Red Integer Color Deviation before channel flag thrown
     * $GTolerance               INTEGER (0-/+255)     Green Integer Color Deviation before channel flag thrown
     * $BTolerance               INTEGER (0-/+255)     Blue Integer Color Deviation before channel flag thrown
     * $WarningTolerance     INTEGER (0-100)          Percentage of channel differences before warning returned
     * $ErrorTolerance          INTEGER (0-100)          Percentage of channel difference before error returned
     */
    public static function compare( $image1, $image2, $RTolerance = 0, $GTolerance = 0, $BTolerance = 0, $WarningTolerance = 1, $ErrorTolerance = 5 ) 
    {
        if (is_resource($image1) ) {
            $im = $image1;
        } else if (! $im = imagecreatefrompng($image1) ) {
            trigger_error("Image 1 could not be opened", E_USER_ERROR);
        }

        if (is_resource($image2) ) {
            $im2 = $image2;
        } else if (! $im2 = imagecreatefrompng($image2) ) {
            trigger_error("Image 2 could not be opened", E_USER_ERROR);
        }


        $OutOfSpec = 0;

        if (imagesx($im) != imagesx($im2) ) {
            die("Width does not match.");
        }
        if (imagesy($im) != imagesy($im2) ) {
            die("Height does not match.");
        }


        //By columns
        for ( $width = 0; $width <= imagesx($im) - 1; $width ++ ) {
            for ( $height = 0; $height <= imagesy($im) - 1; $height ++ ) {
                $rgb = imagecolorat($im, $width, $height);
                $r1  = ( $rgb >> 16 ) & 0xFF;
                $g1  = ( $rgb >> 8 ) & 0xFF;
                $b1  = $rgb & 0xFF;

                $rgb = imagecolorat($im2, $width, $height);
                $r2  = ( $rgb >> 16 ) & 0xFF;
                $g2  = ( $rgb >> 8 ) & 0xFF;
                $b2  = $rgb & 0xFF;

                if (! ( $r1 >= $r2 - $RTolerance
                    && $r1 <= $r2 + $RTolerance )
                ) {
                    $OutOfSpec ++;
                }

                if (! ( $g1 >= $g2 - $GTolerance
                    && $g1 <= $g2 + $GTolerance )
                ) {
                    $OutOfSpec ++;
                }

                if (! ( $b1 >= $b2 - $BTolerance
                    && $b1 <= $b2 + $BTolerance )
                ) {
                    $OutOfSpec ++;
                }


            }
        }
        $TotalPixelsWithColors = ( imagesx($im) * imagesy($im) ) * 3;

        $RET['PixelsByColors']  = $TotalPixelsWithColors;
        $RET['PixelsOutOfSpec'] = $OutOfSpec;

        if ($OutOfSpec != 0 && $TotalPixelsWithColors != 0 ) {
            $PercentOut
                                      =
            ( $OutOfSpec / $TotalPixelsWithColors ) * 100;
            $RET['PercentDifference'] = $PercentOut;
            if ($PercentOut >= $WarningTolerance
            ) //difference triggers WARNINGTOLERANCE%
            {
                $RET['WarningLevel'] = true;
            }
            if ($PercentOut >= $ErrorTolerance
            ) //difference triggers ERRORTOLERANCE%
            {
                $RET['ErrorLevel'] = true;
            }
        }

        return $RET;
    }
}