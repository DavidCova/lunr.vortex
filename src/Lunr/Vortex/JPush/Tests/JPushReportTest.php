<?php

/**
 * This file contains the JPushReportTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2023 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Vortex\JPush\Tests;

use Lunr\Halo\LunrBaseTest;
use Lunr\Vortex\JPush\JPushReport;
use WpOrg\Requests\Response;
use WpOrg\Requests\Session;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;

/**
 * This class contains common setup routines, providers
 * and shared attributes for testing the JPushReport class.
 *
 * @covers \Lunr\Vortex\JPush\JPushReport
 */
abstract class JPushReportTest extends LunrBaseTest
{

    /**
     * Mock instance of the Requests\Session class.
     * @var Session&MockObject
     */
    protected $http;

    /**
     * Mock instance of the Logger class.
     * @var \Psr\Log\LoggerInterface|MockObject
     */
    protected $logger;

    /**
     * Mock instance of the Requests\Response class.
     * @var Response&MockObject
     */
    protected $response;

    /**
     * Testcase Constructor.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->http = $this->getMockBuilder('WpOrg\Requests\Session')->getMock();

        $this->logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();

        $this->response = $this->getMockBuilder('WpOrg\Requests\Response')->getMock();

        $this->class      = new JPushReport($this->http, $this->logger, 12, [ 'endpoint1' ]);
        $this->reflection = new ReflectionClass('Lunr\Vortex\JPush\JPushReport');
    }

    /**
     * Testcase Destructor.
     */
    public function tearDown(): void
    {
        unset($this->http);
        unset($this->logger);
        unset($this->response);
        unset($this->class);
        unset($this->reflection);
    }

}

?>
