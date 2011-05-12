package {
	import flash.display.Sprite;
	import com.kaltura.config.KalturaConfig;
	import com.kaltura.KalturaClient;
	import com.kaltura.commands.session.SessionStart;
	import com.kaltura.events.KalturaEvent;

	public class KalturaClientSample extends Sprite
	{
		private const API_SECRET = "ENTER_YOUR_API_SECRET_KEY";
		private const KALTURA_PARTNER_ID = "ENTER_YOUR_PARTNER_ID";
		
		public function KalturaClientSample()
		{
			if (API_SECRET == "ENTER_YOUR_API_SECRET_KEY" ||
				KALTURA_PARTNER_ID == "ENTER_YOUR_PARTNER_ID") {
				throw (new Error("Please edit the const for the partner id and api secret"));
				return;
			}
			trace ("started!");
			var configuration : KalturaConfig = new KalturaConfig();
			configuration.partnerId = KALTURA_PARTNER_ID;
			configuration.clientTag = "mySampleApplication";
			//configuration.domain = "http://www.mykalturadomain.com";
			
			var kaltura : KalturaClient = new KalturaClient( configuration );	
			
			var startSession : SessionStart = new SessionStart(API_SECRET);
			startSession.addEventListener(KalturaEvent.COMPLETE, completed);
			startSession.addEventListener(KalturaEvent.FAILED, failed);
			
			kaltura.post( startSession );
		}
		
		private function completed (event:KalturaEvent):void {
			trace ("complete: " + event.success);
			trace (event.data);
		}
		
		private function failed (event:KalturaEvent):void {
			trace ("failed: " + event.success);
			trace (event.data);
		}
	}
}
