<?php

?>

<div style="font-family:verdana; font-size: 11px">
Options for kshow (id=<?php echo $kshow_id ?>)<br> 
add '&debug=true' to the url to see the result in a textarea<br> 
<a href="./keditorservices/getAllEntries?kshow_id=<?php echo $kshow_id ?>&debug=<?php echo $debug ?>">getAllEntries</a><br>
<a href="./keditorservices/getKshowInfo?kshow_id=<?php echo $kshow_id ?>&debug=<?php echo $debug ?>">getKshowInfo</a>
<br>
<a href="./keditorservices/getMetadata?kshow_id=<?php echo $kshow_id ?>&debug=<?php echo $debug ?>">getMetadata</a>
<br>
<a href="./keditorservices/setMetadata?kshow_id=<?php echo $kshow_id ?>&debug=<?php echo $debug ?>">setMetadata</a> 
<br>
<a href="./keditorservices/getGlobalAssets?kshow_id=<?php echo $kshow_id ?>&debug=<?php echo $debug ?>">getGlobalAssets (we don't yet have global assets in the DB)</a>
<br>
<a href="./keditorservices/getAllKshows?debug=<?php echo $debug ?>">getAllKshows</a>
</div>
