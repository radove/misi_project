package dovestech;

import java.io.File;
import java.io.StringReader;
import java.util.Date;
import java.util.HashSet;
import java.util.LinkedHashSet;
import java.util.List;
import java.util.Scanner;
import java.util.Set;
import java.util.StringTokenizer;
import java.util.TreeSet;

import org.apache.commons.codec.digest.DigestUtils;
import org.apache.lucene.analysis.CharArraySet;
import org.apache.lucene.analysis.en.EnglishAnalyzer;
import org.apache.lucene.analysis.standard.StandardTokenizer;
import org.apache.lucene.analysis.tokenattributes.CharTermAttribute;
import org.apache.lucene.util.AttributeFactory;
import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

import com.google.common.collect.Lists;

public class CyberWebDownload extends Thread {
	private static Set<String> stopwords = new HashSet<String>();
	private ElasticProcess elastic = new ElasticProcess();

    public void run() {
    	while (true) {
    		try {
    			crawlCyberWire();
    			Thread.sleep(30000000);
    		}
    		catch (Exception e) {
    			e.printStackTrace();
    			return;
    		}
    	}
    }
    
    private void crawlCyberWire() {
    	try {
    		Document doc = Jsoup.connect("https://thecyberwire.com/events.html").get();
			
	    	Elements events = doc.select("div[id=current] > p");

	    	for (Element event : events) {
	        	WebCrawlEntry entry = new WebCrawlEntry();
	        	Elements objs = null;
	        	
	        	entry.setContent(event.ownText());
	
	
	        	Element link = event.select("a.storyHeaderLink").first();
	        	String url = link.attr("abs:href");
	        	entry.setTitle(link.text());
	    		entry.setUrl(url);
	    		entry.setUser_category("Event Website");
	
	    		String location = "";
	        	objs = event.select("span.storySource");
	        	for (Element obj : objs) {
	        		String loc = obj.text();
	        		loc = loc.replaceAll("\\(", "");
	        		loc = loc.replaceAll("\\)", "");
	        		location = loc;
	        	}
	        	
	        	entry.setNlp(entry.getTitle() + ", " + location + ", " + entry.getContent());

	        	CyberUtils cyberUtils = new CyberUtils();
	        	entry.setNgram(cyberUtils.getTokens(entry, new TreeSet<String>(), true));
	        	entry.setNgramSingle(cyberUtils.getTokens(entry, new TreeSet<String>(), false));

	        	entry.setTimestamp(new Date().getTime() + "");
	        	entry.setId(DigestUtils.md5Hex(entry.getTitle()));
	        	entry.setData_type("webcustom");
	        	System.out.println("Indexed Event: " + entry.getId() + entry.getTitle());
	        	elastic.indexDoc(entry);
			}
    	}
    	catch (Exception e) {
			e.printStackTrace();
	    	return;
    	}
    }
}
