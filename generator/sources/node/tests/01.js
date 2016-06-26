var Unit = require('deadunit')
var kc = require('../KalturaClient');
var ktypes = require('../KalturaTypes');
var vo = require ('../KalturaVO.js');
var config = require ('./config.js');

    var cb = function (results){
	if(results){
	    var test = Unit.test('Create -2 Admin session', function () {
		this.count(1);
		if(results.code && results.message){
		    this.log(results.message);
		    this.log(results.code);
		    this.ok(false);
		}else{
		    this.log('KS is: '+results);
		    this.ok(true);
		}
		test.writeConsole(); // writes colorful output!
	    });
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
    var ks = client.session.start(cb, config.minus2_admin_secret, config.user_id, type, config.minus2_partner_id, expiry, privileges);
