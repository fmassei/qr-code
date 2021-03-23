<?php
declare(strict_types=1);

namespace fmassei\QrCode;

use Exception;

class Logo
{
    public string $imageData;
    public string $mimeType;
    public string|null $backgroundColor = null;
    public float $percentSize = 0.2;

    public function __construct(string $imageData, string $mimeType)
    {
        $this->imageData = $imageData;
        $this->mimeType = $mimeType;
    }
    public static function fromPath(string $path) : self {
        if (($data = @file_get_contents($path))===false)
            throw new Exception("File not found.");
        return new self($data, Utils::getMimeType($path));
    }

    public function getImageDataUri(): string
    {
        return 'data:'.$this->mimeType.';base64,'.base64_encode($this->imageData);
    }
}
