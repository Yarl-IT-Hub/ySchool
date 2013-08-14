package org.yarlithub.yschool.ySchoolSetUp.Loader;

import org.apache.poi.ss.usermodel.Row;
import org.yarlithub.yschool.factories.yschoolLite.HibernateYschoolLiteDaoFactory;
import org.yarlithub.yschool.factories.yschoolLite.YschoolLiteDataPoolFactory;
import org.yarlithub.yschool.model.dao.yschoolLite.SubjectDao;
import org.yarlithub.yschool.model.obj.yschoolLite.Subject;
import org.yarlithub.yschool.services.data.DataLayerYschoolLite;
import org.yarlithub.yschool.services.data.DataLayerYschoolLiteImpl;
import org.yarlithub.yschool.ySchoolSetUp.Reader.Reader;

import java.nio.ByteBuffer;


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
        Row row;


        for (int i = 1; i <= reader.getLastRowNumber(); i++) {
            row = reader.getRow(i);


            String subjectName = row.getCell(0).getStringCellValue();
            int isOptional = (int) row.getCell(1).getNumericCellValue();

            Byte[] isOptionalByte = new Byte[1];
            isOptionalByte[0] = (byte) isOptional;

            DataLayerYschoolLite dataLayerYschoolLite = DataLayerYschoolLiteImpl.getInstance();
            SubjectDao subjectDao = HibernateYschoolLiteDaoFactory.getSubjectDao();

            Subject subject = YschoolLiteDataPoolFactory.getSubject();

            subject.setName(subjectName);
            subject.setIsoptional(isOptionalByte);

            subjectDao.save(subject);
            dataLayerYschoolLite.flushSession();


        }
        return true;
    }
}
