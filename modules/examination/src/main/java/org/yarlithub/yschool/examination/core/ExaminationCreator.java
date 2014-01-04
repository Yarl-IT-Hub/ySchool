package org.yarlithub.yschool.examination.core;

import org.hibernate.Criteria;
import org.hibernate.criterion.Restrictions;
import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
import org.yarlithub.yschool.repository.model.obj.yschool.*;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;

import java.util.*;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 9/22/13
 * Time: 9:05 AM
 * To change this template use File | Settings | File Templates.
 */
//TODO: have to change according to subject modules database change.
public class ExaminationCreator {

    DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

    /**
     * @param date      java.util.date
     * @param term      int term 1 or 2 or 3
     * @param examtype  int id as in Exam_Type table in yschool database version1.2
     * @param grade     int grade
     * @param division  Char like A/B/C/D/E/F
     * @param subjectid int id as in Subject table
     * @return exam object if successfully created a CA exam and inserted entries into related database tables, otherwise null.
     */
    public Exam addNewCAExam(Date date, int term, int examtype, int grade, String division, int subjectid) {

        //see database design in data/V1.2/ySchool_ERDiagram_V1.2.png
        //First get the class id from classroom table using available user inputs
        Classroom classroom = getClassId(date, grade, division);
        if (classroom == null) {
            return null;
        }

        //Using class id and subject get the relation table calsssubject id
        Subject subject = dataLayerYschool.getSubject(subjectid);
        ClassroomModule classSubject = getClassroomSubject(classroom, subject);
        if (classSubject == null) {
            return null;
        }

        //Finally enter an exam table entry.
        ExamType examType = dataLayerYschool.getExamType(examtype);
        Exam exam = insertExam(date, term, examType, classSubject);
        if (exam.getId() > 0) {
            /* if SQL error in insert exam then the exam.id remains 0,
             * if success returns the auto-generated id */
            return exam;
        }
        return null;
    }

    /**
     * @param date      java.util.date
     * @param term      int term 1 or 2 or 3
     * @param examtype  int id as in Exam_Type table in yschool database version1.2
     * @param grade     int grade
     * @param subjectid int id as in Subject table
     * @return for each divisions of classroom, checks if the subject is provided and add a term exam entry per class division
     * and if successful and inserted entries into related database tables return list of exams, otherwise null.
     */
    public List<Exam> addNewTermExam(Date date, int term, int examtype, int grade, int subjectid) {

        List<Exam> examList = new ArrayList<Exam>();

        ExamType examType = dataLayerYschool.getExamType(examtype);
        //see database design in data/V1.2/ySchool_ERDiagram_V1.2.png
        //First get the all divisions of classes.
        List<Classroom> classrooms = getClassIds(date, grade);
        if(classrooms ==null){
            return null;
        }
        //for each divisions of classroom entry, check if the subject is provided.
        ListIterator classiter = classrooms.listIterator();
        while (classiter.hasNext()) {
            Classroom classroom = (Classroom) classiter.next();
            Subject subject = dataLayerYschool.getSubject(subjectid);
            ClassroomModule classroomSubject = getClassroomSubject(classroom, subject);
            if (classroomSubject != null) {
                //iteratively enter exams for each divisions.
                examList.add(insertExam(date, term, examType, classroomSubject));
            }
        }
       if(examList.size()>0){
            return examList;
        }
        return null;
    }

    private Exam insertExam(Date date, int term, ExamType examType, ClassroomModule classSubject) {

        Exam exam = YschoolDataPoolFactory.getExam();
        exam.setDate(date);
        exam.setTerm(term);
        exam.setClassroomModuleIdclassroomModule(classSubject);
        exam.setExamTypeIdexamType(examType);
        dataLayerYschool.save(exam);
        dataLayerYschool.flushSession();
        return exam;
    }

    private ClassroomModule getClassroomSubject(Classroom classroom, Subject subject) {

        Criteria getclassCriteria = dataLayerYschool.createCriteria(ClassroomModule.class);
        getclassCriteria.add(Restrictions.eq("classroomIdclass", classroom));
        getclassCriteria.add(Restrictions.eq("subjectIdsubject", subject));
        List<ClassroomModule> list = getclassCriteria.list();
        if (list.size() > 0) {
            int classroomsubjectid = list.get(0).getId();
            ClassroomModule classroomSubject = dataLayerYschool.getClassroomModule(classroomsubjectid);
            return classroomSubject;
        }
        return null;
    }

    private Classroom getClassId(Date date, int grade, String division) {

        Criteria getclassCriteria = dataLayerYschool.createCriteria(Classroom.class);

        List<Classroom> classroomList = new ArrayList<>();
        //year is int for classroom.
        Calendar calendar = Calendar.getInstance();
        calendar.setTime(date);
        int year = calendar.get(Calendar.YEAR);
        getclassCriteria.add(Restrictions.eq("year", year));
        getclassCriteria.add(Restrictions.eq("grade", grade));
        getclassCriteria.add(Restrictions.eq("division", division));
        classroomList = getclassCriteria.list();
        if (classroomList.size() > 0) {
            return classroomList.get(0);
        }
        return null;
    }

    private List<Classroom> getClassIds(Date date, int grade) {

        Criteria getclassCriteria = dataLayerYschool.createCriteria(Classroom.class);
        List<Classroom> classrooms = new ArrayList<>();
        //year is int for classroom.
        Calendar calendar = Calendar.getInstance();
        calendar.setTime(date);
        int year = calendar.get(Calendar.YEAR);
        getclassCriteria.add(Restrictions.eq("year", year));
        getclassCriteria.add(Restrictions.eq("grade", grade));
        classrooms = getclassCriteria.list();
        if (classrooms.size() > 0) {
            return classrooms;
        }
        return null;
    }

}
