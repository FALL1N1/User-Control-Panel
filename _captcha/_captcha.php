<?php


    session_start();
    $CaptchaIMG         = new Securimage();
    $CaptchaIMG->doImage();
    $_SESSION['capcha'] = $CaptchaIMG->_returnCapchaCode();

class Securimage {
    var $basepath;
    var $image_width;
    var $image_height;
    var $image_type  = 2; /* SI_IMAGE_JPEG -1, SI_IMAGE_PNG - 2, SI_IMAGE_GIF - 3 */
    var $code_length = 5;
    var $charset     = 'ABCDEFGHKLMNPRSTUVWYZabcdefghklmnprstuvwyz23456789';

    var $gd_font_size;
    var $ttf_file;
    var $perturbation;
    var $text_angle_minimum;
    var $text_angle_maximum;
    var $text_x_start;
    var $image_bg_color;
    var $text_color;
    var $multi_text_color;
    var $text_transparency_percentage;
    var $num_lines              = 10;
    var $line_color;
    var $draw_lines_over_text   = true;
    var $signature_color;
    var $tmpimg;
    var $iscale;
    var $bgimg;
    var $code;
    var $gdlinecolor;
    var $gdmulticolor;
    var $gdtextcolor;
    var $gdsignaturecolor;
    var $gdbgcolor;

    function Securimage()
    {
        $this->basepath     = dirname(__FILE__);
        $this->image_width  = 230;
        $this->image_height = 80;
        $this->gd_font_size = 24;
        $this->text_x_start = 15;

        $this->ttf_file      = $this->basepath . '/AHGBold.ttf';

        $this->perturbation       = 0.75;
        $this->iscale             = 5;
        $this->text_angle_minimum = 0;
        $this->text_angle_maximum = 0;

        $this->image_bg_color   = new Securimage_Color(0xff, 0xff, 0xff);
        $this->text_color       = new Securimage_Color(0x3d, 0x3d, 0x3d);
        $this->multi_text_color = array(new Securimage_Color(0x0, 0x20, 0xCC),
                                        new Securimage_Color(0x0, 0x30, 0xEE),
                                        new Securimage_color(0x0, 0x40, 0xCC),
                                        new Securimage_Color(0x0, 0x50, 0xEE),
                                        new Securimage_Color(0x0, 0x60, 0xCC));

        $this->text_transparency_percentage = 30;

        $this->line_color       = new Securimage_Color(0x3d, 0x3d, 0x3d);
        $this->signature_color  = new Securimage_Color(0x20, 0x50, 0xCC);
        $this->signature_font   = $this->basepath . '/AHGBold.ttf';
    }

    function doImage()
    {
        $this->im     = imagecreate($this->image_width, $this->image_height);
        $this->tmpimg = imagecreate($this->image_width * $this->iscale, $this->image_height * $this->iscale);

        $this->allocateColors();
        imagepalettecopy($this->tmpimg, $this->im);

        $this->setBackground();
        $this->generateCode();
        $this->drawWord();
        $this->distortedCopy();

        if($this->draw_lines_over_text && $this->num_lines > 0) $this->drawLines();

        $this->output();
    }

    function allocateColors()
    {
        $this->gdbgcolor = imagecolorallocate($this->im, $this->image_bg_color->r, $this->image_bg_color->g, $this->image_bg_color->b);
        $this->gdtextcolor = imagecolorallocate($this->im, $this->text_color->r, $this->text_color->g, $this->text_color->b);
        $this->gdlinecolor = imagecolorallocate($this->im, $this->line_color->r, $this->line_color->g, $this->line_color->b);
        $this->gdsignaturecolor = imagecolorallocate($this->im, $this->signature_color->r, $this->signature_color->g, $this->signature_color->b);
    }

