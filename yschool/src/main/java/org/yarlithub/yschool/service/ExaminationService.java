package org.yarlithub.yschool.service;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.examination.core.NewExamination;

import java.util.Date;

/**
 * TODO description
 */
@Service(value = "examinationService")
public class ExaminationService {
    private static final Logger logger = LoggerFactory.getLogger(ExaminationService.class);

    @Transactional
    public boolean addCAExam(Date date, int term, int examType, int grade, String division, int subjectid) {
        NewExamination newExamination = new NewExamination();
        boolean success = newExamination.addCAExam(date, term, examType, grade, division, subjectid);
        return success;
    }

    @Transactional
    public boolean addTermExam(Date date, int term, int examType, int grade, int subjectid) {
        NewExamination newExamination = new NewExamination();
        boolean success = newExamination.addTermExam(date, term, examType, grade, subjectid);
        return success;
    }
}
