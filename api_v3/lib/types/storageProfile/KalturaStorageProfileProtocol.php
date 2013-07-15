<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaStorageProfileProtocol extends KalturaDynamicEnum implements StorageProfileProtocol
{
	const KALTURA_DC = 0;
	const FTP = 1;
	const SCP = 2;
	const SFTP = 3;
	const S3 = 6;
}
