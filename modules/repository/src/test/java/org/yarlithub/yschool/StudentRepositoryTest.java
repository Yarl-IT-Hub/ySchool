package org.yarlithub.yschool;

import org.junit.Test;
import org.junit.runner.RunWith;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
import org.springframework.test.context.transaction.TransactionConfiguration;
import org.springframework.transaction.annotation.Transactional;

/**
 * Tese tests connects with database and transfer data.
 * Before Running tests make sure to import schema and initial data into the database.
 */

@ContextConfiguration(locations = {"/applicationContext.xml"})
@RunWith(SpringJUnit4ClassRunner.class)
@TransactionConfiguration(transactionManager = "transactionManager", defaultRollback = false)
public class StudentRepositoryTest {

    @Test
    @Transactional
    public void insertStudentTest() {

//        DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();
//
//
//
////        //netsted criteria test for joins : analytics subjectlists    //
////       Criteria st= dataLayerYschool.createCriteria(Subject.class);
////        List<Classroom> lt= st.createAlias("classroom_student","clsu").createAlias("student","st").add(Restrictions.eq("st.idstudent","1")).list();
//        // List<Subject> lt= st.add(Restrictions.eq("name","SAIVISAM")).list();   //
////        System.out.println(lt.get(0).getId());
//
//        //   ClassAnalyzerClassifier s = YschoolDataPoolFactory.getClassAnalyzerClassifier();
//
//        Student student = null;
//        Criteria studentCR = dataLayerYschool.createCriteria(Student.class);
//        studentCR.add(Restrictions.eq("admissionNo", "18746"));                        //String.valueOf(admissionNo)
//        List<Student> studentList = studentCR.list();
//        /*The admission is unique thus the number of students retured should be one */
////        if (studentList.size() == 1) {
//        student = studentList.get(0);
//        System.out.println(student.getName());
//
//
////        Working after data1.0.4
//        //         Exam exam =dataLayerYschool.getExam(1);
////        System.out.println(exam.getId());
////           ExamSync examSync = YschoolDataPoolFactory.getExamSync(null);
////        examSync.setClassIdexam(1);
////        examSync.setSyncStatus(2);
////        examSync.setExamIdexam(exam);
////        dataLayerYschool.save(examSync);
////        dataLayerYschool.flushSession();
////        System.out.println(examSync.getId());
//
//
//        studentList = new ArrayList<>();
//        studentCR = dataLayerYschool.createCriteria(Student.class);
//        studentCR.add(Restrictions.eq("admissionNo", String.valueOf(18746)));                        //String.valueOf(admissionNo)
//        studentList = studentCR.list();
//        /*The admission is unique thus the number of students retured should be one */
////        if (studentList.size() == 1) {
//        student = studentList.get(0);
//        // }
//
//
//        Criteria studentGeneralExamProfiles = dataLayerYschool.createCriteria(StudentGeneralexamProfile.class);
//
//        // studentGeneralExamProfiles.createAlias("admissionNo", "adNo");
//
//        studentGeneralExamProfiles.add(Restrictions.eq("alIslandRank", 18952));
//        List<StudentGeneralexamProfile> adProfiles = studentGeneralExamProfiles.list();
//
//        //  Criteria classroomSubjectCR = dataLayerYschool.createCriteria(ClassroomSubject.class);
//
//
//        //student_classroom_subject data is not ready yet
//        //classroomSubjectCR.createAlias("studentClassroomSubjects", "stclsu").createAlias("stclsu.classroomStudentIdclassroomStudent", "clst").add(Restrictions.eq("clst.studentIdstudent", student));
//        //so using this as temporary ,but this may violate optional subject...
//
//
//        //classroomSubjectCR.createAlias("classroomIdclass", "cl").createAlias("cl.classroomStudents", "clst").add(Restrictions.eq("clst.studentIdstudent", student));
//
//        // classroomSubjectCR.add(Restrictions.eq("cl.grade", 10));
//        //List<ClassroomSubject> lt = classroomSubjectCR.list();
//        System.out.println(adProfiles);
//
//
//        student = dataLayerYschool.getStudent(17);
//
//        Criteria classroomSubjectCR = dataLayerYschool.createCriteria(ClassroomSubject.class);
//        //student_classroom_subject data is not ready yet
//        //classroomSubjectCR.createAlias("studentClassroomSubjects", "stclsu").createAlias("stclsu.classroomStudentIdclassroomStudent", "clst").add(Restrictions.eq("clst.studentIdstudent", student));
//        //so using this as temporary ,but this may violate optional subject...
//        classroomSubjectCR.createAlias("classroomIdclass", "cl").createAlias("cl.classroomStudents", "clst").add(Restrictions.eq("clst.studentIdstudent", student));
//
//        classroomSubjectCR.add(Restrictions.eq("cl.grade", 11));
//        List<ClassroomSubject> lt = classroomSubjectCR.list();
//        System.out.println("\n\n\n\n");
//
//
//        Criteria marks = dataLayerYschool.createCriteria(Marks.class);
//        marks.add(Restrictions.eq("studentIdstudent", student));
//        marks.createAlias("examIdexam", "examId").add(Restrictions.eq("examId.classroomSubjectIdclassroomSubject", lt.get(0)));
//
//        marks.add(Restrictions.eq("examId.term", 3));
//        List<Marks> marksList = marks.list();
//        System.out.println("\n\n\n\n");
//        System.out.println(marksList);

    }
}
