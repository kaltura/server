package lib.Kaltura.output;

import java.util.List;


/**
 * Console class
 *
 * The console logs stuf as well as outputting it to the stdOut (optional)
 * 
 * @package Kaltura
 * @subpackage Output
 */
public class Console
{
	private List<OutputInterface> outputIfc;
	
	public Console(List<OutputInterface> outputIfc) {
		this.outputIfc = outputIfc;
	}
	
	public void start() {
		for (OutputInterface output : outputIfc) {
			output.start();	
		}
	}
	
	public void end() {
		for (OutputInterface output : outputIfc) {
			output.end();	
		}
	}
	
	public void write(String msg) {
		for (OutputInterface output : outputIfc) {
			output.write(msg);	
		}
	}
}
