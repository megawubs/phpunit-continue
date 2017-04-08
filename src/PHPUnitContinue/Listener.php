<?php


namespace Wubs\PHPUnitContinue;


use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use PHPUnit\Util\Printer;

/**
 * Class Listener
 *
 * @package Wubs\PHPUnitContinue
 */
class Listener implements TestListener
{

    /**
     * @var bool
     */
    private $hasError = false;

    /**
     * @var \Wubs\PHPUnitContinue\Config
     */
    private $config;
    /**
     * @var
     */
    private $path;
    /**
     * @var
     */
    private $skippedTests = [];
    /**
     * @var \PHPUnit\Util\Printer
     */
    private $printer;

    /**
     * Listener constructor.
     *
     * @param                              $file
     * @param \Wubs\PHPUnitContinue\Config $schema
     * @param \PHPUnit\Util\Printer        $printer
     */
    function __construct($file, Config $schema, Printer $printer)
    {
        $this->config = $schema;
        if (file_exists($file))
        {
            $this->config->read($file);
        }

        $this->path = $file;
        $this->printer = $printer;
    }

    /**
     * An error occurred.
     *
     * @param Test       $test
     * @param \Exception $e
     * @param float      $time
     */
    public function addError(Test $test, \Exception $e, $time)
    {
        $this->register($test);
    }

    /**
     * A warning occurred.
     *
     * @param Test    $test
     * @param Warning $e
     * @param float   $time
     */
    public function addWarning(Test $test, Warning $e, $time)
    {
        $this->register($test);
    }

    /**
     * A failure occurred.
     *
     * @param Test                 $test
     * @param AssertionFailedError $e
     * @param float                $time
     */
    public function addFailure(Test $test, AssertionFailedError $e, $time)
    {
        $this->register($test);
    }

    /**
     * Incomplete test.
     *
     * @param Test       $test
     * @param \Exception $e
     * @param float      $time
     */
    public function addIncompleteTest(Test $test, \Exception $e, $time)
    {
        //skipping
    }

    /**
     * Risky test.
     *
     * @param Test       $test
     * @param \Exception $e
     * @param float      $time
     */
    public function addRiskyTest(Test $test, \Exception $e, $time)
    {
        //skipping
    }

    /**
     * Skipped test.
     *
     * @param Test       $test
     * @param \Exception $e
     * @param float      $time
     */
    public function addSkippedTest(Test $test, \Exception $e, $time)
    {
        //skipping
    }

    /**
     * A test suite started.
     *
     * @param TestSuite $suite
     */
    public function startTestSuite(TestSuite $suite)
    {
        if ($this->config['continue'] == false || $this->config['has_error'] == false)
        {
            return;
        }

        $foundIt = false;
        $testsToRun = [];
        $this->skippedTests = [];

        /** @var TestCase $test */
        foreach ($suite->tests() as $test)
        {
            if (get_class($test) === $this->config['class'] && $test->getName() == $this->config['method'])
            {
                $foundIt = true;
            }
            if ($foundIt == true)
            {
                $testsToRun[] = $test;
            } else
            {
                $this->skippedTests[] = $test;
            }
        }

        $suite->setTests($testsToRun);

        $this->printer->write("Skipping " . count($this->skippedTests) . " tests in order to continue" . PHP_EOL);
    }

    /**
     * A test suite ended.
     *
     * @param TestSuite $suite
     */
    public function endTestSuite(TestSuite $suite)
    {
        if ($this->config['continue'])
        {
            $this->config['continue'] = false;
        }
        if ($this->hasError == false)
        {
            $this->config['has_error'] = false;
            $this->config['class'] = '';
            $this->config['method'] = '';
        }
    }

    /**
     * A test started.
     *
     * @param Test $test
     */
    public function startTest(Test $test)
    {

    }

    /**
     * A test ended.
     *
     * @param Test  $test
     * @param float $time
     */
    public function endTest(Test $test, $time)
    {

    }


    /**
     * @param \PHPUnit\Framework\Test $test
     */
    private function register(Test $test)
    {
        if ($test instanceof TestCase)
        {
            $this->hasError = true;
            $this->config['has_error'] = true;
            $this->config['class'] = get_class($test);
            $this->config['method'] = $test->getName();
        }
    }
}