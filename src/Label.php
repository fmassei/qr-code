<?php

declare(strict_types=1);

namespace fmassei\QrCode;

final class Label
{
    public const LABEL_TEXT_ALIGN_LEFT = 1;
    public const LABEL_TEXT_ALIGN_CENTER = 2;
    public const LABEL_TEXT_ALIGNM_RIGHT = 3;

    public string $text;

    public string $fontFamily = "arial";
    public int $fontSizePx = 28;
    public int $alignment = self::LABEL_TEXT_ALIGN_CENTER;
    /** @var int[] $margin top, right, bottom, left */
    public array $margin = [0,10,10,10];    //TODO
    public string $textColor = "#000000";
    public string $backgroundColor = "#FFFFFF"; //TODO

    public function __construct(string $text) {
        $this->text = $text;
    }
}
