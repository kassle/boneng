<?php declare(strict_types=1);

namespace Boneng\Codex;

use Boneng\Exception\DecodeBodyException;
use Boneng\Exception\DecodeHeaderException;
use Boneng\Model\Request;

class HttpDecoder implements Decoder {
    private const KEY_PATH = 'path';
    private const KEY_METHOD = 'REQUEST_METHOD';
    private const KEY_CONTENT_TYPE = 'Content-Type';
    private const KEY_CONTENT_LENGTH = 'Content-Length';

    private const VALUE_TYPE_JSON = 'application/json';

    private const DEFAULT_MAX_LENGTH = 1024;
    private const DEFAULT_INPUT_SRC = "php://input";

    public function __construct(int $maxLength, string $inputSrc) {
        $this->maxLength = $maxLength;
        $this->inputSrc = $inputSrc;
    }

    public function decode() : Request {
        $headers = $this->getHeaders();
        return new Request(
            $this->getMethod(),
            $this->getApiPath(),
            $headers,
            $_REQUEST,
            $this->getBody($headers));
    }

    private function getMethod() : string {
        if (\array_key_exists(HttpDecoder::KEY_METHOD, $_SERVER)) {
            return \strtoupper($_SERVER[HttpDecoder::KEY_METHOD]);
        } else {
            throw new DecodeHeaderException('Request method not specified');
        }
    }

    private function getApiPath() : string {
        if (\array_key_exists(HttpDecoder::KEY_PATH, $_REQUEST)) {
            return $_REQUEST[HttpDecoder::KEY_PATH];
        } else {
            return '/';
        }
    }

    private function getHeaders() : array {
        $headers = array(); 
        foreach ($_SERVER as $key => $value) { 
            if (substr($key, 0, 5) == 'HTTP_') {
                $name = mb_convert_case(str_replace('_', '-', substr($key, 5)), MB_CASE_TITLE, 'UTF-8');
                $headers[$name] = $value; 
            } else if (substr($key, 0, 8) == 'CONTENT_') {
                $name = mb_convert_case(str_replace('_', '-', $key), MB_CASE_TITLE, 'UTF-8');
                $headers[$name] = $value; 
            }
        } 
        return $headers; 
    }

    private function getBody(array $headers) : array {
        if ($this->isBodyRequiredHeadersExist($headers)) {
            $type = $headers[HttpDecoder::KEY_CONTENT_TYPE];
            $length = intval($headers[HttpDecoder::KEY_CONTENT_LENGTH]);
            if (HttpDecoder::VALUE_TYPE_JSON == $type && $this->maxLength >= $length) {
                return $this->parseBody($length);
            } else {
                throw new DecodeHeaderException('Unsupported content-type: ' . $headers[HttpDecoder::KEY_CONTENT_TYPE] . ' and content-length: ' . $length);
            }
        } else {
            return array();
        }
    }

    private function isBodyRequiredHeadersExist(array $headers) : bool {
        return \array_key_exists(HttpDecoder::KEY_CONTENT_TYPE, $headers) &&
            \array_key_exists(HttpDecoder::KEY_CONTENT_LENGTH, $headers);
    }

    private function parseBody(int $length) : array {
        try {
            $raw = \file_get_contents($this->inputSrc, false, NULL, 0, $length);
            return \json_decode($raw, true);
        } catch (\Throwable $err) {
            throw new DecodeBodyException('Unable to process request body', $err);
        }
    }
}