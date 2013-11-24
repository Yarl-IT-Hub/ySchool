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
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
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

    public boolean preloadProfiles() {
        YAnalyzer yAnalyzer = new YAnalyzer();
        List<Integer> admissionNoList = null;
        admissionNoList = yAnalyzer.getNeighbours();

        this.matchingStudentProfiles = new ListDataModel(analyticsService.getStudentByAdmissionNumber(admissionNoList));
        //  distributeProfiles();

        return true;
    }
   /* private void distributeProfiles() {


        Iterator<Student> matchingProfilesIterator = matchingStudentProfiles.iterator();
        while (matchingProfilesIterator.hasNext()) {

           Student matchingProfile = matchingProfilesIterator.next();
            matchingProfile.get

            Hibernate.initialize(student);

        }
    }    */


}
