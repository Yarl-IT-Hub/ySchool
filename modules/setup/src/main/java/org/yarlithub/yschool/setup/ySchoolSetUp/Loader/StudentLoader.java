package org.yarlithub.yschool.setup.ySchoolSetUp.Loader;

import org.hibernate.SQLQuery;
import org.yarlithub.yschool.Reader.Reader;
import org.yarlithub.yschool.services.data.DataLayerYschool;
import org.yarlithub.yschool.services.data.DataLayerYschoolImpl;
import org.yarlithub.yschool.setup.dataAccess.SetUpDBQueries;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 8/13/13
 * Time: 11:16 PM
 * To change this template use File | Settings | File Templates.
 */
public class StudentLoader {

    public boolean load(Reader reader) {

        /**
         * In initialization document 1th sheet is student information.
         */
        reader.setSheet(0);
        DataLayerYschool DataLayerYschool = DataLayerYschoolImpl.getInstance();

        for (int i = 1; i <= reader.getLastRowNumber(); i++) {
            reader.setRow(i);

            int addmissionNo = reader.getNumericCellValue(0);
            String nameWithInitials = reader.getStringCellValue(1);
            String fullName = reader.getStringCellValue(2);
            String dob = reader.getStringCellValue(3);
            String gender = reader.getStringCellValue(4);
            String address = reader.getStringCellValue(5);
            int grade = reader.getNumericCellValue(6);
            String division = reader.getStringCellValue(7);

            SimpleDateFormat format = new SimpleDateFormat("dd/MM/yyyy");
            Date parsed = null;
            try {
                parsed = format.parse(dob);
            } catch (ParseException e) {

            }
            java.sql.Date sqldob = new java.sql.Date(parsed.getTime());

            //insert Student table
            SQLQuery insertStudentQuery = DataLayerYschool.createSQLQuery(SetUpDBQueries.STUDENT_INIT_SQL);
            insertStudentQuery.setParameter("addmision_no", addmissionNo);
            insertStudentQuery.setParameter("name", nameWithInitials);      //TODO: name
            insertStudentQuery.setParameter("full_name", fullName);
            insertStudentQuery.setParameter("name_wt_initial", nameWithInitials);
            insertStudentQuery.setParameter("dob", sqldob);
            insertStudentQuery.setParameter("gender", gender);
            insertStudentQuery.setParameter("address", address);
            insertStudentQuery.setParameter("photo", null);
            int result = insertStudentQuery.executeUpdate();

            //get classID, StudentID and insert into class_student relation table
            SQLQuery selectClassIDQuery = DataLayerYschool.createSQLQuery(SetUpDBQueries.GET_CLASS_ID_SQL);
            selectClassIDQuery.setParameter("grade", grade);
            selectClassIDQuery.setParameter("division", division);
            int classID = Integer.valueOf(selectClassIDQuery.list().get(0).toString());

            SQLQuery selectStudentIDQuery = DataLayerYschool.createSQLQuery(SetUpDBQueries.GET_STUDENT_ID_SQL);
            selectStudentIDQuery.setParameter("addmision_no", addmissionNo);
            int studentID = Integer.valueOf(selectStudentIDQuery.list().get(0).toString());

            SQLQuery insertClassStudentQuery = DataLayerYschool.createSQLQuery(SetUpDBQueries.CLASS_STUDENT_INIT_SQL);
            insertClassStudentQuery.setParameter("idClass", classID);
            insertClassStudentQuery.setParameter("idStudent", studentID);
            int result2 = insertClassStudentQuery.executeUpdate();
        }
        return true;
    }
}
