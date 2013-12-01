package org.yarlithub.yschool.web.analytics;

import org.primefaces.model.chart.CartesianChartModel;
import org.primefaces.model.chart.LineChartSeries;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.analytics.core.OLSubjectPrediction;
import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomSubject;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.AnalyticsService;
import org.yarlithub.yschool.service.StudentService;

import javax.faces.bean.ManagedBean;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
import java.io.Serializable;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

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
public class AnalyticsStudentHomeBean implements Serializable {
    @Autowired
    private StudentService studentService;
    @Autowired
    private AnalyticsService analyticsService;
    @Autowired
    private AnalyticsController analyticsController;
    private Student student;
    private DataModel secondarySubjects;
    private DataModel<ClassroomSubject> oLSubjects;
    private DataModel<ClassroomSubject> oLSubjectsEleven;
    private DataModel<OLSubjectPrediction> olSubjectPredictions;
    private DataModel aLSubjects;
    private CartesianChartModel linearModel;
    private CartesianChartModel linearModelTermMarks;
    private List<Double> termMarks = new ArrayList<Double>();

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public DataModel getoLSubjects() {
        return oLSubjects;
    }

    public void setoLSubjects(DataModel oLSubjects) {
        this.oLSubjects = oLSubjects;
    }

    public CartesianChartModel getLinearModel() {
        return linearModel;
    }

    public void setLinearModel(CartesianChartModel linearModel) {
        this.linearModel = linearModel;
    }

    public List<Double> getTermMarks() {
        return termMarks;
    }

    public void setTermMarks(List<Double> termMarks) {
        this.termMarks = termMarks;
    }

    private void createLinearModel() {
        linearModel = new CartesianChartModel();


        LineChartSeries series1 = new LineChartSeries();
        series1.setLabel("Term Marks (year ten)");

        series1.set(1, termMarks.get(0));
        series1.set(2, termMarks.get(1));
        series1.set(3, termMarks.get(2));


        linearModel.addSeries(series1);

        setLinearModel(linearModel);

    }

    private void createLinearModelTermMarks(OLSubjectPrediction olSubjectPrediction) {
        linearModelTermMarks = new CartesianChartModel();


        LineChartSeries termMarks = new LineChartSeries();
        termMarks.setLabel("Term Marks");

        if (olSubjectPrediction.getTermMarks().get(0) != -.1) {
            termMarks.set(1, olSubjectPrediction.getTermMarks().get(0));
        }
        if (olSubjectPrediction.getTermMarks().get(1) != -.1) {
            termMarks.set(2, olSubjectPrediction.getTermMarks().get(1));
        }
        if (olSubjectPrediction.getTermMarks().get(2) != -.1) {
            termMarks.set(3, olSubjectPrediction.getTermMarks().get(2));
        }
        if (olSubjectPrediction.getTermMarks().get(3) != -.1) {
            termMarks.set(4, olSubjectPrediction.getTermMarks().get(3));
        }
        if (olSubjectPrediction.getTermMarks().get(4) != -.1) {
            termMarks.set(5, olSubjectPrediction.getTermMarks().get(4));
        }

        if (olSubjectPrediction.getTermMarks().get(5) != -.1) {
            termMarks.set(6, olSubjectPrediction.getTermMarks().get(5));
        }


        linearModelTermMarks.addSeries(termMarks);
        // linearModel.addSeries(series2);
        setLinearModel(linearModelTermMarks);

    }

    public CartesianChartModel getLinearModelTermMarks() {
        return linearModelTermMarks;
    }

    public void setLinearModelTermMarks(CartesianChartModel linearModelTermMarks) {
        this.linearModelTermMarks = linearModelTermMarks;
    }

    public boolean preloadStudent() {

        this.student = analyticsService.getStudenById(1);
        //  this.student=analyticsController.getStudent();
        this.oLSubjects = new ListDataModel(analyticsService.getOLSubjects(student));
        this.oLSubjectsEleven = new ListDataModel(analyticsService.getOLSubjectsEleven(student));


        List<OLSubjectPrediction> olSubjectPredictions = new ArrayList<>();

        double termMark = 0.0;

        Iterator<ClassroomSubject> olsubjectIterator = oLSubjects.iterator();
        Iterator<ClassroomSubject> olsubjectElevenIterator = oLSubjectsEleven.iterator();


        while (true) {
            ClassroomSubject olSubject = null;
            ClassroomSubject olsubjectEleven = null;

            if (olsubjectIterator.hasNext() && olsubjectElevenIterator.hasNext()) {
                olSubject = olsubjectIterator.next();
                olsubjectEleven = olsubjectElevenIterator.next();

            } else {

                break;

            }


            for (int term = 1; term <= 3; term++) {
                termMark = analyticsService.getTermMarksForOLSub(this.student, olSubject, term);
                if (termMark >= 0 && termMark <= 100) {
                    termMarks.add(termMark);
                } else {
                    termMarks.add(-0.1);
                }

            }


            for (int term = 1; term <= 3; term++) {
                termMark = analyticsService.getTermMarksForOLSub(this.student, olsubjectEleven, term);
                if (termMark >= 0 && termMark <= 100) {
                    termMarks.add(termMark);
                } else {
                    termMarks.add(-0.1);
                }

            }

            OLSubjectPrediction olSubjectPrediction = new OLSubjectPrediction();
            olSubjectPrediction.setOlSubject(olSubject);
            olSubjectPrediction.setTermMarks(termMarks);
            createLinearModelTermMarks(olSubjectPrediction);


            olSubjectPrediction.setLinearModelTermMarks(linearModelTermMarks);

            olSubjectPredictions.add(olSubjectPrediction);

        }

        this.olSubjectPredictions = new ListDataModel<OLSubjectPrediction>(olSubjectPredictions);


        return true;
    }


}

