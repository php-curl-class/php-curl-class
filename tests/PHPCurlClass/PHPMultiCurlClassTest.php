<?php
require '../src/Curl/MultiCurl.php';

use \Curl\MultiCurl;
use \Helper\Test;

class MultiCurlTest extends PHPUnit_Framework_TestCase
{
    public function testMultiCurlCallback()
    {
        $delete_before_send_called = false;
        $delete_success_called = false;
        $delete_error_called = false;
        $delete_complete_called = false;

        $get_before_send_called = false;
        $get_success_called = false;
        $get_error_called = false;
        $get_complete_called = false;

        $head_before_send_called = false;
        $head_success_called = false;
        $head_error_called = false;
        $head_complete_called = false;

        $options_before_send_called = false;
        $options_success_called = false;
        $options_error_called = false;
        $options_complete_called = false;

        $patch_before_send_called = false;
        $patch_success_called = false;
        $patch_error_called = false;
        $patch_complete_called = false;

        $post_before_send_called = false;
        $post_success_called = false;
        $post_error_called = false;
        $post_complete_called = false;

        $put_before_send_called = false;
        $put_success_called = false;
        $put_error_called = false;
        $put_complete_called = false;

        $multi_curl = new MultiCurl();
        $multi_curl->beforeSend(function ($instance) use (
            &$delete_before_send_called, &$delete_success_called, &$delete_error_called, &$delete_complete_called,
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called,
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called,
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called,
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called,
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called,
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            $request_method = $instance->getOpt(CURLOPT_CUSTOMREQUEST);
            echo 'beforeSend request method: ' . $request_method . "\n";
            if ($request_method === 'DELETE') {
                PHPUnit_Framework_Assert::assertFalse($delete_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($delete_success_called);
                PHPUnit_Framework_Assert::assertFalse($delete_error_called);
                PHPUnit_Framework_Assert::assertFalse($delete_complete_called);
                $delete_before_send_called = true;
            }
            if ($request_method === 'GET') {
                PHPUnit_Framework_Assert::assertFalse($get_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($get_success_called);
                PHPUnit_Framework_Assert::assertFalse($get_error_called);
                PHPUnit_Framework_Assert::assertFalse($get_complete_called);
                $get_before_send_called = true;
            }
            if ($request_method === 'HEAD') {
                PHPUnit_Framework_Assert::assertFalse($head_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($head_success_called);
                PHPUnit_Framework_Assert::assertFalse($head_error_called);
                PHPUnit_Framework_Assert::assertFalse($head_complete_called);
                $head_before_send_called = true;
            }
            if ($request_method === 'OPTIONS') {
                PHPUnit_Framework_Assert::assertFalse($options_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($options_success_called);
                PHPUnit_Framework_Assert::assertFalse($options_error_called);
                PHPUnit_Framework_Assert::assertFalse($options_complete_called);
                $options_before_send_called = true;
            }
            if ($request_method === 'PATCH') {
                PHPUnit_Framework_Assert::assertFalse($patch_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($patch_success_called);
                PHPUnit_Framework_Assert::assertFalse($patch_error_called);
                PHPUnit_Framework_Assert::assertFalse($patch_complete_called);
                $patch_before_send_called = true;
            }
            if ($request_method === 'POST') {
                PHPUnit_Framework_Assert::assertFalse($post_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($post_success_called);
                PHPUnit_Framework_Assert::assertFalse($post_error_called);
                PHPUnit_Framework_Assert::assertFalse($post_complete_called);
                $post_before_send_called = true;
            }
            if ($request_method === 'PUT') {
                PHPUnit_Framework_Assert::assertFalse($put_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($put_success_called);
                PHPUnit_Framework_Assert::assertFalse($put_error_called);
                PHPUnit_Framework_Assert::assertFalse($put_complete_called);
                $put_before_send_called = true;
            }
        });
        $multi_curl->success(function ($instance) use (
            &$delete_before_send_called, &$delete_success_called, &$delete_error_called, &$delete_complete_called,
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called,
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called,
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called,
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called,
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called,
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            $request_method = $instance->getOpt(CURLOPT_CUSTOMREQUEST);
            echo 'success request method: ' . $request_method . "\n";
            if ($request_method === 'DELETE') {
                PHPUnit_Framework_Assert::assertTrue($delete_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($delete_success_called);
                PHPUnit_Framework_Assert::assertFalse($delete_error_called);
                PHPUnit_Framework_Assert::assertFalse($delete_complete_called);
                $delete_success_called = true;
            }
            if ($request_method === 'GET') {
                PHPUnit_Framework_Assert::assertTrue($get_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($get_success_called);
                PHPUnit_Framework_Assert::assertFalse($get_error_called);
                PHPUnit_Framework_Assert::assertFalse($get_complete_called);
                $get_success_called = true;
            }
            if ($request_method === 'HEAD') {
                PHPUnit_Framework_Assert::assertTrue($head_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($head_success_called);
                PHPUnit_Framework_Assert::assertFalse($head_error_called);
                PHPUnit_Framework_Assert::assertFalse($head_complete_called);
                $head_success_called = true;
            }
            if ($request_method === 'OPTIONS') {
                PHPUnit_Framework_Assert::assertTrue($options_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($options_success_called);
                PHPUnit_Framework_Assert::assertFalse($options_error_called);
                PHPUnit_Framework_Assert::assertFalse($options_complete_called);
                $options_success_called = true;
            }
            if ($request_method === 'PATCH') {
                PHPUnit_Framework_Assert::assertTrue($patch_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($patch_success_called);
                PHPUnit_Framework_Assert::assertFalse($patch_error_called);
                PHPUnit_Framework_Assert::assertFalse($patch_complete_called);
                $patch_success_called = true;
            }
            if ($request_method === 'POST') {
                PHPUnit_Framework_Assert::assertTrue($post_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($post_success_called);
                PHPUnit_Framework_Assert::assertFalse($post_error_called);
                PHPUnit_Framework_Assert::assertFalse($post_complete_called);
                $post_success_called = true;
            }
            if ($request_method === 'PUT') {
                PHPUnit_Framework_Assert::assertTrue($put_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($put_success_called);
                PHPUnit_Framework_Assert::assertFalse($put_error_called);
                PHPUnit_Framework_Assert::assertFalse($put_complete_called);
                $put_success_called = true;
            }
        });
        $multi_curl->error(function ($instance) use (
            &$delete_error_called,
            &$get_error_called,
            &$head_error_called,
            &$options_error_called,
            &$patch_error_called,
            &$post_error_called,
            &$put_error_called) {
            $delete_error_called = true;
            $get_error_called = true;
            $head_error_called = true;
            $options_error_called = true;
            $patch_error_called = true;
            $post_error_called = true;
            $put_error_called = true;
        });
        $multi_curl->complete(function ($instance) use (
            &$delete_before_send_called, &$delete_success_called, &$delete_error_called, &$delete_complete_called,
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called,
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called,
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called,
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called,
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called,
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            $request_method = $instance->getOpt(CURLOPT_CUSTOMREQUEST);
            echo 'complete request method: ' . $request_method . "\n";
            if ($request_method === 'DELETE') {
                PHPUnit_Framework_Assert::assertTrue($delete_before_send_called);
                PHPUnit_Framework_Assert::assertTrue($delete_success_called);
                PHPUnit_Framework_Assert::assertFalse($delete_error_called);
                PHPUnit_Framework_Assert::assertFalse($delete_complete_called);
                $delete_complete_called = true;
            }
            if ($request_method === 'GET') {
                PHPUnit_Framework_Assert::assertTrue($get_before_send_called);
                PHPUnit_Framework_Assert::assertTrue($get_success_called);
                PHPUnit_Framework_Assert::assertFalse($get_error_called);
                PHPUnit_Framework_Assert::assertFalse($get_complete_called);
                $get_complete_called = true;
            }
            if ($request_method === 'HEAD') {
                PHPUnit_Framework_Assert::assertTrue($head_before_send_called);
                PHPUnit_Framework_Assert::assertTrue($head_success_called);
                PHPUnit_Framework_Assert::assertFalse($head_error_called);
                PHPUnit_Framework_Assert::assertFalse($head_complete_called);
                $head_complete_called = true;
            }
            if ($request_method === 'OPTIONS') {
                PHPUnit_Framework_Assert::assertTrue($options_before_send_called);
                PHPUnit_Framework_Assert::assertTrue($options_success_called);
                PHPUnit_Framework_Assert::assertFalse($options_error_called);
                PHPUnit_Framework_Assert::assertFalse($options_complete_called);
                $options_complete_called = true;
            }
            if ($request_method === 'PATCH') {
                PHPUnit_Framework_Assert::assertTrue($patch_before_send_called);
                PHPUnit_Framework_Assert::assertTrue($patch_success_called);
                PHPUnit_Framework_Assert::assertFalse($patch_error_called);
                PHPUnit_Framework_Assert::assertFalse($patch_complete_called);
                $patch_complete_called = true;
            }
            if ($request_method === 'POST') {
                PHPUnit_Framework_Assert::assertTrue($post_before_send_called);
                PHPUnit_Framework_Assert::assertTrue($post_success_called);
                PHPUnit_Framework_Assert::assertFalse($post_error_called);
                PHPUnit_Framework_Assert::assertFalse($post_complete_called);
                $post_complete_called = true;
            }
            if ($request_method === 'PUT') {
                PHPUnit_Framework_Assert::assertTrue($put_before_send_called);
                PHPUnit_Framework_Assert::assertTrue($put_success_called);
                PHPUnit_Framework_Assert::assertFalse($put_error_called);
                PHPUnit_Framework_Assert::assertFalse($put_complete_called);
                $put_complete_called = true;
            }
        });

        $multi_curl->addDelete(Test::TEST_URL);
        $multi_curl->addGet(Test::TEST_URL);
        $multi_curl->addHead(Test::TEST_URL);
        $multi_curl->addOptions(Test::TEST_URL);
        $multi_curl->addPatch(Test::TEST_URL);
        $multi_curl->addPost(Test::TEST_URL);
        $multi_curl->addPut(Test::TEST_URL);
        $multi_curl->start();

        $this->assertTrue($delete_before_send_called);
        $this->assertTrue($delete_success_called);
        $this->assertFalse($delete_error_called);
        $this->assertTrue($delete_complete_called);

        $this->assertTrue($get_before_send_called);
        $this->assertTrue($get_success_called);
        $this->assertFalse($get_error_called);
        $this->assertTrue($get_complete_called);

        $this->assertTrue($head_before_send_called);
        $this->assertTrue($head_success_called);
        $this->assertFalse($head_error_called);
        $this->assertTrue($head_complete_called);

        $this->assertTrue($options_before_send_called);
        $this->assertTrue($options_success_called);
        $this->assertFalse($options_error_called);
        $this->assertTrue($options_complete_called);

        $this->assertTrue($patch_before_send_called);
        $this->assertTrue($patch_success_called);
        $this->assertFalse($patch_error_called);
        $this->assertTrue($patch_complete_called);

        $this->assertTrue($post_before_send_called);
        $this->assertTrue($post_success_called);
        $this->assertFalse($post_error_called);
        $this->assertTrue($post_complete_called);

        $this->assertTrue($put_before_send_called);
        $this->assertTrue($put_success_called);
        $this->assertFalse($put_error_called);
        $this->assertTrue($put_complete_called);
    }

    public function testMultiCurlCallbackError()
    {
        $delete_before_send_called = false;
        $delete_success_called = false;
        $delete_error_called = false;
        $delete_complete_called = false;

        $get_before_send_called = false;
        $get_success_called = false;
        $get_error_called = false;
        $get_complete_called = false;

        $head_before_send_called = false;
        $head_success_called = false;
        $head_error_called = false;
        $head_complete_called = false;

        $options_before_send_called = false;
        $options_success_called = false;
        $options_error_called = false;
        $options_complete_called = false;

        $patch_before_send_called = false;
        $patch_success_called = false;
        $patch_error_called = false;
        $patch_complete_called = false;

        $post_before_send_called = false;
        $post_success_called = false;
        $post_error_called = false;
        $post_complete_called = false;

        $put_before_send_called = false;
        $put_success_called = false;
        $put_error_called = false;
        $put_complete_called = false;

        $multi_curl = new MultiCurl();
        $multi_curl->beforeSend(function ($instance) use (
            &$delete_before_send_called, &$delete_success_called, &$delete_error_called, &$delete_complete_called,
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called,
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called,
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called,
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called,
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called,
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            $request_method = $instance->getOpt(CURLOPT_CUSTOMREQUEST);
            echo 'beforeSend request method: ' . $request_method . "\n";
            if ($request_method === 'DELETE') {
                PHPUnit_Framework_Assert::assertFalse($delete_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($delete_success_called);
                PHPUnit_Framework_Assert::assertFalse($delete_error_called);
                PHPUnit_Framework_Assert::assertFalse($delete_complete_called);
                $delete_before_send_called = true;
            }
            if ($request_method === 'GET') {
                PHPUnit_Framework_Assert::assertFalse($get_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($get_success_called);
                PHPUnit_Framework_Assert::assertFalse($get_error_called);
                PHPUnit_Framework_Assert::assertFalse($get_complete_called);
                $get_before_send_called = true;
            }
            if ($request_method === 'HEAD') {
                PHPUnit_Framework_Assert::assertFalse($head_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($head_success_called);
                PHPUnit_Framework_Assert::assertFalse($head_error_called);
                PHPUnit_Framework_Assert::assertFalse($head_complete_called);
                $head_before_send_called = true;
            }
            if ($request_method === 'OPTIONS') {
                PHPUnit_Framework_Assert::assertFalse($options_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($options_success_called);
                PHPUnit_Framework_Assert::assertFalse($options_error_called);
                PHPUnit_Framework_Assert::assertFalse($options_complete_called);
                $options_before_send_called = true;
            }
            if ($request_method === 'PATCH') {
                PHPUnit_Framework_Assert::assertFalse($patch_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($patch_success_called);
                PHPUnit_Framework_Assert::assertFalse($patch_error_called);
                PHPUnit_Framework_Assert::assertFalse($patch_complete_called);
                $patch_before_send_called = true;
            }
            if ($request_method === 'POST') {
                PHPUnit_Framework_Assert::assertFalse($post_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($post_success_called);
                PHPUnit_Framework_Assert::assertFalse($post_error_called);
                PHPUnit_Framework_Assert::assertFalse($post_complete_called);
                $post_before_send_called = true;
            }
            if ($request_method === 'PUT') {
                PHPUnit_Framework_Assert::assertFalse($put_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($put_success_called);
                PHPUnit_Framework_Assert::assertFalse($put_error_called);
                PHPUnit_Framework_Assert::assertFalse($put_complete_called);
                $put_before_send_called = true;
            }
        });
        $multi_curl->success(function ($instance) use (
            &$delete_success_called,
            &$get_success_called,
            &$head_success_called,
            &$options_success_called,
            &$patch_success_called,
            &$post_success_called,
            &$put_success_called) {
            $delete_success_called = true;
            $get_success_called = true;
            $head_success_called = true;
            $options_success_called = true;
            $patch_success_called = true;
            $post_success_called = true;
            $put_success_called = true;
        });
        $multi_curl->error(function ($instance) use (
            &$delete_before_send_called, &$delete_success_called, &$delete_error_called, &$delete_complete_called,
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called,
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called,
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called,
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called,
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called,
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            $request_method = $instance->getOpt(CURLOPT_CUSTOMREQUEST);
            echo 'error request method: ' . $request_method . "\n";
            if ($request_method === 'DELETE') {
                PHPUnit_Framework_Assert::assertTrue($delete_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($delete_success_called);
                PHPUnit_Framework_Assert::assertFalse($delete_error_called);
                PHPUnit_Framework_Assert::assertFalse($delete_complete_called);
                $delete_error_called = true;
            }
            if ($request_method === 'GET') {
                PHPUnit_Framework_Assert::assertTrue($get_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($get_success_called);
                PHPUnit_Framework_Assert::assertFalse($get_error_called);
                PHPUnit_Framework_Assert::assertFalse($get_complete_called);
                $get_error_called = true;
            }
            if ($request_method === 'HEAD') {
                PHPUnit_Framework_Assert::assertTrue($head_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($head_success_called);
                PHPUnit_Framework_Assert::assertFalse($head_error_called);
                PHPUnit_Framework_Assert::assertFalse($head_complete_called);
                $head_error_called = true;
            }
            if ($request_method === 'OPTIONS') {
                PHPUnit_Framework_Assert::assertTrue($options_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($options_success_called);
                PHPUnit_Framework_Assert::assertFalse($options_error_called);
                PHPUnit_Framework_Assert::assertFalse($options_complete_called);
                $options_error_called = true;
            }
            if ($request_method === 'PATCH') {
                PHPUnit_Framework_Assert::assertTrue($patch_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($patch_success_called);
                PHPUnit_Framework_Assert::assertFalse($patch_error_called);
                PHPUnit_Framework_Assert::assertFalse($patch_complete_called);
                $patch_error_called = true;
            }
            if ($request_method === 'POST') {
                PHPUnit_Framework_Assert::assertTrue($post_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($post_success_called);
                PHPUnit_Framework_Assert::assertFalse($post_error_called);
                PHPUnit_Framework_Assert::assertFalse($post_complete_called);
                $post_error_called = true;
            }
            if ($request_method === 'PUT') {
                PHPUnit_Framework_Assert::assertTrue($put_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($put_success_called);
                PHPUnit_Framework_Assert::assertFalse($put_error_called);
                PHPUnit_Framework_Assert::assertFalse($put_complete_called);
                $put_error_called = true;
            }
        });
        $multi_curl->complete(function ($instance) use (
            &$delete_before_send_called, &$delete_success_called, &$delete_error_called, &$delete_complete_called,
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called,
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called,
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called,
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called,
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called,
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called) {
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            $request_method = $instance->getOpt(CURLOPT_CUSTOMREQUEST);
            echo 'complete request method: ' . $request_method . "\n";
            if ($request_method === 'DELETE') {
                PHPUnit_Framework_Assert::assertTrue($delete_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($delete_success_called);
                PHPUnit_Framework_Assert::assertTrue($delete_error_called);
                PHPUnit_Framework_Assert::assertFalse($delete_complete_called);
                $delete_complete_called = true;
            }
            if ($request_method === 'GET') {
                PHPUnit_Framework_Assert::assertTrue($get_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($get_success_called);
                PHPUnit_Framework_Assert::assertTrue($get_error_called);
                PHPUnit_Framework_Assert::assertFalse($get_complete_called);
                $get_complete_called = true;
            }
            if ($request_method === 'HEAD') {
                PHPUnit_Framework_Assert::assertTrue($head_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($head_success_called);
                PHPUnit_Framework_Assert::assertTrue($head_error_called);
                PHPUnit_Framework_Assert::assertFalse($head_complete_called);
                $head_complete_called = true;
            }
            if ($request_method === 'OPTIONS') {
                PHPUnit_Framework_Assert::assertTrue($options_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($options_success_called);
                PHPUnit_Framework_Assert::assertTrue($options_error_called);
                PHPUnit_Framework_Assert::assertFalse($options_complete_called);
                $options_complete_called = true;
            }
            if ($request_method === 'PATCH') {
                PHPUnit_Framework_Assert::assertTrue($patch_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($patch_success_called);
                PHPUnit_Framework_Assert::assertTrue($patch_error_called);
                PHPUnit_Framework_Assert::assertFalse($patch_complete_called);
                $patch_complete_called = true;
            }
            if ($request_method === 'POST') {
                PHPUnit_Framework_Assert::assertTrue($post_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($post_success_called);
                PHPUnit_Framework_Assert::assertTrue($post_error_called);
                PHPUnit_Framework_Assert::assertFalse($post_complete_called);
                $post_complete_called = true;
            }
            if ($request_method === 'PUT') {
                PHPUnit_Framework_Assert::assertTrue($put_before_send_called);
                PHPUnit_Framework_Assert::assertFalse($put_success_called);
                PHPUnit_Framework_Assert::assertTrue($put_error_called);
                PHPUnit_Framework_Assert::assertFalse($put_complete_called);
                $put_complete_called = true;
            }
            PHPUnit_Framework_Assert::assertTrue($instance->error);
            PHPUnit_Framework_Assert::assertTrue($instance->curl_error);
            PHPUnit_Framework_Assert::assertFalse($instance->http_error);
            PHPUnit_Framework_Assert::assertEquals(CURLE_OPERATION_TIMEOUTED, $instance->error_code);
            PHPUnit_Framework_Assert::assertEquals(CURLE_OPERATION_TIMEOUTED, $instance->curl_error_code);
        });

        $multi_curl->addDelete(Test::ERROR_URL);
        $multi_curl->addGet(Test::ERROR_URL);
        $multi_curl->addHead(Test::ERROR_URL);
        $multi_curl->addOptions(Test::ERROR_URL);
        $multi_curl->addPatch(Test::ERROR_URL);
        $multi_curl->addPost(Test::ERROR_URL);
        $multi_curl->addPut(Test::ERROR_URL);
        $multi_curl->start();

        $this->assertTrue($delete_before_send_called);
        $this->assertFalse($delete_success_called);
        $this->assertTrue($delete_error_called);
        $this->assertTrue($delete_complete_called);

        $this->assertTrue($get_before_send_called);
        $this->assertFalse($get_success_called);
        $this->assertTrue($get_error_called);
        $this->assertTrue($get_complete_called);

        $this->assertTrue($head_before_send_called);
        $this->assertFalse($head_success_called);
        $this->assertTrue($head_error_called);
        $this->assertTrue($head_complete_called);

        $this->assertTrue($options_before_send_called);
        $this->assertFalse($options_success_called);
        $this->assertTrue($options_error_called);
        $this->assertTrue($options_complete_called);

        $this->assertTrue($patch_before_send_called);
        $this->assertFalse($patch_success_called);
        $this->assertTrue($patch_error_called);
        $this->assertTrue($patch_complete_called);

        $this->assertTrue($post_before_send_called);
        $this->assertFalse($post_success_called);
        $this->assertTrue($post_error_called);
        $this->assertTrue($post_complete_called);

        $this->assertTrue($put_before_send_called);
        $this->assertFalse($put_success_called);
        $this->assertTrue($put_error_called);
        $this->assertTrue($put_complete_called);
    }

    public function testCurlCallback()
    {
        $get_before_send_called = false;
        $get_success_called = false;
        $get_error_called = false;
        $get_complete_called = false;

        $multi_curl = new MultiCurl();
        echo 'about to add get' . "\n";
        $get = $multi_curl->addGet(Test::TEST_URL);
        echo 'get added' . "\n";
        echo 'about to add before send' . "\n";
        $get->beforeSend(function ($instance) use (
            &$get_before_send_called,
            &$get_success_called,
            &$get_error_called,
            &$get_complete_called) {
            echo 'before send called' . "\n";
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertFalse($get_before_send_called);
            PHPUnit_Framework_Assert::assertFalse($get_success_called);
            PHPUnit_Framework_Assert::assertFalse($get_error_called);
            PHPUnit_Framework_Assert::assertFalse($get_complete_called);
            $get_before_send_called = true;
        });
        echo 'about to set success' . "\n";
        $get->success(function ($instance) use (
            &$get_before_send_called,
            &$get_success_called,
            &$get_error_called,
            &$get_complete_called) {
            echo 'success called' . "\n";
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($get_before_send_called);
            PHPUnit_Framework_Assert::assertFalse($get_success_called);
            PHPUnit_Framework_Assert::assertFalse($get_error_called);
            PHPUnit_Framework_Assert::assertFalse($get_complete_called);
            $get_success_called = true;
        });
        echo 'about to set error' . "\n";
        $get->error(function ($instance) use (
            &$get_error_called) {
            echo 'error called' . "\n";
            $get_error_called = true;
        });
        echo 'about to set complete' . "\n";
        $get->complete(function ($instance) use (
            &$get_before_send_called,
            &$get_success_called,
            &$get_error_called,
            &$get_complete_called) {
            echo 'complete called' . "\n";
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($get_before_send_called);
            PHPUnit_Framework_Assert::assertTrue($get_success_called);
            PHPUnit_Framework_Assert::assertFalse($get_error_called);
            PHPUnit_Framework_Assert::assertFalse($get_complete_called);
            $get_complete_called = true;
        });

        echo 'about to run start' . "\n";
        $multi_curl->start();
        echo 'calls done' . "\n";

        $this->assertTrue($get_before_send_called);
        $this->assertTrue($get_success_called);
        $this->assertFalse($get_error_called);
        $this->assertTrue($get_complete_called);
    }

    public function testCurlCallbackError()
    {
        $get_before_send_called = false;
        $get_success_called = false;
        $get_error_called = false;
        $get_complete_called = false;

        $multi_curl = new MultiCurl();
        echo 'about to add get' . "\n";
        $get = $multi_curl->addGet(Test::ERROR_URL);
        echo 'get added' . "\n";
        echo 'about to add before send' . "\n";
        $get->beforeSend(function ($instance) use (
            &$get_before_send_called,
            &$get_success_called,
            &$get_error_called,
            &$get_complete_called) {
            echo 'before send called' . "\n";
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertFalse($get_before_send_called);
            PHPUnit_Framework_Assert::assertFalse($get_success_called);
            PHPUnit_Framework_Assert::assertFalse($get_error_called);
            PHPUnit_Framework_Assert::assertFalse($get_complete_called);
            $get_before_send_called = true;
        });
        echo 'about to set success' . "\n";
        $get->success(function ($instance) use (
            &$get_success_called) {
            echo 'success called' . "\n";
            $get_success_called = true;
        });
        echo 'about to set error' . "\n";
        $get->error(function ($instance) use (
            &$get_before_send_called,
            &$get_success_called,
            &$get_error_called,
            &$get_complete_called) {
            echo 'error called' . "\n";
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($get_before_send_called);
            PHPUnit_Framework_Assert::assertFalse($get_success_called);
            PHPUnit_Framework_Assert::assertFalse($get_error_called);
            PHPUnit_Framework_Assert::assertFalse($get_complete_called);
            $get_error_called = true;
        });
        echo 'about to set complete' . "\n";
        $get->complete(function ($instance) use (
            &$get_before_send_called,
            &$get_success_called,
            &$get_error_called,
            &$get_complete_called) {
            echo 'complete called' . "\n";
            PHPUnit_Framework_Assert::assertInstanceOf('Curl\Curl', $instance);
            PHPUnit_Framework_Assert::assertTrue($get_before_send_called);
            PHPUnit_Framework_Assert::assertFalse($get_success_called);
            PHPUnit_Framework_Assert::assertTrue($get_error_called);
            PHPUnit_Framework_Assert::assertFalse($get_complete_called);
            $get_complete_called = true;
        });

        echo 'about to run start' . "\n";
        $multi_curl->start();
        echo 'calls done' . "\n";

        $this->assertTrue($get_before_send_called);
        $this->assertFalse($get_success_called);
        $this->assertTrue($get_error_called);
        $this->assertTrue($get_complete_called);
    }
}
