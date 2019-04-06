package dovestech;

public class CleanThread extends Thread {
	private ElasticProcess elastic = new ElasticProcess();

    public void run() {
    	while (true) {
    		try {
    			long all = elastic.clean();
    			
    			System.out.println("Cleaned up: " + all);
    			Thread.sleep(6000000);
    		}
    		catch (Exception e) {
    			
    		}
    	}
    }
}
