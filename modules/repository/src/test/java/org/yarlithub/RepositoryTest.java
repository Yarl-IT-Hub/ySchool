package org.yarlithub;

import org.hibernate.SQLQuery;
import org.junit.Test;
import org.junit.runner.RunWith;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
import org.springframework.test.context.transaction.TransactionConfiguration;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
import org.yarlithub.yschool.repository.model.obj.yschool.UserRole;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import static org.junit.Assert.assertEquals;

/**
 * Tese tests connects with database and transfer data.
 * Before Running tests make sure to import schema and initial data into the database.
 */

@ContextConfiguration(locations = {"/applicationContext.xml"})
@RunWith(SpringJUnit4ClassRunner.class)
@TransactionConfiguration(transactionManager = "transactionManager", defaultRollback = false)
public class RepositoryTest {

    @Test
    @Transactional
    public void testUserRole() {

        DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

        //create object and save into RDBMS
        UserRole userRole = YschoolDataPoolFactory.getUserRole();
        userRole.setName("transient_admin_WD#$@DTGHYFEX768IHHJWE");
        dataLayerYschool.save(userRole);
        dataLayerYschool.flushSession();

//        //TODO: hibernate query is not working now.
//        //Use hibernate query language to access RDBMS.
//        Query query = dataLayerYschool.createQuery("SELECT idUser_Role FROM user_role WHERE name = :testname");
//        query.setParameter("testname", "transient_admin_WD#$@DTGHYFEX768IHHJWE");
//        List list = query.list();
//
//        //Test the entered data.
//        assertEquals("Error in accessing UserRole table in database!", 1, list.size());

        //Use SQL query for insert operations.
        String sql = "INSERT INTO user (user_name, email, password, User_Role_idUser_Role) VALUES (:user_name, :email, :password, :User_Role_idUser_Role)";

        SQLQuery insertQuery = dataLayerYschool.createSQLQuery(sql);
        insertQuery.setParameter("user_name", "transient_testuser_WD#$@DTGHYFEX768IHHJWE");
        insertQuery.setParameter("email", "me@me.com");
        insertQuery.setParameter("password", "XXXX");
        insertQuery.setParameter("User_Role_idUser_Role", 1);
        int result1 = insertQuery.executeUpdate();

        assertEquals("Error in inserting value into User table!", 1, result1);

        //Delete the entries used in test to make the DB as it was after test.
        SQLQuery deleteQuery1 = dataLayerYschool.createSQLQuery("DELETE FROM user WHERE user_name = :testname");
        deleteQuery1.setParameter("testname", "transient_testuser_WD#$@DTGHYFEX768IHHJWE");
        deleteQuery1.executeUpdate();

        //Delete the entries used in test to make the DB as it was after test.
        SQLQuery deleteQuery2 = dataLayerYschool.createSQLQuery("DELETE FROM user_role WHERE name = :testname");
        deleteQuery2.setParameter("testname", "transient_admin_WD#$@DTGHYFEX768IHHJWE");
        int result2 = deleteQuery2.executeUpdate();

        assertEquals("Error in deleting value from UserRole table!", 1, result2);


//        User user = YschoolDataPoolFactory.getUser();
//        user.setEmail("testv1.1@gmail.com");
//        user.setUserName("newadmin");
//        user.setPassword("XXX");
//
//        //TODO: This is not working with generated POJOs or  with jUnit.
        //giving assumption violation exception.
//        UserRole userRole1 =  dataLayerYschool.getUserRole(1)   ;
//
//        user.setUserRoleIduserRole(userRole1);
//        dataLayerYschool.save(user);
//        dataLayerYschool.flushSession();
    }
}
