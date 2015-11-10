<?php

// This class handles all the data queries relevant for colaaj and the ini file
class collaajInstallData
{
    private $ini_handle = "";  // handle to the ini object
    private $ini_content = "";
    private $ini_sorted_versions_array = array ();

    public function __construct($ini_file_name){
        if ($ini_file_name) {
            $this->ini_handle = new iniReader($ini_file_name);
            $this->ini_content = $this->ini_handle->getIni_content();
            $this->shift_array_lables_to_version($this->ini_handle->getIni_content());
        } else throw new Exception ("ini file name was not provided");
    }


    public function getLatestVersionData ($needed_extension) {
        $ini_filtered_by_extension = $this->returnFilteredByExtension($needed_extension);
        $temp_keys = array_keys($ini_filtered_by_extension);
        uasort($temp_keys, "version_compare");
        if (count ($ini_filtered_by_extension)) {
            return $ini_filtered_by_extension[array_pop($temp_keys)];
        } else return NULL;
    }


    public function returnVersionUpdateInfo ($needed_extension, $current_version) {
        //Check if there is such a version in the ini file
        if (in_array($current_version, $this->ini_sorted_versions_array)) {
            $this->ini_content = $this->ini_handle->getIni_content();
            if ($this->ini_content[$current_version]) {
                $version_content = $this->ini_content[$current_version];
                $version_content_keys = array_keys( $version_content );
                // Checking if the needed extension exists
                if ( in_array( $needed_extension,  $version_content_keys ))  {
                    return  ($version_content[$needed_extension]);
                } else {
                    KalturaLog::notice ("NOT FOUND $needed_extension\n");
                    return 1;
                }
            } else {
                KalturaLog::notice ("Version $current_version does not contain this version $current_version\n");
                return 1;
            }
        } else {
            KalturaLog::notice ("Key $current_version not found in ini file\n");
            return 1;
        }
    }

//    Returns an array containing only the requested extension, version is key
    public function returnFilteredByExtension($needed_ext) {
        $temp_array = array();
        foreach ($this->ini_content as $key => $value) {
            if (in_array($needed_ext, array_keys($this->ini_content[$key]))) {
                $temp_array[$key] = $this->ini_content[$key][$needed_ext];
            }
        }
        return $temp_array;
    }


    private function shift_array_lables_to_version ($ini_content) {
        $all_keys = array_keys($ini_content);
        foreach ($all_keys as $key) {
            $key = str_replace('_', '.', $key);
            array_push($this->ini_sorted_versions_array, $key);
        }
        uasort($this->ini_sorted_versions_array, "version_compare"); // Sorting the array for later use
        foreach ($this->ini_sorted_versions_array as $key => $value){
            $this->ini_sorted_versions_array[$key] = str_replace('.', '_', $value);
        }
    }

    public function getIni_sorted_versions_array(){
        return $this->ini_sorted_versions_array;
    }
}
