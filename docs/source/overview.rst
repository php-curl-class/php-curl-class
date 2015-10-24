========
Overview
========

Requirements
============

#. PHP 5.3, 5.4, 5.5, 5.6, or HHVM.
#. PHP compiled with cURL.

.. _installation:


Installation
============

The recommended way to install PHP Curl Class is with
`Composer <https://getcomposer.org>`_. Composer is a dependency manager for php.

.. code-block:: bash

    # Install Composer
    curl -sS https://getcomposer.org/installer | php

Install PHP Curl Class as a dependency using composer:

.. code-block:: bash

    composer require php-curl-class/php-curl-class

Composer generates a ``vendor/autoload.php`` file. You can simply include this
file and you will get autoloading for free:

.. code-block:: php

    require __DIR__ . 'vendor/autoload.php';


License
=======

This is free and unencumbered software released into the public domain.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a compiled
binary, for any purpose, commercial or non-commercial, and by any
means.

In jurisdictions that recognize copyright laws, the author or authors
of this software dedicate any and all copyright interest in the
software to the public domain. We make this dedication for the benefit
of the public at large and to the detriment of our heirs and
successors. We intend this dedication to be an overt act of
relinquishment in perpetuity of all present and future rights to this
software under copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

For more information, please refer to <http://unlicense.org/>


Contributing
============

#. Check for open issues or open a new issue to start a discussion around a bug or feature.
#. Fork the repository on GitHub to start making your changes.
#. Write one or more tests for the new feature or that expose the bug.
#. Make code changes to implement the feature or fix the bug.
#. Send a pull request to get your changes merged and published.
