package org.yarlithub.yschool.service;

import net.sf.jasperreports.engine.JRException;
import org.hibernate.Hibernate;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.analytics.core.YAnalyzer;
import org.yarlithub.yschool.analytics.datasync.SyncExamination;
import org.yarlithub.yschool.analytics.reporting.JasperReport;
import org.yarlithub.yschool.repository.model.obj.yschool.*;
import org.yarlithub.yschool.student.core.GetStudent;
import org.yarlithub.yschool.student.core.StudentHelper;

import javax.faces.model.DataModel;
import javax.servlet.ServletOutputStream;
import java.io.IOException;
import java.util.ArrayList;
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

@Service(value = "analyticsService")
public class AnalyticsService {
    private static final Logger logger = LoggerFactory.getLogger(AnalyticsService.class);

    @Transactional
    public List<ClassroomSubject> getOLSubjects(Student student) {

        YAnalyzer yAnalyzer = new YAnalyzer();
        List<ClassroomSubject> classroomSubjectList = yAnalyzer.getOLSubjects(student);

        Iterator<ClassroomSubject> classroomSubjectIterator = classroomSubjectList.iterator();
        while (classroomSubjectIterator.hasNext()) {
            ClassroomSubject classroomSubject = classroomSubjectIterator.next();
            Hibernate.initialize(classroomSubject.getSubjectIdsubject());
            // Hibernate.initialize(classroomSubject.getExams());
        }

        return classroomSubjectList;
    }

    @Transactional
    public List<ClassroomSubject> getALSubjects(Student student) {

        YAnalyzer yAnalyzer = new YAnalyzer();
        List<ClassroomSubject> classroomSubjectList = yAnalyzer.getALSubjects(student);
        if(classroomSubjectList==null){
            /*some students are unknown at AL streams yet*/
            return null;
        }
        Iterator<ClassroomSubject> classroomSubjectIterator = classroomSubjectList.iterator();
        while (classroomSubjectIterator.hasNext()) {
            ClassroomSubject classroomSubject = classroomSubjectIterator.next();
            Hibernate.initialize(classroomSubject.getSubjectIdsubject());
            // Hibernate.initialize(classroomSubject.getExams());
        }

        return classroomSubjectList;
    }

    @Transactional
    public String getALSubjectsResult(Student student, ClassroomSubject classroomSubject) {
        YAnalyzer yAnalyzer = new YAnalyzer();
        return yAnalyzer.getALSubjectsResult(student, classroomSubject);

    }

    @Transactional
    public String getOLSubjectsResult(Student student, ClassroomSubject classroomSubject) {
        YAnalyzer yAnalyzer = new YAnalyzer();
        return yAnalyzer.getOLSubjectsResult(student, classroomSubject);

    }

    @Transactional
    public Student getStudenById(int id) {
        GetStudent getStudent = new GetStudent();
        Student student = getStudent.getStudentByID(id);
        //Hibernate needs lazy initialization of internal objects
        Hibernate.initialize(student.getClassroomStudents());
        Hibernate.initialize(student.getStudentGeneralexamProfiles());
        Iterator<ClassroomStudent> classroomStudentIterator = student.getClassroomStudents().iterator();
        while (classroomStudentIterator.hasNext()) {
            ClassroomStudent classroomStudent = classroomStudentIterator.next();
            Hibernate.initialize(classroomStudent.getClassroomIdclass());
            Hibernate.initialize(classroomStudent.getStudentClassroomSubjects());
        }
        return student;
    }

    @Transactional
    public Student getStudent() {
        GetStudent student = new GetStudent();
        return student.getStudentByID(2);

    }

    @Transactional
    public void printReport(ServletOutputStream servletOutputStream) throws IOException, JRException {       // ServletOutputStream servletOutputStream
        JasperReport jasperReport = new JasperReport();
        jasperReport.printJasperReport(servletOutputStream);                        //  servletOutputStream
    }

    @Transactional
    public List<Student> getStudentByAdmissionNumber(List<Integer> admissionNo) {
        StudentHelper studentHelper = new StudentHelper();
        List<Student> studentList = new ArrayList<Student>();
        Iterator<Integer> adminNoIterator = admissionNo.iterator();
        while (adminNoIterator.hasNext()) {

            int admissionNumber = adminNoIterator.next();
            Student student = studentHelper.getStudentByAdmissionNo(admissionNumber);
                   Hibernate.initialize((StudentGeneralexamProfile) student.getStudentGeneralexamProfiles().toArray()[0]);
            studentList.add(student);
//            Hibernate.initialize(Student.class);
//            Hibernate.initialize(StudentGeneralexamProfile.class);

        }

        return studentList;
    }

    @Transactional
    public List<StudentGeneralexamProfile> getStudentGeneralExamProfileByStudentList(DataModel<Student> matchingStudentProfiles) {


        StudentHelper studentHelper = new StudentHelper();
        List<StudentGeneralexamProfile> matchingProfilesGeneralExam = new ArrayList<StudentGeneralexamProfile>();
        Iterator<Student> matchingProfilesIterator = matchingStudentProfiles.iterator();
        while (matchingProfilesIterator.hasNext()) {

            Student student = matchingProfilesIterator.next();
            StudentGeneralexamProfile studentGeneralexamProfile = studentHelper.getStudentProfileViaStudentID(student);

            matchingProfilesGeneralExam.add(studentGeneralexamProfile);
        }
        return matchingProfilesGeneralExam;


    }

    @Transactional
    public List<Exam> getNotSyncedExams() {
        SyncExamination syncExamination = new SyncExamination();
        List<Exam> examList = syncExamination.getNotSyncedExams();
        //Hibernate needs lazy initialization of internal objects
        Iterator<Exam> iterator = examList.iterator();
        while (iterator.hasNext()) {
            Exam exam = iterator.next();
            Hibernate.initialize(exam.getClassroomSubjectIdclassroomSubject().getClassroomIdclass());
            Hibernate.initialize(exam.getClassroomSubjectIdclassroomSubject().getSubjectIdsubject());
            Hibernate.initialize(exam.getExamTypeIdexamType());
        }
        return examList;

    }

    @Transactional
    public int getStudentIslandRank(Student student) {

           YAnalyzer yAnalyzer =new YAnalyzer();
        return yAnalyzer.getStudentIslandRank(student);

    }

    @Transactional
    public double getStudentzScore(Student student) {
        YAnalyzer yAnalyzer =new YAnalyzer();
        return yAnalyzer.getStudentZscore(student);

    }

    @Transactional
    public String checkStream(Student student) {
        YAnalyzer yAnalyzer =new YAnalyzer();
        return yAnalyzer.checkStream(student);

    }


}
