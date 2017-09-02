<?php

namespace CurlTest;

use \Curl\Curl;
use \Curl\MultiCurl;
use \Helper\Test;

class MultiCurlTest extends \PHPUnit\Framework\TestCase
{
    public function testMultiCurlCallback()
    {
        $delete_before_send_called = false;
        $delete_success_called = false;
        $delete_error_called = false;
        $delete_complete_called = false;

        $download_before_send_called = false;
        $download_success_called = false;
        $download_error_called = false;
        $download_complete_called = false;

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
            &$download_before_send_called, &$download_success_called, &$download_error_called,
            &$download_complete_called,
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called,
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called,
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called,
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called,
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called,
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            $request_method = $instance->getOpt(CURLOPT_CUSTOMREQUEST);
            if ($request_method === 'DELETE') {
                \PHPUnit\Framework\Assert::assertFalse($delete_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($delete_success_called);
                \PHPUnit\Framework\Assert::assertFalse($delete_error_called);
                \PHPUnit\Framework\Assert::assertFalse($delete_complete_called);
                $delete_before_send_called = true;
            }
            if (isset($instance->download)) {
                \PHPUnit\Framework\Assert::assertFalse($download_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($download_success_called);
                \PHPUnit\Framework\Assert::assertFalse($download_error_called);
                \PHPUnit\Framework\Assert::assertFalse($download_complete_called);
                $download_before_send_called = true;
            } elseif ($request_method === 'GET') {
                \PHPUnit\Framework\Assert::assertFalse($get_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($get_success_called);
                \PHPUnit\Framework\Assert::assertFalse($get_error_called);
                \PHPUnit\Framework\Assert::assertFalse($get_complete_called);
                $get_before_send_called = true;
            }
            if ($request_method === 'HEAD') {
                \PHPUnit\Framework\Assert::assertFalse($head_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($head_success_called);
                \PHPUnit\Framework\Assert::assertFalse($head_error_called);
                \PHPUnit\Framework\Assert::assertFalse($head_complete_called);
                $head_before_send_called = true;
            }
            if ($request_method === 'OPTIONS') {
                \PHPUnit\Framework\Assert::assertFalse($options_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($options_success_called);
                \PHPUnit\Framework\Assert::assertFalse($options_error_called);
                \PHPUnit\Framework\Assert::assertFalse($options_complete_called);
                $options_before_send_called = true;
            }
            if ($request_method === 'PATCH') {
                \PHPUnit\Framework\Assert::assertFalse($patch_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($patch_success_called);
                \PHPUnit\Framework\Assert::assertFalse($patch_error_called);
                \PHPUnit\Framework\Assert::assertFalse($patch_complete_called);
                $patch_before_send_called = true;
            }
            if ($request_method === 'POST') {
                \PHPUnit\Framework\Assert::assertFalse($post_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($post_success_called);
                \PHPUnit\Framework\Assert::assertFalse($post_error_called);
                \PHPUnit\Framework\Assert::assertFalse($post_complete_called);
                $post_before_send_called = true;
            }
            if ($request_method === 'PUT') {
                \PHPUnit\Framework\Assert::assertFalse($put_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($put_success_called);
                \PHPUnit\Framework\Assert::assertFalse($put_error_called);
                \PHPUnit\Framework\Assert::assertFalse($put_complete_called);
                $put_before_send_called = true;
            }
        });
        $multi_curl->success(function ($instance) use (
            &$delete_before_send_called, &$delete_success_called, &$delete_error_called, &$delete_complete_called,
            &$download_before_send_called, &$download_success_called, &$download_error_called,
            &$download_complete_called,
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called,
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called,
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called,
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called,
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called,
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            $request_method = $instance->getOpt(CURLOPT_CUSTOMREQUEST);
            if ($request_method === 'DELETE') {
                \PHPUnit\Framework\Assert::assertTrue($delete_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($delete_success_called);
                \PHPUnit\Framework\Assert::assertFalse($delete_error_called);
                \PHPUnit\Framework\Assert::assertFalse($delete_complete_called);
                $delete_success_called = true;
            }
            if (isset($instance->download)) {
                \PHPUnit\Framework\Assert::assertTrue($download_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($download_success_called);
                \PHPUnit\Framework\Assert::assertFalse($download_error_called);
                \PHPUnit\Framework\Assert::assertFalse($download_complete_called);
                $download_success_called = true;
            } elseif ($request_method === 'GET') {
                \PHPUnit\Framework\Assert::assertTrue($get_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($get_success_called);
                \PHPUnit\Framework\Assert::assertFalse($get_error_called);
                \PHPUnit\Framework\Assert::assertFalse($get_complete_called);
                $get_success_called = true;
            }
            if ($request_method === 'HEAD') {
                \PHPUnit\Framework\Assert::assertTrue($head_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($head_success_called);
                \PHPUnit\Framework\Assert::assertFalse($head_error_called);
                \PHPUnit\Framework\Assert::assertFalse($head_complete_called);
                $head_success_called = true;
            }
            if ($request_method === 'OPTIONS') {
                \PHPUnit\Framework\Assert::assertTrue($options_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($options_success_called);
                \PHPUnit\Framework\Assert::assertFalse($options_error_called);
                \PHPUnit\Framework\Assert::assertFalse($options_complete_called);
                $options_success_called = true;
            }
            if ($request_method === 'PATCH') {
                \PHPUnit\Framework\Assert::assertTrue($patch_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($patch_success_called);
                \PHPUnit\Framework\Assert::assertFalse($patch_error_called);
                \PHPUnit\Framework\Assert::assertFalse($patch_complete_called);
                $patch_success_called = true;
            }
            if ($request_method === 'POST') {
                \PHPUnit\Framework\Assert::assertTrue($post_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($post_success_called);
                \PHPUnit\Framework\Assert::assertFalse($post_error_called);
                \PHPUnit\Framework\Assert::assertFalse($post_complete_called);
                $post_success_called = true;
            }
            if ($request_method === 'PUT') {
                \PHPUnit\Framework\Assert::assertTrue($put_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($put_success_called);
                \PHPUnit\Framework\Assert::assertFalse($put_error_called);
                \PHPUnit\Framework\Assert::assertFalse($put_complete_called);
                $put_success_called = true;
            }
        });
        $multi_curl->error(function ($instance) use (
            &$delete_error_called,
            &$download_error_called,
            &$get_error_called,
            &$head_error_called,
            &$options_error_called,
            &$patch_error_called,
            &$post_error_called,
            &$put_error_called
        ) {
            $delete_error_called = true;
            $download_error_called = true;
            $get_error_called = true;
            $head_error_called = true;
            $options_error_called = true;
            $patch_error_called = true;
            $post_error_called = true;
            $put_error_called = true;
        });
        $multi_curl->complete(function ($instance) use (
            &$delete_before_send_called, &$delete_success_called, &$delete_error_called, &$delete_complete_called,
            &$download_before_send_called, &$download_success_called, &$download_error_called,
            &$download_complete_called,
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called,
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called,
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called,
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called,
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called,
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            $request_method = $instance->getOpt(CURLOPT_CUSTOMREQUEST);
            if ($request_method === 'DELETE') {
                \PHPUnit\Framework\Assert::assertTrue($delete_before_send_called);
                \PHPUnit\Framework\Assert::assertTrue($delete_success_called);
                \PHPUnit\Framework\Assert::assertFalse($delete_error_called);
                \PHPUnit\Framework\Assert::assertFalse($delete_complete_called);
                $delete_complete_called = true;
            }
            if (isset($instance->download)) {
                \PHPUnit\Framework\Assert::assertTrue($download_before_send_called);
                \PHPUnit\Framework\Assert::assertTrue($download_success_called);
                \PHPUnit\Framework\Assert::assertFalse($download_error_called);
                \PHPUnit\Framework\Assert::assertFalse($download_complete_called);
                $download_complete_called = true;
            } elseif ($request_method === 'GET') {
                \PHPUnit\Framework\Assert::assertTrue($get_before_send_called);
                \PHPUnit\Framework\Assert::assertTrue($get_success_called);
                \PHPUnit\Framework\Assert::assertFalse($get_error_called);
                \PHPUnit\Framework\Assert::assertFalse($get_complete_called);
                $get_complete_called = true;
            }
            if ($request_method === 'HEAD') {
                \PHPUnit\Framework\Assert::assertTrue($head_before_send_called);
                \PHPUnit\Framework\Assert::assertTrue($head_success_called);
                \PHPUnit\Framework\Assert::assertFalse($head_error_called);
                \PHPUnit\Framework\Assert::assertFalse($head_complete_called);
                $head_complete_called = true;
            }
            if ($request_method === 'OPTIONS') {
                \PHPUnit\Framework\Assert::assertTrue($options_before_send_called);
                \PHPUnit\Framework\Assert::assertTrue($options_success_called);
                \PHPUnit\Framework\Assert::assertFalse($options_error_called);
                \PHPUnit\Framework\Assert::assertFalse($options_complete_called);
                $options_complete_called = true;
            }
            if ($request_method === 'PATCH') {
                \PHPUnit\Framework\Assert::assertTrue($patch_before_send_called);
                \PHPUnit\Framework\Assert::assertTrue($patch_success_called);
                \PHPUnit\Framework\Assert::assertFalse($patch_error_called);
                \PHPUnit\Framework\Assert::assertFalse($patch_complete_called);
                $patch_complete_called = true;
            }
            if ($request_method === 'POST') {
                \PHPUnit\Framework\Assert::assertTrue($post_before_send_called);
                \PHPUnit\Framework\Assert::assertTrue($post_success_called);
                \PHPUnit\Framework\Assert::assertFalse($post_error_called);
                \PHPUnit\Framework\Assert::assertFalse($post_complete_called);
                $post_complete_called = true;
            }
            if ($request_method === 'PUT') {
                \PHPUnit\Framework\Assert::assertTrue($put_before_send_called);
                \PHPUnit\Framework\Assert::assertTrue($put_success_called);
                \PHPUnit\Framework\Assert::assertFalse($put_error_called);
                \PHPUnit\Framework\Assert::assertFalse($put_complete_called);
                $put_complete_called = true;
            }
        });

        $multi_curl->addDelete(Test::TEST_URL);
        $download_file_path = tempnam('/tmp', 'php-curl-class.');
        $multi_curl->addDownload(Test::TEST_URL, $download_file_path)->download = true;
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

        $this->assertTrue($download_before_send_called);
        $this->assertTrue($download_success_called);
        $this->assertFalse($download_error_called);
        $this->assertTrue($download_complete_called);
        $this->assertTrue(unlink($download_file_path));

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

        $download_before_send_called = false;
        $download_success_called = false;
        $download_error_called = false;
        $download_complete_called = false;

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
            &$download_before_send_called, &$download_success_called, &$download_error_called,
            &$download_complete_called,
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called,
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called,
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called,
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called,
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called,
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            $request_method = $instance->getOpt(CURLOPT_CUSTOMREQUEST);
            if ($request_method === 'DELETE') {
                \PHPUnit\Framework\Assert::assertFalse($delete_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($delete_success_called);
                \PHPUnit\Framework\Assert::assertFalse($delete_error_called);
                \PHPUnit\Framework\Assert::assertFalse($delete_complete_called);
                $delete_before_send_called = true;
            }
            if (isset($instance->download)) {
                \PHPUnit\Framework\Assert::assertFalse($download_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($download_success_called);
                \PHPUnit\Framework\Assert::assertFalse($download_error_called);
                \PHPUnit\Framework\Assert::assertFalse($download_complete_called);
                $download_before_send_called = true;
            } elseif ($request_method === 'GET') {
                \PHPUnit\Framework\Assert::assertFalse($get_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($get_success_called);
                \PHPUnit\Framework\Assert::assertFalse($get_error_called);
                \PHPUnit\Framework\Assert::assertFalse($get_complete_called);
                $get_before_send_called = true;
            }
            if ($request_method === 'HEAD') {
                \PHPUnit\Framework\Assert::assertFalse($head_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($head_success_called);
                \PHPUnit\Framework\Assert::assertFalse($head_error_called);
                \PHPUnit\Framework\Assert::assertFalse($head_complete_called);
                $head_before_send_called = true;
            }
            if ($request_method === 'OPTIONS') {
                \PHPUnit\Framework\Assert::assertFalse($options_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($options_success_called);
                \PHPUnit\Framework\Assert::assertFalse($options_error_called);
                \PHPUnit\Framework\Assert::assertFalse($options_complete_called);
                $options_before_send_called = true;
            }
            if ($request_method === 'PATCH') {
                \PHPUnit\Framework\Assert::assertFalse($patch_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($patch_success_called);
                \PHPUnit\Framework\Assert::assertFalse($patch_error_called);
                \PHPUnit\Framework\Assert::assertFalse($patch_complete_called);
                $patch_before_send_called = true;
            }
            if ($request_method === 'POST') {
                \PHPUnit\Framework\Assert::assertFalse($post_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($post_success_called);
                \PHPUnit\Framework\Assert::assertFalse($post_error_called);
                \PHPUnit\Framework\Assert::assertFalse($post_complete_called);
                $post_before_send_called = true;
            }
            if ($request_method === 'PUT') {
                \PHPUnit\Framework\Assert::assertFalse($put_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($put_success_called);
                \PHPUnit\Framework\Assert::assertFalse($put_error_called);
                \PHPUnit\Framework\Assert::assertFalse($put_complete_called);
                $put_before_send_called = true;
            }
        });
        $multi_curl->success(function ($instance) use (
            &$delete_success_called,
            &$download_success_called,
            &$get_success_called,
            &$head_success_called,
            &$options_success_called,
            &$patch_success_called,
            &$post_success_called,
            &$put_success_called
        ) {
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
            &$download_before_send_called, &$download_success_called, &$download_error_called,
            &$download_complete_called,
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called,
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called,
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called,
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called,
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called,
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            $request_method = $instance->getOpt(CURLOPT_CUSTOMREQUEST);
            if ($request_method === 'DELETE') {
                \PHPUnit\Framework\Assert::assertTrue($delete_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($delete_success_called);
                \PHPUnit\Framework\Assert::assertFalse($delete_error_called);
                \PHPUnit\Framework\Assert::assertFalse($delete_complete_called);
                $delete_error_called = true;
            }
            if (isset($instance->download)) {
                \PHPUnit\Framework\Assert::assertTrue($download_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($download_success_called);
                \PHPUnit\Framework\Assert::assertFalse($download_error_called);
                \PHPUnit\Framework\Assert::assertFalse($download_complete_called);
                $download_error_called = true;
            } elseif ($request_method === 'GET') {
                \PHPUnit\Framework\Assert::assertTrue($get_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($get_success_called);
                \PHPUnit\Framework\Assert::assertFalse($get_error_called);
                \PHPUnit\Framework\Assert::assertFalse($get_complete_called);
                $get_error_called = true;
            }
            if ($request_method === 'HEAD') {
                \PHPUnit\Framework\Assert::assertTrue($head_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($head_success_called);
                \PHPUnit\Framework\Assert::assertFalse($head_error_called);
                \PHPUnit\Framework\Assert::assertFalse($head_complete_called);
                $head_error_called = true;
            }
            if ($request_method === 'OPTIONS') {
                \PHPUnit\Framework\Assert::assertTrue($options_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($options_success_called);
                \PHPUnit\Framework\Assert::assertFalse($options_error_called);
                \PHPUnit\Framework\Assert::assertFalse($options_complete_called);
                $options_error_called = true;
            }
            if ($request_method === 'PATCH') {
                \PHPUnit\Framework\Assert::assertTrue($patch_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($patch_success_called);
                \PHPUnit\Framework\Assert::assertFalse($patch_error_called);
                \PHPUnit\Framework\Assert::assertFalse($patch_complete_called);
                $patch_error_called = true;
            }
            if ($request_method === 'POST') {
                \PHPUnit\Framework\Assert::assertTrue($post_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($post_success_called);
                \PHPUnit\Framework\Assert::assertFalse($post_error_called);
                \PHPUnit\Framework\Assert::assertFalse($post_complete_called);
                $post_error_called = true;
            }
            if ($request_method === 'PUT') {
                \PHPUnit\Framework\Assert::assertTrue($put_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($put_success_called);
                \PHPUnit\Framework\Assert::assertFalse($put_error_called);
                \PHPUnit\Framework\Assert::assertFalse($put_complete_called);
                $put_error_called = true;
            }
        });
        $multi_curl->complete(function ($instance) use (
            &$delete_before_send_called, &$delete_success_called, &$delete_error_called, &$delete_complete_called,
            &$download_before_send_called, &$download_success_called, &$download_error_called,
            &$download_complete_called,
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called,
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called,
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called,
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called,
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called,
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            $request_method = $instance->getOpt(CURLOPT_CUSTOMREQUEST);
            if ($request_method === 'DELETE') {
                \PHPUnit\Framework\Assert::assertTrue($delete_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($delete_success_called);
                \PHPUnit\Framework\Assert::assertTrue($delete_error_called);
                \PHPUnit\Framework\Assert::assertFalse($delete_complete_called);
                $delete_complete_called = true;
            }
            if (isset($instance->download)) {
                \PHPUnit\Framework\Assert::assertTrue($download_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($download_success_called);
                \PHPUnit\Framework\Assert::assertTrue($download_error_called);
                \PHPUnit\Framework\Assert::assertFalse($download_complete_called);
                $download_complete_called = true;
            } elseif ($request_method === 'GET') {
                \PHPUnit\Framework\Assert::assertTrue($get_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($get_success_called);
                \PHPUnit\Framework\Assert::assertTrue($get_error_called);
                \PHPUnit\Framework\Assert::assertFalse($get_complete_called);
                $get_complete_called = true;
            }
            if ($request_method === 'HEAD') {
                \PHPUnit\Framework\Assert::assertTrue($head_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($head_success_called);
                \PHPUnit\Framework\Assert::assertTrue($head_error_called);
                \PHPUnit\Framework\Assert::assertFalse($head_complete_called);
                $head_complete_called = true;
            }
            if ($request_method === 'OPTIONS') {
                \PHPUnit\Framework\Assert::assertTrue($options_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($options_success_called);
                \PHPUnit\Framework\Assert::assertTrue($options_error_called);
                \PHPUnit\Framework\Assert::assertFalse($options_complete_called);
                $options_complete_called = true;
            }
            if ($request_method === 'PATCH') {
                \PHPUnit\Framework\Assert::assertTrue($patch_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($patch_success_called);
                \PHPUnit\Framework\Assert::assertTrue($patch_error_called);
                \PHPUnit\Framework\Assert::assertFalse($patch_complete_called);
                $patch_complete_called = true;
            }
            if ($request_method === 'POST') {
                \PHPUnit\Framework\Assert::assertTrue($post_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($post_success_called);
                \PHPUnit\Framework\Assert::assertTrue($post_error_called);
                \PHPUnit\Framework\Assert::assertFalse($post_complete_called);
                $post_complete_called = true;
            }
            if ($request_method === 'PUT') {
                \PHPUnit\Framework\Assert::assertTrue($put_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($put_success_called);
                \PHPUnit\Framework\Assert::assertTrue($put_error_called);
                \PHPUnit\Framework\Assert::assertFalse($put_complete_called);
                $put_complete_called = true;
            }
            \PHPUnit\Framework\Assert::assertTrue($instance->error);
            \PHPUnit\Framework\Assert::assertTrue($instance->curlError);
            \PHPUnit\Framework\Assert::assertFalse($instance->httpError);
            \PHPUnit\Framework\Assert::assertEquals(CURLE_OPERATION_TIMEOUTED, $instance->errorCode);
            \PHPUnit\Framework\Assert::assertEquals(CURLE_OPERATION_TIMEOUTED, $instance->curlErrorCode);
        });

        $multi_curl->addDelete(Test::ERROR_URL);
        $download_file_path = tempnam('/tmp', 'php-curl-class.');
        $multi_curl->addDownload(Test::ERROR_URL, $download_file_path)->download = true;
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

        $this->assertTrue($download_before_send_called);
        $this->assertFalse($download_success_called);
        $this->assertTrue($download_error_called);
        $this->assertTrue($download_complete_called);
        $this->assertTrue(unlink($download_file_path));

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
        $multi_curl = new MultiCurl();

        $delete_before_send_called = false;
        $delete_success_called = false;
        $delete_error_called = false;
        $delete_complete_called = false;
        $delete = $multi_curl->addDelete(Test::TEST_URL);
        $delete->beforeSend(function ($instance) use (
            &$delete_before_send_called, &$delete_success_called, &$delete_error_called, &$delete_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($delete_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($delete_success_called);
            \PHPUnit\Framework\Assert::assertFalse($delete_error_called);
            \PHPUnit\Framework\Assert::assertFalse($delete_complete_called);
            $delete_before_send_called = true;
        });
        $delete->success(function ($instance) use (
            &$delete_before_send_called, &$delete_success_called, &$delete_error_called, &$delete_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($delete_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($delete_success_called);
            \PHPUnit\Framework\Assert::assertFalse($delete_error_called);
            \PHPUnit\Framework\Assert::assertFalse($delete_complete_called);
            $delete_success_called = true;
        });
        $delete->error(function ($instance) use (
            &$delete_error_called
        ) {
            $delete_error_called = true;
        });
        $delete->complete(function ($instance) use (
            &$delete_before_send_called, &$delete_success_called, &$delete_error_called, &$delete_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($delete_before_send_called);
            \PHPUnit\Framework\Assert::assertTrue($delete_success_called);
            \PHPUnit\Framework\Assert::assertFalse($delete_error_called);
            \PHPUnit\Framework\Assert::assertFalse($delete_complete_called);
            $delete_complete_called = true;
        });

        $download_before_send_called = false;
        $download_success_called = false;
        $download_error_called = false;
        $download_complete_called = false;
        $download_file_path = tempnam('/tmp', 'php-curl-class.');
        $download = $multi_curl->addDownload(Test::TEST_URL, $download_file_path);
        $download->beforeSend(function ($instance) use (
            &$download_before_send_called, &$download_success_called, &$download_error_called,
            &$download_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($download_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($download_success_called);
            \PHPUnit\Framework\Assert::assertFalse($download_error_called);
            \PHPUnit\Framework\Assert::assertFalse($download_complete_called);
            $download_before_send_called = true;
        });
        $download->success(function ($instance) use (
            &$download_before_send_called, &$download_success_called, &$download_error_called,
            &$download_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($download_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($download_success_called);
            \PHPUnit\Framework\Assert::assertFalse($download_error_called);
            \PHPUnit\Framework\Assert::assertFalse($download_complete_called);
            $download_success_called = true;
        });
        $download->error(function ($instance) use (
            &$download_error_called
        ) {
            $download_error_called = true;
        });
        $download->complete(function ($instance) use (
            &$download_before_send_called, &$download_success_called, &$download_error_called,
            &$download_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($download_before_send_called);
            \PHPUnit\Framework\Assert::assertTrue($download_success_called);
            \PHPUnit\Framework\Assert::assertFalse($download_error_called);
            \PHPUnit\Framework\Assert::assertFalse($download_complete_called);
            $download_complete_called = true;
        });

        $get_before_send_called = false;
        $get_success_called = false;
        $get_error_called = false;
        $get_complete_called = false;
        $get = $multi_curl->addGet(Test::TEST_URL);
        $get->beforeSend(function ($instance) use (
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($get_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($get_success_called);
            \PHPUnit\Framework\Assert::assertFalse($get_error_called);
            \PHPUnit\Framework\Assert::assertFalse($get_complete_called);
            $get_before_send_called = true;
        });
        $get->success(function ($instance) use (
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($get_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($get_success_called);
            \PHPUnit\Framework\Assert::assertFalse($get_error_called);
            \PHPUnit\Framework\Assert::assertFalse($get_complete_called);
            $get_success_called = true;
        });
        $get->error(function ($instance) use (
            &$get_error_called
        ) {
            $get_error_called = true;
        });
        $get->complete(function ($instance) use (
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($get_before_send_called);
            \PHPUnit\Framework\Assert::assertTrue($get_success_called);
            \PHPUnit\Framework\Assert::assertFalse($get_error_called);
            \PHPUnit\Framework\Assert::assertFalse($get_complete_called);
            $get_complete_called = true;
        });

        $head_before_send_called = false;
        $head_success_called = false;
        $head_error_called = false;
        $head_complete_called = false;
        $head = $multi_curl->addHead(Test::TEST_URL);
        $head->beforeSend(function ($instance) use (
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($head_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($head_success_called);
            \PHPUnit\Framework\Assert::assertFalse($head_error_called);
            \PHPUnit\Framework\Assert::assertFalse($head_complete_called);
            $head_before_send_called = true;
        });
        $head->success(function ($instance) use (
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($head_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($head_success_called);
            \PHPUnit\Framework\Assert::assertFalse($head_error_called);
            \PHPUnit\Framework\Assert::assertFalse($head_complete_called);
            $head_success_called = true;
        });
        $head->error(function ($instance) use (
            &$head_error_called
        ) {
            $head_error_called = true;
        });
        $head->complete(function ($instance) use (
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($head_before_send_called);
            \PHPUnit\Framework\Assert::assertTrue($head_success_called);
            \PHPUnit\Framework\Assert::assertFalse($head_error_called);
            \PHPUnit\Framework\Assert::assertFalse($head_complete_called);
            $head_complete_called = true;
        });

        $options_before_send_called = false;
        $options_success_called = false;
        $options_error_called = false;
        $options_complete_called = false;
        $options = $multi_curl->addOptions(Test::TEST_URL);
        $options->beforeSend(function ($instance) use (
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($options_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($options_success_called);
            \PHPUnit\Framework\Assert::assertFalse($options_error_called);
            \PHPUnit\Framework\Assert::assertFalse($options_complete_called);
            $options_before_send_called = true;
        });
        $options->success(function ($instance) use (
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($options_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($options_success_called);
            \PHPUnit\Framework\Assert::assertFalse($options_error_called);
            \PHPUnit\Framework\Assert::assertFalse($options_complete_called);
            $options_success_called = true;
        });
        $options->error(function ($instance) use (
            &$options_error_called
        ) {
            $options_error_called = true;
        });
        $options->complete(function ($instance) use (
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($options_before_send_called);
            \PHPUnit\Framework\Assert::assertTrue($options_success_called);
            \PHPUnit\Framework\Assert::assertFalse($options_error_called);
            \PHPUnit\Framework\Assert::assertFalse($options_complete_called);
            $options_complete_called = true;
        });

        $patch_before_send_called = false;
        $patch_success_called = false;
        $patch_error_called = false;
        $patch_complete_called = false;
        $patch = $multi_curl->addPatch(Test::TEST_URL);
        $patch->beforeSend(function ($instance) use (
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($patch_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($patch_success_called);
            \PHPUnit\Framework\Assert::assertFalse($patch_error_called);
            \PHPUnit\Framework\Assert::assertFalse($patch_complete_called);
            $patch_before_send_called = true;
        });
        $patch->success(function ($instance) use (
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($patch_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($patch_success_called);
            \PHPUnit\Framework\Assert::assertFalse($patch_error_called);
            \PHPUnit\Framework\Assert::assertFalse($patch_complete_called);
            $patch_success_called = true;
        });
        $patch->error(function ($instance) use (
            &$patch_error_called
        ) {
            $patch_error_called = true;
        });
        $patch->complete(function ($instance) use (
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($patch_before_send_called);
            \PHPUnit\Framework\Assert::assertTrue($patch_success_called);
            \PHPUnit\Framework\Assert::assertFalse($patch_error_called);
            \PHPUnit\Framework\Assert::assertFalse($patch_complete_called);
            $patch_complete_called = true;
        });

        $post_before_send_called = false;
        $post_success_called = false;
        $post_error_called = false;
        $post_complete_called = false;
        $post = $multi_curl->addPost(Test::TEST_URL);
        $post->beforeSend(function ($instance) use (
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($post_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($post_success_called);
            \PHPUnit\Framework\Assert::assertFalse($post_error_called);
            \PHPUnit\Framework\Assert::assertFalse($post_complete_called);
            $post_before_send_called = true;
        });
        $post->success(function ($instance) use (
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($post_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($post_success_called);
            \PHPUnit\Framework\Assert::assertFalse($post_error_called);
            \PHPUnit\Framework\Assert::assertFalse($post_complete_called);
            $post_success_called = true;
        });
        $post->error(function ($instance) use (
            &$post_error_called
        ) {
            $post_error_called = true;
        });
        $post->complete(function ($instance) use (
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($post_before_send_called);
            \PHPUnit\Framework\Assert::assertTrue($post_success_called);
            \PHPUnit\Framework\Assert::assertFalse($post_error_called);
            \PHPUnit\Framework\Assert::assertFalse($post_complete_called);
            $post_complete_called = true;
        });

        $put_before_send_called = false;
        $put_success_called = false;
        $put_error_called = false;
        $put_complete_called = false;
        $put = $multi_curl->addPut(Test::TEST_URL);
        $put->beforeSend(function ($instance) use (
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($put_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($put_success_called);
            \PHPUnit\Framework\Assert::assertFalse($put_error_called);
            \PHPUnit\Framework\Assert::assertFalse($put_complete_called);
            $put_before_send_called = true;
        });
        $put->success(function ($instance) use (
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($put_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($put_success_called);
            \PHPUnit\Framework\Assert::assertFalse($put_error_called);
            \PHPUnit\Framework\Assert::assertFalse($put_complete_called);
            $put_success_called = true;
        });
        $put->error(function ($instance) use (
            &$put_error_called
        ) {
            $put_error_called = true;
        });
        $put->complete(function ($instance) use (
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($put_before_send_called);
            \PHPUnit\Framework\Assert::assertTrue($put_success_called);
            \PHPUnit\Framework\Assert::assertFalse($put_error_called);
            \PHPUnit\Framework\Assert::assertFalse($put_complete_called);
            $put_complete_called = true;
        });

        $multi_curl->start();

        $this->assertTrue($delete_before_send_called);
        $this->assertTrue($delete_success_called);
        $this->assertFalse($delete_error_called);
        $this->assertTrue($delete_complete_called);

        $this->assertTrue($download_before_send_called);
        $this->assertTrue($download_success_called);
        $this->assertFalse($download_error_called);
        $this->assertTrue($download_complete_called);
        $this->assertTrue(unlink($download_file_path));

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

    public function testCurlCallbackError()
    {
        $multi_curl = new MultiCurl();

        $delete_before_send_called = false;
        $delete_success_called = false;
        $delete_error_called = false;
        $delete_complete_called = false;
        $delete = $multi_curl->addDelete(Test::ERROR_URL);
        $delete->beforeSend(function ($instance) use (
            &$delete_before_send_called, &$delete_success_called, &$delete_error_called, &$delete_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($delete_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($delete_success_called);
            \PHPUnit\Framework\Assert::assertFalse($delete_error_called);
            \PHPUnit\Framework\Assert::assertFalse($delete_complete_called);
            $delete_before_send_called = true;
        });
        $delete->success(function ($instance) use (
            &$delete_success_called
        ) {
            $delete_success_called = true;
        });
        $delete->error(function ($instance) use (
            &$delete_before_send_called, &$delete_success_called, &$delete_error_called, &$delete_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($delete_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($delete_success_called);
            \PHPUnit\Framework\Assert::assertFalse($delete_error_called);
            \PHPUnit\Framework\Assert::assertFalse($delete_complete_called);
            $delete_error_called = true;
        });
        $delete->complete(function ($instance) use (
            &$delete_before_send_called, &$delete_success_called, &$delete_error_called, &$delete_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($delete_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($delete_success_called);
            \PHPUnit\Framework\Assert::assertTrue($delete_error_called);
            \PHPUnit\Framework\Assert::assertFalse($delete_complete_called);
            $delete_complete_called = true;
        });

        $download_before_send_called = false;
        $download_success_called = false;
        $download_error_called = false;
        $download_complete_called = false;
        $download_file_path = tempnam('/tmp', 'php-curl-class.');
        $download = $multi_curl->addDownload(Test::ERROR_URL, $download_file_path);
        $download->beforeSend(function ($instance) use (
            &$download_before_send_called, &$download_success_called, &$download_error_called,
            &$download_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($download_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($download_success_called);
            \PHPUnit\Framework\Assert::assertFalse($download_error_called);
            \PHPUnit\Framework\Assert::assertFalse($download_complete_called);
            $download_before_send_called = true;
        });
        $download->success(function ($instance) use (
            &$download_success_called
        ) {
            $download_success_called = true;
        });
        $download->error(function ($instance) use (
            &$download_before_send_called, &$download_success_called, &$download_error_called,
            &$download_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($download_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($download_success_called);
            \PHPUnit\Framework\Assert::assertFalse($download_error_called);
            \PHPUnit\Framework\Assert::assertFalse($download_complete_called);
            $download_error_called = true;
        });
        $download->complete(function ($instance) use (
            &$download_before_send_called, &$download_success_called, &$download_error_called,
            &$download_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($download_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($download_success_called);
            \PHPUnit\Framework\Assert::assertTrue($download_error_called);
            \PHPUnit\Framework\Assert::assertFalse($download_complete_called);
            $download_complete_called = true;
        });

        $get_before_send_called = false;
        $get_success_called = false;
        $get_error_called = false;
        $get_complete_called = false;
        $get = $multi_curl->addGet(Test::ERROR_URL);
        $get->beforeSend(function ($instance) use (
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($get_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($get_success_called);
            \PHPUnit\Framework\Assert::assertFalse($get_error_called);
            \PHPUnit\Framework\Assert::assertFalse($get_complete_called);
            $get_before_send_called = true;
        });
        $get->success(function ($instance) use (
            &$get_success_called
        ) {
            $get_success_called = true;
        });
        $get->error(function ($instance) use (
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($get_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($get_success_called);
            \PHPUnit\Framework\Assert::assertFalse($get_error_called);
            \PHPUnit\Framework\Assert::assertFalse($get_complete_called);
            $get_error_called = true;
        });
        $get->complete(function ($instance) use (
            &$get_before_send_called, &$get_success_called, &$get_error_called, &$get_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($get_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($get_success_called);
            \PHPUnit\Framework\Assert::assertTrue($get_error_called);
            \PHPUnit\Framework\Assert::assertFalse($get_complete_called);
            $get_complete_called = true;
        });

        $head_before_send_called = false;
        $head_success_called = false;
        $head_error_called = false;
        $head_complete_called = false;
        $head = $multi_curl->addHead(Test::ERROR_URL);
        $head->beforeSend(function ($instance) use (
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($head_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($head_success_called);
            \PHPUnit\Framework\Assert::assertFalse($head_error_called);
            \PHPUnit\Framework\Assert::assertFalse($head_complete_called);
            $head_before_send_called = true;
        });
        $head->success(function ($instance) use (
            &$head_success_called
        ) {
            $head_success_called = true;
        });
        $head->error(function ($instance) use (
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($head_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($head_success_called);
            \PHPUnit\Framework\Assert::assertFalse($head_error_called);
            \PHPUnit\Framework\Assert::assertFalse($head_complete_called);
            $head_error_called = true;
        });
        $head->complete(function ($instance) use (
            &$head_before_send_called, &$head_success_called, &$head_error_called, &$head_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($head_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($head_success_called);
            \PHPUnit\Framework\Assert::assertTrue($head_error_called);
            \PHPUnit\Framework\Assert::assertFalse($head_complete_called);
            $head_complete_called = true;
        });

        $options_before_send_called = false;
        $options_success_called = false;
        $options_error_called = false;
        $options_complete_called = false;
        $options = $multi_curl->addOptions(Test::ERROR_URL);
        $options->beforeSend(function ($instance) use (
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($options_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($options_success_called);
            \PHPUnit\Framework\Assert::assertFalse($options_error_called);
            \PHPUnit\Framework\Assert::assertFalse($options_complete_called);
            $options_before_send_called = true;
        });
        $options->success(function ($instance) use (
            &$options_success_called
        ) {
            $options_success_called = true;
        });
        $options->error(function ($instance) use (
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($options_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($options_success_called);
            \PHPUnit\Framework\Assert::assertFalse($options_error_called);
            \PHPUnit\Framework\Assert::assertFalse($options_complete_called);
            $options_error_called = true;
        });
        $options->complete(function ($instance) use (
            &$options_before_send_called, &$options_success_called, &$options_error_called, &$options_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($options_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($options_success_called);
            \PHPUnit\Framework\Assert::assertTrue($options_error_called);
            \PHPUnit\Framework\Assert::assertFalse($options_complete_called);
            $options_complete_called = true;
        });

        $patch_before_send_called = false;
        $patch_success_called = false;
        $patch_error_called = false;
        $patch_complete_called = false;
        $patch = $multi_curl->addPatch(Test::ERROR_URL);
        $patch->beforeSend(function ($instance) use (
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($patch_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($patch_success_called);
            \PHPUnit\Framework\Assert::assertFalse($patch_error_called);
            \PHPUnit\Framework\Assert::assertFalse($patch_complete_called);
            $patch_before_send_called = true;
        });
        $patch->success(function ($instance) use (
            &$patch_success_called
        ) {
            $patch_success_called = true;
        });
        $patch->error(function ($instance) use (
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($patch_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($patch_success_called);
            \PHPUnit\Framework\Assert::assertFalse($patch_error_called);
            \PHPUnit\Framework\Assert::assertFalse($patch_complete_called);
            $patch_error_called = true;
        });
        $patch->complete(function ($instance) use (
            &$patch_before_send_called, &$patch_success_called, &$patch_error_called, &$patch_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($patch_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($patch_success_called);
            \PHPUnit\Framework\Assert::assertTrue($patch_error_called);
            \PHPUnit\Framework\Assert::assertFalse($patch_complete_called);
            $patch_complete_called = true;
        });

        $post_before_send_called = false;
        $post_success_called = false;
        $post_error_called = false;
        $post_complete_called = false;
        $post = $multi_curl->addPost(Test::ERROR_URL);
        $post->beforeSend(function ($instance) use (
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($post_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($post_success_called);
            \PHPUnit\Framework\Assert::assertFalse($post_error_called);
            \PHPUnit\Framework\Assert::assertFalse($post_complete_called);
            $post_before_send_called = true;
        });
        $post->success(function ($instance) use (
            &$post_success_called
        ) {
            $post_sucess_called = true;
        });
        $post->error(function ($instance) use (
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($post_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($post_success_called);
            \PHPUnit\Framework\Assert::assertFalse($post_error_called);
            \PHPUnit\Framework\Assert::assertFalse($post_complete_called);
            $post_error_called = true;
        });
        $post->complete(function ($instance) use (
            &$post_before_send_called, &$post_success_called, &$post_error_called, &$post_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($post_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($post_success_called);
            \PHPUnit\Framework\Assert::assertTrue($post_error_called);
            \PHPUnit\Framework\Assert::assertFalse($post_complete_called);
            $post_complete_called = true;
        });

        $put_before_send_called = false;
        $put_success_called = false;
        $put_error_called = false;
        $put_complete_called = false;
        $put = $multi_curl->addPut(Test::ERROR_URL);
        $put->beforeSend(function ($instance) use (
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($put_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($put_success_called);
            \PHPUnit\Framework\Assert::assertFalse($put_error_called);
            \PHPUnit\Framework\Assert::assertFalse($put_complete_called);
            $put_before_send_called = true;
        });
        $put->success(function ($instance) use (
            &$put_success_called
        ) {
            $put_success_called = true;
        });
        $put->error(function ($instance) use (
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($put_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($put_success_called);
            \PHPUnit\Framework\Assert::assertFalse($put_error_called);
            \PHPUnit\Framework\Assert::assertFalse($put_complete_called);
            $put_error_called = true;
        });
        $put->complete(function ($instance) use (
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($put_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($put_success_called);
            \PHPUnit\Framework\Assert::assertTrue($put_error_called);
            \PHPUnit\Framework\Assert::assertFalse($put_complete_called);
            $put_complete_called = true;
        });

        $multi_curl->start();

        $this->assertTrue($delete_before_send_called);
        $this->assertFalse($delete_success_called);
        $this->assertTrue($delete_error_called);
        $this->assertTrue($delete_complete_called);

        $this->assertTrue($download_before_send_called);
        $this->assertFalse($download_success_called);
        $this->assertTrue($download_error_called);
        $this->assertTrue($download_complete_called);
        $this->assertTrue(unlink($download_file_path));

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

    public function testCurlCallbackOverride()
    {
        $multi_curl = new MultiCurl();
        $multi_curl->beforeSend(function () {
            \PHPUnit\Framework\Assert::assertFalse(true);
        });
        $multi_curl->success(function () {
            \PHPUnit\Framework\Assert::assertFalse(true);
        });
        $multi_curl->error(function () {
            \PHPUnit\Framework\Assert::assertFalse(true);
        });
        $multi_curl->complete(function () {
            \PHPUnit\Framework\Assert::assertFalse(true);
        });

        $delete_before_send_called = false;
        $delete_success_called = false;
        $delete_error_called = false;
        $delete_complete_called = false;
        $delete = $multi_curl->addDelete(Test::TEST_URL);
        $delete->beforeSend(function ($instance) use (&$delete_before_send_called) {
            $delete_before_send_called = true;
        });
        $delete->success(function ($instance) use (&$delete_success_called) {
            $delete_success_called = true;
        });
        $delete->error(function ($instance) use (&$delete_error_called) {
            $delete_error_called = true;
        });
        $delete->complete(function ($instance) use (&$delete_complete_called) {
            $delete_complete_called = true;
        });

        $download_before_send_called = false;
        $download_success_called = false;
        $download_error_called = false;
        $download_complete_called = false;
        $download_file_path = tempnam('/tmp', 'php-curl-class.');
        $download = $multi_curl->addDownload(Test::TEST_URL, $download_file_path);
        $download->beforeSend(function ($instance) use (&$download_before_send_called) {
            $download_before_send_called = true;
        });
        $download->success(function ($instance) use (&$download_success_called) {
            $download_success_called = true;
        });
        $download->error(function ($instance) use (&$download_error_called) {
            $download_error_called = true;
        });
        $download->complete(function ($instance) use (&$download_complete_called) {
            $download_complete_called = true;
        });

        $get_before_send_called = false;
        $get_success_called = false;
        $get_error_called = false;
        $get_complete_called = false;
        $get = $multi_curl->addGet(Test::TEST_URL);
        $get->beforeSend(function ($instance) use (&$get_before_send_called) {
            $get_before_send_called = true;
        });
        $get->success(function ($instance) use (&$get_success_called) {
            $get_success_called = true;
        });
        $get->error(function ($instance) use (&$get_error_called) {
            $get_error_called = true;
        });
        $get->complete(function ($instance) use (&$get_complete_called) {
            $get_complete_called = true;
        });

        $head_before_send_called = false;
        $head_success_called = false;
        $head_error_called = false;
        $head_complete_called = false;
        $head = $multi_curl->addHead(Test::TEST_URL);
        $head->beforeSend(function ($instance) use (&$head_before_send_called) {
            $head_before_send_called = true;
        });
        $head->success(function ($instance) use (&$head_success_called) {
            $head_success_called = true;
        });
        $head->error(function ($instance) use (&$head_error_called) {
            $head_error_called = true;
        });
        $head->complete(function ($instance) use (&$head_complete_called) {
            $head_complete_called = true;
        });

        $options_before_send_called = false;
        $options_success_called = false;
        $options_error_called = false;
        $options_complete_called = false;
        $options = $multi_curl->addOptions(Test::TEST_URL);
        $options->beforeSend(function ($instance) use (&$options_before_send_called) {
            $options_before_send_called = true;
        });
        $options->success(function ($instance) use (&$options_success_called) {
            $options_success_called = true;
        });
        $options->error(function ($instance) use (&$options_error_called) {
            $options_error_called = true;
        });
        $options->complete(function ($instance) use (&$options_complete_called) {
            $options_complete_called = true;
        });

        $patch_before_send_called = false;
        $patch_success_called = false;
        $patch_error_called = false;
        $patch_complete_called = false;
        $patch = $multi_curl->addPatch(Test::TEST_URL);
        $patch->beforeSend(function ($instance) use (&$patch_before_send_called) {
            $patch_before_send_called = true;
        });
        $patch->success(function ($instance) use (&$patch_success_called) {
            $patch_success_called = true;
        });
        $patch->error(function ($instance) use (&$patch_error_called) {
            $patch_error_called = true;
        });
        $patch->complete(function ($instance) use (&$patch_complete_called) {
            $patch_complete_called = true;
        });

        $post_before_send_called = false;
        $post_success_called = false;
        $post_error_called = false;
        $post_complete_called = false;
        $post = $multi_curl->addPost(Test::TEST_URL);
        $post->beforeSend(function ($instance) use (&$post_before_send_called) {
            $post_before_send_called = true;
        });
        $post->success(function ($instance) use (&$post_success_called) {
            $post_success_called = true;
        });
        $post->error(function ($instance) use (&$post_error_called) {
            $post_error_called = true;
        });
        $post->complete(function ($instance) use (&$post_complete_called) {
            $post_complete_called = true;
        });

        $put_before_send_called = false;
        $put_success_called = false;
        $put_error_called = false;
        $put_complete_called = false;
        $put = $multi_curl->addPut(Test::TEST_URL);
        $put->beforeSend(function ($instance) use (&$put_before_send_called) {
            $put_before_send_called = true;
        });
        $put->success(function ($instance) use (&$put_success_called) {
            $put_success_called = true;
        });
        $put->error(function ($instance) use (&$put_error_called) {
            $put_error_called = true;
        });
        $put->complete(function ($instance) use (&$put_complete_called) {
            $put_complete_called = true;
        });

        $multi_curl->start();

        $this->assertTrue($delete_before_send_called);
        $this->assertTrue($delete_success_called);
        $this->assertFalse($delete_error_called);
        $this->assertTrue($delete_complete_called);

        $this->assertTrue($download_before_send_called);
        $this->assertTrue($download_success_called);
        $this->assertFalse($download_error_called);
        $this->assertTrue($download_complete_called);
        $this->assertTrue(unlink($download_file_path));

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

    public function testCurlCallbackAddedAfter()
    {
        $delete_before_send_called = false;
        $delete_success_called = false;
        $delete_error_called = false;
        $delete_complete_called = false;
        $multi_curl = new MultiCurl();
        $multi_curl->addDelete(Test::TEST_URL);
        $multi_curl->beforeSend(function ($instance) use (&$delete_before_send_called) {
            $delete_before_send_called = true;
        });
        $multi_curl->success(function ($instance) use (&$delete_success_called) {
            $delete_success_called = true;
        });
        $multi_curl->error(function ($instance) use (&$delete_error_called) {
            $delete_error_called = true;
        });
        $multi_curl->complete(function ($instance) use (&$delete_complete_called) {
            $delete_complete_called = true;
        });
        $multi_curl->start();
        $this->assertTrue($delete_before_send_called);
        $this->assertTrue($delete_success_called);
        $this->assertFalse($delete_error_called);
        $this->assertTrue($delete_complete_called);

        $download_before_send_called = false;
        $download_success_called = false;
        $download_error_called = false;
        $download_complete_called = false;
        $download_file_path = tempnam('/tmp', 'php-curl-class.');
        $multi_curl = new MultiCurl();
        $multi_curl->addDownload(Test::TEST_URL, $download_file_path);
        $multi_curl->beforeSend(function ($instance) use (&$download_before_send_called) {
            $download_before_send_called = true;
        });
        $multi_curl->success(function ($instance) use (&$download_success_called) {
            $download_success_called = true;
        });
        $multi_curl->error(function ($instance) use (&$download_error_called) {
            $download_error_called = true;
        });
        $multi_curl->complete(function ($instance) use (&$download_complete_called) {
            $download_complete_called = true;
        });
        $multi_curl->start();
        $this->assertTrue($download_before_send_called);
        $this->assertTrue($download_success_called);
        $this->assertFalse($download_error_called);
        $this->assertTrue($download_complete_called);
        $this->assertTrue(unlink($download_file_path));

        $get_before_send_called = false;
        $get_success_called = false;
        $get_error_called = false;
        $get_complete_called = false;
        $multi_curl = new MultiCurl();
        $multi_curl->addGet(Test::TEST_URL);
        $multi_curl->beforeSend(function ($instance) use (&$get_before_send_called) {
            $get_before_send_called = true;
        });
        $multi_curl->success(function ($instance) use (&$get_success_called) {
            $get_success_called = true;
        });
        $multi_curl->error(function ($instance) use (&$get_error_called) {
            $get_error_called = true;
        });
        $multi_curl->complete(function ($instance) use (&$get_complete_called) {
            $get_complete_called = true;
        });
        $multi_curl->start();
        $this->assertTrue($get_before_send_called);
        $this->assertTrue($get_success_called);
        $this->assertFalse($get_error_called);
        $this->assertTrue($get_complete_called);

        $head_before_send_called = false;
        $head_success_called = false;
        $head_error_called = false;
        $head_complete_called = false;
        $multi_curl = new MultiCurl();
        $multi_curl->addHead(Test::TEST_URL);
        $multi_curl->beforeSend(function ($instance) use (&$head_before_send_called) {
            $head_before_send_called = true;
        });
        $multi_curl->success(function ($instance) use (&$head_success_called) {
            $head_success_called = true;
        });
        $multi_curl->error(function ($instance) use (&$head_error_called) {
            $head_error_called = true;
        });
        $multi_curl->complete(function ($instance) use (&$head_complete_called) {
            $head_complete_called = true;
        });
        $multi_curl->start();
        $this->assertTrue($head_before_send_called);
        $this->assertTrue($head_success_called);
        $this->assertFalse($head_error_called);
        $this->assertTrue($head_complete_called);

        $options_before_send_called = false;
        $options_success_called = false;
        $options_error_called = false;
        $options_complete_called = false;
        $multi_curl = new MultiCurl();
        $multi_curl->addOptions(Test::TEST_URL);
        $multi_curl->beforeSend(function ($instance) use (&$options_before_send_called) {
            $options_before_send_called = true;
        });
        $multi_curl->success(function ($instance) use (&$options_success_called) {
            $options_success_called = true;
        });
        $multi_curl->error(function ($instance) use (&$options_error_called) {
            $options_error_called = true;
        });
        $multi_curl->complete(function ($instance) use (&$options_complete_called) {
            $options_complete_called = true;
        });
        $multi_curl->start();
        $this->assertTrue($options_before_send_called);
        $this->assertTrue($options_success_called);
        $this->assertFalse($options_error_called);
        $this->assertTrue($options_complete_called);

        $patch_before_send_called = false;
        $patch_success_called = false;
        $patch_error_called = false;
        $patch_complete_called = false;
        $multi_curl = new MultiCurl();
        $multi_curl->addPatch(Test::TEST_URL);
        $multi_curl->beforeSend(function ($instance) use (&$patch_before_send_called) {
            $patch_before_send_called = true;
        });
        $multi_curl->success(function ($instance) use (&$patch_success_called) {
            $patch_success_called = true;
        });
        $multi_curl->error(function ($instance) use (&$patch_error_called) {
            $patch_error_called = true;
        });
        $multi_curl->complete(function ($instance) use (&$patch_complete_called) {
            $patch_complete_called = true;
        });
        $multi_curl->start();
        $this->assertTrue($patch_before_send_called);
        $this->assertTrue($patch_success_called);
        $this->assertFalse($patch_error_called);
        $this->assertTrue($patch_complete_called);

        $post_before_send_called = false;
        $post_success_called = false;
        $post_error_called = false;
        $post_complete_called = false;
        $multi_curl = new MultiCurl();
        $multi_curl->addPost(Test::TEST_URL);
        $multi_curl->beforeSend(function ($instance) use (&$post_before_send_called) {
            $post_before_send_called = true;
        });
        $multi_curl->success(function ($instance) use (&$post_success_called) {
            $post_success_called = true;
        });
        $multi_curl->error(function ($instance) use (&$post_error_called) {
            $post_error_called = true;
        });
        $multi_curl->complete(function ($instance) use (&$post_complete_called) {
            $post_complete_called = true;
        });
        $multi_curl->start();
        $this->assertTrue($post_before_send_called);
        $this->assertTrue($post_success_called);
        $this->assertFalse($post_error_called);
        $this->assertTrue($post_complete_called);

        $put_before_send_called = false;
        $put_success_called = false;
        $put_error_called = false;
        $put_complete_called = false;
        $multi_curl = new MultiCurl();
        $multi_curl->addPut(Test::TEST_URL);
        $multi_curl->beforeSend(function ($instance) use (&$put_before_send_called) {
            $put_before_send_called = true;
        });
        $multi_curl->success(function ($instance) use (&$put_success_called) {
            $put_success_called = true;
        });
        $multi_curl->error(function ($instance) use (&$put_error_called) {
            $put_error_called = true;
        });
        $multi_curl->complete(function ($instance) use (&$put_complete_called) {
            $put_complete_called = true;
        });
        $multi_curl->start();
        $this->assertTrue($put_before_send_called);
        $this->assertTrue($put_success_called);
        $this->assertFalse($put_error_called);
        $this->assertTrue($put_complete_called);
    }

    public function testSetOptAndSetOptOverride()
    {
        $multi_curl_user_agent = 'multi curl user agent';
        $curl_user_agent = 'curl user agent';
        $data = array('key' => 'HTTP_USER_AGENT');

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'server');
        $multi_curl->setOpt(CURLOPT_USERAGENT, $multi_curl_user_agent);

        $get_1 = $multi_curl->addGet(Test::TEST_URL, $data);
        $get_1->complete(function ($instance) use ($multi_curl_user_agent) {
            \PHPUnit\Framework\Assert::assertEquals($multi_curl_user_agent, $instance->getOpt(CURLOPT_USERAGENT));
        });

        $get_2 = $multi_curl->addGet(Test::TEST_URL, $data);
        $get_2->complete(function ($instance) use ($multi_curl_user_agent) {
            \PHPUnit\Framework\Assert::assertEquals($multi_curl_user_agent, $instance->getOpt(CURLOPT_USERAGENT));
            \PHPUnit\Framework\Assert::assertEquals($multi_curl_user_agent, $instance->response);
        });

        $get_3 = $multi_curl->addGet(Test::TEST_URL, $data);
        $get_3->beforeSend(function ($instance) use ($curl_user_agent) {
            $instance->setOpt(CURLOPT_USERAGENT, $curl_user_agent);
        });
        $get_3->complete(function ($instance) use ($curl_user_agent) {
            \PHPUnit\Framework\Assert::assertEquals($curl_user_agent, $instance->getOpt(CURLOPT_USERAGENT));
            \PHPUnit\Framework\Assert::assertEquals($curl_user_agent, $instance->response);
        });

        $multi_curl->start();
    }

    public function testSetHeaderAndOverride()
    {
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('header-for-all-before', 'a');

        $get_1 = $multi_curl->addGet(Test::TEST_URL);
        $get_1->setHeader('header-for-1st-request', '1');
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->requestHeaders['header-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('b', $instance->requestHeaders['header-for-all-after']);
            \PHPUnit\Framework\Assert::assertEquals('1', $instance->requestHeaders['header-for-1st-request']);
        });

        $get_2 = $multi_curl->addGet(Test::TEST_URL);
        $get_2->setHeader('header-for-2nd-request', '2');
        $get_2->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->requestHeaders['header-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('b', $instance->requestHeaders['header-for-all-after']);
            \PHPUnit\Framework\Assert::assertEquals('2', $instance->requestHeaders['header-for-2nd-request']);
        });

        $multi_curl->setHeader('header-for-all-after', 'b');
        $multi_curl->start();
    }

    public function testBasicHttpAuthSuccess()
    {
        $username1 = 'myusername';
        $password1 = 'mypassword';
        $username2 = 'myotherusername';
        $password2 = 'myotherpassword';

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'http_basic_auth');
        $multi_curl->setBasicAuthentication($username1, $password1);

        $get_1 = $multi_curl->addGet(Test::TEST_URL);
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(CURLAUTH_BASIC, $instance->getOpt(CURLOPT_HTTPAUTH));
        });

        $get_2 = $multi_curl->addGet(Test::TEST_URL);
        $get_2->complete(function ($instance) use ($username1, $password1) {
            \PHPUnit\Framework\Assert::assertEquals($username1, $instance->response->username);
            \PHPUnit\Framework\Assert::assertEquals($password1, $instance->response->password);
            \PHPUnit\Framework\Assert::assertEquals(CURLAUTH_BASIC, $instance->getOpt(CURLOPT_HTTPAUTH));
        });

        $get_3 = $multi_curl->addGet(Test::TEST_URL);
        $get_3->beforeSend(function ($instance) use ($username2, $password2) {
            $instance->setBasicAuthentication($username2, $password2);
        });
        $get_3->complete(function ($instance) use ($username2, $password2) {
            \PHPUnit\Framework\Assert::assertEquals($username2, $instance->response->username);
            \PHPUnit\Framework\Assert::assertEquals($password2, $instance->response->password);
            \PHPUnit\Framework\Assert::assertEquals(CURLAUTH_BASIC, $instance->getOpt(CURLOPT_HTTPAUTH));
        });

        $multi_curl->start();
    }

    public function testDigestHttpAuthSuccess()
    {
        $username = 'myusername';
        $password = 'mypassword';
        $invalid_password = 'anotherpassword';

        // Ensure that http digest returns canceled when not using any http digest authentication.
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'http_digest_auth');

        $get_1 = $multi_curl->addGet(Test::TEST_URL);
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('canceled', $instance->response);
            \PHPUnit\Framework\Assert::assertEquals(401, $instance->httpStatusCode);
        });

        $multi_curl->start();

        // Ensure that http digest returns invalid when using incorrect http digest authentication.
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'http_digest_auth');
        $multi_curl->setDigestAuthentication($username, $invalid_password);
        $this->assertEquals(CURLAUTH_DIGEST, $multi_curl->getOpt(CURLOPT_HTTPAUTH));

        $get_1 = $multi_curl->addGet(Test::TEST_URL);
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(CURLAUTH_DIGEST, $instance->getOpt(CURLOPT_HTTPAUTH));
            \PHPUnit\Framework\Assert::assertEquals('invalid', $instance->response);
            \PHPUnit\Framework\Assert::assertEquals(401, $instance->httpStatusCode);
        });

        $multi_curl->start();

        // Ensure that http digest returns valid when using correct http digest authentication.
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'http_digest_auth');
        $multi_curl->setDigestAuthentication($username, $password);
        $this->assertEquals(CURLAUTH_DIGEST, $multi_curl->getOpt(CURLOPT_HTTPAUTH));

        $get_1 = $multi_curl->addGet(Test::TEST_URL);
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(CURLAUTH_DIGEST, $instance->getOpt(CURLOPT_HTTPAUTH));
            \PHPUnit\Framework\Assert::assertEquals('valid', $instance->response);
            \PHPUnit\Framework\Assert::assertEquals(200, $instance->httpStatusCode);
        });

        $multi_curl->start();

        // Ensure that http digest can return both invalid and valid when using
        // incorrect and correct authentication in the same MultiCurl.
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'http_digest_auth');
        $multi_curl->setDigestAuthentication($username, $password);
        $this->assertEquals(CURLAUTH_DIGEST, $multi_curl->getOpt(CURLOPT_HTTPAUTH));

        $get_1 = $multi_curl->addGet(Test::TEST_URL);
        $get_1->beforeSend(function ($instance) use ($username, $invalid_password) {
            $instance->setDigestAuthentication($username, $invalid_password);
            \PHPUnit\Framework\Assert::assertEquals(CURLAUTH_DIGEST, $instance->getOpt(CURLOPT_HTTPAUTH));
        });
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(CURLAUTH_DIGEST, $instance->getOpt(CURLOPT_HTTPAUTH));
            \PHPUnit\Framework\Assert::assertEquals('invalid', $instance->response);
            \PHPUnit\Framework\Assert::assertEquals(401, $instance->httpStatusCode);
        });

        $get_2 = $multi_curl->addGet(Test::TEST_URL);
        $get_2->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(CURLAUTH_DIGEST, $instance->getOpt(CURLOPT_HTTPAUTH));
            \PHPUnit\Framework\Assert::assertEquals('valid', $instance->response);
            \PHPUnit\Framework\Assert::assertEquals(200, $instance->httpStatusCode);
        });

        $multi_curl->start();
    }

    public function testSetCookie()
    {
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'setcookie');
        $multi_curl->setCookie('mycookie', 'yum');
        $multi_curl->setCookie('cookie-for-all-before', 'a');

        $get_1 = $multi_curl->addGet(Test::TEST_URL);
        $get_1->setCookie('cookie-for-1st-request', '1');
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('yum', $instance->responseCookies['mycookie']);
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->responseCookies['cookie-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('b', $instance->responseCookies['cookie-for-all-after']);
            \PHPUnit\Framework\Assert::assertEquals('1', $instance->responseCookies['cookie-for-1st-request']);
        });

        $get_2 = $multi_curl->addGet(Test::TEST_URL);
        $get_2->setCookie('cookie-for-2nd-request', '2');
        $get_2->beforeSend(function ($instance) {
            $instance->setCookie('mycookie', 'yummy');
        });
        $get_2->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('yummy', $instance->responseCookies['mycookie']);
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->responseCookies['cookie-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('b', $instance->responseCookies['cookie-for-all-after']);
            \PHPUnit\Framework\Assert::assertEquals('2', $instance->responseCookies['cookie-for-2nd-request']);
        });

        $multi_curl->setCookie('cookie-for-all-after', 'b');
        $multi_curl->start();

        $this->assertEquals('yum', $get_1->responseCookies['mycookie']);
        $this->assertEquals('yummy', $get_2->responseCookies['mycookie']);
    }

    public function testSetCookies()
    {
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'setcookie');
        $multi_curl->setCookies(array(
            'mycookie' => 'yum',
            'cookie-for-all-before' => 'a',
        ));

        $get_1 = $multi_curl->addGet(Test::TEST_URL);
        $get_1->setCookies(array(
            'cookie-for-1st-request' => '1',
        ));
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('yum', $instance->responseCookies['mycookie']);
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->responseCookies['cookie-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('b', $instance->responseCookies['cookie-for-all-after']);
            \PHPUnit\Framework\Assert::assertEquals('1', $instance->responseCookies['cookie-for-1st-request']);
        });

        $get_2 = $multi_curl->addGet(Test::TEST_URL);
        $get_2->setCookies(array(
            'cookie-for-2nd-request' => '2',
        ));
        $get_2->beforeSend(function ($instance) {
            $instance->setCookies(array(
                'mycookie' => 'yummy',
            ));
        });
        $get_2->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('yummy', $instance->responseCookies['mycookie']);
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->responseCookies['cookie-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('b', $instance->responseCookies['cookie-for-all-after']);
            \PHPUnit\Framework\Assert::assertEquals('2', $instance->responseCookies['cookie-for-2nd-request']);
        });

        $multi_curl->setCookies(array(
            'cookie-for-all-after' => 'b',
        ));
        $multi_curl->start();

        $this->assertEquals('yum', $get_1->responseCookies['mycookie']);
        $this->assertEquals('yummy', $get_2->responseCookies['mycookie']);
    }

    public function testJsonDecoder()
    {
        $data = array(
            'key' => 'Content-Type',
            'value' => 'application/json',
        );

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'json_response');

        $post_1 = $multi_curl->addPost(Test::TEST_URL, $data);
        $post_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_object($instance->response));
            \PHPUnit\Framework\Assert::assertFalse(is_array($instance->response));
        });

        $post_2 = $multi_curl->addPost(Test::TEST_URL, $data);
        $post_2->setJsonDecoder(function ($response) {
            return json_decode($response, true);
        });
        $post_2->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertFalse(is_object($instance->response));
            \PHPUnit\Framework\Assert::assertTrue(is_array($instance->response));
        });

        $post_3 = $multi_curl->addPost(Test::TEST_URL, $data);
        $post_3->setJsonDecoder(false);
        $post_3->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_string($instance->response));
        });

        $multi_curl->start();


        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'json_response');
        $multi_curl->setJsonDecoder(function ($response) {
            return 'foo';
        });

        $get_1 = $multi_curl->addGet(Test::TEST_URL);
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('foo', $instance->response);
        });

        $get_2 = $multi_curl->addGet(Test::TEST_URL);
        $get_2->beforeSend(function ($instance) {
            $instance->setJsonDecoder(function ($response) {
                return 'bar';
            });
        });
        $get_2->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('bar', $instance->response);
        });

        $get_3 = $multi_curl->addGet(Test::TEST_URL);
        $get_3->beforeSend(function ($instance) {
            $instance->setJsonDecoder(false);
        });
        $get_3->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_string($instance->response));
        });

        $multi_curl->start();
        $this->assertEquals('foo', $get_1->response);
        $this->assertEquals('bar', $get_2->response);


        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'json_response');
        $multi_curl->setJsonDecoder(false);

        $get_4 = $multi_curl->addGet(Test::TEST_URL);
        $get_4->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_string($instance->response));
        });

        $multi_curl->start();
    }

    public function testXMLDecoder()
    {
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'xml_with_cdata_response');

        $post_1 = $multi_curl->addPost(Test::TEST_URL);
        $post_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_object($instance->response));
            \PHPUnit\Framework\Assert::assertInstanceOf('SimpleXMLElement', $instance->response);
            \PHPUnit\Framework\Assert::assertFalse(strpos($instance->response->saveXML(), '<![CDATA[') === false);
        });

        $post_2 = $multi_curl->addPost(Test::TEST_URL);
        $post_2->setXmlDecoder(function ($response) {
            return simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        });
        $post_2->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_object($instance->response));
            \PHPUnit\Framework\Assert::assertInstanceOf('SimpleXMLElement', $instance->response);
            \PHPUnit\Framework\Assert::assertTrue(strpos($instance->response->saveXML(), '<![CDATA[') === false);
        });

        $post_3 = $multi_curl->addPost(Test::TEST_URL);
        $post_3->setXmlDecoder(false);
        $post_3->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_string($instance->response));
        });

        $multi_curl->start();


        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'xml_with_cdata_response');
        $multi_curl->setXmlDecoder(function ($response) {
            return 'foo';
        });

        $get_1 = $multi_curl->addGet(Test::TEST_URL);
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('foo', $instance->response);
        });

        $get_2 = $multi_curl->addGet(Test::TEST_URL);
        $get_2->beforeSend(function ($instance) {
            $instance->setXmlDecoder(function ($response) {
                return 'bar';
            });
        });
        $get_2->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('bar', $instance->response);
        });

        $get_3 = $multi_curl->addGet(Test::TEST_URL);
        $get_3->beforeSend(function ($instance) {
            $instance->setXmlDecoder(false);
        });
        $get_3->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_string($instance->response));
        });

        $multi_curl->start();
        $this->assertEquals('foo', $get_1->response);
        $this->assertEquals('bar', $get_2->response);


        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'xml_with_cdata_response');
        $multi_curl->setXmlDecoder(false);

        $get_4 = $multi_curl->addGet(Test::TEST_URL);
        $get_4->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_string($instance->response));
        });

        $multi_curl->start();
    }

    public function testDownloadCallback()
    {
        // Upload a file.
        $upload_file_path = \Helper\get_png();
        $upload_test = new Test();
        $upload_test->server('upload_response', 'POST', array(
            'image' => '@' . $upload_file_path,
        ));
        $uploaded_file_path = $upload_test->curl->response->file_path;

        // Download the file.
        $download_callback_called = false;
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'download_response');
        $multi_curl->addDownload(Test::TEST_URL . '?' . http_build_query(array(
            'file_path' => $uploaded_file_path,
        )), function ($instance, $fh) use (&$download_callback_called) {
            \PHPUnit\Framework\Assert::assertFalse($download_callback_called);
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue(is_resource($fh));
            \PHPUnit\Framework\Assert::assertEquals('stream', get_resource_type($fh));
            \PHPUnit\Framework\Assert::assertGreaterThan(0, strlen(stream_get_contents($fh)));
            \PHPUnit\Framework\Assert::assertEquals(0, strlen(stream_get_contents($fh)));
            \PHPUnit\Framework\Assert::assertTrue(fclose($fh));
            $download_callback_called = true;
        });
        $multi_curl->start();
        $this->assertTrue($download_callback_called);

        // Remove server file.
        $this->assertEquals('true', $upload_test->server('upload_cleanup', 'POST', array(
            'file_path' => $uploaded_file_path,
        )));

        unlink($upload_file_path);
        $this->assertFalse(file_exists($upload_file_path));
        $this->assertFalse(file_exists($uploaded_file_path));
    }

    public function testDownloadCallbackError()
    {
        $download_before_send_called = false;
        $download_callback_called = false;
        $multi_curl = new MultiCurl();
        $multi_curl->beforeSend(function ($instance) use (&$download_before_send_called) {
            \PHPUnit\Framework\Assert::assertFalse($download_before_send_called);
            $download_before_send_called = true;
        });
        $multi_curl->addDownload(Test::ERROR_URL, function ($instance, $fh) use (&$download_callback_called) {
            $download_callback_called = true;
        });
        $multi_curl->start();
        $this->assertTrue($download_before_send_called);
        $this->assertFalse($download_callback_called);
    }

    public function testSetUrlInConstructor()
    {
        $data = array('key' => 'value');

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'delete_with_body');
        $multi_curl->addDelete($data, array('wibble' => 'wubble'))->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL, $instance->baseUrl);
            \PHPUnit\Framework\Assert::assertEquals(
                '{"get":{"key":"value"},"delete":{"wibble":"wubble"}}',
                $instance->rawResponse
            );
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'get');
        $multi_curl->addDelete($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL, $instance->baseUrl);
            \PHPUnit\Framework\Assert::assertEquals('key=value', $instance->response);
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'get');
        $multi_curl->addGet($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL, $instance->baseUrl);
            \PHPUnit\Framework\Assert::assertEquals('key=value', $instance->response);
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'get');
        $multi_curl->addHead($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL, $instance->baseUrl);
            \PHPUnit\Framework\Assert::assertEquals(
                'HEAD /?key=value HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'get');
        $multi_curl->addOptions($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL, $instance->baseUrl);
            \PHPUnit\Framework\Assert::assertEquals('key=value', $instance->response);
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'request_method');
        $multi_curl->addPatch($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL, $instance->baseUrl);
            \PHPUnit\Framework\Assert::assertEquals('PATCH', $instance->response);
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'post');
        $multi_curl->addPost($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL, $instance->baseUrl);
            \PHPUnit\Framework\Assert::assertEquals('key=value', $instance->response);
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'put');
        $multi_curl->addPut($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL, $instance->baseUrl);
            \PHPUnit\Framework\Assert::assertEquals('key=value', $instance->response);
        });
        $multi_curl->start();
    }

