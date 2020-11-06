<?php declare(strict_types=1);

namespace Boneng;

use Boneng\Processor\Handler;

interface App {
    public function addHandler(Handler $handler) : void;
    public function run() : void;
}