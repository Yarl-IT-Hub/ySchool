package org.yarlithub.yschool.web.examination;

import org.apache.myfaces.custom.fileupload.UploadedFile;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.repository.model.obj.yschool.Marks;
import org.yarlithub.yschool.repository.model.obj.yschool.Results;
import org.yarlithub.yschool.service.ExaminationService;
import org.yarlithub.yschool.web.util.YDateUtils;

import javax.faces.bean.ManagedBean;
import javax.faces.context.ExternalContext;
import javax.faces.context.FacesContext;
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
    private int examid;
    private DataModel<Results> results;
    private DataModel<Marks> marks;
    private UploadedFile marksFile;

    private int yearInt;
    private int dateInt;
    private String monthString;

    public int getExamid() {
        return examid;
    }

    public void setExamid(int examid) {
        this.examid = examid;
    }

    public Exam getExam() {
        return exam;
    }

    public void setExam(Exam exam) {
        this.exam = exam;
    }

    public DataModel<Results> getResults() {
        return results;
    }

    public void setResults(DataModel<Results> results) {
        this.results = results;
    }

    public DataModel<Marks> getMarks() {
        return marks;
    }

    public void setMarks(DataModel<Marks> marks) {
        this.marks = marks;
    }

    public UploadedFile getMarksFile() {
        return marksFile;
    }

    public void setMarksFile(UploadedFile marksFile) {
        this.marksFile = marksFile;
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


    public void preloadExam() {

//        FacesContext context = FacesContext.getCurrentInstance();
//        String examidParam = context.getExternalContext().getRequestParameterMap().get("examid");
//        this.setExamid(Integer.valueOf(examidParam));

//        this.setExam(examinationService.getExambyId(examid));
        this.setExam(examinationController.getExam());

        //load marks/results of the current exam.
        if (exam.getExamTypeIdexamType().getId() == ExamType.GENERAL_EXAM) {
            marks = null;
        } else {  //for term and ca exam we have float marks
            this.marks = new ListDataModel(examinationService.getExamMarks(this.exam.getId()));
        }
    }

    public String uploadMarks() throws IOException {
        examinationService.uploadMarks(marksFile,exam.getId());
        if (exam.getExamTypeIdexamType().getId() == ExamType.GENERAL_EXAM) {
            marks = null;
        } else {  //for term and ca exam we have float marks
            this.marks = new ListDataModel(examinationService.getExamMarks(this.exam.getId()));
        }
        return "ViewExam";
    }
}
