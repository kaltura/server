var kaltura = require('kaltura');

partner_id=102;
service_url='https://www.kaltura.com';
secret='';
var kaltura_conf = new kaltura.kc.KalturaConfiguration(partner_id);
kaltura_conf.serviceUrl = service_url ;
var client = new kaltura.kc.KalturaClient(kaltura_conf);
var type = kaltura.kc.enums.KalturaSessionType.ADMIN;

var expiry = null;
var privileges = null;
var ks = client.session.start(print_ks,secret , 'some@user.com', type, partner_id, expiry, privileges);

function print_ks(result)
{
	console.log(result);
}
