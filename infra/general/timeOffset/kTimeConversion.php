<?php
/**
 * @package infra
 * @subpackage general
 */

/**
 * Contains enumerable time values
 */
interface kTimeConversion extends BaseEnum
{
    const SECOND  = 1;
    const SECONDS = 1;

    const MINUTE  = 60;
    const MINUTES = 60;

    const HOUR    = 3600;
    const HOURS   = 3600;

    const DAY     = 86400;
    const DAYS    = 86400;

    const WEEK    = 604800;
    const WEEKS   = 604800;

    const MONTH   = 2592000;
    const MONTHS  = 2592000;

    const YEAR    = 31557600;
    const YEARS   = 31557600;
}
