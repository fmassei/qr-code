<?php
declare(strict_types=1);

namespace fmassei\QrCode\Writer\Result;

use DOMDocument;

class SvgResult extends AbstractResult implements IResult
{
    public DOMDocument $xml;

    public function __construct(DOMDocument $image)
    {
        $this->xml = $image;
    }

    public function getMimeType(): string
    {
        return 'image/svg+xml';
    }

    public function getString(): string
    {
        return $this->xml->saveXML();
    }

}
