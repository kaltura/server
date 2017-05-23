<?php
/**
 * @package deployment
 *
 * Deploy webcast defualt profiles & temlates
 *
 * No need to re-run after server code deploy
 */
require_once (__DIR__ . '/../../bootstrap.php');

$script = realpath(dirname(__FILE__) . '/../../../') . '/tests/standAloneClient/exec.php';

$config = realpath(dirname(__FILE__)) . '/xml/notifications/public_qna_notification.xml';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/xml/notifications/user_qna_notification.xml';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/xml/notifications/code_qna_notification.xml';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/xml/responseProfiles/polls_response_profile.xml';
passthru("php $script $config");

$config = realpath(dirname(__FILE__)) . '/xml/responseProfiles/qna_response_profiles.xml';
passthru("php $script $config");