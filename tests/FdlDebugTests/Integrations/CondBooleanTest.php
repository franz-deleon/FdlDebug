<?php
namespace FdlDebugTests\Integrations;

use FdlDebug;

/**
 * Front test case.
 */
class CondBooleanTest extends AbstractIntegrationsTestCase
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
        $this->Front = FdlDebug\Front::i();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->Front = null;
        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct()
    {
    }

    /**
     * @group test1
     */
    public function testLoopCondBoolean()
    {
        $this->assertOutputString("int(2)", "int(4)");
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->condBoolean($x % 2 === 0)->pr($x);
        }
    }

    /**
     * @group test2
     */
    public function testLoopCondBooleanReturnsNothingOnFalse()
    {
        $this->expectOutputString("");
        $this->Front->condBoolean(1 === 2)->pr("hello world");
    }

    /**
     * @group test3
     */
    public function testLoopCondBooleanOnNewLine()
    {
        $this->assertOutputString('string(11) "hello world"');
        $this->Front
            ->condBoolean(true)
            ->pr("hello world");
    }

    /**
     * @group test3
     */
    public function testLoopCondBooleanIntegratesWithOtherConditionals()
    {
        $this->assertOutputString('int(3)');
        for ($x = 1; $x <= 5; $x++) {
            $this->Front->condBoolean($x === 3)->loopRange(3, 1)->pr($x);
        }
    }
}
