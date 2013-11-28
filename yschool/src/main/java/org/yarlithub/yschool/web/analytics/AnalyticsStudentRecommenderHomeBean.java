package org.yarlithub.yschool.web.analytics;

import org.primefaces.model.chart.CartesianChartModel;
import org.primefaces.model.chart.ChartSeries;
import org.primefaces.model.chart.PieChartModel;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.analytics.core.YAnalyzer;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.AnalyticsService;
import org.yarlithub.yschool.service.StudentService;

import javax.faces.bean.ManagedBean;
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
public class AnalyticsStudentRecommenderHomeBean implements Serializable {
    @Autowired
    private StudentService studentService;
    @Autowired
    private AnalyticsService analyticsService;
    @Autowired
    private AnalyticsController analyticsController;
    private Student student;
    private PieChartModel pieModelSubject;
    private PieChartModel pieModelBio;
    private PieChartModel pieModelMaths;
    private PieChartModel pieModelCom;
    private PieChartModel pieModelArts;
    private PieChartModel pieModelSuccess;
    private PieChartModel pieModelStreamSuccess;
    private CartesianChartModel categoryModelStreamSuccessFailure;
    private ListDataModel<Student> matchingStudentProfiles;
    private int bio;
    private int bio_f;
    private int bio_s;
    private int math_s;
    private int math_f;
    private int math;
    private int com;
    private int com_s;
    private int com_f;
    private int arts;
    private int art_s;
    private int art_f;
    /*http://www.admission.ugc.ac.lk/ */
    private double Arts_min_Jaffna = 1.2499;
    private double Com_min_Jaffna = 0.3908;
    private double Bio_Sci_min_Jaffna = 0.3207;
    private double Phy_Sci_min_Jaffna = 0.1165;

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public StudentService getStudentService() {
        return studentService;
    }

    public void setStudentService(StudentService studentService) {
        this.studentService = studentService;
    }

    public AnalyticsService getAnalyticsService() {
        return analyticsService;
    }

    public void setAnalyticsService(AnalyticsService analyticsService) {
        this.analyticsService = analyticsService;
    }

    public AnalyticsController getAnalyticsController() {
        return analyticsController;
    }

    public void setAnalyticsController(AnalyticsController analyticsController) {
        this.analyticsController = analyticsController;
    }

    public ListDataModel<Student> getMatchingStudentProfiles() {
        return matchingStudentProfiles;
    }

    public void setMatchingStudentProfiles(ListDataModel<Student> matchingStudentProfiles) {
        this.matchingStudentProfiles = matchingStudentProfiles;
    }

    public int getBio() {
        return bio;
    }

    public void setBio(int bio) {
        this.bio = bio;
    }

    public int getBio_f() {
        return bio_f;
    }

    public void setBio_f(int bio_f) {
        this.bio_f = bio_f;
    }

    public int getBio_s() {
        return bio_s;
    }

    public void setBio_s(int bio_s) {
        this.bio_s = bio_s;
    }

    public int getMath_s() {
        return math_s;
    }

    public void setMath_s(int math_s) {
        this.math_s = math_s;
    }

    public int getMath_f() {
        return math_f;
    }

    public void setMath_f(int math_f) {
        this.math_f = math_f;
    }

    public int getMath() {
        return math;
    }

    public void setMath(int math) {
        this.math = math;
    }

    public int getCom() {
        return com;
    }

    public void setCom(int com) {
        this.com = com;
    }

    public int getCom_s() {
        return com_s;
    }

    public void setCom_s(int com_s) {
        this.com_s = com_s;
    }

    public int getCom_f() {
        return com_f;
    }

    public void setCom_f(int com_f) {
        this.com_f = com_f;
    }

    public int getArts() {
        return arts;
    }

    public void setArts(int arts) {
        this.arts = arts;
    }

    public int getArt_s() {
        return art_s;
    }

    public void setArt_s(int art_s) {
        this.art_s = art_s;
    }

    public int getArt_f() {
        return art_f;
    }

    public void setArt_f(int art_f) {
        this.art_f = art_f;
    }

    public PieChartModel getPieModelSubject() {
        this.createPieModelSubject();
        return pieModelSubject;
    }

    public void setPieModelSubject(PieChartModel pieModelSubject) {
        this.pieModelSubject = pieModelSubject;
    }

    private void createPieModelSubject() {
        pieModelSubject = new PieChartModel();

        pieModelSubject.set("Arts", this.getArts());
        pieModelSubject.set("Biology", this.getBio());
        pieModelSubject.set("Maths", this.getMath());
        pieModelSubject.set("Commerce", this.getCom());
    }

//    public PieChartModel getPieModelSuccess() {
//        this.createPieModelSuccess();
//        return pieModelSuccess;
//    }
//
//    public void setPieModelSuccess(PieChartModel pieModelSuccess) {
//        this.pieModelSuccess = pieModelSuccess;
//    }
//
//    private void createPieModelSuccess() {
//        pieModelSuccess = new PieChartModel();
//
//        pieModelSuccess.set("Arts_Success", this.getArt_s());
//        pieModelSuccess.set("Biology__Success", this.getBio_s());
//        pieModelSuccess.set("Maths__Success", this.getMath_s());
//        pieModelSuccess.set("Commerce__Success", this.getCom_s());
//    }

    public void createPieModelBio() {
        pieModelBio = new PieChartModel();

//        pieModelBio.set("Biological_Science_Failure", this.getBio_f());
//        pieModelBio.set("Biological_Science_Success", this.getBio_s());

        pieModelBio.set("Failure", this.getBio_f());
        pieModelBio.set("Success", this.getBio_s());

    }

