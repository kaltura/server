<?php
/**
 *
 * @service collaajini
 * @package plugins.collaajini
 * @subpackage api.services
 */
class CollaajUpdateService extends KalturaBaseService
{
    /**
     *  Return if there is a newer update according to the givwn OS and version
     *
     * @action clientUpdates
     * @param string $os
     * @param string $version
     * @return KalturaCaptureSpaceUpdateResponse
     * @throws exception
     */
    function clientUpdatesAction ($os, $version)
    {
        $collaajini = new collaajini();
        $returned_object = new KalturaCaptureSpaceUpdateResponse();
        $result = $collaajini->returnUpdateFileUrl($os, $version);

        if ($result) {
            $res = new KalturaCaptureSpaceUpdateResponse();
            $info = new KalturaCaptureSpaceUpdateResponseInfo();
            $info->url = $result['url'];
            $info->hash = new KalturaCaptureSpaceUpdateResponseInfoHash();
            $info->hash->algorithm = "md5";
            $info->hash->value = $result['md5'];
            $res->info = $info;

            //$returned_object->fromObject($collaajini);
            //return $returned_object;
            return $res;
        } else throw new KalturaAPIException ("No update is available for version " . $version . " for " . $os);
    }

    /**
     * Collaaj install
     *
     * @action serveInstall
     * @param string $os
     * @param string $version
     * @return KalturaCaptureSpaceUpdateResponse
     * @throws exception
     */
    public function serveInstallAction($os, $version)
    {
        /* @var $fileSync FileSync */
        $collaajini = new collaajini();
        $returned_object = new KalturaCaptureSpaceUpdateResponse();
        $filePath = $collaajini->returnLatestVersionUrl($os, $version, "serveinstall");
        $t = $collaajini->getVersion();
        if ($t) {
            $fileName = array_pop(explode('/', $filePath));    // Extracting only the file name
            $actualFilePath = "../../../../web/content/third_party/capturespace/".$fileName; // TODO: replace the path to be taken from a conf file
            if (is_readable($actualFilePath)) {
                $fileName = array_pop(explode('/', $filePath));    // Extracting only the file name
                $mimeType = kFile::mimeType($actualFilePath);
                header("Content-Disposition: attachment; filename=\"$fileName\"");
                return $this->dumpFile($actualFilePath, $mimeType);
            } else throw new KalturaAPIException ("There was a problem reading ($filePath) $fileName\n");
        } else throw new KalturaAPIException ("There seem to be no available versions for ".$t);
    }


    /**
     * Collaaj check for update
     *
     * @action serveUpdate
     * @param string $os
     * @param string $version
     * @return KalturaCaptureSpaceUpdateResponse
     * @throws exception
     */
    public function serveUpdateAction($os, $version)
    {

        $collaajini = new collaajini();
        $filtered_results = $collaajini->returnUpdateFileUrl($os, $version, "serveupdate");
        if ($filtered_results) {
            $filePath = $filtered_results["download_url"];  // url contains the full file name
            $fileName = array_pop(explode('/', $filePath));    // Extracting only the file name
            $actualFilePath = "../../../../web/content/third_party/capturespace/".$fileName; // TODO: replace the path to be taken from a conf file
            if (is_readable($actualFilePath)) {
                $mimeType = kFile::mimeType($actualFilePath);
                header("Content-Disposition: attachment; filename=\"$fileName\"");
                return $this->dumpFile($actualFilePath, $mimeType);
            } else throw new KalturaAPIException ("There was a problem reading $filePath\n");
        } else throw new KalturaAPIException ("There seem to be no available versions for ".$os);
    }
}


