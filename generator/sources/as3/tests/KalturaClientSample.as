package {
	import com.kaltura.KalturaClient;
	import com.kaltura.commands.session.SessionStart;
	import com.kaltura.config.KalturaConfig;
	import com.kaltura.events.KalturaEvent;
	import com.kaltura.types.KalturaSessionType;
	
	import flash.display.Sprite;

	public class KalturaClientSample extends Sprite
	{
		private const API_SECRET = "";
		private const KALTURA_PARTNER_ID = "";
		
		public function KalturaClientSample()
		{
			if (API_SECRET == "ENTER_YOUR_API_SECRET_KEY" ||
				KALTURA_PARTNER_ID == "ENTER_YOUR_PARTNER_ID") {
				throw (new Error("Please edit the const for the partner id and api secret"));
				return;
			}
			var configuration : KalturaConfig = new KalturaConfig();
			var kaltura : KalturaClient = new KalturaClient( configuration );	
			var startSession : SessionStart = new SessionStart(API_SECRET, 'testUserName', KalturaSessionType.USER, KALTURA_PARTNER_ID);
			startSession.addEventListener(KalturaEvent.COMPLETE, completed);
			startSession.addEventListener(KalturaEvent.FAILED, failed);
			kaltura.post( startSession );
		}
		
		private function completed (event:KalturaEvent):void {
			trace ("Session Started: " + event.success);
			trace (event.data);
		}
		
		private function failed (event:KalturaEvent):void {
			trace ("Session Failed: " + event.error.errorMsg);
		}
	}
}
