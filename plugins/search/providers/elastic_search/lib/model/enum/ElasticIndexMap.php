<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.enum
 */
interface ElasticIndexMap extends BaseEnum
{
    const ELASTIC_ENTRY_INDEX = 'kaltura_entry';
    const ELASTIC_ENTRY_TYPE = 'entry';
    const ELASTIC_CATEGORY_INDEX = 'kaltura_category';
    const ELASTIC_CATEGORY_TYPE = 'category';
    const ELASTIC_KUSER_INDEX = 'kaltura_kuser';
    const ELASTIC_KUSER_TYPE = 'kuser';
}