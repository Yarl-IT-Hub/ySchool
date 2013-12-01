package org.yarlithub.yschool.analytics.core;

//import com.arima.engine.*;

import com.arima.classanalyzer.analyzer.ProfileMatcher;
import org.hibernate.Criteria;
import org.hibernate.criterion.Restrictions;
import org.yarlithub.yschool.repository.model.obj.yschool.*;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 9/22/13
 * Time: 9:05 AM
 * To change this template use File | Settings | File Templates.
 */
public class YAnalyzer {

    public static List<StudentGeneralexamProfile> theList = new ArrayList<StudentGeneralexamProfile>();
    DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

    public static void main(String[] args) {
        List<String> subjects = new ArrayList<String>();
        List<Integer> marks = new ArrayList<Integer>();
        ArrayList<Integer> addmissionNoList = new ArrayList<Integer>();


        subjects.add("SAIVISM");
        subjects.add("MATHEMATICS");
        subjects.add("SCIENCE AND TECHNOLOGY");
        subjects.add("TAMIL LANGUAGE");
        subjects.add("ENGLISH LANGUAGE");
        subjects.add("HISTORY");
       /* subjects.add("INFORMATION AND COMMUNICATION TECHNOLOGY");
        subjects.add("BUSSINESS AND ACCOUNTING");*/


        marks.add(50);
        marks.add(50);
        marks.add(50);
        marks.add(50);
        marks.add(50);
        marks.add(50);
        /*marks.add(50);
        marks.add(50);*/


        try {
            addmissionNoList = (ArrayList<Integer>) ProfileMatcher.getNearestLocalProfiles(11086, 11, 3, subjects, marks);
            //    System.out.println(addmissionNoList);

            Iterator<Integer> adminNoIterator = addmissionNoList.iterator();
            while (adminNoIterator.hasNext()) {

                int admissionNumber = adminNoIterator.next();


                //      System.out.println(admissionNumber);


            }


        } catch (Exception e) {


        }

//        YAnalyzer yAnalyzer=new YAnalyzer();
//        yAnalyzer.getOLSubjectsMain();
//
//        System.out.println(theList);

    }

    public void getOLSubjectsMain() {
        Student student;
        List<Student> studentList = new ArrayList<>();
        Criteria studentCR = dataLayerYschool.createCriteria(Student.class);
        studentCR.add(Restrictions.eq("admissionNo", String.valueOf(18746)));                        //String.valueOf(admissionNo)
        studentList = studentCR.list();
        /*The admission is unique thus the number of students retured should be one */
//        if (studentList.size() == 1) {
        student = studentList.get(0);
        // }


        Criteria studentGeneralExamProfiles = dataLayerYschool.createCriteria(StudentGeneralexamProfile.class);

        studentGeneralExamProfiles.createAlias("admissionNo", "adNo");

        studentGeneralExamProfiles.add(Restrictions.eq("adNo", 18746));
        List<StudentGeneralexamProfile> adProfiles = studentGeneralExamProfiles.list();

        Criteria classroomSubjectCR = dataLayerYschool.createCriteria(ClassroomSubject.class);
        //student_classroom_subject data is not ready yet
        //classroomSubjectCR.createAlias("studentClassroomSubjects", "stclsu").createAlias("stclsu.classroomStudentIdclassroomStudent", "clst").add(Restrictions.eq("clst.studentIdstudent", student));
        //so using this as temporary ,but this may violate optional subject...


        classroomSubjectCR.createAlias("classroomIdclass", "cl").createAlias("cl.classroomStudents", "clst").add(Restrictions.eq("clst.studentIdstudent", student));

        // classroomSubjectCR.add(Restrictions.eq("cl.grade", 10));
        List<ClassroomSubject> lt = classroomSubjectCR.list();
        theList = adProfiles;


    }

    public List<ClassroomSubject> getOLSubjects(Student student) {

        Criteria classroomSubjectCR = dataLayerYschool.createCriteria(ClassroomSubject.class);
        //student_classroom_subject data is not ready yet
        //classroomSubjectCR.createAlias("studentClassroomSubjects", "stclsu").createAlias("stclsu.classroomStudentIdclassroomStudent", "clst").add(Restrictions.eq("clst.studentIdstudent", student));
        //so using this as temporary ,but this may violate optional subject...
        classroomSubjectCR.createAlias("classroomIdclass", "cl").createAlias("cl.classroomStudents", "clst").add(Restrictions.eq("clst.studentIdstudent", student));

        classroomSubjectCR.add(Restrictions.eq("cl.grade", 10));
        List<ClassroomSubject> lt = classroomSubjectCR.list();

        return lt;
    }

