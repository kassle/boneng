<?php declare(strict_types=1);

namespace Boneng;

use Boneng\Codex\HttpDecoder;
use Boneng\Processor\JsonRenderer;
use Boneng\Processor\Renderer;
use Boneng\Processor\SystemLogger;

class AppFactory {
    private function __construct() { }

    public static function create(string $appName, Renderer $htmlRenderer) : App {
        return new AppImpl(new HttpDecoder(), $htmlRenderer, new JsonRenderer(), new SystemLogger($appName));
    }
}