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
     * This needs to be run first before everything
     * so the entire test would complete without failures.
     * todo: investigate
     */
    public function testEmpty1()
    {
        $this->Front->loopFromFlush();
    }

    /**
     * @group test1
     * @runTestInSeparateProcess
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
     * @runTestInSeparateProcess
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
     * @group test3
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
    public function testUsingLoopFromStartWithLength2()
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

    /**
     * @group test11
     */
    public function testUsingLoopFromStartWithFrom2ndEndAndDoubleLoopUse()
    {
        $this->assertOutputString("int(2)", "int(9)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('2nd from start', 1)->pr($x);
        }
        $this->Front->loopFromFlush();

        for ($i = 6; $i <= 10; $i++) {
            $this->Front->loopFrom('2nd from last', 1)->pr($i);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test12
     */
    public function testUsingLoopFromMiddleWithAfter2nd()
    {
        $this->assertOutputString("int(5)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('2 after middle', 1)->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test13
     */
    public function testUsingLoopFromMiddleWithBefore2nd()
    {
        $this->assertOutputString("int(1)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('2 before middle', 1)->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test14
     */
    public function testUsingLoopFromMiddleWithBeforeOutofBounds()
    {
        $this->expectOutputString("");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('3 before middle', 1)->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test15
     */
    public function testUsingLoopFromMiddleWithAfterOutofBounds()
    {
        $this->expectOutputString("");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('3 after middle', 1)->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test16
     */
    public function testUsingLoopFromMiddleWithBefore1stOnEvenLoopCount()
    {
        $this->assertOutputString("int(4)");
        for ($x = 1; $x <= 10; $x++) {
            $this->Front->loopFrom('1 before middle', 1)->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test17
     */
    public function testUsingLoopFromMiddleWithAfter2ndOnEvenLoopCount()
    {
        $this->assertOutputString("int(8)");
        for ($x = 1; $x <= 10; $x++) {
            $this->Front->loopFrom('3 after middle', 1)->pr($x);
        }
        $this->Front->loopFromFlush();
    }

    /**
     * @group test18
     * @group x
     */
    public function testUsingLoopFromWithNestedEndLoop()
    {
        $this->assertOutputString("int(2)", "int(7)");
        $f = 0;
        for ($x = 1; $x <= 2; $x++) {
            for ($i = 1; $i <= 5; $i++) {
                ++$f;
                $this->Front->loopFrom('1 before center', 1)->pr($f);
            }
            $this->Front->loopFromNestedEnd();
        }
        $this->Front->loopFromFlush();
    }


    /**
     * @group test19
     */
    public function testLoopFromWillWorkWithOtherNonChainingConditionals()
    {
        $this->assertOutputString('int(3)', 'int(3)');
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('middle', 1)->pr($x);
            $this->Front->condBoolean($x == 3)->pr($x);
        }
        $this->Front->loopFromFlush();
    }
}
