package org.yarlithub.yschool.integration.student;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;
import org.junit.runner.RunWith;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
import org.springframework.test.context.transaction.TransactionConfiguration;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.integration.testdata.StudentIntegrationData;
import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
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

    int a = 0;
    private StudentService studentService;

    @Before
    public void setUp() {
        studentService = new StudentService();
    }

    @After
    public void tearDown() {
        studentService = null;
    }

    @Test
    @Transactional
    public void addNewStudentTest() throws ParseException {

        Student studentSaved;
        Iterator newStudentDataIterator = StudentIntegrationData.newStudentData.iterator();
        while (newStudentDataIterator.hasNext()) {
            Object[] parameterList = (Object[]) newStudentDataIterator.next();

            studentSaved = StudentServiceTestUtils.addNewStudent(studentService, (String) parameterList[0], (String) parameterList[1],
                    (String) parameterList[2], (String) parameterList[3], (Date) parameterList[4],
                    (String) parameterList[5], (String) parameterList[6]);
            assertTrue("error!", studentSaved.getId() > 0);
        }
    }

    @Test
    @Transactional
    public void saveOrUpdateTest() {
        Student student = YschoolDataPoolFactory.getStudent();
        int previousId = 0;

        Iterator studentUpdateDataIterator = StudentIntegrationData.studentSaveOrUpdateData.iterator();
        while (studentUpdateDataIterator.hasNext()) {
            Object[] parameterList = (Object[]) studentUpdateDataIterator.next();
            if (previousId != 0) {
                previousId = student.getId();
            }
            student = StudentServiceTestUtils.saveOrUpdate(studentService, student, (String) parameterList[0], (String) parameterList[1],
                    (String) parameterList[2], (String) parameterList[3], (Date) parameterList[4],
                    (String) parameterList[5], (String) parameterList[6]);
            if (previousId != 0) {
               /*First time saves, then updates the same object. */
                assertTrue("not same object!", previousId == student.getId());
            }
            String updatedName = (String) parameterList[7];
            assertEquals("name not updated!", updatedName, student.getName());
        }
    }

}
