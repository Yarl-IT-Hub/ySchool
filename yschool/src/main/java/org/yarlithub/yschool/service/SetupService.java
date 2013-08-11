package org.yarlithub.yschool.service;


import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.schoolSetUp.SchoolInitializer;
import org.yarlithub.yschool.userSetUP.UserIntializer;
import org.yarlithub.yschool.ySchoolSetUp.DataInitializer;

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
     * @param initDocPath
     * @return
     */
    @Transactional
    public boolean ySchoolSetUP(String userName, String usereMail, String password, String schoolName, String schoolAddress,
                                String schoolZone, String schoolDistrict, String schoolProvience, String initDocPath) {
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
        DataInitializer spreadSheetToDB = new DataInitializer();
        boolean isDataInit = spreadSheetToDB.initializeySchoolData(initDocPath);

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
