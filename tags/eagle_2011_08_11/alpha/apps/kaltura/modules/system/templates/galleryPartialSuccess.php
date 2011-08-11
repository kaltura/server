<?php
function createPageFor ( $p , $current_page , $page_size , $count )
{
	$p = (int)$p;
	$a = $p * $page_size + 1;
	$b = (($p+1) * $page_size) ;
	$b = min ( $b , $count ) ;// don't lett the page-end be bigger than the total count
	if ( $p == $current_page ) 
	{
		$str = "[<a title='{$p}' href='javascript:gotoPage ($p)'>{$a}-{$b}</a>] ";
//		$str =  "[{$a}-{$b}] ";
	}
	else
		$str =  "<a title='{$p}' href='javascript:gotoPage ($p)'>{$a}-{$b}</a> "; 
		
	return $str;
}

$media_type_style = array ( null => "white" , "1" => "lightblue" , "2" => "lightgreen" , "5" => "#FDD017" , "6" => "lightgray" );
$status_style = array ( -1 => "red" , -2 => "red" , "0" => "orange" , "1" => "#FDD017" , "2" => "green" , "3" => "gray" ,
	"6" => "Violet");
?>


<div id='pager' style='height:30px; margin-top: 10px; font-family:verdana;font-size:11px;' >Total results: <?= $count ?> 
<?

$str = "";
$start_page = max ( 0 , $page - 5 );
$very_last_page = $count / $page_size;
$end_page = min ( $very_last_page , $start_page + 10 );
//echo "[$page] [$start_page] [$end_page]";

for ( $p=$start_page ; $p < $end_page ; $p++)
{
	$str .= createPageFor ( $p , $page  , $page_size , $count);
}

if ( $start_page > 0 ) echo createPageFor ( 0, $page , $page_size , $count )  ; // add page 0 if not in list  
if ( $start_page > 1 ) echo "..."; // have some dots if there is a real gap between 0 and the rest
echo $str;
if ( $end_page < $very_last_page -1 ) echo "..."; 
if ( $end_page < $very_last_page ) echo createPageFor ( $very_last_page , $page  , $page_size, $count); //add last page if lot in list
?>
</div>

<input type="hidden" name="current_partner_id" id="current_partner_id" value="<?=$partner_id?>" >

<div id='gallery' style='font-family:verdana;font-size:11px;'>
<table style='border: solid 1px; font-family:verdana;font-size:11px;'><tr>
<?
$i=0;
foreach ( $entries as $entry ) {	 
?>
<td style="font-size: 11px; width: 150px ; background-color:<?= $media_type_style[$entry->getMediaType()] ?>">
<a href='javascript:investigate ( "<?= $entry->getId() ?>");'>
<?=  $entry->getId() . " [" . $entry->getPartnerId() . "]" .
"<br>" . $entry->getName()?></a>
<br>
<table border=0>
	<tr>
		<td rowspan=5 style='width:120px;height:90px'><img  onclick='playEntry("<?= $entry->getId() ?>");' width='120' height='90' src="<?= $entry->getThumbnailUrl() ?>" title="<?=  $entry->getId() . " " . $entry->getName()?>"></td>
		<td>st</td><td style='background-color:<?php echo @$status_style[$entry->getStatus()] ?>' ><?php echo $entry->getStatus() ?></td></tr> 
		<tr><td>du</td><td><?=( number_format ( $entry->getLengthInMsecs()/1000 , 2 ) )?>"</td></tr> 
		<tr><td>v</td><td><?= $entry->getViews() ?></td></tr>
		<tr><td>p</td><td><?= $entry->getPlays() ?></td></tr>
		<tr><td>c</td><td><?= $entry->getCreatedAt() ?></td></tr>
</table>
<? 
++$i; 
if ( $i %5 == 0 ) echo "</tr><tr>";
} ?>
</tr></table>
</div id='gallery'>
