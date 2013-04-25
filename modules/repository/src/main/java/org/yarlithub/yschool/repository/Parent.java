package org.yarlithub.yschool.repository;

import org.joda.time.DateTime;
import org.yarlithub.yschool.repository.util.HibernateUtil;

import javax.persistence.Entity;
import javax.persistence.ManyToMany;
import javax.persistence.OneToMany;
import javax.persistence.Table;
import java.util.Date;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: jaykrish
 * Date: 4/25/13
 * Time: 1:18 PM
 * To change this template use File | Settings | File Templates.
 */

@Entity
@Table(name = "parent")
public class Parent extends PersistentObject{

    //unique identifier for parent like admission no. for student.
    private String parentIdentifier;
    private String fullName;
    private String nameWithInitials;
    private Date dob;
    private String addressLine1; // new
    private String addressLine2; // new
    private String city;
    private String country;
    private String tempaddressLine1;
    private String tempaddressLine2;
    private String tempcountry;

    private String gender;
    private String profession;
    private String comment;
    private String email;
    private Integer mobileNo;
    private String userName;
    private String password;


    public void save() {
        HibernateUtil.getCurrentSession().save(this);
    }

    public void update() {
        HibernateUtil.getCurrentSession().update(this);
    }

    public void delete() {
        HibernateUtil.getCurrentSession().delete(this);
    }

    public List<Parent> searchParentByfullName(String parentsfullName) {
        return HibernateUtil.getCurrentSession().createQuery("from Parent  where fullName = ?").setString(0, parentsfullName).list();
    }

    public String getParentIdentifier() {
        return parentIdentifier;
    }

    public void setParentIdentifier(String parentIdentifier) {
        this.parentIdentifier = parentIdentifier;
    }

    public String getFullName() {
        return fullName;
    }

    public void setFullName(String fullName) {
        this.fullName = fullName;
    }

    public String getNameWithInitials() {
        return nameWithInitials;
    }

    public void setNameWithInitials(String nameWithInitials) {
        this.nameWithInitials = nameWithInitials;
    }

    public Date getDob() {
        return dob;
    }

    public void setDob(Date dob) {
        this.dob = dob;
    }

    public String getAddressLine1() {
        return addressLine1;
    }

    public String getGender() {
        return gender;
    }

    public void setGender(String gender) {
        this.gender = gender;
    }

    public String getProfession() {
        return profession;
    }

    public void setProfession(String profession) {
        this.profession = profession;
    }

    public String getComment() {
        return comment;
    }

    public void setComment(String comment) {
        this.comment = comment;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public Integer getMobileNo() {
        return mobileNo;
    }

    public void setMobileNo(Integer mobileNo) {
        this.mobileNo = mobileNo;
    }

    public String getUserName() {
        return userName;
    }

    public void setUserName(String userName) {
        this.userName = userName;
    }

    public void setAddressLine1(String addressLine1) {
        this.addressLine1 = addressLine1;
    }

    public String getAddressLine2() {
        return addressLine2;
    }

    public void setAddressLine2(String addressLine2) {
        this.addressLine2 = addressLine2;
    }

    public String getCity() {
        return city;
    }

    public void setCity(String city) {
        this.city = city;
    }

    public String getCountry() {
        return country;
    }

    public void setCountry(String country) {
        this.country = country;
    }

    public String getTempaddressLine1() {
        return tempaddressLine1;
    }

    public void setTempaddressLine1(String tempaddressLine1) {
        this.tempaddressLine1 = tempaddressLine1;
    }

    public String getTempaddressLine2() {
        return tempaddressLine2;
    }

    public void setTempaddressLine2(String tempaddressLine2) {
        this.tempaddressLine2 = tempaddressLine2;
    }

    public String getTempcountry() {
        return tempcountry;
    }

    public void setTempcountry(String tempcountry) {
        this.tempcountry = tempcountry;
    }

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }


    @Override
    public String toString() {
        final StringBuilder sb = new StringBuilder();
        sb.append("Parent");
        sb.append("{parentIdentifier='").append(parentIdentifier).append('\'');
        sb.append(", fullName='").append(fullName).append('\'');
        sb.append(", nameWithInitials='").append(nameWithInitials).append('\'');
        sb.append(", dob=").append(dob);
        sb.append(", gender='").append(gender).append('\'');
        sb.append(", profession='").append(profession).append('\'');
        sb.append(", comment='").append(comment).append('\'');
        sb.append(", userName='").append(userName).append('\'');
        sb.append(", email='").append(email).append('\'');
        sb.append(", mobileNo=").append(mobileNo);
        sb.append('}');
        return sb.toString();
    }
}
