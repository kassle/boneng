<?php declare(strict_types=1);

namespace Boneng\Codex;

use Boneng\Model\Request;

interface Decoder {
    public function decode() : Request;
}