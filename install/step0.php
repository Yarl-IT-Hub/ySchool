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
session_start();
session_destroy();
echo '<script type="text/javascript">
var page=parent.location.href.replace(/.*\//,"");
if(page && page!="index.php"){
	window.location.href="index.php";
	}

</script>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link rel="stylesheet" href="../styles/installer.css" type="text/css" />
</head>
<body>
<div class="heading">openSIS Installer<!--, Please remember to read the <a href="../INSTALL.txt" target="_new">INSTALL.TXT</a> file first.-->
  <table width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr>
      <td>
	      <table style="height:270px;" border="0" cellspacing="12" cellpadding="12" align="center">
            <tr>
<?php
if($_GET["upreq"] == 'true')
{
 	echo '<td>You were redirected to this page because an upgrade is needed.<br>
     Please, proceed using the action below.</td>';
    echo '</tr><tr>';
    echo '<td valign="middle" align="center"><a href="step0.1.php?mod=upgrade"><img src="images/upgrade.png" alt="Upgrade OpenSIS" width="122" height="150" border="0"/></a></td>';
   /* echo '<td>You were redirected to this page because an upgrade is needed.<br>
Please, proceed using the action below.</td>';
    echo '</tr><tr>';
    echo '<td valign="middle" align="center"><a href="step1.php?mod=upgrade"><img src="images/upgrade.png" alt="Upgrade OpenSIS" width="122" height="150" border="0"/></a></td>';*/
}
else
{
    echo '<td valign="middle" align="center"><a href="step1.php"><img src="images/install.png" width="122" alt="New Installation" height="150" border="0"/></a></td>';
    echo '<td valign="middle" align="center"><a href="step0.1.php?mod=upgrade"><img src="images/upgrade.png" alt="Upgrade OpenSIS" width="122" height="150" border="0"/></a></td>';
}
?>
	          </tr>
	        </table>
		</td>
	</tr>
  </table>
</div>
</body>
</html>
