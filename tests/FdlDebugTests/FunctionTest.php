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
        $this->expectOutputRegex("~END OF BACKTRACE~");
    }

    /**
     * @group test4
     */
    public function testPrFiles()
    {
        pr_files();
        $this->expectOutputRegex("~END~");
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

    /**
     * @group test10
     * @group t1
     */
    public function testMultipleCondFunctionChainingMultiCalls()
    {
        $this->assertOutputString("int(5)", "int(4)");
        for ($x = 1; $x <= 10; ++$x) {
            cond_range(4, 4)
                ->cond_from("center")
                ->cond_bool($x === 5)
                ->pr_now($x);

            cond_range(4, 4)
                ->cond_from("1 before center", 1)
                ->cond_bool($x === 4)
                ->pr_now($x);
        }
        cond_from_flush();
    }

    /**
     * @group t1
     * @group test13
     */
    public function testMultipleNestedCondFrom()
    {
        $this->assertOutputString(
            'string(4) "a1:2"',
            'string(7) "a:1 b:2"',
            'string(7) "a:2 b:2"',
            'string(7) "a:3 b:2"'
        );

        for ($a = 1; $a <= 3; $a++) {
            cond_from("middle", 1)->pr('a1:' . $a);
            for ($b = 1; $b <= 3; $b++) {
                cond_from("middle", 1)->pr('a:' . $a . ' b:' . $b);
            }
            cond_from_nested_end();
        }
        cond_from_flush();
    }

    /**
     * @group test11
     */
    public function testMultipleCondFunctionChainingMultiCallsWith3NestedLoops()
    {
        $this->assertOutputString(
            'string(5) "1st:1"',
            'string(5) "3rd:3"',
            'string(5) "2nd:2"',
            'string(5) "3rd:3"',
            'string(5) "3rd:3"',
            'string(5) "2nd:2"',
            'string(5) "3rd:3"'
        );

        for ($i = 1; $i <= 2; $i++) {
            cond_range(1, 1)->pr("1st:" . $i);
            for ($x = 1; $x <= 2; $x++) {
                cond_range(2, 1)->pr("2nd:" . $x);
                for ($y = 1; $y <= 3; $y++) {
                    cond_range(3, 1)->pr("3rd:" . $y);
                }
                cond_range_nested_end();
            }
            cond_range_nested_end();
        }
    }

    /**
     * @group test12
     */
    public function testMultipleCondFunctionChainingMultiCallsWith2NestedLoops()
    {
        $this->assertOutputString(
            'string(5) "2nd:3"',
            'string(5) "1st:2"',
            'string(5) "2nd:3"'
        );

        for ($x = 1; $x <= 2; $x++) {
            cond_range(2, 1)->pr("1st:" . $x);
            for ($y = 1; $y <= 3; $y++) {
                cond_range(3, 1)->pr("2nd:" . $y);
            }
            cond_range_nested_end();
        }
    }

}
