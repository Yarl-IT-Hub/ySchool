package org.yarlithub.yschool.ySchoolSetUp.Loader;

import org.hibernate.SQLQuery;
import org.yarlithub.yschool.Reader.Reader;
import org.yarlithub.yschool.factories.yschoolLite.YschoolLiteDataPoolFactory;
import org.yarlithub.yschool.model.obj.yschoolLite.Classroom;
import org.yarlithub.yschool.services.data.DataLayerYschoolLite;
import org.yarlithub.yschool.services.data.DataLayerYschoolLiteImpl;

import java.util.Date;


/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 8/13/13
 * Time: 11:16 PM
 * To change this template use File | Settings | File Templates.
 */
public class ClassroomLoader {

    public boolean load(Reader reader) {

        /**
         * In initialization document 4th scheet is class information.
         */
        reader.setSheet(3);
        DataLayerYschoolLite dataLayerYschoolLite = DataLayerYschoolLiteImpl.getInstance();

        //Use SQL query for insert operations.
        String sql = "INSERT INTO classroom (year, grade, division, Section_idSection) VALUES (:year, :grade, :division, :Section_idSection)";

        for (int i = 1; i <= reader.getLastRowNumber(); i++) {
            reader.setRow(i);

            int grade = reader.getNumericCellValue(0);
            String division = reader.getStringCellValue(1);
            int year = reader.getNumericCellValue(2);
            Date date = new Date();
            date.setYear(year);

//            Classroom classroom = YschoolLiteDataPoolFactory.getClassroom();
//            classroom.setGrade(grade);
//            classroom.setDivision(division);
//            classroom.setYear(date);
//            classroom.setSectionIdsection(null);
//            dataLayerYschoolLite.save(classroom);
//            dataLayerYschoolLite.flushSession();

            SQLQuery insertQuery = dataLayerYschoolLite.createSQLQuery(sql);
            insertQuery.setParameter("year", String.valueOf(year));
            insertQuery.setParameter("grade", grade);
            insertQuery.setParameter("division", division);
            insertQuery.setParameter("Section_idSection", null);
            int result = insertQuery.executeUpdate();


        }
        return true;
    }
}
