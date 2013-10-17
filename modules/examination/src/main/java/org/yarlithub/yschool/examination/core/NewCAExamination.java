package org.yarlithub.yschool.examination.core;

import org.hibernate.Criteria;
import org.hibernate.criterion.Restrictions;
import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
import org.yarlithub.yschool.repository.model.obj.yschool.*;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import java.util.Calendar;
import java.util.Date;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 9/22/13
 * Time: 9:05 AM
 * To change this template use File | Settings | File Templates.
 */
public class NewCAExamination {

    DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

    /**
     * @param date      java.util.date
     * @param term      int term 1 or 2 or 3
     * @param examtype  int id as in Exam_Type table in yschool database version1.2
     * @param grade     int grade
     * @param division  Char like A/B/C/D/E/F
     * @param subjectid int id as in Subject table
     * @return true if successfully created a CA exam and inserted entries into related database tables.
     */
    public boolean addCA(Date date, int term, int examtype, int grade, String division, int subjectid) {

        //see database design in data/V1.2/ySchool_ERDiagram_V1.2.png
        //First get the class id from classroom table using available user inputs
        Classroom classroom = getClassid(date, grade, division);
        if (classroom == null) {
            return false;
        }

        //Using class id and subject get the relation table calsssubject id
        Subject subject = dataLayerYschool.getSubject(subjectid);
        ClassSubject classSubject = getClassSubject(classroom, subject);
        if (classSubject == null) {
            return false;
        }

        //Finally enter an exam table entry.
        ExamType examType = dataLayerYschool.getExamType(examtype);
        int success = insertExam(date, term, examType, classSubject);
        if (success == 1) {
            return true;
        }
        return false;
    }

    private int insertExam(Date date, int term, ExamType examType, ClassSubject classSubject) {

        Exam exam = YschoolDataPoolFactory.getExam();
        exam.setDate(date);
        exam.setTerm(term);
        exam.setClassSubjectIdclassSubject(classSubject);
        exam.setExamTypeIdexamType(examType);
        dataLayerYschool.save(exam);
        dataLayerYschool.flushSession();
        //TODO: save method does not indicates/returns success/failure
        return 1;
    }

    private ClassSubject getClassSubject(Classroom classroom, Subject subject) {

        Criteria getclassCriteria = dataLayerYschool.createCriteria(ClassSubject.class);
        getclassCriteria.add(Restrictions.eq("classIdclass", classroom));
        getclassCriteria.add(Restrictions.eq("subjectIdsubject", subject));
        List<ClassSubject> list = getclassCriteria.list();
        if (list.size() > 0) {
            int classsubjectid = list.get(0).getId();
            ClassSubject classSubject = dataLayerYschool.getClassSubject(classsubjectid);
            return classSubject;
        }
        return null;
    }

    private Classroom getClassid(Date date, int grade, String division) {

        Criteria getclassCriteria = dataLayerYschool.createCriteria(Classroom.class);

            //year is int for classroom.
            Calendar calendar = Calendar.getInstance();
            calendar.setTime(date);
            int year = calendar.get(Calendar.YEAR);
        getclassCriteria.add(Restrictions.eq("year", year));
        getclassCriteria.add(Restrictions.eq("grade", grade));
        getclassCriteria.add(Restrictions.eq("division", division));
        List<Classroom> list = getclassCriteria.list();
        if (list.size() > 0) {
            int classroomid = list.get(0).getId();
            Classroom classroom = dataLayerYschool.getClassroom(classroomid);
            return classroom;
        }
        return null;
    }
}
