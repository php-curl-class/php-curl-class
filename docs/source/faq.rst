===
FAQ
===


How do I set custom cURL options?
=================================

Set custom `cURL options
<https://secure.php.net/manual/en/function.curl-setopt.php>`_ using the
``Curl::setOpt`` and ``MultiCurl::setOpt`` methods.

.. code-block:: php

    $curl->setOpt(CURLOPT_ENCODING , 'gzip');

Can PHP Curl Class send asynchronous requests?
==============================================

Yes. Use the ``MultiCurl`` class to send an asynchronous requests.

.. code-block:: php

    require __DIR__ . '/vendor/autoload.php';

    use \Curl\MultiCurl;

    $multi_curl = new MultiCurl();
    $multi_curl->complete(function($instance) {
        echo 'call completed' . "\n";
        echo $instance->response;
    });
    $multi_curl->addGet('https://httpbin.org/get');
    $multi_curl->addGet('https://httpbin.org/get');
    $multi_curl->addGet('https://httpbin.org/get');
    echo 'Starting...';
    $multi_curl->start();
    echo 'All done!';
