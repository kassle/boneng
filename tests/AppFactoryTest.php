<?php declare(strict_types=1);

namespace Boneng;

use PHPUnit\Framework\TestCase;
use Boneng\Processor\Renderer;

class AppFactoryTest extends TestCase {

    public function testCreateShouldReturnInstanceOfAppImpl() {
        $renderer = $this->createMock(Renderer::class);
        $app = AppFactory::create('boneng-testcase', $renderer);

        $this->assertInstanceOf(AppImpl::class, $app);
    }
}