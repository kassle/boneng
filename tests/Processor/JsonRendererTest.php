<?php declare(strict_types=1);

namespace Boneng\Processor;

use PHPUnit\Framework\TestCase;
use Boneng\Model\Response;
use Boneng\Model\Result;
use HttpStatusCodes\HttpStatusCodes;

class JsonRendererTest extends TestCase {
    private $renderer;

    protected function setup() : void {
        $this->renderer = new JsonRenderer();
    }

    public function testRenderShouldReturnResponseWithCorrectStatusCode() {
        $result = $this->createMock(Result::class);
        $result->expects($this->any())->method('getStatus')->willReturn(HttpStatusCodes::HTTP_BAD_REQUEST_CODE);

        $response = $this->renderer->render($result);

        $this->assertEquals(HttpStatusCodes::HTTP_BAD_REQUEST_CODE, $response->getStatusCode());
    }

    public function testRenderResponseGetBodyShouldReturnJsonEncodedData() {
        $data = [ 'first_name' => 'Mirael', 'last_name' => 'Heart' ];
        $result = $this->createMock(Result::class);
        $result->expects($this->any())->method('getData')->willReturn($data);

        $response = $this->renderer->render($result);

        $this->assertEquals(\json_encode($data), $response->getBody());
    }

    public function testRenderResponseGetBodyShouldReturnEmptyStringWhenNoData() {
        $result = $this->createMock(Result::class);
        $result->expects($this->any())->method('getData')->willReturn(array());

        $response = $this->renderer->render($result);

        $this->assertEquals('', $response->getBody());
    }

    public function testRenderResponseGetHeadersShouldIncludeContentTypeJson() {
        $result = $this->createMock(Result::class);
        $response = $this->renderer->render($result);
        $headers = $response->getHeaders();

        $this->assertEquals('application/json', $headers['Content-Type']);
    }

    public function testRenderResponseGetHeadersShouldIncludeContentLength() {
        $result = $this->createMock(Result::class);
        $response = $this->renderer->render($result);
        $headers = $response->getHeaders();

        $this->assertEquals(0, $headers['Content-Length']);
    }

    public function testHeaderContentLengthShouldBasedOnBodySize() {
        $data = [ 'first_name' => 'Mirael', 'last_name' => 'Heart' ];
        $result = $this->createMock(Result::class);
        $result->expects($this->any())->method('getData')->willReturn($data);

        $response = $this->renderer->render($result);
        $headers = $response->getHeaders();

        $this->assertEquals(strlen(\json_encode($data)), $headers['Content-Length']);
    }
}