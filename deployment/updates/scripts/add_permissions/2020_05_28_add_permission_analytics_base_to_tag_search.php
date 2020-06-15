<?php
/**
 * @package deployment
 *
 * Adding permission ANALYTICS_BASE to search action in tagsearch_tag
 */

$script = realpath(dirname(__FILE__) . '/../../../../') . '/alpha/scripts/utils/permissions/addPermissionsAndItems.php';
$config = realpath(dirname(__FILE__)) . '/../../../permissions/service.tagsearch.tag.ini';
passthru("php $script $config");