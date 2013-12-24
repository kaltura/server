package lib.Kaltura.output;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;

public class StandaradOutput implements OutputInterface {

	@Override
	public void start() {
		System.out.println("[" + getTime() + "] ==> Start.");
	}

	@Override
	public void write(String msg) {
		System.out.println(msg);
	}

	@Override
	public void end() {
		System.out.println("[" + getTime() + "] ==> End.");

	}

	protected String getTime() {
		DateFormat df = new SimpleDateFormat("yyyy/mm/dd HH:mm:ss");
		Date currentTime = Calendar.getInstance().getTime();        
		return df.format(currentTime);
	}
}
