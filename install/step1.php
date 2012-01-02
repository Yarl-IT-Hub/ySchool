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
if(page && page!="index.php" ){
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
    <script type="text/javascript">
function showAlert() {
    var divAlert = document.getElementById('divAlert');
    var divConnInfo = document.getElementById('divConnInfo');

    divAlert.style.display='';
    divConnInfo.style.display='none';
}
function hideAlert() {
    var divAlert = document.getElementById('divAlert');
    var divConnInfo = document.getElementById('divConnInfo');

    divAlert.style.display='none';
    divConnInfo.style.display='';
}
    </script>
</head>
<body>
    <?php
    error_reporting(0);
    session_start();

    if($_REQUEST['mod']=='upgrade')
    {
        echo '<div class="heading">You have chosen upgrade';
        $_SESSION['mod'] = 'upgrade';
    }
    else
    {
        echo '<div class="heading">Beginning new openSIS installation';
    }

    echo '<div id="divAlert" style="display:none; height:270px;">';

    $myFile = "../data.php";
    $fh1 = fopen($myFile, 'w');

    if ($fh1 == FALSE)
    {
        echo '<br />';
        echo '<br />This install has no rights to create or update file data.php.';
        echo '<br />';
        echo '<br />You may proceed with this installation, but database access information will not be saved and ';
        echo '<br />the install process will restart when trying to use this system again.';
    }
    fclose($fh1);

    $myFile = "../assets/StudentPhotos/dummy.txt";
    $fh2 = fopen($myFile, 'w');

    if ($fh2 == FALSE)
    {
        echo '<br />';
        echo '<br />Unable to write inside assets/StudentPhotos directory.';
        echo '<br />Student photos will fail to be saved until this permission issue is solved';
    }
    else
    {
        unlink($myFile);
    }
    fclose($fh2);

    $myFile = "../assets/UserPhotos/dummy.txt";
    $fh3 = fopen($myFile, 'w');

    if ($fh3 == FALSE)
    {
        echo '<br />';
        echo '<br />Unable to write inside assets/UserPhotos directory.';
        echo '<br />User photos will fail to be saved until this permission issue is solved';
    }
    else
    {
        unlink($myFile);
    }
    fclose($fh3);

    echo '<br />';
    echo '<br />';
    echo '<br />It is recommended to solve all permission issue before performing the installation.';
    echo '<br />';
    echo '<br />';
    echo '<input type="button" value="Continue" class="btn_wide" onclick="hideAlert()" />';
    echo '</div>';

    if ($fh1 && $fh2 && fh3)
        // show Connection information fields
        echo '<div id="divConnInfo" style="';
    else
        // hide Connection information fields
        echo '<div id="divConnInfo" style="display:none; ';
    
    ?>
	<?php
	if($_SESSION['mod']!='upgrade')
	{
	echo 'background-image:url(images/step1_new.gif); background-repeat:no-repeat; background-position:50% 20px; height:270px;">';	
	}
	else
	{
	echo 'background-image:url(images/step1.gif); background-repeat:no-repeat; background-position:50% 20px; height:270px;">';
	}
	?>

    <form name='step1' id='step1' method="post" action="ins1.php<?php echo ($_REQUEST['mod']=='upgrade')?'?mod=upgrade':''; ?>">
        <table border="0" cellspacing="6" cellpadding="3" align="center">
            <tr>
                <td  align="center" style="padding-top:36px">
                    <?php
                    if($_SESSION['mod']!='upgrade')
                    {
                    echo "Step 1 of 5";
                    }
                    else
                    {
                    echo "Step 1 of 4";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td align="center"><strong>Please Enter MySQL Connection Information</strong></td>
            </tr>
            <tr>
                <td align="left" valign="top"><table width="245" border="0" cellpadding="2" cellspacing="0" id="table1">
                        <tr>
                            <td width="137">Server:</td>
                            <td><input type="text" name="server" size="20" value="localhost" /></td>
                        </tr>
                        <tr>
                            <td width="137">Port:</td>
                            <td><input type="text" name="port" size="20" value="3306" /></td>
                        </tr>
                        <tr>
                            <td width="137">Admin Username:</td>
                            <td><input type="text" name="addusername" size="20" value="root" /></td>
                        </tr>
                        <tr>
                            <td width="137">Admin Password</td>
                            <td><input type="password" name="addpassword" size="20" /></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="submit" value="Save & Next" class="btn_wide" name="DB_Conn" /></td>
                        </tr>
                    </table>
                    <script type="text/javascript">
<?php
if ($fh1 && $fh2 && fh3)
    echo 'hideAlert();';
else
    echo 'showAlert();';
?>
var frmvalidator  = new Validator("step1");
frmvalidator.addValidation("server","req","Please enter the Server Name");
frmvalidator.addValidation("port","req","Please enter the Port");
frmvalidator.addValidation("addusername","req","Please enter the MySQL Admin Username");
                    </script>
                </td>
            </tr>
        </table>
    </form>
    </div>
</body>
</html>
