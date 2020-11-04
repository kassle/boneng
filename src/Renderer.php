<?php declare(strict_types=1);

namespace Boneng;

use Boneng\Model\Response;

interface Renderer {
    public function render(Result $result) : Response;
}