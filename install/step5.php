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
echo '<script type="text/javascript">
var page=parent.location.href.replace(/.*\//,"");
if(page && page!="index.php"){
	window.location.href="index.php";
	}

</script>';
if($_SESSION['mod']=='upgrade'){

$myFile = "../data.php";
$fh = fopen($myFile, 'w');

if ($fh == TRUE)
{
    $string .= "<"."?php \n";
    $string .= "$"."DatabaseType = 'mysql'; \n"	;
    $string .= "$"."DatabaseServer = '".$_SESSION['server']."'; \n"	;
    $string .= "$"."DatabaseUsername = '".$_SESSION['username']."'; \n" ;
    $string .= "$"."DatabasePassword = '".$_SESSION['password']."'; \n";
    $string .= "$"."DatabaseName = '".$_SESSION['db']."'; \n";
    $string .= "$"."DatabasePort = '".$_SESSION['port']."'; \n";
    $string .="?".">";

    fwrite($fh, $string);
}

fclose($fh);
$display_text='Your system has been successfully upgraded to version 4.9. Please click the Login button to proceed to login with your existing login credentials.
';
echo "<html><head><link rel='stylesheet' type='text/css' href='../styles/installer.css'></head><body>
<div class=\"heading\">Installation Successful
<div style=\"background-image:url(images/step4.gif); background-repeat:no-repeat; background-position:50% 20px; height:270px;\">
<table border=\"0\" width=\"500\" cellspacing=\"6\" cellpadding=\"3\" align=\"center\">
      <tr>
        <td  align=\"center\" style=\"padding-top:36px; padding-bottom:16px\">Step 4 of 4</td>
      </tr>
	  <tr><td>
	  <div>".$display_text."</div>
	  </td></tr>
	  <tr>
	  	<td height=20>&nbsp;</td>
	  </tr>
      <tr>
        <td align=\"center\"><a href='../index.php?modfunc=logout&ins=comp' target=\"_parent\"><img src='images/login.png' border=0 /></a></td>
      </tr>
 	</td>
      </tr>
    </table></div></div>
</body></html>
";
}
elseif($_SESSION['mod']!='upgrade')
{
$myFile = "../data.php";
$fh = fopen($myFile, 'w');

if ($fh == TRUE)
{
    $string .= "<"."?php \n";
    $string .= "$"."DatabaseType = 'mysql'; \n"	;
    $string .= "$"."DatabaseServer = '".$_SESSION['server']."'; \n"	;
    $string .= "$"."DatabaseUsername = '".$_SESSION['username']."'; \n" ;
    $string .= "$"."DatabasePassword = '".$_SESSION['password']."'; \n";
    $string .= "$"."DatabaseName = '".$_SESSION['db']."'; \n";
    $string .= "$"."DatabasePort = '".$_SESSION['port']."'; \n";
    $string .= "#$"."DefaultSyear = '".$_SESSION['syear']."'; \n";
    $string .="?".">";

    fwrite($fh, $string);
}

fclose($fh);
$display_text='';
if($_SESSION['school_installed']=='both'){
	
	$display_text="A  school has been created in the name of: ".$_SESSION['sname'].".

You need to follow the instructions in the administrator manual for setting up the school properly. The manual is located in the docs folder.
<br><br>
You have also installed the sample school data. You can select your school or the sample school by clicking on the drop down menu of the school select field on the upper right hand corner.";
#You have also installed the sample school data, you can access it by clicking on the drop down menu  of  the school select field.";
	}else if($_SESSION['school_installed']=='user'){
		$display_text="A  school has been created in the name of: ".$_SESSION['sname'].".

You need to follow the instructions in the administrator manual for setting up the school properly. The manual is located in the docs folder.";
		}else if($_SESSION['school_installed']=='sample'){
			$display_text="You have installed openSIS with a sample school data. Use this school to get familiar with the system and as a guide for creating your own school.  You can also follow the instructions in the administrator manual for setting up and configuring a new school. The manual is located in the docs folder.";
			}

echo "<html><head><link rel='stylesheet' type='text/css' href='../styles/installer.css'></head><body>
<div class=\"heading\">Installation Successful
<div style=\"background-image:url(images/step5.gif); background-repeat:no-repeat; background-position:50% 20px; height:270px;\">
<table border=\"0\" width=\"500\" cellspacing=\"6\" cellpadding=\"3\" align=\"center\">
      <tr>
        <td  align=\"center\" style=\"padding-top:36px; padding-bottom:16px\">Step 5 of 5</td>
      </tr>
	  <tr><td>
	  <div>".$display_text."</div>
	  </td></tr>
      <tr>
        <td align=\"center\"><a href='../index.php?modfunc=logout&ins=comp' target=\"_parent\"><img src='images/login.png' border=0 /></a></td>
      </tr>
 	</td>
      </tr>
    </table></div></div>
</body></html>
";
}
else
{
echo "<html><head><link rel='stylesheet' type='text/css' href='../styles/installer.css'></head><body>
<div class=\"heading\">System Successfully Upgraded
<div style=\"background-image:url(images/step4.gif); background-repeat:no-repeat; background-position:50% 20px; height:270px;\">
<table border=\"0\" cellspacing=\"6\" cellpadding=\"3\" align=\"center\">
      <tr>
        <td  align=\"center\" style=\"padding-top:36px; padding-bottom:16px\">Step 3 of 3</td>
      </tr>
	  
      <tr>
        <td align=\"center\"><a href='../index.php?modfunc=logout'><img src='images/login.png' border=0 /></a></td>
      </tr>
 	</td>
      </tr>
    </table></div></div>
</body></html>
";
}
session_unset();
session_destroy();
?>
