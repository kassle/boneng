<?php declare(strict_types=1);

namespace Boneng;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase {
    private $app;

    protected function setUp(): void {
        $this->app = new App();
    }

    public function testRunShouldReturnOk() {
        $this->expectOutputString("hello");
        $this->app->run("hello");
    }
}

final class HandlerDummy implements Handler {
    public function getPath() : string { 
        return '/';
    }

    public function getMethod() : string {
        return 'GET';
    }

    public function validate(array $request) : bool {
        return true;
    }

    public function run(array $request) {

    }
}