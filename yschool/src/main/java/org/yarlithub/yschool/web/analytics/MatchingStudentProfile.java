package org.yarlithub.yschool.web.analytics;

import org.springframework.beans.factory.annotation.Autowired;
import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomSubject;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.AnalyticsService;

import java.util.ArrayList;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: kana
 * Date: 11/27/13
 * Time: 2:08 PM
 * To change this template use File | Settings | File Templates.
 */

public class MatchingStudentProfile {
    @Autowired
    private AnalyticsService analyticsService;
    private Student student;
    private List<ClassroomSubject> olSubjects = new ArrayList<ClassroomSubject>();
    private List<ClassroomSubject> alSubjects = new ArrayList<ClassroomSubject>();

    public MatchingStudentProfile(Student student) {
        this.setStudent(student);
        this.setOlSubjects(analyticsService.getOLSubjects(student));
        this.setAlSubjects(analyticsService.getALSubjects(student));
    }

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public List<ClassroomSubject> getOlSubjects() {
        return olSubjects;
    }

    public void setOlSubjects(List<ClassroomSubject> olSubjects) {
        this.olSubjects = olSubjects;
    }

    public List<ClassroomSubject> getAlSubjects() {
        return alSubjects;
    }

    public void setAlSubjects(List<ClassroomSubject> alSubjects) {
        this.alSubjects = alSubjects;
    }
}
