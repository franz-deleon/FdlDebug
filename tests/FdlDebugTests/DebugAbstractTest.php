<?php
namespace FdlDebugTest;

/**
 * DebugAbstract test case.
 */
class DebugAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DebugAbstract
     */
    private $DebugAbstract;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->DebugAbstract = $this->getMockForAbstractClass('FdlDebug\DebugAbstract');
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        // TODO Auto-generated DebugAbstractTest::tearDown()
        $this->DebugAbstract = null;
        parent::tearDown();
    }

    /**
     * Tests DebugAbstract->getWriter()
     */
    public function testGetWriter()
    {
        // TODO Auto-generated DebugAbstractTest->testGetWriter()
        $this->markTestIncomplete("getWriter test not implemented");
        $this->DebugAbstract->getWriter(/* parameters */);
    }

    /**
     * Tests DebugAbstract->setWriter()
     */
    public function testSetWriter()
    {
        // TODO Auto-generated DebugAbstractTest->testSetWriter()
        $this->markTestIncomplete("setWriter test not implemented");
        $this->DebugAbstract->setWriter(/* parameters */);
    }

    /**
     * Tests DebugAbstract->getBackTrace()
     */
    public function testGetBackTrace()
    {
        // TODO Auto-generated DebugAbstractTest->testGetBackTrace()
        $this->markTestIncomplete("getBackTrace test not implemented");
        $this->DebugAbstract->getBackTrace(/* parameters */);
    }

    /**
     * Tests DebugAbstract->getFileTrace()
     */
    public function testGetFileTrace()
    {
        // TODO Auto-generated DebugAbstractTest->testGetFileTrace()
        $this->markTestIncomplete("getFileTrace test not implemented");
        $this->DebugAbstract->getFileTrace(/* parameters */);
    }

    /**
     * Tests DebugAbstract->findTraceKeyAndSlice()
     * @group find-trace-key
     * @group find-trace-key-1
     */
    public function testFindTraceKeyAndSlice()
    {
        $trace = array(
            array(
                'file' => 'file1.php',
                'line' => '101',
                'function' => 'someFunc1',
                'class' => 'someClass1',
                'type' => '::',
            ),
            array(
                'file' => 'file2.php',
                'line' => '102',
                'function' => 'someFunc2',
                'class' => 'someClass2',
                'type' => '::',
            ),
            array(
                'file' => 'file3.php',
                'line' => '103',
                'function' => 'someFunc3',
                'class' => 'someClass3',
                'type' => '::',
            ),
        );
        $r = $this->DebugAbstract->findTraceKeyAndSlice($trace, 'line', '102');
        $expected = array(
            array(
                'file' => 'file2.php',
                'line' => '102',
                'function' => 'someFunc2',
                'class' => 'someClass2',
                'type' => '::',
            ),
            array(
                'file' => 'file3.php',
                'line' => '103',
                'function' => 'someFunc3',
                'class' => 'someClass3',
                'type' => '::',
            ),
        );
        $this->assertEquals($expected, $r);
    }

    /**
     * @group find-trace-key
     * @group find-trace-key-2
     */
    public function testFindTraceKeyAndSliceWithSearchValueOffset()
    {
        $trace = array(
            array(
                'file' => 'file1.php',
                'line' => '101',
                'function' => 'someFunc1',
                'class' => 'someClass1',
                'type' => '::',
            ),
            array(
                'file' => 'file2.php',
                'line' => '102',
                'function' => 'someFunc2',
                'class' => 'someClass2',
                'type' => '::',
            ),
            array(
                'file' => 'file3.php',
                'line' => '103',
                'function' => 'someFunc3',
                'class' => 'someClass3',
                'type' => '::',
            ),
            array(
                'file' => 'file4.php',
                'line' => '104',
                'function' => 'someFunc4',
                'class' => 'someClass4',
                'type' => '::',
            ),
            array(
                'file' => 'file5.php',
                'line' => '105',
                'function' => 'someFunc5',
                'class' => 'someClass5',
                'type' => '::',
            ),
        );
        $r = $this->DebugAbstract->findTraceKeyAndSlice($trace, 'line', '102', 3);

        $expected = array(
            array(
                'file' => 'file5.php',
                'line' => '105',
                'function' => 'someFunc5',
                'class' => 'someClass5',
                'type' => '::',
            ),
        );
       $this->assertEquals($expected, $r);
    }

    /**
     * @group find-trace-key
     * @group find-trace-key-3
     */
    public function testFindTraceKeyAndSliceWithSearchValueOffsetAndStartOffset()
    {
        $trace = array(
            array(
                'file' => 'file1.php',
                'line' => '101',
                'function' => 'someFunc1',
                'class' => 'someClass1',
                'type' => '::',
            ),
            array(
                'file' => 'file2.php',
                'line' => '102',
                'function' => 'someFunc2',
                'class' => 'someClass2',
                'type' => '::',
            ),
            array(
                'file' => 'file3.php',
                'line' => '102',
                'function' => 'someFunc3',
                'class' => 'someClass3',
                'type' => '::',
            ),
            array(
                'file' => 'file4.php',
                'line' => '102',
                'function' => 'someFunc4',
                'class' => 'someClass4',
                'type' => '::',
            ),
            array(
                'file' => 'file5.php',
                'line' => '105',
                'function' => 'someFunc5',
                'class' => 'someClass5',
                'type' => '::',
            ),
        );
        $r = $this->DebugAbstract->findTraceKeyAndSlice($trace, 'line', '102', 1, 2);

        $expected = array(
            array(
                'file' => 'file4.php',
                'line' => '102',
                'function' => 'someFunc4',
                'class' => 'someClass4',
                'type' => '::',
            ),
            array(
                'file' => 'file5.php',
                'line' => '105',
                'function' => 'someFunc5',
                'class' => 'someClass5',
                'type' => '::',
            ),
        );
       $this->assertEquals($expected, $r);
    }

    /**
     * @group find-trace-key
     * @group find-trace-key-4
     */
    public function testFindTraceKeyAndSliceWithStartFromEnd()
    {
        $trace = array(
            array(
                'file' => 'file1.php',
                'line' => '101',
                'function' => 'someFunc1',
                'class' => 'someClass1',
                'type' => '::',
            ),
            array(
                'file' => 'file2.php',
                'line' => '102',
                'function' => 'someFunc2',
                'class' => 'someClass2',
                'type' => '::',
            ),
            array(
                'file' => 'file3.php',
                'line' => '102',
                'function' => 'someFunc3',
                'class' => 'someClass3',
                'type' => '::',
            ),
            array(
                'file' => 'file4.php',
                'line' => '102',
                'function' => 'someFunc4',
                'class' => 'someClass4',
                'type' => '::',
            ),
            array(
                'file' => 'file5.php',
                'line' => '105',
                'function' => 'someFunc5',
                'class' => 'someClass5',
                'type' => '::',
            ),
        );

        $r = $this->DebugAbstract->findTraceKeyAndSlice($trace, 'line', '102', 0, 0, true);
        $expected = array(
            array(
                'file' => 'file4.php',
                'line' => '102',
                'function' => 'someFunc4',
                'class' => 'someClass4',
                'type' => '::',
            ),
            array(
                'file' => 'file5.php',
                'line' => '105',
                'function' => 'someFunc5',
                'class' => 'someClass5',
                'type' => '::',
            ),
        );
        $this->assertEquals($expected, $r);
    }

    /**
     * @group find-trace-key
     * @group find-trace-key-5
     */
    public function testFindTraceKeyAndSliceWithStartFromEndWithSearchedValueOffset()
    {
        $trace = array(
            array(
                'file' => 'file1.php',
                'line' => '101',
                'function' => 'someFunc1',
                'class' => 'someClass1',
                'type' => '::',
            ),
            array(
                'file' => 'file2.php',
                'line' => '102',
                'function' => 'someFunc2',
                'class' => 'someClass2',
                'type' => '::',
            ),
            array(
                'file' => 'file3.php',
                'line' => '102',
                'function' => 'someFunc3',
                'class' => 'someClass3',
                'type' => '::',
            ),
            array(
                'file' => 'file4.php',
                'line' => '102',
                'function' => 'someFunc4',
                'class' => 'someClass4',
                'type' => '::',
            ),
            array(
                'file' => 'file5.php',
                'line' => '105',
                'function' => 'someFunc5',
                'class' => 'someClass5',
                'type' => '::',
            ),
        );

        $r = $this->DebugAbstract->findTraceKeyAndSlice($trace, 'line', '102', 1, 0, true);
        $expected = array(
            array(
                'file' => 'file5.php',
                'line' => '105',
                'function' => 'someFunc5',
                'class' => 'someClass5',
                'type' => '::',
            ),
        );
        $this->assertEquals($expected, $r);
    }
}

