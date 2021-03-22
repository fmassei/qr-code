<?php

declare(strict_types=1);

namespace fmassei\QrCode\Writer;

use fmassei\QrCode\QrCode;
use fmassei\QrCode\Writer\Result\PngResult;

class PngWriter implements IWriter
{
    public function write(QrCode $qrCode, array $options = []): PngResult
    {
        $svgWriter = new SvgWriter();
        $svg = ($svgWriter->write($qrCode, $options))->getString();
        $im = new \Imagick();
        $im->readImageBlob($svg);
        $im->setImageFormat("png24");
        return new PngResult($im);
    }
}
