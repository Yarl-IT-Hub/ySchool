package org.yarlithub.yschool.ySchoolSetUp;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.factories.yschoolLite.HibernateYschoolLiteDaoFactory;
import org.yarlithub.yschool.factories.yschoolLite.YschoolLiteDataPoolFactory;
import org.yarlithub.yschool.model.dao.yschoolLite.UserDao;
import org.yarlithub.yschool.model.obj.yschoolLite.User;
import org.yarlithub.yschool.services.data.DataLayerYschoolLite;
import org.yarlithub.yschool.services.data.DataLayerYschoolLiteImpl;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 8/7/13
 * Time: 11:00 PM
 * To change this template use File | Settings | File Templates.
 */
public class DataInitializer {
    private static final Logger logger = LoggerFactory.getLogger(DataInitializer.class);

    /**
     * Takes the ySchool initialization document and enter the initial data into database.
     *
     * @param initDoc Path of the yschool initialization document in users local macnine
     * @return True or False according to success or failure of processing and entering the initial data
     */

    public boolean initializeySchoolData(String initDoc) {

//        //TODO: Kana use the following sample to process your work.
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
