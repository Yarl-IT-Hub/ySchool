/*
 *   (C) Copyright 2012-2013 hSenid Software International (Pvt) Limited.
 *   All Rights Reserved.
 *
 *   These materials are unpublished, proprietary, confidential source code of
 *   hSenid Software International (Pvt) Limited and constitute a TRADE SECRET
 *   of hSenid Software International (Pvt) Limited.
 *
 *   hSenid Software International (Pvt) Limited retains all title to and intellectual
 *   property rights in these materials.
 *
 */
package org.yarlithub.yschool.repository;

import javax.persistence.*;
import java.io.Serializable;
import java.util.Date;

/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@MappedSuperclass
public class PersistentObject implements Serializable {

    private static final long serialVersionUID = 7769773760769217664L;

    @Id
    @GeneratedValue(strategy = GenerationType.AUTO)
    private Long id;

    @Version
    private Long version;

    //    @Temporal(value = TemporalType.TIMESTAMP)
    @Column(updatable = false, name = "created_time")
    private Date createdTime;

    //    @Temporal(value = TemporalType.TIMESTAMP)
    @Column(name = "last_modified_time")
    private Date lastModifiedTime;

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public Long getVersion() {
        return version;
    }

    public void setVersion(Long version) {
        this.version = version;
    }

    public Date getCreatedTime() {
        return createdTime;
    }

    public void setCreatedTime(Date createdTime) {
        this.createdTime = createdTime;
    }

    public Date getLastModifiedTime() {
        return lastModifiedTime;
    }

    public void setLastModifiedTime(Date lastModifiedTime) {
        this.lastModifiedTime = lastModifiedTime;
    }

    public boolean equals(Object o) {
        if (this == o) {
            return true;
        }
        if (o == null || getClass() != o.getClass()) {
            return false;
        }

        final PersistentObject persistentObject = (PersistentObject) o;

        if (id == null || persistentObject.getId() == null) {
            return false;
        }

        return id.equals(persistentObject.getId());
    }

    @PrePersist
    private void createdTime() {
        createdTime = new Date();
    }


    @PreUpdate
    private void updatedTime() {
        lastModifiedTime = new Date();
    }

    public int hashCode() {
        return id == null ? 0 : id.hashCode();
    }
}
