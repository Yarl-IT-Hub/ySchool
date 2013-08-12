package org.yarlithub.yschool.ySchoolSetUp;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.yarlithub.yschool.ySchoolSetUp.contextHandler.ContextHandler;
import org.yarlithub.yschool.ySchoolSetUp.inputStreamToDB.ExtensionXLS;
import org.yarlithub.yschool.ySchoolSetUp.inputStreamToDB.ExtensionXLSX;

import java.io.FileInputStream;
import java.io.IOException;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 8/7/13
 * Time: 11:00 PM
 * To change this template use File | Settings | File Templates.
 */
public class DataInitializer {
    private static final Logger logger = LoggerFactory.getLogger(DataInitializer.class);



    private static byte[] excelBytes;


    /**
     * Takes the ySchool initialization document and enter the initial data into database.
     *
     * @param fileInputStream of the yschool initialization document in users local macnine
     * @return True or False according to success or failure of processing and entering the initial data
     */



    public boolean initializeySchoolData(FileInputStream fileInputStream,String fileName)  {

       String extension= getExtension(fileName);
        writeToDbViaContextHandler(extension,fileInputStream);                               //instantiates correct input reader, writer according to the extension

        logger.debug("Successfully created a setup {}, {}", "controller", "controller");
        return true;
    }

    private void writeToDbViaContextHandler(String extension, FileInputStream fileInputStream) {
        if(extension.contentEquals("xls")){
           ContextHandler contextHandler = new ContextHandler(new ExtensionXLS());
            try {
                contextHandler.inputStreamToDatabase(fileInputStream);
            } catch (IOException e) {
                e.printStackTrace();  //To change body of catch statement use File | Settings | File Templates.
            }
        }
         else  if(extension.contentEquals("xlsx")){
            ContextHandler contextHandler = new ContextHandler(new ExtensionXLSX());
            try {
                contextHandler.inputStreamToDatabase(fileInputStream);
            } catch (IOException e) {
                e.printStackTrace();  //To change body of catch statement use File | Settings | File Templates.
            }
        }
         else {
            logger.error("File Extension is not supported!");
        }
    }


    private String getExtension(String fileName) {
        String extension = null;
        try {
            int i = fileName.lastIndexOf('.');
            if (i > 0 && i < fileName.length() - 1) {
                extension = fileName.substring(i + 1).toLowerCase();
            }

        } catch (NullPointerException ex) {
            System.out.println("The file name is null! " + ex);
        }

        finally {
            return extension;
        }
    }
}
