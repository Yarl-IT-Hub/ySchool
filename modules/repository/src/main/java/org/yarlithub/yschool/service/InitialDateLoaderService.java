/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.yarlithub.yschool.service;

import java.util.Arrays;
import java.util.List;
import javax.faces.bean.ManagedBean;
import javax.faces.bean.SessionScoped;

import org.yarlithub.yschool.repository.util.HibernateUtil;
import org.apache.log4j.Logger;

/**
 * @author dell
 */

@ManagedBean(name = "initialDateLoaderService")
@SessionScoped
public class InitialDateLoaderService {

    private static final Logger logger = Logger.getLogger(InitialDateLoaderService.class);

    private String dataType;
    private List dataList;

    /**
     * @return the dataType
     */
    public String getDataType() {
        return dataType;
    }

    /**
     * @param dataType the dataType to set
     */
    public void setDataType(String dataType) {
        this.dataType = dataType;
    }

    /**
     * @return the dataList
     */
    public List getDataList() {
        return dataList;
    }

    /**
     * @param dataList the dataList to set
     */
    public void setDataList(List dataList) {
        this.dataList = dataList;
    }

    public List loadData(String dataType) {
        logger.info("Loading dataList for type" + dataType);
//        return HibernateUtil.getCurrentSession().createSQLQuery("select * from PreLoadData where dataType = ?").setString(0, dataType).list();
        return Arrays.asList("1", "2", "3", "4", "5");
    }
}
