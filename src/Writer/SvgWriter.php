<?php
declare(strict_types=1);

namespace fmassei\QrCode\Writer;

use DOMDocument;
use DOMElement;
use fmassei\QrCode\Label;
use fmassei\QrCode\Matrix;
use fmassei\QrCode\MatrixFactory;
use fmassei\QrCode\QrCode;
use fmassei\QrCode\Writer\Result\SvgResult;

final class SvgWriter implements IWriter
{
    public const DEF_BLOCK_ID = 'block_id';
    public const BACKGROUND_ID = 'bg';

    public function write(QrCode $qrCode, array $options = []): SvgResult
    {
        $matrix = MatrixFactory::create($qrCode);

        $dom = new DOMDocument();
        $matrixNode = $this->createMatrixNode($qrCode, $matrix, $dom);

        if ($qrCode->frame!==null) {
            $dom = $qrCode->frame->getWithReplacedNodes($matrixNode, $qrCode->label, $qrCode->logo);
        } else {
            $dom->appendChild($matrixNode);
            if ($qrCode->logo!=null)
                $matrixNode->appendChild(
                    $this->createLogoNode($qrCode, $matrix, $dom));
            if ($qrCode->label!=null)
                $matrixNode->appendChild(
                    $this->createLabelNode($qrCode, $matrix, $dom));
        }
        return new SvgResult($dom);
    }

    private function createMatrixNode(QrCode $qrCode, Matrix $matrix, DOMDocument $dom) : DOMElement {
        $root = $dom->createElement('svg');
        $root->setAttribute('xmlns', "http://www.w3.org/2000/svg");
        $root->setAttribute('version', '1.1');
        $root->setAttribute('width', $matrix->outerSize.'px');
        $root->setAttribute('height', $matrix->outerSize.'px');
        $root->setAttribute('viewBox', '0 0 '.$matrix->outerSize.' '.$matrix->outerSize);

        $defs = $dom->createElement('defs');
        $blockDef = $dom->createElement('rect');
        $blockDef->setAttribute('id', self::DEF_BLOCK_ID);
        $blockDef->setAttribute('width', strval($matrix->blockSize));
        $blockDef->setAttribute('height', strval($matrix->blockSize));
        $blockDef->setAttribute('fill', $qrCode->foregroundColor);
        $defs->appendChild($blockDef);
        $root->appendChild($defs);

        $background = $dom->createElement('rect');
        $background->setAttribute('id', self::BACKGROUND_ID);
        $background->setAttribute('x', '0');
        $background->setAttribute('y', '0');
        $background->setAttribute('width', strval($matrix->outerSize));
        $background->setAttribute('height', strval($matrix->outerSize));
        $background->setAttribute('fill', $qrCode->backgroundColor);
        $root->appendChild($background);

        $blockCount = $matrix->getBlockCount();
        for ($row=0; $row<$blockCount; ++$row) {
            for ($col=0; $col<$blockCount; ++$col) {
                if ($matrix->blockValues[$row][$col]===1) {
                    $block = $dom->createElement('use');
                    $block->setAttribute('x', strval($matrix->marginLeft + $matrix->blockSize*$col));
                    $block->setAttribute('y', strval($matrix->marginLeft + $matrix->blockSize*$row));
                    $block->setAttribute('href', '#'.self::DEF_BLOCK_ID);
                    $root->appendChild($block);
                }
            }
        }
        return $root;
    }

    private function createLogoNode(QrCode $qrCode, Matrix $matrix, DOMDocument $dom) : DOMElement
    {
        $logo = $qrCode->logo;

        $logoSize = intval($matrix->innerSize * $logo->percentSize);
        $x = intval($matrix->outerSize) / 2 - $logoSize / 2;
        $y = intval($matrix->outerSize) / 2 - $logoSize / 2;

        $imageDef = $dom->createElement('image');
        $imageDef->setAttribute('x', strval($x));
        $imageDef->setAttribute('y', strval($y));
        $imageDef->setAttribute('width', strval($logoSize));
        $imageDef->setAttribute('height', strval($logoSize));
        $imageDef->setAttribute('preserveAspectRatio', 'none');
        $imageDef->setAttribute('href', $logo->getImageDataUri());

        return $imageDef;
    }

    private function createLabelNode(QrCode $qrCode, Matrix $matrix, DOMDocument $dom) : DOMElement
    {
        $label = $qrCode->label;

        $width = $matrix->outerSize;
        $origHeight = $matrix->outerSize;
        $newHeight = intval($origHeight+$label->fontSizePx*1.3);

        $dom->documentElement->setAttribute('height', $newHeight."px");
        $dom->documentElement->setAttribute('viewBox', "0 0 $width $newHeight");

        $y = $origHeight + $label->fontSizePx;
        switch ($label->alignment) {
            case Label::LABEL_TEXT_ALIGN_LEFT:
                $x = 0;
                $anchor = 'start';
                break;
            case Label::LABEL_TEXT_ALIGNM_RIGHT:
                $x = $width;
                $anchor = 'end';
                break;
            case Label::LABEL_TEXT_ALIGN_CENTER:
            default:
                $x = $width/2;
                $anchor = 'middle';
        }

        $textDef = $dom->createElement('text', $label->text);
        $textDef->setAttribute('x', strval($x));
        $textDef->setAttribute('y', strval($y));
        $textDef->setAttribute('text-anchor', $anchor);
        $textDef->setAttribute('font-family', $label->fontFamily);
        $textDef->setAttribute('font-size', $label->fontSizePx."px");
        $textDef->setAttribute('fill', $label->textColor);

        return $textDef;
    }
}