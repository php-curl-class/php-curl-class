<?php

namespace CurlTest;

use Curl\Curl;
use Curl\MultiCurl;
use Helper\Test;

class NotWaitedMultiCurlTest extends \PHPUnit\Framework\TestCase
{
    public function testCurlCallback()
    {
        $multi_curl = new MultiCurl();

        $delete_before_send_called = false;
        $delete_success_called = false;
        $delete_error_called = false;
        $delete_complete_called = false;
        $delete = new Curl();
        $delete->delete(Test::TEST_URL, array(), array(), false);
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
        $multi_curl->addCurl($delete);

        $download_before_send_called = false;
        $download_success_called = false;
        $download_error_called = false;
        $download_complete_called = false;
        $download_file_path = tempnam('/tmp', 'php-curl-class.');
        $download = new Curl();
        $download->download(Test::TEST_URL, $download_file_path, false);
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
        $multi_curl->addCurl($download);

        $get_before_send_called = false;
        $get_success_called = false;
        $get_error_called = false;
        $get_complete_called = false;
        $get = new Curl();
        $get->get(Test::TEST_URL, array(), false);
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
        $multi_curl->addCurl($get);

        $head_before_send_called = false;
        $head_success_called = false;
        $head_error_called = false;
        $head_complete_called = false;
        $head = new Curl();
        $head->head(Test::TEST_URL, array(), false);
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
        $multi_curl->addCurl($head);

        $options_before_send_called = false;
        $options_success_called = false;
        $options_error_called = false;
        $options_complete_called = false;
        $options = new Curl();
        $options->options(Test::TEST_URL, array(), false);
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
        $multi_curl->addCurl($options);

        $patch_before_send_called = false;
        $patch_success_called = false;
        $patch_error_called = false;
        $patch_complete_called = false;
        $patch = new Curl();
        $patch->patch(Test::TEST_URL, array(), false);
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
        $multi_curl->addCurl($patch);

        $post_before_send_called = false;
        $post_success_called = false;
        $post_error_called = false;
        $post_complete_called = false;
        $post = new Curl();
        $post->post(Test::TEST_URL, array(), false, false);
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
        $multi_curl->addCurl($post);

        $put_before_send_called = false;
        $put_success_called = false;
        $put_error_called = false;
        $put_complete_called = false;
        $put = new Curl();
        $put->put(Test::TEST_URL, array(), false);
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
        $multi_curl->addCurl($put);

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
    public function testCurlCallbackError()
    {
        $multi_curl = new MultiCurl();

        $delete_before_send_called = false;
        $delete_success_called = false;
        $delete_error_called = false;
        $delete_complete_called = false;
        $delete = new Curl();
        $delete->delete(Test::ERROR_URL, array(), array(), false);
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
        $multi_curl->addCurl($delete);

        $download_before_send_called = false;
        $download_success_called = false;
        $download_error_called = false;
        $download_complete_called = false;
        $download_file_path = tempnam('/tmp', 'php-curl-class.');
        $download = new Curl();
        $download->download(Test::ERROR_URL, $download_file_path, false);
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
        $multi_curl->addCurl($download);

        $get_before_send_called = false;
        $get_success_called = false;
        $get_error_called = false;
        $get_complete_called = false;
        $get = new Curl();
        $get->get(Test::ERROR_URL, array(), false);
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
        $multi_curl->addCurl($get);

        $head_before_send_called = false;
        $head_success_called = false;
        $head_error_called = false;
        $head_complete_called = false;
        $head = new Curl();
        $head->head(Test::ERROR_URL, array(), false);
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
        $multi_curl->addCurl($head);

        $options_before_send_called = false;
        $options_success_called = false;
        $options_error_called = false;
        $options_complete_called = false;
        $options = new Curl();
        $options->options(Test::ERROR_URL, array(), false);
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
        $multi_curl->addCurl($options);

        $patch_before_send_called = false;
        $patch_success_called = false;
        $patch_error_called = false;
        $patch_complete_called = false;
        $patch = new Curl();
        $patch->patch(Test::ERROR_URL, array(), false);
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
        $multi_curl->addCurl($patch);

        $post_before_send_called = false;
        $post_success_called = false;
        $post_error_called = false;
        $post_complete_called = false;
        $post = new Curl();
        $post->post(Test::ERROR_URL, array(), false, false);
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
        $multi_curl->addCurl($post);

        $put_before_send_called = false;
        $put_success_called = false;
        $put_error_called = false;
        $put_complete_called = false;
        $put = new Curl();
        $put->put(Test::ERROR_URL, array(), false);
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
        $multi_curl->addCurl($put);

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
        $delete = new Curl();
        $delete->delete(Test::TEST_URL, array(), array(), false);
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
        $multi_curl->addCurl($delete);

        $download_before_send_called = false;
        $download_success_called = false;
        $download_error_called = false;
        $download_complete_called = false;
        $download_file_path = tempnam('/tmp', 'php-curl-class.');
        $download = new Curl();
        $download->download(Test::TEST_URL, $download_file_path, false);
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
        $multi_curl->addCurl($download);

        $get_before_send_called = false;
        $get_success_called = false;
        $get_error_called = false;
        $get_complete_called = false;
        $get = new Curl();
        $get->get(Test::TEST_URL, array(), false);
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
        $multi_curl->addCurl($get);

        $head_before_send_called = false;
        $head_success_called = false;
        $head_error_called = false;
        $head_complete_called = false;
        $head = new Curl();
        $head->head(Test::TEST_URL, array(), false);
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
        $multi_curl->addCurl($head);

        $options_before_send_called = false;
        $options_success_called = false;
        $options_error_called = false;
        $options_complete_called = false;
        $options = new Curl();
        $options->options(Test::TEST_URL, array(), false);
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
        $multi_curl->addCurl($options);

        $patch_before_send_called = false;
        $patch_success_called = false;
        $patch_error_called = false;
        $patch_complete_called = false;
        $patch = new Curl();
        $patch->patch(Test::TEST_URL, array(), false);
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
        $multi_curl->addCurl($patch);

        $post_before_send_called = false;
        $post_success_called = false;
        $post_error_called = false;
        $post_complete_called = false;
        $post = new Curl();
        $post->post(Test::TEST_URL, array(), false, false);
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
        $multi_curl->addCurl($post);

        $put_before_send_called = false;
        $put_success_called = false;
        $put_error_called = false;
        $put_complete_called = false;
        $put = new Curl();
        $put->put(Test::TEST_URL, array(), false);
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
        $multi_curl->addCurl($put);

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
        $this->assertFalse($delete_before_send_called);
        $this->assertFalse($delete_success_called);
        $this->assertFalse($delete_error_called);
        $this->assertFalse($delete_complete_called);
        $multi_curl->addDelete(Test::TEST_URL);
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
        $this->assertFalse($download_before_send_called);
        $this->assertFalse($download_success_called);
        $this->assertFalse($download_error_called);
        $this->assertFalse($download_complete_called);
        $this->assertTrue(unlink($download_file_path));
        $download_file_path = tempnam('/tmp', 'php-curl-class.');
        $multi_curl->addDownload(Test::TEST_URL, $download_file_path);
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
        $this->assertFalse($get_before_send_called);
        $this->assertFalse($get_success_called);
        $this->assertFalse($get_error_called);
        $this->assertFalse($get_complete_called);
        $multi_curl->addGet(Test::TEST_URL);
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
        $this->assertFalse($head_before_send_called);
        $this->assertFalse($head_success_called);
        $this->assertFalse($head_error_called);
        $this->assertFalse($head_complete_called);
        $multi_curl->addHead(Test::TEST_URL);
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
        $this->assertFalse($options_before_send_called);
        $this->assertFalse($options_success_called);
        $this->assertFalse($options_error_called);
        $this->assertFalse($options_complete_called);
        $multi_curl->addOptions(Test::TEST_URL);
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
        $this->assertFalse($patch_before_send_called);
        $this->assertFalse($patch_success_called);
        $this->assertFalse($patch_error_called);
        $this->assertFalse($patch_complete_called);
        $multi_curl->addPatch(Test::TEST_URL);
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
        $this->assertFalse($post_before_send_called);
        $this->assertFalse($post_success_called);
        $this->assertFalse($post_error_called);
        $this->assertFalse($post_complete_called);
        $multi_curl->addPost(Test::TEST_URL);
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
        $this->assertFalse($put_before_send_called);
        $this->assertFalse($put_success_called);
        $this->assertFalse($put_error_called);
        $this->assertFalse($put_complete_called);
        $multi_curl->addPut(Test::TEST_URL);
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

        $get_1 = new Curl();
        $get_1->get(Test::TEST_URL, $data);
        $get_1->complete(function ($instance) use ($multi_curl_user_agent) {
            \PHPUnit\Framework\Assert::assertEquals($multi_curl_user_agent, $instance->getOpt(CURLOPT_USERAGENT));
        });
        $multi_curl->addCurl($get_1);

        $get_2 = new Curl();
        $get_2->get(Test::TEST_URL, $data);
        $get_2->complete(function ($instance) use ($multi_curl_user_agent) {
            \PHPUnit\Framework\Assert::assertEquals($multi_curl_user_agent, $instance->getOpt(CURLOPT_USERAGENT));
            \PHPUnit\Framework\Assert::assertEquals($multi_curl_user_agent, $instance->response);
        });
        $multi_curl->addCurl($get_2);

        $get_3 = new Curl();
        $get_3->get(Test::TEST_URL, $data);
        $get_3->beforeSend(function ($instance) use ($curl_user_agent) {
            $instance->setOpt(CURLOPT_USERAGENT, $curl_user_agent);
        });
        $get_3->complete(function ($instance) use ($curl_user_agent) {
            \PHPUnit\Framework\Assert::assertEquals($curl_user_agent, $instance->getOpt(CURLOPT_USERAGENT));
            \PHPUnit\Framework\Assert::assertEquals($curl_user_agent, $instance->response);
        });
        $multi_curl->addCurl($get_3);

        $multi_curl->start();
    }

    public function testSetHeaderAndOverride()
    {
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('header-for-all-before', 'a');

        $get_1 = new Curl();
        $get_1->get(Test::TEST_URL, array(), false);
        $get_1->setHeader('header-for-1st-request', '1');
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->requestHeaders['header-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('1', $instance->requestHeaders['header-for-1st-request']);
        });
        $multi_curl->addCurl($get_1);

        $get_2 = new Curl();
        $get_2->get(Test::TEST_URL, array(), false);
        $get_2->setHeader('header-for-2nd-request', '2');
        $get_2->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->requestHeaders['header-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('2', $instance->requestHeaders['header-for-2nd-request']);
        });
        $multi_curl->addCurl($get_2);

