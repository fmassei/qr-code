<?php

declare(strict_types=1);

namespace fmassei\QrCode\Writer;

use fmassei\QrCode\Label;
use fmassei\QrCode\Logo;
use fmassei\QrCode\Writer\Result\IResult;
use fmassei\QrCode\QrCode;

interface IWriter
{
    public function write(QrCode $qrCode, array $options = []): IResult;
}
