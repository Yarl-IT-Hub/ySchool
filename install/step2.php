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
<?php
if($_REQUEST['err'])
echo "<script type='text/javascript'>alert('".$_REQUEST['err']."')</script>";


?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link rel="stylesheet" href="../styles/installer.css" type="text/css" />
<script type="text/javascript" src="js/validator.js"></script>
</head>
<body>
<div class="heading">Connected to MySQL DBMS
  <div style="background-image:url(images/step2_new.gif); background-repeat:no-repeat; background-position:50% 20px; height:270px;">
    <form name='step2' id='step2' method="post" action="ins2.php">
      <table border="0" cellspacing="6" cellpadding="3" align="center">
        <tr>
          <td  align="center" style="padding-top:36px; padding-bottom:16px">Step 2 of 5</td>
        </tr>
        <tr>
          <td align="center" valign="top"><strong>System needs to create a new database.<br />
          (This could take up to a minute to complete)<br />
            Please enter a name.</strong></td>
        </tr>
        <tr>
          <td align="center" valign="top"><input type="text" name="db" size="20" value="opensis"  /></td>
        </tr>
	<tr>

	  <td align="center" valign="top"><input type="checkbox" name="purgedb" value="opensis" /><strong>Remove data from existing database</strong></td>
	</tr>
        <tr>
          <td align="center"><input type="submit" value="Save & Next" class="btn_wide" name="Add_DB"  />
          <script language="JavaScript" type="text/javascript">
			var frmvalidator  = new Validator("step2");
			frmvalidator.addValidation("db","req","Please enter the Database Name");
			</script>
			</td>
        </tr>
      </table>
    </form>
  </div>
</div>
</body>
</html>
