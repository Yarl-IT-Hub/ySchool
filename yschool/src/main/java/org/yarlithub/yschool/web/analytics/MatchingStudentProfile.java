package org.yarlithub.yschool.web.analytics;

import org.springframework.beans.factory.annotation.Autowired;
import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomSubject;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.AnalyticsService;

import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
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

    private Student student;
    private DataModel<ClassroomSubject> olSubjects ;
    private DataModel<SubjectResult> alSubjects ;


    public MatchingStudentProfile(Student student) {
        this.setStudent(student);
         }

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public DataModel<ClassroomSubject> getOlSubjects() {
        return olSubjects;
    }

    public void setOlSubjects(DataModel<ClassroomSubject> olSubjects) {
        this.olSubjects = olSubjects;
    }

    public DataModel<SubjectResult> getAlSubjects() {
        return alSubjects;
    }

    public void setAlSubjects(DataModel<SubjectResult> alSubjects) {
        this.alSubjects = alSubjects;
    }
}
