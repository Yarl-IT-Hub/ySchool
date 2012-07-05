/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package org.yarlithub.yschool.repository;

import java.util.List;
import javax.persistence.Entity;
import javax.persistence.Table;
import org.yarlithub.yschool.repository.util.HibernateUtil;
import org.apache.log4j.Logger;

/**
 *
 * @author dell
 */

@Entity
@Table (name = "PreLoadData")
public class PreLoadData extends PersistentObject{
    
    private static final Logger logger = Logger.getLogger(PreLoadData.class);
    
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
    
    public List loadData(String dataType){
        logger.info("Loading dataList for type" + dataType);
        return HibernateUtil.getCurrentSession().createQuery("from PreLoadData where dataType = ?").setString(0, dataType).list();
    }
    
    public void save() {
        HibernateUtil.getCurrentSession().save(this);
    }
}
