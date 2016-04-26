var async = require('async');
var kc = require('../KalturaClient');
var ktypes = require('../KalturaTypes');
var vo = require ('../KalturaVO.js');
var config = require ('./config.js');

console.log('in main');

var create_session = function (results)
{
    console.log(results);
    if(results){
	    if(results.code && results.message){
		console.log(results.message);
		console.log(results.code);
		process.exit(1);
	    }else{
		console.log('KS is: '+results);
	    }
    }else{
	console.log('Something went wrong here :(');
    }
}

var create_upload_token = function (results)
{
console.log(results);
    if(results){
	    if(results.code && results.message){
		console.log(results.message);
		console.log(results.code);
		//process.exit(1);
	    }else{
		console.log('Upload token created. '+results);
	    }
    }else{
	console.log('Something went wrong here :(');
    }
}
doABunchOfThings(function() {
  console.log('back in main');
});

function doABunchOfThings(fnCallback) {
  async.series([
    function(callback) {
	console.log('step 0');
    var kaltura_conf = new kc.KalturaConfiguration(config.minus2_partner_id);
    kaltura_conf.serviceUrl = config.service_url ;
    var client = new kc.KalturaClient(kaltura_conf);
    var type = ktypes.KalturaSessionType.ADMIN;

    var expiry = null;
    var privileges = null;
    var ks = client.session.start(create_session, config.minus2_admin_secret, config.user_id, type, config.minus2_partner_id, expiry, privileges);
	console.log(ks);
      callback();
    },
    function(callback) {
      setTimeout(callback, 1000);
      console.log('client.session.start');
    },
    function(callback) {
	callback();
    },
    function(callback) {
      setTimeout(callback, 2000);
      console.log('client1.uploadToken.add');
	console.log('step 1');
	var kaltura_conf = new kc.KalturaConfiguration(config.partner_id);
	kaltura_conf.serviceUrl = config.service_url ;
	var client1 = new kc.KalturaClient(kaltura_conf);
	var type = ktypes.KalturaSessionType.USER;

	var expiry = null;
	var privileges = null;
	var ks = client1.session.start(create_session, config.secret, config.user_id, type, config.partner_id, expiry, privileges);
	console.log(ks);
	var uploadToken = new vo.KalturaUploadToken();
	uploadToken.fileName = "~/downloads/cat.mp4";
	var result = client1.uploadToken.add(create_upload_token, uploadToken);
	/*var uploadTokenId = result.id;
	var fileData = "~/downloads/cat.mp4";
	var resume = null;
	var finalChunk = null;
	var resumeAt = null;
	var result = client.uploadToken.upload(upload_entry, uploadTokenId, fileData, resume, finalChunk, resumeAt);*/
    },
    function(callback) {
      console.log('step 3');
	var uploadToken = new vo.KalturaUploadToken();
	uploadToken.fileName = "~/downloads/cat.mp4";
	var result = client1.uploadToken.add(create_upload_token, uploadToken);
      callback();
    },
  ], function(err, results) {
    console.log('done with things');
    fnCallback();
  });
}
