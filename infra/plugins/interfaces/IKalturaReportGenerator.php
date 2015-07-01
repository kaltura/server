<?php
/**
 * Enable the plugin to load and search extended objects and types
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaReportGenerator {

    /**
     * Receives the data needed in order to generate the total report of said plugin
     *
     * @param string $partner_id
     * @param KalturaReportType $report_type
     * @param string $object_ids
     */
    public function getTotal($partner_id, $report_type, $object_ids);

}