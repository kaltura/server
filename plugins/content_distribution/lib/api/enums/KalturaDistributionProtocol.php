<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.enum
 */
class KalturaDistributionProtocol extends KalturaEnum
{
	const FTP = 1;
	const SCP = 2;
	const SFTP = 3;
	const HTTP = 4;
	const HTTPS = 5;
	const SFTP_CMD = 8; // SFTP Protocol
	const SFTP_SEC_LIB = 9; // SFTP Protocol
}
