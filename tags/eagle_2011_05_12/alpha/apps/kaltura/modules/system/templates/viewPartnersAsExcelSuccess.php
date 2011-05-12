<?php
?>
<div style='font-family: verdana; font-size: 12px;'>
This page will display all links for easy download by Excel.<br>
Each group will have it's own file with the name of the group and the relevant dates.
<br><br>
<form>
		FROM (YYYY-MM-DD): <input id="from_date" name="from_date" type="text" size=10 value="<?php echo $from_date ?>" >
		TO (YYYY-MM-DD): <input id="to_date" name="to_date" type="text" size=10 value="<?php echo $to_date ?>" >
		Days: <input id="days" name="days" type="text" size=3 value="<?php echo $days ?>" >

		<input type="submit" style='color:black' name="go" value="Go" />

</form>
<br>
<?php
$link_prefix = "./";
// http://www.kaltura.com/index.php/system/viewPartnersA?partner_filter=kmc_signup&filter_type=kmc_signup&type=2&from_date=2009-08-21&to_date=2009-08-27&days=7&new_first=false&partners_between=false&page=1

foreach ( $partner_group_list as $group )
{
	  echo "<a href='$link_prefix/viewPartnersA?partner_filter={$group->name}&filter_type={$group->name}&type=2&from_date={$from_date}&to_date={$to_date}&days={$days}&new_first=false&partners_between=false&page=1'>{$group->name}</a><br>";
}
?>
</div>