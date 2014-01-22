package org.yarlithub.yschool.test.integration.examination;

import org.junit.Test;
import org.junit.runner.RunWith;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
import org.springframework.test.context.transaction.TransactionConfiguration;
import org.springframework.transaction.annotation.Transactional;

import static org.junit.Assert.assertTrue;

/**
 * Created with IntelliJ IDEA.
 * User: JayKrish
 * Date: 1/20/14
 * Time: 11:48 AM
 * To change this template use File | Settings | File Templates.
 */

/**
 * These tests connects with ySchool database  and transfer data.
 * Before Running tests make sure to import schema and initial data into the database.
 */

@ContextConfiguration(locations = {"/applicationContext.xml"})
@RunWith(SpringJUnit4ClassRunner.class)
@TransactionConfiguration(transactionManager = "transactionManager", defaultRollback = true)
public class ExaminationNewTest {

    @Test
    @Transactional
    public void insertStaffTest() {
        assertTrue(true);

    }
}
