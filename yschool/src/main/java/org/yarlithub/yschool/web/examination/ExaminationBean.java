package org.yarlithub.yschool.web.examination;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.service.ExaminationService;

import javax.faces.bean.ManagedBean;
import javax.faces.bean.ManagedProperty;
import javax.faces.event.ActionEvent;
import java.io.Serializable;
import java.util.Date;
import java.util.List;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */

@ManagedBean
@Scope(value = "session")
@Controller
public class ExaminationBean implements Serializable {
    @Autowired
    private ExaminationService examinationService;
    private String page = "_caExamNew";
    private Date date;
    private int term;
    private int examType;
    private int grade;
    private String division;
    private int subjectid;

    private List<Exam> exams;
    private Exam exam;


    public ExaminationBean() {
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

    public List<Exam> getExams() {
        return exams;
    }

    public void setExams(List<Exam> exams) {
        this.exams = exams;
    }

    public Exam getExam() {
        return exam;
    }

    public void setExam(Exam exam) {
        this.exam = exam;
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

    public String addCAExam() {
        boolean setupResult = examinationService.addCAExam(date, term, examType, grade, division, subjectid);
        if (setupResult) {
            //navigates to home page.(see faces-config.xml)
            return "success";
        }
        //shows error page.
        return "failure";
    }

    public String addTermExam() {
        boolean isAddNewTermExam = examinationService.addTermExam(date, term, examType, grade, subjectid);
        if (isAddNewTermExam) {
            //navigates to home page.(see faces-config.xml)
            return "NewTermExamSuccess";
        }
        //shows error page.
        return "NewTermExamFailure";
    }

    public boolean preloadLatestExams() {
        this.setExams(examinationService.listExams(1,5));
        return true;
    }

    public String viewExam(){
        this.exam = exams.get(1);
        return "ViewExam";
    }
}
