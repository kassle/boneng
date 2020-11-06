<?php declare(strict_types=1);

namespace Boneng;

use PHPUnit\Framework\TestCase;
use Boneng\Exception\DecodeBodyException;
use Boneng\Exception\DecodeHeaderException;
use Boneng\Exception\NoHandlerException;
use Boneng\Codex\Decoder;
use Boneng\Model\Request;
use Boneng\Model\Response;
use Boneng\Model\Result;
use Boneng\Processor\Handler;
use Boneng\Processor\Renderer;
use HttpStatusCodes\HttpStatusCodes;
use Psr\Log\LoggerInterface;

final class AppTest extends TestCase {
    private $decoder;
    private $htmlRenderer;
    private $jsonRenderer;
    private $logger;

    private $app;

    protected function setUp(): void {
        $this->decoder = $this->createMock(Decoder::class);
        $this->htmlRenderer = $this->createMock(Renderer::class);
        $this->jsonRenderer = $this->createMock(Renderer::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->app = new App($this->decoder, $this->htmlRenderer, $this->jsonRenderer, $this->logger);
    }

    public function testDecodingFailProcessHeaderShouldReturn400() {
        $this->decoder->expects($this->once())->method('decode')->will($this->throwException(new DecodeHeaderException('no http method header')));

        $this->jsonRenderer->expects($this->once())
            ->method('render')
            ->with($this->callback(function($result) {
                return HttpStatusCodes::HTTP_BAD_REQUEST_CODE === $result->getStatus();
            }))->willReturn(new Response(HttpStatusCodes::HTTP_BAD_REQUEST_CODE, array(), ""));

        $this->app->run();

        $this->assertEquals(HttpStatusCodes::HTTP_BAD_REQUEST_CODE, \http_response_code());
    }

    public function testDecodingFailProcessBodyShouldReturn400() {
        $this->decoder->expects($this->once())->method('decode')->will($this->throwException(new DecodeBodyException('unable to parse body')));

        $this->jsonRenderer->expects($this->once())
            ->method('render')
            ->with($this->callback(function($result) {
                return HttpStatusCodes::HTTP_BAD_REQUEST_CODE === $result->getStatus();
            }))->willReturn(new Response(HttpStatusCodes::HTTP_BAD_REQUEST_CODE, array(), ""));

        $this->app->run();

        $this->assertEquals(HttpStatusCodes::HTTP_BAD_REQUEST_CODE, \http_response_code());
    }

    public function testRunWithoutAnyHandlerShouldReturn404() {
        $request = $this->createMock(Request::class);
        $request->expects($this->any())->method('getMethod')->will($this->returnValue('GET'));
        $request->expects($this->any())->method('getPath')->will($this->returnValue('/article/404'));
        $request->expects($this->any())->method('getHeaders')->will($this->returnValue(array()));

        $this->decoder->expects($this->once())->method('decode')->will($this->returnValue($request));

        $this->jsonRenderer->expects($this->once())
            ->method('render')
            ->with($this->callback(function($result) {
                return HttpStatusCodes::HTTP_NOT_FOUND_CODE === $result->getStatus();
            }))->willReturn(new Response(HttpStatusCodes::HTTP_NOT_FOUND_CODE, array(), ""));

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
        $this->htmlRenderer
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

        $this->jsonRenderer->expects($this->once())
            ->method('render')
            ->with($this->callback(function($result) {
                return HttpStatusCodes::HTTP_BAD_REQUEST_CODE === $result->getStatus();
            }))->willReturn(new Response(HttpStatusCodes::HTTP_BAD_REQUEST_CODE, array(), ""));

        $this->decoder->expects($this->once())->method('decode')->will($this->returnValue($request));

        $this->app->addHandler($handler);
        $this->app->run();

        $this->assertEquals(HttpStatusCodes::HTTP_BAD_REQUEST_CODE, \http_response_code());
    }

    public function testRunShouldPassRequestToHandler() {
        $path = '/article';
        $method = 'POST';

        $this->jsonRenderer->method('render')->willReturn(new Response(HttpStatusCodes::HTTP_OK_CODE, array(), ""));

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

        $this->jsonRenderer->expects($this->once())
            ->method('render')
            ->with($this->equalTo($result))
            ->willReturn(new Response(HttpStatusCodes::HTTP_OK_CODE, array(), ""));

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
        $handler->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($request))
            ->will($this->throwException(new \Exception()));

        $this->decoder->expects($this->once())->method('decode')->willReturn($request);

        $this->jsonRenderer->expects($this->once())
            ->method('render')
            ->with($this->callback(function($result) {
                return HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR_CODE === $result->getStatus();
            }))->willReturn(new Response(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR_CODE, array(), ""));

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

        $this->jsonRenderer->expects($this->once())
            ->method('render')
            ->with($this->callback(function($result) {
                return HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR_CODE === $result->getStatus();
            }))->willReturn(new Response(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR_CODE, array(), ""));

        $this->app->addHandler($handler);
        $this->app->run();

        $this->assertEquals(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR_CODE, \http_response_code());
    }

    /**
     * @runInSeparateProcess
     */
    public function testRunShouldSetupResponseHeader() {
        $headers = [
            'Content-Type' => 'application/json',
            'Content-Length' => 34
        ];
        $response = $this->createMock(Response::class);
        $response->method('getStatusCode')->willReturn(HttpStatusCodes::HTTP_OK_CODE);
        $response->method('getHeaders')->willReturn($headers);

        $this->decoder->method('decode')->will($this->throwException(new DecodeHeaderException('just for shortcut')));
        $this->jsonRenderer
            ->method('render')
            ->willReturn($response);

        $this->app->run();

        $setup = \xdebug_get_headers();

        $this->assertEquals(sizeof($headers), sizeof($setup));
        $this->assertEquals('Content-Type: application/json', $setup[0]);
        $this->assertEquals('Content-Length: 34', $setup[1]);
    }

    public function testRunShouldSendResponseCodeInternalErrorWhenRendererFail() {
        $this->decoder->method('decode')->will($this->throwException(new DecodeHeaderException('quick route')));
        $this->jsonRenderer->expects($this->once())
            ->method('render')
            ->will($this->throwException(new \Exception('testing purpose exception')));

        $this->app->run();

        $this->assertEquals(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR_CODE, \http_response_code());
    }

    public function testRunShouldSendResponseCodeInternalErrorWhenHTMLRendererFail() {
        $headers = [ 'Accept' => 'text/html' ];
        $request = $this->createMock(Request::class);
        $request->method('getHeaders')->willReturn($headers);

        $this->decoder->method('decode')->willReturn($request);
        $this->htmlRenderer->expects($this->once())
            ->method('render')
            ->will($this->throwException(new \Exception('testing purpose exception')));

        $this->app->run();

        $this->assertEquals(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR_CODE, \http_response_code());
    }
}