        $multi_curl->setHeader('header-for-all-after', 'b');
        $multi_curl->start();

        $get_1 = new Curl();
        $get_1->get(Test::TEST_URL, array(), false);
        $get_1->setHeader('header-for-1st-request', '1');
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->requestHeaders['header-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('b', $instance->requestHeaders['header-for-all-after']);
            \PHPUnit\Framework\Assert::assertEquals('1', $instance->requestHeaders['header-for-1st-request']);
        });
        $multi_curl->addCurl($get_1);

        $get_2 = new Curl();
        $get_2->get(Test::TEST_URL, array(), false);
        $get_2->setHeader('header-for-2nd-request', '2');
        $get_2->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->requestHeaders['header-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('b', $instance->requestHeaders['header-for-all-after']);
            \PHPUnit\Framework\Assert::assertEquals('2', $instance->requestHeaders['header-for-2nd-request']);
        });
        $multi_curl->addCurl($get_2);

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

        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(CURLAUTH_BASIC, $instance->getOpt(CURLOPT_HTTPAUTH));
        });
        $multi_curl->addGet(Test::TEST_URL);

        $multi_curl->complete(function ($instance) use ($username1, $password1) {
            \PHPUnit\Framework\Assert::assertEquals($username1, $instance->response->username);
            \PHPUnit\Framework\Assert::assertEquals($password1, $instance->response->password);
            \PHPUnit\Framework\Assert::assertEquals(CURLAUTH_BASIC, $instance->getOpt(CURLOPT_HTTPAUTH));
        });
        $multi_curl->addGet(Test::TEST_URL);

        $multi_curl->beforeSend(function ($instance) use ($username2, $password2) {
            $instance->setBasicAuthentication($username2, $password2);
        });
        $multi_curl->complete(function ($instance) use ($username2, $password2) {
            \PHPUnit\Framework\Assert::assertEquals($username2, $instance->response->username);
            \PHPUnit\Framework\Assert::assertEquals($password2, $instance->response->password);
            \PHPUnit\Framework\Assert::assertEquals(CURLAUTH_BASIC, $instance->getOpt(CURLOPT_HTTPAUTH));
        });
        $multi_curl->addGet(Test::TEST_URL);

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

        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('canceled', $instance->response);
            \PHPUnit\Framework\Assert::assertEquals(401, $instance->httpStatusCode);
        });
        $multi_curl->addGet(Test::TEST_URL);
        $multi_curl->start();

        // Ensure that http digest returns invalid when using incorrect http digest authentication.
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'http_digest_auth');
        $multi_curl->setDigestAuthentication($username, $invalid_password);
        $this->assertEquals(CURLAUTH_DIGEST, $multi_curl->getOpt(CURLOPT_HTTPAUTH));

        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(CURLAUTH_DIGEST, $instance->getOpt(CURLOPT_HTTPAUTH));
            \PHPUnit\Framework\Assert::assertEquals('invalid', $instance->response);
            \PHPUnit\Framework\Assert::assertEquals(401, $instance->httpStatusCode);
        });
        $multi_curl->addGet(Test::TEST_URL);
        $multi_curl->start();

        // Ensure that http digest returns valid when using correct http digest authentication.
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'http_digest_auth');
        $multi_curl->setDigestAuthentication($username, $password);
        $this->assertEquals(CURLAUTH_DIGEST, $multi_curl->getOpt(CURLOPT_HTTPAUTH));

        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(CURLAUTH_DIGEST, $instance->getOpt(CURLOPT_HTTPAUTH));
            \PHPUnit\Framework\Assert::assertEquals('valid', $instance->response);
            \PHPUnit\Framework\Assert::assertEquals(200, $instance->httpStatusCode);
        });
        $multi_curl->addGet(Test::TEST_URL);
        $multi_curl->start();

        // Ensure that http digest can return both invalid and valid when using
        // incorrect and correct authentication in the same MultiCurl.
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'http_digest_auth');
        $multi_curl->setDigestAuthentication($username, $password);
        $this->assertEquals(CURLAUTH_DIGEST, $multi_curl->getOpt(CURLOPT_HTTPAUTH));

        $multi_curl->beforeSend(function ($instance) use ($username, $invalid_password) {
            $instance->setDigestAuthentication($username, $invalid_password);
            \PHPUnit\Framework\Assert::assertEquals(CURLAUTH_DIGEST, $instance->getOpt(CURLOPT_HTTPAUTH));
        });
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(CURLAUTH_DIGEST, $instance->getOpt(CURLOPT_HTTPAUTH));
            \PHPUnit\Framework\Assert::assertEquals('invalid', $instance->response);
            \PHPUnit\Framework\Assert::assertEquals(401, $instance->httpStatusCode);
        });
        $multi_curl->addGet(Test::TEST_URL);

        $multi_curl->beforeSend(null);
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(CURLAUTH_DIGEST, $instance->getOpt(CURLOPT_HTTPAUTH));
            \PHPUnit\Framework\Assert::assertEquals('valid', $instance->response);
            \PHPUnit\Framework\Assert::assertEquals(200, $instance->httpStatusCode);
        });
        $multi_curl->addGet(Test::TEST_URL);

        $multi_curl->start();
    }

    public function testSetCookie()
    {
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'setcookie');
        $multi_curl->setCookie('mycookie', 'yum');
        $multi_curl->setCookie('cookie-for-all-before', 'a');

        $get_1 = new Curl();
        $get_1->get(Test::TEST_URL, array(), false);
        $get_1->setCookie('cookie-for-1st-request', '1');
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('yum', $instance->responseCookies['mycookie']);
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->responseCookies['cookie-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('1', $instance->responseCookies['cookie-for-1st-request']);
        });
        $multi_curl->addCurl($get_1);

        $get_2 = new Curl();
        $get_2->get(Test::TEST_URL);
        $get_2->setCookie('cookie-for-2nd-request', '2');
        $get_2->beforeSend(function ($instance) {
            $instance->setCookie('mycookie', 'yummy');
        });
        $get_2->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('yummy', $instance->responseCookies['mycookie']);
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->responseCookies['cookie-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('2', $instance->responseCookies['cookie-for-2nd-request']);
        });
        $multi_curl->addCurl($get_2);

        $multi_curl->setCookie('cookie-for-all-after', 'b');
        $multi_curl->start();

        $get_1 = new Curl();
        $get_1->get(Test::TEST_URL, array(), false);
        $get_1->setCookie('cookie-for-1st-request', '1');
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('yum', $instance->responseCookies['mycookie']);
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->responseCookies['cookie-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('b', $instance->responseCookies['cookie-for-all-after']);
            \PHPUnit\Framework\Assert::assertEquals('1', $instance->responseCookies['cookie-for-1st-request']);
        });
        $multi_curl->addCurl($get_1);

        $get_2 = new Curl();
        $get_2->get(Test::TEST_URL);
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
        $multi_curl->addCurl($get_2);

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

        $get_1 = new Curl();
        $get_1->get(Test::TEST_URL, array(), false);
        $get_1->setCookies(array(
            'cookie-for-1st-request' => '1',
        ));
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('yum', $instance->responseCookies['mycookie']);
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->responseCookies['cookie-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('1', $instance->responseCookies['cookie-for-1st-request']);
        });
        $multi_curl->addCurl($get_1);

        $get_2 = new Curl();
        $get_2->get(Test::TEST_URL, array(), false);
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
            \PHPUnit\Framework\Assert::assertEquals('2', $instance->responseCookies['cookie-for-2nd-request']);
        });
        $multi_curl->addCurl($get_2);

        $multi_curl->setCookies(array(
            'cookie-for-all-after' => 'b',
        ));
        $multi_curl->start();

        $get_1 = new Curl();
        $get_1->get(Test::TEST_URL, array(), false);
        $get_1->setCookies(array(
            'cookie-for-1st-request' => '1',
        ));
        $get_1->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('yum', $instance->responseCookies['mycookie']);
            \PHPUnit\Framework\Assert::assertEquals('a', $instance->responseCookies['cookie-for-all-before']);
            \PHPUnit\Framework\Assert::assertEquals('b', $instance->responseCookies['cookie-for-all-after']);
            \PHPUnit\Framework\Assert::assertEquals('1', $instance->responseCookies['cookie-for-1st-request']);
        });
        $multi_curl->addCurl($get_1);

        $get_2 = new Curl();
        $get_2->get(Test::TEST_URL, array(), false);
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
        $multi_curl->addCurl($get_2);

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
        $multi_curl->setJsonDecoder(function ($response) {
            return 'first decoder';
        });

        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('first decoder', $instance->response);
        });
        $post_1 = $multi_curl->addPost(Test::TEST_URL, $data);

        $multi_curl->setJsonDecoder(function ($response) {
            return 'second decoder';
        });
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('second decoder', $instance->response);
        });
        $post_2 = $multi_curl->addPost(Test::TEST_URL, $data);

        $multi_curl->start();
        $this->assertEquals('first decoder', $post_1->response);
        $this->assertEquals('second decoder', $post_2->response);


        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'json_response');

        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_object($instance->response));
            \PHPUnit\Framework\Assert::assertFalse(is_array($instance->response));
        });
        $multi_curl->addPost(Test::TEST_URL, $data);

        $multi_curl->setJsonDecoder(function ($response) {
            return json_decode($response, true);
        });
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertFalse(is_object($instance->response));
            \PHPUnit\Framework\Assert::assertTrue(is_array($instance->response));
        });
        $multi_curl->addPost(Test::TEST_URL, $data);

        $multi_curl->setJsonDecoder(false);
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_string($instance->response));
        });
        $multi_curl->addPost(Test::TEST_URL, $data);

        $multi_curl->start();


        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'json_response');
        $multi_curl->setJsonDecoder(function ($response) {
            return 'foo';
        });

        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('foo', $instance->response);
        });
        $get_1 = $multi_curl->addGet(Test::TEST_URL);

        $multi_curl->beforeSend(function ($instance) {
            $instance->setJsonDecoder(function ($response) {
                return 'bar';
            });
        });
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('bar', $instance->response);
        });
        $get_2 = $multi_curl->addGet(Test::TEST_URL);

        $multi_curl->beforeSend(function ($instance) {
            $instance->setJsonDecoder(false);
        });
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_string($instance->response));
        });
        $multi_curl->addGet(Test::TEST_URL);

        $multi_curl->start();
        $this->assertEquals('foo', $get_1->response);
        $this->assertEquals('bar', $get_2->response);


        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'json_response');
        $multi_curl->setJsonDecoder(false);

        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_string($instance->response));
        });
        $multi_curl->addGet(Test::TEST_URL);

        $multi_curl->start();
    }

    public function testXMLDecoder()
    {
        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'xml_with_cdata_response');
        $multi_curl->setXmlDecoder(function ($response) {
            return 'first decoder';
        });

        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('first decoder', $instance->response);
        });
        $post_1 = $multi_curl->addPost(Test::TEST_URL);

        $multi_curl->setXmlDecoder(function ($response) {
            return 'second decoder';
        });
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('second decoder', $instance->response);
        });
        $post_2 = $multi_curl->addPost(Test::TEST_URL);

        $multi_curl->start();
        $this->assertEquals('first decoder', $post_1->response);
        $this->assertEquals('second decoder', $post_2->response);


        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'xml_with_cdata_response');

        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_object($instance->response));
            \PHPUnit\Framework\Assert::assertInstanceOf('SimpleXMLElement', $instance->response);
            \PHPUnit\Framework\Assert::assertFalse(strpos($instance->response->saveXML(), '<![CDATA[') === false);
        });
        $multi_curl->addPost(Test::TEST_URL);

        $multi_curl->setXmlDecoder(function ($response) {
            return simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
        });
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_object($instance->response));
            \PHPUnit\Framework\Assert::assertInstanceOf('SimpleXMLElement', $instance->response);
            \PHPUnit\Framework\Assert::assertTrue(strpos($instance->response->saveXML(), '<![CDATA[') === false);
        });
        $multi_curl->addPost(Test::TEST_URL);

        $multi_curl->setXmlDecoder(false);
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_string($instance->response));
        });
        $multi_curl->addPost(Test::TEST_URL);

        $multi_curl->start();


        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'xml_with_cdata_response');
        $multi_curl->setXmlDecoder(function ($response) {
            return 'foo';
        });

        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('foo', $instance->response);
        });
        $get_1 = $multi_curl->addGet(Test::TEST_URL);

        $multi_curl->beforeSend(function ($instance) {
            $instance->setXmlDecoder(function ($response) {
                return 'bar';
            });
        });
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('bar', $instance->response);
        });
        $get_2 = $multi_curl->addGet(Test::TEST_URL);

        $multi_curl->beforeSend(function ($instance) {
            $instance->setXmlDecoder(false);
        });
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_string($instance->response));
        });
        $get_3 = $multi_curl->addGet(Test::TEST_URL);

        $multi_curl->start();
        $this->assertEquals('foo', $get_1->response);
        $this->assertEquals('bar', $get_2->response);


        $multi_curl = new MultiCurl();
        $multi_curl->setHeader('X-DEBUG-TEST', 'xml_with_cdata_response');
        $multi_curl->setXmlDecoder(false);

        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertTrue(is_string($instance->response));
        });
        $get_4 = $multi_curl->addGet(Test::TEST_URL);

        $multi_curl->start();
    }

    public function testSetUrlInConstructor()
    {
        $data = array('key' => 'value');

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'delete_with_body');
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                '{"get":{"key":"value"},"delete":{"wibble":"wubble"}}',
                $instance->rawResponse
            );
        });
        $multi_curl->addDelete($data, array('wibble' => 'wubble'));
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'get');
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('key=value', $instance->response);
        });
        $multi_curl->addDelete($data);
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'get');
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('key=value', $instance->response);
        });
        $multi_curl->addGet($data);
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'get');
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'HEAD /?key=value HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
        });
        $multi_curl->addHead($data);
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'get');
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('key=value', $instance->response);
        });
        $multi_curl->addOptions($data);
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'request_method');
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('PATCH', $instance->response);
        });
        $multi_curl->addPatch($data);
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'post');
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('key=value', $instance->response);
        });
        $multi_curl->addPost($data);
        $multi_curl->start();

        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setHeader('X-DEBUG-TEST', 'put');
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('key=value', $instance->response);
        });
        $multi_curl->addPut($data);
        $multi_curl->start();
    }

    public function testSetUrl()
    {
        $data = array('key' => 'value');

        $multi_curl = new MultiCurl();
        $multi_curl->setUrl(Test::TEST_URL);
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'DELETE /?key=value HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL . '?key=value', $instance->effectiveUrl);
        });
        $multi_curl->addDelete($data);
        $multi_curl->start();

        $multi_curl = new MultiCurl();
        $multi_curl->setUrl(Test::TEST_URL);
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'GET /?key=value HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL . '?key=value', $instance->effectiveUrl);
        });
        $multi_curl->addGet($data);
        $multi_curl->start();

        $multi_curl = new MultiCurl();
        $multi_curl->setUrl(Test::TEST_URL);
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'HEAD /?key=value HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL . '?key=value', $instance->effectiveUrl);
        });
        $multi_curl->addHead($data);
        $multi_curl->start();

        $multi_curl = new MultiCurl();
        $multi_curl->setUrl(Test::TEST_URL);
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'OPTIONS /?key=value HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL . '?key=value', $instance->effectiveUrl);
        });
        $multi_curl->addOptions($data);

        $multi_curl = new MultiCurl();
        $multi_curl->setUrl(Test::TEST_URL);
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'PATCH / HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL, $instance->effectiveUrl);
        });
        $multi_curl->addPatch($data);

        $multi_curl = new MultiCurl();
        $multi_curl->setUrl(Test::TEST_URL);
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'POST / HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL, $instance->effectiveUrl);
        });
        $multi_curl->addPost($data);

        $multi_curl = new MultiCurl();
        $multi_curl->setUrl(Test::TEST_URL);
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals(
                'PUT / HTTP/1.1',
                $instance->requestHeaders['Request-Line']
            );
            \PHPUnit\Framework\Assert::assertEquals(Test::TEST_URL, $instance->effectiveUrl);
        });
        $multi_curl->addPut($data);
    }

    public function testMultiPostRedirectGet()
    {
        // Deny post-redirect-get
        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $multi_curl->setHeader('X-DEBUG-TEST', 'post_redirect_get');
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('Redirected: GET', $instance->response);
        });
        $multi_curl->addPost(array(), false);
        $multi_curl->start();

        // Allow post-redirect-get
        $multi_curl = new MultiCurl(Test::TEST_URL);
        $multi_curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $multi_curl->setHeader('X-DEBUG-TEST', 'post_redirect_get');
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('Redirected: POST', $instance->response);
        });
        $multi_curl->addPost(array(), true);
        $multi_curl->start();
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
        $multi_curl->complete(function ($instance) use ($request_value) {
            \PHPUnit\Framework\Assert::assertEquals($request_value, $instance->response);
        });
        $multi_curl->addGet(Test::TEST_URL, $data);
        $multi_curl->start();

        $multi_curl = new MultiCurl();
        $multi_curl->setHeader($request_key, $request_value);
        $multi_curl->unsetHeader($request_key);
        $multi_curl->complete(function ($instance) {
            \PHPUnit\Framework\Assert::assertEquals('', $instance->response);
        });
        $multi_curl->addGet(Test::TEST_URL, $data);
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
        $multi_curl->complete(function ($instance) use (&$complete_called) {
            $complete_called = true;
        });
        $multi_curl->addCurl($curl);
        $multi_curl->start();
        $this->assertTrue($complete_called);
    }
}
