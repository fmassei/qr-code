<?php

declare(strict_types=1);

namespace fmassei\QrCode\Writer;

use fmassei\QrCode\QrCode;
use fmassei\QrCode\Writer\Result\PngResult;
use Imagick;
use ImagickPixel;

class PngWriter implements IWriter
{
    /* currently there's a mysterious bug in imagemagick on imageReadBlob with
     * SVG files. We keep the workaround on by default */
    public const READ_BLOB_WORKAROUND = "read_blob_bug";
    public const READ_BLOB_WORKAROUND_DEFAULT = true;

    public function write(QrCode $qrCode, array $options = []): PngResult
    {
        if (!isset($options[self::READ_BLOB_WORKAROUND]))
            $options[self::READ_BLOB_WORKAROUND] = $options[self::READ_BLOB_WORKAROUND_DEFAULT];
        $svgWriter = new SvgWriter();
        $svg = ($svgWriter->write($qrCode, $options))->getString();
        $size = $qrCode->getMatrix()->outerSize;
        $im = new Imagick();
        $im->setBackgroundColor(new ImagickPixel('transparent'));
        $im->setResolution(2000,2000);
        if (!$options[self::READ_BLOB_WORKAROUND]) {
            $tmpName = tempnam(sys_get_temp_dir(), 'FOO');
            file_put_contents($tmpName, $svg);
            $im->readImage($tmpName);
            unlink($tmpName);
        } else {
            $im->readImageBlob($svg, sys_get_temp_dir());
        }
        $im->setImageFormat("png32");
        $ratio = $im->getImageHeight()/(float)$im->getImageWidth();
        $sizeH = (int)($ratio*$size);
        $im->resizeImage($size, $sizeH, Imagick::FILTER_LANCZOS,0);
        return new PngResult($im);
    }
}
