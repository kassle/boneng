<?php declare(strict_types=1);

namespace Boneng\Codex;

use PHPUnit\Framework\TestCase;
use Boneng\Model\Request;

final class HttpDecoderTest extends TestCase {
    private const MAX_LENGTH = 1024;
    private $inputSrc;
    private $decoder;

    protected function setUp(): void {
        $this->inputSrc = \tempnam(sys_get_temp_dir(), 'boneng_');
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->decoder = new HttpDecoder(HttpDecoderTest::MAX_LENGTH, $this->inputSrc);
    }

    protected function tearDown(): void {
        \unlink($this->inputSrc);
    }

    public function testDecodeReturnRootPathWhenApiPathNotSpecified() {
        $request = $this->decoder->decode();

        $this->assertEquals('/', $request->getPath());
    }

    public function testDecodeReturnSpecifiedPath() {
        $path = '/article/1001';
        $_REQUEST['path'] = $path;
        $request = $this->decoder->decode();

        $this->assertEquals($path, $request->getPath());
    }

    public function testDecodeReturnSpecifiedMethod() {
        $method = 'PUT';
        $_SERVER['REQUEST_METHOD'] = $method;

        $request = $this->decoder->decode();

        $this->assertEquals($method, $request->getMethod());
    }

    public function testDecodeThrowInvalidArgumentExceptionWhenMethodNotSpecified() {
        unset($_SERVER['REQUEST_METHOD']);

        $this->expectException(\InvalidArgumentException::class);

        $this->decoder->decode();
    }

    public function testDecodeShouldReturnEmptyHeadersWhenNoneSpecified() {
        $request = $this->decoder->decode();

        $this->assertEquals(0, sizeof($request->getHeaders()));
    }

    public function testDecodeShouldReturnAllSpecifiedHeaders() {
        $header_key1 = 'Accept';
        $header_value1 = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
        $header_key2 = 'User-Agent';
        $header_value2 = 'Mozilla/5.0 (X11; Linux x86_64; rv:78.0) Gecko/20100101 Firefox/78.0';

        $headers = [ $header_key1 => $header_value1, $header_key2 => $header_value2 ];

        $_SERVER['HTTP_ACCEPT'] = $header_value1;
        $_SERVER['HTTP_USER_AGENT'] = $header_value2;
        $_SERVER['HOST'] = 'localhost';

        $request = $this->decoder->decode();

        $this->assertTrue($this->isArraysEquals($headers, $request->getHeaders()));
    }

    public function testDecodeShouldReturnAlsoIncludeSpecialContentHeaders() {
        $header_key1 = 'Content-Type';
        $header_value1 = 'application/json';
        $header_key2 = 'Content-Encoding';
        $header_value2 = 'gzip';
        $header_key3 = 'User-Agent';
        $header_value3 = 'Mozilla/5.0 (X11; Linux x86_64; rv:78.0) Gecko/20100101 Firefox/78.0';

        $headers = [ $header_key1 => $header_value1, $header_key2 => $header_value2, $header_key3 => $header_value3 ];

        $_SERVER['CONTENT_TYPE'] = $header_value1;
        $_SERVER['CONTENT_ENCODING'] = $header_value2;
        $_SERVER['HTTP_USER_AGENT'] = $header_value3;
        $_SERVER['HOST'] = 'localhost';

        $request = $this->decoder->decode();

        $this->assertTrue($this->isArraysEquals($headers, $request->getHeaders()));
    }

    public function testDecodeShouldReturnEmptyRequestParamsWhenNotSpecified() {
        $request = $this->decoder->decode();

        $this->assertEquals(0, sizeof($request->getParameters()));
    }

    public function testDecodeShouldReturnAllRequestParamsWhenSpecified() {
        $_REQUEST['param1'] = 'value of param1';
        $_REQUEST['param2'] = 'value of param2';
        $_REQUEST['param3'] = 'value of param3';
        $_REQUEST['param4'] = 'value of param4';

        $request = $this->decoder->decode();

        $this->assertTrue($this->isArraysEquals($_REQUEST, $request->getParameters()));
    }

    public function testDecodeShouldReturnEmptyBodyWhenNotSpecified() {
        $request = $this->decoder->decode();

        $this->assertEquals(0, sizeof($request->getBody()));
    }

    public function testDecodeShouldThrowInvalidArgumentExceptionWhenBodyIsNotJson() {
        $this->expectException(\InvalidArgumentException::class);

        $_SERVER['CONTENT_TYPE'] = 'text/html';
        $_SERVER['CONTENT_LENGTH'] = '100';


        $this->decoder->decode();
    }

