<?php

declare(strict_types=1);

namespace fmassei\QrCode\Writer;

use fmassei\QrCode\QrCode;
use fmassei\QrCode\Writer\Result\DebugResult;

class DebugWriter implements IWriter
{
    public function write(QrCode $qrCode, array $options = []): DebugResult
    {
        return new DebugResult($qrCode, $options);
    }
}
