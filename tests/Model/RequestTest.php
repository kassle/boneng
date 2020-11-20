<?php declare(strict_types=1);

namespace Boneng\Model;

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase {
    private const METHOD = 'POST';
    private const PATH = '/api/news';
    private const HEADERS = [ 'Content-Type' => 'application/json', 'Content-Length' => 1024 ];
    private const PARAMS = [ 'user' => 'john.doe', 'token' => 'free2token4life' ];
    private const BODY = [ 'title' => 'Boneng - Is it a framework ?', 'author' => 'jane.doe' ];
    private $request;

    protected function setup() : void {
        $this->request = new Request(
            RequestTest::METHOD,
            RequestTest::PATH,
            RequestTest::HEADERS,
            RequestTest::PARAMS,
            RequestTest::BODY);
    }

    public function testGetMethodShouldReturnSpecifiedMethod() {
        $this->assertEquals(RequestTest::METHOD, $this->request->getMethod());
    }

    public function testGetPathShouldReturnSpecifiedPath() {
        $this->assertEquals(RequestTest::PATH, $this->request->getPath());
    }

    public function testGetHeadersShouldReturnSpecifiedHeaders() {
        $headers = $this->request->getHeaders();

        $this->assertEquals(true, $this->isArrayEquals(RequestTest::HEADERS, $headers));
    }

    public function testGetParametersShouldReturnSpecifiedParameters() {
        $params = $this->request->getParameters();

        $this->assertEquals(true, $this->isArrayEquals(RequestTest::PARAMS, $params));
    }

    public function testGetBodyShouldReturnSpecifiedBody() {
        $body = $this->request->getBody();

        $this->assertEquals(true, $this->isArrayEquals(RequestTest::BODY, $body));
    }

    private function isArrayEquals(array $array1, array $array2) : bool {
        return (!array_diff($array1, $array2) && !array_diff($array2, $array1));
    }
}