    function setBackground()
    {
        imagefilledrectangle($this->im, 0, 0, $this->image_width * $this->iscale, $this->image_height * $this->iscale, $this->gdbgcolor);
        imagefilledrectangle($this->tmpimg, 0, 0, $this->image_width * $this->iscale, $this->image_height * $this->iscale, $this->gdbgcolor);

        $dat = @getimagesize($this->bgimg);
        if($dat == false) {
            return;
        }

        switch($dat[2]) {
            case 1:  $newim = @imagecreatefromgif($this->bgimg); break;
            case 2:  $newim = @imagecreatefromjpeg($this->bgimg); break;
            case 3:  $newim = @imagecreatefrompng($this->bgimg); break;
            case 15: $newim = @imagecreatefromwbmp($this->bgimg); break;
            case 16: $newim = @imagecreatefromxbm($this->bgimg); break;
            default: return;
        }

        if(!$newim) return;

        imagecopyresized($this->im, $newim, 0, 0, 0, 0, $this->image_width, $this->image_height, imagesx($newim), imagesy($newim));
    }

    function drawLines()
    {
        for ($line = 0; $line < $this->num_lines; ++$line) {
            $x = $this->image_width * (1 + $line) / ($this->num_lines + 1);
            $x += (0.5 - $this->frand()) * $this->image_width / $this->num_lines;
            $y = rand($this->image_height * 0.1, $this->image_height * 0.9);

            $theta = ($this->frand()-0.5) * M_PI * 0.7;
            $w = $this->image_width;
            $len = rand($w * 0.4, $w * 0.7);
            $lwid = rand(0, 2);

            $k = $this->frand() * 0.6 + 0.2;
            $k = $k * $k * 0.5;
            $phi = $this->frand() * 6.28;
            $step = 0.5;
            $dx = $step * cos($theta);
            $dy = $step * sin($theta);
            $n = $len / $step;
            $amp = 1.5 * $this->frand() / ($k + 5.0 / $len);
            $x0 = $x - 0.5 * $len * cos($theta);
            $y0 = $y - 0.5 * $len * sin($theta);

            for ($i = 0; $i < $n; ++$i) {
                $x = $x0 + $i * $dx + $amp * $dy * sin($k * $i * $step + $phi);
                $y = $y0 + $i * $dy - $amp * $dx * sin($k * $i * $step + $phi);
                imagefilledrectangle($this->im, $x, $y, $x + $lwid, $y + $lwid, $this->gdlinecolor);
            }
        }
    }

    function drawWord()
    {
        $width2 = $this->image_width * $this->iscale;
        $height2 = $this->image_height * $this->iscale;

        $font_size = $height2 * .35;
        $bb = imagettfbbox($font_size, 0, $this->ttf_file, $this->code);
        $tx = $bb[4] - $bb[0];
        $ty = $bb[5] - $bb[1];
        $x  = floor($width2 / 2 - $tx / 2 - $bb[0]);
        $y  = round($height2 / 2 - $ty / 2 - $bb[1]);

        $strlen = strlen($this->code);

        if($this->text_angle_minimum == 0 && $this->text_angle_maximum == 0) { // no angled or multi-color characters
            imagettftext($this->tmpimg, $font_size, 0, $x, $y, $this->gdtextcolor, $this->ttf_file, $this->code);
        } else {
            for($i = 0; $i < $strlen; ++$i) {
                $angle = rand($this->text_angle_minimum, $this->text_angle_maximum);
                $y = rand($y - 5, $y + 5);

                $font_color = $this->gdtextcolor;

                $ch = $this->code{$i};

                imagettftext($this->tmpimg, $font_size, $angle, $x, $y, $font_color, $this->ttf_file, $ch);

                if(strpos('abcdeghknopqsuvxyz', $ch) !== false) {
                    $min_x = $font_size - ($this->iscale * 6);
                    $max_x = $font_size - ($this->iscale * 6);
                } else if(strpos('ilI1', $ch) !== false) {
                    $min_x = $font_size / 5;
                    $max_x = $font_size / 3;
                } else if(strpos('fjrt', $ch) !== false) {
                    $min_x = $font_size - ($this->iscale * 12);
                    $max_x = $font_size - ($this->iscale * 12);
                } else if($ch == 'wm') {
                    $min_x = $font_size;
                    $max_x = $font_size + ($this->iscale * 3);
                } else { // numbers, capitals or unicode
                    $min_x = $font_size + ($this->iscale * 2);
                    $max_x = $font_size + ($this->iscale * 5);
                }

                $x += rand($min_x, $max_x);
            }
        }
    }

