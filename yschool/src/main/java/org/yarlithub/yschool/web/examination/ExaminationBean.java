package org.yarlithub.yschool.web.examination;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.service.ExaminationService;

import javax.faces.bean.ManagedBean;
import java.io.Serializable;
import java.util.Date;


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
}
