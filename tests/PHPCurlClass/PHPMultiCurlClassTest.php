<?php
require '../src/Curl/Curl.php';
require '../src/Curl/MultiCurl.php';
require 'Helper.php';

use \Curl\MultiCurl;
//use \Curl\CaseInsensitiveArray;
use \Helper\Test;

class MultiCurlTest extends PHPUnit_Framework_TestCase
{
    public function testMultiCurlCallback()
    {
        $before_send_called = false;
        $success_called = false;
        $error_called = false;
        $complete_called = false;

        $multi_curl = new MultiCurl();
        $multi_curl->beforeSend(function ($instance) use (
            &$before_send_called,
            &$success_called,
            &$error_called,
            &$complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertFalse($before_send_called);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $before_send_called = true;
        });
        $multi_curl->success(function ($instance) use (
            &$before_send_called,
            &$success_called,
            &$error_called,
            &$complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($before_send_called);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $success_called = true;
        });
        $multi_curl->error(function ($instance) use (
            &$error_called) {
            $error_called = true;
        });
        $multi_curl->complete(function ($instance) use (
            &$before_send_called,
            &$success_called,
            &$error_called,
            &$complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($before_send_called);
            PHPUnit_Framework_Assert::assertTrue($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $complete_called = true;
        });

        $multi_curl->addGet(Test::TEST_URL);
        $multi_curl->start();

        $this->assertTrue($before_send_called);
        $this->assertTrue($success_called);
        $this->assertFalse($error_called);
        $this->assertTrue($complete_called);
    }

    public function testMultiCurlCallbackError()
    {
        $before_send_called = false;
        $success_called = false;
        $error_called = false;
        $complete_called = false;

        $multi_curl = new MultiCurl();
        $multi_curl->beforeSend(function ($instance) use (
            &$before_send_called,
            &$success_called,
            &$error_called,
            &$complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertFalse($before_send_called);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $before_send_called = true;
        });
        $multi_curl->success(function ($instance) use (
            &$success_called) {
            $success_called = true;
        });
        $multi_curl->error(function ($instance) use (
            &$before_send_called,
            &$success_called,
            &$error_called,
            &$complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($before_send_called);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $error_called = true;
        });
        $multi_curl->complete(function ($instance) use (
            &$before_send_called,
            &$success_called,
            &$error_called,
            &$complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($before_send_called);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertTrue($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            PHPUnit_Framework_Assert::assertTrue($instance->error);
            PHPUnit_Framework_Assert::assertTrue($instance->curl_error);
            PHPUnit_Framework_Assert::assertFalse($instance->http_error);
            PHPUnit_Framework_Assert::assertEquals(CURLE_OPERATION_TIMEOUTED, $instance->error_code);
            PHPUnit_Framework_Assert::assertEquals(CURLE_OPERATION_TIMEOUTED, $instance->curl_error_code);
            $complete_called = true;
        });

        $multi_curl->addGet(Test::ERROR_URL);
        $multi_curl->start();

        $this->assertTrue($before_send_called);
        $this->assertFalse($success_called);
        $this->assertTrue($error_called);
        $this->assertTrue($complete_called);
    }

    public function testCurlCallback()
    {
        $before_send_called = false;
        $success_called = false;
        $error_called = false;
        $complete_called = false;

        $multi_curl = new MultiCurl();
        echo 'about to add get' . "\n";
        $get = $multi_curl->addGet(Test::TEST_URL);
        echo 'get added' . "\n";
        echo 'about to add before send' . "\n";
        $get->beforeSend(function ($instance) use (
            &$before_send_called,
            &$success_called,
            &$error_called,
            &$complete_called) {
            echo 'before send called' . "\n";
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertFalse($before_send_called);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $before_send_called = true;
        });
        echo 'about to set success' . "\n";
        $get->success(function ($instance) use (
            &$before_send_called,
            &$success_called,
            &$error_called,
            &$complete_called) {
            echo 'success called' . "\n";
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($before_send_called);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $success_called = true;
        });
        echo 'about to set error' . "\n";
        $get->error(function ($instance) use (
            &$error_called) {
            echo 'error called' . "\n";
            $error_called = true;
        });
        echo 'about to set complete' . "\n";
        $get->complete(function ($instance) use (
            &$before_send_called,
            &$success_called,
            &$error_called,
            &$complete_called) {
            echo 'complete called' . "\n";
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($before_send_called);
            PHPUnit_Framework_Assert::assertTrue($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $complete_called = true;
        });

        echo 'about to run start' . "\n";
        $multi_curl->start();
        echo 'calls done' . "\n";

        $this->assertTrue($before_send_called);
        $this->assertTrue($success_called);
        $this->assertFalse($error_called);
        $this->assertTrue($complete_called);
    }

    public function testCurlCallbackError()
    {
        $before_send_called = false;
        $success_called = false;
        $error_called = false;
        $complete_called = false;

        $multi_curl = new MultiCurl();
        echo 'about to add get' . "\n";
        $get = $multi_curl->addGet(Test::ERROR_URL);
        echo 'get added' . "\n";
        echo 'about to add before send' . "\n";
        $get->beforeSend(function ($instance) use (
            &$before_send_called,
            &$success_called,
            &$error_called,
            &$complete_called) {
            echo 'before send called' . "\n";
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertFalse($before_send_called);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $before_send_called = true;
        });
        echo 'about to set success' . "\n";
        $get->success(function ($instance) use (
            &$success_called) {
            echo 'success called' . "\n";
            $success_called = true;
        });
        echo 'about to set error' . "\n";
        $get->error(function ($instance) use (
            &$before_send_called,
            &$success_called,
            &$error_called,
            &$complete_called) {
            echo 'error called' . "\n";
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($before_send_called);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertFalse($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $error_called = true;
        });
        echo 'about to set complete' . "\n";
        $get->complete(function ($instance) use (
            &$before_send_called,
            &$success_called,
            &$error_called,
            &$complete_called) {
            echo 'complete called' . "\n";
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($before_send_called);
            PHPUnit_Framework_Assert::assertFalse($success_called);
            PHPUnit_Framework_Assert::assertTrue($error_called);
            PHPUnit_Framework_Assert::assertFalse($complete_called);
            $complete_called = true;
        });

        echo 'about to run start' . "\n";
        $multi_curl->start();
        echo 'calls done' . "\n";

        $this->assertTrue($before_send_called);
        $this->assertFalse($success_called);
        $this->assertTrue($error_called);
        $this->assertTrue($complete_called);
    }
}
