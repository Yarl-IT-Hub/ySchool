package org.yarlithub.yschool.web.classroom;

import org.yarlithub.yschool.repository.model.obj.yschool.Classroom;
import org.yarlithub.yschool.repository.model.obj.yschool.Grade;

import javax.faces.model.DataModel;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 12/8/13
 * Time: 4:05 PM
 * To change this template use File | Settings | File Templates.
 */

public class GradeView {
    private DataModel<Classroom> classroomDataModel;
    private Grade grade;

    public GradeView(DataModel<Classroom> classroomDataModel, Grade grade) {
        this.classroomDataModel = classroomDataModel;
        this.grade = grade;
    }

    public DataModel<Classroom> getClassroomDataModel() {
        return classroomDataModel;
    }

    public void setClassroomDataModel(DataModel<Classroom> classroomDataModel) {
        this.classroomDataModel = classroomDataModel;
    }

    public Grade getGrade() {
        return grade;
    }

    public void setGrade(Grade grade) {
        this.grade = grade;
    }
}
