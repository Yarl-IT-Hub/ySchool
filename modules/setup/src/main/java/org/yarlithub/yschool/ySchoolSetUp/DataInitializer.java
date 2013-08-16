package org.yarlithub.yschool.ySchoolSetUp;

import org.apache.myfaces.custom.fileupload.UploadedFile;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.yarlithub.yschool.Reader.Reader;
import org.yarlithub.yschool.Reader.ReaderFactory;
import org.yarlithub.yschool.ySchoolSetUp.Loader.SubjectLoader;

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
     * @param uploadedFile of the yschool initialization document in users local macnine
     * @return True or False according to success or failure of processing and entering the initial data
     */


    public boolean initializeySchoolData(UploadedFile uploadedFile) throws IOException {

        ReaderFactory readerFactory = new ReaderFactory();
        Reader reader = readerFactory.getspreadSheetReader(uploadedFile);

        SubjectLoader subjectLoader = new SubjectLoader();
        subjectLoader.load(reader);


        return true;
    }
}
