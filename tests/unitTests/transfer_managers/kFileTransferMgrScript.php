<?php
chdir('/opt/kaltura/app/scripts');

require_once 'bootstrap.php';



$server = 'ftp.kaltura.com';
$user = 'sdrpftp990222';
$pass = 'oodiot2G';
$pubKey = 'ssh-dss AAAAB3NzaC1kc3MAAACBAJvAdbn/SX9JE6dYHQ+axEzsazfsNBl9dHU/O5iVgOzhU75BP6aR5K7XxWEyieo0oO/TC1VLCUGXnsI7rrF7r8tsDziOz9U8Kz5eR1WJy7vlsEaQC7zRf+71B9bjoEgkQRAY6LoF+13JzmCLHIs9cUwqaOPKZyz5OKWzO8AXf+YzAAAAFQClCemdxYTAQG6IMrRyrfeJGYsqYQAAAIEAgd42kwErRRPShRHYKZcCJfDxiz2sUZTxJ0ystNmXXHEc1imk8ofVMli8CgZ5Xb+d8Goa/iNXjgLoiYrmT+YuoeykVV2K659Sq2heD/AHwr049lyoM08QmsXZYh2PJlrP8+uvGaQ6GdvqAX6wuJt+r/fS/7Hb4IbQ1VjVfKw+HXwAAACACMquqgkuOTC1u6i1Q4bIta/qBovfAZBNOL34O4Pr6sCkYxKsD4a9s02mTS7nD9uelMGZfv4rSbXVB2wOxrVeJNd2HYDdse7tFaPy8BBUwhn7avM0aI/tmfTlF4/TX0BfcHfSHjXs9G80vssfOZMUiuKY3V6AKYzgALU8AO8TgAk= root@pa-ftp';
//$pubKey = 'ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEA0a7DLipG/outKxHwirWeYc5idTRBUhhbWkX9D8xjJSEMy80RVPWqiq8p97Y3S5b6xkdffBv1FJZYEyUsDT6gI4uPv4zK5L93qZyeObRdLtwdx+wpNiXDIdhISKzsVbwnfvSdBrf3mDLA/gS3OluKopwk3PMeWgPHCCcbiBn/xNV111UZnIcz8ReZ52a24Bt2/7U442MH3GY99I9ke2uJNTfkG3nevKxzRie1Bs+UCzW9cznS554kbgrZryDXWrp+fgeC0/5KP3KA4YkNw3BIgH+pMp1Xq7vCzcn0w7cAcZ97YewNOInP/iR1RvAHRNi9b6C9B2Wanabrp1xA+a7JIQ== ovation_via_Kaltura';

$pubKeyPath = '/tmp/pubkey';
$pvtKey = '-----BEGIN DSA PRIVATE KEY-----
Proc-Type: 4,ENCRYPTED
DEK-Info: DES-EDE3-CBC,14C75FD31923B739

bIqJGUTodKWmmCrSJuju9BYwiD64RWAfeNyfM2vUXQyWIFpiEtNFickxfemx2LY9
693CJjJCiaGCvfXVfUYASUJUlzFXuaDfXYRwM2VabcIDtfBR/YueFi6/z6L6pP+f
0Wnrs1BbC6T1KjxAOQACt4MJtW+WDFJjCiqwe0rCio+LrTFjB9COAAJzhYnixrEC
z0NFs4kNG0ixO+h9YXKCcDR2zWLgKUZ/3/eTdiz2mwbqSjvJpyi/2xr0Qjf4sB0S
iA9r8VMgugM6Q6cZ01+UCoYnMH1SFexJtb1zOhNqn5bs8rogb0F1MGnOscKoNbgO
JCgaWouYE/r17K9osGK7nHTLy2wzNCvuv9WQjxCjmuQpjbl0MJgmTfkibqSyJqAK
Dm62ji2OBfgIs8HRdXWjs05p1Tmo+5ieFfeG440QTyBCUH6fuHaAUdasbbiPykmt
KYPYaTaH+fKbmuROZE0dmUYLMeeB4cCltuaTu5llIvEPT4+Yccp8iK1yfjhZW7Jz
jqd4EpVueNTCkL1LNFltporr22v6q/fRZw56JXMQ4zcrXIZct7Drm5bsKJprGAfU
NTTs+lEN0pQwr61MgZlE7KMuHUfbd8Mr
-----END DSA PRIVATE KEY-----
';
$pvtKeyPath = '/tmp/pvtkey';
$passPhrase = 'EaHah3vo ce0Sohth';
$port = 22;
$localFile = '/tmp/' . uniqid('test_');
$remoteFile = '/home/sdrpftp990222/' . uniqid('test_');

