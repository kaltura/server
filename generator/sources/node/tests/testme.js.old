var cb = function (results){
    if(results){
	console.log(results);
    }
    // console.log(client);

	if(results.code && results.message){
	    console.log('FAILED');
	    console.log(results.message);
	    console.log(results.code);
	    process.exit(1);
	}
};

var kc = require('../KalturaClient');
var ktypes = require('../KalturaTypes');
var vo = require ('../KalturaVO.js');
var config = require ('./config.js');

try{
    var kaltura_conf = new kc.KalturaConfiguration(config.minus2_partner_id);
}catch(e){
    console.log(e);
    process.exit(1);
}
kaltura_conf.serviceUrl = config.service_url ;

var client = new kc.KalturaClient(kaltura_conf);
var type = ktypes.KalturaSessionType.ADMIN;

var expiry = null;
var privileges = null;
//console.log('***************Creating -2 admin session***************');
var ks = client.session.start(cb, config.minus2_admin_secret, config.user_id, type, config.minus2_partner_id, expiry, privileges);
var partner = new vo.KalturaPartner();
partner.name = "MBP";
partner.appearInSearch = null;
partner.adminName = "MBP";
partner.adminEmail = "mbp@example.com";
partner.description = "MBP";
var cms_password = 'testit';
var template_partner_id = null;
var silent = null;
//console.log('***************Registering '+partner.name + 'with ' + partner.adminEmail+ '***************');
var result = client.partner.register(cb, partner, cms_password, template_partner_id, silent);

