<?php

/**
 * This file contains functionality to generate JPush Notification_3rd payloads.
 *
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Vortex\JPush;

use ReflectionClass;

/**
 * JPush Notification_3rd Payload Generator.
 */
class JPushNotification3rdPayload extends JPushPayload
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Construct the payload for the push notification.
     *
     * @return array JPushPayload
     */
    public function get_payload(): array
    {
        $elements = $this->elements;

        unset($elements['notification']);

        return $elements;
    }

    /**
     * Sets the payload sound data.
     *
     * @param string $sound The notification sound
     *
     * @return JPushNotification3rdPayload Self Reference
     */
    public function set_sound(string $sound): self
    {
        return $this->set_notification_3rd_data('sound', $sound);
    }

}

?>
