<?php
namespace Lightuna\Util;

use Lightuna\Log\Logger;

class ExceptionHandler
{
    private $config;
    private $logger;

    public function __construct(array $config, Logger $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    public function global(\Throwable $e)
    {
        $this->handle($e);
    }

    public function handle(\Throwable $e)
    {
        $this->logger->debug(
            '{file}: Exception occur: {code}: {msg}',
            [
                'file' => $e->getFile(),
                'msg' => $e->getMessage()
            ]
        );
        if ($this->config['site']['environment'] === 'dev') {
            $this->printError($e);
            die();
        } else {
            Redirection::temporary(
                "{$this->config['site']['baseUrl']}/error.php?msg={$e->getMessage()}"
            );
        }
    }

    private function printError(\Throwable $e)
    {
        echo $e->getMessage();
        echo '<br/>';
        echo '<br/>';
        echo $e->getTraceAsString();
    }
}