package dovestech;

import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.io.StringReader;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashMap;
import java.util.HashSet;
import java.util.LinkedHashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.TreeSet;

import org.apache.lucene.analysis.standard.StandardTokenizer;
import org.apache.lucene.analysis.tokenattributes.CharTermAttribute;
import org.apache.lucene.util.AttributeFactory;

import com.twitter.hbc.core.endpoint.Location;
import com.twitter.hbc.core.endpoint.Location.Coordinate;

public class ConfigReader {

	private static Map<String, String> mappings = new HashMap<String, String>();
	private static Map<String, Set<String>> keywordMapping = new HashMap<String, Set<String>>();
	private static List<Location> geo = new ArrayList<Location>(); 
	private static List<Long> followingList = new ArrayList<Long>(); 
	
	public ConfigReader(String configFile) {
		if (mappings.isEmpty()) {
			try {
				BufferedReader reader = new BufferedReader(new FileReader(
						configFile));
				String line = reader.readLine();
				while (line != null) {
					line = reader.readLine();
					if (line == null) {
						break;
					}
					if (line.startsWith("#") || !line.contains("=")) {
						break;
					}
					String[] data = line.split("=");
					String key = data[0];
					key = key.toLowerCase();
					String value = data[1];
					
					if (key.contains(".keyword")) {
						value = value.toLowerCase();
						Set<String> setList = new HashSet<String>();
						key = key.replaceAll(".keyword", "");
						setList.addAll(Arrays.asList(value.split(",")));
						keywordMapping.put(key,  setList);
					}
					else if (key.contains(".geo")) {
						key = key.replaceAll(".geo", "");
						String[] geoString = value.split(",");
						double lat1 = Double.parseDouble(geoString[0]);
						double lon1 = Double.parseDouble(geoString[1]);
						double lat2 = Double.parseDouble(geoString[2]);
						double lon2 = Double.parseDouble(geoString[3]);
						Location loc = new Location(new Coordinate(lat1, lon1), new Coordinate(lat2, lon2));
						geo.add(loc);
					}
					else if (key.contains(".following")) {
						key = key.replaceAll(".following", "");
						String[] followString = value.split(",");
						
						for (String follow : followString) {
							followingList.add(Long.parseLong(follow));
						}
					}
					else {
						mappings.put(key, value);
					}
				}
				reader.close();
			} catch (IOException e) {
				e.printStackTrace();
			}
		}
		
		for (String key : mappings.keySet()) {
			System.out.println(key + "=" + mappings.get(key));
		}
	}
	
	public String getCategory(String content) throws Exception {
		content = content.toLowerCase();
		for (String key : keywordMapping.keySet()) {
			Set<String> values = keywordMapping.get(key);
			for (String value : values) {
	    	    if (content.contains(value)) {
	    	    	return key;
	    	    }
			}
		}
		
		return "";
		
	}
	
	public List<String> getKeywords() {
		List<String> keywords = new ArrayList<String>();
		for (String key : keywordMapping.keySet()) {
			Set<String> values = keywordMapping.get(key);
			for (String v : values) {
				keywords.add(v);
			}
		}
		return keywords;
	}
	
	public List<Location> getLocations() {
		return geo;
	}
	
	public List<Long> getFollowings() {
		return followingList;
	}
	
	public String getProperty(String key) {
		key = key.toLowerCase();
		return mappings.get(key);
	}
}
