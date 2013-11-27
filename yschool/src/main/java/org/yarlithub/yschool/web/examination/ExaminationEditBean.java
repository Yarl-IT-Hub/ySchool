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
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
import java.io.IOException;
import java.io.Serializable;
import java.util.Calendar;
import java.util.Date;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */

@ManagedBean
@Scope(value = "session")
@Controller
public class ExaminationEditBean implements Serializable {
    @Autowired
    private ExaminationService examinationService;
    private String page = "_caExamNew";
    private Date date;
    private int term;
    private int examType;
    private int grade;
    private String division;
    private int subjectid;
    private int yearInt;
    private int dateInt;
    private String monthString;
    private DataModel exams;
    private Exam exam;
    private DataModel<Results> results;
    private DataModel<Marks> marks;

    private UploadedFile marksFile;


    public ExaminationEditBean() {
        setExamType(ExamType.CA_EXAM);
    }

    public int getExamType() {
        return examType;
    }

    public void setExamType(int examType) {
        this.examType = examType;
    }

    public int getTerm() {
        return term;
    }

    public void setTerm(int term) {
        this.term = term;
    }

    public Date getDate() {
        return date;
    }

    public void setDate(Date date) {
        this.date = date;
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

    public String getDivision() {
        return division;
    }

    public void setDivision(String division) {
        this.division = division;
    }

    public int getSubjectid() {
        return subjectid;
    }

    public void setSubjectid(int subjectid) {
        this.subjectid = subjectid;
    }

    public int getGrade() {
        return grade;
    }

    public void setGrade(int grade) {
        this.grade = grade;
    }

    public DataModel getExams() {
        return exams;
    }

    public void setExams(DataModel exams) {
        this.exams = exams;
    }

    public Exam getExam() {
        return exam;
    }

    public void setExam(Exam exam) {
        this.exam = exam;
    }

    public DataModel<Marks> getMarks() {
        return marks;
    }

    public void setMarks(DataModel<Marks> marks) {
        this.marks = marks;
    }

    public DataModel<Results> getResults() {
        return results;
    }

    public void setResults(DataModel<Results> results) {
        this.results = results;
    }

    public UploadedFile getMarksFile() {
        return marksFile;
    }

    public void setMarksFile(UploadedFile marksFile) {
        this.marksFile = marksFile;
    }

    /**
     * Ajax Dynamic loading.
     */

    public String getPage() {
        return page;
    }

    public void setPage(String page) {
        this.page = page;
    }

    public void setCAExamPage() {
        setExamType(ExamType.CA_EXAM);
        this.page = "_caExamNew";
    }

    public void setTermExamPage() {
        setExamType(ExamType.TERM_EXAM);
        this.page = "_termExamNew";
    }

//    public String addCAExam() {
//        boolean setupResult = examinationService.addCAExam(date, term, examType, grade, division, subjectid);
//        if (setupResult) {
//            //navigates to home page.(see faces-config.xml)
//            return "success";
//        }
//        //shows error page.
//        return "failure";
//    }
//
//    public String addTermExam() {
//        boolean isAddNewTermExam = examinationService.addTermExam(date, term, examType, grade, subjectid);
//        if (isAddNewTermExam) {
//            //navigates to home page.(see faces-config.xml)
//            return "NewTermExamSuccess";
//        }
//        //shows error page.
//        return "NewTermExamFailure";
//    }

    public boolean preloadLatestExams() {
        exams = new ListDataModel(examinationService.listExams(1, 5));
        this.setExams(exams);
        return true;
    }

    public String viewExam() {
        this.exam = (Exam) exams.getRowData();
        if (exam.getExamTypeIdexamType().getId() == ExamType.GENERAL_EXAM) {
            marks = null;
        } else {  //for term and ca exam we have float marks
            this.marks = new ListDataModel(examinationService.getExamMarks(this.exam.getId()));
        }
        return "ViewExam";
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
