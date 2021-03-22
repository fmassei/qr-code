<?php

declare(strict_types=1);

namespace fmassei\QrCode;

class Matrix
{
    /** @var array<int, array<int, int>> */
    public array $blockValues = [];
    public float $blockSize;
    public int $innerSize;
    public int $outerSize;
    public int $marginLeft;
    public int $marginRight;

    /**
     * @param int[][] $blockValues
     * @param int $size
     * @param int $margin
     * @param int $roundBlockSizeMode one of the QrCode::ROUND_BLOCK_SIZE_MODE_ constants
     */
    public function __construct(array $blockValues, int $size, int $margin, int $roundBlockSizeMode)
    {
        $this->blockValues = $blockValues;
        $blockCount = $this->getBlockCount();
        $this->blockSize = $size / $blockCount;
        $this->innerSize = $size;
        $this->outerSize = $size + 2 * $margin;

        switch ($roundBlockSizeMode) {
            case QrCode::ROUND_BLOCK_SIZE_MODE_ENLARGE:
                $this->blockSize = intval(ceil($this->blockSize));
                $this->innerSize = intval($this->blockSize * $blockCount);
                $this->outerSize = intval($this->innerSize + 2 * $margin);
                break;
            case QrCode::ROUND_BLOCK_SIZE_MODE_SHRINK:
                $this->blockSize = intval(floor($this->blockSize));
                $this->innerSize = intval($this->blockSize * $blockCount);
                $this->outerSize = intval($this->innerSize + 2 * $margin);
                break;
            case QrCode::ROUND_BLOCK_SIZE_MODE_MARGIN:
                $this->blockSize = intval(floor($this->blockSize));
                $this->innerSize = intval($this->blockSize * $blockCount);
                break;
            case QrCode::ROUND_BLOCK_SIZE_MODE_NONE:
            default:
                break;
        }

        $this->marginLeft = intval(($this->outerSize - $this->innerSize) / 2);
        $this->marginRight = $this->outerSize - $this->innerSize - $this->marginLeft;
    }

    public function getBlockCount(): int
    {
        return count($this->blockValues[0]);
    }
}
