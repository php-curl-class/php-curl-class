<?php
// Receive PUT file.
// See also "examples/put_large_file_chunked.php".

function file_get_contents_chunked($filename, $chunk_size, $callback) {
    $handle = fopen($filename, 'r');
    while (!feof($handle)) {
        call_user_func_array($callback, [fread($handle, $chunk_size)]);
    }
    fclose($handle);
}

$tmpnam = tempnam('/tmp', 'php-curl-class.');
$file = fopen($tmpnam, 'wb+');

// Use file_get_contents_chunked() rather than file_get_contents() to avoid error:
// "Fatal error:  Allowed memory size of ... bytes exhausted (tried to allocate ... bytes) in ... on line 0".
file_get_contents_chunked('php://input', 4096, function ($chunk) use (&$file) {
    fwrite($file, $chunk);
});

// @codingStandardsIgnoreFile
