<?php

namespace CurlTest;

use Curl\Curl;
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

        $search_before_send_called = false;
        $search_success_called = false;
        $search_error_called = false;
        $search_complete_called = false;

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
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called,
            &$search_before_send_called, &$search_success_called, &$search_error_called, &$search_complete_called
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
            if ($request_method === 'SEARCH') {
                \PHPUnit\Framework\Assert::assertFalse($search_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($search_success_called);
                \PHPUnit\Framework\Assert::assertFalse($search_error_called);
                \PHPUnit\Framework\Assert::assertFalse($search_complete_called);
                $search_before_send_called = true;
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
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called,
            &$search_before_send_called, &$search_success_called, &$search_error_called, &$search_complete_called
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
            if ($request_method === 'SEARCH') {
                \PHPUnit\Framework\Assert::assertTrue($search_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($search_success_called);
                \PHPUnit\Framework\Assert::assertFalse($search_error_called);
                \PHPUnit\Framework\Assert::assertFalse($search_complete_called);
                $search_success_called = true;
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
            &$put_error_called,
            &$search_error_called
        ) {
            $delete_error_called = true;
            $download_error_called = true;
            $get_error_called = true;
            $head_error_called = true;
            $options_error_called = true;
            $patch_error_called = true;
            $post_error_called = true;
            $put_error_called = true;
            $search_error_called = true;
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
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called,
            &$search_before_send_called, &$search_success_called, &$search_error_called, &$search_complete_called
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
            if ($request_method === 'SEARCH') {
                \PHPUnit\Framework\Assert::assertTrue($search_before_send_called);
                \PHPUnit\Framework\Assert::assertTrue($search_success_called);
                \PHPUnit\Framework\Assert::assertFalse($search_error_called);
                \PHPUnit\Framework\Assert::assertFalse($search_complete_called);
                $search_complete_called = true;
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
        $multi_curl->addSearch(Test::TEST_URL);
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

        $this->assertTrue($search_before_send_called);
        $this->assertTrue($search_success_called);
        $this->assertFalse($search_error_called);
        $this->assertTrue($search_complete_called);
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

        $search_before_send_called = false;
        $search_success_called = false;
        $search_error_called = false;
        $search_complete_called = false;

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
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called,
            &$search_before_send_called, &$search_success_called, &$search_error_called, &$search_complete_called
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
            if ($request_method === 'SEARCH') {
                \PHPUnit\Framework\Assert::assertFalse($search_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($search_success_called);
                \PHPUnit\Framework\Assert::assertFalse($search_error_called);
                \PHPUnit\Framework\Assert::assertFalse($search_complete_called);
                $search_before_send_called = true;
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
            &$put_success_called,
            &$search_success_called
        ) {
            $delete_success_called = true;
            $get_success_called = true;
            $head_success_called = true;
            $options_success_called = true;
            $patch_success_called = true;
            $post_success_called = true;
            $put_success_called = true;
            $search_success_called = true;
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
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called,
            &$search_before_send_called, &$search_success_called, &$search_error_called, &$search_complete_called
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
            if ($request_method === 'SEARCH') {
                \PHPUnit\Framework\Assert::assertTrue($search_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($search_success_called);
                \PHPUnit\Framework\Assert::assertFalse($search_error_called);
                \PHPUnit\Framework\Assert::assertFalse($search_complete_called);
                $search_error_called = true;
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
            &$put_before_send_called, &$put_success_called, &$put_error_called, &$put_complete_called,
            &$search_before_send_called, &$search_success_called, &$search_error_called, &$search_complete_called
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
            if ($request_method === 'SEARCH') {
                \PHPUnit\Framework\Assert::assertTrue($search_before_send_called);
                \PHPUnit\Framework\Assert::assertFalse($search_success_called);
                \PHPUnit\Framework\Assert::assertTrue($search_error_called);
                \PHPUnit\Framework\Assert::assertFalse($search_complete_called);
                $search_complete_called = true;
            }
            \PHPUnit\Framework\Assert::assertTrue($instance->error);
            \PHPUnit\Framework\Assert::assertTrue($instance->curlError);
            \PHPUnit\Framework\Assert::assertFalse($instance->httpError);
            $possible_errors = [
                CURLE_SEND_ERROR, CURLE_OPERATION_TIMEOUTED, CURLE_COULDNT_CONNECT, CURLE_GOT_NOTHING];
            \PHPUnit\Framework\Assert::assertTrue(
                in_array($instance->errorCode, $possible_errors, true),
                'errorCode: ' . $instance->errorCode
            );
            \PHPUnit\Framework\Assert::assertTrue(
                in_array($instance->curlErrorCode, $possible_errors, true),
                'curlErrorCode: ' . $instance->curlErrorCode
            );
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
        $multi_curl->addSearch(Test::ERROR_URL);
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

        $this->assertTrue($search_before_send_called);
        $this->assertFalse($search_success_called);
        $this->assertTrue($search_error_called);
        $this->assertTrue($search_complete_called);
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

        $search_before_send_called = false;
        $search_success_called = false;
        $search_error_called = false;
        $search_complete_called = false;
        $search = $multi_curl->addSearch(Test::TEST_URL);
        $search->beforeSend(function ($instance) use (
            &$search_before_send_called, &$search_success_called, &$search_error_called, &$search_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($search_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($search_success_called);
            \PHPUnit\Framework\Assert::assertFalse($search_error_called);
            \PHPUnit\Framework\Assert::assertFalse($search_complete_called);
            $search_before_send_called = true;
        });
        $search->success(function ($instance) use (
            &$search_before_send_called, &$search_success_called, &$search_error_called, &$search_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($search_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($search_success_called);
            \PHPUnit\Framework\Assert::assertFalse($search_error_called);
            \PHPUnit\Framework\Assert::assertFalse($search_complete_called);
            $search_success_called = true;
        });
        $search->error(function ($instance) use (
            &$search_error_called
        ) {
            $search_error_called = true;
        });
        $search->complete(function ($instance) use (
            &$search_before_send_called, &$search_success_called, &$search_error_called, &$search_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($search_before_send_called);
            \PHPUnit\Framework\Assert::assertTrue($search_success_called);
            \PHPUnit\Framework\Assert::assertFalse($search_error_called);
            \PHPUnit\Framework\Assert::assertFalse($search_complete_called);
            $search_complete_called = true;
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

        $this->assertTrue($search_before_send_called);
        $this->assertTrue($search_success_called);
        $this->assertFalse($search_error_called);
        $this->assertTrue($search_complete_called);
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

        $search_before_send_called = false;
        $search_success_called = false;
        $search_error_called = false;
        $search_complete_called = false;
        $search = $multi_curl->addSearch(Test::ERROR_URL);
        $search->beforeSend(function ($instance) use (
            &$search_before_send_called, &$search_success_called, &$search_error_called, &$search_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertFalse($search_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($search_success_called);
            \PHPUnit\Framework\Assert::assertFalse($search_error_called);
            \PHPUnit\Framework\Assert::assertFalse($search_complete_called);
            $search_before_send_called = true;
        });
        $search->success(function ($instance) use (
            &$search_success_called
        ) {
            $search_success_called = true;
        });
        $search->error(function ($instance) use (
            &$search_before_send_called, &$search_success_called, &$search_error_called, &$search_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($search_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($search_success_called);
            \PHPUnit\Framework\Assert::assertFalse($search_error_called);
            \PHPUnit\Framework\Assert::assertFalse($search_complete_called);
            $search_error_called = true;
        });
        $search->complete(function ($instance) use (
            &$search_before_send_called, &$search_success_called, &$search_error_called, &$search_complete_called
        ) {
            \PHPUnit\Framework\Assert::assertInstanceOf('Curl\Curl', $instance);
            \PHPUnit\Framework\Assert::assertTrue($search_before_send_called);
            \PHPUnit\Framework\Assert::assertFalse($search_success_called);
            \PHPUnit\Framework\Assert::assertTrue($search_error_called);
            \PHPUnit\Framework\Assert::assertFalse($search_complete_called);
            $search_complete_called = true;
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

        $this->assertTrue($search_before_send_called);
        $this->assertFalse($search_success_called);
        $this->assertTrue($search_error_called);
        $this->assertTrue($search_complete_called);
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

        $search_before_send_called = false;
        $search_success_called = false;
        $search_error_called = false;
        $search_complete_called = false;
        $search = $multi_curl->addsearch(Test::TEST_URL);
        $search->beforeSend(function ($instance) use (&$search_before_send_called) {
            $search_before_send_called = true;
        });
        $search->success(function ($instance) use (&$search_success_called) {
            $search_success_called = true;
        });
        $search->error(function ($instance) use (&$search_error_called) {
            $search_error_called = true;
        });
        $search->complete(function ($instance) use (&$search_complete_called) {
            $search_complete_called = true;
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

        $this->assertTrue($search_before_send_called);
        $this->assertTrue($search_success_called);
        $this->assertFalse($search_error_called);
        $this->assertTrue($search_complete_called);
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

        $search_before_send_called = false;
        $search_success_called = false;
        $search_error_called = false;
        $search_complete_called = false;
        $multi_curl = new MultiCurl();
        $multi_curl->addSearch(Test::TEST_URL);
        $multi_curl->beforeSend(function ($instance) use (&$search_before_send_called) {
            $search_before_send_called = true;
        });
        $multi_curl->success(function ($instance) use (&$search_success_called) {
            $search_success_called = true;
        });
        $multi_curl->error(function ($instance) use (&$search_error_called) {
            $search_error_called = true;
        });
        $multi_curl->complete(function ($instance) use (&$search_complete_called) {
            $search_complete_called = true;
        });
        $multi_curl->start();
        $this->assertTrue($search_before_send_called);
        $this->assertTrue($search_success_called);
        $this->assertFalse($search_error_called);
        $this->assertTrue($search_complete_called);
    }

    public function testSetOptAndSetOptOverride()
    {
        $multi_curl_user_agent = 'multi curl user agent';
        $curl_user_agent = 'curl user agent';
        $data = ['key' => 'HTTP_USER_AGENT'];

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
        $multi_curl->setCookies([
            'mycookie' => 'yum',
            'cookie-for-all-before' => 'a',
        ]);

        $get_1 = $multi_curl->addGet(Test::TEST_URL);
        $get_1->setCookies([
            'cookie-for-1st-request' => '1',
        ]);
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('yum', $instance->responseCookies['mycookie']);
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->responseCookies['cookie-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('b', $instance->responseCookies['cookie-for-all-after']);
            \PHPUnit\Framework\Assert::assertEquals('1', $instance->responseCookies['cookie-for-1st-request']);
        });

        $get_2 = $multi_curl->addGet(Test::TEST_URL);
        $get_2->setCookies([
            'cookie-for-2nd-request' => '2',
        ]);
        $get_2->beforeSend(function ($instance) {
            $instance->setCookies([
                'mycookie' => 'yummy',
            ]);
        });
        $get_2->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('yummy', $instance->responseCookies['mycookie']);
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->responseCookies['cookie-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('b', $instance->responseCookies['cookie-for-all-after']);
            \PHPUnit\Framework\Assert::assertEquals('2', $instance->responseCookies['cookie-for-2nd-request']);
        });

        $multi_curl->setCookies([
            'cookie-for-all-after' => 'b',
        ]);
        $multi_curl->start();

        $this->assertEquals('yum', $get_1->responseCookies['mycookie']);
        $this->assertEquals('yummy', $get_2->responseCookies['mycookie']);
    }

    public function testJsonRequest()
    {
        foreach ([
                [
                    [
                        'key' => 'value',
                    ],
                    '{"key":"value"}',
                ],
                [
                    [
                        'key' => 'value',
                        'strings' => [
                            'a',
                            'b',
                            'c',
                        ],
                    ],
                    '{"key":"value","strings":["a","b","c"]}',
                ],
            ] as $test) {
            list($data, $expected_response) = $test;

            $multi_curl = new MultiCurl();
            $multi_curl->setHeader('X-DEBUG-TEST', 'post_json');
            $multi_curl->complete(function ($instance) use ($expected_response, $data) {
                \PHPUnit\Framework\Assert::assertEquals($expected_response, $instance->response);
            });
            $multi_curl->addPost(Test::TEST_URL, json_encode($data));
            $multi_curl->start();

            foreach ([
                'Content-Type',
                'content-type',
                'CONTENT-TYPE'] as $key) {
                foreach ([
                    'APPLICATION/JSON',
                    'APPLICATION/JSON; CHARSET=UTF-8',
                    'APPLICATION/JSON;CHARSET=UTF-8',
                    'application/json',
                    'application/json; charset=utf-8',
                    'application/json;charset=UTF-8',
                    ] as $value) {
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

    public function testJsonDecoder()
    {
        $data = [
            'key' => 'Content-Type',
            'value' => 'application/json',
        ];

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'json_response');
        $multi_curl->setJsonDecoder(function ($response) {
            return 'first decoder';
        });

        $post_1 = $multi_curl->addPost(Test::TEST_URL, $data);
        $post_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('first decoder', $instance->response);
        });

        $post_2 = $multi_curl->addPost(Test::TEST_URL, $data);
        $post_2->setJsonDecoder(function ($response) {
            return 'second decoder';
        });
        $post_2->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('second decoder', $instance->response);
        });

        $multi_curl->start();
        $this->assertEquals('first decoder', $post_1->response);
        $this->assertEquals('second decoder', $post_2->response);


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
        $multi_curl->setXmlDecoder(function ($response) {
            return 'first decoder';
        });

        $post_1 = $multi_curl->addPost(Test::TEST_URL);
        $post_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('first decoder', $instance->response);
        });

        $post_2 = $multi_curl->addPost(Test::TEST_URL);
        $post_2->setXmlDecoder(function ($response) {
            return 'second decoder';
        });
        $post_2->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('second decoder', $instance->response);
        });

        $multi_curl->start();
        $this->assertEquals('first decoder', $post_1->response);
        $this->assertEquals('second decoder', $post_2->response);


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

    public function testDownload()
    {
        // Create and upload a file.
        $upload_file_path = \Helper\get_png();
        $uploaded_file_path = \Helper\upload_file_to_server($upload_file_path);

        // Download the file.
        $downloaded_file_path = tempnam('/tmp', 'php-curl-class.');
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'download_response');
        $multi_curl->addDownload(Test::TEST_URL . '?' . http_build_query([
            'file_path' => $uploaded_file_path,
        ]), $downloaded_file_path);
        $multi_curl->complete(function ($instance) use ($upload_file_path) {
            \PHPUnit\Framework\Assert::assertFalse($instance->error);
            \PHPUnit\Framework\Assert::assertEquals(md5_file($upload_file_path), $instance->responseHeaders['ETag']);
        });
        $multi_curl->start();
        $this->assertNotEquals($uploaded_file_path, $downloaded_file_path);

        $this->assertEquals(filesize($upload_file_path), filesize($downloaded_file_path));
        $this->assertEquals(md5_file($upload_file_path), md5_file($downloaded_file_path));

        // Remove server file.
        \Helper\remove_file_from_server($uploaded_file_path);

        unlink($upload_file_path);
        unlink($downloaded_file_path);
        $this->assertFalse(file_exists($upload_file_path));
        $this->assertFalse(file_exists($downloaded_file_path));
    }

    public function testDownloadRange()
    {
        // Create and upload a file.
        $filename = \Helper\get_png();
        $uploaded_file_path = \Helper\upload_file_to_server($filename);

        $filesize = filesize($filename);

        foreach ([
                false,
                0,
                1,
                2,
                3,
                5,
                10,
                25,
                50,
                $filesize - 3,
                $filesize - 2,
                $filesize - 1,

                // A partial temporary file having the exact same file size as the complete source file should only
                // occur under certain circumstances (almost never). When the download successfully completed, the
                // temporary file should have been moved to the download destination save path. However, it is possible
                // that a larger file download was interrupted after which the source file was updated and now has the
                // exact same file size as the partial temporary. When resuming the download, the range is now
                // unsatisfiable as the first byte position exceeds the available range. The entire file should be
                // downloaded again.
                $filesize - 0,

                // A partial temporary file having a larger file size than the complete source file should only occur
                // under certain circumstances. This is possible when a download was interrupted after which the source
                // file was updated with a smaller file. When resuming the download, the range is now unsatisfiable as
                // the first byte position exceeds the the available range. The entire file should be downloaded again.
                $filesize + 1,
                $filesize + 2,
                $filesize + 3,

            ] as $length) {
            $source = Test::TEST_URL;
            $destination = \Helper\get_tmp_file_path();

            // Start with no file.
            if ($length === false) {
                $this->assertFalse(file_exists($destination));

            // Start with $length bytes of file.
            } else {
                // Simulate resuming partially downloaded temporary file.
                $partial_filename = $destination . '.pccdownload';

                if ($length === 0) {
                    $partial_content = '';
                } else {
                    $file = fopen($filename, 'rb');
                    $partial_content = fread($file, $length);
                    fclose($file);
                }

                // Partial content size should be $length bytes large for testing resume download behavior.
                if ($length <= $filesize) {
                    $this->assertEquals($length, strlen($partial_content));

                // Partial content should not be larger than the original file size.
                } else {
                    $this->assertEquals($filesize, strlen($partial_content));
                }

                file_put_contents($partial_filename, $partial_content);
                $this->assertEquals(strlen($partial_content), strlen(file_get_contents($partial_filename)));
            }

            // Download (the remaining bytes of) the file.
            $multi_curl = new MultiCurl();
            $multi_curl->setHeader('X-DEBUG-TEST', 'download_file_range');
            $multi_curl->addDownload($source . '?' . http_build_query([
                'file_path' => $uploaded_file_path,
            ]), $destination);

            clearstatcache();

            $instance_error = false;
            $multi_curl->complete(function ($instance) use ($filesize, $length, $destination, &$instance_error) {
                $expected_bytes_downloaded = $filesize - min($length, $filesize);
                $bytes_downloaded = $instance->responseHeaders['content-length'];
                if ($length === false || $length === 0) {
                    $expected_http_status_code = 200; // 200 OK
                    \PHPUnit\Framework\Assert::assertEquals($expected_bytes_downloaded, $bytes_downloaded);
                } elseif ($length >= $filesize) {
                    $expected_http_status_code = 416; // 416 Requested Range Not Satisfiable
                } else {
                    $expected_http_status_code = 206; // 206 Partial Content
                    \PHPUnit\Framework\Assert::assertEquals($expected_bytes_downloaded, $bytes_downloaded);
                }
                \PHPUnit\Framework\Assert::assertEquals($expected_http_status_code, $instance->httpStatusCode);
                $instance_error = $instance->error;
            });
            $multi_curl->start();

            if (!$instance_error) {
                $this->assertEquals($filesize, filesize($destination));
                unlink($destination);
                $this->assertFalse(file_exists($destination));
            }
        }

        // Remove server file.
        \Helper\remove_file_from_server($uploaded_file_path);

        unlink($filename);
        $this->assertFalse(file_exists($filename));
    }

    public function testDownloadErrorDeleteTemporaryFile()
    {
        $destination = \Helper\get_tmp_file_path();

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', '404');
        $multi_curl->addDownload(Test::TEST_URL, $destination);
        $multi_curl->complete(function ($instance) use ($destination) {
            \PHPUnit\Framework\Assert::assertFalse(file_exists($instance->getDownloadFileName()));
            \PHPUnit\Framework\Assert::assertFalse(file_exists($destination));
        });
        $multi_curl->start();
    }

    public function testDownloadCallback()
    {
        // Upload a file.
        $upload_file_path = \Helper\get_png();
        $upload_test = new Test();
        $upload_test->server('upload_response', 'POST', [
            'image' => '@' . $upload_file_path,
        ]);
        $uploaded_file_path = $upload_test->curl->response->file_path;

        // Download the file.
        $download_callback_called = false;
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'download_response');
        $multi_curl->addDownload(Test::TEST_URL . '?' . http_build_query([
            'file_path' => $uploaded_file_path,
        ]), function ($instance, $fh) use (&$download_callback_called) {
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
        $this->assertEquals('true', $upload_test->server('upload_cleanup', 'POST', [
            'file_path' => $uploaded_file_path,
        ]));

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
        $data = ['key' => 'value'];

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'delete_with_body');
        $multi_curl->addDelete($data, ['wibble' => 'wubble'])->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                '{"get":{"key":"value"},"delete":{"wibble":"wubble"}}',
                $instance->rawResponse
            );
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'get');
        $multi_curl->addDelete($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('key=value', $instance->response);
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'get');
        $multi_curl->addGet($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('key=value', $instance->response);
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'get');
        $multi_curl->addHead($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'HEAD /?key=value HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'get');
        $multi_curl->addOptions($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('key=value', $instance->response);
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'request_method');
        $multi_curl->addPatch($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('PATCH', $instance->response);
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'post');
        $multi_curl->addPost($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('key=value', $instance->response);
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'put');
        $multi_curl->addPut($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('key=value', $instance->response);
        });
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'search');
        $multi_curl->addSearch($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('key=value', $instance->response);
        });
        $multi_curl->start();
    }

    public function testSetUrl()
    {
        $data = ['key' => 'value'];

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

        $multi_curl = new MultiCurl();
        $multi_curl->setUrl(Test::TEST_URL);
        $multi_curl->addSearch($data)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'SEARCH / HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL, $instance->effectiveUrl);
        });
    }

    public function testAddRequestAfterStart()
    {
        $multi_curl = new MultiCurl();

        $urls = [];
        $copy_of_urls = [];
        for ($i = 0; $i < 10; $i++) {
            $url = Test::TEST_URL . '?' . md5(mt_rand());
            $urls[] = $url;
            $copy_of_urls[] = $url;
        }

        $urls_called = [];
        $multi_curl->complete(function ($instance) use (&$multi_curl, &$urls, &$urls_called) {
            $urls_called[] = $instance->url;
            $next_url = array_pop($urls);
            if ($next_url !== null) {
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
        $this->assertNotNull($multi_curl->multiCurl);
        $multi_curl->close();
        $this->assertNull($multi_curl->multiCurl);
    }

    public function testCookieJarAfterClose()
    {
        $cookie_jar = tempnam('/tmp', 'php-curl-class.');

        $multi_curl = new MultiCurl();
        $multi_curl->setCookieJar($cookie_jar);
        $multi_curl->addGet(Test::TEST_URL);
        $multi_curl->start();
        $multi_curl->close();
        $cookies = file_get_contents($cookie_jar);
        $this->assertNotEmpty($cookies);
    }

    public function testMultiPostRedirectGet()
    {
        // Deny post-redirect-get
        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $multi_curl->setHeader('X-DEBUG-TEST', 'post_redirect_get');
        $multi_curl->addPost([], false)->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('Redirected: POST', $instance->response);
        });
        $multi_curl->start();

        // Allow post-redirect-get
        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $multi_curl->setHeader('X-DEBUG-TEST', 'post_redirect_get');
        $multi_curl->addPost([], true)->complete(function ($instance) {
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
        $data = [
            'test' => 'server',
            'key' => 'HTTP_X_REQUEST_ID',
        ];

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

    public function testRetry()
    {
        $tests = [
            [
                'maximum_number_of_retries' => null,
                'failures' => 0,
                'expect_success' => true,
                'expect_attempts' => 1,
                'expect_retries' => 0,
            ],
            [
                'maximum_number_of_retries' => 0,
                'failures' => 0,
                'expect_success' => true,
                'expect_attempts' => 1,
                'expect_retries' => 0,
            ],
            [
                'maximum_number_of_retries' => 0,
                'failures' => 1,
                'expect_success' => false,
                'expect_attempts' => 1,
                'expect_retries' => 0,
            ],
            [
                'maximum_number_of_retries' => 1,
                'failures' => 1,
                'expect_success' => true,
                'expect_attempts' => 2,
                'expect_retries' => 1,
            ],
            [
                'maximum_number_of_retries' => 1,
                'failures' => 2,
                'expect_success' => false,
                'expect_attempts' => 2,
                'expect_retries' => 1,
            ],
            [
                'maximum_number_of_retries' => 2,
                'failures' => 2,
                'expect_success' => true,
                'expect_attempts' => 3,
                'expect_retries' => 2,
            ],
            [
                'maximum_number_of_retries' => 3,
                'failures' => 3,
                'expect_success' => true,
                'expect_attempts' => 4,
                'expect_retries' => 3,
            ],
        ];
        foreach ($tests as $test) {
            $maximum_number_of_retries = $test['maximum_number_of_retries'];
            $failures = $test['failures'];
            $expect_success = $test['expect_success'];
            $expect_attempts = $test['expect_attempts'];
            $expect_retries = $test['expect_retries'];

            $multi_curl = new MultiCurl();
            $multi_curl->setOpt(CURLOPT_COOKIEJAR, '/dev/null');
            $multi_curl->setHeader('X-DEBUG-TEST', 'retry');

            if ($maximum_number_of_retries !== null) {
                $multi_curl->setRetry($maximum_number_of_retries);
            }

            $instance = $multi_curl->addGet(Test::TEST_URL, ['failures' => $failures]);
            $multi_curl->start();

            $this->assertEquals($expect_success, !$instance->error);
            $this->assertEquals($expect_attempts, $instance->attempts);
            $this->assertEquals($expect_retries, $instance->retries);
        }
    }

    public function testRetryCallable()
    {
        $tests = [
            [
                'maximum_number_of_retries' => null,
                'failures' => 0,
                'expect_success' => true,
                'expect_attempts' => 1,
                'expect_retries' => 0,
            ],
            [
                'maximum_number_of_retries' => 0,
                'failures' => 0,
                'expect_success' => true,
                'expect_attempts' => 1,
                'expect_retries' => 0,
            ],
            [
                'maximum_number_of_retries' => 0,
                'failures' => 1,
                'expect_success' => false,
                'expect_attempts' => 1,
                'expect_retries' => 0,
            ],
            [
                'maximum_number_of_retries' => 1,
                'failures' => 1,
                'expect_success' => true,
                'expect_attempts' => 2,
                'expect_retries' => 1,
            ],
            [
                'maximum_number_of_retries' => 1,
                'failures' => 2,
                'expect_success' => false,
                'expect_attempts' => 2,
                'expect_retries' => 1,
            ],
            [
                'maximum_number_of_retries' => 2,
                'failures' => 2,
                'expect_success' => true,
                'expect_attempts' => 3,
                'expect_retries' => 2,
            ],
            [
                'maximum_number_of_retries' => 3,
                'failures' => 3,
                'expect_success' => true,
                'expect_attempts' => 4,
                'expect_retries' => 3,
            ],
        ];
        foreach ($tests as $test) {
            $maximum_number_of_retries = $test['maximum_number_of_retries'];
            $failures = $test['failures'];
            $expect_success = $test['expect_success'];
            $expect_attempts = $test['expect_attempts'];
            $expect_retries = $test['expect_retries'];

            $multi_curl = new MultiCurl();
            $multi_curl->setOpt(CURLOPT_COOKIEJAR, '/dev/null');
            $multi_curl->setHeader('X-DEBUG-TEST', 'retry');

            if ($maximum_number_of_retries !== null) {
                $multi_curl->setRetry(function ($instance) use ($maximum_number_of_retries) {
                    $return = $instance->retries < $maximum_number_of_retries;
                    return $return;
                });
            }

            $instance = $multi_curl->addGet(Test::TEST_URL, ['failures' => $failures]);
            $multi_curl->start();

            $this->assertEquals($expect_success, !$instance->error);
            $this->assertEquals($expect_attempts, $instance->attempts);
            $this->assertEquals($expect_retries, $instance->retries);
        }
    }

    public function testRelativeUrl()
    {
        $multi_curl = new MultiCurl(Test::TEST_URL . 'path/');
        $this->assertEquals('http://127.0.0.1:8000/path/', (string)$multi_curl->baseUrl);

        $get_1 = $multi_curl->addGet('test', [
            'a' => '1',
            'b' => '2',
        ]);
        $this->assertEquals('http://127.0.0.1:8000/path/test?a=1&b=2', (string)$get_1->url);

        $get_2 = $multi_curl->addGet('/root', [
            'c' => '3',
            'd' => '4',
        ]);
        $this->assertEquals('http://127.0.0.1:8000/root?c=3&d=4', (string)$get_2->url);

        $tests = [
            [
                'args' => [
                    'http://www.example.com/',
                    '/foo',
                ],
                'expected' => 'http://www.example.com/foo',
            ],
            [
                'args' => [
                    'http://www.example.com/',
                    '/foo/',
                ],
                'expected' => 'http://www.example.com/foo/',
            ],
            [
                'args' => [
                    'http://www.example.com/',
                    '/dir/page.html',
                ],
                'expected' => 'http://www.example.com/dir/page.html',
            ],
            [
                'args' => [
                    'http://www.example.com/dir1/page2.html',
                    '/dir/page.html',
                ],
                'expected' => 'http://www.example.com/dir/page.html',
            ],
            [
                'args' => [
                    'http://www.example.com/dir1/page2.html',
                    'dir/page.html',
                ],
                'expected' => 'http://www.example.com/dir1/dir/page.html',
            ],
            [
                'args' => [
                    'http://www.example.com/dir1/dir3/page.html',
                    '../dir/page.html',
                ],
                'expected' => 'http://www.example.com/dir1/dir/page.html',
            ],
        ];
        foreach ($tests as $test) {
            $multi_curl = new MultiCurl($test['args']['0']);
            $multi_curl->setUrl($test['args']['1']);
            $this->assertEquals(
                $test['expected'],
                $multi_curl->getOpt(CURLOPT_URL),
                "Joint URLs: '{$test['args']['0']}', '{$test['args']['1']}'"
            );

            $multi_curl = new MultiCurl($test['args']['0']);
            $multi_curl->setUrl($test['args']['1'], ['a' => '1', 'b' => '2']);
            $this->assertEquals(
                $test['expected'] . '?a=1&b=2',
                $multi_curl->getOpt(CURLOPT_URL),
                "Joint URL '{$test['args']['0']}' with parameters a=1, b=2"
            );

            $multi_curl = new MultiCurl();
            $multi_curl->setUrl($test['args']['0']);
            $multi_curl->setUrl($test['args']['1']);
            $this->assertEquals(
                $test['expected'],
                $multi_curl->getOpt(CURLOPT_URL),
                "Joint URLs: '{$test['args']['0']}', '{$test['args']['1']}'"
            );

            $multi_curl = new MultiCurl();
            $multi_curl->setUrl($test['args']['0'], ['a' => '1', 'b' => '2']);
            $multi_curl->setUrl($test['args']['1']);
            $this->assertEquals(
                $test['expected'],
                $multi_curl->getOpt(CURLOPT_URL),
                "Joint URL '{$test['args']['0']}' with parameters a=1, b=2 and URL '{$test['args']['1']}'"
            );

            $multi_curl = new MultiCurl();
            $multi_curl->setUrl($test['args']['0']);
            $multi_curl->setUrl($test['args']['1'], ['a' => '1', 'b' => '2']);
            $this->assertEquals(
                $test['expected'] . '?a=1&b=2',
                $multi_curl->getOpt(CURLOPT_URL),
                "Joint URL '{$test['args']['0']}' and URL '{$test['args']['1']}' with parameters a=1, b=2"
            );

            $multi_curl = new MultiCurl();
            $multi_curl->setUrl($test['args']['0'], ['a' => '1', 'b' => '2']);
            $multi_curl->setUrl($test['args']['1'], ['c' => '3', 'd' => '4']);
            $this->assertEquals(
                $test['expected'] . '?c=3&d=4',
                $multi_curl->getOpt(CURLOPT_URL),
                "Joint URL '{$test['args']['0']}' with parameters a=1, b=2 " .
                "and URL '{$test['args']['1']}' with parameters c=3, d=4"
            );
        }
    }

    public function testPostDataEmptyJson()
    {
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'post_json');
        $multi_curl->setHeader('Content-Type', 'application/json');
        $multi_curl->addPost(Test::TEST_URL);
        $post_complete_called = false;
        $multi_curl->complete(function ($instance) use (&$post_complete_called) {
            \PHPUnit\Framework\Assert::assertEquals('', $instance->response);
            \PHPUnit\Framework\Assert::assertEquals('', $instance->getOpt(CURLOPT_POSTFIELDS));
            $post_complete_called = true;
        });
        $multi_curl->start();
        $this->assertTrue($post_complete_called);
    }

    public function testProxySettings()
    {
        $multi_curl = new MultiCurl();
        $multi_curl->setProxy('proxy.example.com', '1080', 'username', 'password');

        $this->assertEquals('proxy.example.com', $multi_curl->getOpt(CURLOPT_PROXY));
        $this->assertEquals('1080', $multi_curl->getOpt(CURLOPT_PROXYPORT));
        $this->assertEquals('username:password', $multi_curl->getOpt(CURLOPT_PROXYUSERPWD));

        $multi_curl->unsetProxy();
        $this->assertNull($multi_curl->getOpt(CURLOPT_PROXY));
    }

    public function testSetProxyAuth()
    {
        $auth = CURLAUTH_BASIC;

        $multi_curl = new MultiCurl();
        $this->assertNull($multi_curl->getOpt(CURLOPT_PROXYAUTH));
        $multi_curl->setProxyAuth($auth);
        $this->assertEquals($auth, $multi_curl->getOpt(CURLOPT_PROXYAUTH));
    }

    public function testSetProxyType()
    {
        $type = CURLPROXY_SOCKS5;

        $multi_curl = new MultiCurl();
        $this->assertNull($multi_curl->getOpt(CURLOPT_PROXYTYPE));
        $multi_curl->setProxyType($type);
        $this->assertEquals($type, $multi_curl->getOpt(CURLOPT_PROXYTYPE));
    }

    public function testSetProxyTunnel()
    {
        $tunnel = true;

        $multi_curl = new MultiCurl();
        $this->assertNull($multi_curl->getOpt(CURLOPT_HTTPPROXYTUNNEL));
        $multi_curl->setProxyTunnel($tunnel);
        $this->assertEquals($tunnel, $multi_curl->getOpt(CURLOPT_HTTPPROXYTUNNEL));
    }

    public function testSetProxiesRandomProxy()
    {
        $proxies = [
            'example.com:80',
            'example.com:443',
            'example.com:1080',
            'example.com:3128',
            'example.com:8080',
        ];

        $multi_curl = new MultiCurl();
        $multi_curl->setProxies($proxies);
        $get_1 = $multi_curl->addGet(Test::TEST_URL);
        $get_2 = $multi_curl->addGet(Test::TEST_URL);
        $get_3 = $multi_curl->addGet(Test::TEST_URL);

        // Make MultiCurl::curls accessible and MultiCurl::initHandle()
        // callable.
        $reflector = new \ReflectionClass('\Curl\MultiCurl');
        $property = $reflector->getProperty('curls');
        $property->setAccessible(true);
        $multi_curl_curls = $property->getValue($multi_curl);
        $multi_curl_initHandle = $reflector->getMethod('initHandle');
        $multi_curl_initHandle->setAccessible(true);

        // Ensure we have the requests queued.
        $this->assertCount(3, $multi_curl_curls);

        // Invoke MultiCurl::initHandle() so that proxies are set.
        foreach ($multi_curl_curls as $curl) {
            $multi_curl_initHandle->invoke($multi_curl, $curl);
        }

        // Ensure requests are set to one of the random proxies.
        $this->assertContains($get_1->getOpt(CURLOPT_PROXY), $proxies);
        $this->assertContains($get_2->getOpt(CURLOPT_PROXY), $proxies);
        $this->assertContains($get_3->getOpt(CURLOPT_PROXY), $proxies);
    }

    public function testSetProxiesAlreadySet()
    {
        $proxies = [
            'example.com:80',
            'example.com:443',
            'example.com:1080',
            'example.com:3128',
            'example.com:8080',
        ];

        $multi_curl = new MultiCurl();
        $multi_curl->setProxies($proxies);
        $get_1 = $multi_curl->addGet(Test::TEST_URL);
        $get_2 = $multi_curl->addGet(Test::TEST_URL);
        $get_2->setProxy('example.com:9999');
        $get_3 = $multi_curl->addGet(Test::TEST_URL);

        // Make MultiCurl::curls accessible and MultiCurl::initHandle()
        // callable.
        $reflector = new \ReflectionClass('\Curl\MultiCurl');
        $property = $reflector->getProperty('curls');
        $property->setAccessible(true);
        $multi_curl_curls = $property->getValue($multi_curl);
        $multi_curl_initHandle = $reflector->getMethod('initHandle');
        $multi_curl_initHandle->setAccessible(true);

        // Ensure we have the requests queued.
        $this->assertCount(3, $multi_curl_curls);

        // Invoke MultiCurl::initHandle() so that proxies are set.
        foreach ($multi_curl_curls as $curl) {
            $multi_curl_initHandle->invoke($multi_curl, $curl);
        }

        // Ensure requests are set to one of the random proxies.
        $this->assertContains($get_1->getOpt(CURLOPT_PROXY), $proxies);
        $this->assertContains($get_3->getOpt(CURLOPT_PROXY), $proxies);

        // Ensure request with specific proxy is not set to one of the random proxies.
        $this->assertNotContains($get_2->getOpt(CURLOPT_PROXY), $proxies);
    }

    public function testSetFile()
    {
        $file = STDOUT;

        $multi_curl = new MultiCurl();
        $this->assertNull($multi_curl->getOpt(CURLOPT_FILE));
        $multi_curl->setFile($file);
        $this->assertEquals($file, $multi_curl->getOpt(CURLOPT_FILE));
    }

    public function testSetRange()
    {
        $range = '1000-';

        $multi_curl = new MultiCurl();
        $this->assertNull($multi_curl->getOpt(CURLOPT_RANGE));
        $multi_curl->setRange($range);
        $this->assertEquals($range, $multi_curl->getOpt(CURLOPT_RANGE));
    }

    public function testDisableTimeout()
    {
        $multi_curl = new MultiCurl();
        $multi_curl->disableTimeout();
        $get = $multi_curl->addGet(Test::TEST_URL);
        $get->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertNull($instance->getOpt(CURLOPT_TIMEOUT));
        });
        $multi_curl->start();
    }

    public function testSetRateLimitUnits()
    {
        foreach ([
                [
                    'rate_limit' => '1/s',
                    'expected' => [
                        'rate_limit' => '1/1s',
                        'max_requests' => '1',
                        'interval' => '1',
                        'unit' => 's',
                        'interval_seconds' => '1',
                    ],
                ],
                [
                    'rate_limit' => '1/1s',
                    'expected' => [
                        'rate_limit' => '1/1s',
                        'max_requests' => '1',
                        'interval' => '1',
                        'unit' => 's',
                        'interval_seconds' => '1',
                    ],
                ],
                [
                    'rate_limit' => '10/60s',
                    'expected' => [
                        'rate_limit' => '10/60s',
                        'max_requests' => '10',
                        'interval' => '60',
                        'unit' => 's',
                        'interval_seconds' => '60',
                    ],
                ],
                [
                    'rate_limit' => '10/60S',
                    'expected' => [
                        'rate_limit' => '10/60s',
                        'max_requests' => '10',
                        'interval' => '60',
                        'unit' => 's',
                        'interval_seconds' => '60',
                    ],
                ],
                [
                    'rate_limit' => '1/m',
                    'expected' => [
                        'rate_limit' => '1/1m',
                        'max_requests' => '1',
                        'interval' => '1',
                        'unit' => 'm',
                        'interval_seconds' => '60',
                    ],
                ],
                [
                    'rate_limit' => '1/1m',
                    'expected' => [
                        'rate_limit' => '1/1m',
                        'max_requests' => '1',
                        'interval' => '1',
                        'unit' => 'm',
                        'interval_seconds' => '60',
                    ],
                ],
                [
                    'rate_limit' => '5000/60m',
                    'expected' => [
                        'rate_limit' => '5000/60m',
                        'max_requests' => '5000',
                        'interval' => '60',
                        'unit' => 'm',
                        'interval_seconds' => '3600',
                    ],
                ],
                [
                    'rate_limit' => '5000/60M',
                    'expected' => [
                        'rate_limit' => '5000/60m',
                        'max_requests' => '5000',
                        'interval' => '60',
                        'unit' => 'm',
                        'interval_seconds' => '3600',
                    ],
                ],
                [
                    'rate_limit' => '1/h',
                    'expected' => [
                        'rate_limit' => '1/1h',
                        'max_requests' => '1',
                        'interval' => '1',
                        'unit' => 'h',
                        'interval_seconds' => '3600',
                    ],
                ],
                [
                    'rate_limit' => '1/1h',
                    'expected' => [
                        'rate_limit' => '1/1h',
                        'max_requests' => '1',
                        'interval' => '1',
                        'unit' => 'h',
                        'interval_seconds' => '3600',
                    ],
                ],
                [
                    'rate_limit' => '5000/1h',
                    'expected' => [
                        'rate_limit' => '5000/1h',
                        'max_requests' => '5000',
                        'interval' => '1',
                        'unit' => 'h',
                        'interval_seconds' => '3600',
                    ],
                ],
                [
                    'rate_limit' => '100000/24h',
                    'expected' => [
                        'rate_limit' => '100000/24h',
                        'max_requests' => '100000',
                        'interval' => '24',
                        'unit' => 'h',
                        'interval_seconds' => '86400',
                    ],
                ],
                [
                    'rate_limit' => '100000/24H',
                    'expected' => [
                        'rate_limit' => '100000/24h',
                        'max_requests' => '100000',
                        'interval' => '24',
                        'unit' => 'h',
                        'interval_seconds' => '86400',
                    ],
                ],
            ] as $test) {
            $multi_curl = new MultiCurl();
            $multi_curl->setRateLimit($test['rate_limit']);

            $this->assertEquals(
                $test['expected']['rate_limit'],
                \Helper\get_multi_curl_property_value($multi_curl, 'rateLimit')
            );
            $this->assertEquals(
                $test['expected']['max_requests'],
                \Helper\get_multi_curl_property_value($multi_curl, 'maxRequests')
            );
            $this->assertEquals(
                $test['expected']['interval'],
                \Helper\get_multi_curl_property_value($multi_curl, 'interval')
            );
            $this->assertEquals(
                $test['expected']['unit'],
                \Helper\get_multi_curl_property_value($multi_curl, 'unit')
            );
            $this->assertEquals(
                $test['expected']['interval_seconds'],
                \Helper\get_multi_curl_property_value($multi_curl, 'intervalSeconds')
            );
        }
    }

    public function testSetRateLimitPerSecond1()
    {
        //  R0--|
        //  R1--|
        //      W---------------|
        //                      R2--|
        //                      R3--|
        //                          W---------------|
        //                                          R4--|
        //  0   1   2   3   4   5   6   7   8   9   10  11  12

        $request_stats = [];

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'timeout');
        $multi_curl->setRateLimit('2/5s');
        $multi_curl->beforeSend(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id] = [];
            $request_stats[$instance->id]['start'] = microtime(true);
        });
        $multi_curl->complete(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id]['stop'] = microtime(true);
        });

        $multi_curl->addGet(Test::getTestUrl('8001') . '?seconds=1');
        $multi_curl->addGet(Test::getTestUrl('8002') . '?seconds=1');
        $multi_curl->addGet(Test::getTestUrl('8003') . '?seconds=1');
        $multi_curl->addGet(Test::getTestUrl('8004') . '?seconds=1');
        $multi_curl->addGet(Test::getTestUrl('8005') . '?seconds=1');

        $multi_curl->start();
        $request_stats = \Helper\get_request_stats($request_stats, $multi_curl);

        // Assert R0 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['0']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['0']['relative_start']);
        // Assert R0 ends around 1.
        $this->assertGreaterThanOrEqual(0.8, $request_stats['0']['relative_stop']);
        $this->assertLessThanOrEqual(1.5, $request_stats['0']['relative_stop']);

        // Assert R1 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['1']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['1']['relative_start']);
        // Assert R1 ends around 1.
        $this->assertGreaterThanOrEqual(0.8, $request_stats['1']['relative_stop']);
        $this->assertLessThanOrEqual(1.5, $request_stats['1']['relative_stop']);

        // Assert R2 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['2']['relative_start']);
        $this->assertLessThanOrEqual(5.5, $request_stats['2']['relative_start']);
        // Assert R2 ends around 6.
        $this->assertGreaterThanOrEqual(5.8, $request_stats['2']['relative_stop']);
        $this->assertLessThanOrEqual(6.5, $request_stats['2']['relative_stop']);

        // Assert R3 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['3']['relative_start']);
        $this->assertLessThanOrEqual(5.5, $request_stats['3']['relative_start']);
        // Assert R3 ends around 6.
        $this->assertGreaterThanOrEqual(5.8, $request_stats['3']['relative_stop']);
        $this->assertLessThanOrEqual(6.5, $request_stats['3']['relative_stop']);

        // Assert R4 starts around 10 and not before.
        $this->assertGreaterThanOrEqual(10, $request_stats['4']['relative_start']);
        $this->assertLessThanOrEqual(10.5, $request_stats['4']['relative_start']);
        // Assert R4 ends around 11.
        $this->assertGreaterThanOrEqual(10.8, $request_stats['4']['relative_stop']);
        $this->assertLessThanOrEqual(11.5, $request_stats['4']['relative_stop']);
    }

    public function testSetRateLimitPerSecond2()
    {
        //  R0--|
        //  R1------|
        //          W-----------|
        //                      R2--|
        //                      R3------|
        //                              W-----------|
        //                                          R4--|
        //  0   1   2   3   4   5   6   7   8   9   10  11  12

        $request_stats = [];

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'timeout');
        $multi_curl->setRateLimit('2/5s');
        $multi_curl->beforeSend(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id] = [];
            $request_stats[$instance->id]['start'] = microtime(true);
        });
        $multi_curl->complete(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id]['stop'] = microtime(true);
        });

        $multi_curl->addGet(Test::getTestUrl('8001') . '?seconds=1');
        $multi_curl->addGet(Test::getTestUrl('8002') . '?seconds=2');
        $multi_curl->addGet(Test::getTestUrl('8003') . '?seconds=1');
        $multi_curl->addGet(Test::getTestUrl('8004') . '?seconds=2');
        $multi_curl->addGet(Test::getTestUrl('8005') . '?seconds=1');

        $multi_curl->start();
        $request_stats = \Helper\get_request_stats($request_stats, $multi_curl);

        // Assert R0 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['0']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['0']['relative_start']);
        // Assert R0 ends around 1.
        $this->assertGreaterThanOrEqual(0.8, $request_stats['0']['relative_stop']);
        $this->assertLessThanOrEqual(1.5, $request_stats['0']['relative_stop']);

        // Assert R1 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['1']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['1']['relative_start']);
        // Assert R1 ends around 2.
        $this->assertGreaterThanOrEqual(1.8, $request_stats['1']['relative_stop']);
        $this->assertLessThanOrEqual(2.5, $request_stats['1']['relative_stop']);

        // Assert R2 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['2']['relative_start']);
        $this->assertLessThanOrEqual(5.5, $request_stats['2']['relative_start']);
        // Assert R2 ends around 6.
        $this->assertGreaterThanOrEqual(5.8, $request_stats['2']['relative_stop']);
        $this->assertLessThanOrEqual(6.5, $request_stats['2']['relative_stop']);

        // Assert R3 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['3']['relative_start']);
        $this->assertLessThanOrEqual(5.5, $request_stats['3']['relative_start']);
        // Assert R3 ends around 7.
        $this->assertGreaterThanOrEqual(6.8, $request_stats['3']['relative_stop']);
        $this->assertLessThanOrEqual(7.5, $request_stats['3']['relative_stop']);

        // Assert R4 starts around 10 and not before.
        $this->assertGreaterThanOrEqual(10, $request_stats['4']['relative_start']);
        $this->assertLessThanOrEqual(10.5, $request_stats['4']['relative_start']);
        // Assert R4 ends around 11.
        $this->assertGreaterThanOrEqual(10.8, $request_stats['4']['relative_stop']);
        $this->assertLessThanOrEqual(11.5, $request_stats['4']['relative_stop']);
    }

    public function testSetRateLimitPerSecond3()
    {
        //  R0------|
        //  R1------------------|
        //                      R2------|
        //                      R3------------------|
        //                                          R4--|
        //  0   1   2   3   4   5   6   7   8   9   10  11  12

        $request_stats = [];

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'timeout');
        $multi_curl->setRateLimit('2/5s');
        $multi_curl->beforeSend(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id] = [];
            $request_stats[$instance->id]['start'] = microtime(true);
        });
        $multi_curl->complete(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id]['stop'] = microtime(true);
        });

        $multi_curl->addGet(Test::getTestUrl('8001') . '?seconds=2');
        $multi_curl->addGet(Test::getTestUrl('8002') . '?seconds=5');
        $multi_curl->addGet(Test::getTestUrl('8003') . '?seconds=2');
        $multi_curl->addGet(Test::getTestUrl('8004') . '?seconds=5');
        $multi_curl->addGet(Test::getTestUrl('8005') . '?seconds=1');

        $multi_curl->start();
        $request_stats = \Helper\get_request_stats($request_stats, $multi_curl);

        // Assert R0 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['0']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['0']['relative_start']);
        // Assert R0 ends around 2.
        $this->assertGreaterThanOrEqual(1.8, $request_stats['0']['relative_stop']);
        $this->assertLessThanOrEqual(2.5, $request_stats['0']['relative_stop']);

        // Assert R1 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['1']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['1']['relative_start']);
        // Assert R1 ends around 5.
        $this->assertGreaterThanOrEqual(4.8, $request_stats['1']['relative_stop']);
        $this->assertLessThanOrEqual(5.5, $request_stats['1']['relative_stop']);

        // Assert R2 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['2']['relative_start']);
        $this->assertLessThanOrEqual(5.5, $request_stats['2']['relative_start']);
        // Assert R2 ends around 7.
        $this->assertGreaterThanOrEqual(6.8, $request_stats['2']['relative_stop']);
        $this->assertLessThanOrEqual(7.5, $request_stats['2']['relative_stop']);

        // Assert R3 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['3']['relative_start']);
        $this->assertLessThanOrEqual(5.5, $request_stats['3']['relative_start']);
        // Assert R3 ends around 10.
        $this->assertGreaterThanOrEqual(9.8, $request_stats['3']['relative_stop']);
        $this->assertLessThanOrEqual(10.5, $request_stats['3']['relative_stop']);

        // Assert R4 starts around 10 and not before.
        $this->assertGreaterThanOrEqual(10, $request_stats['4']['relative_start']);
        $this->assertLessThanOrEqual(10.5, $request_stats['4']['relative_start']);
        // Assert R4 ends around 11.
        $this->assertGreaterThanOrEqual(10.8, $request_stats['4']['relative_stop']);
        $this->assertLessThanOrEqual(11.5, $request_stats['4']['relative_stop']);
    }

    public function testSetRateLimitPerSecond4()
    {
        //  R0------|
        //  R1----------------------------------|
        //                      R2----------|
        //                      R3----------------------|
        //                                          R4--|
        //  0   1   2   3   4   5   6   7   8   9   10  11  12

        $request_stats = [];

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'timeout');
        $multi_curl->setRateLimit('2/5s');
        $multi_curl->beforeSend(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id] = [];
            $request_stats[$instance->id]['start'] = microtime(true);
        });
        $multi_curl->complete(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id]['stop'] = microtime(true);
        });

        $multi_curl->addGet(Test::getTestUrl('8001') . '?seconds=2');
        $multi_curl->addGet(Test::getTestUrl('8002') . '?seconds=9');
        $multi_curl->addGet(Test::getTestUrl('8003') . '?seconds=3');
        $multi_curl->addGet(Test::getTestUrl('8004') . '?seconds=6');
        $multi_curl->addGet(Test::getTestUrl('8005') . '?seconds=1');

        $multi_curl->start();
        $request_stats = \Helper\get_request_stats($request_stats, $multi_curl);

        // Assert R0 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['0']['relative_start'], $request_stats['message']);
        $this->assertLessThanOrEqual(0.5, $request_stats['0']['relative_start'], $request_stats['message']);
        // Assert R0 ends around 2.
        $this->assertGreaterThanOrEqual(1.8, $request_stats['0']['relative_stop'], $request_stats['message']);
        $this->assertLessThanOrEqual(2.5, $request_stats['0']['relative_stop'], $request_stats['message']);

        // Assert R1 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['1']['relative_start'], $request_stats['message']);
        $this->assertLessThanOrEqual(0.5, $request_stats['1']['relative_start'], $request_stats['message']);
        // Assert R1 ends around 9.
        $this->assertGreaterThanOrEqual(8.8, $request_stats['1']['relative_stop'], $request_stats['message']);
        $this->assertLessThanOrEqual(9.5, $request_stats['1']['relative_stop'], $request_stats['message']);

        // Assert R2 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['2']['relative_start'], $request_stats['message']);
        $this->assertLessThanOrEqual(5.5, $request_stats['2']['relative_start'], $request_stats['message']);
        // Assert R2 ends around 8.
        $this->assertGreaterThanOrEqual(7.8, $request_stats['2']['relative_stop'], $request_stats['message']);
        $this->assertLessThanOrEqual(8.5 + 1, $request_stats['2']['relative_stop'], $request_stats['message']);

        // Assert R3 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['3']['relative_start'], $request_stats['message']);
        $this->assertLessThanOrEqual(5.5, $request_stats['3']['relative_start'], $request_stats['message']);
        // Assert R3 ends around 11.
        $this->assertGreaterThanOrEqual(10.8, $request_stats['3']['relative_stop'], $request_stats['message']);
        $this->assertLessThanOrEqual(11.5 + 1, $request_stats['3']['relative_stop'], $request_stats['message']);

        // Assert R4 starts around 10 and not before.
        $this->assertGreaterThanOrEqual(10, $request_stats['4']['relative_start'], $request_stats['message']);
        $this->assertLessThanOrEqual(10.5, $request_stats['4']['relative_start'], $request_stats['message']);
        // Assert R4 ends around 11.
        $this->assertGreaterThanOrEqual(10.8, $request_stats['4']['relative_stop'], $request_stats['message']);
        $this->assertLessThanOrEqual(11.5 + 1, $request_stats['4']['relative_stop'], $request_stats['message']);
    }

    public function testSetRateLimitPerSecond5()
    {
        //  R0--------------------------|
        //  R1--------------------------|
        //                      R2------|
        //                      R3------|
        //                              W-----------|
        //                                          R4------|
        //  0   1   2   3   4   5   6   7   8   9   10  11  12

        $request_stats = [];

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'timeout');
        $multi_curl->setRateLimit('2/5s');
        $multi_curl->beforeSend(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id] = [];
            $request_stats[$instance->id]['start'] = microtime(true);
        });
        $multi_curl->complete(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id]['stop'] = microtime(true);
        });

        $multi_curl->addGet(Test::getTestUrl('8001') . '?seconds=7');
        $multi_curl->addGet(Test::getTestUrl('8002') . '?seconds=7');
        $multi_curl->addGet(Test::getTestUrl('8003') . '?seconds=2');
        $multi_curl->addGet(Test::getTestUrl('8004') . '?seconds=2');
        $multi_curl->addGet(Test::getTestUrl('8005') . '?seconds=2');

        $multi_curl->start();
        $request_stats = \Helper\get_request_stats($request_stats, $multi_curl);

        // Assert R0 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['0']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['0']['relative_start']);
        // Assert R0 ends around 7.
        $this->assertGreaterThanOrEqual(6.8, $request_stats['0']['relative_stop']);
        $this->assertLessThanOrEqual(7.5, $request_stats['0']['relative_stop']);

        // Assert R1 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['1']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['1']['relative_start']);
        // Assert R1 ends around 7.
        $this->assertGreaterThanOrEqual(6.8, $request_stats['1']['relative_stop']);
        $this->assertLessThanOrEqual(7.5, $request_stats['1']['relative_stop']);

        // Assert R2 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['2']['relative_start']);
        $this->assertLessThanOrEqual(5.5, $request_stats['2']['relative_start']);
        // Assert R2 ends around 7.
        $this->assertGreaterThanOrEqual(6.8, $request_stats['2']['relative_stop']);
        $this->assertLessThanOrEqual(7.5 + 1, $request_stats['2']['relative_stop']);

        // Assert R3 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['3']['relative_start']);
        $this->assertLessThanOrEqual(5.5, $request_stats['3']['relative_start']);
        // Assert R3 ends around 7.
        $this->assertGreaterThanOrEqual(6.8, $request_stats['3']['relative_stop']);
        $this->assertLessThanOrEqual(7.5 + 1, $request_stats['3']['relative_stop']);

        // Assert R4 starts around 10 and not before.
        $this->assertGreaterThanOrEqual(10, $request_stats['4']['relative_start']);
        $this->assertLessThanOrEqual(10.5, $request_stats['4']['relative_start']);
        // Assert R4 ends around 12.
        $this->assertGreaterThanOrEqual(11.8, $request_stats['4']['relative_stop']);
        $this->assertLessThanOrEqual(12.5, $request_stats['4']['relative_stop']);
    }

    public function testSetRateLimitPerSecond6()
    {
        //  R0--------------------------|
        //  R1--------------------------|
        //                      R2--------------|
        //                      R3------|
        //                                      W---|
        //                                          R4------|
        //  0   1   2   3   4   5   6   7   8   9   10  11  12

        $request_stats = [];

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'timeout');
        $multi_curl->setRateLimit('2/5s');
        $multi_curl->beforeSend(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id] = [];
            $request_stats[$instance->id]['start'] = microtime(true);
        });
        $multi_curl->complete(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id]['stop'] = microtime(true);
        });

        $multi_curl->addGet(Test::getTestUrl('8001') . '?seconds=7');
        $multi_curl->addGet(Test::getTestUrl('8002') . '?seconds=7');
        $multi_curl->addGet(Test::getTestUrl('8003') . '?seconds=4');
        $multi_curl->addGet(Test::getTestUrl('8004') . '?seconds=2');
        $multi_curl->addGet(Test::getTestUrl('8005') . '?seconds=2');

        $multi_curl->start();
        $request_stats = \Helper\get_request_stats($request_stats, $multi_curl);

        // Assert R0 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['0']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['0']['relative_start']);
        // Assert R0 ends around 7.
        $this->assertGreaterThanOrEqual(6.8, $request_stats['0']['relative_stop']);
        $this->assertLessThanOrEqual(7.5, $request_stats['0']['relative_stop']);

        // Assert R1 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['1']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['1']['relative_start']);
        // Assert R1 ends around 7.
        $this->assertGreaterThanOrEqual(6.8, $request_stats['1']['relative_stop']);
        $this->assertLessThanOrEqual(7.5, $request_stats['1']['relative_stop']);

        // Assert R2 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['2']['relative_start']);
        $this->assertLessThanOrEqual(5.5, $request_stats['2']['relative_start']);
        // Assert R2 ends around 9.
        $this->assertGreaterThanOrEqual(8.8, $request_stats['2']['relative_stop']);
        $this->assertLessThanOrEqual(9.5 + 1, $request_stats['2']['relative_stop']);

        // Assert R3 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['3']['relative_start']);
        $this->assertLessThanOrEqual(5.5, $request_stats['3']['relative_start']);
        // Assert R3 ends around 7.
        $this->assertGreaterThanOrEqual(6.8, $request_stats['3']['relative_stop']);
        $this->assertLessThanOrEqual(7.5 + 1, $request_stats['3']['relative_stop']);

        // Assert R4 starts around 10 and not before.
        $this->assertGreaterThanOrEqual(10, $request_stats['4']['relative_start']);
        $this->assertLessThanOrEqual(10.5, $request_stats['4']['relative_start']);
        // Assert R4 ends around 12.
        $this->assertGreaterThanOrEqual(11.8, $request_stats['4']['relative_stop']);
        $this->assertLessThanOrEqual(12.5, $request_stats['4']['relative_stop']);
    }

    public function testSetRateLimitPerSecond7()
    {
        //  R0------|
        //  R1----------------------------------------------|
        //                      R2----------------------|
        //                      R3----------------------|
        //                                          R4------|
        //                                          R5------|
        //  0   1   2   3   4   5   6   7   8   9   10  11  12

        $request_stats = [];

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'timeout');
        $multi_curl->setRateLimit('2/5s');
        $multi_curl->beforeSend(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id] = [];
            $request_stats[$instance->id]['start'] = microtime(true);
        });
        $multi_curl->complete(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id]['stop'] = microtime(true);
        });

        $multi_curl->addGet(Test::getTestUrl('8001') . '?seconds=2');
        $multi_curl->addGet(Test::getTestUrl('8002') . '?seconds=12');
        $multi_curl->addGet(Test::getTestUrl('8003') . '?seconds=6');
        $multi_curl->addGet(Test::getTestUrl('8004') . '?seconds=6');
        $multi_curl->addGet(Test::getTestUrl('8005') . '?seconds=2');
        $multi_curl->addGet(Test::getTestUrl('8006') . '?seconds=2');

        $multi_curl->start();
        $request_stats = \Helper\get_request_stats($request_stats, $multi_curl);

        // Assert R0 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['0']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['0']['relative_start']);
        // Assert R0 ends around 2.
        $this->assertGreaterThanOrEqual(1.8, $request_stats['0']['relative_stop']);
        $this->assertLessThanOrEqual(2.5, $request_stats['0']['relative_stop']);

        // Assert R1 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['1']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['1']['relative_start']);
        // Assert R1 ends around 12.
        $this->assertGreaterThanOrEqual(11.8, $request_stats['1']['relative_stop']);
        $this->assertLessThanOrEqual(12.5, $request_stats['1']['relative_stop']);

        // Assert R2 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['2']['relative_start']);
        $this->assertLessThanOrEqual(5.5, $request_stats['2']['relative_start']);
        // Assert R2 ends around 11.
        $this->assertGreaterThanOrEqual(10.8, $request_stats['2']['relative_stop']);
        $this->assertLessThanOrEqual(11.5 + 1, $request_stats['2']['relative_stop']);

        // Assert R3 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['3']['relative_start']);
        $this->assertLessThanOrEqual(5.5, $request_stats['3']['relative_start']);
        // Assert R3 ends around 11.
        $this->assertGreaterThanOrEqual(10.8, $request_stats['3']['relative_stop']);
        $this->assertLessThanOrEqual(11.5 + 1, $request_stats['3']['relative_stop']);

        // Assert R4 starts around 10 and not before.
        $this->assertGreaterThanOrEqual(10, $request_stats['4']['relative_start']);
        $this->assertLessThanOrEqual(10.5, $request_stats['4']['relative_start']);
        // Assert R4 ends around 12.
        $this->assertGreaterThanOrEqual(11.8, $request_stats['4']['relative_stop']);
        $this->assertLessThanOrEqual(12.5 + 1, $request_stats['4']['relative_stop']);

        // Assert R5 starts around 10 and not before.
        $this->assertGreaterThanOrEqual(10, $request_stats['5']['relative_start']);
        $this->assertLessThanOrEqual(10.5, $request_stats['5']['relative_start']);
        // Assert R5 ends around 12.
        $this->assertGreaterThanOrEqual(11.8, $request_stats['5']['relative_stop']);
        $this->assertLessThanOrEqual(12.5 + 1, $request_stats['5']['relative_stop']);
    }

    public function testSetRateLimitPerSecond8()
    {
        //  R0------------------------------|
        //  R1----------------------------------------------|
        //                      R2--------------------------|
        //                      R3--------------|
        //                                          R4--|
        //                                          R5------|
        //  0   1   2   3   4   5   6   7   8   9   10  11  12

        $request_stats = [];

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'timeout');
        $multi_curl->setRateLimit('2/5s');
        $multi_curl->beforeSend(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id] = [];
            $request_stats[$instance->id]['start'] = microtime(true);
        });
        $multi_curl->complete(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id]['stop'] = microtime(true);
        });

        $multi_curl->addGet(Test::getTestUrl('8001') . '?seconds=8');
        $multi_curl->addGet(Test::getTestUrl('8002') . '?seconds=12');
        $multi_curl->addGet(Test::getTestUrl('8003') . '?seconds=7');
        $multi_curl->addGet(Test::getTestUrl('8004') . '?seconds=4');
        $multi_curl->addGet(Test::getTestUrl('8005') . '?seconds=1');
        $multi_curl->addGet(Test::getTestUrl('8006') . '?seconds=2');

        $multi_curl->start();
        $request_stats = \Helper\get_request_stats($request_stats, $multi_curl);

        // Assert R0 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['0']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['0']['relative_start']);
        // Assert R0 ends around 8.
        $this->assertGreaterThanOrEqual(7.8, $request_stats['0']['relative_stop']);
        $this->assertLessThanOrEqual(8.5, $request_stats['0']['relative_stop']);

        // Assert R1 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['1']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['1']['relative_start']);
        // Assert R1 ends around 12.
        $this->assertGreaterThanOrEqual(11.8, $request_stats['1']['relative_stop']);
        $this->assertLessThanOrEqual(12.5, $request_stats['1']['relative_stop']);

        // Assert R2 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['2']['relative_start']);
        $this->assertLessThanOrEqual(5.5, $request_stats['2']['relative_start']);
        // Assert R2 ends around 12.
        $this->assertGreaterThanOrEqual(11.8, $request_stats['2']['relative_stop']);
        $this->assertLessThanOrEqual(12.5 + 1, $request_stats['2']['relative_stop']);

        // Assert R3 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['3']['relative_start']);
        $this->assertLessThanOrEqual(5.5, $request_stats['3']['relative_start']);
        // Assert R3 ends around 9.
        $this->assertGreaterThanOrEqual(8.8, $request_stats['3']['relative_stop']);
        $this->assertLessThanOrEqual(9.5 + 1, $request_stats['3']['relative_stop']);

        // Assert R4 starts around 10 and not before.
        $this->assertGreaterThanOrEqual(10, $request_stats['4']['relative_start']);
        $this->assertLessThanOrEqual(10.5, $request_stats['4']['relative_start']);
        // Assert R4 ends around 11.
        $this->assertGreaterThanOrEqual(10.8, $request_stats['4']['relative_stop']);
        $this->assertLessThanOrEqual(11.5, $request_stats['4']['relative_stop']);

        // Assert R5 starts around 10 and not before.
        $this->assertGreaterThanOrEqual(10, $request_stats['5']['relative_start']);
        $this->assertLessThanOrEqual(10.5, $request_stats['5']['relative_start']);
        // Assert R5 ends around 12.
        $this->assertGreaterThanOrEqual(11.8, $request_stats['5']['relative_stop']);
        $this->assertLessThanOrEqual(12.5, $request_stats['5']['relative_stop']);
    }

    public function testSetRateLimitPerSecond9()
    {
        //  R0----------------------------------------------|
        //  R1----------------------------------------------|
        //                      R2--------------------------|
        //                      R3--------------------------|
        //                                          R4------|
        //                                          R5------|
        //  0   1   2   3   4   5   6   7   8   9   10  11  12

        $request_stats = [];

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'timeout');
        $multi_curl->setRateLimit('2/5s');
        $multi_curl->beforeSend(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id] = [];
            $request_stats[$instance->id]['start'] = microtime(true);
        });
        $multi_curl->complete(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id]['stop'] = microtime(true);
        });

        $multi_curl->addGet(Test::getTestUrl('8001') . '?seconds=12');
        $multi_curl->addGet(Test::getTestUrl('8002') . '?seconds=12');
        $multi_curl->addGet(Test::getTestUrl('8003') . '?seconds=7');
        $multi_curl->addGet(Test::getTestUrl('8004') . '?seconds=7');
        $multi_curl->addGet(Test::getTestUrl('8005') . '?seconds=2');
        $multi_curl->addGet(Test::getTestUrl('8006') . '?seconds=2');

        $multi_curl->start();
        $request_stats = \Helper\get_request_stats($request_stats, $multi_curl);

        // Assert R0 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['0']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['0']['relative_start']);
        // Assert R0 ends around 12.
        $this->assertGreaterThanOrEqual(11.8, $request_stats['0']['relative_stop']);
        $this->assertLessThanOrEqual(12.5, $request_stats['0']['relative_stop']);

        // Assert R1 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['1']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['1']['relative_start']);
        // Assert R1 ends around 12.
        $this->assertGreaterThanOrEqual(11.8, $request_stats['1']['relative_stop']);
        $this->assertLessThanOrEqual(12.5, $request_stats['1']['relative_stop']);

        // Assert R2 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['2']['relative_start']);
        $this->assertLessThanOrEqual(5.5, $request_stats['2']['relative_start']);
        // Assert R2 ends around 12.
        $this->assertGreaterThanOrEqual(11.8, $request_stats['2']['relative_stop']);
        $this->assertLessThanOrEqual(12.5 + 1, $request_stats['2']['relative_stop']);

        // Assert R3 starts around 5 and not before.
        $this->assertGreaterThanOrEqual(5, $request_stats['3']['relative_start']);
        $this->assertLessThanOrEqual(5.5, $request_stats['3']['relative_start']);
        // Assert R3 ends around 12.
        $this->assertGreaterThanOrEqual(11.8, $request_stats['3']['relative_stop']);
        $this->assertLessThanOrEqual(12.5 + 1, $request_stats['3']['relative_stop']);

        // Assert R4 starts around 10 and not before.
        $this->assertGreaterThanOrEqual(10, $request_stats['4']['relative_start']);
        $this->assertLessThanOrEqual(10.5, $request_stats['4']['relative_start']);
        // Assert R4 ends around 12.
        $this->assertGreaterThanOrEqual(11.8, $request_stats['4']['relative_stop']);
        $this->assertLessThanOrEqual(12.5 + 1, $request_stats['4']['relative_stop']);

        // Assert R5 starts around 10 and not before.
        $this->assertGreaterThanOrEqual(10, $request_stats['5']['relative_start']);
        $this->assertLessThanOrEqual(10.5, $request_stats['5']['relative_start']);
        // Assert R5 ends around 12.
        $this->assertGreaterThanOrEqual(11.8, $request_stats['5']['relative_stop']);
        $this->assertLessThanOrEqual(12.5 + 1, $request_stats['5']['relative_stop']);
    }

    public function testSetRateLimitPerSecondOnePerSecond()
    {
        $request_stats = [];

        $multi_curl = new MultiCurl();
        $multi_curl->setRateLimit('1/1s');
        $multi_curl->beforeSend(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id] = [];
            $request_stats[$instance->id]['start'] = microtime(true);
        });
        $multi_curl->complete(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id]['stop'] = microtime(true);
        });

        $multi_curl->addGet(Test::getTestUrl('8001'));
        $multi_curl->addGet(Test::getTestUrl('8002'));
        $multi_curl->addGet(Test::getTestUrl('8003'));

        $multi_curl->start();
        $request_stats = \Helper\get_request_stats($request_stats, $multi_curl);

        // Assert R0 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['0']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['0']['relative_start']);

        // Assert R1 starts around 1 and not before.
        $this->assertGreaterThanOrEqual(1, $request_stats['1']['relative_start']);
        $this->assertLessThanOrEqual(1.5, $request_stats['1']['relative_start']);

        // Assert R2 starts around 2 and not before.
        $this->assertGreaterThanOrEqual(2, $request_stats['2']['relative_start']);
        $this->assertLessThanOrEqual(2.5, $request_stats['2']['relative_start']);
    }

    public function testSetRateLimitFivePerThirtySecond()
    {
        $request_stats = [];

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'timeout');
        $multi_curl->setRateLimit('5/30s');
        $multi_curl->beforeSend(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id] = [];
            $request_stats[$instance->id]['start'] = microtime(true);
        });
        $multi_curl->complete(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id]['stop'] = microtime(true);
        });

        $multi_curl->addGet(Test::getTestUrl('8003') . '?seconds=15');
        $multi_curl->addGet(Test::getTestUrl('8001') . '?seconds=25');
        $multi_curl->addGet(Test::getTestUrl('8006') . '?seconds=35');
        $multi_curl->addGet(Test::getTestUrl('8005') . '?seconds=45');
        $multi_curl->addGet(Test::getTestUrl('8004') . '?seconds=20');
        $multi_curl->addGet(Test::getTestUrl('8002') . '?seconds=10');

        $multi_curl->start();
        $request_stats = \Helper\get_request_stats($request_stats, $multi_curl);

        // Assert R5 starts around 30 and not before.
        $this->assertGreaterThanOrEqual(30, $request_stats['5']['relative_start']);
        $this->assertLessThanOrEqual(30.5, $request_stats['5']['relative_start']);
    }

    public function testSetRateLimitOnePerOneMinute()
    {
        $request_stats = [];

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'timeout');
        $multi_curl->setRateLimit('1/1m');
        $multi_curl->beforeSend(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id] = [];
            $request_stats[$instance->id]['start'] = microtime(true);
        });
        $multi_curl->complete(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id]['stop'] = microtime(true);
        });

        $multi_curl->addGet(Test::getTestUrl('8002') . '?seconds=30');
        $multi_curl->addGet(Test::getTestUrl('8001') . '?seconds=70');
        $multi_curl->addGet(Test::getTestUrl('8003') . '?seconds=10');

        $multi_curl->start();
        $request_stats = \Helper\get_request_stats($request_stats, $multi_curl);

        // Assert R1 starts around 60 and not before.
        $this->assertGreaterThanOrEqual(60, $request_stats['1']['relative_start']);
        $this->assertLessThanOrEqual(60.5, $request_stats['1']['relative_start']);
        // Assert R2 starts around 120 and not before.
        $this->assertGreaterThanOrEqual(120, $request_stats['2']['relative_start']);
        $this->assertLessThanOrEqual(120.5, $request_stats['2']['relative_start']);
    }

    public function testSetRateLimitThreePerOneMinute()
    {
        $request_stats = [];

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'timeout');
        $multi_curl->setRateLimit('3/1m');
        $multi_curl->beforeSend(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id] = [];
            $request_stats[$instance->id]['start'] = microtime(true);
        });
        $multi_curl->complete(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id]['stop'] = microtime(true);
        });

        $multi_curl->addGet(Test::getTestUrl('8004') . '?seconds=20');
        $multi_curl->addGet(Test::getTestUrl('8002') . '?seconds=65');
        $multi_curl->addGet(Test::getTestUrl('8001') . '?seconds=45');
        $multi_curl->addGet(Test::getTestUrl('8003') . '?seconds=10');

        $multi_curl->start();
        $request_stats = \Helper\get_request_stats($request_stats, $multi_curl);

        // Assert R3 starts around 60 and not before.
        $this->assertGreaterThanOrEqual(60, $request_stats['3']['relative_start']);
        $this->assertLessThanOrEqual(60.5, $request_stats['3']['relative_start']);
    }

    public function testSetRateLimitThreePerSixtyFiveSeconds()
    {
        $request_stats = [];

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'timeout');
        $multi_curl->setRateLimit('3/65s');
        $multi_curl->beforeSend(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id] = [];
            $request_stats[$instance->id]['start'] = microtime(true);
        });
        $multi_curl->complete(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id]['stop'] = microtime(true);
        });

        $multi_curl->addGet(Test::getTestUrl('8001') . '?seconds=5');
        $multi_curl->addGet(Test::getTestUrl('8002') . '?seconds=5');
        $multi_curl->addGet(Test::getTestUrl('8003') . '?seconds=5');
        $multi_curl->addGet(Test::getTestUrl('8004') . '?seconds=5');

        $multi_curl->start();
        $request_stats = \Helper\get_request_stats($request_stats, $multi_curl);

        // Assert R3 starts around 65 and not before.
        $this->assertGreaterThanOrEqual(65, $request_stats['3']['relative_start']);
        $this->assertLessThanOrEqual(65.5, $request_stats['3']['relative_start']);
    }

    public function testSetRateLimitTenPerTwoMinutes()
    {
        $request_stats = [];

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'timeout');
        $multi_curl->setRateLimit('10/2m');
        $multi_curl->beforeSend(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id] = [];
            $request_stats[$instance->id]['start'] = microtime(true);
        });
        $multi_curl->complete(function ($instance) use (&$request_stats) {
            $request_stats[$instance->id]['stop'] = microtime(true);
        });

        for ($i = 0; $i <= 30; $i++) {
            $multi_curl->addGet(Test::TEST_URL . '?seconds=1');
        }

        $multi_curl->start();
        $request_stats = \Helper\get_request_stats($request_stats, $multi_curl);

        // 0-9 starts >= 0.
        // 10-19 starts >= 120.
        // 20-29 starts >= 240.
        // 30-39 starts >= 360.

        // Assert R0 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['0']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['0']['relative_start']);
        // Assert R9 starts around 0 and not before.
        $this->assertGreaterThanOrEqual(0, $request_stats['9']['relative_start']);
        $this->assertLessThanOrEqual(0.5, $request_stats['9']['relative_start']);

        // Assert R10 starts around 120 and not before.
        $this->assertGreaterThanOrEqual(120, $request_stats['10']['relative_start']);
        $this->assertLessThanOrEqual(120.5, $request_stats['10']['relative_start']);
        // Assert R19 starts around 120 and not before.
        $this->assertGreaterThanOrEqual(120, $request_stats['19']['relative_start']);
        $this->assertLessThanOrEqual(120.5, $request_stats['19']['relative_start']);

        // Assert R20 starts around 240. Allow for some drift.
        $this->assertGreaterThanOrEqual(239, $request_stats['20']['relative_start']);
        $this->assertLessThanOrEqual(241, $request_stats['20']['relative_start']);
        // Assert R29 starts around 240. Allow for some drift.
        $this->assertGreaterThanOrEqual(239, $request_stats['29']['relative_start']);
        $this->assertLessThanOrEqual(241, $request_stats['29']['relative_start']);

        // Assert R30 starts around 360. Allow for some drift.
        $this->assertGreaterThanOrEqual(359, $request_stats['30']['relative_start']);
        $this->assertLessThanOrEqual(361, $request_stats['30']['relative_start']);
    }

    public function testSetHeadersAssociativeArray()
    {
        $multi_curl = new MultiCurl();
        $multi_curl->setHeaders([
            ' Key1 ' => ' Value1 ',
            ' Key2 ' => ' Value2',
            ' Key3 ' => 'Value3 ',
            ' Key4 ' => 'Value4',
            ' Key5' => ' Value5 ',
            ' Key6' => ' Value6',
            ' Key7' => 'Value7 ',
            ' Key8' => 'Value8',
            'Key9 ' => ' Value9 ',
            'Key10 ' => ' Value10',
            'Key11 ' => 'Value11 ',
            'Key12 ' => 'Value12',
            'Key13' => ' Value13 ',
            'Key14' => ' Value14',
            'Key15' => 'Value15 ',
            'Key16' => 'Value16',
        ]);
        $multi_curl->addGet(Test::TEST_URL);

        $curls = \Helper\get_multi_curl_property_value($multi_curl, 'curls');
        foreach ($curls as $curl) {
            $this->assertEquals([
                'Key1: Value1',
                'Key2: Value2',
                'Key3: Value3',
                'Key4: Value4',
                'Key5: Value5',
                'Key6: Value6',
                'Key7: Value7',
                'Key8: Value8',
                'Key9: Value9',
                'Key10: Value10',
                'Key11: Value11',
                'Key12: Value12',
                'Key13: Value13',
                'Key14: Value14',
                'Key15: Value15',
                'Key16: Value16',
            ], $curl->getOpt(CURLOPT_HTTPHEADER));

            $headers = \Helper\get_curl_property_value($curl, 'headers');
            $this->assertEquals('Value1', $headers['Key1']);
            $this->assertEquals('Value2', $headers['Key2']);
            $this->assertEquals('Value3', $headers['Key3']);
            $this->assertEquals('Value4', $headers['Key4']);
            $this->assertEquals('Value5', $headers['Key5']);
            $this->assertEquals('Value6', $headers['Key6']);
            $this->assertEquals('Value7', $headers['Key7']);
            $this->assertEquals('Value8', $headers['Key8']);
            $this->assertEquals('Value9', $headers['Key9']);
            $this->assertEquals('Value10', $headers['Key10']);
            $this->assertEquals('Value11', $headers['Key11']);
            $this->assertEquals('Value12', $headers['Key12']);
            $this->assertEquals('Value13', $headers['Key13']);
            $this->assertEquals('Value14', $headers['Key14']);
            $this->assertEquals('Value15', $headers['Key15']);
            $this->assertEquals('Value16', $headers['Key16']);
        }
    }

    public function testSetHeadersIndexedArray()
    {
        $multi_curl = new MultiCurl();
        $multi_curl->setHeaders([
            ' Key1 : Value1 ',
            ' Key2 : Value2',
            ' Key3 :Value3 ',
            ' Key4 :Value4',
            ' Key5: Value5 ',
            ' Key6: Value6',
            ' Key7:Value7 ',
            ' Key8:Value8',
            'Key9 : Value9 ',
            'Key10 : Value10',
            'Key11 :Value11 ',
            'Key12 :Value12',
            'Key13: Value13 ',
            'Key14: Value14',
            'Key15:Value15 ',
            'Key16:Value16',
        ]);
        $multi_curl->addGet(Test::TEST_URL);

        $curls = \Helper\get_multi_curl_property_value($multi_curl, 'curls');
        foreach ($curls as $curl) {
            $this->assertEquals([
                'Key1: Value1',
                'Key2: Value2',
                'Key3: Value3',
                'Key4: Value4',
                'Key5: Value5',
                'Key6: Value6',
                'Key7: Value7',
                'Key8: Value8',
                'Key9: Value9',
                'Key10: Value10',
                'Key11: Value11',
                'Key12: Value12',
                'Key13: Value13',
                'Key14: Value14',
                'Key15: Value15',
                'Key16: Value16',
            ], $curl->getOpt(CURLOPT_HTTPHEADER));

            $headers = \Helper\get_curl_property_value($curl, 'headers');
            $this->assertEquals('Value1', $headers['Key1']);
            $this->assertEquals('Value2', $headers['Key2']);
            $this->assertEquals('Value3', $headers['Key3']);
            $this->assertEquals('Value4', $headers['Key4']);
            $this->assertEquals('Value5', $headers['Key5']);
            $this->assertEquals('Value6', $headers['Key6']);
            $this->assertEquals('Value7', $headers['Key7']);
            $this->assertEquals('Value8', $headers['Key8']);
            $this->assertEquals('Value9', $headers['Key9']);
            $this->assertEquals('Value10', $headers['Key10']);
            $this->assertEquals('Value11', $headers['Key11']);
            $this->assertEquals('Value12', $headers['Key12']);
            $this->assertEquals('Value13', $headers['Key13']);
            $this->assertEquals('Value14', $headers['Key14']);
            $this->assertEquals('Value15', $headers['Key15']);
            $this->assertEquals('Value16', $headers['Key16']);
        }
    }
}
