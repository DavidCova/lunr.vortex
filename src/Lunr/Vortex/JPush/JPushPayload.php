<?php

/**
 * This file contains functionality to generate JPush payloads.
 *
 * SPDX-FileCopyrightText: Copyright 2020 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Vortex\JPush;

/**
 * JPush Payload Generator.
 */
abstract class JPushPayload
{

    /**
     * Array of Push Notification elements.
     * @var array
     */
    protected array $elements;

    /**
     * Supported push platforms
     * @var array
     */
    private const PLATFORMS = [ 'ios', 'android' ];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->elements = [];

        $this->elements['platform']         = self::PLATFORMS;
        $this->elements['audience']         = [];
        $this->elements['notification']     = [];
        $this->elements['notification_3rd'] = [];
        $this->elements['message']          = [];
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->elements);
    }

    /**
     * Construct the payload for the push notification.
     *
     * @return array JPushPayload
     */
    abstract public function get_payload(): array;

    /**
     * Sets the notification identifier.
     *
     * @param string $identifier The notification identifier
     *
     * @return JPushPayload Self Reference
     */
    public function set_notification_identifier(string $identifier): self
    {
        $this->elements['cid'] = $identifier;

        return $this;
    }

    /**
     * Sets the notification message payload.
     *
     * @param string $message The notification body
     *
     * @return JPushPayload Self Reference
     */
    public function set_body(string $message): self
    {
        return $this->set_message_data('msg_content', $message)
                    ->set_notification_3rd_data('content', $message)
                    ->set_notification_data('alert', $message);
    }

    /**
     * Sets the notification title payload.
     *
     * @param string $message The notification title
     *
     * @return JPushPayload Self Reference
     */
    public function set_title(string $message): self
    {
        return $this->set_message_data('title', $message)
                    ->set_notification_3rd_data('title', $message)
                    ->set_notification_data('title', $message, [ 'android' ]);
    }

    /**
     * Sets the payload key data.
     *
     * The fields of data represent the key-value pairs of the message's payload data.
     *
     * @param array $data The actual notification information
     *
     * @return JPushPayload Self Reference
     */
    public function set_data(array $data): self
    {
        return $this->set_message_data('extras', $data)
                    ->set_notification_3rd_data('extras', $data)
                    ->set_notification_data('extras', $data);
    }

    /**
     * Sets the payload category data.
     *
     * @param string $category The notification category
     *
     * @return JPushPayload Self Reference
     */
    public function set_category(string $category): self
    {
        return $this->set_message_data('content_type', $category)
                    ->set_notification_data('category', $category);
    }

    /**
     * Sets the payload key time_to_live.
     *
     * It defines how long (in seconds) the message should be kept on JPush storage,
     * if the device is offline.
     *
     * @param int $ttl The time in seconds for the notification to stay alive
     *
     * @return JPushPayload Self Reference
     */
    public function set_time_to_live(int $ttl): self
    {
        $this->set_options('time_to_live', $ttl);

        return $this;
    }

    /**
     * Sets the payload key collapse_key.
     *
     * An arbitrary string that is used to collapse a group of alike messages
     * when the device is offline, so that only the last message gets sent to the client.
     *
     * @param string $key The notification collapse key identifier
     *
     * @return JPushPayload Self Reference
     */
    public function set_collapse_key(string $key): self
    {
        $this->set_options('apns_collapse_id', $key);

        return $this;
    }

    /**
     * Set additional JPush values in the 'options' key.
     *
     * @param string                $key   Options key.
     * @param string|int|float|bool $value Options value.
     *
     * @return JPushPayload Self Reference
     */
    public function set_options(string $key, $value): self
    {
        if (!isset($this->elements['options']))
        {
            $this->elements['options'] = [];
        }

        $this->elements['options'][$key] = $value;

        return $this;
    }

    /**
     * Set notification value for one or more platforms.
     *
     * @param string   $key       The key in the notification->platform object.
     * @param mixed    $value     The value accompanying that key.
     * @param string[] $platforms The platforms to apply this to.
     *
     * @return JPushPayload Self Reference
     */
    protected function set_notification_data(string $key, $value, array $platforms = self::PLATFORMS): self
    {
        foreach ($platforms as $platform)
        {
            $this->elements['notification'][$platform][$key] = $value;
        }

        return $this;
    }

    /**
     * Set notification value for one or more platforms.
     *
     * @param string $key   The key in the notification->platform object.
     * @param mixed  $value The value accompanying that key.
     *
     * @return JPushPayload Self Reference
     */
    protected function set_message_data(string $key, $value): self
    {
        $this->elements['message'][$key] = $value;

        return $this;
    }

    /**
     * Set notification value for one or more platforms.
     *
     * @param string $key   The key in the notification->platform object.
     * @param mixed  $value The value accompanying that key.
     *
     * @return JPushPayload Self Reference
     */
    protected function set_notification_3rd_data(string $key, $value): self
    {
        $this->elements['notification_3rd'][$key] = $value;

        return $this;
    }

}

?>
