<?php

/**
 * This file contains the FCMResponseAddBatchResponseTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2013 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Vortex\FCM\Tests;

use Lunr\Vortex\PushNotificationStatus;

/**
 * This class contains tests for the add_batch_response function of the FCMResponse class.
 *
 * @covers Lunr\Vortex\FCM\FCMResponse
 */
class FCMResponseAddBatchResponseTest extends FCMResponseTest
{

    /**
     * Test that add_batch_response() with no endpoint doesn't modify the statuses property.
     *
     * @covers  Lunr\Vortex\FCM\FCMResponse::add_batch_response
     */
    public function testAddBatchResponseWithNoEndpointDoesNotModifyStatuses(): void
    {
        $statuses = [
            'endpoint1' => PushNotificationStatus::ERROR,
            'endpoint2' => PushNotificationStatus::SUCCESS,
        ];

        $this->set_reflection_property_value('statuses', $statuses);

        $this->class->add_batch_response($this->batch_response, []);

        $this->assertPropertySame('statuses', $statuses);
    }

    /**
     * Test that add_batch_response() with endpoints add status for each of them.
     *
     * @covers  Lunr\Vortex\FCM\FCMResponse::add_batch_response
     */
    public function testAddBatchResponseWithEndpointsAddStatus(): void
    {
        $statuses = [
            'endpoint1' => PushNotificationStatus::ERROR,
            'endpoint2' => PushNotificationStatus::SUCCESS,
        ];

        $endpoints = [ 'endpoint2', 'endpoint3', 'endpoint4' ];

        $expected_statuses = [
            'endpoint1' => PushNotificationStatus::ERROR,
            'endpoint2' => PushNotificationStatus::INVALID_ENDPOINT,
            'endpoint3' => PushNotificationStatus::UNKNOWN,
            'endpoint4' => PushNotificationStatus::SUCCESS,
        ];

        $this->set_reflection_property_value('statuses', $statuses);

        $pos = 0;

        $this->batch_response->expects($this->exactly(3))
                             ->method('get_status')
                             ->willReturnMap(
                                 [
                                     [ 'endpoint2', PushNotificationStatus::INVALID_ENDPOINT ],
                                     [ 'endpoint3', PushNotificationStatus::UNKNOWN ],
                                     [ 'endpoint4', PushNotificationStatus::SUCCESS ],
                                 ]
                             );

        $this->class->add_batch_response($this->batch_response, $endpoints);

        $this->assertPropertySame('statuses', $expected_statuses);
    }

}

?>
