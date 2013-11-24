package org.yarlithub.yschool.setup.dataAccess;

/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 8/20/13
 * Time: 3:13 PM
 * To change this template use File | Settings | File Templates.
 */
public class SetUpDBQueries {
    /**
     * TODO: description
      */
    public static final String CLASS_INIT_SQL = "INSERT INTO classroom (year, grade, division, section_idsection) VALUES (:year, :grade, :division, :Section_idSection)";
    public static final String SUBJECT_INIT_SQL =  "INSERT INTO subject (name, isOptional) VALUES (:name, :isOptional)";
    public static final String STAFF_INIT_SQL = "INSERT INTO staff (staffID, name, full_name, photo) VALUES (:id, :name, :full_name, :photo)";
    public static final String STUDENT_INIT_SQL =  "INSERT INTO student (addmision_no, name, full_name, name_wt_initial, dob, gender, address, photo) VALUES (:addmision_no, :name, :full_name, :name_wt_initial, :dob, :gender, :address, :photo)";
    public static final String GET_CLASS_ID_SQL = "SELECT idclass FROM classroom WHERE grade = :grade AND division = :division";
    public static final String GET_STUDENT_ID_SQL = "SELECT idstudent FROM student WHERE addmision_no = :addmision_no";
    public static final String CLASS_STUDENT_INIT_SQL =  "INSERT INTO classroom_student (classroom_idclass, student_idstudent) VALUES (:idClass, :idStudent)";

    public static final String INSERT_USER= "INSERT INTO user (user_name, email, password, user_role_iduser_role) VALUES (:user_name, :email, :password, :User_Role_idUser_Role)";
    public static final String INSERT_SCHOOL = "INSERT INTO school (name, address, zone, district, province) VALUES (:name, :address, :zone, :district, :province)" ;
}
