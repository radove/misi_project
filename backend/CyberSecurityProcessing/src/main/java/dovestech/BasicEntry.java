package dovestech;

import java.util.ArrayList;
import java.util.List;
import java.util.Set;
import java.util.TreeSet;

public class BasicEntry {

	private String category;
	private String content;
	private String nlp;
	private String id;
	private String timestamp;
	private String title;
	private List<String> ngram = new ArrayList<String>();
	private List<String> ngramSingle = new ArrayList<String>();
	private String user_category;
	
	public String getCategory() {
		return category;
	}
	public void setCategory(String category) {
		this.category = category;
	}
	public String getContent() {
		return content;
	}
	public void setContent(String content) {
		this.content = content;
	}
	public String getNlp() {
		return nlp;
	}
	public void setNlp(String nlp) {
		this.nlp = nlp;
	}
	public String getId() {
		return id;
	}
	public void setId(String id) {
		this.id = id;
	}
	public String getTimestamp() {
		return timestamp;
	}
	public void setTimestamp(String timestamp) {
		this.timestamp = timestamp;
	}
	public List<String> getNgram() {
		return ngram;
	}
	public void setNgram(List<String> ngram) {
		this.ngram = ngram;
	}
	public String getTitle() {
		return title;
	}
	public void setTitle(String title) {
		this.title = title;
	}
	public List<String> getNgramSingle() {
		return ngramSingle;
	}
	public void setNgramSingle(List<String> ngramSingle) {
		this.ngramSingle = ngramSingle;
	}
	public String getUser_category() {
		return user_category;
	}
	public void setUser_category(String user_category) {
		this.user_category = user_category;
	}
	
}
