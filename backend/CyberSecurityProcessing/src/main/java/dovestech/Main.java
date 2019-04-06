package dovestech;

public class Main {
	public static ConfigReader config = null;

	public static void main(String[] args) {
		log("Starting Process");
		(new CyberWebDownload()).start();
		(new CyberImportData()).start();
		(new GenericWebCrawl()).start();
		if (args.length == 1) {
			config = new ConfigReader(args[0]);
		}
		else {
			if (config == null) {
				log("Add a parameter for the config file...");
				return;
			}
		}
		try {
			(new CleanThread()).start();
			Thread.sleep(5000);
			(new TwitterThread("keywords")).start();

	    	if (!Main.config.getProperty("debug").equals("true")) {
				Thread.sleep(60000);
	    	}
	    	
			//(new TwitterThread("locations")).start();

		}
		catch (Exception e) {
			System.out.println("Trouble Starting Up");
		}
	}
	
	public static void log(String log) {
		System.out.println(log);
	}

}
