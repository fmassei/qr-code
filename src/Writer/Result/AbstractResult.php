<?php
declare(strict_types=1);

namespace fmassei\QrCode\Writer\Result;

abstract class AbstractResult implements IResult
{
    public function getDataUri(): string
    {
        return 'data:'.$this->getMimeType().';base64,'.base64_encode($this->getString());
    }
}
