package org.yarlithub.yschool.service;

import org.apache.myfaces.custom.fileupload.UploadedFile;
import org.hibernate.Hibernate;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.analytics.datasync.SyncExamination;
import org.yarlithub.yschool.examination.core.ExaminationHelper;
import org.yarlithub.yschool.examination.core.ExaminationCreator;
import org.yarlithub.yschool.examination.core.ExaminationLoader;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.repository.model.obj.yschool.Marks;
import org.yarlithub.yschool.repository.model.obj.yschool.Results;

import java.io.IOException;
import java.util.Date;
import java.util.Iterator;
import java.util.List;
/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 9/22/13
 * Time: 9:05 AM
 * To change this template use File | Settings | File Templates.
 */

/**
 * TODO description
 */
@Service(value = "examinationService")
public class ExaminationService {
    private static final Logger logger = LoggerFactory.getLogger(ExaminationService.class);

    /**
     *
     * @param date      java.util.date
     * @param term      int term 1 or 2 or 3
     * @param examType  int id as in Exam_Type table in yschool database version1.2
     * @param grade     int grade
     * @param division  Char like A/B/C/D/E/F
     * @param subjectid int id as in Subject table
     * @return exam if successfully created a CA exam and inserted entries into related database tables,
     *  otherwise null.
     */
    @Transactional
    public Exam addCAExam(Date date, int term, int examType, int grade, String division, int subjectid) {
        ExaminationCreator examinationCreator = new ExaminationCreator();
        Exam exam = examinationCreator.addNewCAExam(date, term, examType, grade, division, subjectid);
        return exam;
    }

    /**
     * @param date      java.util.date
     * @param term      int term 1 or 2 or 3
     * @param examType  int id as in Exam_Type table in yschool database version1.2
     * @param grade     int grade
     * @param subjectid int id as in Subject table
     * @return for each divisions of classroom, checks if the subject is provided and add a term exam entry per class division
     * and if successful and inserted entries into related database tables return list of exams,
     * otherwise null.
     */
    @Transactional
    public List<Exam> addTermExam(Date date, int term, int examType, int grade, int subjectid) {
        ExaminationCreator examinationCreator = new ExaminationCreator();
        List<Exam> examList = examinationCreator.addNewTermExam(date, term, examType, grade, subjectid);
        return examList;
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
        List<Marks> marksList = examinationHelper.getExamMarks(examid);
        //Hibernate needs lazy initialization of internal objects
        Iterator<Marks> marksIterator = marksList.iterator();
        while (marksIterator.hasNext()) {
            Marks mark = marksIterator.next();
            Hibernate.initialize(mark.getStudentIdstudent());
        }
        return marksList;
    }

    @Transactional
    public List<Results> getExamResults(Integer examid) {
        ExaminationHelper examinationHelper = new ExaminationHelper();
        List<Results> resultsList = examinationHelper.getExamResults(examid);
        //Hibernate needs lazy initialization of internal objects
        Iterator<Results> resultsIterator = resultsList.iterator();
        while (resultsIterator.hasNext()) {
            Results results = resultsIterator.next();
            Hibernate.initialize(results.getStudentIdstudent());
        }
        return resultsList;
    }

    @Transactional
    public void uploadMarks(UploadedFile marksFile, int examid) throws IOException {
        ExaminationLoader examinationLoader = new ExaminationLoader();
        examinationLoader.loadMarks(marksFile, examid);
        SyncExamination syncExamination = new SyncExamination();
        boolean success = syncExamination.addNewSyncExam(examid);
    }

    @Transactional
    public void uploadResults(UploadedFile resultsFile, int examid) throws IOException {
        ExaminationLoader examinationLoader = new ExaminationLoader();
        examinationLoader.loadResults(resultsFile, examid);
        SyncExamination syncExamination = new SyncExamination();
        boolean success = syncExamination.addNewSyncExam(examid);
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
