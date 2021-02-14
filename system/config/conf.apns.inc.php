<?php

/**
 * This file contains Apple Push Notifcation Service configuration
 * values, like:
 * <ul>
 * <li></li>
 * </ul>
 *
 * PHP Version 5.3
 *
 * @category   Config
 * @package    Core
 * @subpackage Config
 * @author     M2Mobi <info@m2mobi.com>
 * @author     Julio Foulquié <julio@m2mobi.com>
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 */

/**
 * Apple Push Notification Service Settings
 * @global array $config['apns']
 */
$config['apns'] = array();

/**
 * PUSH-URL for APNS
 * @global String $config['apns']['push']
 */
$config['apns']['push'] = '';

/**
 * Feedback-URL for APNS
 * @global String $config['apns']['feedback']
 */
$config['apns']['feedback'] = '';

/**
 * Array containing Certificate Details
 * @global array $config['apns']['cert']
 */
$config['apns']['cert'] = array();

/**
 * Path to the APNS Certificate
 * @global String $config['apns']['cert']['path']
 */
$config['apns']['cert']['path'] = '';

/**
 * Passphrase for the APNS Certificate
 * @global String $config['apns']['cert']['pass']
 */
$config['apns']['cert']['pass'] = '';

/**
 * Path to log-file for logging PUSH errors
 * @global String $config['apns']['log']
 */
$config['apns']['log'] = '';

?>