package org.yarlithub.yschool.web.examination;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.service.ExaminationService;

import javax.faces.bean.ManagedBean;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
import java.io.Serializable;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */

@ManagedBean
@Scope(value = "session")
@Controller
public class ExaminationHomeBean implements Serializable {
    @Autowired
    private ExaminationService examinationService;
    @Autowired
    private ExaminationController examinationController;

    private DataModel<Exam> exams;

    public DataModel getExams() {
        return exams;
    }

    public void setExams(DataModel exams) {
        this.exams = exams;
    }

    public boolean preloadLatestExams() {
        exams = new ListDataModel(examinationService.listExams(0, 100));
        this.setExams(exams);
        return true;
    }

    public String viewExam() {
        examinationController.setCurrentExam(exams.getRowData());
//        if (exam.getExamTypeIdexamType().getId() == ExamType.GENERAL_EXAM) {
//            marks = null;
//        } else {  //for term and ca exam we have float marks
//            this.marks = new ListDataModel(examinationService.getExamMarks(this.exam.getId()));
//        }
        return "ViewExam";
    }

}
