<?php

/**
 * This file contains the JPushNotification3rdPayloadTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Vortex\JPush\Tests;

use Lunr\Halo\LunrBaseTest;
use Lunr\Vortex\JPush\JPushNotification3rdPayload;
use ReflectionClass;

/**
 * This class contains common setup routines, providers
 * and shared attributes for testing the JPushNotification3rdPayload class.
 *
 * @covers Lunr\Vortex\JPush\JPushNotification3rdPayload
 */
class JPushNotification3rdPayloadTest extends LunrBaseTest
{

    /**
     * Sample payload json
     * @var string
     */
    protected $payload;

    /**
     * Testcase Constructor.
     */
    public function setUp(): void
    {
        $elements_array = [
            'registration_ids' => [ 'one', 'two', 'three' ],
            'collapse_key'     => 'test',
            'data'             => [
                'key1' => 'value1',
                'key2' => 'value2',
            ],
            'time_to_live'     => 10,
        ];

        $this->payload = json_encode($elements_array);

        $this->class = new JPushNotification3rdPayload();

        $this->reflection = new ReflectionClass('Lunr\Vortex\JPush\JPushNotification3rdPayload');
    }

    /**
     * Testcase Destructor.
     */
    public function tearDown(): void
    {
        unset($this->payload);
        unset($this->class);
        unset($this->reflection);
    }

}

?>
