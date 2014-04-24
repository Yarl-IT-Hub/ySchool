package org.yarlithub.yschool.repository;

/**
 * Created with IntelliJ IDEA.
 * User: admin
 * Date: 2014-04-05
 * Time: 3:01 PM
 * To change this template use File | Settings | File Templates.
 */
public class DBConstants {
    public static class staff{
         public static final String idstaff="idstaff";
         public static final String staffID="staffID";
         public static final String name="name";
         public static final String full_name="full_name";
         public static final String photo="photo";
         public static final String modified_time="modified_time";
    }
    public static class classroom{
        public static final String idclassroom="idclassroom";
        public static final String year="year";
        public static final String grade_idgrade="grade_idgrade";
        public static final String division_iddivision="division_iddivision";
        public static final String section_idsection="section_idsection";
        public static final String modified_time="modified_time";
    }
    public static class classroom_has_staff_has_role
    {
        public static final String staff_has_role_idstaff_has_role="staff_has_role_idstaff_has_role";
        public static final String classroom_idclassroom="classroom_idclassroom";
    }
    public static class classroom_module
    {
        public static final String idclassroom_module="idclassroom_module";
        public static final String classroom_idclassroom="classroom_idclassroom";
        public static final String module_idmodule="module_idmodule";
    }
    public static class classroom_module_has_staff_has_role

    {
        public static final String classroom_module_idclassroom_module="classroom_module_idclassroom_module";
        public static final String staff_has_role_idstaff_has_role="staff_has_role_idstaff_has_role";
    }
    public static class classroom_student
    {
        public static final String idclassroom_student="idclassroom_student";
        public static final String student_idstudent="student_idstudent";
        public static final String classroom_idclassroom="classroom_idclassroom";
    }
    public static class role
    {
        public static final String idrole="idrole";
        public static final String role_name="role_name";
    }
    /*public static class class_analyzer_classifier
    {
        public static final String idclass_analyzer_classifier="idclass_analyzer_classifier";
        public static final String year="year";
        public static final String grade="grade";
    }*/

    public static class staff_has_role
    {
        public static final String idstaff_has_role="idstaff_has_role";
        public static final String staff_idstaff="staff_idstaff";
        public static final String role_idrole="role_idrole";
        public static final String start_date="start_date";
        public static final String end_date="end_date";
    }

    public static class module
    {
        public static final String idmodule="idmodule";
        public static final String subject_idsubject="subject_idsubject";
        public static final String grade_idgrade="grade_idgrade";
        public static final String is_optional="is_optional";
        public static final String modified_time="modified_time";
    }

    public static class student
    {
        public static final String idstudent="idstudent";
        public static final String admission_no="admission_no";
        public static final String name="name";
        public static final String full_name="full_name";
        public static final String name_wt_initial="name_wt_initial";
        public static final String dob="dob";
        public static final String gender="gender";
        public static final String address="address";
        public static final String photo="photo";
        public static final String modified_time="modified_time";
    }


}
