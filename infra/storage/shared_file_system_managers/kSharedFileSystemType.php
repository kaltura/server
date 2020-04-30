<?php
/**
 * Created by IntelliJ IDEA.
 * User: yossi.papiashvili
 * Date: 5/26/19
 * Time: 4:19 PM
 */

/**
 * @package infra
 * @subpackage Storage
 */

interface kSharedFileSystemMgrType extends BaseEnum
{
	const NFS = "NFS";
	const S3 = "S3";
}