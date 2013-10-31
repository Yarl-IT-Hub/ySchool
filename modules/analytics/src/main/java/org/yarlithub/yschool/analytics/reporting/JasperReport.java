package org.yarlithub.yschool.analytics.reporting;

/**
 * Created with IntelliJ IDEA.
 * User: Kana
 * Date: 10/29/13
 * Time: 3:10 PM
 * To change this template use File | Settings | File Templates.
 */

import net.sf.jasperreports.engine.*;
import net.sf.jasperreports.engine.data.JRBeanCollectionDataSource;
import net.sf.jasperreports.engine.design.JasperDesign;
import net.sf.jasperreports.engine.xml.JRXmlLoader;


import javax.servlet.ServletOutputStream;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class JasperReport {

    private String jrxmlPath;

    public void printJasperReport(ServletOutputStream servletOutputStream) throws IOException, JRException {                 //ServletOutputStream servletOutputStream


      jrxmlPath = "/home/kana/ySchool/modules/analytics/src/main/java/org/yarlithub/yschool/analytics/reporting/jrxmlFiles/CLASS_report_student_progress.jrxml";

        //  jrxmlPath = "/home/kana/ySchool/modules/analytics/src/main/java/org/yarlithub/yschool/analytics/reporting/jrxmlFiles/Copy.jrxml";



        InputStream inputStream = new FileInputStream(
                jrxmlPath);

        DataBeanMaker dataBeanMaker = new DataBeanMaker();
        List<StudentDataBean> dataBeanList = dataBeanMaker.getStudentDataBeanList();

        JRBeanCollectionDataSource beanColDataSource = new JRBeanCollectionDataSource(
                dataBeanList);

        Map parameters = new HashMap();

        JasperDesign jasperDesign = JRXmlLoader.load(inputStream);

        net.sf.jasperreports.engine.JasperReport jasperReport = JasperCompileManager
                .compileReport(jasperDesign);

        JasperPrint jasperPrint = JasperFillManager.fillReport(jasperReport,
                parameters, beanColDataSource);


//           String reportPath = FacesContext.getCurrentInstance().getExternalContext().getRealPath("/reports/chartReport.jasper");
//
//            HttpServletResponse httpServletResponse = (HttpServletResponse) FacesContext.getCurrentInstance().getExternalContext().getResponse();
//            httpServletResponse.addHeader("Content-disposition", "attachment: filename=studentReport.pdf");
//            ServletOutputStream servletOutputStream = httpServletResponse.getOutputStream();
//


        JasperExportManager.exportReportToPdfStream(jasperPrint, servletOutputStream);


//            FacesContext.getCurrentInstance().responseComplete();


        JasperExportManager.exportReportToPdfFile(jasperPrint,
                "/home/kana/ySchool/modules/analytics/src/main/java/org/yarlithub/yschool/analytics/reporting/studentReport.pdf");
    }

}


