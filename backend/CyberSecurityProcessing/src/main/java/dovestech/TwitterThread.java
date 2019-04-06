package dovestech;

import java.util.ArrayList;
import java.util.List;
import java.util.concurrent.BlockingQueue;
import java.util.concurrent.LinkedBlockingQueue;
import java.util.concurrent.TimeUnit;

import com.google.common.collect.Lists;
import com.twitter.hbc.ClientBuilder;
import com.twitter.hbc.core.Client;
import com.twitter.hbc.core.Constants;
import com.twitter.hbc.core.Hosts;
import com.twitter.hbc.core.HttpHosts;
import com.twitter.hbc.core.Constants.FilterLevel;
import com.twitter.hbc.core.endpoint.Location;
import com.twitter.hbc.core.endpoint.Location.Coordinate;
import com.twitter.hbc.core.endpoint.StatusesFilterEndpoint;
import com.twitter.hbc.core.event.Event;
import com.twitter.hbc.core.processor.StringDelimitedProcessor;
import com.twitter.hbc.httpclient.auth.Authentication;
import com.twitter.hbc.httpclient.auth.OAuth1;

public class TwitterThread extends Thread {
	
	public String runType = "";
	
	public TwitterThread(String runType) {
		this.runType = runType;
	}

    public void run() {
    	TwitterUtil twitterUtil = new TwitterUtil();
    	/** Set up your blocking queues: Be sure to size these properly based on expected TPS of your stream */
    	BlockingQueue<String> msgQueue = new LinkedBlockingQueue<String>(100000);
    	BlockingQueue<Event> eventQueue = new LinkedBlockingQueue<Event>(1000);

    	/** Declare the host you want to connect to, the endpoint, and authentication (basic auth or oauth) */
    	Hosts hosebirdHosts = new HttpHosts(Constants.STREAM_HOST);
    	StatusesFilterEndpoint hosebirdEndpoint = new StatusesFilterEndpoint();

    	List<Long> followings = Main.config.getFollowings();
    	List<String> terms = Lists.newArrayList(Main.config.getKeywords());
    	List<Location> locations = Main.config.getLocations();
    	hosebirdEndpoint.filterLevel(FilterLevel.None);
    	
    	if (runType.equals("follows")) {
    		for (Long term : followings) {
    			System.out.println("Loaded follower: " + term);
    		}
        	hosebirdEndpoint.followings(followings);
    	}
    	else if (runType.equals("keywords")) {
    		for (String term : terms) {
    			System.out.println("Loaded term: " + term);
    		}
    		hosebirdEndpoint.trackTerms(terms);
    	}
    	else if (runType.equals("locations")) {
    		for (Location location : locations) {
    			System.out.println("Loaded location: " + location.toString());
    		}
    		hosebirdEndpoint.locations(locations);
    	}
    	else {
        	return;
    	}
    	
    	Authentication hosebirdAuth = new OAuth1(Main.config.getProperty("consumerKey"), Main.config.getProperty("consumerSecret"), Main.config.getProperty("token"), Main.config.getProperty("tokenSecret"));

    	ClientBuilder builder = new ClientBuilder()
    			  .hosts(hosebirdHosts)
    			  .name("Hosebird-Client-01")                              // optional: mainly for the logs
    			  .authentication(hosebirdAuth)
    			  .endpoint(hosebirdEndpoint)
    			  .processor(new StringDelimitedProcessor(msgQueue))
    			  .eventMessageQueue(eventQueue);                          // optional: use this if you want to process client events

		Client hosebirdClient = builder.build();
		// Attempts to establish a connection.
		System.out.println("Connecting to Hosebird");
		hosebirdClient.connect();
		System.out.println("Connected to Hosebird");
		

		while (!hosebirdClient.isDone()) {
		  try {
			String msg = msgQueue.take();
			twitterUtil.processMessage(msg, runType);
		  } catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		  }
		}
		System.out.println("Disconnected From Hosebird");
    }
    
}
