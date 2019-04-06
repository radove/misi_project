package dovestech;

import java.util.Calendar;
import java.util.Date;
import java.util.HashSet;
import java.util.Set;

import org.elasticsearch.action.bulk.BulkRequestBuilder;
import org.elasticsearch.action.bulk.BulkResponse;
import org.elasticsearch.action.search.SearchResponse;
import org.elasticsearch.action.search.SearchType;
import org.elasticsearch.client.transport.TransportClient;
import org.elasticsearch.common.xcontent.XContentType;
import org.elasticsearch.index.query.BoolQueryBuilder;
import org.elasticsearch.index.query.QueryBuilders;
import org.elasticsearch.index.reindex.BulkByScrollResponse;
import org.elasticsearch.index.reindex.DeleteByQueryAction;
import org.elasticsearch.search.SearchHit;
import org.elasticsearch.search.SearchHits;

import com.fasterxml.jackson.databind.JsonNode;
import com.fasterxml.jackson.databind.ObjectMapper;

import dovestech.ESConnection;

public class ElasticProcess {
	private static ObjectMapper mapper = new ObjectMapper();
	private ESConnection connection = new ESConnection();
	
	public void indexDoc(BasicEntry entry) throws Exception {
    	ObjectMapper mapper = new ObjectMapper();
    	String jsonInString = mapper.writeValueAsString(entry);
    	TransportClient client = connection.getConnection();
		
		BulkRequestBuilder bulkRequest = client.prepareBulk();
		bulkRequest.add(client.prepareIndex(Main.config.getProperty("index"), "data", entry.getId()).setSource(jsonInString, XContentType.JSON).setPipeline("opennlp-pipeline"));

		BulkResponse bulkResponse = bulkRequest.get();
		if (bulkResponse.hasFailures()) {
			Main.log("Failed to Index Entry: " + jsonInString);
		}
	}
	public Set<String> getURLs() throws Exception {
		Set<String> data = new HashSet<String>();
		
		ESConnection connection = new ESConnection();
		TransportClient client = connection.getConnection();
		
		SearchResponse response = client.prepareSearch("url")
		        .setSearchType(SearchType.DFS_QUERY_THEN_FETCH)
		        .get();
		
        SearchHit[] results = response.getHits().getHits();
        for(SearchHit hit : results) {
            String json = hit.getSourceAsString();
        	JsonNode node = mapper.readTree(json);
        	if (node != null) {
    			JsonNode urlNode = node.get("url");
    			JsonNode catNode = node.get("user_category");
    			if (urlNode != null && catNode != null) {
    	        	data.add(urlNode.asText() + "|" + catNode.asText());
    			}
    		}
    	}
		return data;
	}

	
	public long clean() throws Exception {
		ESConnection connection = new ESConnection();

		TransportClient client = connection.getConnection();
		
		BoolQueryBuilder boolQuery = QueryBuilders.boolQuery();
		boolQuery.filter(QueryBuilders.rangeQuery("timestamp").lte(getDate(-7)));
		boolQuery.must(QueryBuilders.termQuery("data_type", "twitter"));

		BulkByScrollResponse response =
			    DeleteByQueryAction.INSTANCE.newRequestBuilder(client)
			        .filter(boolQuery) 
			        .source(Main.config.getProperty("index"))                                  
			        .get();                                             

		long deleted = response.getDeleted();
			
		return deleted;
	}
	
	private String getDate(int d) {
		Calendar cal = Calendar.getInstance();
		cal.setTime(new Date());
		cal.add(Calendar.DATE, d);
		Date newDate = cal.getTime();
		return newDate.getTime() + "";
	}


}
