package org.yarlithub.yschool.analytics.core;

//import com.arima.engine.*;

import org.hibernate.Criteria;
import org.hibernate.criterion.Restrictions;
import org.yarlithub.yschool.repository.model.obj.yschool.ClassroomSubject;
import org.yarlithub.yschool.repository.model.obj.yschool.Student;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 9/22/13
 * Time: 9:05 AM
 * To change this template use File | Settings | File Templates.
 */
public class YAnalyzer {

    DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

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
}
