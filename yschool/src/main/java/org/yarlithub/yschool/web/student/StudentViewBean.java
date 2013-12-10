package org.yarlithub.yschool.web.student;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomStudent;
import org.yarlithub.yschool.service.StudentService;

import javax.faces.bean.ManagedBean;
import java.io.Serializable;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@ManagedBean
@Scope(value = "session")
@Controller
public class StudentViewBean implements Serializable {

    private Student student ;
    private DataModel listStudents;
    private  ClassroomStudent classroomStudent;

    @Autowired
    private StudentService studentService;

    @Autowired
    private StudentController studentController;


    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }


    public DataModel getListStudents() {
        return listStudents;
    }

    public void setListStudents(DataModel listStudents) {
        this.listStudents = listStudents;
    }

    public ClassroomStudent getClassroomStudent() {
        return classroomStudent;
    }

    public void setClassroomStudent(ClassroomStudent classroomStudent) {
        this.classroomStudent = classroomStudent;
    }

    public  boolean preload(){
        setStudent(studentController.getStudent());
        this.listStudents=new ListDataModel(studentService.getClassroomStudent(this.student.getId()));
        return true;
    }
}
