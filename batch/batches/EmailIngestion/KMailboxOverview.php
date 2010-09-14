<?php

/*
Usage -  php <FILE_NAME> <host> <port> <user> <pass>

Output for each folder :
<user>@<host>: <folder>: <total messages>

If the INBOX folder contains READ messages, this line will also appear at the end :
<user>@<host>: READ messages in INBOX: <number of read messages in INBOX>
*/

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'KMailChecker.class.php');

$arguments = $_SERVER['argv'];

if (count($arguments) < 5) {
	die('Usages is \'php '.basename(__FILE__).' <host> <port> <user> <pass>\''.PHP_EOL);
}

$host = $arguments[1];
$port = $arguments[2];
$user = $arguments[3];
$pass = $arguments[4];


$mailChecker = new KMailChecker($host, $port, $user, $pass, '/novalidate-cert');

if (!@$mailChecker->connect()) {
	die("Can't connect to [$host:$port] as $user - ".imap_last_error().PHP_EOL);
}

$folders = $mailChecker->getFolders();

$seenInInbox = false;
foreach ($folders as $curFolder) {
	$overview = $mailChecker->getFolderOverview($curFolder);
	echo "$user@$host: $curFolder: $overview->messages".PHP_EOL; // total messages for each folder
	if ($curFolder == 'INBOX' && $overview->unseen < $overview->messages) {
		$seenInInbox = $overview->messages - $overview->unseen;	
	}
}
if ($seenInInbox) {
	echo "$user@$host: READ messages in INBOX: $seenInInbox";
}



