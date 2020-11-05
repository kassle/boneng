<?php declare(strict_types=1);

namespace Boneng;

use Boneng\Codex\Decoder;
use Boneng\Exception\NoHandlerException;
use Boneng\Model\Request;
use Boneng\Model\Response;
use Boneng\Model\Result;
use Boneng\Processor\Handler;
use Boneng\Processor\Renderer;
use HttpStatusCodes\HttpStatusCodes;

final class App {
    private const KEY_ACCEPT_TYPE = 'Accept';
    private const VALUE_TYPE_HTML = 'text/html';
    private $handlers = array();
    private $decoder;
    private $renderer;

    public function __construct(Decoder $decoder, Renderer $renderer) {
        $this->decoder = $decoder;
        $this->renderer = $renderer;
    }

    public function addHandler(Handler $handler) : void {
        $this->handlers[$handler->getPath()][$handler->getMethod()] = $handler;
    }

    public function run() : void {
        $request = $this->decoder->decode();

        try {
            $handler = $this->findHandler($request->getPath(), $request->getMethod());
            if ($handler->validate($request)) {
                $result = $handler->run($request);
            } else {
                $result = new Result(HttpStatusCodes::HTTP_BAD_REQUEST_CODE);
            }
        } catch (NoHandlerException $ex) {
            error_log($ex->getMessage());
            $result = new Result(HttpStatusCodes::HTTP_NOT_FOUND_CODE);
        } catch (\Throwable $err) {
            error_log("$err");
            $result = new Result(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR_CODE);
        }

        if ($this->isHtml($request)) {
            $response = $this->renderer->render($result);
        } else {
            $response = new Response($result->getStatus(), array(), "");
        }

        \http_response_code($response->getStatusCode());
        if (strlen($response->getBody()) > 0) {
            print($response->getBody());
        }
    }

    private function findHandler(string $path, string $method) : Handler {
        if (array_key_exists($path, $this->handlers)) {
            $sub = $this->handlers[$path];
            if (array_key_exists($method, $sub)) {
                return $sub[$method];
            }
        }

        throw new NoHandlerException($method, $path);
    }

    private function isHtml(Request $request) : bool {
        $headers = $request->getHeaders();
        if (\array_key_exists(App::KEY_ACCEPT_TYPE, $headers)) {
            return (\stripos($headers[App::KEY_ACCEPT_TYPE], App::VALUE_TYPE_HTML) >= 0);
        } else {
            return false;
        }
    }
}