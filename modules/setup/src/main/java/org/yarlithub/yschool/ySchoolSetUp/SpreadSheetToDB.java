package org.yarlithub.yschool.ySchoolSetUp;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.yarlithub.yschool.services.data.DataLayerYschoolLite;
import org.yarlithub.yschool.services.data.DataLayerYschoolLiteImpl;
import org.yarlithub.yschool.factories.yschoolLite.HibernateYschoolLiteDaoFactory;
import org.yarlithub.yschool.factories.yschoolLite.YschoolLiteDataPoolFactory;
import org.yarlithub.yschool.model.dao.yschoolLite.UserDao;
import org.yarlithub.yschool.model.obj.yschoolLite.User;
/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 8/7/13
 * Time: 11:00 PM
 * To change this template use File | Settings | File Templates.
 */
public class SpreadSheetToDB {
    private static final Logger logger = LoggerFactory.getLogger(SpreadSheetToDB.class);
    public boolean initializeySchool(String initDoc){

//
//        DataLayerYschoolLite dataLayerYschoolLite = DataLayerYschoolLiteImpl.getInstance();
//        UserDao userDao = HibernateYschoolLiteDaoFactory.getUserDao();
//
//        User user = YschoolLiteDataPoolFactory.getUser();
//
//        user.setEmail("controller@gmail.com");
//        user.setUserName("controller");
//        user.setPassword("controller");
//        user.setUserRole((byte) 1);
//
//        userDao.save(user);
//        dataLayerYschoolLite.flushSession();
        logger.debug("Successfuly created a setup {}, {}", "controller", "controller");
             return true;
    }
}