    public void createPieModelMaths() {
        pieModelMaths = new PieChartModel();

        pieModelMaths.set("Failure", this.getMath_f());
        pieModelMaths.set("Success", this.getMath_s());

//        pieModelMaths.set("Physical_Science_Failure", this.getMath_f());
//        pieModelMaths.set("Physical_Science_Success", this.getMath_s())

    }

    public void createPieModelCom() {
        pieModelCom = new PieChartModel();

//        pieModelCom.set("Commerce_Failure", this.getCom_f());
//        pieModelCom.set("Commerce_Success", this.getCom_s());
        pieModelCom.set("Failure", this.getCom_f());
        pieModelCom.set("Success", this.getCom_s());
    }

    public void createPieModelArts() {
        pieModelArts = new PieChartModel();

//        pieModelArts.set("Arts_Failure", this.getArt_f());
//        pieModelArts.set("Arts_Success", this.getArt_s());
        pieModelArts.set("Failure", this.getArt_f());
        pieModelArts.set("Success", this.getArt_s());

    }

    public CartesianChartModel getCategoryModelStreamSuccessFailure() {
        createCategoryModel();
        return categoryModelStreamSuccessFailure;
    }

    public void setCategoryModelStreamSuccessFailure(CartesianChartModel categoryModelStreamSuccessFailure) {
        this.categoryModelStreamSuccessFailure = categoryModelStreamSuccessFailure;
    }

    private void createCategoryModel() {
        categoryModelStreamSuccessFailure = new CartesianChartModel();

        ChartSeries boys = new ChartSeries();
        boys.setLabel("Successful");

        boys.set("2004", 120);
        boys.set("2005", 100);
        boys.set("2006", 44);
        boys.set("2007", 150);
        boys.set("2008", 25);

        ChartSeries girls = new ChartSeries();
        girls.setLabel("Failure");

        girls.set("2004", 52);
        girls.set("2005", 60);
        girls.set("2006", 110);
        girls.set("2007", 135);
        girls.set("2008", 120);

        categoryModelStreamSuccessFailure.addSeries(boys);
        categoryModelStreamSuccessFailure.addSeries(girls);
    }

    public String navigateToStreamProfiles_Arts() {


        analyticsController.setProfileStream("Arts");
        return "StreamProfiles";
    }

    public String navigateToStreamProfiles_Commerce() {
        analyticsController.setProfileStream("Commerce");
        return "StreamProfiles";
    }

    public String navigateToStreamProfiles_Maths() {
        analyticsController.setProfileStream("Maths");
        return "StreamProfiles";
    }

    public String navigateToStreamProfiles_Science() {
        analyticsController.setProfileStream("Science");
        return "StreamProfiles";
    }

    public boolean preloadStudent() {
        this.art_s = 0;
        this.arts = 0;
        this.art_f = 0;
        this.com = 0;
        this.com_s = 0;
        this.com_f = 0;
        this.math = 0;
        this.math_s = 0;
        this.math_f = 0;
        this.bio_f = 0;
        this.bio_s = 0;
        this.bio = 0;


        YAnalyzer yAnalyzer = new YAnalyzer();
        List<Integer> admissionNoList = new ArrayList<>();
        admissionNoList = yAnalyzer.getNeighbours();

        this.matchingStudentProfiles = new ListDataModel(analyticsService.getStudentByAdmissionNumber(admissionNoList));


        Iterator<Student> matchingStudentProfileIterator = this.matchingStudentProfiles.iterator();

        while (matchingStudentProfileIterator.hasNext()) {
            student = matchingStudentProfileIterator.next();

            String stream = analyticsService.checkStream(student);

            if (stream.contentEquals("Arts")) {
                arts += 1;
                if (analyticsService.getStudentzScore(student) > this.Arts_min_Jaffna) {
                    art_s += 1;
                } else {
                    art_f += 1;
                }


            } else if (stream.contentEquals("Commerce")) {
                com += 1;
                if (analyticsService.getStudentzScore(student) > this.Com_min_Jaffna) {
                    com_s += 1;
                } else {
                    com_f += 1;
                }


            } else if (stream.contentEquals("Physical Science")) {
                math += 1;
                if (analyticsService.getStudentzScore(student) > this.Phy_Sci_min_Jaffna) {
                    math_s += 1;
                } else {
                    math_f += 1;
                }


            } else if (stream.contentEquals("Biological Science")) {
                bio += 1;
                if (analyticsService.getStudentzScore(student) > this.Bio_Sci_min_Jaffna) {
                    bio_s += 1;
                } else {
                    bio_f += 1;
                }


            }

        }


        return true;
    }

    public PieChartModel getPieModelBio() {
        createPieModelBio();
        return pieModelBio;
    }

    public void setPieModelBio(PieChartModel pieModelBio) {
        this.pieModelBio = pieModelBio;
    }

    public PieChartModel getPieModelMaths() {
        createPieModelMaths();
        return pieModelMaths;
    }

    public void setPieModelMaths(PieChartModel pieModelMaths) {

        this.pieModelMaths = pieModelMaths;
    }

    public PieChartModel getPieModelCom() {
        createPieModelCom();
        return pieModelCom;
    }

    public void setPieModelCom(PieChartModel pieModelCom) {
        this.pieModelCom = pieModelCom;
    }

    public PieChartModel getPieModelArts() {
        createPieModelArts();
        return pieModelArts;
    }

    public void setPieModelArts(PieChartModel pieModelArts) {
        this.pieModelArts = pieModelArts;
    }
}



