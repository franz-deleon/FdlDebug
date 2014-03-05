<?php
namespace FdlDebugTests;

use FdlDebug;

/**
 * Front test case.
 */
class LoopFromTest extends \PHPUnit_Framework_TestCase
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
        $this->expectOutputString($this->formatOutput("int(5)"));
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('end')->pr($x);
        }
        $this->Front->loopFromEnd();
    }

    /**
     * @group test2
     */
    public function testLoopFromUsingFromEndWithExpression1stFrom()
    {
        $this->expectOutputString($this->formatOutput("int(5)"));
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('1st from end')->pr($x);
        }
        $this->Front->loopFromEnd();
    }

    /**
     * @group test2a
     */
    public function testLoopFromUsingFromEndWithExpression2ndFrom()
    {
        $this->expectOutputString($this->formatOutput("int(4)", "int(5)"));
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('2nd from end')->pr($x);
        }
        $this->Front->loopFromEnd();
    }

    /**
     * @group test2b
     */
    public function testLoopFromUsingFromEndWithExpression3rdFrom()
    {
        $this->expectOutputString($this->formatOutput("int(3)", "int(4)", "int(5)"));
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('3rd from end')->pr($x);
        }
        $this->Front->loopFromEnd();
    }

    /**
     * @group test2c
     */
    public function testLoopFromUsingFromEndWithExpression4thFrom()
    {
        $this->expectOutputString($this->formatOutput("int(2)", "int(3)", "int(4)", "int(5)"));
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('4th from end')->pr($x);
        }
        $this->Front->loopFromEnd();
    }

    /**
     * @group test3
     */
    public function testLoopFromUsingFromStart()
    {
        $this->expectOutputString($this->formatOutput("int(1)", "int(2)", "int(3)", "int(4)", "int(5)"));
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('start')->pr($x);
        }
        $this->Front->loopFromEnd();
    }

    /**
     * @group test3a
     */
    public function testLoopFromUsingFromStartWithExpression1stFrom()
    {
        $this->expectOutputString($this->formatOutput("int(1)", "int(2)", "int(3)", "int(4)", "int(5)"));
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('1st from start')->pr($x);
        }
        $this->Front->loopFromEnd();
    }

    /**
     * @group test3b
     */
    public function testLoopFromUsingFromStartWithExpression2ndFrom()
    {
        $this->expectOutputString($this->formatOutput("int(2)", "int(3)", "int(4)", "int(5)"));
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('2nd from start')->pr($x);
        }
        $this->Front->loopFromEnd();
    }

    /**
     * @group test3c
     */
    public function testLoopFromUsingFromStartWithExpression3rdFrom()
    {
        $this->expectOutputString($this->formatOutput("int(3)", "int(4)", "int(5)"));
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('3rd from start')->pr($x);
        }
        $this->Front->loopFromEnd();
    }

    /**
     * @group test3d
     */
    public function testLoopFromUsingFromStartWithExpression4thFrom()
    {
        $this->expectOutputString($this->formatOutput("int(4)", "int(5)"));
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('4th from start')->pr($x);
        }
        $this->Front->loopFromEnd();
    }

    /**
     * @group test4
     */
    public function testUsingDoubleLoopFromCalls()
    {
        $this->expectOutputString($this->formatOutput("int(5)", "int(1)"));
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('end', 1)->pr($x);
            $this->Front->loopFrom('start', 1)->pr($x);
        }
        $this->Front->loopFromEnd();
        $this->Front->loopFromEnd();
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
        $this->Front->loopFromEnd();
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
        $this->Front->loopFromEnd();
    }

    /**
     * @group test7
     */
    public function testUsingLoopFromStartWithLegth2()
    {
        $this->expectOutputString($this->formatOutput("int(2)", "int(3)"));
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->loopFrom('2nd from start', 2)->pr($x);
        }
        $this->Front->loopFromEnd();
    }

    /**
     * @group test8
     */
    public function testUsingLoopFromWithBooleanCond()
    {
        $this->expectOutputString($this->formatOutput("int(2)"));
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->condBoolean($x === 2)->loopFrom('2nd from start', 2)->pr($x);
        }
        $this->Front->loopFromEnd();
    }

    protected function formatOutput($output)
    {
        $args = func_get_args();
        return implode("\n", $args) . "\n";
    }
}
