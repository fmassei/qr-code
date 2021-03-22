<?php

declare(strict_types=1);

namespace fmassei\QrCode\Writer;

use fmassei\QrCode\MatrixFactory;
use fmassei\QrCode\Writer\Result\BinaryResult;
use fmassei\QrCode\QrCode;

class BinaryWriter implements IWriter
{
    public function write(QrCode $qrCode, array $options = []): BinaryResult
    {
        return new BinaryResult(MatrixFactory::create($qrCode));
    }
}
