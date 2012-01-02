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
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link rel="stylesheet" href="../styles/installer.css" type="text/css" />
<script type="text/javascript" src="js/validator.js"></script>
</head>
<body>
<div class="heading">Your School has been created
<div style="background-image:url(images/step4_new.gif); background-repeat:no-repeat; background-position:50% 20px; height:270px;">
  <form name='step4' id='step4' method="post" action="ins4.php">
    <table border="0" cellspacing="6" cellpadding="3" align="center">
      <tr>
        <td  align="center" style="padding-top:36px; padding-bottom:16px">Step 4 of 5</td>
      </tr>
      <tr>
        <td align="center"><strong>Please create Admin Username and Password:</strong></td>
      </tr>
      <tr>
        <td align="left" valign="top"><table width="245" border="0" cellpadding="4" cellspacing="0" id="table1">
            <tr>
              <td width="137">Username:</td>
              <td><input type="text" name="auname" size="20" /></td>
            </tr>
            <tr>
              <td width="137">Password:</td>
              <td><input type="password" name="apassword" size="20" /></td>
            </tr>
            <tr>
              <td width="137">Confirm Password:</td>
              <td><input type="password" name="capassword" size="20" /></td>
            </tr>
            <tr>
              <td></td>
              <td><input type="submit" value="Save & Next"  class="btn_wide" name="btninsert" /></td>
            </tr>
          </table>
          <script language="JavaScript" type="text/javascript">
			
			function CheckPasswords()
			{
				  var frm = document.forms["step4"];
				  if(frm.apassword.value != frm.capassword.value)
					{
						alert('The Password and Confirm Password does not match!');
						frm.capassword.focus();
						return false;
					  }
					  else
					  {
						return true;
					  }
			}
			
			
			
				var frmvalidator  = new Validator("step4");
				frmvalidator.addValidation("auname","req","Please enter the Administrative Username");
				frmvalidator.addValidation("apassword","req","Please enter the Administrative Password");
				frmvalidator.addValidation("capassword","req","Retype the Administrative Password");
				frmvalidator.setAddnlValidationFunction("CheckPasswords"); 
			
			</script>        </td>
      </tr>
    </table>
  </form>
</div>
</div>
</body>
</html>
