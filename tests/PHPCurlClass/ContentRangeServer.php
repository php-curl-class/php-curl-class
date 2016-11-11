<?php

namespace ContentRangeServer;

use RangeHeader\RangeHeader;

class ContentRangeServer
{
    public function serve($path)
    {
        $filesize = filesize($path);
        $fp = fopen($path, 'r');

        if (!isset($_SERVER['HTTP_RANGE'])) {
            header('HTTP/1.1 200 OK');
            header('Content-Length: ' . $filesize);
            header('Accept-Ranges: bytes');
            fpassthru($fp);
        } else {
            $range = new RangeHeader($_SERVER['HTTP_RANGE'], $path);

            if (!$range->isValid()) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header('Content-Range: ' . $range->getContentRangeHeader());
                exit;
            }

            $length = $range->getLength();

            header('HTTP/1.1 206 Partial Content');
            header('Content-Length: ' . $length);
            header('Content-Range: ' . $range->getContentRangeHeader());

            $start = $range->getFirstBytePosition();
            if ($start > 0) {
                fseek($fp, $start, SEEK_SET);
            }

            $chunk_size = 4096;
            while ($length) {
                $read = $length > $chunk_size ? $chunk_size : $length;
                $length -= $read;
                echo fread($fp, $read);
            }
        }

        fclose($fp);
    }
}
