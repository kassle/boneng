<?php declare(strict_types=1);

namespace Boneng;

use Boneng\Model\Request;

interface Handler {
    public function getPath() : string;
    public function getMethod() : string;
    public function validate(Request $request) : bool;
    public function run(Request $request) : Result;
}