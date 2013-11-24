package org.yarlithub.yschool.setup.ySchoolSetUp;

import org.apache.myfaces.custom.fileupload.UploadedFile;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.yarlithub.yschool.spreadSheetReader.Reader;
import org.yarlithub.yschool.spreadSheetReader.ReaderFactory;
import org.yarlithub.yschool.setup.ySchoolSetUp.Loader.ClassroomLoader;
import org.yarlithub.yschool.setup.ySchoolSetUp.Loader.StaffLoader;
import org.yarlithub.yschool.setup.ySchoolSetUp.Loader.StudentLoader;
import org.yarlithub.yschool.setup.ySchoolSetUp.Loader.SubjectLoader;

import java.io.IOException;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
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
     * @param uploadedFile of the yschool initialization document from user's local machine
     * @return True or False according to success or failure of processing and entering the initial data
     */


    public boolean initializeySchoolData(UploadedFile uploadedFile) throws IOException {

        ReaderFactory readerFactory = new ReaderFactory();
        Reader initDocReader = readerFactory.getspreadSheetReader(uploadedFile);

        ClassroomLoader classroomLoader = new ClassroomLoader();
        classroomLoader.load(initDocReader);

        SubjectLoader subjectLoader = new SubjectLoader();
        subjectLoader.load(initDocReader);

        StaffLoader staffLoader = new StaffLoader();
        staffLoader.load(initDocReader);

        StudentLoader studentLoader = new StudentLoader();
        studentLoader.load(initDocReader);

        //TODO: check and return
        return true;
    }
}
