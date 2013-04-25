package org.yarlithub.yschool.yearly;

import org.yarlithub.yschool.repository.Medium;

import javax.annotation.PostConstruct;
import javax.faces.bean.ManagedBean;
import javax.faces.bean.SessionScoped;
import java.io.Serializable;
import java.util.List;

@ManagedBean
@SessionScoped
public class MediumBean implements Serializable {
    private List<Medium> availableMediums;

    @PostConstruct
    public void init() {
        availableMediums = Medium.findAll();
    }

    public List<Medium> getAvailableMediums() {
        return availableMediums;
    }
}
