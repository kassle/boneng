<?php declare(strict_types=1);

namespace Boneng;

use PHPUnit\Framework\TestCase;

final class ResultTest extends TestCase {
    public function testGetStatusShouldReturnSpecifiedStatus() {
        $status = 13;
        $result = new Result($status);

        $this->assertEquals($status, $result->getStatus());
    }
}