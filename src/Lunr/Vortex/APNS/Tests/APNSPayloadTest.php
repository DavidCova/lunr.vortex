<?php

/**
 * This file contains the APNSPayloadTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2014 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Vortex\APNS\Tests;

use Lunr\Halo\LunrBaseTest;
use ReflectionClass;

/**
 * This class contains common setup routines, providers
 * and shared attributes for testing the APNSPayload class.
 *
 * @covers Lunr\Vortex\APNS\APNSPayload
 */
class APNSPayloadTest extends LunrBaseTest
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
            'alert'       => 'apnsmessage',
            'badge'       => 10,
            'sound'       => 'bingbong.wav',
            'custom_data' => [
                'key1' => 'value1',
                'key2' => 'value2',
            ],
        ];

        $this->payload = json_encode($elements_array);

        $this->class = $this->getMockBuilder('Lunr\Vortex\APNS\APNSPayload')
                            ->getMockForAbstractClass();

        $this->reflection = new ReflectionClass('Lunr\Vortex\APNS\APNSPayload');
    }

    /**
     * Unit test data provider for payload files.
     *
     * @return array $values Array of non-object values
     */
    public function payloadProvider(): array
    {
        $values   = [];
        $values[] = [ '/Vortex/apns/alert.json', [ 'alert' => 'apnsmessage' ] ];
        $values[] = [ '/Vortex/apns/custom_data.json', [ 'custom_data' => [ 'key1' => 'value1', 'key2' => 'value2' ] ] ];
        $values[] = [ '/Vortex/apns/badge.json', [ 'badge' => 10 ] ];
        $values[] = [
            '/Vortex/apns/apns.json',
            [
                'alert'       => 'apnsmessage',
                'badge'       => 10,
                'sound'       => 'bingbong.wav',
                'custom_data' => [ 'key1' => 'value1', 'key2' => 'value2' ],
            ],
        ];

        return $values;
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
