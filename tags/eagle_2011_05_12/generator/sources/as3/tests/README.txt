The test API sample shows a simple client setup and session creation call.
For good practice, it is always best to keep the secret key hidden in the server and recieve the kaltura session (aka KS) via flashvars or a different external method.

To setup the sample to compile, copy the com folder to the root of the sample code, open the KalturaClientSample.fla in Flash IDE (CS4 and above) and compile.
You should see an error saying you need to define the partner id and secret api key. 
Open the KalturaClientSample.as file and edit the following lines, adding your Kaltura partner information:
private const API_SECRET = "ENTER_YOUR_API_SECRET_KEY";
private const KALTURA_PARTNER_ID = "ENTER_YOUR_PARTNER_ID";
Compile again. You should get a message in the trace window indicating the session create call was successful and the actual KS returned from the Kaltura server.

If you are using a Kaltura self hosted server, open the KalturaClientSample.as file and uncomment and modify the following line (change the url to your Kaltura server domain):
//configuration.domain = "http://www.mykalturadomain.com";