    public List<ClassroomSubject> getOLSubjectsGradeEleven(Student student) {

        Criteria classroomSubjectCR = dataLayerYschool.createCriteria(ClassroomSubject.class);
        //student_classroom_subject data is not ready yet
        //classroomSubjectCR.createAlias("studentClassroomSubjects", "stclsu").createAlias("stclsu.classroomStudentIdclassroomStudent", "clst").add(Restrictions.eq("clst.studentIdstudent", student));
        //so using this as temporary ,but this may violate optional subject...
        classroomSubjectCR.createAlias("classroomIdclass", "cl").createAlias("cl.classroomStudents", "clst").add(Restrictions.eq("clst.studentIdstudent", student));

        classroomSubjectCR.add(Restrictions.eq("cl.grade", 11));
        List<ClassroomSubject> lt = classroomSubjectCR.list();

        return lt;
    }

    public List<ClassroomSubject> getALSubjects(Student student) {

        Criteria classroomCR = dataLayerYschool.createCriteria(Classroom.class);
        classroomCR.add(Restrictions.eq("grade", 13));
        classroomCR.createAlias("classroomStudents", "clst").add(Restrictions.eq("clst.studentIdstudent", student));
        List<Classroom> classroomList = classroomCR.list();
        if (classroomList.get(0).getDivision().equalsIgnoreCase("Unknown")) {
            /*al stream unknown students*/
            return null;
        } else {
            if (classroomList.get(0).getDivision().equalsIgnoreCase("Arts")) {
             /*arts stram have to check optional subjects*/
                Criteria classroomSubjectCR = dataLayerYschool.createCriteria(ClassroomSubject.class);
                //student_classroom_subject data is only available for arts AL stidents
                classroomSubjectCR.createAlias("studentClassroomSubjects", "stclsu").createAlias("stclsu.classroomStudentIdclassroomStudent", "clst").add(Restrictions.eq("clst.studentIdstudent", student));
                //so using this as temporary ,but this may violate optional subject...
                classroomSubjectCR.createAlias("classroomIdclass", "cl").add(Restrictions.eq("cl.grade", 13));
                List<ClassroomSubject> lt = classroomSubjectCR.list();
                return lt;
            } else {
                 /*AL maths, com, bio students only 3 compulsury subjects*/
                Criteria classroomSubjectCR = dataLayerYschool.createCriteria(ClassroomSubject.class);
                //student_classroom_subject data is not ready yet
                //classroomSubjectCR.createAlias("studentClassroomSubjects", "stclsu").createAlias("stclsu.classroomStudentIdclassroomStudent", "clst").add(Restrictions.eq("clst.studentIdstudent", student));
                //so using this as temporary ,but this may violate optional subject...
                classroomSubjectCR.createAlias("classroomIdclass", "cl").createAlias("cl.classroomStudents", "clst").add(Restrictions.eq("clst.studentIdstudent", student));

                classroomSubjectCR.add(Restrictions.eq("cl.grade", 13));
                List<ClassroomSubject> lt = classroomSubjectCR.list();

                return lt;
            }
        }


    }

    public List<Integer> getNeighbours() {

        List<String> subjects = new ArrayList<String>();
        List<Integer> marks = new ArrayList<Integer>();
        List<Integer> addmissionNoList = new ArrayList<Integer>();

        subjects.add("SAIVISM");
        subjects.add("MATHEMATICS");
        subjects.add("SCIENCE AND TECHNOLOGY");
        subjects.add("TAMIL LANGUAGE");
        subjects.add("ENGLISH LANGUAGE");
        subjects.add("HISTORY");
        /*subjects.add("INFORMATION AND COMMUNICATION TECHNOLOGY");
        subjects.add("BUSSINESS AND ACCOUNTING");*/


        marks.add(90);
        marks.add(90);
        marks.add(90);
        marks.add(90);
        marks.add(90);
        marks.add(90);
       /* marks.add(90);
        marks.add(90);*/


        try {
            addmissionNoList = ProfileMatcher.getNearestLocalProfiles(11086, 11, 3, subjects, marks);
            return addmissionNoList;

        } catch (Exception e) {
            return null;

        }

    }

