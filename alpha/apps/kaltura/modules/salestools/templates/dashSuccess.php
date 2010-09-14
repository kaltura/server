<?php

?>
<html>
<head>
<script type="text/javascript">
var locations = new Array (
	"batchwatch" ,
//	"showErrors",
	"showtime",
	"status"
);

var locations_index = 0;

function changeLocation()
{
	if ( locations_index >= locations.length ) locations_index = 0;

	target = locations[locations_index];
	e = document.getElementById( "theiframe" );
//	alert  ( e );
	e.src = target;

	_title = document.getElementById( "title" );
	_title.innerHTML = target;
	locations_index++;
	t = setTimeout ( "changeLocation()" , 30000 );
}


</script>
</head>
<body border=0 padding=0 pacing=0 style="width:100%">
<div id="title" style='font-family:calibri; font-size: 28px'>Title</div>
<div  style="width:100%">
<iframe id="theiframe" name="theiframe" src="" style="width: 98%;height: 700px; border: 0"></iframe>
</div>
</body>
<script>
changeLocation();
</script>
</html>