    public function testDecodeShouldThrowInvalidArgumentExceptionWhenBodyLengthIsTooLarge() {
        $this->expectException(\InvalidArgumentException::class);

        $_SERVER['CONTENT_TYPE'] = 'text/html';
        $_SERVER['CONTENT_LENGTH'] = '1025';

        $this->decoder->decode();
    }

    public function testDecodeShouldReturnParsedBody() {
        $json = '{ "msg": "hello world" }';
        $expected = [ 'msg' => 'hello world' ];

        $file = \fopen($this->inputSrc, "w");
        \fwrite($file, $json);
        \fclose($file);

        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_SERVER['CONTENT_LENGTH'] = \strlen($json);

        $request = $this->decoder->decode();

        $this->assertTrue($this->isArraysEquals($expected, $request->getBody()));
    }

    public function testDecodeShouldThrowInvalidArgumentExceptionWhenFailToParseBody() {
        $json = '{ "not json format" }';

        $file = \fopen($this->inputSrc, "w");
        \fwrite($file, $json);
        \fclose($file);

        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_SERVER['CONTENT_LENGTH'] = \strlen($json);

        $this->expectException(\InvalidArgumentException::class);
        $request = $this->decoder->decode();
    }

    private function isArraysEquals(array $array1, array $array2) : bool {
        return (!array_diff($array1, $array2) && !array_diff($array2, $array1));
    }

    // public function testGetMethodShouldReturnTypeGet() {
    //     $_SERVER['REQUEST_METHOD'] = 'get';

    //     $this->assertEquals(Decoder::METHOD_GET, $this->decoder->getMethod());
    // }

    // public function testGetMethodShouldReturnTypePost() {
    //     $_SERVER['REQUEST_METHOD'] = 'PosT';

    //     $this->assertEquals(Decoder::METHOD_POST, $this->decoder->getMethod());
    // }

    // public function testGetMethodShouldReturnTypeOption() {
    //     $_SERVER['REQUEST_METHOD'] = 'OPTIONS';

    //     $this->assertEquals(Decoder::METHOD_OPTIONS, $this->decoder->getMethod());
    // }

    // public function testGetMethodShouldReturnTypeOther() {
    //     $_SERVER['REQUEST_METHOD'] = 'UNKNOWN';

    //     $this->assertEquals(Decoder::METHOD_OTHER, $this->decoder->getMethod());
    // }

    // public function testGetMethodShouldReturnTypeOtherWhenNotSpecified() {
    //     $this->assertEquals(Decoder::METHOD_OTHER, $this->decoder->getMethod());
    // }

    // public function testGetApiPathShouldReturnApiPath() {
    //     $_REQUEST['path'] = '/my/api/path';

    //     $this->assertEquals($_REQUEST['path'], $this->decoder->getApiPath());
    // }

    // public function testGetApiPathShouldThrowExceptionWhenNotSpecified() {
    //     $this->expectException(\InvalidArgumentException::class);
    //     $this->decoder->getApiPath();
    // }

    // public function testGetAcceptedTypeShouldReturnJsonIfNotDefined() {
    //     $type = $this->decoder->getAcceptedType();

    //     $this->assertEquals(Decoder::ACCEPTED_TYPE_JSON, $type);
    // }

    // public function testGetAcceptedTypeShouldReturnHtmlIfDefinedAsHtml() {
    //     $_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';

    //     $type = $this->decoder->getAcceptedType();

    //     $this->assertEquals(Decoder::ACCEPTED_TYPE_HTML, $type);
    // }

    // public function testGetAcceptedTypeShouldReturnDefaultHtmlIfDefinedOtherThanHtml() {
    //     $_SERVER['HTTP_ACCEPT'] = 'application/xml';

    //     $type = $this->decoder->getAcceptedType();

    //     $this->assertEquals(Decoder::ACCEPTED_TYPE_JSON, $type);
    // }

    // public function testGetRequestParametersShouldReturnRequestArray() {
    //     $_REQUEST['key1'] = 'value1';
    //     $_REQUEST['key2'] = 'value2';

    //     $retval = $this->decoder->getRequestParameters();
    //     $result = (!array_diff($retval, $_REQUEST) && !array_diff($_REQUEST, $retval));

    //     $this->assertEquals(true, $result);
    // }
}