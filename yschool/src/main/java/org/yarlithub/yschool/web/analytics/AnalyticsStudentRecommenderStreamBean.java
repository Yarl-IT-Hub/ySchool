package org.yarlithub.yschool.web.analytics;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;
import org.yarlithub.yschool.analytics.core.MatchingStudentProfile;
import org.yarlithub.yschool.analytics.core.SubjectResult;
import org.yarlithub.yschool.analytics.core.YAnalyzer;
import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomModule;
import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomModule;
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
public class AnalyticsStudentRecommenderStreamBean implements Serializable {
    @Autowired
    private StudentService studentService;
    @Autowired
    private AnalyticsService analyticsService;
    @Autowired
    private AnalyticsController analyticsController;
    private Student student;
    private DataModel<Student> matchingStudentProfiles;
    private DataModel<MatchingStudentProfile> matchingProfilesWithSubRes;
    private DataModel<MatchingStudentProfile> matchingStudentProfileCommerce;
    private DataModel<MatchingStudentProfile> matchingStudentProfileArts;
    private DataModel<MatchingStudentProfile> matchingStudentProfileScience;
    private DataModel<MatchingStudentProfile> matchingStudentProfileMaths;
    private DataModel<MatchingStudentProfile> matchingStudentProfileStream;
    private String currentStream;

    public Student getStudent() {

        return student;
    }

    public void setStudent(Student student) {
        this.student = student;
    }

    public String getCurrentStream() {
        return currentStream;
    }

    public void setCurrentStream(String currentStream) {
        this.currentStream = currentStream;
    }

    public DataModel<MatchingStudentProfile> getMatchingStudentProfileStream() {
        return matchingStudentProfileStream;
    }

    public void setMatchingStudentProfileStream(DataModel<MatchingStudentProfile> matchingStudentProfileStream) {
        this.matchingStudentProfileStream = matchingStudentProfileStream;
    }

    public DataModel<MatchingStudentProfile> getMatchingStudentProfileCommerce() {
        return matchingStudentProfileCommerce;
    }

    public void setMatchingStudentProfileCommerce(DataModel<MatchingStudentProfile> matchingStudentProfileCommerce) {
        this.matchingStudentProfileCommerce = matchingStudentProfileCommerce;
    }

    public DataModel<MatchingStudentProfile> getMatchingStudentProfileArts() {
        return matchingStudentProfileArts;
    }

    public void setMatchingStudentProfileArts(DataModel<MatchingStudentProfile> matchingStudentProfileArts) {
        this.matchingStudentProfileArts = matchingStudentProfileArts;
    }

    public DataModel<MatchingStudentProfile> getMatchingStudentProfileScience() {
        return matchingStudentProfileScience;
    }

    public void setMatchingStudentProfileScience(DataModel<MatchingStudentProfile> matchingStudentProfileScience) {
        this.matchingStudentProfileScience = matchingStudentProfileScience;
    }

    public DataModel<MatchingStudentProfile> getMatchingStudentProfileMaths() {
        return matchingStudentProfileMaths;
    }

    public void setMatchingStudentProfileMaths(DataModel<MatchingStudentProfile> matchingStudentProfileMaths) {
        this.matchingStudentProfileMaths = matchingStudentProfileMaths;
    }

    public DataModel<Student> getMatchingStudentProfiles() {
        return matchingStudentProfiles;
    }

