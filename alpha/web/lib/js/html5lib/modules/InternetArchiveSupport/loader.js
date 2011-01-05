/**
* EmbedPlayer loader
*/
/**
* Default player module configuration
*/

/*
( function( mw ) {
 
  mw.addResourcePaths( {
      "mw.InternetArchiveSupport"	: "mw.InternetArchiveSupport.js"
  } );
*/

mw.IA = 
{
  playingClipNumMW:0,
  flowplayerplaylist:null,
  VIDEO_WIDTH:640,
  VIDEO_HEIGHT:480,
  
  
  
  css:function(str)
  {
    var obj = document.createElement('style');
    obj.setAttribute('type', 'text/css');
    if (obj.styleSheet)
      obj.styleSheet.cssText = str; //MSIE
    else
      obj.appendChild(document.createTextNode(str)); // other browsers
    
    var headobj = document.getElementsByTagName("head")[0];
    headobj.appendChild(obj);
  },

  
  // parse a CGI arg
  arg:function(theArgName)
  {
    sArgs = location.search.slice(1).split('&');
    r = '';
    for (var i=0; i < sArgs.length; i++)
    {
      if (sArgs[i].slice(0,sArgs[i].indexOf('=')) == theArgName)
      {
        r = sArgs[i].slice(sArgs[i].indexOf('=')+1);
        break;
      }
    }
    return (r.length > 0 ? unescape(r).split(',') : '')
  },

  
  // try to parse the identifier from the video and make the lower right icon
  // then go to the item's /details/ page
  detailsLink:function()
  {
    if (typeof(location.pathname)!='undefined'  &&
        location.pathname.length>0  &&
        location.pathname.match(/^\/details\//))
    {
      return '/details/'+location.pathname.replace(/^\/details\/([^\/]+).*$/,
                                                   '$1');
    }
    if (typeof(location.pathname)!='undefined'  &&
        location.pathname.length>0  &&
        location.pathname.match(/^\/embed\//))
    {
      return '/details/'+location.pathname.replace(/^\/embed\/([^\/]+).*$/,
                                                   '$1');
    }
    return '';
  },


  embedUrl:function()
  {
    return ('http://www.archive.org/embed/' +
            mw.IA.detailsLink().replace(/\/details\//,''));
  },


  // marks the playlist row for the video that is playing w/ orange triangle
  indicateIsPlaying:function(clipnum)
  {
    var els = this.flowplayerplaylist.getElementsByTagName("img");
    for (var i=0; i < els.length; i++)
      els[i].style.visibility = (i==clipnum ? 'visible' : 'hidden');
  },






// Set up so that:
//   - when "click to play" clicked, resize video window and playlist
//   - we advance to the next clip (when 2+ present)
newEmbedPlayerMW:function(arg)
{
  var player = $('#mwplayer');
  if (!player)
    return;

  mw.log('newEmbedPlayerMW()');
  player.bind('ended', mw.IA.onDoneMW);
  player.unbind('play').bind('play', mw.IA.firstplayMW);

  player.bind('onCloseFullScreen', function(){ setTimeout(function() { mw.IA.resizeMW(); }, 500); }); //xxxx timeout lameness
},
  

resizeMW:function()
{
  var player = $('#mwplayer');
  
  $('#flowplayerdiv')[0].style.width  = this.VIDEO_WIDTH;
  $('#flowplayerdiv')[0].style.height = this.VIDEO_HEIGHT;
  
  $('#flowplayerplaylist')[0].style.width  = this.VIDEO_WIDTH;
  
  var jplay = player[0];
  IAD.log('IA ' + jplay.getWidth() + 'x' + jplay.getHeight());
  
  jplay.resizePlayer({'width':  this.VIDEO_WIDTH,
        'height': this.VIDEO_HEIGHT},true);
},
  

firstplayMW:function()
{
  if (typeof(mw.IA.MWsetup)!='undefined')
    return;
  mw.IA.MWsetup = true;

  mw.log('firstplayMW()');
  mw.IA.resizeMW();
},


playClipMW:function(idx, id, mp4, ogv)
{
  mw.IA.playingClipNumMW = idx;
  mw.log('IA play: '+mp4+'('+idx+')');

  // set things up so we can update the "playing triangle"
  this.flowplayerplaylist = $('#flowplayerplaylist')[0];
  this.indicateIsPlaying(idx);

  mw.ready(function(){

      var player = $('#mwplayer'); // <div id="mwplayer"><video ...></div>
      if (!player)
        return;
      
      player.embedPlayer(
        { 'autoplay' : true, 'autoPlay' : true,
            'sources' : [
              { 'src' : '/download/'+id+'/'+mp4 },
              { 'src' : '/download/'+id+'/'+ogv }
              ]
            }
        );
    });

  return false;
},


onDoneMW:function(event, onDoneActionObject )
{
  mw.IA.playingClipNumMW++;
  
  var plist = $('#flowplayerplaylist')[0].getElementsByTagName('tr');
  mw.log(plist);
  var row=plist[mw.IA.playingClipNumMW];
  if (typeof(row)=='undefined')
    return;
  
  var js=row.getAttribute('onClick');
  //alert('HIYA xxxx tracey '+mw.IA.playingClipNumMW + ' ==> ' + js);
  
  var parts=js.split("'");
  var id=parts[1];
  var mp4=parts[3];
  var ogv=parts[5];
  
  mw.IA.playClipMW(mw.IA.playingClipNumMW, id, mp4, ogv);
},


  
  

  setup:function()
  {
    mw.IA.css(".archive-icon {\n\
background-image:url('http://www.archive.org/images/logo-mw.png') !important;\n\
background-repeat:no-repeat;\n\
display: block;\n\
height: 12px;\n\
width: 12px;\n\
margin-top: -6px !important;\n\
margin-left: 3px !important;\n\
}\n\
\n\
div.control-bar { -moz-border-radius:6px; -webkit-border-radius:6px; -khtml-border-radius:6px; border-radius:6px; }\n\
\n\
");


    var det = mw.IA.detailsLink();
    
    if (det == ''  &&  typeof(document.getElementsByTagName)!='undefined')
    {
      var els = document.getElementsByTagName('object');
      if (els  &&  els.length)
      {
        var i=0;
        for (i=0; i < els.length; i++)
        {
          var el = els[i];
          if (typeof(el.data)!='undefined')
          {
            var mat = el.data.match(/\.archive.org\/download\/([^\/]+)/);
            if (typeof(mat)!='undefined'  &&  mat  &&  mat.length>1)
            {
              det = '/details/' + mat[1]; //xxx not working yet for embed codes!!
              break;
            }
          }
        }
      }
    }
    
    
    //var mods = mw.getConfig('enabledModules');
    //mods.push('InternetArchiveSupport');


    mw.setConfig( {		
        // We want our video player to attribute something...
        "EmbedPlayer.AttributionButton" : true,
        
        // 'enabledModules' : mods,
        
        //"EmbedPlayer.NativeControlsMobileSafari" : true, //xxx
        
        // Our attribution button
        'EmbedPlayer.AttributionButton' : {
          'title' : 'Internet Archive',
          'href' : 'http://www.archive.org'+det,
          'class' : 'archive-icon'
        }
      });
    

    //alert(mw.getConfig('enabledModules'));
    //mw.load('mw.InternetArchiveSupport', function() { alert('loada'); });


    


    // NOTE: keep this outside "mw.ready()" so that "click-to-play" does indeed
    // cause the newEmbedPlayerMW() call
    $( mw ).bind('newEmbedPlayerEvent', mw.IA.newEmbedPlayerMW);

    mw.ready(function(){
        mw.log("IA Player says mw.ready()");
        
        
        var star = (mw.IA.arg('start') ? parseFloat(mw.IA.arg('start')) : 0);
        if (star)
        {
          mw.IA.resizeMW();
          var jplay = $('#mwplayer').get(0);
          var dura = jplay.duration;
          IAD.log(star+'s of '+dura+'s');
          
          jplay.currentTime = star;
          jplay.play();
        }
      });
  }
};

  

mw.IA.setup();
