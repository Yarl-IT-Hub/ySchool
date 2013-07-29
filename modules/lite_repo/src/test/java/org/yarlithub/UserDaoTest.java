package org.yarlithub;

import org.junit.Test;
import org.junit.runner.RunWith;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
import org.springframework.test.context.transaction.TransactionConfiguration;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.factories.yschoolLite.HibernateYschoolLiteDaoFactory;
import org.yarlithub.yschool.factories.yschoolLite.YschoolLiteDataPoolFactory;
import org.yarlithub.yschool.model.dao.yschoolLite.UserDao;
import org.yarlithub.yschool.model.obj.yschoolLite.User;
import org.yarlithub.yschool.services.data.DataLayerYschoolLite;
import org.yarlithub.yschool.services.data.DataLayerYschoolLiteImpl;

@ContextConfiguration(locations = { "/applicationContext.xml" } )
@RunWith(SpringJUnit4ClassRunner.class)
@TransactionConfiguration(transactionManager = "transactionManager", defaultRollback = false)
public class UserDaoTest {

    @Test
    @Transactional
    public void testUserDao() {
        DataLayerYschoolLite dataLayerYschoolLite = DataLayerYschoolLiteImpl.getInstance();
        UserDao userDao = HibernateYschoolLiteDaoFactory.getUserDao();

        User user = YschoolLiteDataPoolFactory.getUser();

        user.setEmail("tom@gmail.com");
        user.setUserName("Tom");
        user.setPassword("XXX");
        user.setUserRole((byte) 1);

        userDao.save(user);
        dataLayerYschoolLite.flushSession();
    }
}
