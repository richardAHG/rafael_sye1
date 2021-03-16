<?php

class Log
{
    private $path = 'log';
    private $filename = 'log.txt';
    private $separator = "|";
    private $break = "\n";

    public function __construct($type, $message)
    {
        $filepath = dirname(__DIR__).DIRECTORY_SEPARATOR.$this->path.DIRECTORY_SEPARATOR.$this->filename;
        file_put_contents(
            $filepath,
            $this->template($type, $message)
        );
    }

    private function template($type, $message)
    {
        return date("Y-m-d H:i:s").$this->separator
        .$type.$this->separator
        .$message
        .$this->break;
    }
}