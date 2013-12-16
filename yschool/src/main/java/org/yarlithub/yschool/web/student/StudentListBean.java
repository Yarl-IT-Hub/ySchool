package org.yarlithub.yschool.web.student;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Classroom;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.StudentService;

import javax.faces.bean.ManagedBean;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
import java.io.Serializable;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@ManagedBean
@Scope(value = "session")
@Controller
public class StudentListBean implements Serializable {

    private DataModel<Student> listStudents;
    @Autowired
    private StudentService studentService;
    @Autowired
    private StudentController studentController;

    public DataModel getListStudents() {
        return listStudents;
    }

    public void setListStudents(DataModel listStudents) {
        this.listStudents = listStudents;
    }

    public boolean preload() {

        Classroom classroom = studentController.getClassroom();
        listStudents = new ListDataModel(studentService.getClassroomStudent(classroom));
        return true;
    }

    public String viewStudent() {
        Student student = (Student) listStudents.getRowData();
        studentController.setStudent(student);
        return "viewStudent";
    }
}
