package lib.Kaltura;

import java.io.PrintWriter;
import java.io.StringWriter;
import java.util.ArrayList;
import java.util.List;

import lib.Kaltura.notification.Processor;
import lib.Kaltura.notification.handlers.SyncSampleHandler;
import lib.Kaltura.output.Console;
import lib.Kaltura.output.OutputInterface;
import lib.Kaltura.output.StandaradOutput;

import com.kaltura.client.types.KalturaHttpNotification;

/**
 *  This class is a sample class for notification handling
 */
public class HttpNotificationHandler {
	
	/** The processor responsible for the notification handling*/
	private Processor processor;
	/** The console used for handling */
	private Console console;

	/**
	 * Constructor
	 */
	public HttpNotificationHandler() {
		List<OutputInterface> output = new ArrayList<OutputInterface>();
		output.add(new StandaradOutput());
		this.console = new Console(output);
		this.console.start();
		
		processor = new Processor(console);
		processor.registerHandler(new SyncSampleHandler(console));
	}
	
	/**
	 * Single event handling
	 * @param httpNotification
	 */
	public void handle(KalturaHttpNotification httpNotification) {
		startHandling();
		try {
			
			console.write("Statrted notification handling : ");
			console.write("\tNotificationID : " + httpNotification.eventNotificationJobId);
			console.write("\tTemplate : " + httpNotification.templateName + "(" + httpNotification.templateId + ") - " + httpNotification.templateSystemName);
			console.write("\tEvent Type : " + httpNotification.eventType);
			console.write("\tEvent Object : " + httpNotification.eventObjectType);
			
			processor.handleNotification(httpNotification);
			
		} catch (Exception e) {
			StringWriter sw = new StringWriter();
			e.printStackTrace(new PrintWriter(sw));
			String exceptionAsString = sw.toString();
			console.write("An error occurred!");
			console.write(e.getCause() + ": " + e.getMessage());
			console.write(exceptionAsString);
		} finally {
			doneConsole();
		}
	}
	
	public void finalize() {
		console.end();
	}
	
	private void startHandling() {
		console.write("==============================");
		console.write("Started handling notification");
		console.write("==============================");
	}
	
	private void doneConsole() {
		console.write("==============================");
		console.write("Done handling notification");
		console.write("==============================");
		
	}
}
