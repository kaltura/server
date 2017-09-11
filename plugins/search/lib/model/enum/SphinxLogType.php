<?php
/**
 * @package plugins.sphinxSearch
 * @subpackage model.enum
 */
interface SphinxLogType extends BaseEnum
{
    const SPHINX = 0;
    const ELASTIC = 1;
}