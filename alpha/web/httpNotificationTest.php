<?php

$fp = fopen('/tmp/httpNotificationTest.log', 'w');
fwrite($fp, 'HTTP NOTIFICATION TEST LOG');

$body = file_get_contents('php://input');
fwrite($fp, "\n\n\n****** Body ******\n\n\n");
fwrite($fp, $body);

fwrite($fp, "\n\n\n****** Signature ******\n\n\n");
$secret = 'cded6ce2359fb85ff11ac9fa0bede401';
$givenSignature = $_SERVER["HTTP_X_KALTURA_SIGNATURE"];
$sigCorrect = false;
$dataSig = sha1($secret . $body);
fwrite($fp, "Calculated Signature:\n$dataSig\n\n\n");
fwrite($fp, "Given Signature:\n$givenSignature\n\n");
if($givenSignature == $dataSig)
{
   $sigCorrect = true;
}
$convertedBool = $sigCorrect ? 'true' : 'false';
fwrite($fp, "SignatureCorrect is: $convertedBool\n\n\n");

fwrite($fp, "\n\n\n****** Done ******\n\n\n");
fclose($fp);
