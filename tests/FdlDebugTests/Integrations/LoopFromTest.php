<?php
namespace FdlDebugTests\Integrations;

use FdlDebug;

/**
 * Front test case.
 */
class LoopFromTest extends AbstractIntegrationsTestCase
{
    /**
     * @var Front
     */
    private $Front;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        // TODO Auto-generated FrontTest::setUp()
        $this->Front = FdlDebug\Front::i();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated FrontTest::tearDown()
        $this->Front = null;
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
        // TODO Auto-generated constructor
    }

    /**
     * @group test1
     */
    public function testLoopFromUsingFromEnd()
    {
        $this->assertOutputString("int(5)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('end')->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test2
     */
    public function testLoopFromUsingFromEndWithExpression1stFrom()
    {
        $this->assertOutputString("int(5)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('1st from end')->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test2a
     */
    public function testLoopFromUsingFromEndWithExpression2ndFrom()
    {
        $this->assertOutputString("int(4)", "int(5)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('2nd from end')->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test2b
     */
    public function testLoopFromUsingFromEndWithExpression3rdFrom()
    {
        $this->assertOutputString("int(3)", "int(4)", "int(5)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('3rd from end')->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test2c
     */
    public function testLoopFromUsingFromEndWithExpression4thFrom()
    {
        $this->assertOutputString("int(2)", "int(3)", "int(4)", "int(5)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('4th from end')->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test3
     */
    public function testLoopFromUsingFromStart()
    {
        $this->assertOutputString("int(1)", "int(2)", "int(3)", "int(4)", "int(5)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('start')->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test3a
     */
    public function testLoopFromUsingFromStartWithExpression1stFrom()
    {
        $this->assertOutputString("int(1)", "int(2)", "int(3)", "int(4)", "int(5)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('1st from start')->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test3b
     */
    public function testLoopFromUsingFromStartWithExpression2ndFrom()
    {
        $this->assertOutputString("int(2)", "int(3)", "int(4)", "int(5)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('2nd from start')->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test3c
     */
    public function testLoopFromUsingFromStartWithExpression3rdFrom()
    {
        $this->assertOutputString("int(3)", "int(4)", "int(5)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('3rd from start')->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test3d
     */
    public function testLoopFromUsingFromStartWithExpression4thFrom()
    {
        $this->assertOutputString("int(4)", "int(5)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('4th from start')->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test4
     */
    public function testUsingMultipleLoopFromCalls()
    {
        $this->assertOutputString("int(5)", "int(1)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('end', 1)->pr($x);
            $this->Front->loopFrom('start', 1)->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test5
     */
    public function testUsingLoopFromEndWhereOffsetIsOutOfBounds()
    {
        $this->expectOutputString("");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('6th from end')->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test6
     */
    public function testUsingLoopFromStartWhereOffsetIsOutOfBounds()
    {
        $this->expectOutputString("");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('6th from start')->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test7
     */
    public function testUsingLoopFromStartWithLegth2()
    {
        $this->assertOutputString("int(2)", "int(3)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('2nd from start', 2)->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test8
     */
    public function testUsingLoopFromWithBooleanCond()
    {
        $this->assertOutputString("int(2)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->condBoolean($x === 2)->loopFrom('2nd from start', 2)->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test9
     */
    public function testUsingLoopFromWithBooleanCondAndLoopRangeOnSeparateLines()
    {
        $this->assertOutputString("int(2)");
        for ($x = 1; $x <= 5; $x++) {
            $this
                ->Front
                ->condBoolean($x === 2) // print on count 2
                ->loopRange($x % 2 === 0) // print if its an even number
                ->loopFrom('2nd from start', 2) // print from 2nd of loop start
                ->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test10
     */
    public function testUsingLoopFromWithNestedLoops()
    {
        $this->assertOutputString('string(14) "3rd-from-end:3"', 'string(16) "2nd-from-start:2"');
        for ($i = 1; $i <= 5; $i++) {
            $this->Front->loopFrom('3rd from end', 1)->pr("3rd-from-end:" . $i);
            for ($x = 1; $x <= 5; $x++) {
                $this->Front->loopFrom('2nd from start', 1)->pr("2nd-from-start:" . $x);
            }
        }
        $this->Front->loopFromFlush();
    }
}
