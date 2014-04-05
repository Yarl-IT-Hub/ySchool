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
    List<Player> players;

    private Player selectedPlayer1;

    private Player selectedPlayer2;

    public SearchBean() {
        players = PlayerConverter.playerDB;
    }

    public Player getSelectedPlayer1() {
        return selectedPlayer1;
    }

    public void setSelectedPlayer1(Player selectedPlayer1) {
        this.selectedPlayer1 = selectedPlayer1;
    }

    public Player getSelectedPlayer2() {
        return selectedPlayer2;
    }

    public void setSelectedPlayer2(Player selectedPlayer2) {
        this.selectedPlayer2 = selectedPlayer2;
    }

    public List<Player> completePlayer(String query) {
        List<Player> suggestions = new ArrayList<Player>();

        for(Player p : players) {
            if(p.getName().startsWith(query))
                suggestions.add(p);
        }

        return suggestions;
    }
}
