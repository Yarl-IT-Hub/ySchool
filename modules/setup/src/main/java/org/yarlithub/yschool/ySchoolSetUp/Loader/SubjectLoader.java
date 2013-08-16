package org.yarlithub.yschool.ySchoolSetUp.Loader;


import org.yarlithub.yschool.Reader.Reader;
import org.yarlithub.yschool.factories.yschoolLite.YschoolLiteDataPoolFactory;
import org.yarlithub.yschool.model.obj.yschoolLite.Subject;
import org.yarlithub.yschool.services.data.DataLayerYschoolLite;
import org.yarlithub.yschool.services.data.DataLayerYschoolLiteImpl;


/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
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


        DataLayerYschoolLite dataLayerYschoolLite = DataLayerYschoolLiteImpl.getInstance();
        for (int i = 1; i <= reader.getLastRowNumber(); i++) {
            reader.setRow(i);

            String subjectName = reader.getStringCellValue(0);
            int isOptional = reader.getNumericCellValue(1);

            boolean isOptionalBool = false;
            if (isOptional == 1) {
                isOptionalBool = true;
            }

            Subject subject = YschoolLiteDataPoolFactory.getSubject();
            subject.setName(subjectName);
            subject.setIsoptional(isOptionalBool);

            dataLayerYschoolLite.save(subject);
            dataLayerYschoolLite.flushSession();


        }
        return true;
    }
}
