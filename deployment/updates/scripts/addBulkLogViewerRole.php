<?php
set_time_limit(0);
ini_set("memory_limit","124M");
error_reporting(E_ALL);

require_once (dirname ( __FILE__ ) . '/../../bootstrap.php');

$bulkLogViewerLog = new UserRole();
$bulkLogViewerLog->setStrId('BULK_LOG_VIEWER_ROLE');
$bulkLogViewerLog->setName('Bulk Log Viewer Role');
$bulkLogViewerLog->setSystemName('Bulk Log Viewer Role');
$bulkLogViewerLog->setDescription('Bulk Log Viewer Role');
$bulkLogViewerLog->setPartnerId(0);
$bulkLogViewerLog->setStatus(UserRoleStatus::ACTIVE);
$bulkLogViewerLog->setPermissionNames('BULK_LOG_DOWNLOAD');
$bulkLogViewerLog->save();
	