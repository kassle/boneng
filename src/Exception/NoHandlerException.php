<?php declare(strict_types=1);

namespace Boneng\Exception;

class NoHandlerException extends \Exception {
    private const MESSAGE_PREFIX = 'No Handler for method: ';
    private const MESSAGE_SUFFIX = '; path: ';

    public function __construct(string $method, string $path) {
        parent::__construct(
            NoHandlerException::MESSAGE_PREFIX . $method . NoHandlerException::MESSAGE_SUFFIX . $path,
            0, NULL);
    }
}