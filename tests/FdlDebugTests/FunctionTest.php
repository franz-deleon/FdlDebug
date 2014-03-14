<?php
namespace FdlDebugTests;

use FdlDebug\Bootstrap;

/**
 *  test case.
 */
class FunctionTest extends Integrations\AbstractIntegrationsTestCase
{
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @group test1
     */
    public function testPr()
    {
        $this->assertOutputString('string(12) "hello world!"');
        pr("hello world!");
    }

    /**
     * @group test2
     */
    public function testPrGlobal()
    {
        pr_global();
        $this->expectOutputRegex("~SERVER~");
    }

    /**
     * @group test3
     */
    public function testPrBacktrace()
    {
        pr_backtrace();
        $this->expectOutputRegex("~END OF TRACE~");
    }

    /**
     * @group test4
     */
    public function testPrFiles()
    {
        pr_files();
        $this->expectOutputRegex("~END OF TRACE~");
    }

    /**
     * @group test5
     */
    public function testPrTraceVar()
    {
        // enable the xdebug tracing
        $_GET['XDEBUG_TRACE'] = 1;

        // overwrite the trace dir
        $config =& Bootstrap::getConfigs();
        $config['xdebug']['trace_output_dir'] = __DIR__ . '/Assets';
        $config['xdebug_tracing_enabled'] = true;

        prx_trace_var('cherylx');
        $this->expectOutputRegex("~var\([$]cherylx\) assignment~");
    }

    /**
     * @group test6
     */
    public function testCondBool()
    {
        $this->assertOutputString("int(2)", "int(4)");
        for ($x = 1; $x <= 5; $x++) {
            cond_bool($x % 2 === 0)->pr($x);
        }
    }

    /**
     * @group test7
     */
    public function testCondRange()
    {
        $this->assertOutputString("int(3)", "int(4)", "int(5)");
        for ($x = 1; $x <= 5; ++$x) {
            cond_range(3)->pr_now($x);
        }
    }

    /**
     * @group test8
     */
    public function testCondFrom()
    {
        $this->assertOutputString("int(3)");
        for ($x = 1; $x <= 5; ++$x) {
            cond_from("center", 1)->pr($x);
        }
        cond_from_flush();
    }

    /**
     * @group test9
     */
    public function testMultipleCondFunctionChaining()
    {
        $this->assertOutputString("int(5)");
        for ($x = 1; $x <= 10; ++$x) {
            $z = cond_bool($x === 5)
                ->cond_range(4, 4)
                ->cond_from("center")
                ->pr_now($x);
        }
        cond_from_flush();
    }
}

