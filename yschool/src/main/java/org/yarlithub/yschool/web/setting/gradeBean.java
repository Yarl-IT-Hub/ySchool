package org.yarlithub.yschool.web.setting;

import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;

import javax.faces.bean.ManagedBean;
import javax.faces.component.html.HtmlDataTable;
import java.util.ArrayList;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: Pirinthapan
 * Date: 4/10/14
 * Time: 10:36 PM
 * To change this template use File | Settings | File Templates.
 */
@ManagedBean
@Scope(value = "session")
@Controller
public class gradeBean {

    private List<Grade> grades;
    private boolean gradeEditMode = false;
    private HtmlDataTable gradeTable;
    private boolean gradeUnEditMode = true;
    private String grade;
    private String description;


    public HtmlDataTable getGradeTable() {
        return gradeTable;
    }

    public void setGradeTable(HtmlDataTable gradeTable) {
        this.gradeTable = gradeTable;
    }

    public List<Grade> getGrades() {
        return grades;
    }

    public void setGrades(List<Grade> grades) {
        this.grades = grades;
    }

    public String getGrade() {
        return grade;
    }

    public void setGrade(String grade) {
        this.grade = grade;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public boolean isGradeEditMode() {
        return gradeEditMode;
    }

    public void setGradeEditMode(boolean gradeEditMode) {
        this.gradeEditMode = gradeEditMode;
    }

    public boolean isGradeUnEditMode() {
        return gradeUnEditMode;
    }

    public void setGradeUnEditMode(boolean gradeUnEditMode) {
        this.gradeUnEditMode = gradeUnEditMode;
    }

    public gradeBean() {
        grades = new ArrayList<>();
    }

    public void saveGrade(){
        Grade grade = grades.get(grades.size()-1);
        grade.setGrade(getGrade());
        grade.setDescription(getDescription());
    }

    public void addGrade(){
        System.out.println("add grade called...............................................");
        Grade grade = new Grade();
        grade.setGrade("6");
        grade.setDescription("kdkdk");
        grades.add(grade);
        System.out.println("grade added........................................................" + grade);
    }

    public void deleteGrade(){
        Grade selectedGrade = (Grade)gradeTable.getRowData();
        grades.remove(selectedGrade);
    }

    public void gradeChangeToEditMode(){
        gradeEditMode = true;
        gradeUnEditMode = false;
    }

    public void gradeCompleteEditMode(){
        gradeEditMode = false;
        gradeUnEditMode = true;
    }

    public void gradeCancelEditMode(){
        gradeEditMode = false;
        gradeUnEditMode = true;
    }

    public void gradeDeleteEditMode(){
        gradeEditMode = false;
        gradeUnEditMode = true;
    }

    public boolean isGradeAdded(){
        return grades.size()>0;
    }
}
