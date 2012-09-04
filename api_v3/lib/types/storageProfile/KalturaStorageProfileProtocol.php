<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaStorageProfileProtocol extends KalturaEnum
{
	const KALTURA_DC = 0;
	const FTP = 1;
	const SCP = 2;
	const SFTP = 3;
	const S3 = 6;
	const SFTP_CMD = 8;
	const SFTP_SEC_LIB = 9;
}
