.. title:: PHP Curl Class

============================
PHP Curl Class Documentation
============================

PHP Curl Class makes it easy to send HTTP requests and integrate with web APIs.

.. code-block:: php

    $curl = new Curl();
    $curl->get('https://httpbin.org/get', array(
        'q' => 'keyword',
    ));
    echo $curl->httpStatusCode; // 200
    echo $curl->responseHeaders['content-type']; // "application/json"

    // Send asynchronous requests.
    $multi_curl = new MultiCurl();
    $multi_curl->complete(function($instance) {
        echo 'call completed' . "\n";
        echo $instance->response;
    });
    $multi_curl->addPost('https://httpbin.org/post');
    $multi_curl->addGet('https://httpbin.org/get');
    $multi_curl->addDelete('https://httpbin.org/delete');
    $multi_curl->start();


User Guide
==========

.. toctree::
    :maxdepth: 3

    overview
    faq
