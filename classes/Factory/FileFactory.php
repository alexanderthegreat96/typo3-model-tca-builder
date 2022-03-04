<?php
class FileFactory extends Zip
{
    public function __construct()
    {
        $this->path = __DIR__ . '/../Generated/';
    }

    public function createAndDownload()
    {
        $archive = $this->createArhive($this->path);
        $this->downloadArhive(__DIR__ . '../temp/' .$archive);
    }
}