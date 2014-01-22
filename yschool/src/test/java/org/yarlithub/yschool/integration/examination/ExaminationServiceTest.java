package org.yarlithub.yschool.integration.examination;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;
import org.junit.runner.RunWith;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
import org.springframework.test.context.transaction.TransactionConfiguration;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.integration.testdata.ExaminationIntegrationData;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;
import org.yarlithub.yschool.service.ExaminationService;

import java.util.Date;
import java.util.Iterator;
import java.util.List;

import static org.junit.Assert.assertEquals;
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
public class ExaminationServiceTest {

    DataLayerYschool dataLayerYschool;
    private ExaminationService examinationService;

    @Before
    @Transactional
    public void setUp() {
        examinationService = new ExaminationService();
        dataLayerYschool = DataLayerYschoolImpl.getInstance();
    }

    @After
    public void tearDown() {
        examinationService = null;
    }

    @Test
    @Transactional
    public void addNewCAExamTest() {

        Exam examSaved;
        Iterator newCAExamDataIterator = ExaminationIntegrationData.newCAExamData.iterator();
        while (newCAExamDataIterator.hasNext()) {
            Object[] parameterList = (Object[]) newCAExamDataIterator.next();

            examSaved = ExaminationServiceTestUtils.addNewCAExam(examinationService, (Date) parameterList[0], (int) parameterList[1],
                    (int) parameterList[2], (int) parameterList[3], (int) parameterList[4],
                    (int) parameterList[5]);
            assertTrue("error!", examSaved.getId() > 0);
        }
    }

    @Test
    @Transactional
    public void addNewTermExamTest() {

        Iterator newCAExamDataIterator = ExaminationIntegrationData.newTermExamData.iterator();
        while (newCAExamDataIterator.hasNext()) {
            Object[] parameterList = (Object[]) newCAExamDataIterator.next();

            List<Exam> examSaved = ExaminationServiceTestUtils.addNewTermExam(examinationService, (Date) parameterList[0], (int) parameterList[1],
                    (int) parameterList[2], (int) parameterList[3], (int) parameterList[4]);
            assertTrue("error!", examSaved.size() > 0);
        }
    }

    @Test
    @Transactional
    public void getLatestExamsTest() {

        Exam examSaved=null;
        Iterator newCAExamDataIterator = ExaminationIntegrationData.newCAExamData.iterator();
        if (newCAExamDataIterator.hasNext()) {
            Object[] parameterList = (Object[]) newCAExamDataIterator.next();

            examSaved = ExaminationServiceTestUtils.addNewCAExam(examinationService, (Date) parameterList[0], (int) parameterList[1],
                    (int) parameterList[2], (int) parameterList[3], (int) parameterList[4],
                    (int) parameterList[5]);
            assertTrue("error!", examSaved.getId() > 0);
        }
        List<Exam> latestExams = examinationService.getLatestExams(0,1);
        assertEquals("Error in get latest exams!",examSaved.getId(),latestExams.get(0).getId());
    }


    @Test
    @Transactional
    public void getExambyIdTest() {

        Exam examSaved=null;
        Iterator newCAExamDataIterator = ExaminationIntegrationData.newCAExamData.iterator();
        while (newCAExamDataIterator.hasNext()) {
            Object[] parameterList = (Object[]) newCAExamDataIterator.next();

            examSaved = ExaminationServiceTestUtils.addNewCAExam(examinationService, (Date) parameterList[0], (int) parameterList[1],
                    (int) parameterList[2], (int) parameterList[3], (int) parameterList[4],
                    (int) parameterList[5]);
            assertTrue("error!", examSaved.getId() > 0);
        }
        Exam exam = examinationService.getExambyId(examSaved.getId());
        assertTrue("Error in get  exam by  id!",exam.equals(examSaved));
    }

}
