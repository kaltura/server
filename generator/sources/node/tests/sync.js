var sync = require('synchronize');
var fiber = sync.fiber;
var await = sync.await;
var defer = sync.defer;
var kaltura = require('../KalturaClient');
var ktypes = require('../KalturaTypes');
var vo = require ('../KalturaVO.js');
var config = require ('./config.js');

function init_client(callback) {
	console.log('Initializing client');
	var clientConfig = new kaltura.KalturaConfiguration(config.partner_id);
	var client = new kaltura.KalturaClient(clientConfig);

	clientConfig.serviceUrl = config.service_url;

	var type = ktypes.KalturaSessionType.ADMIN;
	
	if(typeof callback === 'function'){
	    client.session.start(function(ks) {
		    client.setKs(ks);
		    console.log(ks);
		    callback(client);
	    }, config.admin_secret, 'test', type, config.partner_id, 86400, 'disableentitlement');
	}else{
		client.setKs(callback);
		return client;
	}
}

function cb(results)
{
	console.log(results);
}
try {
    fiber(function() {
	client=(init_client());
	var partner = new vo.KalturaPartner();
	partner.name = "MBP";
	partner.appearInSearch = null;
	partner.adminName = "MBP";
	partner.adminEmail = "mbp@example.com";
	partner.description = "MBP";
	var cms_password = 'testit';
	var template_partner_id = null;
	var silent = null;
	var result = client.partner.register(cb, partner, cms_password, template_partner_id, silent);

	
        //var obj2 = await( anotherAsyncMethod( obj1, defer() ) );
        //var result = await( lastAsyncMethod( obj2, defer() ) );
    });
} catch(err) {
    //TODO Handle error
}
