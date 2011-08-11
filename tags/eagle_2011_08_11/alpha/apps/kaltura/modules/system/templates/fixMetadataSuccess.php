<div style="font-family: arial; font-size:12px; ">
<script type="text/javascript">
//jQuery.noConflict();


/***************************************/
// jQuery Tabber
// By Jordan Boesch
// www.boedesign.com
// Dec 25, 2007 (Merry Christmas!)
/***************************************/

(function($){

		$.jtabber = function(params){
				
				// parameters
				var navDiv = params.mainLinkTag;
				var selectedClass = params.activeLinkClass;
				var hiddenContentDiv = params.hiddenContentClass;
				var showDefaultTab = params.showDefaultTab;
				var showErrors = params.showErrors;
				var effect = params.effect;
				var effectSpeed = params.effectSpeed;
				
				// If error checking is enabled
				if(showErrors){
					if(!$(navDiv).attr('title')){
						alert("ERROR: The elements in your mainLinkTag paramater need a 'title' attribute.\n ("+navDiv+")");	
						return false;
					}
					else if(!$("."+hiddenContentDiv).attr('id')){
						alert("ERROR: The elements in your hiddenContentClass paramater need to have an id.\n (."+hiddenContentDiv+")");	
						return false;
					}
				}
				
				// If we want to show the first block of content when the page loads
				if(!isNaN(showDefaultTab)){
					showDefaultTab--;
					$("."+hiddenContentDiv+":eq("+showDefaultTab+")").css('display','block');
					$(navDiv+":eq("+showDefaultTab+")").addClass(selectedClass);	
				}
				
				// each anchor
				$(navDiv).each(function(){
										
					$(this).click(function(){
						// once clicked, remove all classes
						$(navDiv).each(function(){
							$(this).removeClass();
						})
						// hide all content
						$("."+hiddenContentDiv).css('display','none');
						
						// now lets show the desired information
						$(this).addClass(selectedClass);
						var contentDivId = $(this).attr('title');
						
						if(effect != null){
							
							switch(effect){
								
								case 'slide':
								$("#"+contentDivId).slideDown(effectSpeed);
								break;
								case 'fade':
								$("#"+contentDivId).fadeIn(effectSpeed);
								break;
								
							}
								
						}
						else {
							$("#"+contentDivId).css('display','block');
						}
						return false;
					})
				})
			
			}
	
})(jQuery);	


        $(document).ready(function(){
            
        $.jtabber({
            mainLinkTag: "#nav a", // much like a css selector, you must have a 'title' attribute that links to the div id name
            activeLinkClass: "selected", // class that is applied to the tab once it's clicked
            hiddenContentClass: "hiddencontent", // the class of the content you are hiding until the tab is clicked
            showDefaultTab: 1, // 1 will open the first tab, 2 will open the second etc.  null will open nothing by default
            showErrors: true, // true/false - if you want errors to be alerted to you
            effect: 'slide', // null, 'slide' or 'fade' - do you want your content to fade in or slide in?
            effectSpeed: 'fast' // 'slow', 'medium' or 'fast' - the speed of the effect
        })
            
        })


function show ( elem )
{
//	alert ( "1" );
	e = jQuery ( elem );
	t = e.find( "textarea" ).text() ;
	_the_text = jQuery ( "#the_text" );
	text_area  = _the_text.find ( "textarea" );
	text_area.text ( t );
	_the_text.css ( "display" , "block" );
}

function closeText()
{
	_the_text = jQuery ( "#the_text" );
	_the_text.css ( "display" , "none" );
}

</script>

<span style="display:none;position:absolute;top:100px;left:100px; width:400; height:600;" id="the_text">
<textarea style="border:3px solid gold" cols=80 rows=30></textarea>
<button onclick="closeText()">X</button>
</span>

<a href="/index.php/system/login?exit=true">logout</a> Time on Machine: <?php echo date ( "Y-m-d H:i:s." , time() ) ?>
<br>
<form>
	Entry Ids: <input type="text" name="entry_ids" value="<?php echo $entry_ids ?>">

	Kshow Ids: <input type="text" name="kshow_ids" value="<?php echo $kshow_ids ?>">

<input type="submit" id="Go" name="Go" value="Go"/>
</form>

<?php if ( $result ) { ?>
<div><?php echo $result ?></div>
<?php } else { ?>
<div>

<div id='tabs'>

<?php $i=1; 
foreach ( $fixed_data_list as $fixed_data ) { ?>
<li><a class='selected' title="tab<?php echo  $i ?>"><?php echo  $fixed_data->show_entry->getId() ?></a></li>
<?php ++$i; 
} 
$i=1; 
?>
</ul>
<?php
foreach ( $fixed_data_list as $fixed_data ) { ?>
<div id='tab<?php echo  $i ?>' class='hiddencontent' style="DISPLAY: <?php echo  $i ==1 ? 'block' : 'none' ?>" oldblock="block">
	Before change: duration [<?php echo  $fixed_data->old_duration ?>]<br>
	<textarea cols="120" rows="20" readonly='readonly'>	<?php echo  $fixed_data->old_content ?>	</textarea >
	<br>
	After change: duration [<?php echo  $fixed_data->fixed_duration ?>]<br>
	<textarea cols="120" rows="20" readonly='readonly'>	<?php echo  $fixed_data->fixed_content ?>	</textarea >
</div>
<?php 
++$i; } ?>
</div>

<?php } ?>

