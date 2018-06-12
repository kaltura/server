<?php
/**
 * @package deployment
 */
require_once (__DIR__ . '/../../bootstrap.php');

if ($argc < 2)
{
	print "Uage: php $argv[0] <partner_id> [max_live_stream_inputs=10] [max_live_stream_outputs=10]";
}

$maxLiveStreamInputs = 10;
if ($argc >= 3)
	$maxLiveStreamInputs = $argv[2];
$maxLiveStreamOutputs = 10;
if ($argc >= 4)
	$maxLiveStreamOutputs = $argv[3];


$partner = PartnerPeer::retrieveByPK($argv[1]);
$partner->setMaxLiveStreamInputs($maxLiveStreamInputs);
$partner->setMaxLiveStreamOutputs($maxLiveStreamOutputs);
$partner->save();

?>