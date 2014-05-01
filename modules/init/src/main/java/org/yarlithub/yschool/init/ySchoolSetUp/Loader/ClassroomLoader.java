package org.yarlithub.yschool.init.ySchoolSetUp.Loader;

import org.hibernate.Criteria;
import org.hibernate.criterion.Restrictions;
import org.yarlithub.yschool.repository.model.obj.yschool.Grade;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;
import org.yarlithub.yschool.spreadSheetHandler.Reader;

import java.util.List;


/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 8/13/13
 * Time: 11:16 PM
 * To change this template use File | Settings | File Templates.
 */
public class ClassroomLoader {

    public boolean load(Reader reader) throws Exception {

        /**
         * In initialization document 3th sheet is classroom information.
         */
        reader.setSheet(2);
        DataLayerYschool DataLayerYschool = DataLayerYschoolImpl.getInstance();

        for (int i = 1; i <= reader.getLastRowNumber(); i++) {
            reader.setRow(i);

            int grade = reader.getNumericCellValue(0);
            String division = reader.getStringCellValue(1);
            //TODO:get it from system?properties?
            int year = 2014;

            Grade gradeInstance;
            Criteria gradeCriteria = DataLayerYschool.createCriteria(Grade.class);
            gradeCriteria.add(Restrictions.eq("grade", grade));
            List<Grade> gradeList = gradeCriteria.list();
            if (gradeList.size() == 1)
                gradeInstance = (Grade) gradeList.get(0);
            else
                throw new Exception("DataloadError: Classrooms row " + i + "col " + 1 + " Grade " + grade + " not found in Grades");

        }
        return true;
    }
}
