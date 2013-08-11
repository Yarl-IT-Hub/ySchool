package org.yarlithub.yschool.ySchoolSetUp.excelToMySQL;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;

public interface InputFileSteamToDatabase {

	public void writeToDataBase(FileInputStream fileInputStream) throws IOException;



}
