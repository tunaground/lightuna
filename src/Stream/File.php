<?php

namespace Lightuna\Stream;

class File implements StreamInterface
{
    private string $filePath;
    private string $mode;

    public function __construct(string $filePath, string $mode = "a+")
    {
        $this->filePath = $filePath;
        $this->mode = $mode;
    }

    public function write(string $message)
    {
        $fp = fopen($this->filePath, $this->mode);
        fwrite($fp, $message . PHP_EOL);
        fclose($fp);
    }
}
<<<<<<< HEAD
=======

>>>>>>> develop2
