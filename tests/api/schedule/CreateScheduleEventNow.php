<?php

require_once __DIR__ . '/../../lib/KalturaClient.php';
require_once __DIR__ . '/../../lib/KalturaPlugins/KalturaScheduleClientPlugin.php';

$config = new KalturaConfiguration();
$config->curlTimeout = 1000;
$config->serviceUrl = 'http://dev-backend21.dev.kaltura.com/';

$partnerId = 101;
$adminSecret = 'a3f89e2b8516c3036e0b2a4966d62a51';
$userId = 'tan-tan';
$type = KalturaSessionType::ADMIN;
$expiry = 86400;
$privileges = '';

$client = new KalturaClient($config);
$client->setPartnerId($partnerId);
$client->setKs($client->generateSessionV2($adminSecret, $userId, $type, $partnerId, $expiry, $privileges));

$plugin = KalturaScheduleClientPlugin::get($client);

$filter = new KalturaCameraScheduleResourceFilter();
$filter->tagsLike = 'tantan1';
$scheduleResourceList = $plugin->scheduleResource->listAction($filter);
if($scheduleResourceList->totalCount)
{
	$camera1 = reset($scheduleResourceList->objects);
}
else
{
	$camera1 = new KalturaCameraScheduleResource();
	$camera1->name = 'tantan1';
	$camera1->tags = 'tantan1';
	$camera1->streamUrl = 'rtmp://localhost:1936/kLive/tantan1';
	$camera1 = $plugin->scheduleResource->add($camera1);
}

$filter = new KalturaCameraScheduleResourceFilter();
$filter->tagsLike = 'tantan2';
$scheduleResourceList = $plugin->scheduleResource->listAction($filter);
if($scheduleResourceList->totalCount)
{
	$camera2 = reset($scheduleResourceList->objects);
}
else
{
	$camera2 = new KalturaCameraScheduleResource();
	$camera2->name = 'tantan2';
	$camera2->tags = 'tantan2';
	$camera2->streamUrl = 'rtmp://localhost:1936/kLive/tantan2';
	$camera2 = $plugin->scheduleResource->add($camera2);
}

$event = new KalturaLiveStreamScheduleEvent();
$event->recuranceType = KalturaScheduleEventRecuranceType::NONE;
$event->summary = 'Test now - ' . date('H:m:s');
$event->startDate = strtotime('+30 second');
$event->endDate = $event->startDate + 1200;
$event = $plugin->scheduleEvent->add($event);

$eventResource = new KalturaScheduleEventResource();
$eventResource->resourceId = $camera1->id;
$eventResource->eventId = $event->id;
$eventResource = $plugin->scheduleEventResource->add($eventResource);

$eventResource = new KalturaScheduleEventResource();
$eventResource->resourceId = $camera2->id;
$eventResource->eventId = $event->id;
$eventResource = $plugin->scheduleEventResource->add($eventResource);

var_dump($event);