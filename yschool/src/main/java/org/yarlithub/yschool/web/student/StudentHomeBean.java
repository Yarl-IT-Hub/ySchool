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
import java.util.ArrayList;
import java.util.List;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@ManagedBean
@Scope(value = "session")
@Controller
public class StudentHomeBean implements Serializable {

       private DataModel<Grade> gradeDataModel;
//       private  DataModel<Student> studentDataModel;

    @Autowired
    private StudentService studentService;

    @Autowired
   private StudentController studentController;




    public DataModel<Grade> getGradeDataModel() {
        return gradeDataModel;
    }

    public void setGradeDataModel(DataModel<Grade> gradeDataModel) {
        this.gradeDataModel = gradeDataModel;
    }


    public boolean preloadstudents() {

        Grade grade10=new Grade(10);
        List<Classroom> classroomList10=studentService.getCurrentClasses(10);
        grade10.setClassroomDataModel(new ListDataModel<Classroom>(classroomList10));

        Grade grade11=new Grade(11);
        List<Classroom> classroomList11=studentService.getCurrentClasses(11);
        grade11.setClassroomDataModel(new ListDataModel<Classroom>(classroomList11));


        List gradelist=new ArrayList();
        gradelist.add(grade10);
        gradelist.add(grade11);
        gradeDataModel =new ListDataModel<Grade>(gradelist);


        return true;
    }

    public String viewClassroom() {
         Grade grade = gradeDataModel.getRowData();
         Classroom classroom=grade.getClassroomDataModel().getRowData();
        studentController.setClassroom(classroom);
        return "ViewListStudents";
    }

}