    function distortedCopy()
    {
        $numpoles = 3; // distortion factor

        // make array of poles AKA attractor points
        for ($i = 0; $i < $numpoles; ++$i) {
            $px[$i]  = rand($this->image_width * 0.3, $this->image_width * 0.7);
            $py[$i]  = rand($this->image_height * 0.3, $this->image_height * 0.7);
            $rad[$i] = rand($this->image_width * 0.4, $this->image_width * 0.7);
            $tmp     = -$this->frand() * 0.15 - 0.15;
            $amp[$i] = $this->perturbation * $tmp;
        }

        $bgCol   = imagecolorat($this->tmpimg, 0, 0);
        $width2  = $this->iscale * $this->image_width;
        $height2 = $this->iscale * $this->image_height;

        imagepalettecopy($this->im, $this->tmpimg); // copy palette to final image so text colors come across

        // loop over $img pixels, take pixels from $tmpimg with distortion field
        for ($ix = 0; $ix < $this->image_width; ++$ix) {
            for ($iy = 0; $iy < $this->image_height; ++$iy) {
                $x = $ix;
                $y = $iy;

                for ($i = 0; $i < $numpoles; ++$i) {
                    $dx = $ix - $px[$i];
                    $dy = $iy - $py[$i];
                    if($dx == 0 && $dy == 0) continue;

                    $r = sqrt($dx * $dx + $dy * $dy);
                    if($r > $rad[$i]) continue;

                    $rscale = $amp[$i] * sin(3.14 * $r / $rad[$i]);
                    $x += $dx * $rscale;
                    $y += $dy * $rscale;
                }

                $c = $bgCol;
                $x *= $this->iscale;
                $y *= $this->iscale;

                if($x >= 0 && $x < $width2 && $y >= 0 && $y < $height2) {
                    $c = imagecolorat($this->tmpimg, $x, $y);
                }

                if($c != $bgCol) { // only copy pixels of letters to preserve any background image
                    imagesetpixel($this->im, $ix, $iy, $c);
                }
            }
        }
    }

    function _returnCapchaCode() { return strtolower($this->code); }

    function generateCode()
    {
        $code = '';
        for($i = 1, $cslen = strlen($this->charset); $i <= $this->code_length; ++$i) {
            $code .= $this->charset{rand(0, $cslen - 1)};
        }
        $this->code = $code;
    }

    function output()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        switch($this->image_type)
        {
            case 1:
                header("Content-Type: image/jpeg");
                imagejpeg($this->im, null, 90);
                break;
            case 3:
                header("Content-Type: image/gif");
                imagegif($this->im);
                break;
            default:
                header("Content-Type: image/png");
                imagepng($this->im);
                break;
        }

        imagedestroy($this->im);
    }

    function frand()
    {
        return 0.0001*rand(0,9999);
    }
}

class Securimage_Color {
    var $r;
    var $g;
    var $b;

    function Securimage_Color($red, $green = null, $blue = null)
    {
        if($green == null && $blue == null && preg_match('/^#[a-f0-9]{3,6}$/i', $red)) {
            $col = substr($red, 1);
            if(strlen($col) == 3) {
                $red   = str_repeat(substr($col, 0, 1), 2);
                $green = str_repeat(substr($col, 1, 1), 2);
                $blue  = str_repeat(substr($col, 2, 1), 2);
            } else {
                $red   = substr($col, 0, 2);
                $green = substr($col, 2, 2);
                $blue  = substr($col, 4, 2);
            }

            $red   = hexdec($red);
            $green = hexdec($green);
            $blue  = hexdec($blue);
        } else {
            if($red < 0) $red       = 0;
            if($red > 255) $red     = 255;
            if($green < 0) $green   = 0;
            if($green > 255) $green = 255;
            if($blue < 0) $blue     = 0;
            if($blue > 255) $blue   = 255;
        }

        $this->r = $red;
        $this->g = $green;
        $this->b = $blue;
    }
}