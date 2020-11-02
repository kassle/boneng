<?php declare(strict_types=1);

namespace Boneng\Codex;

use Boneng\Model;

class HttpDecoder implements Decoder {
    private const KEY_PATH = 'path';
    private const KEY_ACCEPT = 'HTTP_ACCEPT';
    private const KEY_METHOD = 'REQUEST_METHOD';

    private const VALUE_ACCEPT_HTML = 'text/html';

    public function getMethod() : string {
        if (\array_key_exists(HttpDecoder::KEY_METHOD, $_SERVER)) {
            $method = \strtoupper($_SERVER[HttpDecoder::KEY_METHOD]);
            if (Decoder::METHOD_GET === $method) {
                return Decoder::METHOD_GET;
            } else if (Decoder::METHOD_POST === $method) {
                return Decoder::METHOD_POST;
            } else if (Decoder::METHOD_OPTIONS === $method) {
                return Decoder::METHOD_OPTIONS;
            } else {
                return Decoder::METHOD_OTHER;
            }
        } else {
            return Decoder::METHOD_OTHER;
        }
    }

    public function getApiPath() : string {
        if (\array_key_exists(HttpDecoder::KEY_PATH, $_REQUEST)) {
            return $_REQUEST[HttpDecoder::KEY_PATH];
        } else {
            throw new \InvalidArgumentException("API Path is not specified");
        }
    }

    public function getAcceptedType() : int {
        if ($this->isAcceptHtml()) {
            return Decoder::ACCEPTED_TYPE_HTML;
        } else {
            return Decoder::ACCEPTED_TYPE_JSON;
        }
    }

    private function isAcceptHtml() : bool {
        if (\array_key_exists(HttpDecoder::KEY_ACCEPT, $_SERVER)) {
            $result = \stripos($_SERVER[HttpDecoder::KEY_ACCEPT], HttpDecoder::VALUE_ACCEPT_HTML);
            if ($result === false) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function getRequestParameters() : array {
        return $_REQUEST;
    }

    public function decode() : Request {
        return new Request('', '', array(), array(), array());
    }
}