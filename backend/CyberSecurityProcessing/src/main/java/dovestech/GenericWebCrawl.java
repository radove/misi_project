package dovestech;

import java.io.File;
import java.util.Date;
import java.util.HashSet;
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

public class GenericWebCrawl extends Thread {
	private ElasticProcess elastic = new ElasticProcess();
	private Set<String> visitedURL = new HashSet<String>();

    public void run() {
		int i = 0;
    	while (true) {
    		try {
    			i++;
    			if (i > 30000000) {
    				visitedURL.clear();
    			}
    			Thread.sleep(30000);
    			Set<String> urls = elastic.getURLs();
    			for (String url : urls) {
    				genericDownload(url, true);
    			}
    		}
    		catch (Exception e) {
    			// Keep Trying
    			e.printStackTrace();
        		System.out.println(e.getMessage());
    			System.out.println("Full Loop: Ran into trouble importing a file");
    		}
    	}
    }

    private void genericDownload(String dataString, boolean goFurther) {
    	try {
    		String split[] = dataString.split("\\|");
    		String url = split[0];
    		String user_category = split[1];
    		if (!visitedURL.contains(url)) {
    			visitedURL.add(url);
    			if (!url.startsWith("https")) {
        			visitedURL.add(url.replace("http", "https"));
    			}
    			
        		if (!goFurther) {
        			System.out.println("--> Started Crawling: " + url);
        		}
        		else {
        			System.out.println("Started Crawling: " + url);
        		}
        		
	    		Document doc = Jsoup.connect(url).get();
	        	WebCrawlEntry entry = new WebCrawlEntry();
	        	if (!goFurther) {
	        		entry.setData_type("webharvest");
	        	}
	        	entry.setTimestamp(new Date().getTime() + "");
	        	entry.setId(DigestUtils.md5Hex(url));
	        	entry.setTitle(doc.title());
	    		entry.setContent(doc.text());
	    		entry.setUrl(url);
	    		entry.setUser_category(user_category);
	    		entry.setNlp(doc.text());
	        	CyberUtils cyberUtils = new CyberUtils();
	        	entry.setNgram(cyberUtils.getTokens(entry, new TreeSet<String>(), true));
	        	entry.setNgramSingle(cyberUtils.getTokens(entry, new TreeSet<String>(), false));
	        	elastic.indexDoc(entry);
	        	
	    		if (goFurther) {
	    			Elements links = doc.select("a");
	    			for (Element link : links) {
	    	        	String newURL = link.attr("abs:href");
	    	        	if (!newURL.contains("#")) {
		    	        	genericDownload(newURL + "|" + user_category, false);
	    	        	}
	    			}
	    		}

	    		if (!goFurther) {
	    			System.out.println("--> Finished Crawling: " + url);
	    		}
	    		else {
	    			System.out.println("Finished Crawling: " + url);
	    		}
    		}
    		else {
    			System.out.println("Didn't Web Crawl This Time: " + url);
    		}

    	}
    	catch (Exception e) {
    		return;
    	}

    }
}
