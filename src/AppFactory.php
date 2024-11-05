<?php declare(strict_types=1);

namespace Boneng;

use Boneng\Codex\Decoder;
use Boneng\Codex\HttpDecoder;
use Boneng\Processor\JsonRenderer;
use Boneng\Processor\Renderer;
use Boneng\Processor\SystemLogger;
use Psr\Log\LoggerInterface;


class AppFactory {
    private function __construct() { }

    public static function create(string $appName, Renderer $htmlRenderer, Decoder $decoder = new HttpDecoder()) : App {
        return new AppImpl($decoder, $htmlRenderer, new JsonRenderer(), new SystemLogger($appName));
    }
}