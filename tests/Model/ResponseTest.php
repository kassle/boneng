<?php declare(strict_types=1);

namespace Boneng\Model;

use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase {
    const STATUS_CODE = 123;
    const HEADER_KEY = 'Content-Type';
    const HEADER_VALUE = 'application/json';
    const BODY = '{ "message": "hello world" }';

    private $response;

    protected function setUp(): void {
        $this->response = new Response(
            ResponseTest::STATUS_CODE,
            [ ResponseTest::HEADER_KEY => ResponseTest::HEADER_VALUE ],
            ResponseTest::BODY);
    }

    public function testGetStatusCodeShouldReturnSpecifiedStatusCode() {
        $this->assertEquals(ResponseTest::STATUS_CODE, $this->response->getStatusCode());
    }

    public function testGetHeadersShouldReturnSpecifiedHeaders() {
        $headers = $this->response->getHeaders();

        $this->assertEquals(1, \sizeof($headers));
        $this->assertEquals(ResponseTest::HEADER_KEY, \array_key_first($headers));
        $this->assertEquals(ResponseTest::HEADER_VALUE, $headers[ResponseTest::HEADER_KEY]);
    }

    public function testGetBodyShouldReturnSpecifiedBody() {
        $this->assertEquals(ResponseTest::BODY, $this->response->getBody());
    }
}