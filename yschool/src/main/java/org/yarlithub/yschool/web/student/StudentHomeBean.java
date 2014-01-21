package org.yarlithub.yschool.web.student;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
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
@Scope(value = "view")
@Controller
public class StudentHomeBean implements Serializable {


    @Autowired
    private StudentService studentService;

    @Autowired
    private StudentController studentController;

    private String searchKey = null;
    private DataModel<Student> studentsSearchResultAjax;
    private DataModel<Student> studentsSearchResult;
    private String page = "studentSearch";
    private Student student;

    public StudentHomeBean() {
        super();
        studentsSearchResultAjax = new ListDataModel<Student>();
    }

    public String getSearchKey() {
        return searchKey;
    }

    public void setSearchKey(String searchKey) {
        this.searchKey = searchKey;
    }

    public DataModel<Student> getStudentsSearchResultAjax() {
        return studentsSearchResultAjax;
    }

    public void setStudentsSearchResultAjax(DataModel<Student> studentsSearchResultAjax) {
        this.studentsSearchResultAjax = studentsSearchResultAjax;
    }

    public DataModel<Student> getStudentsSearchResult() {
        return studentsSearchResult;
    }

    public void setStudentsSearchResult(DataModel<Student> studentsSearchResult) {
        this.studentsSearchResult = studentsSearchResult;
    }

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public String getPage() {
        return page;
    }

    public void setPage(String page) {
        this.page = page;
    }

    public String viewStudentAjax(){
        studentsSearchResultAjax = new ListDataModel<Student>(studentService.getStudentsNameLike(searchKey,10));
        setStudent(studentsSearchResultAjax.getRowData());
        studentController.setStudent(student);
        return "viewStudentAjax";
    }

    public String viewStudentSearch(){
        studentsSearchResult = new ListDataModel<Student>(studentService.getStudentsNameLike(searchKey,30));
        studentController.setStudentList(studentsSearchResult);
        return "ViewStudentList";
    }
//
//
//    public String viewClassroom() {
//        Grade grade = gradeDataModel.getRowData();
//        Classroom classroom=grade.getClassroomDataModel().getRowData();
//        studentController.setClassroom(classroom);
//        return "ViewListStudents";
//    }

}