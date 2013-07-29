package org.yarlithub.yschool.service;


import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.factories.yschoolLite.HibernateYschoolLiteDaoFactory;
import org.yarlithub.yschool.factories.yschoolLite.YschoolLiteDataPoolFactory;
import org.yarlithub.yschool.model.dao.yschoolLite.UserDao;
import org.yarlithub.yschool.model.obj.yschoolLite.User;
import org.yarlithub.yschool.services.data.DataLayerYschoolLite;
import org.yarlithub.yschool.services.data.DataLayerYschoolLiteImpl;

@Service(value = "setupService")
public class SetupService {
    private static final Logger logger = LoggerFactory.getLogger(SetupService.class);

    @Transactional
    public void createSetup(String username, String password)  {
        logger.debug("Starting to create a setup {}, {}", username, password);

        DataLayerYschoolLite dataLayerYschoolLite = DataLayerYschoolLiteImpl.getInstance();
        UserDao userDao = HibernateYschoolLiteDaoFactory.getUserDao();

        User user = YschoolLiteDataPoolFactory.getUser();

        user.setEmail(username + "@gmail.com");
        user.setUserName(username);
        user.setPassword(password);
        user.setUserRole((byte) 1);

        userDao.save(user);
        dataLayerYschoolLite.flushSession();
        logger.debug("Successfuly created a setup {}, {}", username, password);
    }
}
