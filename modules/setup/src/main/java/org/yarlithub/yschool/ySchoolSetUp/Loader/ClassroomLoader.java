package org.yarlithub.yschool.ySchoolSetUp.Loader;

import org.apache.poi.ss.usermodel.Row;
import org.yarlithub.yschool.factories.yschoolLite.HibernateYschoolLiteDaoFactory;
import org.yarlithub.yschool.factories.yschoolLite.YschoolLiteDataPoolFactory;
import org.yarlithub.yschool.model.dao.yschoolLite.ClassroomDao;
import org.yarlithub.yschool.model.dao.yschoolLite.StudentDao;
import org.yarlithub.yschool.model.obj.yschoolLite.Classroom;
import org.yarlithub.yschool.model.obj.yschoolLite.Student;
import org.yarlithub.yschool.services.data.DataLayerYschoolLite;
import org.yarlithub.yschool.services.data.DataLayerYschoolLiteImpl;
import org.yarlithub.yschool.ySchoolSetUp.Reader.Reader;

import java.util.Date;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 8/13/13
 * Time: 11:16 PM
 * To change this template use File | Settings | File Templates.
 */
public class ClassroomLoader {

    public boolean load(Reader reader){

        /**
         * In initialization document 4th scheet is class information.
         */
        reader.setSheet(3);
        Row row;


        for (int i = 1; i <= reader.getLastRowNumber(); i++) {
            row = reader.getRow(i);

//            int grade = (int) row.getCell(0).getNumericCellValue();
//            String division = row.getCell(1).getStringCellValue();
//            int year = (int) row.getCell(2).getNumericCellValue();
//            Date date = new Date();
//            date.setYear(year);
//
//            DataLayerYschoolLite dataLayerYschoolLite = DataLayerYschoolLiteImpl.getInstance();
//            ClassroomDao classroomDao = HibernateYschoolLiteDaoFactory.getClassroomDao();
//
//            Classroom classroom = YschoolLiteDataPoolFactory.getClassroom();
//
//            classroom.setGrade(grade);
//            classroom.setDivision(division);
//            classroom.setYear(date);
//
//            classroomDao.save(classroom);
//            dataLayerYschoolLite.flushSession();


        }
        return true;
    }
}
