#!/usr/bin/php
<?php
/*
 * Created on Apr 18, 2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once(realpath(dirname(__FILE__)).'/../config/sfrootdir.php');
define('SF_APP',         'kaltura');
define('SF_ENVIRONMENT', 'batch');
define('SF_DEBUG',       false);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');
require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'lib/batch/myBatchPartnerUsage.class.php');
ini_set('memory_limit', '512M');
/**
 * input vars:
 *      script_name (daily_storage / monthly_agg)
 *      date        ( optional, null = today / 'yyyy-mm-dd' )
 */
$date = '';
$script_name = @$argv[1];
if (!isset($argv[2]))
{
    $date = date('Y-m-d');
}
else
{
    $date = $argv[2];
    // verify date format
}
switch ($script_name)
{
    case 'daily_storage':
        $batchClient = new myNewBatchPartnerUsage();
        $batchClient->doDailyStorageAggregation( $date );
        break;
    case 'monthly_agg':
        ini_set('memory_limit',(1024*1000*1000));
        $batchClient = new myNewBatchPartnerUsage();
        $batchClient->doMonthlyAggregation( $date );
        break;
    default:
        echo 'Usage: '.__FILE__.' <script_name> [date]'.PHP_EOL.'Example: '.__FILE__.' daily_storage 2009-05-04'.PHP_EOL;
        echo PHP_EOL.'  script_name - daily_storage / monthly_agg'.PHP_EOL;
        echo '  date - optional, format: yyyy-mm-dd. If not specified, today is used.'.PHP_EOL;
}

?>
