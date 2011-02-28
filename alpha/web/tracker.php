<?php
header("Cache-Control:");
header("Expires: Sun, 19 Nov 2000 08:52:00 GMT");
header("Pragma:");

$referer = @$_SERVER['HTTP_REFERER'];
$parts = parse_url($referer);


$var_utmac='UA-7714780-3'; //enter the new urchin code
$var_utmhn='www.kaltura.com'; //enter your domain
$var_utmn=rand(1000000000,9999999999); //random request number
$var_cookie=rand(10000000,99999999); //random cookie number
$var_random=rand(1000000000,2147483647); //number under 2147483647
$var_today=time(); //today
$var_referer=@$_SERVER['HTTP_REFERER']; //referer url

$var_uservar='-'; //enter your own user defined variable
$var_utmp=@$_GET['referer'].'#kpartner_id='.@$_GET['kpartner_id'].'&action=view';

$urchinUrl='http://www.google-analytics.com/__utm.gif?utmwv=1&utmn='.$var_utmn.'&utmsr=-&utmsc=-&utmul=-&utmje=0&utmfl=-&utmdt=-&utmhn='.$var_utmhn.'&utmr='.$var_referer.'&utmp='.$var_utmp.'&utmac='.$var_utmac.'&utmcc=__utma%3D'.$var_cookie.'.'.$var_random.'.'.$var_today.'.'.$var_today.'.'.$var_today.'.2%3B%2B__utmb%3D'.$var_cookie.'%3B%2B__utmc%3D'.$var_cookie.'%3B%2B__utmz%3D'.$var_cookie.'.'.$var_today.'.2.2.utmccn%3D(direct)%7Cutmcsr%3D(direct)%7Cutmcmd%3D(none)%3B%2B__utmv%3D'.$var_cookie.'.'.$var_uservar.'%3B';
 
$handle = fopen ($urchinUrl, "r");
$test = fgets($handle);
fclose($handle);

