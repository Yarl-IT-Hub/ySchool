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
<div class="heading">Warning

<center>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>
	      <table width="100%" style="height:270px;" border="0" cellspacing="12" cellpadding="12" align="center">
            <tr>
			 	<td valign="middle" align="center" colspan="2" style="font-size:14px;">Please be advised that only openSIS-CE version 4.7 or 4.8 X can be<br /> upgraded to the new version 4.9 via this installer.
			 <br /><br />
             If you are running version 4.7 or 4.8 X click Continue to upgrade,<br /> otherwise click Go Back and try New Installation.
				</td>
			</tr>
			<tr>
<?php

    echo '<td align="left"><a href="step0.php"><img src="images/go_back.png"  alt="New Installation"  border="0"/></a></td>';
    echo '<td align="right"><a href="step1.php?mod=upgrade"><img src="images/continue.png" alt="Upgrade OpenSIS" border="0"/></a></td>';

?>
	          </tr>
	        </table>
		</td>
	</tr>
  </table>
  </center>
</div>
</body>
</html>
