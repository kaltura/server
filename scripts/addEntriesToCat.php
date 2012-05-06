<?php
require_once ("C:\web\content\clientlibs/php5/KalturaClient.php");

$catId = $argv[1];

$ini = parse_ini_file("categoriesConfig.ini");

$config = new KalturaConfiguration($ini["partner_id"]);
$config->serviceUrl = $ini["service_url"];
$client = new KalturaClient($config);
$partnerId = $ini["partner_id"];
$ks = $client->session->start($ini["admin_secret"], "", KalturaSessionType::ADMIN, $partnerId);
$client->setKs($ks);

$pager = new KalturaFilterPager();
$pager->pageSize = 50;
$entries = $client->baseEntry->listAction(null,$pager);

foreach ($entries->objects as $entry)
{
    /* @var $entry KalturaBaseEntry */
    $categoryEntry = new KalturaCategoryEntry();
    $categoryEntry->categoryId = $catId;
    $categoryEntry->entryId = $entry->id;
    $client->categoryEntry->add ($categoryEntry);
}

echo 'done';

