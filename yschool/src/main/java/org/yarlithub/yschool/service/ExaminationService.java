package org.yarlithub.yschool.service;

import org.apache.myfaces.custom.fileupload.UploadedFile;
import org.hibernate.Hibernate;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.examination.core.ExamLoader;
import org.yarlithub.yschool.examination.core.ExaminationHelper;
import org.yarlithub.yschool.examination.core.NewExamination;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.repository.model.obj.yschool.Marks;

import java.io.IOException;
import java.util.Date;
import java.util.Iterator;
import java.util.List;

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

    @Transactional
    public List<Exam> listExams(int start, int max) {
        ExaminationHelper examinationHelper = new ExaminationHelper();
        List<Exam> exams = examinationHelper.getLatestExams(start, max);

        //Hibernate needs lazy initialization of internal objects
        Iterator<Exam> iterator = exams.iterator();
        while (iterator.hasNext()) {
            Exam exam = iterator.next();
            Hibernate.initialize(exam.getClassroomSubjectIdclassroomSubject().getClassroomIdclass());
            Hibernate.initialize(exam.getClassroomSubjectIdclassroomSubject().getSubjectIdsubject());
            Hibernate.initialize(exam.getExamTypeIdexamType());
        }
        return exams;
    }

    @Transactional
    public List<Marks> getExamMarks(Integer examid) {
        ExaminationHelper examinationHelper = new ExaminationHelper();
        List<Marks> marks = examinationHelper.getExamMarks(examid);
        //Hibernate needs lazy initialization of internal objects
        Iterator<Marks> iterator = marks.iterator();
        while (iterator.hasNext()) {
            Marks mark = iterator.next();
            Hibernate.initialize(mark.getStudentIdstudent());
        }
        return marks;
    }

    @Transactional
    public void uploadMarks(UploadedFile marksFile, int examid) throws IOException {
        ExamLoader loadExamination = new ExamLoader();
        loadExamination.loadMarks(marksFile,examid);
    }

    @Transactional
    public Exam getExambyId(int examid){
     ExaminationHelper examinationHelper = new ExaminationHelper();
        Exam exam = examinationHelper.getExambyId(examid);
        Hibernate.initialize(exam.getExamTypeIdexamType());
        Hibernate.initialize(exam.getClassroomSubjectIdclassroomSubject());
        Hibernate.initialize(exam.getClassroomSubjectIdclassroomSubject().getSubjectIdsubject());
        Hibernate.initialize(exam.getClassroomSubjectIdclassroomSubject().getClassroomIdclass());

        return exam;
    }
}
