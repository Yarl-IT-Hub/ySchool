<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. web: www.os4ed.com
#
#  openSIS is  web-based, open source, and comes packed with features that 
#  include student demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  This program is released under the terms of the GNU General Public License as  
#  published by the Free Software Foundation, version 2 of the License. 
#  See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#***************************************************************************************
error_reporting(0);
include('Redirect_root.php');
include("data.php");
$connection=mysql_connect($DatabaseServer,$DatabaseUsername,$DatabasePassword);
mysql_select_db($DatabaseName,$connection);
$log_msg=mysql_fetch_assoc(mysql_query("SELECT MESSAGE FROM LOGIN_MESSAGE WHERE DISPLAY='Y'"));
//mysql_query("CALL SEAT_COUNT()");
Warehouse('header');
	echo '<link rel="stylesheet" type="text/css" href="styles/login.css">';
	echo '<script type="text/javascript" src="js/tabmenu.js"></script>';
	echo "
	<script type='text/javascript'>
	function delete_cookie (cookie_name)
		{
  			var cookie_date = new Date ( );
  			cookie_date.setTime ( cookie_date.getTime() - 1 );
			  document.cookie = cookie_name += \"=; expires=\" + cookie_date.toGMTString();
		}

</script>";
	echo "<BODY onLoad='document.loginform.USERNAME.focus();  delete_cookie(\"dhtmlgoodies_tab_menu_tabIndex\");'>";
	echo "";
	
	echo "
	<form name=loginform method='post' action='index.php'>
	<table width='100%' height='100%' border='0' cellspacing='0' cellpadding='0'>
  <tr>
    <td valign='middle' height='100%'><table class='wrapper' border='0' cellspacing='0' cellpadding='0' align='center'>
        
        <tr>
          <td class='header'><table width='100%' border='0' cellspacing='0' cellpadding='0' class='logo_padding'>
              <tr>
                <td><img src='assets/osis_logo.png' border='0' /></td>
                <td align='right'><a href='http://www.os4ed.com' target=_blank ><img src='assets/os4ed_logo.png' height='62' width='66' border='0'/></a></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td class='content'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
              <tr>
                <td><table width='100%' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                      <td class='header_padding'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
                          <tr>
                            <td class='header_txt'>Student Information System</td>
                          </tr>
                        </table></td>
                    </tr>";
					if($_REQUEST['maintain']=='Y'){
				echo "<tr><td align='center' style='color:red'><b>"."openSIS is under maintenance and login privileges have been turned off. Please log in when it is available again."."</b></td></tr>";
					}
                    echo "<tr>
                      <td class='padding'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
                          <tr>
                            <td>
                
				<table border='0' width='100%' cellspacing='2' cellpadding='2' align=center>
                 
                  <tr>
                    <td width='40%' align='right'>User Name :</td>
                    <td width='60%'><input name='USERNAME' type='text' class='login_txt'></td>
                  </tr>
                  <tr>
                    <td align='right'>Password :</td>
                    <td><input name='PASSWORD' class='login_txt' type='password' AUTOCOMPLETE = 'off'></td>
                  </tr>
				  <tr><td colspan=2>
				  " ;
				  
				  if($_REQUEST['reason'])
				$note[] = 'You must have javascript enabled to use openSIS.';
				echo ErrorMessage($error,'Error');
	
				  echo "
				  </td></tr><tr>
                    <td></td>
                    <td><input name='' type='submit' class='login' value='' onMouseDown=Set_Cookie('dhtmlgoodies_tab_menu_tabIndex','',-1) />
                    </td>
                  </tr>
				  </table>
				  </td>
                          </tr>
                          <tr>
                            <td align='center'><p style='padding:6px;'>".$log_msg['MESSAGE']."</p></td>
                          </tr>
                        </table></td>
                    </tr>
                  </table>
              </tr>
            </table>
        <tr>
          <td class='footer' valign='top'><table width='100%' border='0' cellspacing='0' cellpadding='0'>
              <tr>
                <td class='margin'></td>
              </tr>
              <tr>
                <td align='center' class='copyright'>
                Copyright &copy; 2011 Open Solutions for Education, Inc. (<a href='http://www.os4ed.com' target='_blank'>OS4Ed</a>).
                openSIS is licensed under the <a href='http://www.gnu.org/licenses/gpl.html' target='_blank'>GPL License</a>.
                </td>
              </tr>
            </table></td>
        </tr>
      </table></td>
  </tr>
</table>
</td>
</tr>
</table></form>
";

	Warehouse("footer");
?>
