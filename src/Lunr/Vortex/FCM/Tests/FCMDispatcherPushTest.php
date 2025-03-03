<?php

/**
 * This file contains the FCMDispatcherPushTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2017 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Vortex\FCM\Tests;

use Lunr\Vortex\PushNotificationStatus;
use Lunr\Vortex\APNS\APNSPayload;
use Lunr\Vortex\Email\EmailPayload;
use Lunr\Vortex\JPush\JPushMessagePayload;
use Lunr\Vortex\WNS\WNSTilePayload;
use WpOrg\Requests\Exception as RequestsException;

/**
 * This class contains test for the push() method of the FCMDispatcher class.
 *
 * @covers Lunr\Vortex\FCM\FCMDispatcher
 */
class FCMDispatcherPushTest extends FCMDispatcherTest
{

    /**
     * Unit test data provider for unsupported payload objects.
     *
     * @return array Unsupported payload objects
     */
    public static function unsupportedPayloadProvider(): array
    {
        $data          = [];
        $data['apns']  = [ new APNSPayload() ];
        $data['email'] = [ new EmailPayload() ];
        $data['jpush'] = [ new JPushMessagePayload() ];
        $data['wns']   = [ new WNSTilePayload() ];

        return $data;
    }

    /**
     * Test that push() throws an exception is the passed payload object is not supported.
     *
     * @param object $payload Unsupported payload object
     *
     * @dataProvider unsupportedPayloadProvider
     * @covers       Lunr\Vortex\FCM\FCMDispatcher::push
     */
    public function testPushingWithUnsupportedPayloadThrowsException($payload): void
    {
        $endpoints = [ 'endpoint' ];

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Invalid payload object!');

        $this->class->push($payload, $endpoints);
    }

    /**
     * Test that push() returns FCMResponseObject.
     *
     * @covers Lunr\Vortex\FCM\FCMDispatcher::push
     */
    public function testPushReturnsFCMResponseObject(): void
    {
        $endpoints = [];

        $this->constant_redefine('Lunr\Vortex\FCM\FCMDispatcher::BATCH_SIZE', 2);

        $result = $this->class->push($this->payload, $endpoints);

        $this->assertInstanceOf('Lunr\Vortex\FCM\FCMResponse', $result);
    }

    /**
     * Test that push_batch() returns FCMBatchResponse.
     *
     * @covers Lunr\Vortex\FCM\FCMDispatcher::push_batch
     */
    public function testPushBatchReturnsFCMBatchResponseObject(): void
    {
        $endpoints = [ 'endpoint' ];

        $response = $this->getMockBuilder('WpOrg\Requests\Response')->getMock();

        $this->http->expects($this->once())
                   ->method('post')
                   ->will($this->returnValue($response));

        $method = $this->get_accessible_reflection_method('push_batch');
        $result = $method->invokeArgs($this->class, [ $this->payload, &$endpoints ]);

        $this->assertInstanceOf('Lunr\Vortex\FCM\FCMBatchResponse', $result);
    }

    /**
     * Test that push() doesn't send any request if no endpoint is set.
     *
     * @covers Lunr\Vortex\FCM\FCMDispatcher::push
     */
    public function testPushDoesNoRequestIfNoEndpoint(): void
    {
        $endpoints = [];

        $this->http->expects($this->never())
                   ->method('post');

        $this->class->push($this->payload, $endpoints);
    }

