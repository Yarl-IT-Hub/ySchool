package org.yarlithub.yschool.integration.service.examination;

import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.service.ExaminationService;

import java.util.Date;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 1/22/14
 * Time: 11:04 PM
 * To change this template use File | Settings | File Templates.
 */
public class ExaminationServiceTestUtils {

    public static Exam addNewCAExam(ExaminationService examinationService, Date date, int term, int examType,
                                    int gradeid, int divisionid, int moduleid) {
        Exam exam = examinationService.addCAExam(date, term, examType, gradeid, divisionid, moduleid);
        return exam;
    }

    public static List<Exam> addNewTermExam(ExaminationService examinationService, Date date, int term, int examType,
                                            int gradeid, int moduleid) {
        List<Exam> examList = examinationService.addTermExam(date, term, examType, gradeid, moduleid);
        return examList;

    }
}
