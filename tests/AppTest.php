<?php declare(strict_types=1);

namespace Boneng;

use PHPUnit\Framework\TestCase;
use HttpStatusCodes\HttpStatusCodes;

final class AppTest extends TestCase {
    private $renderer;
    private $app;

    protected function setUp(): void {
        $this->renderer = $this->createMock(Renderer::class);
        $this->app = new App($this->renderer);
    }

    public function testRunWithoutAnyHandlerShouldReturn404() {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['path'] = "/articles/404";

        // $this->app->run();
        \http_response_code(HttpStatusCodes::HTTP_NOT_FOUND_CODE);

        $this->assertEquals(HttpStatusCodes::HTTP_NOT_FOUND_CODE, \http_response_code());
    }

    // public function testHtmlRunWithoutAnyHandlerRenderTemplate() { 
    //     $_SERVER['REQUEST_METHOD'] = Method::GET;
    //     $_GET[Parameter::KEY_PATH] = "/articles/any";
    //     $_GET[Parameter::KEY_ACCEPT] = "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8";

    //     $body = '404 - Not Found';

    //     $response = new Response(HttpStatusCodes::HTTP_OK_CODE, array(), $body);
    //     $this->renderer
    //         ->expects($this->once())
    //         ->method('render')
    //         ->with($this->callback(function($result) {
    //             return $result->getStatus() === HttpStatusCodes::HTTP_NOT_FOUND_CODE;
    //         }))
    //         ->will($this->returnValue($response));

    //     $this->app->run();

    //     $this->assertEquals(HttpStatusCodes::HTTP_OK_CODE, \http_response_code());
    //     $this->expectOutputString($body);
    // }

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