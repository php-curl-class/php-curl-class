<?php

// Migrate pre-4.x.x php scripts to use current naming convention of $camelCase
// for properties / class member variables. To use, change into the directory
// containing php files that you wish to update and run this script. Example:
// $ cd /path/to/project
// $ php ../php-curl-class/scripts/v4_migration.php

$cwd = getcwd();
echo 'Checking current directory "' . $cwd . '" for possible changes.' . "\n";

$results = array(
    'errors' => array(),
    'files_found' => array(),
    'files_to_change' => array(),
);

$migrations = array(
    'error_code' => 'errorCode',
    'error_message' => 'errorMessage',
    'curl_error' => 'curlError',
    'curl_error_code' => 'curlErrorCode',
    'curl_error_message' => 'curlErrorMessage',
    'http_error' => 'httpError',
    'http_status_code' => 'httpStatusCode',
    'http_error_message' => 'httpErrorMessage',
    'base_url' => 'baseUrl',
    'request_headers' => 'requestHeaders',
    'response_headers' => 'responseHeaders',
    'raw_response_headers' => 'rawResponseHeaders',
    'raw_response' => 'rawResponse',
    'before_send_function' => 'beforeSendFunction',
    'download_complete_function' => 'downloadCompleteFunction',
);

$directory = new RecursiveDirectoryIterator($cwd);
$iterator = new RecursiveIteratorIterator($directory);
$regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
foreach ($regex as $file) {
    $filepath = $file['0'];
    $results['files_found'][] = $filepath;
    if ($filepath === __FILE__) {
        continue;
    }
    $data = file_get_contents($filepath);
    $short_path = str_replace($cwd, '', $filepath);
    if ($data === false) {
        $results['errors'][] = $filepath;
        echo $short_path . ' [ERROR]' . "\n";
    } else {
        foreach ($migrations as $old => $new) {
            if (!(strpos($data, '->' . $old) === false)) {
                $results['files_to_change'][] = $filepath;
                echo $short_path . "\n";
                break;
            }
        }
    }
}

foreach ($results as $name => $files) {
    $results[$name . '_count'] = count($files);
}
$results['errors_count'] = count($results['errors']);
$results['files_found_count'] = count($results['files_found']);
$results['files_to_change_count'] = count($results['files_to_change']);

if ($results['errors_count'] > 0) {
    echo 'ERROR: Unable to read files.' . "\n";
    exit(1);
} else if ($results['files_found_count'] === 0) {
    echo 'Current directory "' . $cwd . '"' . "\n";
    echo 'ERROR: No read files found in current directory.' . "\n";
    exit(1);
} else if ($results['files_to_change_count'] === 0) {
    echo 'OK: No files to change.' . "\n";
    exit(0);
} else if ($results['files_to_change_count'] > 0) {
    echo $results['files_to_change_count'] . ' of ' . $results['files_found_count'] . ' files to change found.' . "\n";
    echo 'Continue? [y/n] ';
    if (!in_array(trim(fgets(STDIN)), array('y', 'Y'))) {
        die();
    }
    foreach ($results['files_to_change'] as $filepath) {
        $data = file_get_contents($filepath);
        foreach ($migrations as $old => $new) {
            $data = str_replace('->' . $old, '->' . $new, $data);
        }
        file_put_contents($filepath, $data);
    }
    echo 'Done' . "\n";
    exit(0);
}
