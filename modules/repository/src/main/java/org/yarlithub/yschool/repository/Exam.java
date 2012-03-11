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

import javax.persistence.Entity;
import javax.persistence.Table;

/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@Entity
@Table(name = "exam")
public class Exam extends PersistentObject {
    
    private String name;
    private String marks;
    private Status status;
            
}
