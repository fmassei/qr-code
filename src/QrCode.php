<?php

declare(strict_types=1);

namespace fmassei\QrCode;

class QrCode
{
    public const ERROR_CORRECTION_LEVEL_LOW = 1;
    public const ERROR_CORRECTION_LEVEL_MEDIUM = 2;
    public const ERROR_CORRECTION_LEVEL_HIGH = 3;
    public const ERROR_CORRECTION_LEVEL_QUARTILE = 4;

    public const ROUND_BLOCK_SIZE_MODE_NONE = 1;
    public const ROUND_BLOCK_SIZE_MODE_SHRINK = 2;
    public const ROUND_BLOCK_SIZE_MODE_MARGIN = 3;
    public const ROUND_BLOCK_SIZE_MODE_ENLARGE = 4;

    public string $data;
    public string $encoding = "UTF-8";
    public int $errorCorrectionLevel = self::ERROR_CORRECTION_LEVEL_LOW;
    public int $size = 300;
    public int $margin = 10;
    public int $roundBlockSizeMode = self::ROUND_BLOCK_SIZE_MODE_MARGIN;
    public string $foregroundColor = "#000000";
    public string $backgroundColor = "#ffffff";
    public Logo|null $logo = null;
    public Label|null $label = null;
    public SVGFrame|null $frame = null;

    public function __construct(string $data) {
        $this->data = $data;
    }
}
