package org.yarlithub.yschool.service;

import net.sf.jasperreports.engine.JRException;
import org.hibernate.Hibernate;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.analytics.core.YAnalyzer;
import org.yarlithub.yschool.analytics.reporting.JasperReport;
import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomStudent;
import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomSubject;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.student.core.GetStudent;
import org.yarlithub.yschool.student.core.StudentHelper;

import javax.servlet.ServletOutputStream;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

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
    public Student getStudenById(int id) {
        GetStudent getStudent = new GetStudent();
        Student student = getStudent.getStudentByID(id);
        //Hibernate needs lazy initialization of internal objects
        Hibernate.initialize(student.getClassroomStudents());
        Iterator<ClassroomStudent> classroomStudentIterator = student.getClassroomStudents().iterator();
        while (classroomStudentIterator.hasNext()) {
            ClassroomStudent classroomStudent = classroomStudentIterator.next();
            Hibernate.initialize(classroomStudent.getClassroomIdclass());
            Hibernate.initialize(classroomStudent.getStudentClassroomSubjects());
        }
        return student;
    }

    @Transactional
    public List<Student> getStudentByAdmissionNumber(List<Integer> admissionNo) {
        StudentHelper studentHelper = new StudentHelper();
        List<Student> studentList= new ArrayList<Student>();
        Iterator<Integer> adminNoIterator = admissionNo.iterator();
        while (adminNoIterator.hasNext()) {

            int admissionNumber = adminNoIterator.next();
           Student student= studentHelper.getStudentByAdmissionNo(admissionNumber);

            studentList.add(student);
            Hibernate.initialize(student);

        }

        return studentList;
    }

    @Transactional
    public Student getStudent() {
        GetStudent student = new GetStudent();
        return student.getStudentByID(1);

    }

    @Transactional
    public void printReport(ServletOutputStream servletOutputStream) throws IOException, JRException {       // ServletOutputStream servletOutputStream
        JasperReport jasperReport = new JasperReport();
        jasperReport.printJasperReport(servletOutputStream);                        //  servletOutputStream
    }
}