    public function testSetUrl()
    {
        $data = array('key' => 'value');

        $multi_curl = new MultiCurl();
        $multi_curl->setUrl(Test::TEST_URL);
        $multi_curl->addDelete($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'DELETE /?key=value HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL . '?key=value', $instance->effectiveUrl);
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl();
        $multi_curl->setUrl(Test::TEST_URL);
        $multi_curl->addGet($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'GET /?key=value HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL . '?key=value', $instance->effectiveUrl);
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl();
        $multi_curl->setUrl(Test::TEST_URL);
        $multi_curl->addHead($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'HEAD /?key=value HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL . '?key=value', $instance->effectiveUrl);
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl();
        $multi_curl->setUrl(Test::TEST_URL);
        $multi_curl->addOptions($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'OPTIONS /?key=value HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL . '?key=value', $instance->effectiveUrl);
        });

        $multi_curl = new MultiCurl();
        $multi_curl->setUrl(Test::TEST_URL);
        $multi_curl->addPatch($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'PATCH / HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL, $instance->effectiveUrl);
        });

        $multi_curl = new MultiCurl();
        $multi_curl->setUrl(Test::TEST_URL);
        $multi_curl->addPost($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'POST / HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL, $instance->effectiveUrl);
        });

        $multi_curl = new MultiCurl();
        $multi_curl->setUrl(Test::TEST_URL);
        $multi_curl->addPut($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'PUT / HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL, $instance->effectiveUrl);
        });
    }

    public function testAddRequestAfterStart()
    {
        $multi_curl = new MultiCurl();

        $urls = array();
        $copy_of_urls = array();
        for ($i = 0; $i < 10; $i++) {
            $url = Test::TEST_URL . '?' . md5(mt_rand());
            $urls[] = $url;
            $copy_of_urls[] = $url;
        }

        $urls_called = array();
        $multi_curl->complete(function ($instance) use (&$multi_curl, &$urls, &$urls_called) {
            $urls_called[] = $instance->url;
            $next_url = array_pop($urls);
            if (!($next_url === null)) {
                $multi_curl->addGet($next_url);
            }
        });

        $multi_curl->addGet(array_pop($urls));
        $multi_curl->start();

        $this->assertNotEmpty($copy_of_urls);
        $this->assertNotEmpty($urls_called);
        $this->assertEquals(count($copy_of_urls), count($urls_called));

        foreach ($copy_of_urls as $url) {
            $this->assertTrue(in_array($url, $urls_called, true));
        }
    }

    public function testClose()
    {
        $multi_curl = new MultiCurl();
        $multi_curl->addGet(Test::TEST_URL);
        $multi_curl->start();
        $this->assertTrue(is_resource($multi_curl->multiCurl));
        $multi_curl->close();
        $this->assertFalse(is_resource($multi_curl->multiCurl));
    }

    public function testMultiPostRedirectGet()
    {
        // Deny post-redirect-get
        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $multi_curl->setHeader('X-DEBUG-TEST', 'post_redirect_get');
        $multi_curl->addPost(array(), false)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('Redirected: POST', $instance->response);
        });
        $multi_curl->start();

        // Allow post-redirect-get
        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $multi_curl->setHeader('X-DEBUG-TEST', 'post_redirect_get');
        $multi_curl->addPost(array(), true)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('Redirected: GET', $instance->response);
        });
        $multi_curl->start();
    }

    public function testAlternativeStandardErrorOutput()
    {

        $buffer = fopen('php://memory', 'w+');

        $multi_curl = new MultiCurl();
        $multi_curl->verbose(true, $buffer);
        $multi_curl->addGet(Test::TEST_URL);
        $multi_curl->start();

        rewind($buffer);
        $stderr = stream_get_contents($buffer);
        fclose($buffer);

        $this->assertNotEmpty($stderr);
    }

    public function testUnsetHeader()
    {
        $request_key = 'X-Request-Id';
        $request_value = '1';
        $data = array(
            'test' => 'server',
            'key' => 'HTTP_X_REQUEST_ID',
        );

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader($request_key, $request_value);
        $multi_curl->addGet(Test::TEST_URL, $data)->complete(function ($instance) use ($request_value) {
            \PHPUnit\Framework\Assert::assertEquals($request_value, $instance->response);
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader($request_key, $request_value);
        $multi_curl->unsetHeader($request_key);
        $multi_curl->addGet(Test::TEST_URL, $data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('', $instance->response);
        });
        $multi_curl->start();
    }

    public function testAddCurl()
    {
        $curl = new Curl();
        $curl->setUrl(Test::TEST_URL);
        $curl->setOpt(CURLOPT_CUSTOMREQUEST, 'GET');
        $curl->setOpt(CURLOPT_HTTPGET, true);

        $complete_called = false;
        $multi_curl = new MultiCurl();
        $multi_curl->addCurl($curl)->complete(function ($instance) use (&$complete_called) {
            $complete_called = true;
        });
        $multi_curl->start();
        $this->assertTrue($complete_called);
    }

    public function testSequentialId()
    {
        $multi_curl = new MultiCurl();
        $multi_curl->complete(function ($instance) {
            $sequential_id = $instance->getOpt(CURLOPT_POSTFIELDS);
            \PHPUnit\Framework\Assert::assertEquals($sequential_id, $instance->id);
        });

        for ($i = 0; $i < 100; $i++) {
            $multi_curl->addPost(Test::TEST_URL, $i);
        }

        $multi_curl->start();
    }

    public function testAscendingNumericalOrder()
    {
        $counter = 0;
        $multi_curl = new MultiCurl();
        $multi_curl->setConcurrency(1);
        $multi_curl->complete(function ($instance) use (&$counter) {
            $sequential_id = $instance->getOpt(CURLOPT_POSTFIELDS);
            \PHPUnit\Framework\Assert::assertEquals($counter, $sequential_id);
            $counter++;
        });

        for ($i = 0; $i < 100; $i++) {
            $multi_curl->addPost(Test::TEST_URL, $i);
        }

        $multi_curl->start();
    }

    public function testRetryMulti()
    {
        $tests = array(
            array(
                'maximum_number_of_retries' => null,
                'failures' => 0,
                'expect_success' => true,
                'expect_attempts' => 1,
                'expect_retries' => 0,
            ),
            array(
                'maximum_number_of_retries' => 0,
                'failures' => 0,
                'expect_success' => true,
                'expect_attempts' => 1,
                'expect_retries' => 0,
            ),
            array(
                'maximum_number_of_retries' => 0,
                'failures' => 1,
                'expect_success' => false,
                'expect_attempts' => 1,
                'expect_retries' => 0,
            ),
            array(
                'maximum_number_of_retries' => 1,
                'failures' => 1,
                'expect_success' => true,
                'expect_attempts' => 2,
                'expect_retries' => 1,
            ),
            array(
                'maximum_number_of_retries' => 1,
                'failures' => 2,
                'expect_success' => false,
                'expect_attempts' => 2,
                'expect_retries' => 1,
            ),
            array(
                'maximum_number_of_retries' => 2,
                'failures' => 2,
                'expect_success' => true,
                'expect_attempts' => 3,
                'expect_retries' => 2,
            ),
            array(
                'maximum_number_of_retries' => 3,
                'failures' => 3,
                'expect_success' => true,
                'expect_attempts' => 4,
                'expect_retries' => 3,
            ),
        );
        foreach ($tests as $test) {
            $maximum_number_of_retries = $test['maximum_number_of_retries'];
            $failures = $test['failures'];
            $expect_success = $test['expect_success'];
            $expect_attempts = $test['expect_attempts'];
            $expect_retries = $test['expect_retries'];

            $multi_curl = new MultiCurl();
            $multi_curl->setOpt(CURLOPT_COOKIEJAR, '/dev/null');
            $multi_curl->setHeader('X-DEBUG-TEST', 'retry');

            if (!($maximum_number_of_retries === null)) {
                $multi_curl->setRetry($maximum_number_of_retries);
            }

            $instance = $multi_curl->addGet(Test::TEST_URL, array('failures' => $failures));
            $multi_curl->start();

            $this->assertEquals($expect_success, !$instance->error);
            $this->assertEquals($expect_attempts, $instance->attempts);
            $this->assertEquals($expect_retries, $instance->retries);
        }
    }

    public function testRetryCallableMulti()
    {
        $tests = array(
            array(
                'maximum_number_of_retries' => null,
                'failures' => 0,
                'expect_success' => true,
                'expect_attempts' => 1,
                'expect_retries' => 0,
            ),
            array(
                'maximum_number_of_retries' => 0,
                'failures' => 0,
                'expect_success' => true,
                'expect_attempts' => 1,
                'expect_retries' => 0,
            ),
            array(
                'maximum_number_of_retries' => 0,
                'failures' => 1,
                'expect_success' => false,
                'expect_attempts' => 1,
                'expect_retries' => 0,
            ),
            array(
                'maximum_number_of_retries' => 1,
                'failures' => 1,
                'expect_success' => true,
                'expect_attempts' => 2,
                'expect_retries' => 1,
            ),
            array(
                'maximum_number_of_retries' => 1,
                'failures' => 2,
                'expect_success' => false,
                'expect_attempts' => 2,
                'expect_retries' => 1,
            ),
            array(
                'maximum_number_of_retries' => 2,
                'failures' => 2,
                'expect_success' => true,
                'expect_attempts' => 3,
                'expect_retries' => 2,
            ),
            array(
                'maximum_number_of_retries' => 3,
                'failures' => 3,
                'expect_success' => true,
                'expect_attempts' => 4,
                'expect_retries' => 3,
            ),
        );
        foreach ($tests as $test) {
            $maximum_number_of_retries = $test['maximum_number_of_retries'];
            $failures = $test['failures'];
            $expect_success = $test['expect_success'];
            $expect_attempts = $test['expect_attempts'];
            $expect_retries = $test['expect_retries'];

            $multi_curl = new MultiCurl();
            $multi_curl->setOpt(CURLOPT_COOKIEJAR, '/dev/null');
            $multi_curl->setHeader('X-DEBUG-TEST', 'retry');

            if (!($maximum_number_of_retries === null)) {
                $multi_curl->setRetry(function ($instance) use ($maximum_number_of_retries) {
                    $return = $instance->retries < $maximum_number_of_retries;
                    return $return;
                });
            }

            $instance = $multi_curl->addGet(Test::TEST_URL, array('failures' => $failures));
            $multi_curl->start();

            $this->assertEquals($expect_success, !$instance->error);
            $this->assertEquals($expect_attempts, $instance->attempts);
            $this->assertEquals($expect_retries, $instance->retries);
        }
    }
}
