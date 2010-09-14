<?php
?>
<script>
function createDynamicScript( script_src ) 
{	
	script = document.createElement('script');	
	script.src = script_src;	
	script.type = 'text/javascript';	
	script.defer = false;	
	jQuery ( script ).appendTo ( 'head' );
}


$$ = function(x) { return document.getElementById(x); }

jQuery.noConflict();
jQuery(document).ready(function(){
	jQuery("optgroup").attr( "style" , "font-style:normal; font-weight:normal; padding: 3px 1px 0px 3px;" );

	var div_elem_id= "#hidden_divs";
//	alert ( div_elem_id );
	div_elem = jQuery ( div_elem_id );
	hide ( div_elem );
	
	updateTitle();
})

function copyToClipboard(inElement){
	var inElement = $$(inElement);
	inElement.select();
	if (inElement.createTextRange) {
		var range = inElement.createTextRange();
		if (range)
			range.execCommand('Copy');
	} else {
		var flashcopier = 'flashcopier';
		if(!$$(flashcopier))
			jQuery("body").log("body").append("<div id='flashcopier'></div>");
		$$(flashcopier).innerHTML = '';
		var divinfo = '<embed src="/images/flash/_clipboard.swf" FlashVars="clipboard='+encodeURIComponent(inElement.value)+'" width="0" height="0" type="application/x-shockwave-flash"></embed>';
		$$(flashcopier).innerHTML = divinfo;
  }
}

function updateTitle ( )
{
	var service_url_elem = jQuery ( "select[@name=service_url]" )[0].value;
	var service_elem = jQuery ( "select[@name=service]" )[0].value;
	
	window.document.title = "TestMe " + service_url_elem  + ":" +  service_elem;
}

function updateSelect ( elem )
{
//	var inElement = $$(elem);	
	var current_value_elem = $$(elem.id + "_current_value" );
	if ( current_value_elem != null )	current_value_elem.innerHTML = elem.value;
	
	updateTitle();
}

var current_service = null;

function selectService( select_elem , update_history )
{
	var div_elem_id= "#div_" + select_elem.value;
	
	jQuery("#service_name").text("(" + select_elem.value + ")");
//	alert ( div_elem_id );
	div_elem = jQuery ( div_elem_id );
	hide ( current_service );
	extra_fields_elem = jQuery ( "#extra_fields" );
	extra_fields_elem.append ( div_elem );

	show ( div_elem );
	current_service = div_elem;
	
	service_elem = $$("service");
	if ( service_elem != select_elem )
	{
		// the history was used - update the value of the service selection
		service_elem.value = select_elem.value;
	}
	if( update_history  ) updateHistory ( select_elem );
	
	updateTitle();
}

function updateHistory ( select_elem )
{
	new_value = select_elem.value;
	if ( new_value == "" ) return;
	history_elem = $$("history");
	if ( history_elem != null )
	{
		html = history_elem.innerHTML;
		option = "<option value=\"" + new_value + "\">" + new_value + "</option>";
		html = html.replace ( option , "!" , "gim" );
		html = option + html; // put at head of text
		history_elem.innerHTML = html;
	}
}
 
function hide ( service_elem )
{
	if ( service_elem != null )
	{
		service_elem.find("input, select, textarea").attr ( "disabled" , "disabled" );
		service_elem.hide();
	}
}


function show ( service_elem )
{
	service_elem.find("input, select, textarea").removeAttr ( "disabled" );
	// update all the 
	service_elem.find("input[@class=shouldsend]").each(function(i) {  enableDisable ( this ) ;} );

	service_elem.show();
}

function submitForm ()
{
try
{
	service_elem = jQuery ( "#service" )[0].value;
	if ( service_elem == "" )
	{
		alert ("Please select a service" );
		return false;
	}
	form = jQuery ( "#theform" );

	service_url_elem = jQuery ( "select[@name=service_url]" )[0].value;
	
//	jQuery ( "#remote_script" )[0].src = service_url_elem + "/testme.js" ;
	
	index_path_elem= jQuery ( "select[@name=index_path]" )[0].value;
	url = "http://" + service_url_elem + "/" + index_path_elem + "/partnerservices2/" + service_elem;
	updateUrl ( url );
	form.attr ("action" , url );

	if ( service_elem == "upload" || service_elem == "webcamdummy" || service_elem == "addbulkupload" )
	{
		form.attr ("enctype" , "multipart/form-data" );
	}
	else
	{
		// regular form submition
		form.removeAttr ("enctype" );
	}

	if ( service_elem == 'startsession' ) 
	{
		simulateStartSession ( url  );
	}
}
catch ( e ) 
{
//	alert ( e.line + "\n" + e.name + "\n" + e.message ); 
}		
	return true;
}

function updateUrl ( url )
{
	submiturl = jQuery ("#submit_url" );
	if ( submiturl ) [0].value = url ;
}

