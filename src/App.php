<?php declare(strict_types=1);

namespace Boneng;

use Boneng\Codex\Decoder;
use Boneng\Exception\DecodeBodyException;
use Boneng\Exception\DecodeHeaderException;
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
    private $htmlRenderer;
    private $jsonRenderer;

    public function __construct(Decoder $decoder, Renderer $htmlRenderer, Renderer $jsonRenderer) {
        $this->decoder = $decoder;
        $this->htmlRenderer = $htmlRenderer;
        $this->jsonRenderer = $jsonRenderer;
    }

    public function addHandler(Handler $handler) : void {
        $this->handlers[$handler->getPath()][$handler->getMethod()] = $handler;
    }

    public function run() : void {
        $request = NULL;
        try {
            $request = $this->decoder->decode();
            $handler = $this->findHandler($request->getPath(), $request->getMethod());
            if ($handler->validate($request)) {
                $result = $handler->run($request);
            } else {
                $result = new Result(HttpStatusCodes::HTTP_BAD_REQUEST_CODE);
            }
        } catch (DecodeHeaderException $ex) {
            error_log($ex->getMessage());
            $result = new Result(HttpStatusCodes::HTTP_BAD_REQUEST_CODE);
        } catch (DecodeBodyException $ex) {
            error_log("$ex");
            $result = new Result(HttpStatusCodes::HTTP_BAD_REQUEST_CODE);
        } catch (NoHandlerException $ex) {
            error_log($ex->getMessage());
            $result = new Result(HttpStatusCodes::HTTP_NOT_FOUND_CODE);
        } catch (\Throwable $err) {
            error_log("$err");
            $result = new Result(HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR_CODE);
        }

        if ($this->isHtml($request)) {
            $response = $this->htmlRenderer->render($result);
        } else {
            $response = $this->jsonRenderer->render($result);
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

    private function isHtml(Request $request = NULL) : bool {
        if (!\is_null($request)) {
            $headers = $request->getHeaders();
            if (\array_key_exists(App::KEY_ACCEPT_TYPE, $headers)) {
                return (\stripos($headers[App::KEY_ACCEPT_TYPE], App::VALUE_TYPE_HTML) >= 0);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}