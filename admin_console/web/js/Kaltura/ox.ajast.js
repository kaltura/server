// This file contains a simple Javascript broker that encapsulates 
// the AJAST technique, allowing for cross-domain REST 
// (REpresentatoinal State Transfer) calls.
// 
// Copyright (c) 2008 Håvard Stranden <havard.stranden@gmail.com>
//
// Permission is hereby granted, free of charge, to any person
// obtaining a copy of this software and associated documentation
// files (the "Software"), to deal in the Software without
// restriction, including without limitation the rights to use,
// copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the
// Software is furnished to do so, subject to the following
// conditions:
// 
// The above copyright notice and this permission notice shall be
// included in all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
// OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
// NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
// HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
// WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
// FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
// OTHER DEALINGS IN THE SOFTWARE.

if(typeof(OX) === 'undefined') var OX = {};
OX.AJAST = 
{
  Broker : function(url, callbackparameter, optional_decode_json_response, optional_timeout_milliseconds, optional_default_params)
  {
    this.url = url;
    this.cb = callbackparameter;
    this.params = [];
    this.timeout = optional_timeout_milliseconds || 5000; // Timeout in milliseconds
    if(typeof(optional_default_params) !== 'undefined')
    {
      for(p in optional_default_params)
        this.params.push(p + '=' + encodeURIComponent(optional_default_params[p]));
    }
    
    this.jsonmode = optional_decode_json_response || false;
  },
  
  __callbacks__ : {},
  
  __callid__ : 1,
  
  call: function(url, callbackparameter, callbackfunction, optional_timeout, optional_decode_json_response)
  {
    var callbackid = 'callback' + OX.AJAST.__callid__;
    
    // Append callback parameter (this also implicitly avoids caching, since the callback id is different for each call)
    url += '&' + encodeURIComponent(callbackparameter) + '=' + encodeURIComponent('OX.AJAST.__callbacks__.' + callbackid);
      
    // Create script tag for the call
    var tag = OX.AJAST.createScriptTag(url);
    // Get the head of the document
    var head = document.getElementsByTagName('head').item(0);
    
      
    // Create a timeout function  
    var timedout = function()
    {
      if(OX.AJAST.__callbacks__[callbackid] !== 'undefined') // If the callback still exists...
      {
        // Replace original wrapped callback with a dummy that just deletes itself
        OX.AJAST.__callbacks__[callbackid] = function(){ delete OX.AJAST.__callbacks__[callbackid]; }; 
        // Signal that the call timed out
        callbackfunction(false); 
        // Remove the script tag (timed out)
        head.removeChild(tag); 
      }    
    };
    
    // Create timer for the timeout function
    var timer = setTimeout(timedout, optional_timeout || 5000);
      
    var decode_response = optional_decode_json_response || false;
    
    // Create the callback function          
    OX.AJAST.__callbacks__[callbackid] = function(data)
    {
      // Clear the timeout
      clearTimeout(timer);
      
      if(typeof(data) === 'undefined')
        callbackfunction(false); // Callback with nothing
      else
      {
        callbackfunction(true, decode_response ? eval(data) : data);
      }
      // Replace original callback with a dummy function 
      delete OX.AJAST.__callbacks__[callbackid];
      // Remove the script tag (finished)
      head.removeChild(tag);
    };
    
    // Inject the call
    head.appendChild(tag);
  },
  
  createScriptTag: function(url)
  {
    var s = document.createElement('script');
    s.setAttribute('type', 'text/javascript');
    //BUG-FIX (Zohar,04-01-2010): OX.AJAST.Broker.__callid__++) should be OX.AJAST.__callid__++
    s.setAttribute('id', 'oxajastcall' + OX.AJAST.__callid__++);
    s.setAttribute('src', url);
    return s;
  }
};

OX.AJAST.Broker.prototype.call = function(params, callback)
{
  // Create arguments
  var args = [];
  for(p in params)
    args.push(p + '=' + encodeURIComponent(params[p]));
  for(p in this.params)
    args.push(this.params[p]);
  //BUG-FIX (Zohar,04-01-2010): Consider cases with ? already in the Url
  if (this.url.indexOf('?', 0) > -1)
	  this.url += '&' + args.join('&');
  else
	  this.url += '?' + args.join('&');
  OX.AJAST.call(this.url, this.cb, callback, this.timeout, this.jsonmode);
};