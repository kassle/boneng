<?php declare(strict_types=1);

namespace Boneng;

final class Result {
    private $status;

    public function __construct(int $status) {
        $this->status = $status;
    }

    public function getStatus() : int {
        return $this->status;
    }
}