package org.yarlithub.yschool.web.commons;

import org.springframework.context.annotation.Scope;
import org.springframework.stereotype.Controller;

import javax.faces.bean.ManagedBean;
import java.util.ArrayList;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: JayKrish
 * Date: 3/23/14
 * Time: 11:30 PM
 * To change this template use File | Settings | File Templates.
 */
@ManagedBean
@Scope(value = "session")
@Controller
public class SearchBean {
    static List<Item> itemArrayList;

    public SearchBean() {
         itemArrayList = new ArrayList<Item>();
        itemArrayList.add(new Item("atest", "valuetest",1));
        itemArrayList.add(new Item("atest", "valuetest",2));
        itemArrayList.add(new Item("btest", "valuetest",3));

    }

    public List<Item> getItemArrayList() {
        return itemArrayList;
    }

    public void setItemArrayList(List<Item> itemArrayList) {
        this.itemArrayList = itemArrayList;
    }

    public List<Item> getList(String qu) {
        return itemArrayList;
    }

    public class Item {
        String name;
        String val;
        int no;

        public Item(String name, String val, int no) {
            this.name = name;
            this.val = val;
            this.no=no;
        }

        public int getNo() {
            return no;
        }

        public void setNo(int no) {
            this.no = no;
        }

        public String getName() {
            return name;
        }

        public void setName(String name) {
            this.name = name;
        }

        public String getVal() {
            return val;
        }

        public void setVal(String val) {
            this.val = val;
        }
    }
}
