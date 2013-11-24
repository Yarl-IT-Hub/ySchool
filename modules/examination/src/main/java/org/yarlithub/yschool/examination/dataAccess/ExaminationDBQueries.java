package org.yarlithub.yschool.examination.dataAccess;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 8/20/13
 * Time: 3:13 PM
 * To change this template use File | Settings | File Templates.
 */
public class ExaminationDBQueries {
    /**
     * TODO: delete
     */
    public static final String INSERT_MARKS = "INSERT INTO marks (exam_idexam, student_idstudent, marks) VALUES (:idexam, :idstudent, :marks)";
    public static final String INSERT_RESULTS = "INSERT INTO results (exam_idexam, student_idstudent, results) VALUES (:idexam, :idstudent, :results)";

    public static final String GET_STUDENTID = "SELECT idstudent FROM student WHERE addmision_no = :add_no" ;

  }
