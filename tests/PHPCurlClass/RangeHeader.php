<?php

namespace RangeHeader;

class RangeHeader
{
    private $first_byte;
    private $last_byte;

    public function __construct($http_range_header)
    {
        // Simulate basic support for the Content-Range header.
        preg_match('/bytes=(\d+)?-(\d+)?/', $http_range_header, $matches);
        $this->first_byte = isset($matches['1']) ? (int)$matches['1'] : null;
        $this->last_byte = isset($matches['2']) ? (int)$matches['2'] : null;
    }

    public function getFirstBytePosition($file_size)
    {
        $size = (int)$file_size;

        if ($this->first_byte === null) {
            return $size - 1 - $this->last_byte;
        }

        return $this->first_byte;
    }

    public function getLastBytePosition($file_size)
    {
        $size = (int)$file_size;

        if ($this->last_byte === null) {
            return $size - 1;
        }

        return $this->last_byte;
    }

    public function getLength($file_size)
    {
        $size = (int)$file_size;

        return $this->getLastBytePosition($size) - $this->getFirstBytePosition($size) + 1;
    }

    public function getContentRangeHeader($file_size)
    {
        return
            'bytes ' . $this->getFirstBytePosition($file_size) . '-' . $this->getLastBytePosition($file_size) . '/' .
            $file_size;
    }
}
