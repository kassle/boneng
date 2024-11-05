<?php declare(strict_types=1);

namespace Boneng\Exception;

class DecodeHeaderException extends DecoderException {
    public function __construct(string $msg, \Throwable $cause = NULL) {
        parent::__construct($msg, $cause);
    }
}