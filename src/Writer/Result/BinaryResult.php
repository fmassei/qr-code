<?php

declare(strict_types=1);

namespace fmassei\QrCode\Writer\Result;

use fmassei\QrCode\Matrix;

class BinaryResult extends AbstractResult implements IResult
{
    private Matrix $matrix;

    public function __construct(Matrix $matrix)
    {
        $this->matrix = $matrix;
    }

    public function getMimeType(): string { return 'text/plain'; }

    public function getString(): string
    {
        $binaryString = '';
        $size = $this->matrix->getBlockCount();
        for ($row=0; $row<$size; ++$row) {
            for ($col=0; $col<$size; ++$col) {
                $binaryString .= $this->matrix->blockValues[$row][$col];
            }
            $binaryString .= "\n";
        }
        return $binaryString;
    }
}