    public void setMatchingStudentProfiles(DataModel<Student> matchingStudentProfiles) {
        this.matchingStudentProfiles = matchingStudentProfiles;
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

    public DataModel<MatchingStudentProfile> getMatchingProfilesWithSubRes() {
        return matchingProfilesWithSubRes;
    }

    public void setMatchingProfilesWithSubRes(DataModel<MatchingStudentProfile> matchingProfilesWithSubRes) {
        this.matchingProfilesWithSubRes = matchingProfilesWithSubRes;
    }

    public boolean preloadProfiles() {


        YAnalyzer yAnalyzer = new YAnalyzer();
        List<Integer> admissionNoList = new ArrayList<>();
        admissionNoList = yAnalyzer.getNeighbours();

        this.matchingStudentProfiles = new ListDataModel(analyticsService.getStudentByAdmissionNumber(admissionNoList));

        // this.matchingStudentGeneralExamProfiles = new ListDataModel(analyticsService.getStudentGeneralExamProfileByStudentList(matchingStudentProfiles));


        // matchingStudentProfile.setAlSubjects(new ListDataModel<ClassroomModule>(analyticsService.getALSubjects(this.getStudent())));
        List<SubjectResult> subjectResultListAL = null;
        List<SubjectResult> subjectResultListOL = null;
        List<MatchingStudentProfile> matchingStudentProfileClass = new ArrayList<>();
        List<MatchingStudentProfile> matchingStudentProfileClassArts = new ArrayList<>();
        List<MatchingStudentProfile> matchingStudentProfileClassCommerce = new ArrayList<>();
        List<MatchingStudentProfile> matchingStudentProfileClassMaths = new ArrayList<>();
        List<MatchingStudentProfile> matchingStudentProfileClassScience = new ArrayList<>();

        List<ClassroomModule> classroomALSubjectList = new ArrayList<>();
        List<ClassroomModule> classroomOLSubjectList = new ArrayList<>();
        Student student = new Student();
        MatchingStudentProfile matchingStudentProfile;


        Iterator<Student> matchingStudentProfileIterator = this.matchingStudentProfiles.iterator();

        while (matchingStudentProfileIterator.hasNext()) {
            subjectResultListAL = new ArrayList<>();
            subjectResultListOL = new ArrayList<>();
            student = matchingStudentProfileIterator.next();
            //student=analyticsService.getStudent();
            matchingStudentProfile = new MatchingStudentProfile(student);
            int islarank = analyticsService.getStudentIslandRank(student);
            double zsocre = analyticsService.getStudentzScore(student);
            matchingStudentProfile.setIslandRank(islarank);
            matchingStudentProfile.setzScore(zsocre);
            classroomALSubjectList = analyticsService.getALSubjects(student);

            if (classroomALSubjectList != null) {
                Iterator<ClassroomModule> classroomALSubjectIterator = classroomALSubjectList.iterator();
                while (classroomALSubjectIterator.hasNext()) {

                    ClassroomModule classroomALSubject = classroomALSubjectIterator.next();
                    String result = analyticsService.getALSubjectsResult(student, classroomALSubject);
                    SubjectResult subjectResult = new SubjectResult(classroomALSubject, result);
                    subjectResultListAL.add(subjectResult);

                }


                classroomOLSubjectList = analyticsService.getOLSubjects(student);


                if (classroomOLSubjectList != null) {
                    Iterator<ClassroomModule> classroomOLSubjectIterator = classroomOLSubjectList.iterator();
                    while (classroomOLSubjectIterator.hasNext()) {

                        ClassroomModule classroomOLSubject = classroomOLSubjectIterator.next();
                        String result = analyticsService.getOLSubjectsResult(student, classroomOLSubject);
                        SubjectResult subjectResult = new SubjectResult(classroomOLSubject, result);
                        subjectResultListOL.add(subjectResult);

                    }
                }

                matchingStudentProfile.setAlSubjects(new ListDataModel<SubjectResult>(subjectResultListAL));
                matchingStudentProfile.setOlSubjects(new ListDataModel<SubjectResult>(subjectResultListOL));
                matchingStudentProfileClass.add(matchingStudentProfile);
                /*check stream*/

                String stream = analyticsService.checkStream(student);

                if (stream.contentEquals("Arts")) {

                    matchingStudentProfileClassArts.add(matchingStudentProfile);
                } else if (stream.contentEquals("Commerce")) {

                    matchingStudentProfileClassCommerce.add(matchingStudentProfile);
                } else if (stream.contentEquals("Physical Science")) {

                    matchingStudentProfileClassMaths.add(matchingStudentProfile);
                } else if (stream.contentEquals("Biological Science")) {

                    matchingStudentProfileClassScience.add(matchingStudentProfile);
                }

            }
        }

        matchingProfilesWithSubRes = new ListDataModel<MatchingStudentProfile>(matchingStudentProfileClass);
        matchingStudentProfileArts = new ListDataModel<MatchingStudentProfile>(matchingStudentProfileClassArts);
        matchingStudentProfileCommerce = new ListDataModel<MatchingStudentProfile>(matchingStudentProfileClassCommerce);
        matchingStudentProfileMaths = new ListDataModel<MatchingStudentProfile>(matchingStudentProfileClassMaths);
        matchingStudentProfileScience = new ListDataModel<MatchingStudentProfile>(matchingStudentProfileClassScience);

        if (analyticsController.getProfileStream().contentEquals("Arts")) {
            this.currentStream = "Arts";
            matchingStudentProfileStream = matchingStudentProfileArts;
        } else if (analyticsController.getProfileStream().contentEquals("Commerce")) {
            this.currentStream = "Commerce";
            matchingStudentProfileStream = matchingStudentProfileCommerce;
        } else if (analyticsController.getProfileStream().contentEquals("Maths")) {
            this.currentStream = "Physical Science";
            matchingStudentProfileStream = matchingStudentProfileMaths;
        } else if (analyticsController.getProfileStream().contentEquals("Science")) {
            this.currentStream = "Biological Science";
            matchingStudentProfileStream = matchingStudentProfileScience;
        }
        return true;
    }
}
