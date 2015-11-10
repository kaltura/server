<?php

class collaajini
{
    const INI_FILE_NAME = "../../collaajClientUpdate.ini";
    const AVAILABLE_OS_TYPES = "Mac OS X,Windows";
    const UPDATE_KEY = "version";
    const DOWNLOAD_URL = "download_url";
    const MD5 = "md5";
    const DOWNLOAD_URL_TEMPLATE = "http://<HOSTNAME>/api_v3/index.php/service/collaajupdate_collaajupdate/action/<ACTION>/os/<OS>/version/<VERSION>/filename/";

    private $collaaj_data = "";

    private $returned_data = array(
        "os" => "",
        "version" => "",
        "download_url" => "",
        "md5" => "",
    );

    public function __construct() {
        $this->collaaj_data = new collaajInstallData(self::INI_FILE_NAME);
    }

    public function returnLatestVersionUrl($given_os, $current_version) {
        $actual_extension = $this->identifyNeededExtension($given_os, "install");
        $ini_returned_data = $this->collaaj_data->getLatestVersionData($actual_extension);
        if ($ini_returned_data) {
            foreach ($ini_returned_data as $key => $value) {
                $this->returned_data[$key] = $value;
            }
            // Appending the current file name to the to future url
            $this->returned_data["url"] = $this->prepareDownloadUrl($given_os,$current_version, "serveinstall",self::DOWNLOAD_URL_TEMPLATE.$this->returned_data[self::DOWNLOAD_URL]);
            return $this->returned_data[self::DOWNLOAD_URL];
        } else throw new KalturaAPIException ("No install version found for $given_os");
    }

    public function returnUpdateFileUrl($given_os, $current_version) {
        $actual_extension = $this->identifyNeededExtension($given_os, "update");
        $returned_data = $this->collaaj_data->returnVersionUpdateInfo($actual_extension, $current_version);
        if ($returned_data != 1) {
            foreach ($returned_data as $key => $value) {
                $this->returned_data[$key] = $value;
            }
            $this->returned_data["url"] = $this->prepareDownloadUrl($given_os,$current_version, "serveupdate",self::DOWNLOAD_URL_TEMPLATE.$this->returned_data[self::DOWNLOAD_URL]);
            return $this->returned_data;

        } else throw new KalturaAPIException ("There is no update for $current_version");
    }

    public function prepareDownloadUrl($needed_os, $current_version, $action, $original_url) {
        $returned_url = $original_url;
        $returned_url = str_replace ("<HOSTNAME>", gethostname(),$returned_url);
        $returned_url = str_replace ("<VERSION>", $current_version,$returned_url);
        $returned_url = str_replace ("<OS>", $needed_os,$returned_url);
        $returned_url = str_replace ("<ACTION>", $action,$returned_url);

        return $returned_url;
    }

    public function identifyOs($given_os) {
        if ($given_os) {
            $os_types = explode (',', self::AVAILABLE_OS_TYPES);
            foreach ($os_types as $os) {
                if (strstr($given_os,$os)) {
                    return $os;
                }
            }
            if (in_array($given_os, $os_types) ) {
                return $os_types[array_search($given_os, $os_types)];
            } else throw new KalturaAPIException ("OS not supported / wrong is provided: '". $given_os."");
        }
        else throw new KalturaAPIException ("No OS was provided");
    }

    //  Returns the needed extension in order to identify the file to be fetched. Note that it assumes that all checks regarding values were done before
    public function identifyNeededExtension ($given_os, $action) {;
        $extension_mapping = array (
            "update"    => array ("Windows" => "msi", "Mac OS X" => "zip"),
            "install"   => array ("Windows" => "exe", "Mac OS X" => "dmg"),
        );
        $actual_os = $this->identifyOs($given_os);
        return $extension_mapping[$action][$actual_os];
    }

    public function returnReturned_data () {
        return $this->returned_data;
    }

    public function getVersion() {
        return $this->returned_data["version"];
    }


}
