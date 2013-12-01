package org.yarlithub.yschool.analytics.datasync;

/**
 * Created with IntelliJ IDEA.
 * User: jaykrish
 * Date: 11/15/13
 * Time: 1:05 PM
 * To change this template use File | Settings | File Templates.
 */
public class Constants {

    /**
     * CLASS database performance types
     */
    public static final int PERFORMANCE_TYPE_EXAM = 1;
    public static final int PERFORMANCE_TYPE_ATTENDANCE = 2;
    public static final int PERFORMANCE_TYPE_SPORTS = 3;
    public static final int PERFORMANCE_TYPE_COMMUNITY = 4;
    /**
     * CLASS Database connection strings.
     */
    public static final String CLASS_DB_NAME = "jdbc:mysql://localhost:3306/class";
    public static final String CLASS_DB_USERNAME = "root";
    public static final String CLASS_DB_PASSWORD = "";
    public static final String INSERT_STUDENT = "INSERT INTO student (school_no, student_school_id, gender, religion, language, father, mother, siblings) VALUES (?,?,?,?,?,?,?,?)";
    public static final String INSERT_EXAM = "INSERT INTO exam (school_no, date, grade, division, term, subject_idsubject, exam_type_idexam_type) VALUES (?,?,?,?,?,?,?)";
    public static final String SELECT_STUDENTID = "SELECT idstudent FROM student WHERE school_no = ? AND student_school_id = ?";
    public static final String STUDENTIDCOL = "idstudent";
    public static final String INSERT_STUDENT_PERFORMANCE = "INSERT INTO student_performance (student_idstudent, performance_type_idperformance_type) VALUES (?,?)";
    public static final String INSERT_RESULTS = "INSERT INTO results (student_performance_idstudent_performance, results, exam_id_exam) VALUES (?,?,?)";
    public static final String INSERT_MARKS = "INSERT INTO marks (student_performance_idstudent_performance, makrs, exam_id_exam) VALUES (?,?,?)";
    /**
     * Sync Success/Error messages codes.
     */
    public static final String SUCCESS_MSG = "SD01:";
    public static final String ERROR_CLASS_NOT_FOUND = "ED01:";
    public static final String ERROR_SQL = "ED02:";
    public static final String ERROR_INSTANTIATION = "ED03:";
    public static final String ERROR_ILLEGAL_ACCESS = "ED04:";
    public static final String ERROR_JSON = "ED05:";
    public static final String ERROR_NO_STUDENT = "ED06:";
    public static final String ERROR_UNKNOWN = "ED99:";
    public static final String WARNING_MSG = "WD01:";
    /**
     * JSON Keys: Commons
     */
    public static final String SCHOOL_NO = "schoolNo";
    public static final String ADDMISSION_NO = "studentAdmissionNo";
    public static final String PERFORMANCE_LIST = "performanceList";
    /**
     * JSON keys: Student
     */
    public static final String GENDER = "gender";
    public static final String RELIGION = "religion";
    public static final String LANGUAGE = "language";
    public static final String FATHER = "father";
    public static final String MOTHER = "mother";
    public static final String NO_OF_SIBLINGS = "noOfSiblings";
    public static final String FROM = "from";
    public static final String TO = "to";
    /**
     * JSON keys: Exam Performance
     */
    public static final String EXAM_DATE = "date";
    public static final String EXAM_GRADE = "grade";
    public static final String EXAM_DIVISION = "division";
    public static final String EXAM_TERM = "term";
    public static final String EXAM_SUBJECT_ID = "subjectId";
    public static final String EXAM_TYPE = "examType";
    public static final String EXAM_MARKS = "marks";
    public static final String EXAM_RESULTS = "results";
    /*CLASS conventions*/
    public static final int CONTINUOUS_ASSESSMENT = 1;
    public static final int TERM_EXAM = 2;
    public static final int GENERAL_EXAM = 3;
}