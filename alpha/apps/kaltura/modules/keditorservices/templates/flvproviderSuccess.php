<?php

if ($flv_streamer->pendingEntriesCount())
	requestUtils::sendCdnHeaders("flv", $total_length, 0);
else
	requestUtils::sendCdnHeaders("flv", $total_length);

echo myFlvHandler::createFlvHeader();

$flv_streamer->printMetadata();
$flv_streamer->streamFlv();

die; // prevent symfony from sending its headers and filling our logs with warnings
?>