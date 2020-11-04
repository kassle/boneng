<?php declare(strict_types=1);

namespace Boneng;

use PHPUnit\Framework\TestCase;
use Boneng\Codex\Decoder;
use Boneng\Model\Request;
use Boneng\Model\Response;
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

    // public function testRunGetShouldPassParameterToHandler() {
    //     $path = '/article/123';
    //     $_SERVER['REQUEST_METHOD'] = 'Method::GET';
    //     $_GET[Parameter::KEY_PATH] = $path;
    //     $_GET['param_number_one'] = 'value_number_one';
    //     $_GET['param_number_two'] = 'value_number_two';

    //     $result = new Result(HttpStatusCodes::HTTP_BAD_REQUEST_CODE);

    //     $handler = $this->createMock(Handler::class);
    //     $handler->expects($this->any())->method('getMethod')->will($this->returnValue(Method::GET));
    //     $handler->expects($this->any())->method('getPath')->will($this->returnValue($path));
    //     $handler->expects($this->once())
    //         ->method('run')
    //         ->with($this->callback(function($param) {
    //             return !array_diff($param, $_GET) && !array_diff($_GET, $param);
    //         }))
    //         ->will($this->returnValue($result));

    //     $this->app->addHandler($handler);
    //     $this->app->run();

    //     $this->assertEquals(HttpStatusCodes::HTTP_BAD_REQUEST_CODE, \http_response_code());
    // }

    // public function testRunPostShouldPassParameterToHandler() {
    //     $path = '/article/post';
    //     $_SERVER['REQUEST_METHOD'] = Method::POST;
    //     $_POST[Parameter::KEY_PATH] = $path;
    //     $_POST['param_number_one'] = 'value_number_one';
    //     $_POST['param_number_two'] = 'value_number_two';

    //     $result = new Result(HttpStatusCodes::HTTP_BAD_REQUEST_CODE);

    //     $handler = $this->createMock(Handler::class);
    //     $handler->expects($this->any())->method('getMethod')->will($this->returnValue(Method::GET));
    //     $handler->expects($this->any())->method('getPath')->will($this->returnValue($path));
    //     $handler->expects($this->once())
    //         ->method('run')
    //         ->with($this->callback(function($param) {
    //             return !array_diff($param, $_GET) && !array_diff($_GET, $param);
    //         }))
    //         ->will($this->returnValue($result));

    //     $this->app->addHandler($handler);
    //     $this->app->run();

    //     $this->assertEquals(HttpStatusCodes::HTTP_BAD_REQUEST_CODE, \http_response_code());
    // }
}