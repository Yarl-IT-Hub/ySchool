package org.yarlithub.yschool.web.examination;

import org.apache.myfaces.custom.fileupload.UploadedFile;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
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
    @Autowired
    private ExaminationService examinationService;
    @Autowired
    private ExaminationController examinationController;
    private Exam exam;
    private DataModel marksORresults;
    private UploadedFile marksORresultsFile;
    private int yearInt;
    private int dateInt;
    private String monthString;
    public boolean generalExam;

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

    public void preloadExam() {

        this.setExam(examinationController.getExam());

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
            examinationService.uploadMarks(marksORresultsFile, exam.getId());
            marksORresults = new ListDataModel(examinationService.getExamResults(this.exam.getId()));
        } else {  //for term and ca exam we have float marks
            examinationService.uploadMarks(marksORresultsFile, exam.getId());
            this.marksORresults = new ListDataModel(examinationService.getExamMarks(this.exam.getId()));
        }
        return "ViewExam";
    }

    public void syncExam(){
        for (int i=0;i<10000;i++){
               String a="kjds";
            a.equalsIgnoreCase("kjsdf") ;
        }
    }
}
