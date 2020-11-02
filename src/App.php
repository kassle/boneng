<?php declare(strict_types=1);

namespace Boneng;

use HttpStatusCodes\HttpStatusCodes;

final class App {
    private $handlers = array();
    private $renderer;
    // private $decoder;

    public function __construct(Renderer $renderer) {
        $this->renderer = $renderer;
        // $this->decoder = $decoder;
    }

    public function addHandler(Handler $handler) : void {
        $this->handlers[$handler->getPath()][$handler->getMethod()] = $handler;
    }

    public function run() : void {
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        $path = $_GET[Parameter::KEY_PATH];

        try {
            $handler = $this->findHandler($path, $method);
            $result = $handler->run($_GET);
        } catch (\Throwable $ex) {
            // error_log("$ex");
            $result = new Result(HttpStatusCodes::HTTP_NOT_FOUND_CODE);
        }

        if ($this->isHtml()) {
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

        throw new \Exception("No handler available for path: " . $path . "; method: " . $method);
    }

    // private function getRequest($method) : array {
    //     if (METHOD_GET == $method) {
    //         return $_GET;
    //     } else {
    //         $body = json_decode(file_get_contents("php://input"), true, 5);
    //         return array_merge($_POST, $body);
    //     }
    // }

    private function isHtml() : bool {
        if (\array_key_exists(Parameter::KEY_ACCEPT, $_GET)) {
            return (\stripos($_GET[Parameter::KEY_ACCEPT], Parameter::VALUE_ACCEPT_HTML) >= 0);
        } else {
            return false;
        }
    }
}