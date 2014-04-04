<?php
    // create 6x6 matrix canvas
    $im = @imagecreatetruecolor(300, 300) or die('Cannot Initialize new GD image stream');
 
    // set color pallete
    $white = imagecolorallocate($im, 255, 255, 255);
    $black = imagecolorallocate($im, 0, 0, 0);
    $red = imagecolorallocate($im, 255, 0, 0);
    $gray = imagecolorallocate($im, 128, 128, 128);
    $text_color = imagecolorallocate($im, 233, 14, 91);
 
    // font path
    $font = './Arial-Bold.ttf'; // Arial bold font
 
    // dots' path
    $yellow_dot = imagecreatefrompng("./yellow_dot.png");
    $blue_dot = imagecreatefrompng("./blue_dot.png");
    
    // set white background
    imagefill($im, 0, 0, $white);
 
    /* draw matrix box x1, y1, x2, y2*/
 
    // external borders
    imageline($im, 0,0,299,0, $black);
    imageline($im, 0,0,0,299, $black);
    imageline($im, 299,0,299,299, $black);
    imageline($im, 0,299,299,299, $black);
 
    // inner lines
    for ($i = 0; $i < 300; $i = $i + 50)
    {
        imageline($im, $i,0,$i,299, $black); // vertical
        imageline($im, 0,$i,299,$i, $black); // horizontal
    }
 
    /* draw numbers into the boxes */
 
    // define numbers array
    $numbers = range(1, 36);
 
    // set $coordinates
    $coordinates = array();
 
    /* set counter */
    $counter = 0;
 
    // if $_request[s] is a valid roulette number
    if(is_numeric($_REQUEST["s"]) && $_REQUEST["s"] >= 0 && $_REQUEST["s"] <= 36)
    {
        // browse until the number is found
        for($i = 0; ; $i++)
        {
            if($numbers[$i] == $_REQUEST["s"])
            {
                // set counter with index
                $counter = $i;
 
                // exit for
                break;
            }
        }
    }
 
    // inner lines
    for ($i = 25; $i < 300; $i = $i + 50)
    {
        for ($ii = 25; $ii < 300; $ii = $ii + 50)
        {
            // write text
            global $counter;
 
            // text to draw
            $text = $numbers[$counter];
 
            // Add some shadow
            imagettftextalign($im, 18, 0, $ii, $i + 2, $gray, $font, $text);
 
            // write the text
            imagettftextalign($im, 18, 0, $ii, $i, isRed($numbers[$counter]) ? $red : $black, $font, $text);
 
            // set number positions
            $coordinates[$numbers[$counter]] = array($ii, $i);
 
            // rise counter
            $counter++;
        }
    }
 
    /* highlight  numbers */
 
    // explode numbers in request
    if (strlen($_REQUEST["n"]) > 0)
    {
        // Numbers
        $numbers = array_count_values(preg_split('/[^0-9]/i', $_REQUEST["n"]));
        
        // process each of them
        foreach ($numbers as $k => $v)
        {
         // Single or double+
         if($v > 1)         
         {
            // Highlight Blue
            imagecopymerge_alpha($im, $blue_dot, $coordinates[$k][0] - 16, $coordinates[$k][1] - 16, 0, 0, 32, 32, 100);
         }
         else
         {
            // Highlight Yellow
            imagecopymerge_alpha($im, $yellow_dot, $coordinates[$k][0] - 16, $coordinates[$k][1] - 16, 0, 0, 32, 32, 100);
         }
        }
    }
 
    // draw footer
    imagestring($im, 1, 107, 290,  'BetSelection.cc', $text_color);
 
    // output image to browser
    header ('Content-Type: image/png');
    imagepng($im);
    imagedestroy($im);
?>
 
<?
    /**
    * Functions
    */
 
    function isRed($number)
    {
        // return color for input number
        switch ($number)
        {
            case 1:
            case 3:
            case 5:
            case 7:
            case 9:
            case 12:
            case 14:
            case 16:
            case 18:
            case 19:
            case 21:
            case 23:
            case 25:
            case 27:
            case 30:
            case 32:
            case 34:
            case 36:
                // red numbers
                return true;
                break;
                // default = black
            default:
                return false;
        }
    }
    /**
    * aligned ttf text
    *
    * @param mixed $image
    * @param mixed $size
    * @param mixed $angle
    * @param mixed $x
    * @param mixed $y
    * @param mixed $color
    * @param mixed $font
    * @param mixed $text
    */
    function imagettftextalign($image, $size, $angle, $x, $y, $color, $font, $text)
    {
 
        //check width of the text
        $box = imagettfbbox($size, $angle, $font, $text);
        $width = abs($box[4] - $box[0]);
        $height = abs($box[5] - $box[3]);
 
        $x -= $width / 2;
        $y += ($heigth / 2) + 7;
 
        //write text
        imagettftext ($image, $size, $angle, $x, $y, $color, $font, $text);
    }
 
    /**
    * alpha blended copymerge
    *
    * @param mixed $dst_im
    * @param mixed $src_im
    * @param mixed $dst_x
    * @param mixed $dst_y
    * @param mixed $src_x
    * @param mixed $src_y
    * @param mixed $src_w
    * @param mixed $src_h
    * @param mixed $pct
    * @param mixed $trans
    */
    function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct, $trans = NULL)
    {
        $dst_w = imagesx($dst_im);
        $dst_h = imagesy($dst_im);
 
        // bounds checking
        $src_x = max($src_x, 0);
        $src_y = max($src_y, 0);
        $dst_x = max($dst_x, 0);
        $dst_y = max($dst_y, 0);
        if ($dst_x + $src_w > $dst_w)
            $src_w = $dst_w - $dst_x;
        if ($dst_y + $src_h > $dst_h)
            $src_h = $dst_h - $dst_y;
 
        for($x_offset = 0; $x_offset < $src_w; $x_offset++)
            for($y_offset = 0; $y_offset < $src_h; $y_offset++)
            {
                // get source & dest color
                $srccolor = imagecolorsforindex($src_im, imagecolorat($src_im, $src_x + $x_offset, $src_y + $y_offset));
                $dstcolor = imagecolorsforindex($dst_im, imagecolorat($dst_im, $dst_x + $x_offset, $dst_y + $y_offset));
 
                // apply transparency
                if (is_null($trans) || ($srccolor !== $trans))
                {
                    $src_a = $srccolor['alpha'] * $pct / 100;
                    // blend
                    $src_a = 127 - $src_a;
                    $dst_a = 127 - $dstcolor['alpha'];
                    $dst_r = ($srccolor['red'] * $src_a + $dstcolor['red'] * $dst_a * (127 - $src_a) / 127) / 127;
                    $dst_g = ($srccolor['green'] * $src_a + $dstcolor['green'] * $dst_a * (127 - $src_a) / 127) / 127;
                    $dst_b = ($srccolor['blue'] * $src_a + $dstcolor['blue'] * $dst_a * (127 - $src_a) / 127) / 127;
                    $dst_a = 127 - ($src_a + $dst_a * (127 - $src_a) / 127);
                    $color = imagecolorallocatealpha($dst_im, $dst_r, $dst_g, $dst_b, $dst_a);
                    // paint
                    if (!imagesetpixel($dst_im, $dst_x + $x_offset, $dst_y + $y_offset, $color))
                        return false;
                    imagecolordeallocate($dst_im, $color);
                }
        }
        return true;
    }
?>