    /**
     * Test that push() resets the properties after a push.
     *
     * @covers Lunr\Vortex\FCM\FCMDispatcher::push
     */
    public function testPushResetsProperties(): void
    {
        $endpoints = [ 'endpoint' ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('{"collapse_key":"abcde-12345"}');

        $this->set_reflection_property_value('auth_token', 'auth_token');

        $response = $this->getMockBuilder('WpOrg\Requests\Response')->getMock();

        $this->http->expects($this->once())
                   ->method('post')
                   ->will($this->returnValue($response));

        $this->class->push($this->payload, $endpoints);

        $this->assertPropertyEquals('auth_token', 'auth_token');
    }

    /**
     * Test that push() works as expected when the request failed.
     *
     * @covers Lunr\Vortex\FCM\FCMDispatcher::push
     */
    public function testPushWithFailedRequest(): void
    {
        $this->mock_function('curl_errno', function () { return 10; });

        $endpoints = [ 'endpoint' ];

        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'key=',
        ];

        $url  = 'https://fcm.googleapis.com/fcm/send';
        $post = '{"to":"endpoint"}';

        $options = [
            'timeout'         => 15,
            'connect_timeout' => 15
        ];

        $this->http->expects($this->once())
                   ->method('post')
                   ->with($this->equalTo($url), $this->equalTo($headers), $this->equalTo($post), $this->equalTo($options))
                   ->will($this->throwException(new RequestsException('cURL error 10: Request error', 'curlerror', NULL)));

        $message = 'Dispatching FCM notification(s) failed: {message}';
        $context = [ 'message' => 'cURL error 10: Request error' ];

        $this->logger->expects($this->exactly(2))
                     ->method('warning')
                     ->withConsecutive([ $message, $context ], [ 'Dispatching push notification failed: {error}' ]);

        $result = $this->class->push($this->payload, $endpoints);

        $this->assertInstanceOf('Lunr\Vortex\FCM\FCMResponse', $result);

        $this->unmock_function('curl_errno');
    }

    /**
     * Test that push() works as expected when the request failed.
     *
     * @covers Lunr\Vortex\FCM\FCMDispatcher::push
     */
    public function testPushWithTimeoutRequest()
    {
        $this->mock_function('curl_errno', function () { return 28; });

        $endpoints = [ 'endpoint' ];

        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'key=',
        ];

        $url  = 'https://fcm.googleapis.com/fcm/send';
        $post = '{"to":"endpoint"}';

        $options = [
            'timeout'         => 15,
            'connect_timeout' => 15
        ];

        $this->http->expects($this->once())
                   ->method('post')
                   ->with($this->equalTo($url), $this->equalTo($headers), $this->equalTo($post), $this->equalTo($options))
                   ->will($this->throwException(new RequestsException('cURL error 28: Request timed out', 'curlerror', NULL)));

        $message = 'Dispatching FCM notification(s) failed: {message}';
        $context = [ 'message' => 'cURL error 28: Request timed out' ];

        $this->logger->expects($this->exactly(2))
                     ->method('warning')
                     ->withConsecutive([ $message, $context ], [ 'Dispatching push notification failed: {error}' ]);

        $result = $this->class->push($this->payload, $endpoints);

        $this->assertInstanceOf('Lunr\Vortex\FCM\FCMResponse', $result);

        $this->assertSame($result->get_status('endpoint'), PushNotificationStatus::TEMPORARY_ERROR);

