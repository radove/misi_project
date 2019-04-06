package dovestech;

import java.io.File;
import java.util.Date;
import java.util.List;
import java.util.Set;
import java.util.TreeSet;

import org.apache.commons.codec.digest.DigestUtils;
import org.apache.commons.io.FileUtils;
import org.apache.commons.io.filefilter.TrueFileFilter;
import org.apache.tika.Tika;
import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

public class CyberImportData extends Thread {
	private ElasticProcess elastic = new ElasticProcess();

    public void run() {
    	while (true) {
    		try {
    			Thread.sleep(5000);
    			cyberInput();
    		}
    		catch (Exception e) {
    			// Keep Trying
    			e.printStackTrace();
        		System.out.println(e.getMessage());
    			System.out.println("Full Loop: Ran into trouble importing a file");
    		}
    	}
    }
    
    private void cyberInput() {
    	try {
    		File dir = new File("/opt/dropbox");
    		List<File> files = (List<File>) FileUtils.listFiles(dir, TrueFileFilter.INSTANCE, TrueFileFilter.INSTANCE);
    		for (File f : files) {
    			String fileName = f.getName();
    			String category = "";
    			if (fileName.endsWith("Resume")) {
    				category = "Resume";
    				fileName = fileName.replace("Resume", "");
    			}
    			if (fileName.endsWith("White_Paper")) {
    				category = "White Paper";
    				fileName = fileName.replace("White_Paper", "");
    			}
    			if (fileName.endsWith("Marketing")) {
    				category = "Marketing";
    				fileName = fileName.replace("Marketing", "");
    			}
        		ImportEntry entry = new ImportEntry();
        		entry.setUser_category(category);
        	    Tika tika = new Tika();
        	    String fileType = tika.detect(f);
		        entry.setFileType(fileType);
		        entry.setTitle(fileName);
		        String content = tika.parseToString(f);
		        entry.setContent(content);
		        entry.setNlp(content);
		        entry.setFileName(fileName);
		        entry.setId(DigestUtils.md5Hex(content));
	        	CyberUtils cyberUtils = new CyberUtils();
	        	entry.setNgram(cyberUtils.getTokens(entry, new TreeSet<String>(), true));
	        	entry.setNgramSingle(cyberUtils.getTokens(entry, new TreeSet<String>(), false));
	        	entry.setTimestamp(new Date().getTime() + "");
	        	elastic.indexDoc(entry);
	            new File("/var/www/html/root/cyber/data/" + entry.getId()).delete();
			    f.renameTo(new File("/var/www/html/root/cyber/data/" + entry.getId()));
    		}
    	}
    	catch (Exception e) {
    		System.out.println(e.getMessage());
			System.out.println("Ran into trouble importing a file");
	    	return;
    	}
    }
}
