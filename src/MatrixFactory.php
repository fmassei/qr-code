<?php
declare(strict_types=1);

namespace fmassei\QrCode;

use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Common\ErrorCorrectionLevel;

class MatrixFactory
{
    private function __constructor() {}

    protected static function getBaconCorrectionLevel(int $ourCorrectionLevel) : ErrorCorrectionLevel {
        switch ($ourCorrectionLevel) {
            case QrCode::ERROR_CORRECTION_LEVEL_HIGH:
                return ErrorCorrectionLevel::valueOf("H");
            case QrCode::ERROR_CORRECTION_LEVEL_QUARTILE:
                return ErrorCorrectionLevel::valueOf("Q");
            case QrCode::ERROR_CORRECTION_LEVEL_MEDIUM:
                return ErrorCorrectionLevel::valueOf("M");
            case QrCode::ERROR_CORRECTION_LEVEL_LOW:
            default:
                return ErrorCorrectionLevel::valueOf("L");
        }
    }

    public static function create(QrCode $qrCode) : Matrix
    {
        $baconErrorCorrectionLevel = self::getBaconCorrectionLevel($qrCode->errorCorrectionLevel);
        $baconMatrix = Encoder::encode($qrCode->data, $baconErrorCorrectionLevel, strval($qrCode->encoding))->getMatrix();

        $blockValues = [];
        $columnCount = $baconMatrix->getWidth();
        $rowCount = $baconMatrix->getHeight();
        for ($rowIndex = 0; $rowIndex < $rowCount; ++$rowIndex) {
            $blockValues[$rowIndex] = [];
            for ($columnIndex = 0; $columnIndex < $columnCount; ++$columnIndex) {
                $blockValues[$rowIndex][$columnIndex] = $baconMatrix->get($columnIndex, $rowIndex);
            }
        }

        return new Matrix($blockValues, $qrCode->size, $qrCode->margin, $qrCode->roundBlockSizeMode);
    }
}
