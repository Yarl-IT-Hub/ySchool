package org.yarlithub.yschool.userSetUP;

import org.hibernate.SQLQuery;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.yarlithub.yschool.factories.yschoolLite.HibernateYschoolLiteDaoFactory;
import org.yarlithub.yschool.factories.yschoolLite.YschoolLiteDataPoolFactory;
import org.yarlithub.yschool.model.dao.yschoolLite.UserDao;
import org.yarlithub.yschool.model.obj.yschoolLite.User;
import org.yarlithub.yschool.model.obj.yschoolLite.UserRole;
import org.yarlithub.yschool.services.data.DataLayerYschoolLite;
import org.yarlithub.yschool.services.data.DataLayerYschoolLiteImpl;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
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

        DataLayerYschoolLite dataLayerYschoolLite = DataLayerYschoolLiteImpl.getInstance();
//        User user = YschoolLiteDataPoolFactory.getUser();
//
//        UserRole userRoleOBJ =DataLayerYschoolLiteImpl.getInstance().getUserRole(1);
//
//        user.setEmail(usereMail);
//        user.setUserName(userName);
//        user.setPassword(password);
//        user.setUserRoleIduserRole(userRoleOBJ);
//
//        dataLayerYschoolLite.save(user);
//        dataLayerYschoolLite.flushSession();

        String sql = "INSERT INTO user (user_name, email, password, User_Role_idUser_Role) VALUES (:user_name, :email, :password, :User_Role_idUser_Role)";

        SQLQuery insertQuery = dataLayerYschoolLite.createSQLQuery(sql);
        insertQuery.setParameter("user_name",userName );
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
