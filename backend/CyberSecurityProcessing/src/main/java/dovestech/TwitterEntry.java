package dovestech;

import java.util.Set;
import java.util.TreeSet;

public class TwitterEntry extends BasicEntry {

	private String data_type = "twitter";
	private String country;
	private String country_code;
	private String location;
	private String screen_name;
	private String realName;
	private String geo;
	private String md5;
	private String profile_image;
	private String media_image_url;
	private String category;
	private boolean retweeted = false;
	private Set<String> hashtags = new TreeSet<String>();
	
	public String getCountry() {
		return country;
	}
	public void setCountry(String country) {
		this.country = country;
	}
	public String getCountry_code() {
		return country_code;
	}
	public void setCountry_code(String country_code) {
		this.country_code = country_code;
	}
	public String getScreen_name() {
		return screen_name;
	}
	public void setScreen_name(String screen_name) {
		this.screen_name = screen_name;
	}
	public String getLocation() {
		return location;
	}
	public void setLocation(String location) {
		this.location = location;
	}
	public String getProfile_image() {
		return profile_image;
	}
	public void setProfile_image(String profile_image) {
		this.profile_image = profile_image;
	}
	public String getCategory() {
		return category;
	}
	public void setCategory(String category) {
		this.category = category;
	}
	public boolean isRetweeted() {
		return retweeted;
	}
	public void setRetweeted(boolean retweeted) {
		this.retweeted = retweeted;
	}
	public String getRealName() {
		return realName;
	}
	public void setRealName(String realName) {
		this.realName = realName;
	}
	public String getMedia_image_url() {
		return media_image_url;
	}
	public void setMedia_image_url(String media_image_url) {
		this.media_image_url = media_image_url;
	}
	public String getGeo() {
		return geo;
	}
	public void setGeo(String geo) {
		this.geo = geo;
	}
	public String getMd5() {
		return md5;
	}
	public void setMd5(String md5) {
		this.md5 = md5;
	}
	public Set<String> getHashtags() {
		return hashtags;
	}
	public void setHashtags(Set<String> hashtags) {
		this.hashtags = hashtags;
	}
	public String getData_type() {
		return data_type;
	}
}
