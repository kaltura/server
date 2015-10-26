<?php
/**
 * Enable the plugin to load and search extended objects and types
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaReportProvider {

    /**
     * Receives the data needed in order to generate the total report of said plugin
     *
     * @param $partner_id
     * @param $report_type
     * @param $report_flavor
     * @param $objectIds
     * @param $inputFilter
     * @param null $orderBy
     * @return mixed array(array(<header> => <value>))
     */
    public function getReportResult($partner_id, $report_type, $report_flavor, $objectIds, $inputFilter,
                                    $page_size , $page_index, $orderBy = null);
}