package org.yarlithub.yschool.web.commons;

/**
 * Created with IntelliJ IDEA.
 * User: Pirinthapan
 * Date: 4/5/14
 * Time: 3:42 PM
 * To change this template use File | Settings | File Templates.
 */
public class SearchResult {
    private String name;
    private String type;
    private int id;

    public SearchResult(String name, String type, int id) {
        this.name = name;
        this.type = type;
        this.id = id;

    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }
}
