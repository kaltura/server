<?php
/**
 * @package plugins.dropFolder
 * @subpackage model.enum
 */ 
interface DropFolderType extends BaseEnum
{
	const LOCAL = 1;
	const FTP = 2;
	const SCP = 3;
	const SFTP = 4;
	const S3 = 6;
	const SFTP_CMD = 8;
	const SFTP_SEC_LIB = 9;
}