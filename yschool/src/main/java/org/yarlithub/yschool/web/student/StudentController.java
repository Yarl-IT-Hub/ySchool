package org.yarlithub.yschool.web.student;

import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Classroom;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;

import javax.faces.bean.ManagedBean;
import javax.faces.model.DataModel;
import java.io.Serializable;

/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@ManagedBean
@Scope(value = "session")
@Controller
public class StudentController implements Serializable {

    private Student student ;
    private  Classroom classroom;
    private DataModel<Student> studentList;

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public Classroom getClassroom() {
        return classroom;
    }

    public void setClassroom(Classroom classroom) {
        this.classroom = classroom;
    }

    public DataModel<Student> getStudentList() {
        return studentList;
    }

    public void setStudentList(DataModel<Student> studentList) {
        this.studentList = studentList;
    }
}

