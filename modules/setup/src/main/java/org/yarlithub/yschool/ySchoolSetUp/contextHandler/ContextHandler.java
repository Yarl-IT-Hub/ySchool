package org.yarlithub.yschool.ySchoolSetUp.contextHandler;

import org.yarlithub.yschool.ySchoolSetUp.excelToMySQL.InputFileSteamToDatabase;

import java.io.FileInputStream;
import java.io.IOException;

public class ContextHandler {

	InputFileSteamToDatabase inputStreamToDatabase;

	public ContextHandler(InputFileSteamToDatabase excelToDatabase) {
		this.inputStreamToDatabase = excelToDatabase;
	}


	public void inputStreamToDatabase(FileInputStream fileInputStream) throws IOException {

		inputStreamToDatabase.writeToDataBase(fileInputStream);
	}


}
