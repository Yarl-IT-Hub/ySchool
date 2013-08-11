package org.yarlithub.yschool.ySchoolSetUp.contextHandler;

import org.yarlithub.yschool.ySchoolSetUp.excelToMySQL.InputFileStreamToDatabase;

import java.io.FileInputStream;
import java.io.IOException;

public class ContextHandler {

	InputFileStreamToDatabase inputStreamToDatabase;

	public ContextHandler(InputFileStreamToDatabase excelToDatabase) {
		this.inputStreamToDatabase = excelToDatabase;
	}


	public void inputStreamToDatabase(FileInputStream fileInputStream) throws IOException {

		inputStreamToDatabase.writeToDataBase(fileInputStream);
	}


}
