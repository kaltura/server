<?php

class myImapMessage
{

	function getMessage( $msg, $mid, $pid )
	{
	

	// Gather header information
	// Sets the seen flag if this is a subpart of a multipart message
	$msg->getHeaders($mid, $pid);

	// Use this to *not* set the seen flag
	// $msg->getHeaders($mid, $pid, 1024, 1024, NULL, FT_PEEK);
	//
	// Must also use this in the call to getBody below.

	// Gather inline/attachment parts specific to this part
	$msg->getParts($mid, $pid);

	// Are there inline or attachment parts?
	if (count($msg->inPid[$mid]) > 0 || count($msg->attachPid[$mid]) > 0)
	{
		echo "              <table style='width: 100%; border: 1px solid black; background: white;'>\n",
             "                  <tr>\n",
             "                      <td style='font-size: 10px; font-weight: bold;'>\n",
             "                          attachments\n",
             "                      </td>\n",
             "                  </tr>\n",
             "                      <td style='padding: 5px;'>\n";
	}

	// Are there inline parts?
	if (count($msg->inPid[$mid]) > 0)
	{
		foreach ($msg->inPid[$mid] as $i => $inid)
		{
			echo "                          Inline part:";
			 $this->getMessage($msg, $mid, $msg->inPid[$mid][$i]);
			 echo "{$msg->inFname[$mid][$i]} {$msg->inFtype[$mid][$i]} ";
			 echo $msg->convertBytes($msg->inFsize[$mid][$i])."<br />\n";
		}
	}

	// Are there attachments?
	if (count($msg->attachPid[$mid]) > 0)
	{
		foreach ($msg->attachPid[$mid] as $i => $aid)
		{
			echo "Attachment:";
			$this->getMessage($msg, $mid, $msg->attachPid[$mid][$i]);
			echo $msg->attachFname[$mid][$i].' '.$msg->attachFtype[$mid][$i]."<br/>\n";
			
			 
			$fp=fopen("$i_attach","w");
			$data=$msg->convertBytes($msg->attachFsize[$mid][$i]);
			fputs($fp,$data);
			fclose($fp);
					
		}
	}

	if (count($msg->inPid[$mid]) > 0 || count($msg->attachPid[$mid]) > 0)
	{
		echo "                      </td>\n",
             "                  </tr>\n",
             "              </table>\n";
	}

	echo "              <table style='width: 100%; border: 1px solid black; background: white; margin-top: 5px;'>\n",
             "                  <tr>\n",
             "                     <td>\n",
             "                      <pre>\n",

             // Print the Raw Headers
             htmlspecialchars($msg->getRawHeaders($mid)),

             "                      </pre>\n",
             "                    </td>\n",
             "                  </tr>\n",
             "              </table>\n";

             // Retrieve the message body (sets the seen flag)
             $body = $msg->getBody($mid, $pid);

             // Use this to *not* set the seen flag
             // $body = $msg->getBody($mid, $pid, 0, 'text/html', FT_PEEK);
             //
             // Must also use this in the call to getHeaders above.

             if ($body['ftype'] == 'text/plain')
             {
             	echo "              <table style='width: 100%; border: 1px solid black; background: white; margin-top: 5px;'>\n",
             "                  <tr>\n",
             "                      <td>\n",

             // If this is a plain/text part format it for display
             nl2br(htmlspecialchars($body['message'])),

             "                      </td>\n",
             "                  </tr>\n",
             "              </table>\n";
             }
             else
             {
             	echo $body['message'];
             }

             // Close the stream
             $msg->close();
}
}
?>