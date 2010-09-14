<?php
function run_command($cmd, $outputs = 1)
{
        $descriptorspec = array(
//         0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
//         1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
           2 => array("pipe", "w") // stderr is a file to write to
        );
        if (!$outputs)
        {
                $descriptorspec = array();
        }
        $handle = proc_open($cmd,$descriptorspec,$pipes);
        do
        {
                $status = proc_get_status($handle);
        } while($status['running']);

        $err = '';
        if($outputs)
        {
                $err = stream_get_contents($pipes[2]);
                //$stdout = stream_get_contents($pipes[1]);
                //$sdtin = stream_get_contents($pipes[0]);
        }

        $out = array($err,  'return' => $status['exitcode']);
        return $out;
}
