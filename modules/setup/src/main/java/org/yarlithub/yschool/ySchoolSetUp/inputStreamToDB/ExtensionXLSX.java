package org.yarlithub.yschool.ySchoolSetUp.inputStreamToDB;

import org.apache.poi.ss.usermodel.Row;
import org.apache.poi.xssf.usermodel.XSSFSheet;
import org.apache.poi.xssf.usermodel.XSSFWorkbook;
import org.yarlithub.yschool.factories.yschoolLite.HibernateYschoolLiteDaoFactory;
import org.yarlithub.yschool.factories.yschoolLite.YschoolLiteDataPoolFactory;
import org.yarlithub.yschool.model.dao.yschoolLite.StudentDao;
import org.yarlithub.yschool.model.obj.yschoolLite.Student;
import org.yarlithub.yschool.services.data.DataLayerYschoolLite;
import org.yarlithub.yschool.services.data.DataLayerYschoolLiteImpl;

import java.io.FileInputStream;
import java.io.IOException;


public class ExtensionXLSX implements InputFileStreamToDatabase {
  //  private File excelfile;

    public ExtensionXLSX() {
       // this.excelfile = excelFile;
    }

    @Override
    public void writeToDataBase(FileInputStream fileInputStream) throws IOException {


        XSSFWorkbook wb = new XSSFWorkbook(fileInputStream);
        XSSFSheet sheet = wb.getSheetAt(0);
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
