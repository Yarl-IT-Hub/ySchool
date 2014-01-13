package org.yarlithub.yschool.web.examination;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.service.ExaminationService;

import javax.faces.bean.ManagedBean;
import java.io.Serializable;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */

@ManagedBean
@Scope(value = "session")
@Controller
public class ExaminationController implements Serializable {
    @Autowired
    private ExaminationService examinationService;
    private Exam exam;

    public Exam getExam() {
        return exam;
    }

    public void setExam(Exam exams) {
        this.exam = exams;
    }

    public void setCurrentExam(Exam exam) {
        this.exam = exam;
    }

}
