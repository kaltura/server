#!/usr/bin/php -q
<?php
require_once('convert_xfiles.php');
require_once('run_command.php');
/*
        Convert power point files to flash files
        In:
                1st parameter - input path
                2nd parameter - output path
        Out:
                Results of convertions
*/
error_reporting(E_ERROR);
/* Configurations */
$config["memory_limit"] = "256M";
$config["path_unoconv"] = '/opt/openoffice.org3/program/python /opt/kaltura/ppt2swf/unoconv-0.3/'; /* Path of unoconv */
$config["path_logs"] = '/opt/kaltura/ppt2swf/logs/'; /* Path for logs */
$config["output_file"] = 'pdf'; /* Type of output file */
$config["timeout"] = '120'; /* Set the timeout limit in seconds */
$config["static_ppt"] = dirname(__FILE__).'/static/static.ppt'; /* Set location of static ppt that we know will convert correctly to PDF */
$config["static_pdf_output"] = dirname(__FILE__).'/static/static.pdf'; /* Set location of static pdf output */
/*              */

$tmp_filename = time();

//Set timeout limit
set_time_limit($config["timeout"]);
$file_input = $argv[1];
//$file_output = $_GET["out"];
$file_output = $argv[2];

if(!$file_input || !$file_output){
        die('must provide input file and output file');
}

ini_set('memory_limit', $config["memory_limit"]);

// get input file into process folder
$file_ext = substr($file_input, strrpos($file_input,'.'));
$real_file_ext_new = strtolower(pathinfo($file_input, PATHINFO_EXTENSION));
//if($real_file_ext_new == "docx" || $real_file_ext_new == "pptx") // add more later
// Gonen [30-09-09] - since we have problems with OO conversion, we decided to pass all requests to MS-Office
// Gonen [07-10-09] - odp, odt, ods - don't use MSOfice
if($real_file_ext_new != "odt" && $real_file_ext_new != "ods" && $real_file_ext_new != "odp" && $real_file_ext_new != "pdf" && $real_file_ext_new != "xls" && $real_file_ext_new != "xlsx")
{
        set_time_limit(0);
        ini_set('default_socket_timeout',400 );
        $web_file_input = str_replace('/web/', '',$file_input);
        $output_swf = convert_xfiles($web_file_input);
        if($output_swf === false)
        {
                echo 'ConvertFailed';
        }
        else
        {
                mkdir(pathinfo($file_output, PATHINFO_DIRNAME), 777, true);
                $result = file_put_contents($file_output, $output_swf);
                if(!$result)
                {
                        echo 'ConvertFailed - could not copy to target '.$file_output;
                }
                else
                {
                        echo 'Successfully converted using XP-conversion';
                }
        }
        exit(0);
}

$real_file_input = dirname(__FILE__).'/process/'.$tmp_filename.$file_ext;
copy($file_input, $real_file_input);
$swf_output_file = dirname(__FILE__).'/process/'.$tmp_filename.'.swf';
$pdf_output_file = dirname(__FILE__).'/process/'.$tmp_filename.'.pdf';


$real_file_ext = pathinfo($real_file_input, PATHINFO_EXTENSION);
if (strtolower($real_file_ext) == "pdf")
{
        copy($real_file_input, $pdf_output_file);
        $conv2pdf_result = null;
}
else
{
        //Build the command
        $command = $config["path_unoconv"]. 'unoconv.py -f ' . $config["output_file"] . ' ' . $real_file_input . ' 2>&1';

        $result_text = 'Executing: '.$command . "\n";
        // try unoconv from ppt to pdf using the soffice
        //$result = exec($command);
        $result = run_command($command);
var_dump($result);
        $conv2pdf_result = $result['return'];
}
//Generate the log file
$handle = fopen($config["path_logs"]. 'ppt2swf.log', "a");

