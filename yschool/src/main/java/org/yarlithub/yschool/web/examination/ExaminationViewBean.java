package org.yarlithub.yschool.web.examination;

import org.apache.myfaces.custom.fileupload.UploadedFile;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.analytics.datasync.SyncStatus;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.repository.model.obj.yschool.Results;
import org.yarlithub.yschool.service.ExaminationService;
import org.yarlithub.yschool.web.util.YDateUtils;

import javax.faces.bean.ManagedBean;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
import java.io.IOException;
import java.io.Serializable;
import java.util.Calendar;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */

@ManagedBean
@Scope(value = "session")
@Controller
public class ExaminationViewBean implements Serializable {
    public boolean generalExam;
    @Autowired
    private ExaminationService examinationService;
    @Autowired
    private ExaminationController examinationController;
    private Exam exam;
    private boolean synced;
    private DataModel marksORresults;
    private UploadedFile marksORresultsFile;
    private int yearInt;
    private int dateInt;
    private String monthString;

    private int currentRowIslandRank;
    private double currentRowZScore;

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

    public int getCurrentRowIslandRank() {
        return ((Results)marksORresults.getRowData()).getStudentIdstudent().getStudentGeneralexamProfiles().iterator().next().getAlIslandRank();

    }

    public void setCurrentRowIslandRank(int currentRowIslandRank) {
        this.currentRowIslandRank = currentRowIslandRank;
    }

    public double getCurrentRowZScore() {
        return ((Results)marksORresults.getRowData()).getStudentIdstudent().getStudentGeneralexamProfiles().iterator().next().getZscore();
    }

    public void setCurrentRowZScore(double currentRowZScore) {
        this.currentRowZScore = currentRowZScore;
    }

    public boolean isSynced() {
        return synced;
    }

    public void setSynced(boolean synced) {
        this.synced = synced;
    }

    public void preloadExam() {

        this.setExam(examinationService.getExambyId(examinationController.getExam().getId()));

        setSynced(true);
        if(!exam.getExamSyncs().isEmpty()){
        if(exam.getExamSyncs().iterator().next().getSyncStatus()== SyncStatus.NOT_SYNCED
                || exam.getExamSyncs().iterator().next().getSyncStatus()== SyncStatus.MODIFIED_AFTER_SYNCED){
               setSynced(false);
        }
        }

        //load marks/marksORresults of the current exam.
        if (exam.getExamTypeIdexamType().getId() == ExamType.GENERAL_EXAM) {
            setGeneralExam(true);
            this.marksORresults = new ListDataModel(examinationService.getExamResults(this.exam.getId()));
        } else {  //for term and ca exam we have float marks
            setGeneralExam(false);
            this.marksORresults = new ListDataModel(examinationService.getExamMarks(this.exam.getId()));
        }
    }

    public String uploadMarks() throws IOException {

        if (exam.getExamTypeIdexamType().getId() == ExamType.GENERAL_EXAM) {
            examinationService.uploadResults(marksORresultsFile, exam.getId());
            marksORresults = new ListDataModel(examinationService.getExamResults(this.exam.getId()));
        } else {  //for term and ca exam we have float marks
            examinationService.uploadMarks(marksORresultsFile, exam.getId());
            this.marksORresults = new ListDataModel(examinationService.getExamMarks(this.exam.getId()));
        }
        examinationController.setCurrentExam(examinationService.getExambyId(this.exam.getId()));
        return "ViewExam";
    }

    public String editExam() {
        return "EditExam";
    }

    public String syncCLASS() {
        return "AnalyticsSync";

    }
}
