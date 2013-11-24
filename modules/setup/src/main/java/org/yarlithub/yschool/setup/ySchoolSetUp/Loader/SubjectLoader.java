package org.yarlithub.yschool.setup.ySchoolSetUp.Loader;


import org.yarlithub.yschool.spreadSheetReader.Reader;
import org.yarlithub.yschool.repository.factories.yschool.YschoolDataPoolFactory;
import org.yarlithub.yschool.repository.model.obj.yschool.Subject;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;


/**
 * Created with IntelliJ IDEA.
 * User: Jay Krish
 * Date: 8/13/13
 * Time: 11:16 PM
 * To change this template use File | Settings | File Templates.
 */
public class SubjectLoader {

    public boolean load(Reader reader) {

        /**
         * In initialization document 3th scheet is subjects information.
         */
        reader.setSheet(2);


        DataLayerYschool DataLayerYschool = DataLayerYschoolImpl.getInstance();

        for (int i = 1; i <= reader.getLastRowNumber(); i++) {
            reader.setRow(i);

            //we use hibernate ORM for subject initiation (it works here due to no dependencies with other tables)

            String subjectName = reader.getStringCellValue(0);
            int isOptional = reader.getNumericCellValue(1);

            boolean isOptionalBool = false;
            if (isOptional == 1) {
                isOptionalBool = true;
            }

            Subject subject = YschoolDataPoolFactory.getSubject();
            subject.setName(subjectName);
            subject.setIsOptional(isOptionalBool);

            DataLayerYschool.save(subject);
            DataLayerYschool.flushSession();


        }
        return true;
    }
}
