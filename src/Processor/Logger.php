<?php declare(strict_types=1);

namespace Boneng\Processor;

interface Logger {
    public function debug(string $message, \Throwable $err = NULL);
    public function info(string $message, \Throwable $err = NULL);
    public function warn(string $message, \Throwable $err = NULL);
    public function error(string $message, \Throwable $err = NULL);
}