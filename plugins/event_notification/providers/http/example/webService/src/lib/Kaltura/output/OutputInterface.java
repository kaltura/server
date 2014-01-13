package lib.Kaltura.output;

/**
 * OutpuInterface interface
 *
 * @package Kaltura
 * @subpackage Output
 */
public interface OutputInterface
{
	/**
	 * Initializes and starts the output
	 */
	public void start();
	
	/**
 	 * Write message
 	 *
 	 * @param String, message you want to write
 	 */
	public void write(String msg);
	
	/**
	 * Terminates and closes the output
	 */
	public void end();

}