if ($conv2pdf_result) {
        // unoconv failed to convert ppt to pdf
        $error =  time().": Failed executing: " . $command. "\n";
        $error .= "     Reason: ".print_r($result,true)."\n";
        fwrite($handle, $error);
        // try static ppt to PDF
        $static_command = $config["path_unoconv"]. 'unoconv.py -f '.$config["output_file"] . ' ' . $config["static_ppt"] . ' 2>&1';
        $result_static = run_command($static_command);
        // if static PPT converted, output error for batch
        if (!$result_static['return'])
        {
                echo 'ConvertFailed -unoconv-ppt2pdf('. $real_file_input .')'.PHP_EOL;
                exit(0);
        }
        // if static PPT failed - restart soffice, output for batch
        else
        {
                echo 'RetryConversion ('. $real_file_input .')'.PHP_EOL;
                $oolog = fopen($config["path_logs"] .'oostatus.log', "a");
                fwrite($oolog, '['.date('Y-m-d H:i:s').'] OpenOffice Error'."\n");
                fclose($oolog);
                exit(0);
        }
        //echo $error;
} else {
        // unoconv PPT->PDF worked, going to convert to SWF
        echo time() . ": Successfully executed " . $command . "\n";
        if (!file_exists($pdf_output_file))
        {
                 fwrite($handle, 'pdf output file not found '.$pdf_output_file.' . output file is: '.$file_output);
                echo 'real output file not found '.$pdf_output_file.' . output file is: '.$file_output.PHP_EOL;
                echo 'ConvertFailed ('. $pdf_output_file  .')'.PHP_EOL;
                exit(0);
        }
        else
        {
                $command = "/usr/local/bin/pdf2swf -t -G -s flashversion=9 -s zoom=100 $pdf_output_file -o $swf_output_file 2>&1";
                $command = "/usr/local/bin/pdf2swf -t -G -s flashversion=9 -s zoom=100 $pdf_output_file -o $swf_output_file >> /opt/kaltura/ppt2swf/logs/pdf2swf.log";
                $result_text = 'Executing: '.$command . "\n";
                $result = exec($command);

                if ($result && 0){
                        $error =  time().": Failed executing: " . $command. "\n";
                        $error .= "     Reason: $result\n";
                        fwrite($handle, $error);
                        echo 'ConvertFailed ('. $command  .')'.PHP_EOL;
                        exit(0);
                        //echo $error;
                } else {
                        echo time() . ": Successfully executed " . $command . "\n";
                
                        if (file_exists($swf_output_file))
                        {
								kLog::log(__METHOD__." - $swf_output_file file exists");	
                                mkdir(pathinfo($file_output, PATHINFO_DIRNAME), 777, true);
                                $moving = rename($swf_output_file, $file_output);
                                if (!$moving)
                                {
                                        fwrite($handle, 'could not move file '.$swf_output_file.' to '.$file_output);
                                        echo 'could not move file '.$swf_output_file.' to '.$file_output.PHP_EOL;
                                        echo 'ConvertFailed ('. $swf_output_file .' '. $file_output .')'.PHP_EOL;
                                        exit(0);
                                }
                        }
                        else
                        {
								kLog::log(__METHOD__." - $swf_output_file file doesnt exist");	
                                fwrite($handle, 'real output file not found '.$swf_output_file.' . output file is: '.$file_output);
                                echo 'real output file not found '.$swf_output_file.' . output file is: '.$file_output.PHP_EOL;
                                echo 'ConvertFailed ('. $swf_output_file  .')'.PHP_EOL;
                                exit(0);
                        }
                }
        }
}

unlink($pdf_file_input);
unlink($swf_file_input);
fclose($handle);
echo "exited at end, conversion successful";
exit(0);

function restart_soffice()
{
        // kill running processes
        $res = run_command('killall -9 /opt/openoffice.org3/program/soffice.bin', 0);

        // start soffice
        $res =run_command('/opt/kaltura/ppt2swf/start_openoffice_daemon.sh &', 0);
}
?>
