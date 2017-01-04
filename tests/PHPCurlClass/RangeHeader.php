<?php

namespace RangeHeader;

class RangeHeader
{
    private $first_byte;
    private $last_byte;
    private $filesize;
    private $is_valid = true;

    public function __construct($http_range_header, $file_path)
    {
        // Simulate basic support for the Content-Range header.
        preg_match('/bytes=(\d+)?-(\d+)?/', $http_range_header, $matches);
        $this->first_byte = isset($matches['1']) ? (int)$matches['1'] : null;
        $this->last_byte = isset($matches['2']) ? (int)$matches['2'] : null;

        $this->filesize = filesize($file_path);

        // Start position begins after end of file.
        if ($this->first_byte >= $this->filesize) {
            $this->is_valid = false;
        }

        // "If the last-byte-pos value is present, it MUST be greater than or equal to the first-byte-pos in that
        // byte-range-spec, or the byte- range-spec is syntactically invalid."
        if (!($this->last_byte === null) && !($this->last_byte >= $this->first_byte)) {
            $this->is_valid = false;
        }
    }

    public function getFirstBytePosition()
    {
        if ($this->first_byte === null) {
            return $this->filesize - 1 - $this->last_byte;
        }

        return $this->first_byte;
    }

    public function getLastBytePosition()
    {
        if ($this->last_byte === null) {
            return $this->filesize - 1;
        }

        return $this->last_byte;
    }

    public function getLength()
    {
        return $this->getLastBytePosition() - $this->getFirstBytePosition() + 1;
    }

    public function getByteRangeSpec()
    {
        return $this->is_valid ? $this->getFirstBytePosition() . '-' . $this->getLastBytePosition() : '*';
    }

    public function getContentRangeHeader()
    {
        return 'bytes ' . $this->getByteRangeSpec() . '/' .  $this->filesize;
    }

    public function isValid()
    {
        return $this->is_valid;
    }
}
