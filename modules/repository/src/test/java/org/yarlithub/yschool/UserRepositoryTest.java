package org.yarlithub.yschool;

import org.junit.Test;
import org.junit.runner.RunWith;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
import org.springframework.test.context.transaction.TransactionConfiguration;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
import org.yarlithub.yschool.repository.model.obj.yschool.User;
import org.yarlithub.yschool.repository.model.obj.yschool.UserRole;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import static org.junit.Assert.assertTrue;

/**
 * Schema ySchool-1.0.5
 * Test Data ySchool-DATASET-009
 */

@ContextConfiguration(locations = {"/applicationContext.xml"})
@RunWith(SpringJUnit4ClassRunner.class)
@TransactionConfiguration(transactionManager = "transactionManager", defaultRollback = true)
public class UserRepositoryTest {

    @Test
    @Transactional
    public void testcreateUser() {

        DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

        //create object and save into RDBMS
        UserRole userRole = YschoolDataPoolFactory.getUserRole();
        userRole.setName("transient_admin_WD#$@DTGHYFEX768IHHJWE");
        dataLayerYschool.save(userRole);
        assertTrue("Error in creating user role", userRole.getId() > 0);

        /*should create user with passing null due to foreign key constrain on user_role*/
        User user = YschoolDataPoolFactory.getUser(null);
        user.setEmail("testv1.1@gmail.com");
        user.setUserName("newadmin");
        user.setPassword("XXX");
        user.setUserRoleIduserRole(userRole);
        dataLayerYschool.save(user);
        assertTrue("Error in creating user", user.getId() > 0);
        dataLayerYschool.flushSession();

    }
}
