<?php
/**
 * @package deployment
 *
 * Deploy polls push notification
 *
 * No need to re-run after server code deploy
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . '/../../../') . '/tests/standAloneClient/exec.php';

$config = realpath(dirname(__FILE__)) . '/xml/notifications/polls_qna_notification.xml';
passthru("php $script $config");