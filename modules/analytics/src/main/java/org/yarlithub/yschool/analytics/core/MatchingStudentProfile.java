package org.yarlithub.yschool.analytics.core;

import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomSubject;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.repository.model.obj.yschool.StudentGeneralexamProfile;

import javax.faces.model.DataModel;

/**
 * Created with IntelliJ IDEA.
 * User: kana
 * Date: 11/27/13
 * Time: 2:08 PM
 * To change this template use File | Settings | File Templates.
 */

public class MatchingStudentProfile {

    private Student student;
    private DataModel<SubjectResult> olSubjects;
    private DataModel<SubjectResult> alSubjects;
    private int islandRank;
    private double zScore;

    public MatchingStudentProfile(Student student) {
        this.setStudent(student);
           }

    public int getIslandRank() {
        return islandRank;
    }

    public void setIslandRank(int islandRank) {
        this.islandRank = islandRank;
    }

    public double getzScore() {
        return zScore;
    }

    public void setzScore(double zScore) {
        this.zScore = zScore;
    }

    public Student getStudent() {
        return student;

    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public DataModel<SubjectResult> getOlSubjects() {
        return olSubjects;
    }

    public void setOlSubjects(DataModel<SubjectResult> olSubjects) {
        this.olSubjects = olSubjects;
    }

    public DataModel<SubjectResult> getAlSubjects() {
        return alSubjects;
    }

    public void setAlSubjects(DataModel<SubjectResult> alSubjects) {
        this.alSubjects = alSubjects;
    }


}
