<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */
interface ESearchCaptionFieldName extends BaseEnum
{
    const CONTENT = 'caption_assets.content';
    const START_TIME = 'caption_assets.start_time';
    const END_TIME = 'caption_assets.end_time';
    const LANGUAGE = 'caption_assets.language';
    const LABEL = 'caption_assets.label';
    const CAPTION_ASSET_ID = 'caption_assets.caption_asset_id';
}
