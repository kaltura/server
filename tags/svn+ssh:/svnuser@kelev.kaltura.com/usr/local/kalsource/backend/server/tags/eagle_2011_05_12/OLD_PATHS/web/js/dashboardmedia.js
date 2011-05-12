jQuery.noConflict();
jQuery(document).ready(function(){	


	getPartnerId = function ()
	{
		p_id_elem = jQuery ( "#partner_id" );
		if ( p_id_elem !== null )
		{
			p_id = p_id_elem.val();
		}
		else
		{
			p_id = -1;
		}
		return p_id;
	}
	
	formatPartnerId = function()
	{
		return "partner_id=" + getPartnerId();
	}

	partnerSelect = function ( elem )
	{
		partner_id= jQuery ( elem ).val();
		document.location = "?partner_id=" + partner_id;
	}	
	
	getReferer = function ()
	{
		return jQuery ( "#referer" ).val();
	}
	
	formatReferer = function()
	{
		return "referer=" + getReferer();
	}

	gotoSelectedMedia = function( kshowId, entryId)
	{
		document.location  = '/index.php/browse?kshow_id=' + kshowId + '&entry_id=' + entryId;
	}
	
	requestMediaPage = function( elem, page )
	{
		if ( page <=0  ) return;
		requestMedia( mediaPager, page);
	}
	
	
	requestMediaPagePeople = function( elem, page )
	{
		
		if ( page <=0  ) return;
		requestMediaPeople( mediaPager, page);
	}
	
	
	requestMedia = function(pager, page )
	{
		  jQuery.getJSON( '/index.php/system/viewShows?' + formatPartnerId() ,
			{sort: mediaSortOrder, page: page, page_size: pager.pageSize, first: "0", partof: kaltura_part_of_flag ,producer_id: producer_id},
			function(json) { pager.updateJSON(null, json, updateJSONMedia) } );
	}
	
	
	requestMediaPeople = function(pager, page )
	{
		jQuery.getJSON( '/index.php/system/viewUsers?' + formatPartnerId() ,
			{sort: mediaSortOrder, page: page, page_size: pager.pageSize, first: "0" },
			function(json) { pager.updateJSON(null, json, updateJSONMediaPeople) } );
	}
	
	
	updateJSONMedia = function(request, json, pager)
	{
		updateJSON(request, json, pager);
		updatePagerAndRebind ( "media_pager" , null , requestMediaPage );
		updatePagerAndRebind ( "media_pagerB" , null , requestMediaPage );
	}
	
	
	updateJSONMediaPeople = function(request, json, pager)
	{
		updateJSON(request, json, pager);
		updatePagerAndRebind ( "media_pager" , null , requestMediaPagePeople );
		updatePagerAndRebind ( "media_pagerB" , null , requestMediaPagePeople );
	}
	
	onClickDelete = function(kshow_id)
	{
		var confirmation = confirm( "Are you sure you want to delete this Kaltura?\r\nOnce you delete it, it will be gone for good...\r\n\r\nPress [OK] to delete or [Cancel] to keep it.\r\n" );
		if( confirmation ) window.location = "/index.php/system/deleteKshow?kshow_id=" + kshow_id;
	}
	
	onClickDeleteUser = function( kuser_id)
	{
		var confirmation = confirm( "Are you sure you want to delete this user?\r\nOnce you delete it, it will be gone for good...\r\n\r\nPress [OK] to delete or [Cancel] to keep it.\r\n" );
		if( confirmation ) alert( 'coming soon...' + kuser_id );
	}
	
		
	onClickCustomize = function(id)
	{
			
		alert( "coming soon...");
		//document.location.href = MODULE_BROWSE + '?kshow_id=' + id +'#customize';
	}
	
	changeMediaSortOrder = function(e, order)
	{
		jQuery(e).parent().children().removeClass("color2");
		jQuery(e).addClass("color2");
		mediaSortOrder = order;
		mediaPager.reset();
		mediaPager.requestObjects(1);
	}


	requestMediaWidget = function(pager, page )
	{
		jQuery.getJSON( '/index.php/system/viewWidgets?' + formatPartnerId() + '&' + formatReferer(),
			{sort: mediaSortOrder, page: page, page_size: pager.pageSize, first: "0" },
			function(json) { pager.updateJSON(null, json, updateJSONMediaWidget) } );
	}

	requestMediaPageWidget = function( elem, page )
	{
		
		if ( page <=0  ) return;
		requestMediaWidget( mediaPager, page);
	}
	
	updateJSONMediaWidget = function(request, json, pager)
	{
		updateJSON(request, json, pager);
		updatePagerAndRebind ( "media_pager" , null , requestMediaPageWidget );
		updatePagerAndRebind ( "media_pagerB" , null , requestMediaPageWidget );

	}
			
	jQuery("button").Tooltip({ track: true, delay: 200 });
});
