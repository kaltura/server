<?php

class iniReader
{
    const INI_STRUCTURE = "download_url,md5";

    private $file_name = "";
    private $ini_content = array();


    public function __construct($filename_input)
    {
        if (is_readable($filename_input)) {
            $this->file_name = $filename_input;
            $temp_read_content = $this->readIniFile();
            $this->importIniData($temp_read_content);
        } else throw new Exception ("Could not read " . $this->$filename_input);
    }

    private function readIniFile() {
        if ($this->file_name != '') {
            return parse_ini_file($this->file_name, TRUE);
        } else throw new Exception ("Could not parse " . $this->file_name);
    }

    private function importIniData($content) {
        $split_keys = explode(',', self::INI_STRUCTURE);
        foreach ($content as $key => $value) {
            $this->ini_content[$key] = array();
            foreach ($value as $ext => $data) {
                $temp_split = explode(',', $data);
                $temp_array['os'] = $ext;
                $temp_array['version'] = $key;
                for ($index = 0; $index < count($split_keys); $index++) {
                    $temp_array[$split_keys[$index]] = $temp_split[$index];
                }
                $this->ini_content[$key][$ext] = $temp_array;
            }
        }
    }


    public function getFfile_name () {
        return $this->file_name;
    }

    public function getIni_content() {
        return $this->ini_content;
    }
}
