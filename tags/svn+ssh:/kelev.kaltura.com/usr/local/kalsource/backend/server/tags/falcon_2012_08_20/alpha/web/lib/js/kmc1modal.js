/*
This file is part of the Kaltura Collaborative Media Suite which allows users 
to do with audio, video, and animation what Wiki platfroms allow them to do with 
text.

Copyright (C) 2006-2008  Kaltura Inc.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

// this will be used to explicitly empty an object (mostly flash) that even when the modal dialog box closes - must be nulled
var object_id_to_remove;  
function setObjectToRemove ( obj_id )
{
	object_id_to_remove = obj_id;
}


var div_id_to_hide; 
function setDivToHide ( div_id )
{
	div_id_to_hide = div_id;
}


// initModalBox called from gotoCW - to open the contribution wizard as an iFrame in the 
// widget page
function kalturaInitModalBox ( url, options )
{
	kalturaCloseModalBox();
	
	var width = 680;
	var height = 360;
	
	if (typeof(options) != "undefined")
	{
		if (options.width)
			width = options.width;
		if (options.height)
			height = options.height;
	}
	
	if ( div_id_to_hide )
	{
		div_to_hide = document.getElementById ( div_id_to_hide ) ;
		div_to_hide.style.visibility="hidden";
	}
	
	var objBody = document.getElementsByTagName("body").item(0);

	// create overlay div and hardcode some functional styles (aesthetic styles are in CSS file)
	var objOverlay = document.createElement("div");
	objOverlay.setAttribute('id','overlay');
//	if (jQuery(window).width() > 900 ) // don't bother to show the Overlay if page size is small
//		objBody.appendChild(objOverlay, objBody.firstChild);
	
	// create modalbox div, same note about styles as above
	var objModalbox = document.createElement("div");
	objModalbox.setAttribute('id','modalbox');
	//objModalbox.setAttribute('style', 'margin-left:-' + width / 2 + 'px; width:' + width + 'px');
	if (jQuery(window).width() < 900 )
		objModalbox.className = "locked";
	else{
		objModalbox.style.width = width + 'px';
		objModalbox.style.marginLeft = -(width / 2) + 'px';
		objModalbox.style.height = height + 'px';
		//objModalbox.style.marginTop = -(height / 2) + 'px';
		//objModalbox.style.top = "50%";
	}
	
	// create content div inside objModalbox
	var objModalboxContent = document.createElement("div");
	objModalboxContent.setAttribute('id','mbContent');
	if ( url != null )
	{
		objModalboxContent.innerHTML = '<iframe scrolling="no" width="'+width+'" height="'+height+'" frameborder="0" src="' + url + '"/>';
	}
	objModalbox.appendChild(objModalboxContent, objModalbox.firstChild);
	
	objBody.appendChild(objModalbox, objOverlay.nextSibling);	
	
	return objModalboxContent;
}


function kalturaCloseModalBox ()
{
	if ( div_id_to_hide )
	{
		div_to_hide = document.getElementById ( div_id_to_hide ) ;
		div_to_hide.style.visibility ="visible";
	}

	if ( object_id_to_remove )
	{
//	alert ( object_id_to_remove );
		var elem_to_remove = document.getElementById(object_id_to_remove);	
		if ( elem_to_remove ) 
		{
//	alert ( object_id_to_remove + " " + elem_to_remove );

try
{
			if ( elem_to_remove.parentNode ) 			elem_to_remove.parentNode.removeChild( elem_to_remove );
} catch ( ex )
{
}

		}
	}
	
	// TODO - have some JS to close the modalBox without refreshing the page if there is no need
//	overlay_obj = document.getElementById("overlay");
	modalbox_obj = document.getElementById("modalbox");
//	overlay_obj.parentNode.removeChild( overlay_obj );
	if ( modalbox_obj )
		modalbox_obj.parentNode.removeChild( modalbox_obj );
	
	return false;
}

function $id(x){ return document.getElementById(x); }


function kalturaRefreshTop ()
{
	if ( this != window.top )
	{
		window.top.kalturaRefreshTop();
		return false;
	}	
	window.location = new String(window.location).replace("&__temp=1", "").replace("/__temp/1", "");
}
