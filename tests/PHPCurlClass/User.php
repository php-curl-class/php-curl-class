<?php

namespace Helper;

// Check interface exists to fix "Fatal error: Interface 'JsonSerializable' not found in ../tests/PHPCurlClass/User.php
// on line X".
if (interface_exists('JsonSerializable')) {
    class User implements \JsonSerializable
    {
        private $name;
        private $email;

        public function __construct($name = null, $email = null)
        {
            $this->name = $name;
            $this->email = $email;
        }

        public function jsonSerialize()
        {
            return [
                'name' => $this->name,
                'email' => $this->email,
            ];
        }
    }
}
