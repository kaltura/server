<?php

require_once(__DIR__ . '/../bootstrap.php');

$thumbParams = new thumbParams();
$thumbParams->setVersion(1);
$thumbParams->setPartnerId(0);
$thumbParams->setTags('');
$thumbParams->setIsDefault(false);
$thumbParams->setFormat(thumbParams::CONTAINER_FORMAT_JPG);
//$thumbParams->setWidth(800);
//$thumbParams->setHeight(600);

$thumbParams->setSourceParamsId(3);
$thumbParams->setCropType(1);
//$thumbParams->setQuality(100);
$thumbParams->setCropX(100);
$thumbParams->setCropY(100);
//$thumbParams->setCropWidth();
//$thumbParams->setCropHeight();
//$thumbParams->setCropProvider();
//$thumbParams->setCropProviderData();
$thumbParams->setVideoOffset(2);
//$thumbParams->setScaleWidth();
//$thumbParams->setScaleHeight();
//$thumbParams->setBackgroundColor();

$thumbParams->setName($thumbParams->getWidth() . ' x ' . $thumbParams->getHeight());
$thumbParams->setDescription($thumbParams->getName());

$thumbParams->save();

echo "Done\n";
