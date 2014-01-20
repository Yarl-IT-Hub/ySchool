package org.yarlithub.yschool.web.student;

import org.yarlithub.yschool.repository.model.obj.yschool.Classroom;

import javax.faces.model.DataModel;

/**
 * Created with IntelliJ IDEA.
 * User: HP
 * Date: 12/8/13
 * Time: 4:05 PM
 * To change this template use File | Settings | File Templates.
 */

public class Grade {
    private DataModel<Classroom> classroomDataModel;
    private int grade;

    public Grade(int grade) {
        this.grade = grade;
    }

    public DataModel<Classroom> getClassroomDataModel() {
        return classroomDataModel;
    }

    public void setClassroomDataModel(DataModel<Classroom> classroomDataModel) {
        this.classroomDataModel = classroomDataModel;
    }

    public int getGrade() {
        return grade;
    }

    public void setGrade(int grade) {
        this.grade = grade;
    }
}