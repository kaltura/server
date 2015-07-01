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
     * @param string $partner_id
     * @param KalturaReportType $report_type
     * @param string $objectIds
     * @return array(array(<header> => <value>))
     */
    public function getReportResult($partner_id, $report_type, $report_flavor, $objectIds);

}