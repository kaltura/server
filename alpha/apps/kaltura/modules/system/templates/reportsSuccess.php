<HTML>
<BODY bgcolor="#FFFFFF" style="font-family:arial">

Last 24 hours (per hour)<br>
<?php

//include charts.php to access the InsertChart function
//include "charts.class.php";

// the swf and charts_library are placed under web/charts
echo charts::InsertChart ( "/charts/charts.swf", "/charts/charts_library", url_for ( "system") . "/reports?chart=hour", 1200, 450 );

?>
<br><br>
Last 31 days (per day)<br>
<?php

//include charts.php to access the InsertChart function
//include "charts.class.php";

// the swf and charts_library are placed under web/charts
echo charts::InsertChart ( "/charts/charts.swf", "/charts/charts_library", url_for ( "system") . "/reports?chart=day", 1200, 450 );

?>

</BODY>
</HTML>


