<?php declare(strict_types=1);

namespace Boneng;

use Boneng\Model\Response;
use Boneng\Model\Result;

interface Renderer {
    public function render(Result $result) : Response;
}