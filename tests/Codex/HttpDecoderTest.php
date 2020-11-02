<?php declare(strict_types=1);

namespace Boneng\Codex;

use PHPUnit\Framework\TestCase;

final class HttpDecoderTest extends TestCase {
    private $decoder;

    protected function setUp(): void {
        $this->decoder = new HttpDecoder();
    }

    public function testGetMethodShouldReturnTypeGet() {
        $_SERVER['REQUEST_METHOD'] = 'get';

        $this->assertEquals(Decoder::METHOD_GET, $this->decoder->getMethod());
    }

    public function testGetMethodShouldReturnTypePost() {
        $_SERVER['REQUEST_METHOD'] = 'PosT';

        $this->assertEquals(Decoder::METHOD_POST, $this->decoder->getMethod());
    }

    public function testGetMethodShouldReturnTypeOption() {
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';

        $this->assertEquals(Decoder::METHOD_OPTIONS, $this->decoder->getMethod());
    }

    public function testGetMethodShouldReturnTypeOther() {
        $_SERVER['REQUEST_METHOD'] = 'UNKNOWN';

        $this->assertEquals(Decoder::METHOD_OTHER, $this->decoder->getMethod());
    }

    public function testGetMethodShouldReturnTypeOtherWhenNotSpecified() {
        $this->assertEquals(Decoder::METHOD_OTHER, $this->decoder->getMethod());
    }

    public function testGetApiPathShouldReturnApiPath() {
        $_REQUEST['path'] = '/my/api/path';

        $this->assertEquals($_REQUEST['path'], $this->decoder->getApiPath());
    }

    public function testGetApiPathShouldThrowExceptionWhenNotSpecified() {
        $this->expectException(\InvalidArgumentException::class);
        $this->decoder->getApiPath();
    }

    public function testGetAcceptedTypeShouldReturnJsonIfNotDefined() {
        $type = $this->decoder->getAcceptedType();

        $this->assertEquals(Decoder::ACCEPTED_TYPE_JSON, $type);
    }

    public function testGetAcceptedTypeShouldReturnHtmlIfDefinedAsHtml() {
        $_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';

        $type = $this->decoder->getAcceptedType();

        $this->assertEquals(Decoder::ACCEPTED_TYPE_HTML, $type);
    }

    public function testGetAcceptedTypeShouldReturnDefaultHtmlIfDefinedOtherThanHtml() {
        $_SERVER['HTTP_ACCEPT'] = 'application/xml';

        $type = $this->decoder->getAcceptedType();

        $this->assertEquals(Decoder::ACCEPTED_TYPE_JSON, $type);
    }

    public function testGetRequestParametersShouldReturnRequestArray() {
        $_REQUEST['key1'] = 'value1';
        $_REQUEST['key2'] = 'value2';

        $retval = $this->decoder->getRequestParameters();
        $result = (!array_diff($retval, $_REQUEST) && !array_diff($_REQUEST, $retval));

        $this->assertEquals(true, $result);
    }
}