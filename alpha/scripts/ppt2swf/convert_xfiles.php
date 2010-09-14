<?php
function convert_xfiles($filename)
{
        $url = 'http://www.kaltura.com/'.$filename;
//echo $url.PHP_EOL;
        $request = file_get_contents('http://pa-xp/run-wdoc2swf.php?url='.$url);
echo $request;
        if($request == 'failed to get a file')
        {
                return false;
        }
        $parts = explode('|',$request);
        if($parts[0] == 'failed to convert' || $parts[0] != 'ready')
        {
                return false;
        }
        else
        {
                if(strpos($parts[1],"\n"))
                {
                        $url = 'http://pa-xp'.substr($parts[1],0,strpos($parts[1],"\n"));
//      exec('echo '.$url.' >> logs/ppt2swf.log');
                }
                else
                        $url = 'http://pa-xp'.$parts[1];
                $swf_file = file_get_contents($url);
                return $swf_file;
        }
}
