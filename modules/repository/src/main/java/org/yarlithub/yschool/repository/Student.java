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

import org.joda.time.DateTime;

import javax.persistence.Entity;
import javax.persistence.Table;
import java.util.List;

/**
 * $LastChangedDate$
 * $LastChangedBy$
 * $LastChangedRevision$
 */
@Entity
@Table(name = "student")
public class Student extends PersistentObject {
    
    private String fname;
    private String lname;
    private DateTime dob;
    private DateTime registrationDate;

    private List<Exam> exams;
}
