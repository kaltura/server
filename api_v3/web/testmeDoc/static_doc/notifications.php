<h2>Notifications</h2>

<p>
Kaltura implements a set of HTTP POST notifications that could be sent upon specific media events to a URL on partners` host servers.
</p>
<p>  
The following notifications modes are supported and could be configured by partners at their account settings page within <a href="../../index.php/kmc">Kaltura Management console</a>:
</p>
<ul>
	<li>
		<b>Server notifications</b><br/> 
		Notifications that are being sent, asynchronously, to a partner hosted URL, directly from Kaltura servers.
	</li>
	<li>
		<b>Client notifications</b><br/>
		Notifications that are being sent to a partner hosted URL from Kaltura client components (kaltura widget) and are synchronized with widgets internal steps control.
	</li>
</ul>
<p>
Kaltura notifications provide partners with the following functionalities:
</p>
<ul>
	<li>
		Ability to integrate with Kaltura technology in a way that is synchronized with media events occurring on Kaltura’s servers and/or with the internal steps control of Kaltura client components, when applicable.
	</li>
	<li>
		Ability to implement a synchronized local management instance of media related metadata and thumbnails for improving website performance. This may include local media searching and caching capabilities.
	</li>
</ul>

<p>
The following table summarizes the different notification types and the parameters that are being included within each notification. For more information on the possible types of notifications is please review the <a href="?object=KalturaNotificationType">KalturaNotificationType</a> documentation page.
</p>

  <table cellspacing="0" id="notifications_table">
		<tr>
			<th><b>Notification</b></th>
			<th>Entry Add</th>
			<th>Entry Update</th>
			<th>Entry Update Permissions</th>
			<th>Entry Delete</th>
			<th>Entry Block</th>
			<th>Entry Update Thumbnail</th>
			<th>User Banned</th>
		</tr>
		<tr>
			<td><b>Client Notification</b></td>
			<td class="center property_yes">&nbsp;</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td><b>Server Notification</b></td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
		</tr>
		<tr>
			<th colspan="8"><b>Fields:</b></th>
		</tr>
		<tr>
			<td>notification_type</td>
			<td>entry_add</td>
			<td>entry_update</td>
			<td>entry_update_permissions</td>
			<td>entry_delete</td>
			<td>entry_block</td>
			<td>entry_update_thumbnail</td>
			<td>user_banned</td>
		</tr>
		<tr>
			<td>notification_id</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
		</tr>
		<tr>
			<td>puser_id</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
		</tr>
		<tr>
			<td>partner_id</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
		</tr>
		<tr>
			<td>kshow_id (Obsolete)</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>entry_id</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>name</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>description</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>tags</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>search_text</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>media_type</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>length_in_msecs</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>permissions</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>thumbnail_url</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>group_id</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>partner_data</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>status</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>width</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>height</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>data_url</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>download_url</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>download_size</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>media_date</td>
			<td class="center property_yes">&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>screen_name</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
		</tr>
		<tr>
			<td>email</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td class="center property_yes">&nbsp;</td>
		</tr>
	</table>