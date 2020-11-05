<?php declare(strict_types=1);

namespace Boneng\Exception;

class DecoderException extends \Exception {
    public function __construct(string $msg, \Throwable $cause = NULL) {
        parent::__construct($msg, 0, $cause);
    }
}