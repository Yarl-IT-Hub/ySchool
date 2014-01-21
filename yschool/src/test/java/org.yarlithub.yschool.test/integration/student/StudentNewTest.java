package org.yarlithub.yschool.test.integration.student;

import org.junit.Test;
import org.junit.runner.RunWith;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
import org.springframework.test.context.transaction.TransactionConfiguration;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.web.student.StudentNewBean;

import static org.junit.Assert.assertTrue;

/**
 * These tests connects with database ySchool-1.0.5  and transfer data.
 * Before Running tests make sure to import schema and initial data into the database.
 */

@ContextConfiguration(locations = {"/applicationContext.xml"})
@RunWith(SpringJUnit4ClassRunner.class)
@TransactionConfiguration(transactionManager = "transactionManager", defaultRollback = false)
public class StudentNewTest {

    @Test
    @Transactional
    public void insertStaffTest() {
        StudentNewBean studentBean=new StudentNewBean();
        studentBean.setName("wow");
        System.out.println(studentBean.getName());

    }
}
