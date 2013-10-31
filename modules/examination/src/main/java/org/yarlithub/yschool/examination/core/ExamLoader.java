package org.yarlithub.yschool.examination.core;

import org.apache.myfaces.custom.fileupload.UploadedFile;
import org.hibernate.SQLQuery;
import org.yarlithub.yschool.examination.dataAccess.ExaminationDBQueries;
import org.yarlithub.yschool.repository.services.data.DataLayerYschool;
import org.yarlithub.yschool.repository.services.data.DataLayerYschoolImpl;
import org.yarlithub.yschool.spreadSheetReader.Reader;
import org.yarlithub.yschool.spreadSheetReader.ReaderFactory;

import java.io.IOException;

/**
 * Created with IntelliJ IDEA.
 * User: jayrksih
 * Date: 10/31/13
 * Time: 11:21 AM
 * To change this template use File | Settings | File Templates.
 */
public class ExamLoader {
    public void loadMarks(UploadedFile marksFile, int examid) throws IOException {
        ReaderFactory readerFactory = new ReaderFactory();
        Reader marksDocReader = readerFactory.getspreadSheetReader(marksFile);

        marksDocReader.setSheet(0);
        DataLayerYschool DataLayerYschool = DataLayerYschoolImpl.getInstance();

        for (int i = 2; i <= marksDocReader.getLastRowNumber(); i++) {
            marksDocReader.setRow(i);

            int addno = marksDocReader.getNumericCellValue(0);
            float marks = marksDocReader.getNumericCellValue(2);

            SQLQuery getstudentidQuery = DataLayerYschool.createSQLQuery(ExaminationDBQueries.GET_STUDENTID);
            getstudentidQuery.setParameter("add_no",String.valueOf(addno));
            int studentid= Integer.valueOf(getstudentidQuery.list().get(0).toString());

            SQLQuery insertQuery = DataLayerYschool.createSQLQuery(ExaminationDBQueries.INSERT_MARKS);
            insertQuery.setParameter("idexam", examid);
            insertQuery.setParameter("idstudent", studentid);
            insertQuery.setParameter("marks", marks);
            int result = insertQuery.executeUpdate();

        }

    }
}
