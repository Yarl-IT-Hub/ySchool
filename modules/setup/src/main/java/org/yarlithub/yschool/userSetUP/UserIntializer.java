package org.yarlithub.yschool.userSetUP;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.yarlithub.yschool.factories.yschoolLite.HibernateYschoolLiteDaoFactory;
import org.yarlithub.yschool.factories.yschoolLite.YschoolLiteDataPoolFactory;
import org.yarlithub.yschool.model.dao.yschoolLite.UserDao;
import org.yarlithub.yschool.model.obj.yschoolLite.User;
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
        UserDao userDao = HibernateYschoolLiteDaoFactory.getUserDao();
        User user = YschoolLiteDataPoolFactory.getUser();

        user.setEmail(usereMail);
        user.setUserName(userName);
        user.setPassword(password);
        user.setUserRole((byte) 1);

        userDao.save(user);
        dataLayerYschoolLite.flushSession();

        logger.debug("Successfuly created user {}", userName);
        //TODO check success/failure
        return true;
    }
}
