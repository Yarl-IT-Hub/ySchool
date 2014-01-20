package org.yarlithub.yschool.web.student;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Classroom;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.StudentService;

import javax.annotation.PostConstruct;
import javax.faces.bean.ManagedBean;
import java.io.Serializable;

/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@ManagedBean
@Scope(value = "view")
@Controller
public class StudentEditBean implements Serializable {

    private Student student;
    @Autowired
    private StudentService studentService;
    @Autowired
    private StudentController studentController;

    @PostConstruct
    public void init(){
        this.preLoad();
    }
    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public boolean preLoad() {

        setStudent(studentController.getStudent());
        return  true;
    }

    public String editstudent(){
        studentService.saveOrUpdate(student);
        return "editStudent";
    }

    public String backToView(){
        return "backToView";
    }
}

