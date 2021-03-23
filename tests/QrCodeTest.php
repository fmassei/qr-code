<?php

declare(strict_types=1);

namespace fmassei\QrCode\Tests;

use fmassei\QrCode\QrCode;
use fmassei\QrCode\Label;
use fmassei\QrCode\Logo;
use fmassei\QrCode\SVGFrame;
use fmassei\QrCode\Writer\IWriter;
use fmassei\QrCode\Writer\BinaryWriter;
use fmassei\QrCode\Writer\DebugWriter;
use fmassei\QrCode\Writer\PngWriter;
use fmassei\QrCode\Writer\SvgWriter;
use fmassei\QrCode\Writer\Result\BinaryResult;
use fmassei\QrCode\Writer\Result\DebugResult;
use fmassei\QrCode\Writer\Result\PngResult;
use fmassei\QrCode\Writer\Result\SvgResult;
use \Exception;
use Generator;
use Imagick;
use PHPUnit\Framework\TestCase;

final class QrCodeTest extends TestCase
{
    /**
     * @testdox Write as $resultClass with content type $contentType
     * @dataProvider writerProvider
     * @param IWriter $writer
     * @param string $resultClass
     * @param string $contentType
     */
    public function testQrCode(IWriter $writer, string $resultClass, string $contentType): void
    {
        $qrCode = new QrCode("test");

        // Create generic logo
        $qrCode->logo = Logo::fromPath(__DIR__.'/assets/logo.png');

        // Create generic label
        $qrCode->label = new Label('Label');

        $result = $writer->write($qrCode);

        $this->assertInstanceOf($resultClass, $result);
        $this->assertEquals($contentType, $result->getMimeType());
        $this->assertStringContainsString('data:'.$result->getMimeType().';base64,', $result->getDataUri());
    }

    public function writerProvider(): Generator
    {
        yield [new BinaryWriter(), BinaryResult::class, 'text/plain'];
        yield [new DebugWriter(), DebugResult::class, 'text/plain'];
        yield [new PngWriter(), PngResult::class, 'image/png'];
        yield [new SvgWriter(), SvgResult::class, 'image/svg+xml'];
    }

    /**
     * @testdox Size and margin are handled correctly
     */
    public function testSetSize(): void
    {
        $qrCode = new QrCode("QR Code");
        $qrCode->size = 400;
        $qrCode->margin = 15;
        $image = (new PngWriter())->write($qrCode)->image;
        $this->assertTrue($image->getImageWidth() === 430);
        $this->assertTrue($image->getImageHeight() === 430);
    }

    /**
     * @testdox Size and margin are handled correctly with rounded blocks
     * @dataProvider roundedSizeProvider
     * @param int $size
     * @param int $margin
     * @param int $roundBlockSizeMode
     * @param int $expectedSize
     */
    public function testSetSizeRounded(int $size, int $margin, int $roundBlockSizeMode, int $expectedSize): void
    {
        $qrCode = new QrCode('QR Code contents with some length to have some data');
        $qrCode->size = $size;
        $qrCode->margin = $margin;
        $qrCode->roundBlockSizeMode = $roundBlockSizeMode;
        $image = (new PngWriter())->write($qrCode)->image;
        $this->assertTrue($image->getImageWidth() === $expectedSize);
        $this->assertTrue($image->getImageHeight() === $expectedSize);
    }

    public function roundedSizeProvider(): Generator
    {
        yield [400, 0, QrCode::ROUND_BLOCK_SIZE_MODE_ENLARGE, 406];
        yield [400, 5, QrCode::ROUND_BLOCK_SIZE_MODE_ENLARGE, 416];
        yield [400, 0, QrCode::ROUND_BLOCK_SIZE_MODE_MARGIN, 400];
        yield [400, 5, QrCode::ROUND_BLOCK_SIZE_MODE_MARGIN, 410];
        yield [400, 0, QrCode::ROUND_BLOCK_SIZE_MODE_SHRINK, 377];
        yield [400, 5, QrCode::ROUND_BLOCK_SIZE_MODE_SHRINK, 387];
    }

    /**
     * @testdox Invalid logo path results in exception
     */
    public function testInvalidLogoPath(): void
    {
        $this->expectException(Exception::class);
        $writer = new SvgWriter();
        $qrCode = new QrCode('QR Code');
        $qrCode->logo = Logo::fromPath('/my/invalid/path.png');
        $writer->write($qrCode);
    }

    /**
     * @testdox Invalid logo data results in exception
     */
    public function testInvalidLogoData(): void
    {
        $this->expectException(Exception::class);
        $writer = new SvgWriter();
        $qrCode = new QrCode('QR Code');
        $qrCode->logo = Logo::fromPath(__DIR__.'/QrCodeTest.php');
        $writer->write($qrCode);
    }

    /**
     * @testdox Result can be saved to file
     */
    public function testSavePngToFile(): void
    {
        $path = __DIR__.'/test-save-to-file.png';
        $writer = new PngWriter();
        $qrCode = new QrCode('QR Code');
        $qrCode->errorCorrectionLevel = QrCode::ERROR_CORRECTION_LEVEL_MEDIUM;
        $qrCode->logo = Logo::fromPath(__DIR__.'/assets/logo.png');
        $qrCode->label = new Label("MyLabel");
        $qrCode->label->backgroundColor = '#f0f';
        $qrCode->label->textColor = '#0ff';
        $qrCode->label->alignment = Label::LABEL_TEXT_ALIGNM_RIGHT;
        $qrCode->label->margin = [50,50,50,50];

        file_put_contents($path, $writer->write($qrCode)->getString());

        $this->assertTrue((new Imagick())->readImage($path));

        unlink($path);
    }

    /**
     * @testdox Result can be saved to file
     */
    public function testSaveSvgToFile(): void
    {
        $path = __DIR__.'/test-save-to-file.svg';
        $writer = new SvgWriter();
        $qrCode = new QrCode('QR Code');
        $qrCode->label = new Label("MyLabel");
        $qrCode->logo = Logo::fromPath(__DIR__.'/assets/logo.png');
        $qrCode->frame = SVGFrame::fromFile(__DIR__."/assets/frame_test.svg");

        file_put_contents($path, $writer->write($qrCode)->getString());

        $this->assertTrue((new Imagick())->readImage($path));

        unlink($path);
    }
}
