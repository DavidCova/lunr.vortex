<?php

/**
 * This file contains the FCMResponseGetStatusTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2013 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Vortex\FCM\Tests;

use Lunr\Vortex\PushNotificationStatus;

/**
 * This class contains tests for the get_status function of the FCMResponse class.
 *
 * @covers Lunr\Vortex\FCM\FCMResponse
 */
class FCMResponseGetStatusTest extends FCMResponseTest
{

    /**
     * Unit test data provider.
     *
     * @return array $data array of endpoints statuses / status result
     */
    public function endpointDataProvider(): array
    {
        $data = [];

        // return unknown status if no status set
        $data[] = [ [], PushNotificationStatus::UNKNOWN ];

        // return unknown status if endpoint absent
        $data[] = [
            [
                'endpoint1' => PushNotificationStatus::INVALID_ENDPOINT,
            ],
            PushNotificationStatus::UNKNOWN,
        ];
        $data[] = [
            [
                'endpoint1' => PushNotificationStatus::ERROR,
                'endpoint2' => PushNotificationStatus::INVALID_ENDPOINT,
                'endpoint3' => PushNotificationStatus::SUCCESS,
            ],
            PushNotificationStatus::UNKNOWN,
        ];

        // return endpoint own status if present
        $data[] = [
            [
                'endpoint_param' => PushNotificationStatus::INVALID_ENDPOINT,
            ],
            PushNotificationStatus::INVALID_ENDPOINT,
        ];
        $data[] = [
            [
                'endpoint1'      => PushNotificationStatus::ERROR,
                'endpoint_param' => PushNotificationStatus::SUCCESS,
                'endpoint2'      => PushNotificationStatus::TEMPORARY_ERROR,
            ],
            PushNotificationStatus::SUCCESS,
        ];

        return $data;
    }

    /**
     * Test the get_status() behavior.
     *
     * @param array $statuses Endpoints statuses
     * @param int   $status   Expected function result
     *
     * @dataProvider endpointDataProvider
     * @covers       Lunr\Vortex\FCM\FCMResponse::get_status
     */
    public function testGetStatus($statuses, $status): void
    {
        $this->set_reflection_property_value('statuses', $statuses);

        $result = $this->class->get_status('endpoint_param');

        $this->assertEquals($status, $result);
    }

}

?>
