package org.yarlithub.yschool.web.analytics;

import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomSubject;

/**
 * Created with IntelliJ IDEA.
 * User: kana
 * Date: 11/27/13
 * Time: 6:27 PM
 * To change this template use File | Settings | File Templates.
 */
public class SubjectResult {
    private ClassroomSubject classroomSubject;
    private String result;

    public SubjectResult(ClassroomSubject classroomSubject, String result) {
        this.classroomSubject = classroomSubject;
        this.result = result;
    }

    public ClassroomSubject getClassroomSubject() {
        return classroomSubject;
    }

    public void setClassroomSubject(ClassroomSubject classroomSubject) {
        this.classroomSubject = classroomSubject;
    }

    public String getResult() {
        return result;
    }

    public void setResult(String result) {
        this.result = result;
    }
}
