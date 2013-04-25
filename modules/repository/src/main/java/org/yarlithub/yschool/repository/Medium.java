package org.yarlithub.yschool.repository;

import org.yarlithub.yschool.repository.util.HibernateUtil;

import javax.persistence.*;
import java.io.Serializable;
import java.util.List;

@NamedQueries({
        @NamedQuery(
                name = "findAllMediums",
                query = "from Medium"
        )
})
@Entity
@Table(name = "medium")
public class Medium implements Serializable {

    public enum Language {
        ENGLISH {
            @Override
            public String id() {
                return "English";
            }
        },
        TAMIL {
            @Override
            public String id() {
                return "Tamil";
            }
        };

        public abstract String id();
    }

    @Id
    @GeneratedValue(strategy = GenerationType.AUTO)
    private Long id;

    private Language language;

    public Medium() {
    }

    public Medium(Language language) {
        this.language = language;
    }

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public Language getLanguage() {
        return language;
    }

    public void setLanguage(Language language) {
        this.language = language;
    }

    public void save() {
        HibernateUtil.getSessionFactory().getCurrentSession().save(this);
    }

    public void update() {
        HibernateUtil.getSessionFactory().getCurrentSession().update(this);
    }

    public void delete() {
        HibernateUtil.getSessionFactory().getCurrentSession().delete(this);
    }

    public static List<Medium> findAll() {
        return HibernateUtil.getCurrentSession().getNamedQuery("findAllMediums").list();
    }

    @Override
    public String toString() {
        return "Medium{" +
                "id=" + id +
                ", language=" + language +
                '}';
    }

    public String description() {
        return language.id();
    }
}