file_put_contents($localFile, uniqid('test_'));
file_put_contents($pubKeyPath, $pubKey);
file_put_contents($pvtKeyPath, $pubKey);

function testIt($type, $server, $user, $pass, $port, $pubKeyPath, $pvtKeyPath, $passPhrase, $localFile, $remoteFile)
{
	try
	{
		$kFileTransferMgr = kFileTransferMgr::getInstance($type);
		if($pass)
			$kFileTransferMgr->login($server, $user, $pass, $port);
		else
			$kFileTransferMgr->loginPubKey($server, $user, $pubKeyPath, $pvtKeyPath, $passPhrase, $port);
				
		$kFileTransferMgr->putFile($remoteFile, $localFile);

		$actualSize = $kFileTransferMgr->fileSize($remoteFile);
		echo "size: $actualSize\n";
	}
	catch(Exception $e)
	{
		echo "error: " . $e->getMessage() . "\n";
	}
}

echo "\n\nSFTP public key only:\n";
$remoteFile = '/home/sdrpftp990222/' . uniqid('test_');
testIt(kFileTransferMgrType::SFTP, $server, $user, null, $port, $pubKeyPath, null, $passPhrase, $localFile, $remoteFile);

echo "\n\nSFTP public key and private:\n";
$remoteFile = '/home/sdrpftp990222/' . uniqid('test_');
testIt(kFileTransferMgrType::SFTP, $server, $user, null, $port, $pubKeyPath, $pvtKeyPath, $passPhrase, $localFile, $remoteFile);

echo "\n\nSFTP password:\n";
$remoteFile = '/home/sdrpftp990222/' . uniqid('test_');
testIt(kFileTransferMgrType::SFTP, $server, $user, $pass, $port, null, null, $passPhrase, $localFile, $remoteFile);


echo "\n\nSFTP_CMD public key only:\n";
$remoteFile = '/home/sdrpftp990222/' . uniqid('test_');
testIt(kFileTransferMgrType::SFTP_CMD, $server, $user, null, $port, $pubKeyPath, null, $passPhrase, $localFile, $remoteFile);

echo "\n\nSFTP_CMD public key and private:\n";
$remoteFile = '/home/sdrpftp990222/' . uniqid('test_');
testIt(kFileTransferMgrType::SFTP_CMD, $server, $user, null, $port, $pubKeyPath, $pvtKeyPath, $passPhrase, $localFile, $remoteFile);

echo "\n\nSFTP_CMD password:\n";
$remoteFile = '/home/sdrpftp990222/' . uniqid('test_');
testIt(kFileTransferMgrType::SFTP_CMD, $server, $user, $pass, $port, null, null, $passPhrase, $localFile, $remoteFile);


echo "\n\nSFTP_SEC_LIB public key only:\n";
$remoteFile = '/home/sdrpftp990222/' . uniqid('test_');
testIt(kFileTransferMgrType::SFTP_SEC_LIB, $server, $user, null, $port, $pubKeyPath, null, $passPhrase, $localFile, $remoteFile);

echo "\n\nSFTP_SEC_LIB private key only:\n";
$remoteFile = '/home/sdrpftp990222/' . uniqid('test_');
testIt(kFileTransferMgrType::SFTP_SEC_LIB, $server, $user, null, $port, null, $pvtKeyPath, $passPhrase, $localFile, $remoteFile);

echo "\n\nSFTP_SEC_LIB public key and private:\n";
$remoteFile = '/home/sdrpftp990222/' . uniqid('test_');
testIt(kFileTransferMgrType::SFTP_SEC_LIB, $server, $user, null, $port, $pubKeyPath, $pvtKeyPath, $passPhrase, $localFile, $remoteFile);

echo "\n\nSFTP_SEC_LIB password:\n";
$remoteFile = '/home/sdrpftp990222/' . uniqid('test_');
testIt(kFileTransferMgrType::SFTP_SEC_LIB, $server, $user, $pass, $port, null, null, $passPhrase, $localFile, $remoteFile);
