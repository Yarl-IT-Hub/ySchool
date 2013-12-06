package org.yarlithub.yschool.web.analytics;

import com.arima.classanalyzer.core.ExamStandard;
import org.primefaces.model.chart.CartesianChartModel;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.analytics.core.OLSubjectPrediction;
import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomSubject;
import org.yarlithub.yschool.repository.model.obj.yschool.Exam;
import org.yarlithub.yschool.service.AnalyticsService;
import org.yarlithub.yschool.service.StudentService;

import javax.faces.bean.ManagedBean;
import javax.faces.model.DataModel;
import java.io.Serializable;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 11/2/13
 * Time: 10:25 PM
 * To change this template use File | Settings | File Templates.
 */

@ManagedBean
@Scope(value = "session")
@Controller
public class AnalyticsExaminationHomeBean implements Serializable {
    @Autowired
    private StudentService studentService;
    @Autowired
    private AnalyticsService analyticsService;
    @Autowired
    private AnalyticsController analyticsController;

    private Exam exam;
    private DataModel<OLSubjectPrediction> olSubjectPredictions;
    private DataModel aLSubjects;
    private CartesianChartModel linearModel;
    private CartesianChartModel linearModelTermMarks;




    public boolean preloadStudent() {

        return true;

    }

}

