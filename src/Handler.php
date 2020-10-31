<?php declare(strict_types=1);

namespace Boneng;

interface Handler {
    public function getPath() : string;
    public function getMethod() : string;
    public function validate(array $request) : bool;
    public function run(array $request);
}