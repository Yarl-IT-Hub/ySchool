package org.yarlithub.yschool.service;

import org.apache.myfaces.custom.fileupload.UploadedFile;
import org.hibernate.Hibernate;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.analytics.datasync.SyncExamination;
import org.yarlithub.yschool.commons.api.CommonsHelper;
import org.yarlithub.yschool.examination.api.ExaminationCreator;
import org.yarlithub.yschool.examination.api.ExaminationHelper;
import org.yarlithub.yschool.examination.api.ExaminationLoader;
import org.yarlithub.yschool.module.api.ModuleHelper;
import org.yarlithub.yschool.repository.model.obj.yschool.*;

import java.io.IOException;
import java.util.Date;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 9/22/13
 * Time: 9:05 AM
 * To change this template use File | Settings | File Templates.
 */

@Service(value = "examinationService")
public class ExaminationService {
    private static final Logger LOGGER = LoggerFactory.getLogger(ExaminationService.class);

    @Transactional
    public Exam addCAExam(Date date, int term, int examType, int gradeid, int divisionid, int moduleid) {
        LOGGER.debug("Adding new CA Exam for :" + gradeid + divisionid);
        return ExaminationCreator.addNewCAExam(date, term, examType, gradeid, divisionid, moduleid);
    }

    @Transactional
    public List<Exam> addTermExam(Date date, int term, int examType, int gradeid, int moduleid) {
        return ExaminationCreator.addNewTermExam(date, term, examType, gradeid, moduleid);
    }

    @Transactional
    public List<Exam> getLatestExams(int start, int max) {
        List<Exam> exams = ExaminationHelper.getLatestExams(start, max);
        //Hibernate needs lazy initialization of internal objects
        for (Exam exam : exams) {
            Hibernate.initialize(exam.getClassroomModuleIdclassroomModule().getClassroomIdclassroom());
            Hibernate.initialize(exam.getClassroomModuleIdclassroomModule().getClassroomIdclassroom().getGradeIdgrade());
            Hibernate.initialize(exam.getClassroomModuleIdclassroomModule().getClassroomIdclassroom().getDivisionIddivision());
            Hibernate.initialize(exam.getClassroomModuleIdclassroomModule());
            Hibernate.initialize(exam.getClassroomModuleIdclassroomModule().getModuleIdmodule());
            Hibernate.initialize(exam.getClassroomModuleIdclassroomModule().getModuleIdmodule().getSubjectIdsubject());
            Hibernate.initialize(exam.getExamTypeIdexamType());
        }
        return exams;
    }

    @Transactional
    public List<Marks> getExamMarks(Integer examId) {
        List<Marks> marksList = ExaminationHelper.getExamMarks(examId);
        for (Marks mark : marksList) {
            Hibernate.initialize(mark.getStudentIdstudent());
        }
        return marksList;
    }

    @Transactional
    public List<Results> getExamResults(Integer examId) {
        List<Results> resultsList = ExaminationHelper.getExamResults(examId);
        //Hibernate needs lazy initialization of internal objects
        for (Results results : resultsList) {
            Hibernate.initialize(results.getStudentIdstudent());
            for (StudentGeneralexamProfile studentGeneralexamProfile : results.getStudentIdstudent().getStudentGeneralexamProfiles()) {
                /*to print islandrank and zscore*/
                Hibernate.initialize(studentGeneralexamProfile);
            }
        }
        return resultsList;
    }

    //TODO:handle exceptions.
    @Transactional
    public boolean uploadMarks(UploadedFile marksFile, int examid) throws IOException {
        ExaminationLoader.loadMarks(marksFile, examid);
        SyncExamination syncExamination = new SyncExamination();
        return syncExamination.addNewSyncExam(examid);
    }

    @Transactional
    public boolean uploadResults(UploadedFile resultsFile, int examid) throws IOException {
        ExaminationLoader.loadResults(resultsFile, examid);
        SyncExamination syncExamination = new SyncExamination();
        return syncExamination.addNewSyncExam(examid);
    }

    @Transactional
    public Exam getExambyId(int examid) {
        Exam exam = ExaminationHelper.getExamById(examid);
        Hibernate.initialize(exam.getExamTypeIdexamType());
        Hibernate.initialize(exam.getClassroomModuleIdclassroomModule());
        Hibernate.initialize(exam.getClassroomModuleIdclassroomModule().getModuleIdmodule());
        Hibernate.initialize(exam.getClassroomModuleIdclassroomModule().getClassroomIdclassroom());
        Hibernate.initialize(exam.getClassroomModuleIdclassroomModule().getModuleIdmodule().getSubjectIdsubject());
        Hibernate.initialize(exam.getClassroomModuleIdclassroomModule().getClassroomIdclassroom().getGradeIdgrade());
        Hibernate.initialize(exam.getClassroomModuleIdclassroomModule().getClassroomIdclassroom().getDivisionIddivision());
        for (ExamSync examSync : exam.getExamSyncs()) {
            Hibernate.initialize(examSync);
        }
        return exam;
    }

    @Transactional
    public List<Grade> getAvailableGrades() {
        return CommonsHelper.getAllGrades();
    }

    @Transactional
    public List<Division> getAvailableDivisions() {
        return CommonsHelper.getAllDivisions();
    }

    @Transactional
    public List<Module> getAllModules() {
        List<Module> allModules = ModuleHelper.getAllModules();
        for (Module availableModule : allModules) {
            Hibernate.initialize(availableModule.getSubjectIdsubject());
        }
        return allModules;
    }

    @Transactional
    public List<Module> getModules(int gradeId) {
        List<Module> modules = ModuleHelper.getModules(gradeId);
        for (Module module : modules) {
            Hibernate.initialize(module.getSubjectIdsubject());
        }
        return modules;
    }
}
