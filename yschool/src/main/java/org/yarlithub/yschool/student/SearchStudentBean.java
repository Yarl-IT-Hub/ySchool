/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.yarlithub.yschool.student;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import javax.faces.bean.ManagedBean;
import javax.faces.bean.RequestScoped;
import javax.faces.bean.SessionScoped;
import org.yarlithub.yschool.repository.Student;

/**
 *
 * @author Mayoo
 */
@ManagedBean
@SessionScoped
public class SearchStudentBean {

    public Student student;
    private List<Student> selectStudentList;
    {
        final Student student1 = new Student();
        student1.setFullName("Name 1");
        student1.setAdmissionNumber("Admission No 1");
      selectStudentList = Arrays.asList(student1);
    }
 
    public  SearchStudentBean() {
        
     
    }    
    public String selectStudent(){
        selectStudentList = new ArrayList<>();
        selectStudentList.add(new Student());
        selectStudentList.add(new Student());
        selectStudentList.add(new Student());
        return "/yschool/faces/student/add-student-class.xhtml";
    }

    public Student getStudent() {
        return student;
    }
    
    

    public void setStudent(Student student) {
        this.student = student;
    }

    public List<Student> getSelectStudentList() {
        return selectStudentList;
    }

    public void setSelectStudentList(List<Student> selectStudentList) {
        this.selectStudentList = selectStudentList;
    }

    
    
   
}
