<?php declare(strict_types=1);

namespace Boneng\Exception;

class DecodeHeaderException extends DecoderException {
    public function __construct(string $msg) {
        parent::__construct($msg);
    }
}