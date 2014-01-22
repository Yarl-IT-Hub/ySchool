package org.yarlithub.yschool.integration.student;

import org.junit.Test;
import org.junit.runner.RunWith;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
import org.springframework.test.context.transaction.TransactionConfiguration;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.integration.testdata.StudentIntegrationData;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.StudentService;

import java.text.ParseException;
import java.util.Date;
import java.util.Iterator;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;

//import org.yarlithub.yschool.integration.utils.SpringJUnit4ParameterizedClassRunner;
//import org.yarlithub.yschool.integration.utils.SpringParameterizedRunner;

/**
 * Created with IntelliJ IDEA.
 * User: JayKrish
 * Date: 1/20/14
 * Time: 11:50 AM
 * To change this template use File | Settings | File Templates.
 */

/**
 * These tests connects with ySchool database  and transfer data.
 * Before Running tests make sure to import schema and initial data into the database.
 */

@ContextConfiguration(locations = {"/applicationContext.xml"})
@RunWith(SpringJUnit4ClassRunner.class)
@TransactionConfiguration(transactionManager = "transactionManager", defaultRollback = true)
@Transactional
public class StudentServiceTest {

    private StudentService studentService;


//    //@Parameterized.Parameters
//    public static Collection<Object[]> parameters() throws ParseException {
//        return StudentIntegrationData.parameters();
//    }

    //@Before
    public void setUp() {
        studentService = new StudentService();
    }

    //@After
    public void tearDown() {
        studentService = null;
    }

    @Test
    @Transactional
    public void run() throws ParseException {

        Iterator parameterIterator = StudentIntegrationData.newStudentData.iterator();
        while (parameterIterator.hasNext()) {
            Object[] parameterList = (Object[]) parameterIterator.next();
            setUp();
            addNewStudentTest((String) parameterList[0], (String) parameterList[1],
                    (String) parameterList[2], (String) parameterList[3], (Date) parameterList[4],
                    (String) parameterList[5], (String) parameterList[6]);
            tearDown();
        }
    }

    @Transactional
    public void addNewStudentTest(String addmision_No, String name, String fullname, String name_wt_initial,
                                     Date dob, String gender, String address) {
        Student student= studentService.addNewStudent(addmision_No, name, fullname, name_wt_initial, dob, gender, address);
        assertTrue("error!",student.getId()>0);
    }
}
