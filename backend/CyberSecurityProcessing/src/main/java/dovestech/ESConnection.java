package dovestech;

import java.net.InetAddress;
import java.net.UnknownHostException;

import org.elasticsearch.client.transport.TransportClient;
import org.elasticsearch.common.settings.Settings;
import org.elasticsearch.common.transport.TransportAddress;
import org.elasticsearch.transport.client.PreBuiltTransportClient;

public class ESConnection {

	public static TransportClient client = null;
	
	public TransportClient getConnection() {
		try {
			if (client == null) {
				client = new PreBuiltTransportClient(Settings.EMPTY)
			        .addTransportAddress(new TransportAddress(InetAddress.getByName(Main.config.getProperty("hostname")), 9300));
			}
			else {
				return client;
			}
		} catch (UnknownHostException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return client;

	}
}
