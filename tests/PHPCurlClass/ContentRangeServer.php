<?php

namespace ContentRangeServer;

use RangeHeader\RangeHeader;

class ContentRangeServer
{
    public function serve($path)
    {
        $range = new RangeHeader($_SERVER['HTTP_RANGE']);

        $filesize = filesize($path);
        $fp = fopen($path, 'r');

        if (!isset($_SERVER['HTTP_RANGE'])) {
            header('HTTP/1.1 200 OK');
            header('Content-Length: ' . $filesize);
            header('Accept-Ranges: bytes');
            fpassthru($fp);
        } else {
            header('HTTP/1.1 206 Partial Content');
            header('Content-Length: ' . $range->getLength($filesize));
            header('Content-Range: ' . $range->getContentRangeHeader($filesize));

            $start = $range->getFirstBytePosition($filesize);
            if ($start > 0) {
                fseek($fp, $start, SEEK_SET);
            }

            $length = $range->getLength($filesize);
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
