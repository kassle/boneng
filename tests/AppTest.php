<?php declare(strict_types=1);

namespace Boneng;

use PHPUnit\Framework\TestCase;
use Boneng\Codex\Decoder;
use Boneng\Model\Request;
use Boneng\Model\Response;
use Boneng\Exception\NoHandlerException;
use HttpStatusCodes\HttpStatusCodes;

final class AppTest extends TestCase {
    private $decoder;
    private $renderer;

    private $app;

    protected function setUp(): void {
        $this->decoder = $this->createMock(Decoder::class);
        $this->renderer = $this->createMock(Renderer::class);

        $this->app = new App($this->decoder, $this->renderer);
    }

    public function testRunWithoutAnyHandlerShouldReturn404() {
        $request = $this->createMock(Request::class);
        $request->expects($this->any())->method('getMethod')->will($this->returnValue('GET'));
        $request->expects($this->any())->method('getPath')->will($this->returnValue('/article/404'));
        $request->expects($this->any())->method('getHeaders')->will($this->returnValue(array()));
        $this->decoder->expects($this->once())->method('decode')->will($this->returnValue($request));

        $this->app->run();

        $this->assertEquals(HttpStatusCodes::HTTP_NOT_FOUND_CODE, \http_response_code());
    }

    public function testRunWithoutAnyHandlerShouldReturn404InHtmlFormat() {
        $headers = [ 'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8' ];
        $body = '404 - Not Found';

        $request = $this->createMock(Request::class);
        $request->expects($this->any())->method('getMethod')->will($this->returnValue('GET'));
        $request->expects($this->any())->method('getPath')->will($this->returnValue('/article/404'));
        $request->expects($this->any())->method('getHeaders')->will($this->returnValue($headers));
        $this->decoder->expects($this->once())->method('decode')->will($this->returnValue($request));

        $response = new Response(HttpStatusCodes::HTTP_OK_CODE, array(), $body);
        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with($this->callback(function($result) {
                return $result->getStatus() === HttpStatusCodes::HTTP_NOT_FOUND_CODE;
            }))
            ->will($this->returnValue($response));


        $this->expectOutputString($body);
        $this->app->run();
        $this->assertEquals(HttpStatusCodes::HTTP_OK_CODE, \http_response_code());
    }

    public function testRunShouldReturn400WhenFailInHandlerValidation() {
        $path = '/users/password';
        $method = 'POST';

        $request = $this->createMock(Request::class);
        $request->expects($this->any())->method('getMethod')->will($this->returnValue($method));
        $request->expects($this->any())->method('getPath')->will($this->returnValue($path));
        $request->expects($this->any())->method('getHeaders')->will($this->returnValue(array()));

        $handler = $this->createMock(Handler::class);
        $handler->expects($this->any())->method('getPath')->will($this->returnValue($path));
        $handler->expects($this->any())->method('getMethod')->will($this->returnValue($method));
        $handler->expects($this->once())->method('validate')->with($this->equalTo($request))->will($this->returnValue(false));

        $this->decoder->expects($this->once())->method('decode')->will($this->returnValue($request));

        $this->app->addHandler($handler);
        $this->app->run();

        $this->assertEquals(HttpStatusCodes::HTTP_BAD_REQUEST_CODE, \http_response_code());
    }

    public function testRunShouldPassRequestToHandler() {
        $path = '/article';
        $method = 'POST';

        $result = $this->createMock(Result::class);
        $result->expects($this->any())->method('getStatus')->will($this->returnValue(HttpStatusCodes::HTTP_OK_CODE));

        $request = $this->createMock(Request::class);
        $request->expects($this->any())->method('getMethod')->will($this->returnValue($method));
        $request->expects($this->any())->method('getPath')->will($this->returnValue($path));
        $request->expects($this->any())->method('getHeaders')->will($this->returnValue(array()));

        $handler = $this->createMock(Handler::class);
        $handler->expects($this->any())->method('getPath')->will($this->returnValue($path));
        $handler->expects($this->any())->method('getMethod')->will($this->returnValue($method));
        $handler->expects($this->once())->method('validate')->with($this->equalTo($request))->will($this->returnValue(true));
        $handler->expects($this->once())->method('run')->with($this->equalTo($request))->will($this->returnValue($result));

        $this->decoder->expects($this->once())->method('decode')->will($this->returnValue($request));

        $this->app->addHandler($handler);
        $this->app->run();

        $this->assertEquals(HttpStatusCodes::HTTP_OK_CODE, \http_response_code());
    }

    public function testRunShouldReturn500WhenHandlerValidationHasError() {
        $path = '/users';
        $method = 'DELETE';

        $request = $this->createMock(Request::class);
        $request->expects($this->any())->method('getMethod')->willReturn($method);
        $request->expects($this->any())->method('getPath')->willReturn($path);
        $request->expects($this->any())->method('getHeaders')->willReturn(array());

        $handler = $this->createMock(Handler::class);
        $handler->expects($this->any())->method('getPath')->willReturn($path);
        $handler->expects($this->any())->method('getMethod')->willReturn($method);
        $handler->expects($this->once())->method('validate')->with($this->equalTo($request))->will($this->throwException(new \Exception()));

        $this->decoder->expects($this->once())->method('decode')->willReturn($request);

        $this->app->addHandler($handler);
        $this->app->run();

        $this->assertEquals(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR_CODE, \http_response_code());
    }

    public function testRunShouldReturn500WhenHandlerRunHasError() {
        $path = '/users';
        $method = 'UPDATE';

        $request = $this->createMock(Request::class);
        $request->expects($this->any())->method('getMethod')->willReturn($method);
        $request->expects($this->any())->method('getPath')->willReturn($path);
        $request->expects($this->any())->method('getHeaders')->willReturn(array());

        $handler = $this->createMock(Handler::class);
        $handler->expects($this->any())->method('getPath')->willReturn($path);
        $handler->expects($this->any())->method('getMethod')->willReturn($method);
        $handler->expects($this->once())->method('validate')->with($this->equalTo($request))->willReturn(true);
        $handler->expects($this->once())->method('run')->with($this->equalTo($request))->will($this->throwException(new \Exception()));

        $this->decoder->expects($this->once())->method('decode')->willReturn($request);

        $this->app->addHandler($handler);
        $this->app->run();

        $this->assertEquals(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR_CODE, \http_response_code());
    }
}