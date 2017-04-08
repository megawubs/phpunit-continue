<?php


namespace Wubs\PHPUnitContinue;


use PHPUnit\TextUI\Command;

/**
 * Class ContinueCommand
 *
 * @package Wubs\PHPUnitContinue
 */
class ContinueCommand extends Command
{

    /**
     * @var \Wubs\PHPUnitContinue\Config
     */
    private $config;

    /**
     * ContinueCommand constructor.
     */
    function __construct()
    {
        $this->longOptions['continue'] = 'continueHandler';
        $this->config = new Config();
    }

    /**
     *
     */
    protected function continueHandler()
    {
        $content = file_get_contents($this->arguments['configuration']);
        $xml = new \SimpleXMLElement($content);
        $file = (string)$xml->xpath('/phpunit/listeners/listener[@class="Wubs\PHPUnitContinue\Listener"]/arguments/string')[0];

        if (file_exists($file))
        {
            $this->config->read($file);
        }

        $this->config['continue'] = true;
        $this->config->write($file);
    }

    /**
     * @param bool $exit
     *
     * @return mixed
     */
    public static function main($exit = true)
    {
        $command = new static;

        return $command->run($_SERVER['argv'], $exit);
    }
}