        $this->unmock_function('curl_errno');
    }

    /**
     * Test that push() sends correct request with no properties set except the endpoint.
     *
     * @covers Lunr\Vortex\FCM\FCMDispatcher::push
     */
    public function testPushRequestWithDefaultValues(): void
    {
        $endpoints = [ 'endpoint' ];

        $response = $this->getMockBuilder('WpOrg\Requests\Response')->getMock();

        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'key=',
        ];

        $url  = 'https://fcm.googleapis.com/fcm/send';
        $post = '{"to":"endpoint"}';

        $options = [
            'timeout'         => 15,
            'connect_timeout' => 15
        ];

        $this->http->expects($this->once())
                   ->method('post')
                   ->with($this->equalTo($url), $this->equalTo($headers), $this->equalTo($post), $this->equalTo($options))
                   ->will($this->returnValue($response));

        $this->class->push($this->payload, $endpoints);
    }

    /**
     * Test that push() sends correct request with single endpoint.
     *
     * @covers Lunr\Vortex\FCM\FCMDispatcher::push
     */
    public function testPushRequestWithSingleEndpoint(): void
    {
        $endpoints = [ 'endpoint' ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('{"collapse_key":"abcde-12345"}');

        $this->set_reflection_property_value('auth_token', 'auth_token');

        $response = $this->getMockBuilder('WpOrg\Requests\Response')->getMock();

        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'key=auth_token',
        ];

        $url  = 'https://fcm.googleapis.com/fcm/send';
        $post = '{"collapse_key":"abcde-12345","to":"endpoint"}';

        $options = [
            'timeout'         => 15,
            'connect_timeout' => 15
        ];

        $this->http->expects($this->once())
                   ->method('post')
                   ->with($this->equalTo($url), $this->equalTo($headers), $this->equalTo($post), $this->equalTo($options))
                   ->will($this->returnValue($response));

        $this->class->push($this->payload, $endpoints);
    }

    /**
     * Test that push() generates a payload that doesn't encode unicode characters.
     *
     * @covers Lunr\Vortex\FCM\FCMDispatcher::push
     */
    public function testPushRequestWithMultibyteCharacters(): void
    {
        $endpoints = [ 'endpoint' ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('{"collapse_key":"abcde-12345","data":{"message":"凄い"}}');

        $this->set_reflection_property_value('auth_token', 'auth_token');

        $response = $this->getMockBuilder('WpOrg\Requests\Response')->getMock();

        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'key=auth_token',
        ];

        $url  = 'https://fcm.googleapis.com/fcm/send';
        $post = '{"collapse_key":"abcde-12345","data":{"message":"凄い"},"to":"endpoint"}';

        $options = [
            'timeout'         => 15,
            'connect_timeout' => 15
        ];

        $this->http->expects($this->once())
                   ->method('post')
                   ->with($url, $headers, $post, $options)
                   ->willReturn($response);

        $this->class->push($this->payload, $endpoints);
    }

    /**
     * Test that push() sends correct request with multiple endpoints within one batch.
     *
     * @covers Lunr\Vortex\FCM\FCMDispatcher::push
     */
    public function testPushRequestWithMultipleEndpointsOneBatch(): void
    {
        $endpoints = [ 'endpoint1', 'endpoint2' ];

        $this->payload->expects($this->once())
                      ->method('get_payload')
                      ->willReturn('{"collapse_key":"abcde-12345"}');

        $this->set_reflection_property_value('auth_token', 'auth_token');

        $response = $this->getMockBuilder('WpOrg\Requests\Response')->getMock();

        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'key=auth_token',
        ];

        $url  = 'https://fcm.googleapis.com/fcm/send';
        $post = '{"collapse_key":"abcde-12345","registration_ids":["endpoint1","endpoint2"]}';

        $options = [
            'timeout'         => 15,
            'connect_timeout' => 15
        ];

        $this->http->expects($this->once())
                   ->method('post')
                   ->with($this->equalTo($url), $this->equalTo($headers), $this->equalTo($post), $this->equalTo($options))
                   ->will($this->returnValue($response));

        $this->class->push($this->payload, $endpoints);
    }

    /**
     * Test that push() sends correct request with multiple endpoints within multiple batches.
     *
     * @covers Lunr\Vortex\FCM\FCMDispatcher::push
     */
    public function testPushRequestWithMultipleEndpointsMultipleBatches(): void
    {
        $endpoints = [ 'endpoint1', 'endpoint2', 'endpoint3', 'endpoint4', 'endpoint5' ];

        $this->payload->expects($this->exactly(3))
                      ->method('get_payload')
                      ->willReturn('{"collapse_key":"abcde-12345"}');

        $this->set_reflection_property_value('auth_token', 'auth_token');

        $response = $this->getMockBuilder('WpOrg\Requests\Response')->getMock();

        $headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'key=auth_token',
        ];

        $options = [
            'timeout'         => 15,
            'connect_timeout' => 15
        ];

        $url = 'https://fcm.googleapis.com/fcm/send';

        $pos = 0;

        $post1 = '{"collapse_key":"abcde-12345","registration_ids":["endpoint1","endpoint2"]}';
        $post2 = '{"collapse_key":"abcde-12345","registration_ids":["endpoint3","endpoint4"]}';
        $post3 = '{"collapse_key":"abcde-12345","to":"endpoint5"}';

        $this->http->expects($this->exactly(3))
                   ->method('post')
                   ->withConsecutive([ $url, $headers, $post1, $options ], [ $url, $headers, $post2, $options ], [ $url, $headers, $post3, $options ])
                   ->will($this->returnValue($response));

        $this->class->push($this->payload, $endpoints);
    }

}

?>
