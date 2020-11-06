<?php declare(strict_types=1);

namespace Boneng\Processor;

use PHPUnit\Framework\TestCase;

class SystemLoggerTest extends TestCase {
    private const TAG = 'SysLogTest';
    private $logger;

    protected function setup() : void {
        $this->logger = new SystemLogger(SystemLoggerTest::TAG, true);
    }

    public function testEmergencyShouldPrintOut() {
        $msg = 'test emergency log';
        $this->expectOutputString(SystemLoggerTest::TAG . ': ' . $msg);

        $this->logger->emergency($msg);
    }

    public function testAlertShouldPrintOut() {
        $msg = 'test alert log';
        $this->expectOutputString(SystemLoggerTest::TAG . ': ' . $msg);

        $this->logger->alert($msg);
    }

    public function testCriticalShouldPrintOut() {
        $msg = 'test critical log';
        $this->expectOutputString(SystemLoggerTest::TAG . ': ' . $msg);

        $this->logger->critical($msg);
    }
    
    public function testErrorShouldPrintOut() {
        $msg = 'test error log';
        $this->expectOutputString(SystemLoggerTest::TAG . ': ' . $msg);

        $this->logger->error($msg);
    }

    public function testWarningShouldPrintOut() {
        $msg = 'test warning log';
        $this->expectOutputString(SystemLoggerTest::TAG . ': ' . $msg);

        $this->logger->warning($msg);
    }

    public function testNoticeShouldPrintOut() {
        $msg = 'test notice log';
        $this->expectOutputString(SystemLoggerTest::TAG . ': ' . $msg);

        $this->logger->notice($msg);
    }

    public function testInfoShouldPrintOut() {
        $msg = 'test info log';
        $this->expectOutputString(SystemLoggerTest::TAG . ': ' . $msg);

        $this->logger->info($msg);
    }

    public function testDebugShouldPrintOut() {
        $msg = 'test debug log';
        $this->expectOutputString(SystemLoggerTest::TAG . ': ' . $msg);

        $this->logger->debug($msg);
    }
}