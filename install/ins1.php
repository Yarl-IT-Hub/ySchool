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
include '../functions/ParamLib.php';
error_reporting(0);
session_start();
$_SESSION['username'] = $_POST["addusername"];
$_SESSION['password'] = $_POST["addpassword"];
$_SESSION['server'] = $_POST["server"];
$_SESSION['port'] = $_POST["port"];
$_SESSION['host'] = $_POST['server'] . ':' . $_POST['port'];
$err .= "
<html>
<head>
<link rel='stylesheet' type='text/css' href='../styles/installer.css' />
</head>
<body>
<div class='heading'>Couldn't connect to database server: " . $_SESSION['host'] . "
<div style='height:280px;'>

<br /><br /><span class='header_txt'>Possible causes are:</span>

<ul class='error_cause'>
<li>1. MySQL is not installed. Try downloading from <a href='http://dev.mysql.com/downloads/' target=_blank>MySQL Website</a></li>
<li>2. Username or Password or MySQL Configuration is incorrect</li>
<li>3. Php.ini is not properly configured. Search for MySQL in php.ini</li>
</ul>
<div style='height:55px;'>&nbsp;</div>";
if(clean_param($_REQUEST['mod'],PARAM_ALPHAMOD)=='upgrade'){
$err.="<a href='step1.php?mod=upgrade'>";
}else {
$err.="<a href='step1.php'>";
}
$err.="<img src='images/retry.png' border='0' /><a>

</div>
</div>
</body>
</html>
";


$dbconn = mysql_connect($_SESSION['host'],$_SESSION['username'],$_SESSION['password'])
or 
exit($err);


if(clean_param($_REQUEST['mod'],PARAM_ALPHAMOD)=='upgrade')
{
header('Location: selectdb.php');
}
else
{
header('Location: step2.php');
}
?>
                    