package org.yarlithub.yschool.web.examination;

import org.apache.myfaces.custom.fileupload.UploadedFile;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Division;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.repository.model.obj.yschool.Grade;
import org.yarlithub.yschool.repository.model.obj.yschool.Module;
import org.yarlithub.yschool.service.ExaminationService;
import org.yarlithub.yschool.web.util.PageName;
import org.yarlithub.yschool.web.util.YDateUtils;

import javax.faces.bean.ManagedBean;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
import java.io.IOException;
import java.io.Serializable;
import java.util.Calendar;
import java.util.Date;
import java.util.List;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */

@ManagedBean
@Scope(value = "view")
@Controller
public class ExaminationViewBean implements Serializable {


    public boolean generalExam;
    @Autowired
    private ExaminationService examinationService;
    private int examId;
    private Exam exam;
    private DataModel marksORresults;
    private UploadedFile marksORresultsFile;
    private boolean editMode = false;
    //for examination edit
    private List<Grade> availableGrades;
    private List<Division> availableDivisions;
    private List<Module> availableModules;

    public int getExamId() {
        return examId;
    }

    public void setExamId(int examId) {
        this.examId = examId;
    }

    public Exam getExam() {
        return exam;
    }

    public void setExam(Exam exam) {
        this.exam = exam;
    }

    public DataModel getMarksORresults() {
        return marksORresults;
    }

    public void setMarksORresults(DataModel marksORresults) {
        this.marksORresults = marksORresults;
    }

    public UploadedFile getMarksORresultsFile() {
        return marksORresultsFile;
    }

    public void setMarksORresultsFile(UploadedFile marksORresultsFile) {
        this.marksORresultsFile = marksORresultsFile;
    }

    public int getYearInt() {
        Calendar cal = Calendar.getInstance();
        cal.setTime(this.exam.getDate());
        return cal.get(Calendar.YEAR);

    }

    public int getDateInt() {
        Calendar cal = Calendar.getInstance();
        cal.setTime(this.exam.getDate());
        return cal.get(Calendar.DATE);
    }

    public String getMonthString() {
        Calendar cal = Calendar.getInstance();
        cal.setTime(this.exam.getDate());
        return YDateUtils.getMonthForInt(cal.get(Calendar.MONTH));
    }

    public boolean isGeneralExam() {
        return generalExam;
    }

    public void setGeneralExam(boolean generalExam) {
        this.generalExam = generalExam;
    }

    public boolean isEditMode() {
        return editMode;
    }

    public void setEditMode(boolean editMode) {
        this.editMode = editMode;
    }

    public List<Grade> getAvailableGrades() {
        return availableGrades;
    }

    public void setAvailableGrades(List<Grade> availableGrades) {
        this.availableGrades = availableGrades;
    }

    public List<Division> getAvailableDivisions() {
        return availableDivisions;
    }

    public void setAvailableDivisions(List<Division> availableDivisions) {
        this.availableDivisions = availableDivisions;
    }

    public List<Module> getAvailableModules() {
        return availableModules;
    }

    public void setAvailableModules(List<Module> availableModules) {
        this.availableModules = availableModules;
    }

    /**
     * Change to edit mode, examination details become editable.
     */
    public void editMode(){
        setAvailableGrades(this.examinationService.getAvailableGrades());
        setAvailableDivisions(this.examinationService.getAvailableDivisions());
        setAvailableModules(this.examinationService.getAllModules());
        setEditMode(true);
    }

    /**
     * Change to view mode, examination details become not editable.
     */
    public void viewMode(){
        setEditMode(false);
    }

    /**
     * Get exam using id, load marks/results of the exam.
     */
    public void preloadExam() {

        this.setExam(examinationService.getExambyId(examId));

        //load marks/marksORresults of the current exam.
        if (exam.getExamTypeIdexamType().getId() == ExamType.GENERAL_EXAM) {
            setGeneralExam(true);
            this.marksORresults = new ListDataModel(examinationService.getExamResults(this.exam.getId()));
        } else {
            //for term and ca exam we have float marks
            setGeneralExam(false);
            this.marksORresults = new ListDataModel(examinationService.getExamMarks(this.exam.getId()));
        }
    }

    /**
     * load the spread sheet entries to db.
     * @return navigation to examination view.
     * @throws IOException
     */
    public String uploadMarks() throws IOException {

        if (exam.getExamTypeIdexamType().getId() == ExamType.GENERAL_EXAM) {
            examinationService.uploadResults(marksORresultsFile, exam.getId());
            marksORresults = new ListDataModel(examinationService.getExamResults(this.exam.getId()));
        } else {  //for term and ca exam we have float marks
            examinationService.uploadMarks(marksORresultsFile, exam.getId());
            this.marksORresults = new ListDataModel(examinationService.getExamMarks(this.exam.getId()));
        }

        return PageName.EXAMINATION_VIEW;
    }

}
