package org.yarlithub.yschool.examination.api;

import org.apache.log4j.Logger;
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
    static Logger log = Logger.getLogger(ExaminationCreator.class);

    static DataLayerYschool dataLayerYschool = DataLayerYschoolImpl.getInstance();

    /**
     * @param date       java.util.date
     * @param term       int term 1 or 2 or 3
     * @param examtype   int id as in Exam_Type table in yschool database version1.2
     * @param gradeid    Grade id
     * @param divisionid Division id
     * @param moduleid   Module id
     * @return exam object if successfully created a CA exam and inserted entries into related database tables, otherwise null.
     */
    public static Exam addNewCAExam(Date date, int term, int examtype, int gradeid, int divisionid, int moduleid) {

        Grade grade = dataLayerYschool.getGrade(gradeid);
        Division division = dataLayerYschool.getDivision(divisionid);
        Module module = dataLayerYschool.getModule(moduleid);
        ExamType examType = dataLayerYschool.getExamType(examtype);

        //see database design in data/V1.2/ySchool_ERDiagram_V1.2.png
        //First get the class id from classroom table using available user inputs
        Classroom classroom = getClassroom(date, grade, division);
        if (classroom == null) {
            return null;
        }

        //Using class id and module get the relation table calsssubject id
        ClassroomModule classroomModule = getClassroomModule(classroom, module);
        if (classroomModule == null) {
            return null;
        }

        //Finally enter an exam table entry.
        Exam exam = insertExam(date, term, examType, classroomModule);
        if (exam.getId() > 0) {
            /* if SQL error in insert exam then the exam.id remains 0,
             * if success returns the auto-generated id */
            return exam;
        }
        return null;
    }

    /**
     * @param date     java.util.date
     * @param term     int term 1 or 2 or 3
     * @param examtype int id as in Exam_Type table in yschool database version1.2
     * @param gradeid  int grade
     * @param moduleid int id as in Subject table
     * @return for each divisions of classroom, checks if the subject is provided and add a term exam entry per class division
     *         and if successful and inserted entries into related database tables return list of exams, otherwise null.
     */
    public static List<Exam> addNewTermExam(Date date, int term, int examtype, int gradeid, int moduleid) {

        Grade grade = dataLayerYschool.getGrade(gradeid);
        Module module = dataLayerYschool.getModule(moduleid);
        ExamType examType = dataLayerYschool.getExamType(examtype);
        List<Exam> examList = new ArrayList();


        //First get the all classrooms in the grade.
        List<Classroom> classrooms = getClassrooms(date, grade);
        if (classrooms == null) {
            return null;
        }
        //for each divisions of classroom entry, check if the subject is provided.
        ListIterator classroomListIterator = classrooms.listIterator();
        while (classroomListIterator.hasNext()) {
            Classroom classroom = (Classroom) classroomListIterator.next();
            ClassroomModule classroomModule = getClassroomModule(classroom, module);
            if (classroomModule != null) {
                //iteratively enter exams for each divisions.
                examList.add(insertExam(date, term, examType, classroomModule));
            }
        }
        if (examList.size() > 0) {
            return examList;
        }
        return null;
    }

    private static Exam insertExam(Date date, int term, ExamType examType, ClassroomModule classroomModule) {

        Exam exam = YschoolDataPoolFactory.getExam();
        exam.setDate(date);
        exam.setTerm(term);
        exam.setClassroomModuleIdclassroomModule(classroomModule);
        exam.setExamTypeIdexamType(examType);
        dataLayerYschool.save(exam);
        dataLayerYschool.flushSession();

        return exam;
    }

    private static ClassroomModule getClassroomModule(Classroom classroom, Module module) {

        Criteria getclassCriteria = dataLayerYschool.createCriteria(ClassroomModule.class);
        getclassCriteria.add(Restrictions.eq("classroomIdclassroom", classroom));
        getclassCriteria.add(Restrictions.eq("moduleIdmodule", module));
        List<ClassroomModule> classroomModulelist = getclassCriteria.list();
        if (classroomModulelist.size() > 0) {
            return classroomModulelist.get(0);
        }
        return null;
    }

    private static Classroom getClassroom(Date date, Grade grade, Division division) {

        Criteria getclassCriteria = dataLayerYschool.createCriteria(Classroom.class);

        List<Classroom> classroomList = new ArrayList<>();
        //year is int for classroom.
        Calendar calendar = Calendar.getInstance();
        calendar.setTime(date);
        int year = calendar.get(Calendar.YEAR);
        getclassCriteria.add(Restrictions.eq("year", year));
        getclassCriteria.add(Restrictions.eq("gradeIdgrade", grade));
        getclassCriteria.add(Restrictions.eq("divisionIddivision", division));
        classroomList = getclassCriteria.list();
        if (classroomList.size() > 0) {
            return classroomList.get(0);
        }
        return null;
    }

    private static List<Classroom> getClassrooms(Date date, Grade grade) {

        Criteria getclassCriteria = dataLayerYschool.createCriteria(Classroom.class);
        List<Classroom> classroomList = new ArrayList<>();
        //year is int for classroom.
        Calendar calendar = Calendar.getInstance();
        calendar.setTime(date);
        int year = calendar.get(Calendar.YEAR);
        getclassCriteria.add(Restrictions.eq("year", year));
        getclassCriteria.add(Restrictions.eq("gradeIdgrade", grade));
        classroomList = getclassCriteria.list();
        if (classroomList.size() > 0) {
            return classroomList;
        }
        return null;
    }

}
