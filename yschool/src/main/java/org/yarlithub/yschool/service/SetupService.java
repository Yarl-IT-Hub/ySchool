package org.yarlithub.yschool.service;


import org.apache.commons.io.FilenameUtils;
import org.apache.myfaces.custom.fileupload.UploadedFile;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.schoolSetUp.SchoolInitializer;
import org.yarlithub.yschool.userSetUP.UserIntializer;
import org.yarlithub.yschool.ySchoolSetUp.DataInitializer;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;

/**
 * TODO description
 */
@Service(value = "setupService")
public class SetupService {
    private static final Logger logger = LoggerFactory.getLogger(SetupService.class);

    /**
     * TODO description
     *
     * @param userName
     * @param password
     * @param schoolName
     * @param schoolAddress
     * @param schoolZone
     * @param schoolDistrict
     * @param schoolProvience
     * @param initFile
     * @return
     */
    @Transactional
    public boolean ySchoolSetUP(String userName, String usereMail, String password, String schoolName, String schoolAddress,
                                String schoolZone, String schoolDistrict, String schoolProvience, UploadedFile initFile) {
        logger.debug("Starting to create a setup {}, {}", userName, password);

        /**
         *  TODO description
         */
        UserIntializer userInitializer = new UserIntializer();
        //TODO password encryption in service layer
        boolean isUserInit = userInitializer.initializeySchoolUser(userName, usereMail, password, 1);
        /**
         *  TODO description
         */
        SchoolInitializer schoolInitializer = new SchoolInitializer();
        boolean isSchoolInit = schoolInitializer.initializeSchool(schoolName, schoolAddress, schoolZone, schoolDistrict, schoolProvience);
        /**
         *   TODO description
         */


        FileInputStream initFileInputStream = null;
        try {
            String initFileName = FilenameUtils.getName(initFile.getName());
            File dataFile = new File(initFileName);
            FileOutputStream fos = new FileOutputStream(dataFile);
            fos.write(initFile.getBytes());
            fos.flush();
            fos.close();
            FileInputStream fileInputStream=new FileInputStream(dataFile.getAbsolutePath());

            DataInitializer spreadSheetToDB = new DataInitializer();
            boolean isDataInit = spreadSheetToDB.initializeySchoolData(fileInputStream, initFileName);
        } catch (IOException e) {
            logger.debug("Sent to setup module Error");
        }

        logger.debug("Successfuly created a setup {}", userName);
        //TODO check success/failure
        return true;
    }

    /**
     * TODO Description
     *
     * @param userName
     * @param password
     */
    @Transactional
    public void logIn(String userName, String password) {
        //TODO authentication

    }
}
