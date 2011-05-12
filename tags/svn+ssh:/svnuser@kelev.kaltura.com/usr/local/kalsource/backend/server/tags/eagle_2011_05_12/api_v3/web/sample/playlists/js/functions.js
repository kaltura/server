jQuery.fn.clearInput = function(msg){
	this.focus(function(){
		if( this.value == this.defaultValue )
			this.value = "";
	}).blur(function(){
		if( this.value == "" )
			this.value = this.defaultValue;
	});
};


$(function(){                
    // initialize scrollable  
    $("div.scrollable").scrollable({ 
        size: 3, 
        items: 'ul',   
        hoverClass: 'hover' 
    });
	
	 $("#videoboxContent > div.mediaContent div.scrollable > ul li div").hover(
      function(){
        ie = $.browser.msie;
        if(ie)
         $(this).find("b").hide();
        else
         $(this).find("b").stop().animate({ opacity:0.4 }, 160 );
      }, 
      function(){
         ie = $.browser.msie;
         if(ie)
          $(this).find("b").show();
         else
          $(this).find("b").stop().animate({ opacity:1 }, 160 );
      }
    );
	 
	jQuery("#searchKaltruaVideos > input.inputSearch").keydown(function(e){
		if (e.keyCode == 13) {
			return false;
		}
	}).clearInput();
     
}); 

var mykdp = new KalturaPlayerController("kaltura-static-playlist");
var divs = Array('tab1','tab2','tab3','tab4','tab5');
function init_page(entry_id,tag) {
  mykdp.currentEntryId = entry_id;
  $('#'+tag+'_'+entry_id).addClass('active_entry');
  $('#videoBoxMenu li a').each(function(){
   $(this).click(function(){
    toggle_visibles(this.id);
    a_id = this.id;
    li_parent = $(this).parent('li');
    $('#videoBoxMenu li').removeClass('active');
    $('#videoBoxMenu li').attr('style','');
    $(li_parent).addClass('active');
    $(li_parent).attr('style','background-color:#'+color_for_id(a_id));
   });
  });
  toggle_visibles('tab1');
  $('#tab1').click();
  $('div.scrollable ul.clearfix li').each(function(){
   if(this.id) {
    entry_id = this.id;
    //thumb_url = 'http://cdn.kaltura.com/p/12136/sp/1213600/thumbnail/entry_id/'+entry_id+'/version/100000/height/83';
    //$('#'+entry_id+' img').attr('src',thumb_url);
    $(this).click(function(){
      switch_player_item(this.id);
     });
   }
  });
}

function color_for_id(id){
  if(id == 'tab1') return 'f93839';
  if(id == 'tab2') return 'ec8131';
  if(id == 'tab3') return '4386bd';
  if(id == 'tab4') return 'a6e5ec';
  if(id == 'tab5') return '7fab3a';
}

function switch_player_item(entry_id,tag) {
  mykdp.insertEntry(entry_id,true);
  mykdp.currentEntryId = entry_id;
  if(tag) {
   $('#'+tag+'_'+entry_id).addClass('active_entry');
  }
}

function toggle_visibles(divid) {
  for(i=0;i<divs.length;i++) {
    $('#'+divs[i]+'_row1').hide();
    $('#'+divs[i]+'_row2').hide();
  }
  $('#'+divid+'_row1').show();
  $('#'+divid+'_row2').show();
}

$('document').ready(function(){
 init_page();
});
