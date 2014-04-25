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
    private boolean divisionEditMode = false;
    private String gradeDescription = "Grade 6";
    private String divisionDescription = "Division A";
    private boolean gradeUnEditMode = true;
    private boolean divisionUnEditMode = true;
    private String grade = "6";
    private String division = "A";

    private boolean gradeEditMode1 = false;
    private boolean divisionEditMode1 = false;
    private String gradeDescription1 = "Grade 7";
    private String divisionDescription1 = "Division B";
    private boolean gradeUnEditMode1 = true;
    private boolean divisionUnEditMode1 = true;
    private String grade1 = "7";
    private String division1 = "B";



    public String getGradeDescription() {
        return gradeDescription;
    }

    public void setGradeDescription(String gradeDescription) {
        this.gradeDescription = gradeDescription;
    }

    public String getDivisionDescription() {
        return divisionDescription;
    }

    public void setDivisionDescription(String divisionDescription) {
        this.divisionDescription = divisionDescription;
    }

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

    public String getDivision() {
        return division;
    }

    public void setDivision(String division) {
        this.division = division;
    }

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

    public void gradeChangeToEditMode1(){
        gradeEditMode1 = true;
        gradeUnEditMode1 = false;
    }

    public void gradeCompleteEditMode1(){
        gradeEditMode1 = false;
        gradeUnEditMode1 = true;
    }

    public void gradeCancelEditMode1(){
        gradeEditMode1 = false;
        gradeUnEditMode1 = true;
    }

    public void gradeDeleteEditMode1(){
        gradeEditMode1 = false;
        gradeUnEditMode1 = true;
    }

    public void divisionChangeToEditMode1(){
        divisionEditMode1 = true;
        divisionUnEditMode1 = false;
    }

    public void divisionCompleteEditMode1(){
        divisionEditMode1 = false;
        divisionUnEditMode1 = true;
    }

    public void divisionCancelEditMode1(){
        divisionEditMode1 = false;
        divisionUnEditMode1 = true;
    }

    public void divisionDeleteEditMode1(){
        divisionEditMode1 = false;
        divisionUnEditMode1 = true;
    }

    public boolean isGradeEditMode1() {
        return gradeEditMode1;
    }

    public void setGradeEditMode1(boolean gradeEditMode1) {
        this.gradeEditMode1 = gradeEditMode1;
    }

    public boolean isDivisionEditMode1() {
        return divisionEditMode1;
    }

    public void setDivisionEditMode1(boolean divisionEditMode1) {
        this.divisionEditMode1 = divisionEditMode1;
    }

    public String getGradeDescription1() {
        return gradeDescription1;
    }

    public void setGradeDescription1(String gradeDescription1) {
        this.gradeDescription1 = gradeDescription1;
    }

    public String getDivisionDescription1() {
        return divisionDescription1;
    }

    public void setDivisionDescription1(String divisionDescription1) {
        this.divisionDescription1 = divisionDescription1;
    }

    public boolean isGradeUnEditMode1() {
        return gradeUnEditMode1;
    }

    public void setGradeUnEditMode1(boolean gradeUnEditMode1) {
        this.gradeUnEditMode1 = gradeUnEditMode1;
    }

    public boolean isDivisionUnEditMode1() {
        return divisionUnEditMode1;
    }

    public void setDivisionUnEditMode1(boolean divisionUnEditMode1) {
        this.divisionUnEditMode1 = divisionUnEditMode1;
    }

    public String getGrade1() {
        return grade1;
    }

    public void setGrade1(String grade1) {
        this.grade1 = grade1;
    }

    public String getDivision1() {
        return division1;
    }

    public void setDivision1(String division1) {
        this.division1 = division1;
    }
}
