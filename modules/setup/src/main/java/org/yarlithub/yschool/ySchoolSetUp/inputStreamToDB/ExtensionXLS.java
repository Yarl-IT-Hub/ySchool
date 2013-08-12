package org.yarlithub.yschool.ySchoolSetUp.inputStreamToDB;

import org.apache.poi.hssf.usermodel.HSSFSheet;
import org.apache.poi.hssf.usermodel.HSSFWorkbook;
import org.apache.poi.poifs.filesystem.POIFSFileSystem;
import org.apache.poi.ss.usermodel.Row;
import org.yarlithub.yschool.factories.yschoolLite.HibernateYschoolLiteDaoFactory;
import org.yarlithub.yschool.factories.yschoolLite.YschoolLiteDataPoolFactory;
import org.yarlithub.yschool.model.dao.yschoolLite.StudentDao;
import org.yarlithub.yschool.model.obj.yschoolLite.Student;
import org.yarlithub.yschool.services.data.DataLayerYschoolLite;
import org.yarlithub.yschool.services.data.DataLayerYschoolLiteImpl;

import java.io.FileInputStream;
import java.io.IOException;

public class ExtensionXLS implements InputFileStreamToDatabase {

    @Override
    public void writeToDataBase(FileInputStream fileInputStream) {


        FileInputStream excelInputStream = null;

        excelInputStream = fileInputStream;


        POIFSFileSystem fs = null;
        try {
            fs = new POIFSFileSystem(excelInputStream);
        } catch (IOException e) {
            e.printStackTrace();  //To change body of catch statement use File | Settings | File Templates.
        }
        HSSFWorkbook wb = null;
        try {
            wb = new HSSFWorkbook(fs);
        } catch (IOException e) {
            e.printStackTrace();  //To change body of catch statement use File | Settings | File Templates.
        }
        HSSFSheet sheet = wb.getSheetAt(0);
        Row row;

        for (int i = 1; i <= sheet.getLastRowNum(); i++) {
            row = sheet.getRow(i);

            int admissionNo = (int) row.getCell(0).getNumericCellValue();

            String nameWithInitials = row.getCell(1).getStringCellValue();

            DataLayerYschoolLite dataLayerYschoolLite = DataLayerYschoolLiteImpl.getInstance();
            StudentDao studentDao = HibernateYschoolLiteDaoFactory.getStudentDao();

            Student student = YschoolLiteDataPoolFactory.getStudent();

            student.setAddmisionNo(Integer.toString(admissionNo));
            student.setName(nameWithInitials);


            studentDao.save(student);
            dataLayerYschoolLite.flushSession();


        }


    }


}
