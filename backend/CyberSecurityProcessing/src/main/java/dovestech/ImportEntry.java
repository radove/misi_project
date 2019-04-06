package dovestech;

public class ImportEntry extends BasicEntry {
	
	private String data_type = "import";
	private String fileName;
	private String fileType;

	public String getFileName() {
		return fileName;
	}

	public void setFileName(String fileName) {
		this.fileName = fileName;
	}

	public String getData_type() {
		return data_type;
	}

	public String getFileType() {
		return fileType;
	}

	public void setFileType(String fileType) {
		this.fileType = fileType;
	}
}
