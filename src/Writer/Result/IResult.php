<?php
declare(strict_types=1);

namespace fmassei\QrCode\Writer\Result;

interface IResult
{
    public function getMimeType(): string;
    public function getString(): string;
    public function getDataUri(): string;
}
