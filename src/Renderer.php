<?php declare(strict_types=1);

namespace Boneng;

interface Renderer {
    public function render(Result $result) : Response;
}