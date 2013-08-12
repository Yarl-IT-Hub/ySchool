package org.yarlithub.yschool.ySchoolSetUp.inputStreamToDB;

import java.io.FileInputStream;
import java.io.IOException;

public interface InputFileStreamToDatabase {

	public void writeToDataBase(FileInputStream fileInputStream) throws IOException;



}
