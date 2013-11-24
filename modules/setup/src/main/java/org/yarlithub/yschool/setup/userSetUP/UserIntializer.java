package org.yarlithub.yschool.setup.userSetUP;

import org.hibernate.SQLQuery;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;
import org.yarlithub.yschool.setup.dataAccess.SetUpDBQueries;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 8/8/13
 * Time: 4:49 PM
 * To change this template use File | Settings | File Templates.
 */
public class UserIntializer {
    private static final Logger logger = LoggerFactory.getLogger(UserIntializer.class);

    public UserIntializer() {

    }

    /**
     * Initilize user by entering data into user table of the yschool database.
     *
     * @param userName Name field from setup page
     * @param password Password field from setup page.
     * @param userRole use 1 for admin for now.
     * @return true or false relative to successful or failure entry of user data.
     */

    public boolean initializeySchoolUser(String userName, String usereMail, String password, int userRole) {

        logger.debug("Creating user {}", userName);

        DataLayerYschool DataLayerYschool = DataLayerYschoolImpl.getInstance();
//        User user = YschoolDataPoolFactory.getUser();
//
//        UserRole userRoleOBJ =DataLayerYschoolImpl.getInstance().getUserRole(1);
//
//        user.setEmail(usereMail);
//        user.setUserName(userName);
//        user.setPassword(password);
//        user.setUserRoleIduserRole(userRoleOBJ);
//
//        DataLayerYschool.save(user);
//        DataLayerYschool.flushSession();

        SQLQuery insertQuery = DataLayerYschool.createSQLQuery(SetUpDBQueries.INSERT_USER);
        insertQuery.setParameter("user_name", userName);
        insertQuery.setParameter("email", usereMail);
        //TODO: password encryption
        insertQuery.setParameter("password", password);
        insertQuery.setParameter("User_Role_idUser_Role", userRole);
        int result1 = insertQuery.executeUpdate();

        logger.debug("Successfuly created user {}", userName);
        //TODO check success/failure
        return true;
    }
}
