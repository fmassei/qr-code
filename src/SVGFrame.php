<?php
declare(strict_types=1);

namespace fmassei\QrCode;

use DOMDocument;
use DOMElement;
use DOMXPath;

class SVGFrame
{
    public DOMDocument $dom;
    public string $IDQrCode = "QrCode";
    public string $IDLabel = "Label";
    public string $IDLogo = "Logo";

    public function __construct(DOMDocument $dom) {
        $this->dom = $dom;
    }

    public static function fromFile(string $path) : self {
        $dom = new DOMDocument();
        $dom->load($path);
        return new self($dom);
    }

    public function getWithReplacedNodes(DOMElement $qrCodeElement, Label|null $label, Logo|null $logo) : DOMDocument {
        $xpath = new DOMXPath($this->dom);
        if (($elements = $xpath->query("//*[@id]"))===null)
            return $this->dom;
        /** @var DOMElement $element */
        foreach($elements as $element) {
            switch ($element->getAttribute('id')) {
                case $this->IDQrCode:
                    $x = $element->getAttribute('x');
                    $y = $element->getAttribute('y');
                    $width = $element->getAttribute('width');
                    $height = $element->getAttribute('height');

                    $svgNode = $this->dom->importNode($qrCodeElement, true);
                    $svgNode->setAttribute('x', $x);
                    $svgNode->setAttribute('y', $y);
                    $svgNode->setAttribute('width', $width);
                    $svgNode->setAttribute('height', $height);
                    $gNode = $this->dom->createElement('g');
                    $gNode->appendChild($svgNode);

                    $element->parentNode->replaceChild($gNode, $element);
                    break;
                case $this->IDLogo:
                    $x = $element->getAttribute('x');
                    $y = $element->getAttribute('y');
                    $width = $element->getAttribute('width');
                    $height = $element->getAttribute('height');
                    $logoNode = $this->dom->createElement('image');

                    $logoNode->setAttribute('x', $x);
                    $logoNode->setAttribute('y', $y);
                    $logoNode->setAttribute('width', $width);
                    $logoNode->setAttribute('height', $height);
                    $logoNode->setAttribute('preserveAspectRatio', 'none');
                    $logoNode->setAttribute('href', $logo->getImageDataUri());
                    $element->parentNode->replaceChild($logoNode, $element);
                    break;
                case $this->IDLabel:
                    if ($label === null)
                        break;
                    $element->nodeValue = $label->text;
                    break;
            }
        }
        return $this->dom;
    }
}