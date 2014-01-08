package org.yarlithub.yschool.analytics.core;

import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomModule;

/**
 * Created with IntelliJ IDEA.
 * User: kana
 * Date: 11/27/13
 * Time: 6:27 PM
 * To change this template use File | Settings | File Templates.
 */
//TODO: redo due to database change to subject module
public class SubjectResult {
    private ClassroomModule classroomSubject;
    private String result;

    public SubjectResult(ClassroomModule classroomSubject, String result) {
        this.classroomSubject = classroomSubject;

        this.result = result;
    }

    public ClassroomModule getClassroomSubject() {
        return classroomSubject;
    }

    public void setClassroomSubject(ClassroomModule classroomSubject) {
        this.classroomSubject = classroomSubject;
    }

    public String getResult() {
        return result;
    }

    public void setResult(String result) {
        this.result = result;
    }
}
