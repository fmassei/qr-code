<?php
declare(strict_types=1);

namespace fmassei\QrCode\Writer;

use DOMDocument;
use DOMElement;
use fmassei\QrCode\Label;
use fmassei\QrCode\Matrix;
use fmassei\QrCode\QrCode;
use fmassei\QrCode\Writer\Result\SvgResult;

final class SvgWriter implements IWriter
{
    public const DEF_BLOCK_ID = 'block_id';
    public const BACKGROUND_ID = 'bg';

    public function write(QrCode $qrCode, array $options = []): SvgResult
    {
        $matrix = $qrCode->getMatrix();

        $dom = new DOMDocument();
        $matrixNode = $this->createMatrixNode($qrCode, $matrix, $dom);

        if ($qrCode->frame!==null) {
            $dom = $qrCode->frame->getWithReplacedNodes($matrixNode, $qrCode);
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
                    $block = $dom->createElement('rect');
                    $block->setAttribute('x', strval($matrix->marginLeft + $matrix->blockSize*$col));
                    $block->setAttribute('y', strval($matrix->marginLeft + $matrix->blockSize*$row));
                    $block->setAttribute('width', strval($matrix->blockSize));
                    $block->setAttribute('height', strval($matrix->blockSize));
                    $block->setAttribute('fill', $qrCode->foregroundColor);
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

        $gDef = $dom->createElement('g');

        if ($qrCode->logo->backgroundColor!==null) {
            $bgDef = $dom->createElement('rect');
            $bgDef->setAttribute('x', strval($x));
            $bgDef->setAttribute('y', strval($y));
            $bgDef->setAttribute('width', strval($logoSize));
            $bgDef->setAttribute('height', strval($logoSize));
            $bgDef->setAttribute('fill', $qrCode->logo->backgroundColor);
            $gDef->appendChild($bgDef);
        }

        $imageDef = $dom->createElement('image');
        $imageDef->setAttribute('x', strval($x));
        $imageDef->setAttribute('y', strval($y));
        $imageDef->setAttribute('width', strval($logoSize));
        $imageDef->setAttribute('height', strval($logoSize));
        $imageDef->setAttribute('preserveAspectRatio', 'none');
        $imageDef->setAttribute('href', $logo->getImageDataUri());
        $gDef->appendChild($imageDef);

        return $gDef;
    }

    private function createLabelNode(QrCode $qrCode, Matrix $matrix, DOMDocument $dom) : DOMElement
    {
        $label = $qrCode->label;

        $width = $matrix->outerSize;
        $origHeight = $matrix->outerSize;
        $newHeight = intval($origHeight
            +   $label->fontSizePx*1.3
            +   $label->margin[0] + $label->margin[2]);

        $dom->documentElement->setAttribute('height', $newHeight."px");
        $dom->documentElement->setAttribute('viewBox', "0 0 $width $newHeight");

        $y = $origHeight + $label->fontSizePx+$label->margin[0];
        switch ($label->alignment) {
            case Label::LABEL_TEXT_ALIGN_LEFT:
                $x = $label->margin[3];
                $anchor = 'start';
                break;
            case Label::LABEL_TEXT_ALIGNM_RIGHT:
                $x = $width-$label->margin[1];
                $anchor = 'end';
                break;
            case Label::LABEL_TEXT_ALIGN_CENTER:
            default:
                $x = $width/2 + $label->margin[3] - $label->margin[1];
                $anchor = 'middle';
        }
        $gNode = $dom->createElement('g');

        $textBg = $dom->createElement('rect');
        $textBg->setAttribute('x', '0');
        $textBg->setAttribute('y', strval($origHeight));
        $textBg->setAttribute('width', strval($width));
        $textBg->setAttribute('height', strval($newHeight-$origHeight));
        $textBg->setAttribute('fill', $label->backgroundColor);
        $gNode->appendChild($textBg);

        $textDef = $dom->createElement('text', $label->text);
        $textDef->setAttribute('x', strval($x));
        $textDef->setAttribute('y', strval($y));
        $textDef->setAttribute('text-anchor', $anchor);
        $textDef->setAttribute('font-family', $label->fontFamily);
        $textDef->setAttribute('font-size', $label->fontSizePx."px");
        $textDef->setAttribute('fill', $label->textColor);
        $gNode->appendChild($textDef);

        return $gNode;
    }
}