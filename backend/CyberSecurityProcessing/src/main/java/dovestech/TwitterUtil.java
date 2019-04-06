package dovestech;

import java.io.BufferedReader;
import java.io.File;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.StringReader;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashSet;
import java.util.LinkedHashSet;
import java.util.List;
import java.util.Scanner;
import java.util.Set;
import java.util.StringTokenizer;
import java.util.TreeSet;

import org.apache.lucene.analysis.CharArraySet;
import org.apache.lucene.analysis.en.EnglishAnalyzer;
import org.apache.lucene.analysis.standard.StandardTokenizer;
import org.apache.lucene.analysis.tokenattributes.CharTermAttribute;
import org.apache.lucene.util.AttributeFactory;

import com.fasterxml.jackson.databind.JsonNode;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.google.common.collect.Lists;

public class TwitterUtil {
	private ElasticProcess elastic = new ElasticProcess();
	private static ObjectMapper mapper = new ObjectMapper();

    public void processMessage(String json, String runType) throws Exception {
    	
    	if (Main.config.getProperty("debug").equals("true")) {
    		System.out.println(json);
    	}
    	
    	TwitterEntry entry = new TwitterEntry();
    	entry.setCategory("unknown");

    	Set<String> filterList = new HashSet<String>();
    	
    	JsonNode node = mapper.readTree(json);

    	JsonNode id = node.get("id");
    	if (id != null) {
    		entry.setId(id.asText());
    	}
    	else {
    		return;
    	}
    	
    	JsonNode extended = node.get("extended_tweet");
    	if (extended != null) {
    		JsonNode full_text = extended.get("full_text");
    		if (full_text != null) {
	    		entry.setContent(full_text.asText());
    		}
    		
    		JsonNode entities = extended.get("entities");
    		if (entities != null) {
    			JsonNode media = entities.get("media");
    			if (media != null) {
	    			if (media.isArray()) {
					    for (final JsonNode mediaObj : media) {
					    	if (mediaObj != null) {
			        			JsonNode mediaURL = mediaObj.get("media_url");
			        			if (mediaURL != null) {
			        				entry.setMedia_image_url(mediaURL.asText());
			        			}
					    	}
					    }
	    			}
	    			else {
	        			JsonNode mediaURL = media.get("media_url");
	        			if (mediaURL != null) {
	        				entry.setMedia_image_url(mediaURL.asText());
	        			}
	
	    			}
    			}

    			JsonNode tags = entities.get("hashtags");
    			if (tags != null) {
    				Set<String> hashtags = new TreeSet<String>();
	    			if (tags.isArray()) {
					    for (final JsonNode tagObj : tags) {
					    	if (tagObj != null) {
			        			JsonNode hash = tagObj.get("text");
			        			if (hash != null) {
			        				String tag = hash.asText().toLowerCase();
			        				if (!tag.equals("cybersecurity")) {
				        				hashtags.add(tag);
			        				}
			        			}
					    	}
					    }
	    			}
	    			entry.setHashtags(hashtags);
    			}
    		}    		
    	}
    	String text_result = entry.getContent();
    	if (text_result == null || text_result.isEmpty()) {
	    	JsonNode text = node.get("text");
	    	if (text != null) {
	    		entry.setContent(text.asText());
	    	}
    	}
    	
    	JsonNode place = node.get("place");
    	if (place != null) {
    		JsonNode country = place.get("country");
    		JsonNode country_code = place.get("country_code");
    		JsonNode location = place.get("full_name");
    		
    		if (country != null) {
    			entry.setCountry(country.asText());
    		}
    		if (country_code != null) {
    			entry.setCountry_code(country_code.asText());
    		}
    		if (location != null) {
    			entry.setLocation(location.asText());
    		}
    		
    		JsonNode bounding_box = place.get("bounding_box");
    		if (bounding_box != null) {
    			JsonNode coordinates = bounding_box.get("coordinates");
    			if (coordinates != null) {
    				if (coordinates.isArray()) {
    				    for (final JsonNode objNode : coordinates) {
    				    	if (objNode.isArray()) {
    	    				    for (final JsonNode newNode : objNode) {
    	    				    	String lon = newNode.get(0).asText();
    	    				    	String lat = newNode.get(1).asText();
    	    				    	entry.setGeo(lat + "," + lon);
    	    				    	break;
    	    				    }
    				    	}
    				    }
    				}
    			}
    		}
    	}
    	
    	JsonNode user = node.get("user");
    	if (user != null) {
    		JsonNode profileImage = user.get("profile_image_url");
    		JsonNode screenName = user.get("screen_name");
    		
    		if (profileImage != null) {
    			entry.setProfile_image(profileImage.asText());
    		}
    		if (screenName != null) {
    			filterList.add(screenName.asText());
    			entry.setScreen_name(screenName.asText());
    		}
    		JsonNode name = user.get("name");
    		
    		if (name != null) {
    			filterList.add(name.asText());
    			entry.setRealName(name.asText());
    		}

    	}
    	
    	JsonNode retweeted = node.get("retweeted_status");
    	if (retweeted != null) {
        	JsonNode userRetweet = retweeted.get("user");
        	if (userRetweet != null) {
        		JsonNode profileImage = userRetweet.get("profile_image_url");
        		JsonNode screenName = userRetweet.get("screen_name");
        		JsonNode name = userRetweet.get("name");
        		
        		if (name != null) {
        			filterList.add(name.asText());
        			entry.setRealName(name.asText());
        		}

        		if (profileImage != null) {
        			entry.setProfile_image(profileImage.asText());
        		}
        		if (screenName != null) {
        			filterList.add(screenName.asText());
        			entry.setScreen_name(screenName.asText());
        		}
            	JsonNode retweetText = retweeted.get("text");
            	if (retweetText != null) {
            		entry.setContent(retweetText.asText());
            	}
            	
            	entry.setRetweeted(true);
    		}
    		JsonNode entities = retweeted.get("entities");
    		if (entities != null) {
    			JsonNode media = entities.get("media");
    			if (media != null) {
	    			if (media.isArray()) {
					    for (final JsonNode mediaObj : media) {
					    	if (mediaObj != null) {
			        			JsonNode mediaURL = mediaObj.get("media_url");
			        			if (mediaURL != null) {
			        				entry.setMedia_image_url(mediaURL.asText());
			        			}
					    	}
					    }
	    			}
	    			else {
	        			JsonNode mediaURL = media.get("media_url");
	        			if (mediaURL != null) {
	        				entry.setMedia_image_url(mediaURL.asText());
	        			}
	
	    			}
    			}

    			JsonNode tags = entities.get("hashtags");
    			if (tags != null) {
    				Set<String> hashtags = new TreeSet<String>();
	    			if (tags.isArray()) {
					    for (final JsonNode tagObj : tags) {
					    	if (tagObj != null) {
			        			JsonNode hash = tagObj.get("text");
			        			if (hash != null) {
			        				String tag = hash.asText().toLowerCase();
			        				if (!tag.equals("cybersecurity")) {
				        				hashtags.add(tag);
			        				}
			        			}
					    	}
					    }
	    			}
	    			entry.setHashtags(hashtags);
    			}
    		}    		
    	}
    	
    	entry.setCategory(runType);

    	CyberUtils cyberUtils = new CyberUtils();

    	entry.setNgram(cyberUtils.getTokens(entry, new TreeSet<String>(), true));
    	entry.setNgramSingle(cyberUtils.getTokens(entry, new TreeSet<String>(), false));
    	entry.setTitle(entry.getRealName());
    	entry.setTimestamp(new Date().getTime() + "");
    	entry.setUser_category("Twitter");
    	
    	String content = entry.getContent();
    	
		content = content.replaceAll("#", "");
		content = content.replaceAll("@", "");
		content = content.replaceAll("amp;", "");
		content = content.replaceAll("today", "");
		SimpleDateFormat sdf = new SimpleDateFormat("MMMM dd yyyy hh:mm aaa");
		entry.setNlp(entry.getTitle() + " writes: " + content + sdf.format(new Date()) + entry.getLocation());
		
		elastic.indexDoc(entry);
    }
    
}
