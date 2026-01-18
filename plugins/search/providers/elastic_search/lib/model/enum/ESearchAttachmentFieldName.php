<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */
interface ESearchAttachmentFieldName extends BaseEnum
{
	const CONTENT = 'attachment_assets.content';
	const FILE_NAME = 'attachment_assets.file_name';
	const PAGE_NUMBER = 'attachment_assets.page_number';
	const ASSET_ID = 'attachment_assets.asset_id';
}