function simulateStartSession ( url )
{
	// json format	- 1
	admin = gnv ("admin" );
	fixed_url = url + "?format=2" + gnv("partner_id") + gnv("subp_id" ) + gnv ("uid" ) + gnv ("secret" ) + gnv ( "privileges" ) + gnv ( "expiry" ) + admin;
	jQuery.ajax({
			url: fixed_url ,
			async: true , 
			complete: fillKs
		} );	
}

//get name-value 
function gnv ( elem_name )
{
	elem = jQuery ( "input[@name=" + elem_name + "]" )[0];
	return "&" + elem.name + "=" + elem.value;
}

// get value
function gv ( elem_name )
{
	elem = jQuery ( "input[@name=" + elem_name + "]" )[0];
	return elem.value;
}

function fillKs ( res  )
{
	m = res.responseText.match ( "<ks>(.*)</ks>");
	if ( m != null  ) 
	{
		ks = m[1];
		el = jQuery ( "input[@name=ks]" );
		el[0].value = ks ;
		admin = gv ("admin" );
		if ( admin == "1" )
			el[0].style.color = "red";
		else
			el[0].style.color = "blue";
	}
	//obj = eval ( res.responseText );
	
}

function switchKs ()
{
	ks1_elem = jQuery ( "input[@name=ks]" )[0];
	ks2_elem = jQuery ( "input[@name=ks2]" )[0];
	ks1 = ks1_elem.value;
	ks2 = ks2_elem.value;
	ks1_elem.value = ks2;
	ks2_elem.value = ks1;

	color = ks1_elem.style.color;
	ks1_elem.style.color = ks2_elem.style.color;
	ks2_elem.style.color = color;
	return false;
}

function enableDisable ( caller_elem , original_elem_id )
{
	// the sibling_id holds the id of the element to enable/disable
	elem_id = jQuery(caller_elem).attr ( "sibling_id" );
	elem = jQuery ( "#" + elem_id );
//	disable = elem.attr ( "disabled" ) ==  "disabled";
	disable = jQuery ( caller_elem ).attr ( "checked" );
	if ( disable )
	{
		elem.removeAttr ( "disabled" );
	}
	else
	{
		elem.attr ( "disabled" , "disabled" );
	}
}

function enableDisableChildren ( caller_elem , div_elem_id )
{
	div_elem = jQuery ( "#" + div_elem_id );
	var checked = jQuery ( caller_elem ).attr ( "checked" );

	children = div_elem.find ( "input:checkbox" );
	children.each(
		function(i) 
		{ 
			e = jQuery(this); 
			if ( checked ) 
			{
				e.attr ( "checked" , checked );
			}
			else
			{
				e.removeAttr ( "checked" );
			}
//			s += "[" + i + "] [" + this + "]" ; 
			enableDisable ( this , div_elem_id ); 
		});
}

function showHideGroup ( caller_elem , div_elem_id )
{
	div_elem = jQuery ( "#" + div_elem_id );
	div_elem.toggle( 40 );
	show ( div_elem );
	return false;
}

function save()
{
	elems = jQuery ( "[@pid]" );
	var cook = "";
	var count =0;
	for ( i=0 ; i< elems.length ; ++i )
	{
		elem = elems[i];
		//cook += elem.pid + "=" elem.value + "&";
		if ( elem.value != "" )
		{
			cook += elem.getAttribute("pid") + "=" + elem.value + "&";
			count++;
		}
	}
	
	createCookie( "testme" , cook , 365 );
	return false;
}

// if empty_elements is true - elements that had no value will be emptied.
// if empty_elements is fasle - elements that had no value will be left untouchd.
function restore( empty_elements )
{
	cook = readCookie ( "testme" );
	var name_value_pairs = cook.split( "&" );
	count = 0;
	var name_value_map = new Array();
	// create a map with all the name-values
	for ( i=0 ; i< name_value_pairs.length ; ++i )
	{
		name_value = name_value_pairs[i];
	
		name_value_arr = name_value.split ( "=" );
		name=name_value_arr[0];
		value=name_value_arr[1];
		name_value_map[name]=value;
	}
	
	elems = jQuery ( "[@pid]" );
	var cook = "";
	var count =0;
	for ( i=0 ; i< elems.length ; ++i )
	{
		elem = elems[i];
		name = elem.getAttribute("pid");
		value = name_value_map[name];
		if ( value )		elem.value = value;
		else 
		{
			// only in this case empty the value
			if ( empty_elements ) elem.value = "";
		}
	}
	
	// once restored - update the selection of the "service" element
	var service_elem = $$("service");
	if ( service_elem.value != "" ) service_elem.onchange(); 
	return false;
}

// >> -------------------- cookies -----------------
function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}
// << -------------------- cookies -----------------


function over ( btn_elem )
{
	btn_elem.style.backgroundColor = "#ddeedd";
}
function out ( btn_elem )
{
	btn_elem.style.backgroundColor = "#dddddd";
}
</script>