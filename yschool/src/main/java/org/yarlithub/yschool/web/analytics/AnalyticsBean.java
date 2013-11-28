package org.yarlithub.yschool.web.analytics;

import net.sf.jasperreports.engine.JRException;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.analytics.core.MatchingStudentProfile;
import org.yarlithub.yschool.analytics.core.SubjectResult;
import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomSubject;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.AnalyticsService;

import javax.faces.bean.ManagedBean;
import javax.faces.context.FacesContext;
import javax.faces.model.ListDataModel;
import javax.servlet.ServletOutputStream;
import javax.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.io.Serializable;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import org.primefaces.model.chart.CartesianChartModel;
import org.primefaces.model.chart.LineChartSeries;


/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */

@ManagedBean
@Scope(value = "session")
@Controller
public class AnalyticsBean implements Serializable {


    @Autowired
    private AnalyticsService analyticsService;
    private Student student;

    private CartesianChartModel linearModel;
    private MatchingStudentProfile matchingStudentProfile;

    public MatchingStudentProfile getMatchingStudentProfile() {
        return matchingStudentProfile;
    }

    public void setMatchingStudentProfile(MatchingStudentProfile matchingStudentProfile) {
        this.matchingStudentProfile = matchingStudentProfile;
    }

    public AnalyticsBean(){
        createLinearModel();
    }

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public boolean preloadStudent() {
        this.setStudent(analyticsService.getStudent());
        matchingStudentProfile = new MatchingStudentProfile(this.getStudent());
        matchingStudentProfile.setOlSubjects(new ListDataModel<ClassroomSubject>(analyticsService.getOLSubjects(this.getStudent())));

       // matchingStudentProfile.setAlSubjects(new ListDataModel<ClassroomSubject>(analyticsService.getALSubjects(this.getStudent())));
        List<SubjectResult> subjectResultList = new ArrayList<>();
        List<ClassroomSubject> classroomSubjectList = analyticsService.getALSubjects(this.getStudent());
        Iterator<ClassroomSubject> classroomSubjectIterator = classroomSubjectList.iterator();
        while (classroomSubjectIterator.hasNext()){
            ClassroomSubject classroomSubject = classroomSubjectIterator.next();
            String result =  analyticsService.getALSubjectsResult(this.getStudent(), classroomSubject) ;
            SubjectResult subjectResult = new SubjectResult(classroomSubject,result);
            subjectResultList.add(subjectResult);

        }
        matchingStudentProfile.setAlSubjects(new ListDataModel<SubjectResult>(subjectResultList));

        return true;

    }
    public CartesianChartModel getLinearModel() {

        return linearModel;
    }


        public void printReport() throws IOException, JRException {
            HttpServletResponse httpServletResponse = (HttpServletResponse) FacesContext.getCurrentInstance().getExternalContext().getResponse();
            httpServletResponse.addHeader("Content-disposition", "attachment; filename=report.pdf");
            ServletOutputStream servletOutputStream = httpServletResponse.getOutputStream();

            analyticsService.printReport(servletOutputStream);
            FacesContext.getCurrentInstance().responseComplete();
        }


    private void createLinearModel() {
        linearModel = new CartesianChartModel();

        LineChartSeries series1 = new LineChartSeries();
        series1.setLabel("Series 1");

        series1.set(1, 2);
        series1.set(2, 1);
        series1.set(3, 3);
        series1.set(4, 6);
        series1.set(5, 8);

        LineChartSeries series2 = new LineChartSeries();
        series2.setLabel("Series 2");
        series2.setMarkerStyle("diamond");

        series2.set(1, 6);
        series2.set(2, 3);
        series2.set(3, 2);
        series2.set(4, 7);
        series2.set(5, 9);

        linearModel.addSeries(series1);
        linearModel.addSeries(series2);
    }

}
