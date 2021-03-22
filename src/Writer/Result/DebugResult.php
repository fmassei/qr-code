<?php

declare(strict_types=1);

namespace fmassei\QrCode\Writer\Result;

use fmassei\QrCode\QrCode;

class DebugResult extends AbstractResult implements IResult
{
    private QrCode $qrCode;
    private array $options;

    /**
     * @param QrCode $qrCode
     * @param array<mixed> $options
     */
    public function __construct(QrCode $qrCode, array $options = [])
    {
        $this->qrCode = $qrCode;
        $this->options = $options;
    }

    public function getMimeType(): string
    {
        return 'text/plain';
    }

    public function getString(): string
    {
        $debugLines = [];

        $debugLines[] = 'Data: '.$this->qrCode->data;
        $debugLines[] = 'Encoding: '.$this->qrCode->encoding;
        $debugLines[] = 'Error Correction Level: '.$this->qrCode->errorCorrectionLevel;
        $debugLines[] = 'Size: '.$this->qrCode->size;
        $debugLines[] = 'Margin: '.$this->qrCode->margin;
        $debugLines[] = 'Round block size mode: '.$this->qrCode->roundBlockSizeMode;
        $debugLines[] = 'Foreground color: ['.$this->qrCode->foregroundColor.']';
        $debugLines[] = 'Background color: ['.$this->qrCode->backgroundColor.']';

        foreach ($this->options as $key => $value) {
            $debugLines[] = 'Writer option: '.$key.': '.$value;
        }

        if ($this->qrCode->logo!==null) {
            $debugLines[] = 'Logo data: '.base64_encode($this->qrCode->logo->imageData);
            $debugLines[] = 'Logo mime type: '.$this->qrCode->logo->mimeType;
            $debugLines[] = 'Logo size percent: '.$this->qrCode->logo->percentSize;
        }

        if ($this->qrCode->label!==null) {
            $debugLines[] = 'Label text: '.$this->qrCode->label->text;
            $debugLines[] = 'Label font family: '.$this->qrCode->label->fontFamily;
            $debugLines[] = 'Label font size: '.$this->qrCode->label->fontSizePx;
            $debugLines[] = 'Label alignment: '.$this->qrCode->label->alignment;
            $debugLines[] = 'Label margin: ['.implode(', ', $this->qrCode->label->margin).']';
            $debugLines[] = 'Label text color: ['.$this->qrCode->label->textColor.']';
            $debugLines[] = 'Label background color: ['.$this->qrCode->label->backgroundColor.']';
        }

        return implode("\n", $debugLines);
    }
}
