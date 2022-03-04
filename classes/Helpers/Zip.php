<?php
class Zip
{

    /**
     * @param string $path
     */
    public function createArhive(string $path = '')
    {
        $zip = new ZipArchive;
        if ($zip->open(__DIR__ . '/../temp/' .time().'.zip', ZipArchive::CREATE) === TRUE){
            foreach (new DirectoryIterator($path) as $fileInfo) {
                if(in_array($fileInfo->getFilename(),[".",".."])) continue;
                $fileName = $fileInfo->getPathname();
                $zip->addFile($fileName);
            }
            $zip->close();
            return time().'.zip';
        }
    }

    /**
     * @param string $filename
     */
    public function downloadArhive(string $filename = '')
    {
        if (file_exists($filename)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="'.basename($filename).'"');
            header('Content-Length: ' . filesize($filename));

            flush();
            readfile($filename);
            // delete file
            unlink($filename);

        }
    }

}