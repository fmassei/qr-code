<?php
declare(strict_types=1);

namespace fmassei\QrCode\Writer\Result;

use Imagick;

class PngResult extends AbstractResult implements IResult
{
    public Imagick $image;

    public function __construct(Imagick $image)
    {
        $this->image = $image;
    }

    public function getMimeType(): string
    {
        return 'image/png';
    }

    public function getString(): string
    {
        ob_start();
        echo $this->image;
        return strval(ob_get_clean());
    }
}
