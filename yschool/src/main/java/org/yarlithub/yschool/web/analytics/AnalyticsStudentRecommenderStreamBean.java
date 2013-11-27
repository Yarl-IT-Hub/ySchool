package org.yarlithub.yschool.web.analytics;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.analytics.core.YAnalyzer;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.service.AnalyticsService;
import org.yarlithub.yschool.service.StudentService;

import javax.faces.bean.ManagedBean;
import javax.faces.model.DataModel;
import javax.faces.model.ListDataModel;
import java.io.Serializable;
import java.util.ArrayList;
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
public class AnalyticsStudentRecommenderStreamBean implements Serializable {
    @Autowired
    private StudentService studentService;
    @Autowired
    private AnalyticsService analyticsService;
    @Autowired
    private AnalyticsController analyticsController;
    private Student student;
    private DataModel<Student> matchingStudentProfiles;
    private DataModel<Student> matchingStudentGeneralExamProfiles;
    private DataModel<Student> matchingStudentProfiles_arts;
    private DataModel<Student> matchingStudentProfiles_commerce;
    private DataModel<Student> matchingStudentProfiles_maths;
    private DataModel<Student> matchingStudentProfiles_science;

    public Student getStudent() {
        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public DataModel<Student> getMatchingStudentProfiles() {
        return matchingStudentProfiles;
    }

    public void setMatchingStudentProfiles(DataModel<Student> matchingStudentProfiles) {
        this.matchingStudentProfiles = matchingStudentProfiles;
    }


   /* private void distributeProfiles() {


        Iterator<Student> matchingProfilesIterator = matchingStudentProfiles.iterator();
        while (matchingProfilesIterator.hasNext()) {

           Student matchingProfile = matchingProfilesIterator.next();
            matchingProfile.get

            Hibernate.initialize(student);

        }
    }    */

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

    public DataModel<Student> getMatchingStudentProfiles_arts() {
        return matchingStudentProfiles_arts;
    }

    public void setMatchingStudentProfiles_arts(DataModel<Student> matchingStudentProfiles_arts) {
        this.matchingStudentProfiles_arts = matchingStudentProfiles_arts;
    }

    public DataModel<Student> getMatchingStudentProfiles_commerce() {
        return matchingStudentProfiles_commerce;
    }

    public void setMatchingStudentProfiles_commerce(DataModel<Student> matchingStudentProfiles_commerce) {
        this.matchingStudentProfiles_commerce = matchingStudentProfiles_commerce;
    }

    public DataModel<Student> getMatchingStudentProfiles_maths() {
        return matchingStudentProfiles_maths;
    }

    public void setMatchingStudentProfiles_maths(DataModel<Student> matchingStudentProfiles_maths) {
        this.matchingStudentProfiles_maths = matchingStudentProfiles_maths;
    }

    public DataModel<Student> getMatchingStudentProfiles_science() {
        return matchingStudentProfiles_science;
    }

    public void setMatchingStudentProfiles_science(DataModel<Student> matchingStudentProfiles_science) {
        this.matchingStudentProfiles_science = matchingStudentProfiles_science;
    }

    public DataModel<Student> getMatchingStudentGeneralExamProfiles() {
        return matchingStudentGeneralExamProfiles;
    }

    public void setMatchingStudentGeneralExamProfiles(DataModel<Student> matchingStudentGeneralExamProfiles) {
        this.matchingStudentGeneralExamProfiles = matchingStudentGeneralExamProfiles;
    }

    public boolean preloadProfiles() {
        YAnalyzer yAnalyzer = new YAnalyzer();
        List<Integer> admissionNoList = new ArrayList<>();
        admissionNoList = yAnalyzer.getNeighbours();

        this.matchingStudentProfiles = new ListDataModel(analyticsService.getStudentByAdmissionNumber(admissionNoList));
        this.matchingStudentGeneralExamProfiles = new ListDataModel(analyticsService.getStudentGeneralExamProfileByStudentList(matchingStudentProfiles));


        return true;
    }
}
