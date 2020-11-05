<?php declare(strict_types=1);

namespace Boneng\Model;

use PHPUnit\Framework\TestCase;

final class ResultTest extends TestCase {
    private const STATUS = 13;
    private const DATA = [ 'key1' => 'value #1', 'key2' => 'value #2' ];

    private $result;

    public function setup() : void {
        $this->result = new Result(ResultTest::STATUS, ResultTest::DATA);
    }

    public function testGetStatusShouldReturnSpecifiedStatus() {
        $this->assertEquals(ResultTest::STATUS, $this->result->getStatus());
    }

    public function testGetDataShouldReturnSpecifiedData() {
        $value = $this->result->getData();

        $this->assertTrue($this->isArraysEquals(ResultTest::DATA, $value));
    }

    private function isArraysEquals(array $array1, array $array2) : bool {
        return (!array_diff($array1, $array2) && !array_diff($array2, $array1));
    }
}