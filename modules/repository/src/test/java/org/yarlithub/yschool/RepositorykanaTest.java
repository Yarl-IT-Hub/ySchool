package org.yarlithub.yschool;

import org.hibernate.Criteria;
import org.hibernate.criterion.Restrictions;
import org.junit.Test;
import org.junit.runner.RunWith;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;
import org.springframework.test.context.transaction.TransactionConfiguration;
import org.springframework.transaction.annotation.Transactional;
import org.yarlithub.yschool.repository.model.obj.yschool.ClassAnalyzerClassifier;
import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomSubject;
import org.yarlithub.yschool.repository.model.obj.yschool.Marks;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import java.util.Iterator;
import java.util.List;

/**
 * Tese tests connects with database and transfer data.
 * Before Running tests make sure to import schema and initial data into the database.
 */

@ContextConfiguration(locations = {"/applicationContext.xml"})
@RunWith(SpringJUnit4ClassRunner.class)
@TransactionConfiguration(transactionManager = "transactionManager", defaultRollback = false)
public class RepositorykanaTest {

    @Test
    @Transactional
    public void testPrediction() {

        DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();


        Student student = dataLayerYschool.getStudent(17);



        double re=-222;

        ClassroomSubject classroomSubject = dataLayerYschool.getClassroomSubject(2);
        Criteria marks = dataLayerYschool.createCriteria(Marks.class);
        marks.add(Restrictions.eq("studentIdstudent", student));
        marks.createAlias("examIdexam", "examId").add(Restrictions.eq("examId.classroomSubjectIdclassroomSubject", classroomSubject));

        marks.add(Restrictions.eq("examId.term", 1));
        List<Marks> marksList = marks.list();
        if (marksList == null) {
            re = -1;
        } else if (marksList.isEmpty()) {
            re= -2;
        } else {
            if (marksList.get(0).getMarks() == null) {
                re=-3;
            }
        }
//        if(marksList.get(0)!=null){
//        re=marksList.get(0).getMarks();
//        }













        Criteria classroomSubjectCR = dataLayerYschool.createCriteria(ClassroomSubject.class);
        //student_classroom_subject data is not ready yet
        //classroomSubjectCR.createAlias("studentClassroomSubjects", "stclsu").createAlias("stclsu.classroomStudentIdclassroomStudent", "clst").add(Restrictions.eq("clst.studentIdstudent", student));
        //so using this as temporary ,but this may violate optional subject...
        classroomSubjectCR.createAlias("classroomIdclass", "cl").createAlias("cl.classroomStudents", "clst").add(Restrictions.eq("clst.studentIdstudent", student));

        classroomSubjectCR.add(Restrictions.eq("cl.grade", 11));
        List<ClassroomSubject> lt = classroomSubjectCR.list();
        System.out.println("\n\n\n\n");


        System.out.println(lt.size());
        Iterator<ClassroomSubject> iterator=lt.iterator();

        while (iterator.hasNext()) {
            System.out.println("\n" + iterator.next().getSubjectIdsubject().getName());
        }

        System.out.println(lt.iterator().next().getSubjectIdsubject().getName());
        System.out.println(lt.iterator().next().getSubjectIdsubject().getName());
        System.out.println(lt.get(1).getSubjectIdsubject().getName());

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
