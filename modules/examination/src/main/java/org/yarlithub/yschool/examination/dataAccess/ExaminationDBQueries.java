package org.yarlithub.yschool.examination.dataAccess;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 8/20/13
 * Time: 3:13 PM
 * To change this template use File | Settings | File Templates.
 */
public class ExaminationDBQueries {
    /**
     * TODO: delete
     */

    //not used after build 1.0.42, instead Hibernate Criteria Queries are used.
    public static final String INSERT_EXAM = "INSERT INTO exam (date, term, year, Class_Subject_idClass_Subject, Exam_Type_idExam_Type) VALUES (:date, :term, :year, :idClass_Subject, :idExam_Type)";
    public static final String GET_CLASSID = "SELECT idClass FROM classroom WHERE year = :year AND grade = :grade AND division = :division";
    public static final String GET_CLASS_SUBJECTID = "SELECT idClass_Subject FROM class_subject WHERE Class_idClass = :idClass AND Subject_idSubject = :idSubject";


}
