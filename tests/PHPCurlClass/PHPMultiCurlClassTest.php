<?php

namespace CurlTest;

use Curl\Curl;
use Curl\Errors\CurlLockedException;
use Curl\MultiCurl;
use Helper\Test;

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
            if (isset($instance->downloadCompleteCallback)) {
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
            if ($request_method === 'POST' || $instance->getOpt(CURLOPT_POST)) {
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
            if (isset($instance->downloadCompleteCallback)) {
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
            if ($request_method === 'POST' || $instance->getOpt(CURLOPT_POST)) {
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
            if (isset($instance->downloadCompleteCallback)) {
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
            if ($request_method === 'POST' || $instance->getOpt(CURLOPT_POST)) {
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
        $multi_curl->addDownload(Test::TEST_URL, $download_file_path);
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

    /**
     * @group long
     */
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
            if (isset($instance->downloadCompleteCallback)) {
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
            if ($request_method === 'POST' || $instance->getOpt(CURLOPT_POST)) {
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
            if (isset($instance->downloadCompleteCallback)) {
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
            if ($request_method === 'POST' || $instance->getOpt(CURLOPT_POST)) {
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
            if (isset($instance->downloadCompleteCallback)) {
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
            if ($request_method === 'POST' || $instance->getOpt(CURLOPT_POST)) {
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
            $possible_errors = array(CURLE_OPERATION_TIMEOUTED, CURLE_COULDNT_CONNECT);
            \PHPUnit\Framework\Assert::assertContains($instance->errorCode, $possible_errors);
            \PHPUnit\Framework\Assert::assertContains($instance->curlErrorCode, $possible_errors);
        });

        $multi_curl->addDelete(Test::ERROR_URL);
        $download_file_path = tempnam('/tmp', 'php-curl-class.');
        $multi_curl->addDownload(Test::ERROR_URL, $download_file_path);
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

    public function testJsonRequest()
    {
        $data_and_expected_response = array(
            array(
                array(
                    'key' => 'value',
                ),
                '{"key":"value"}',
            ),
            array(
                array(
                    'key'     => 'value',
                    'strings' => array(
                        'a',
                        'b',
                        'c',
                    ),
                ),
                '{"key":"value","strings":["a","b","c"]}',
            ),
        );
        foreach ($data_and_expected_response as $test) {
            list($data, $expected_response) = $test;

            $multi_curl = new MultiCurl();
            $multi_curl->setHeader('X-DEBUG-TEST', 'post_json');
            $multi_curl->complete(function ($instance) use ($expected_response, $data) {
                \PHPUnit\Framework\Assert::assertEquals($expected_response, $instance->response);
            });
            $multi_curl->addPost(Test::TEST_URL, json_encode($data));
            $multi_curl->start();

            $keys = array(
                'Content-Type',
                'content-type',
                'CONTENT-TYPE'
            );
            $values = array(
                'APPLICATION/JSON',
                'APPLICATION/JSON; CHARSET=UTF-8',
                'APPLICATION/JSON;CHARSET=UTF-8',
                'application/json',
                'application/json; charset=utf-8',
                'application/json;charset=UTF-8',
            );
            foreach ($keys as $key) {
                foreach ($values as $value) {
                    $multi_curl = new MultiCurl();
                    $multi_curl->setHeader('X-DEBUG-TEST', 'post_json');
                    $multi_curl->setHeader($key, $value);
                    $multi_curl->complete(function ($instance) use ($expected_response, $data) {
                        \PHPUnit\Framework\Assert::assertEquals($expected_response, $instance->response);
                    });
                    $multi_curl->addPost(Test::TEST_URL, json_encode($data));
                    $multi_curl->start();

                    $multi_curl = new MultiCurl();
                    $multi_curl->setHeader('X-DEBUG-TEST', 'post_json');
                    $multi_curl->setHeader($key, $value);
                    $multi_curl->complete(function ($instance) use ($expected_response, $data) {
                        \PHPUnit\Framework\Assert::assertEquals($expected_response, $instance->response);
                    });
                    $multi_curl->addPost(Test::TEST_URL, $data);
                    $multi_curl->start();
                }
            }
        }
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
        $multi_curl->addDownload(
            Test::TEST_URL . '?' . http_build_query(array(
                'file_path' => $uploaded_file_path,
            )),
            function ($instance, $fh) use (&$download_callback_called) {
                \PHPUnit\Framework\Assert::assertFalse($download_callback_called);
                \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
                \PHPUnit\Framework\Assert::assertTrue(is_resource($fh));
                \PHPUnit\Framework\Assert::assertEquals('stream', get_resource_type($fh));
                \PHPUnit\Framework\Assert::assertGreaterThan(0, strlen(stream_get_contents($fh)));
                \PHPUnit\Framework\Assert::assertEquals(0, strlen(stream_get_contents($fh)));
                \PHPUnit\Framework\Assert::assertTrue(fclose($fh));
                $download_callback_called = true;
            }
        );
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
                'failures'                  => 0,
                'expect_success'            => true,
                'expect_attempts'           => 1,
                'expect_retries'            => 0,
            ),
            array(
                'maximum_number_of_retries' => 0,
                'failures'                  => 0,
                'expect_success'            => true,
                'expect_attempts'           => 1,
                'expect_retries'            => 0,
            ),
            array(
                'maximum_number_of_retries' => 0,
                'failures'                  => 1,
                'expect_success'            => false,
                'expect_attempts'           => 1,
                'expect_retries'            => 0,
            ),
            array(
                'maximum_number_of_retries' => 1,
                'failures'                  => 1,
                'expect_success'            => true,
                'expect_attempts'           => 2,
                'expect_retries'            => 1,
            ),
            array(
                'maximum_number_of_retries' => 1,
                'failures'                  => 2,
                'expect_success'            => false,
                'expect_attempts'           => 2,
                'expect_retries'            => 1,
            ),
            array(
                'maximum_number_of_retries' => 2,
                'failures'                  => 2,
                'expect_success'            => true,
                'expect_attempts'           => 3,
                'expect_retries'            => 2,
            ),
            array(
                'maximum_number_of_retries' => 3,
                'failures'                  => 3,
                'expect_success'            => true,
                'expect_attempts'           => 4,
                'expect_retries'            => 3,
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
                'failures'                  => 0,
                'expect_success'            => true,
                'expect_attempts'           => 1,
                'expect_retries'            => 0,
            ),
            array(
                'maximum_number_of_retries' => 0,
                'failures'                  => 0,
                'expect_success'            => true,
                'expect_attempts'           => 1,
                'expect_retries'            => 0,
            ),
            array(
                'maximum_number_of_retries' => 0,
                'failures'                  => 1,
                'expect_success'            => false,
                'expect_attempts'           => 1,
                'expect_retries'            => 0,
            ),
            array(
                'maximum_number_of_retries' => 1,
                'failures'                  => 1,
                'expect_success'            => true,
                'expect_attempts'           => 2,
                'expect_retries'            => 1,
            ),
            array(
                'maximum_number_of_retries' => 1,
                'failures'                  => 2,
                'expect_success'            => false,
                'expect_attempts'           => 2,
                'expect_retries'            => 1,
            ),
            array(
                'maximum_number_of_retries' => 2,
                'failures'                  => 2,
                'expect_success'            => true,
                'expect_attempts'           => 3,
                'expect_retries'            => 2,
            ),
            array(
                'maximum_number_of_retries' => 3,
                'failures'                  => 3,
                'expect_success'            => true,
                'expect_attempts'           => 4,
                'expect_retries'            => 3,
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

    public function testCurlLocking()
    {
        $multi_curl = new MultiCurl();

        $get = $multi_curl->addGet(Test::TEST_URL);
        $this->assertTrue($get->isLocked());
        $multi_curl->start();
        $this->assertFalse($get->isLocked());

        $head = $multi_curl->addHead(Test::TEST_URL);
        $this->assertTrue($head->isLocked());
        $multi_curl->start();
        $this->assertFalse($head->isLocked());

        $post = $multi_curl->addPost(Test::TEST_URL);
        $this->assertTrue($post->isLocked());
        $multi_curl->start();
        $this->assertFalse($post->isLocked());

        $put = $multi_curl->addPut(Test::TEST_URL);
        $this->assertTrue($put->isLocked());
        $multi_curl->start();
        $this->assertFalse($put->isLocked());

        $patch = $multi_curl->addPatch(Test::TEST_URL);
        $this->assertTrue($patch->isLocked());
        $multi_curl->start();
        $this->assertFalse($patch->isLocked());

        $delete = $multi_curl->addDelete(Test::TEST_URL);
        $this->assertTrue($delete->isLocked());
        $multi_curl->start();
        $this->assertFalse($delete->isLocked());

        $options = $multi_curl->addOptions(Test::TEST_URL);
        $this->assertTrue($options->isLocked());
        $multi_curl->start();
        $this->assertFalse($options->isLocked());

        $search = $multi_curl->addSearch(Test::TEST_URL);
        $this->assertTrue($search->isLocked());
        $multi_curl->start();
        $this->assertFalse($search->isLocked());

        $download_file_path = tempnam('/tmp', 'php-curl-class.');
        $download = $multi_curl->addDownload(Test::TEST_URL, $download_file_path);
        $this->assertTrue($download->isLocked());
        $multi_curl->start();
        $this->assertFalse($download->isLocked());

        $curl = new Curl(Test::TEST_URL);
        $multi_curl->addCurl($curl);
        $this->assertTrue($curl->isLocked());
        $multi_curl->start();
        $this->assertFalse($curl->isLocked());
    }

    public function testChangeLockedCurl()
    {
        $multi_curl = new MultiCurl();

        $raised_on_before_send = false;
        $raised_on_success = false;
        $raised_on_error = false;
        $raised_on_complete = false;

        $before_send_called = false;
        $success_called = false;
        $error_called = false;
        $complete_called = false;

        $curl = new Curl(Test::TEST_URL);
        $multi_curl->addCurl($curl);
        try {
            $curl->beforeSend(function ($instance) use (&$before_send_called) {
                $before_send_called = true;
            });
        } catch (CurlLockedException $e) {
            $raised_on_before_send = true;
        }
        try {
            $curl->success(function ($instance) use (&$success_called) {
                $success_called = true;
            });
        } catch (CurlLockedException $e) {
            $raised_on_success = true;
        }
        try {
            $curl->error(function ($instance) use (&$error_called) {
                $error_called = true;
            });
        } catch (CurlLockedException $e) {
            $raised_on_error = true;
        }
        try {
            $curl->complete(function ($instance) use (&$complete_called) {
                $complete_called = true;
            });
        } catch (CurlLockedException $e) {
            $raised_on_complete = true;
        }

        $this->assertTrue($raised_on_before_send);
        $this->assertTrue($raised_on_success);
        $this->assertTrue($raised_on_error);
        $this->assertTrue($raised_on_complete);

        $this->assertFalse($before_send_called);
        $this->assertFalse($success_called);
        $this->assertFalse($error_called);
        $this->assertFalse($complete_called);

        $raised_on_set_header = false;
        $raised_on_set_headers = false;
        $raised_on_set_cookie = false;
        $raised_on_set_cookies = false;
        $raised_on_set_opt = false;
        $raised_on_set_opts = false;

        try {
            $curl->setHeader('some-header', 'some-header-value');
        } catch (CurlLockedException $e) {
            $raised_on_set_header = true;
        }
        try {
            $curl->setHeaders(array('some-header' => 'some-header-value'));
        } catch (CurlLockedException $e) {
            $raised_on_set_headers = true;
        }
        try {
            $curl->setCookie('some-cookie', 'some-cookie-value');
        } catch (CurlLockedException $e) {
            $raised_on_set_cookie = true;
        }
        try {
            $curl->setCookies(array('some-cookie' => 'some-cookie-value'));
        } catch (CurlLockedException $e) {
            $raised_on_set_cookies = true;
        }
        try {
            $curl->setOpt('some-opt', 'some-opt-value');
        } catch (CurlLockedException $e) {
            $raised_on_set_opt = true;
        }
        try {
            $curl->setOpts(array('some-opt' => 'some-opt-value'));
        } catch (CurlLockedException $e) {
            $raised_on_set_opts = true;
        }

        $this->assertTrue($raised_on_set_header);
        $this->assertTrue($raised_on_set_headers);
        $this->assertTrue($raised_on_set_cookie);
        $this->assertTrue($raised_on_set_cookies);
        $this->assertTrue($raised_on_set_opt);
        $this->assertTrue($raised_on_set_opts);
    }
}
