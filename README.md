# QR Code

[![Latest Stable Version](http://img.shields.io/packagist/v/fmassei/qr-code.svg)](https://packagist.org/packages/fmassei/qr-code)
[![Build Status](https://github.com/fmassei/qr-code/workflows/CI/badge.svg)](https://github.com/fmassei/qr-code/actions)
[![Total Downloads](http://img.shields.io/packagist/dt/fmassei/qr-code.svg)](https://packagist.org/packages/fmassei/qr-code)
[![Monthly Downloads](http://img.shields.io/packagist/dm/fmassei/qr-code.svg)](https://packagist.org/packages/fmassei/qr-code)
[![License](http://img.shields.io/packagist/l/fmassei/qr-code.svg)](https://packagist.org/packages/fmassei/qr-code)

This library helps you create QR codes in SVG, PNG, and binary format.

- Use the Imagick extension to render self-generated SVG images.
- Logo and label placement
- Can use of SVG frames
- Common generation options
- Matrix generated by [bacon/bacon-qr-code](https://github.com/Bacon/BaconQrCode)
- Simple internal structure

This library is based on the structure of [endroid/qr-code](https://github.com/endroid/qr-code), trying to
overcome many deal-breaker characteristics present at the time of forking.
Among the bigger changes:
- Unified drawing (so results look the same in all formats)
- Switch from GD to Imagick (for SVG support)
- Switch from SimpleXMLElement to DOMDocument (more complex operations on SVG nodes)

## Installation

Use [Composer](https://getcomposer.org/) to install the library.

``` bash
$ composer require fmassei/qr-code
```

## Usage: generation and options

```php
use fmassei\QrCode\QrCode;
use fmassei\QrCode\Logo;
use fmassei\QrCode\Label;
use fmassei\QrCode\Writer\PngWriter;

/* to create a code, make an instance of the QrCode class */
$qrCode = new QrCode("QR code data");

/* override some default settings */
$qrCode->size = 400;
$qrCode->foregroundColor = '#f00';
$qrCode->errorCorrectionLevel = QrCode::ERROR_CORRECTION_LEVEL_MEDIUM;

/* to set the optional logo and a label */
$qrCode->logo = Logo::fromPath(__DIR__.'/mylogo.png');
$qrCode->label = new Label("My first code!");

/* use a writer to generate the result */
$result = (new PngWriter())->write($qrCode);
        
```

## Usage: working with results

```php
/* Directly output the QR code */
header('Content-Type: '.$result->getMimeType());
echo $result->getString();

/* Save it to a file */
$result->saveToFile(__DIR__.'/qrcode.png');

/* Generate a data URI to include image data inline (i.e. inside an <img> tag) */
$dataUri = $result->getDataUri();
```

## One liner
Everyone expects it, I guess.
```php
echo (new PngWriter())->write(new QrCode($myData))->getString();
```

## Frames


## License

This bundle is under the MIT license. For the full copyright and license
information please view the LICENSE file that was distributed with this source code.