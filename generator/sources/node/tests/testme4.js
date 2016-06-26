var Unit = require('deadunit')
var kc = require('../KalturaClient');
var ktypes = require('../KalturaTypes');
var vo = require ('../KalturaVO.js');
var config = require ('./config.js');

var create_session = function (results)
{
    console.log(results);
    if(results){
	    if(results.code && results.message){
		console.log(results.message);
		console.log(results.code);
		//process.exit(1);
	    }else{
		console.log('KS is: '+results);
	    }
    }else{
	console.log('Something went wrong here :(');
    }
}

var create_partner = function (results)
{
    if(results){
	    if(results.code && results.message){
		console.log(results.message);
		console.log(results.code);
		//process.exit(1);
	    }else{
		console.log('Partner created, ID: '+results.id);
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

var upload_entry = function (results)
{
    if(results){
	    if(results.code && results.message){
		console.log(results.message);
		console.log(results.code);
		//process.exit(1);
	    }else{
		console.log(results);
	    }
    }else{
	console.log('Something went wrong here :(');
    }
}
var kaltura_conf = new kc.KalturaConfiguration(config.minus2_partner_id);
kaltura_conf.serviceUrl = config.service_url ;
var client = new kc.KalturaClient(kaltura_conf);
var type = ktypes.KalturaSessionType.ADMIN;

var expiry = null;
var privileges = null;
var ks = client.session.start(create_session, config.minus2_admin_secret, config.user_id, type, config.minus2_partner_id, expiry, privileges);

var partner = new vo.KalturaPartner();
partner.name = "MBP";
partner.appearInSearch = null;
partner.adminName = "MBP";
partner.adminEmail = "mbp@example.com";
partner.description = "MBP";
var cms_password = 'testit';
var template_partner_id = null;
var silent = null;
var result = client.partner.register(create_partner, partner, cms_password, template_partner_id, silent);

var kaltura_conf = new kc.KalturaConfiguration(config.partner_id);
kaltura_conf.serviceUrl = config.service_url ;
var client1 = new kc.KalturaClient(kaltura_conf);
var type = ktypes.KalturaSessionType.USER;

var expiry = null;
var privileges = null;
var ks = client1.session.start(create_session, config.secret, config.user_id, type, config.partner_id, expiry, privileges);
var uploadToken = new vo.KalturaUploadToken();
//var uploadToken.fileName = "~/downloads/cat.mp4";
var result = client1.uploadToken.add(create_upload_token, uploadToken);
/*var uploadTokenId = result.id;
var fileData = "~/downloads/cat.mp4";
var resume = null;
var finalChunk = null;
var resumeAt = null;
var result = client.uploadToken.upload(upload_entry, uploadTokenId, fileData, resume, finalChunk, resumeAt);*/
