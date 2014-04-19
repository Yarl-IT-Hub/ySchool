package org.yarlithub.yschool.web.setting;

import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;

import javax.faces.bean.ManagedBean;

/**
 * Created with IntelliJ IDEA.
 * User: Pirinthapan
 * Date: 4/8/14
 * Time: 11:17 PM
 * To change this template use File | Settings | File Templates.
 */

@ManagedBean
@Scope(value = "session")
@Controller
public class settingsGradesDivisionsBean {
    private boolean gradeEditMode = false;

    public boolean isDivisionEditMode() {
        return divisionEditMode;
    }

    public void setDivisionEditMode(boolean divisionEditMode) {
        this.divisionEditMode = divisionEditMode;
    }

    public boolean isDivisionUnEditMode() {
        return divisionUnEditMode;
    }

    public void setDivisionUnEditMode(boolean divisionUnEditMode) {
        this.divisionUnEditMode = divisionUnEditMode;
    }

    private boolean gradeUnEditMode = true;
    private boolean divisionEditMode = false;
    private boolean divisionUnEditMode = true;
    private String grade = "6";

    public String getDivision() {
        return division;
    }

    public void setDivision(String division) {
        this.division = division;
    }

    private String division = "A";

    public String getGrade() {
        return grade;
    }

    public void setGrade(String grade) {
        this.grade = grade;
    }

    public settingsGradesDivisionsBean(){
        gradeUnEditMode = true;
        gradeEditMode = false;
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

    public void divisionChangeToEditMode(){
        divisionEditMode = true;
        divisionUnEditMode = false;
    }

    public void divisionCompleteEditMode(){
        divisionEditMode = false;
        divisionUnEditMode = true;
    }

    public void divisionCancelEditMode(){
        divisionEditMode = false;
        divisionUnEditMode = true;
    }

    public void divisionDeleteEditMode(){
        divisionEditMode = false;
        divisionUnEditMode = true;
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
}
