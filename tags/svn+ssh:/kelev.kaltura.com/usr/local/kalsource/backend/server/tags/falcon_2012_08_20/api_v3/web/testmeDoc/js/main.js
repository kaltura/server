jQuery(function() {
	$('div#services ul.actions').hide();
	/*$('div#services ul li a').each(function(){
		$(this).click(function(){
			
			// general visibility plays - hiding lists, showing others etc..
			parent_li = $(this).parent('li.service');
			if (parent_li.html())
			{
				actions = parent_li.find('ul.actions');
				// close all open services and remove their "expended" class
				$('div#services ul.services li.service').removeClass('expended');				
				$('div#services ul.actions').hide();
				// show actions for current service and add "expended" class to the service LI
				parent_li.addClass('expended');
				actions.toggle();
			}

			
			// if service clicked - get service description (+ list of actions ???)
			if (parent_li.html()) // assume service
			{
				$('#doc').load('ajax-get-service-info.php?service='+$(this).attr('href'));
			}
			else // assume action
			{
				service_a = $(this).parent('li.action').parent('ul.actions').parent('li.service').find('a');
				service = service_a.attr('href');
				$('#doc').load('ajax-get-action-info.php', { service: service , action: $(this).attr('href')} );
				
			}
			return false;
		});
	});*/
	
	/*$('div#general ul li a').click(function(){
		$('#doc').load('ajax-get-general.php', {page: $(this).attr('href')} );
		return false;
	});*/
	$('div#general ul li a[href=overview]').trigger('click');
	
});

function kalturaInitModalBox ( url, options )
{
	if($.browser.msie)
	{
		if($.browser.version == 6)
			return true;
	}
	
	if (document.getElementById("overlay"))
	{
		overlay_obj = document.getElementById("overlay");
		modalbox_obj = document.getElementById("modalbox");
		overlay_obj.parentNode.removeChild( overlay_obj );
		modalbox_obj.parentNode.removeChild( modalbox_obj );		
	}
	var objBody = document.getElementsByTagName("body").item(0);

	// create overlay div and hardcode some functional styles (aesthetic styles are in CSS file)
	var objOverlay = document.createElement("div");
	objOverlay.setAttribute('id','overlay');
	objBody.appendChild(objOverlay, objBody.firstChild);
	$('div#overlay').click(function(){ kalturaCloseModalBox(); });
	
	
	var width = 800;
	var height = 600;
	if (options)
	{
		if (options.width)
			width = options.width;
		if (options.height)
			height = options.height;
	}

	// create modalbox div, same note about styles as above
	var objModalbox = document.createElement("div");
	objModalbox.setAttribute('id','modalbox');
	//objModalbox.setAttribute('style', 'width:'+width+'px;height:'+height+'px;margin-top:'+(0-height/2)+'px;margin-left:'+(0-width/2)+'px;');
	objModalbox.style.width = width+'px';
	objModalbox.style.height = height+'px';
	objModalbox.style.marginTop = (0-height/2)+'px';
	objModalbox.style.marginLeft = (0-width/2)+'px';
	
	// create content div inside objModalbox
	var objModalboxContent = document.createElement("div");
	objModalboxContent.setAttribute('id','mbContent');
	if ( url != null )
	{
		objModalboxContent.innerHTML = '<iframe id="kaltura_modal_iframe" scrolling="no" width="' + width + '" height="' + height + '" frameborder="0" src="' + url + '"/>';
	}
	objModalbox.appendChild(objModalboxContent, objModalbox.firstChild);
	
	objBody.appendChild(objModalbox, objOverlay.nextSibling);
	$(objBody).keypress(function(e){ if(e.keyCode == 27) kalturaCloseModalBox(); });
	return false;
	
	return objModalboxContent;
}

function kalturaCloseModalBox ()
{

	if ( this != window.top )
	{
		window.top.kalturaCloseModalBox();
		return false;
	}

	//alert ( "kalturaCloseModalBox" );
	// TODO - have some JS to close the modalBox without refreshing the page if there is no need
	overlay_obj = document.getElementById("overlay");
	modalbox_obj = document.getElementById("modalbox");
	overlay_obj.parentNode.removeChild( overlay_obj );
	modalbox_obj.parentNode.removeChild( modalbox_obj );
	var objBody = document.getElementsByTagName("body").item(0);
	$(objBody).unbind('keypress');
	return false;
}