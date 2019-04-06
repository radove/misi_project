package dovestech;

import java.io.File;
import java.io.StringReader;
import java.util.ArrayList;
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

import com.google.common.collect.Lists;

public class CyberUtils {
	private static Set<String> stopwords = new HashSet<String>();

	public List<String> getTokens(BasicEntry entry, Set<String> filterList, boolean twoWords) throws Exception {
    	
    	StringBuilder filteredString = new StringBuilder();
		StringTokenizer st = new StringTokenizer(entry.getContent());
		while (st.hasMoreElements()) {
			String term = (String) st.nextElement();
			if (!term.startsWith("@") && !term.startsWith("http") && !containsDigit(term) && !filterList.contains(term)) {
				filteredString.append(term + " ");
			}
		}
    	
    	AttributeFactory factory = AttributeFactory.DEFAULT_ATTRIBUTE_FACTORY;
    	//  Lucene 5.x
    	StandardTokenizer tokenizer = new StandardTokenizer(factory);
    	tokenizer.setReader(new StringReader(filteredString.toString()));
    	tokenizer.reset();

    	// Then process tokens - same between 4.x and 5.x
    	// NOTE: Here I'm adding a single expected attribute to handle string tokens,
    	//  but you would probably want to do something more meaningful/elegant
    	CharTermAttribute attr = tokenizer.addAttribute(CharTermAttribute.class);

    	List<String> tokens = new ArrayList<String>();
    	LinkedHashSet<String> trainTokens = new LinkedHashSet<String>();

	    String lastTerm = "";
    	while(tokenizer.incrementToken()) {
    		
    	    // Grab the term
    	    String term = attr.toString();
    	    term = term.toLowerCase();
    	    trainTokens.add(term);
    		if (!isStopWord(term) && term.length() > 3) {
    	    	tokens.add(term);
    	    	if (twoWords) {
	    	    	if (lastTerm.isEmpty()) {
	    	    		lastTerm = term;
	    	    	}
	    	    	else {
	    	    		tokens.add(lastTerm + " " + term);
	    	    		lastTerm = term;
	    	    	}
    	    	}
    		}
    	}
    	
    	tokenizer.close();
    	
    	return tokens;
	}
	

    public final boolean containsDigit(String s) {
        boolean containsDigit = false;

        if (s != null && !s.isEmpty()) {
            for (char c : s.toCharArray()) {
                if (containsDigit = Character.isDigit(c)) {
                    break;
                }
            }
        }

        return containsDigit;
    }
    
    private boolean isStopWord(String term) {
    	try {
	        CharArraySet luceneStopWords = EnglishAnalyzer.getDefaultStopSet();
	    	if (stopwords.isEmpty()) {
	    		File file = new File("stopwords");
				try (Scanner scanner = new Scanner(file)) {
				    while (scanner.hasNextLine()) {
				        String line = scanner.nextLine();
				        stopwords.add(line);
				    }
				}
		        System.out.println("Loaded Stop Words");
			}
	    	List<String> terms = Lists.newArrayList("cyber", "security", "cybersecurity");

			if (stopwords.contains(term.toLowerCase()) || luceneStopWords.contains(term.toLowerCase()) || terms.contains(term.toLowerCase())) {
				return true;
			}
			else {
				return false;
			}
    	}
    	catch (Exception e) {
    		System.out.println("Failed to load Stop Words" + e);
			return false;
    	}
    }
}