    public String getALSubjectsResult(Student student, ClassroomSubject classroomSubject) {


        Criteria resultCR = dataLayerYschool.createCriteria(Results.class);
        resultCR.add(Restrictions.eq("studentIdstudent", student));
        resultCR.createAlias("examIdexam", "ex").add(Restrictions.eq("ex.classroomSubjectIdclassroomSubject", classroomSubject));
        List<Results> resultList = resultCR.list();
        if (resultList.size() > 0) {
            return resultList.get(0).getResults();
        }
        return "not available";
    }

    public String getOLSubjectsResult(Student student, ClassroomSubject classroomSubject) {


        Criteria marksCR = dataLayerYschool.createCriteria(Marks.class);
        marksCR.add(Restrictions.eq("studentIdstudent", student));
        marksCR.createAlias("examIdexam", "ex").add(Restrictions.eq("ex.classroomSubjectIdclassroomSubject", classroomSubject));
        List<Marks> resultList = marksCR.list();
        if (resultList.size() > 0) {
            return String.valueOf(resultList.get(0).getMarks());
        }
        return "not available";
    }

    public int getStudentIslandRank(Student student) {
        Criteria islsndrankCR = dataLayerYschool.createCriteria(StudentGeneralexamProfile.class);
        islsndrankCR.add(Restrictions.eq("studentIdstudent", student));
        List<StudentGeneralexamProfile> studentGeneralexamProfile = islsndrankCR.list();

        return studentGeneralexamProfile.get(0).getAlIslandRank();

    }

    public double getStudentZscore(Student student) {
        Criteria zScore = dataLayerYschool.createCriteria(StudentGeneralexamProfile.class);
        zScore.add(Restrictions.eq("studentIdstudent", student));
        List<StudentGeneralexamProfile> studentGeneralexamProfile = zScore.list();
        return studentGeneralexamProfile.get(0).getZscore();

    }

    public String checkStream(Student student) {

        Criteria classRoomStudent = dataLayerYschool.createCriteria(ClassroomStudent.class);
        classRoomStudent.add(Restrictions.eq("studentIdstudent", student));
        classRoomStudent.createAlias("classroomIdclass", "class").add(Restrictions.eq("class.grade", 13));


        List<ClassroomStudent> classRoomStudents = classRoomStudent.list();
        return classRoomStudents.get(0).getClassroomIdclass().getDivision();
    }

    public double getTermMarksForOLSub(Student student, ClassroomSubject classroomSubject, int term) {
        Criteria marks = dataLayerYschool.createCriteria(Marks.class);
        marks.add(Restrictions.eq("studentIdstudent", student));
        marks.createAlias("examIdexam", "examId").add(Restrictions.eq("examId.classroomSubjectIdclassroomSubject", classroomSubject));

        marks.add(Restrictions.eq("examId.term", term));
        List<Marks> marksList = marks.list();
        if (marksList == null) {
            return -1;
        } else if (marksList.isEmpty()) {
            return -1;
        } else {
            if (marksList.get(0).getMarks() == null) {
                return -1;
            }


        }
        return marksList.get(0).getMarks();
    }

}


//    public List getOLSubjects(Student student){
//        DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();
//        Criteria c1=dataLayerYschool.createCriteria(ClassroomStudent.class).createCriteria("");
//        c1.add(Restrictions.eq("studentIdstudent",student));
//        List<ClassroomStudent> classroomStudents = c1.list();
//
//        Iterator<ClassroomStudent> classroomStudentIterator =classroomStudents.iterator();
//        while(classroomStudentIterator.hasNext()){
//            Hibernate.initialize(classroomStudentIterator.next().getStudentClassroomSubjects());
//            Hibernate.initialize((classroomStudentIterator.next().getClassroomIdclass()));
//                 }
//  List<Classroom> lt= st.createAlias("classroom_student","clsu").createAlias("student","st").add(Restrictions.eq("st.idstudent","1")).list();
//          return null;
//
//    }
