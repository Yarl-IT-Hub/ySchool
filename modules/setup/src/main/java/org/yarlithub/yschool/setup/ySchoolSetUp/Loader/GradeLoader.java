package org.yarlithub.yschool.setup.ySchoolSetUp.Loader;


import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
import org.yarlithub.yschool.repository.model.obj.yschool.Grade;
import org.yarlithub.yschool.repository.model.obj.yschool.Subject;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;
import org.yarlithub.yschool.spreadSheetReader.Reader;


/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 8/13/13
 * Time: 11:16 PM
 * To change this template use File | Settings | File Templates.
 */
public class GradeLoader {

    public boolean load(Reader reader) throws Exception{

        /**
         * In initialization document 1th sheet is grades information.
         */
        reader.setSheet(0);

        DataLayerYschool DataLayerYschool = DataLayerYschoolImpl.getInstance();

        for (int i = 1; i <= reader.getLastRowNumber(); i++) {
            reader.setRow(i);

            int grade = reader.getNumericCellValue(0);

            Grade gradeInstance = YschoolDataPoolFactory.getGrade();
            gradeInstance.setGrade(grade);

            DataLayerYschool.save(gradeInstance);
        }
        DataLayerYschool.flushSession();
        return true;
    }
}
