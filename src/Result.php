<?php declare(strict_types=1);

namespace Boneng;

class Result {
    private $status;
    private $data;

    public function __construct(int $status, array $data = array()) {
        $this->status = $status;
        $this->data = $data;
    }

    public function getStatus() : int {
        return $this->status;
    }

    public function getData() : array {
        return $this->data;
    }
}