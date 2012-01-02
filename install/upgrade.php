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
include("custom.class.php");
$mysql_database=$_SESSION['db'];
$dbUser=$_SESSION['username'];
$dbPass=$_SESSION['password'];
$dbconn = mysql_connect($_SESSION['server'],$_SESSION['username'],$_SESSION['password']) or die() ;

mysql_select_db($mysql_database);
$proceed=mysql_query("SELECT name
FROM APP
WHERE value LIKE  '4.7%' OR value LIKE '4.8%'");
$proceed=mysql_fetch_assoc($proceed);
if($proceed['name']){
	$date_time=date("m-d-Y");
    $Export_FileName=$mysql_database.'_'.$date_time.'.sql';
	$myFile = "upgrade.sql";
    executeSQL($myFile);
	//backup_db($mysql_database,$Export_FileName);
	        exec("mysqldump -n -t -c --user $dbUser --password=$dbPass $mysql_database > $Export_FileName");
	
	$res_student_field='SHOW COLUMNS FROM STUDENTS WHERE FIELD LIKE "CUSTOM_%"';
	$objCustomStudents=new custom($mysql_database);
	$objCustomStudents->set($res_student_field,'STUDENTS');
	
	$res_staff_field='SHOW COLUMNS FROM STAFF WHERE FIELD LIKE "CUSTOM_%"';
	$objCustomStaff=new custom($mysql_database);
	$objCustomStaff->set($res_staff_field,'STAFF');
	
	mysql_query("drop database $mysql_database");
	mysql_query("create database $mysql_database");
	mysql_select_db($mysql_database);
	#$myFile = "opensis-4.5-schema-mysql.sql";
	#$myFile = "opensis-4.7-schema-mysql.sql";
        $myFile = "opensis-4.9-schema-mysql.sql";
    executeSQL($myFile);
	
	//execute custome field for student
	foreach($objCustomStudents->customQueryString as $query){
	mysql_query($query);
	}
	//execute custome field for satff
	foreach($objCustomStaff->customQueryString as $query){
	mysql_query($query);
	}
	
	#$myFile = "opensis-4.5-procs-mysql.sql";
	$myFile = "opensis-4.9-procs-mysql.sql";
    executeSQL($myFile);
	mysql_query("ALTER TABLE USER_PROFILES CHANGE `id` `id` INT( 8 ) NOT NULL");

    exec("mysql --user $dbUser --password=$dbPass $mysql_database < $Export_FileName");

	mysql_query("delete from APP");
	$appTable="INSERT INTO `APP` (`name`, `value`) VALUES
('version', '4.9'),
('date', 'February 01, 2011'),
('build', '02012011001'),
('update', '0'),
('last_updated', 'February 01, 2011')";
	mysql_query($appTable);
	$custom_insert=mysql_query("select count(*) from CUSTOM_FIELDS where title in('Ethnicity','Common Name','Physician','Physician Phone','Preferred Hospital','Gender','Email','Phone','Language')");
	$custom_insert=mysql_fetch_array($custom_insert);
	$custom_insert=$custom_insert[0];
	if($custom_insert<9){
	$custom_insert="INSERT INTO `CUSTOM_FIELDS` (`type`, `search`, `title`, `sort_order`, `select_options`, `category_id`, `system_field`, `required`, `default_selection`, `hide`) VALUES
('text', NULL, 'Ethnicity', 3, NULL, 1, 'Y', NULL, NULL, NULL),
('text', NULL, 'Common Name', 2, NULL, 1, 'Y', NULL, NULL, NULL),
('text', NULL, 'Physician', 6, NULL, 2, 'Y', NULL, NULL, NULL),
('text', NULL, 'Physician Phone', 7, NULL, 2, 'Y', NULL, NULL, NULL),
('text', NULL, 'Preferred Hospital', 8, NULL, 2, 'Y', NULL, NULL, NULL),
('text', NULL, 'Gender', 5, NULL, 1, 'Y', NULL, NULL, NULL),
('text', NULL, 'Email', 6, NULL, 1, 'Y', NULL, NULL, NULL),
('text', NULL, 'Phone', 9, NULL, 1, 'Y', NULL, NULL, NULL),
('text', NULL, 'Language', 8, NULL, 1, 'Y', NULL, NULL, NULL);";
mysql_query($custom_insert);
	}
	$login_msg=mysql_query("SELECT COUNT(*) FROM LOGIN_MESSAGE WHERE 1");
	$login_msg=mysql_fetch_array($login_msg);
	$login_msg=$login_msg[0];
	if($login_msg<1){
	$login_msg="INSERT INTO `LOGIN_MESSAGE` (`id`, `message`, `display`) VALUES
(1, 'This is a restricted network. Use of this network, its equipment, and resources is monitored at all times and requires explicit permission from the network administrator. If you do not have this permission in writing, you are violating the regulations of this network and can and will be prosecuted to the fullest extent of law. By continuing into this system, you are acknowledging that you are aware of and agree to these terms.', 'Y')";
mysql_query($login_msg);
	}
	
	$syear=mysql_fetch_assoc(mysql_query("select MAX(syear) as year, MIN(start_date) as start from SCHOOL_YEARS"));
	$_SESSION['syear']=$syear['year'];
                  $max_syear=$syear['year'];
                  $start_date=$syear['start'];
//=============================4.8.1 To 4.9===================================
$up_sql="INSERT INTO STUDENT_ENROLLMENT_CODES(syear,title,short_name,type)VALUES
(".$max_syear.",'Transferred in District','TRAN','TrnD'),
(".$max_syear.",'Transferred in District','TRAN','TrnE'),
(".$max_syear.",'Rolled over','ROLL','Roll'); ";
mysql_query($up_sql) or die(show_error1());

$up_sql ="INSERT INTO PROFILE_EXCEPTIONS (profile_id, modname, can_use, can_edit) VALUES
    (0, 'Scheduling/PrintSchedules.php','Y',NULL),
    (1, 'Scheduling/ViewSchedule.php', 'Y', NULL),
    (2, 'Scheduling/ViewSchedule.php', 'Y', NULL),
    (1, 'School_Setup/UploadLogo.php', 'Y', 'Y'); ";
mysql_query($up_sql) or die(show_error1());

$up_sql ="INSERT INTO PROGRAM_CONFIG (program, title, value) VALUES
    ('MissingAttendance', 'LAST_UPDATE','".$start_date."'); ";
mysql_query($up_sql) or die(show_error1());

$up_sql ="UPDATE PROFILE_EXCEPTIONS SET modname='Scheduling/ViewSchedule.php' WHERE modname='Scheduling/Schedule.php' AND (profile_id=0 OR profile_id=3);";
mysql_query($up_sql) or die(show_error1());
//====================================================================
	mysql_query("UPDATE SCHEDULE SET dropped='Y' WHERE end_date IS NOT NULL AND end_date < CURDATE() AND dropped='N'");
	header('Location: step5.php');
	unset($objCustomStudents);
	unset($objCustomStaff);
}else{
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="../styles/installer.css" />
</head>
<body>
<div class="heading2">Warning
<div style="height:270px;">
<br /><br />
 <table border="0" cellspacing="6" cellpadding="3" align="center">
            <tr>
			 <td colspan="2" align="center">
		<p>	The database you have chosen is not compliant with openSIS-CE ver 4.7 or ver 4.8 X We are unable to proceed.</p>

<p>Click Retry to select another database, or Exit to quit the installation.
</p>
		</td>
			</tr>
            <tr>
            	<td colspan="2" style="height:100px;">&nbsp;</td>
            </tr>
			<tr>


  <td align="left"><a href="selectdb.php"><img src="images/retry.png"  alt="Retry"  border="0"/></a></td>
    <td align="right"><a href="step0.php" ><img src="images/exit.png" alt="Exit" border="0" /></a></td>


	          </tr>
	        </table>
</div>
</div>
</body>
</html>
<?php }

function executeSQL($myFile)
{	
	$sql = file_get_contents($myFile);
	$sqllines = explode("\n",$sql);
	$cmd = '';
	$delim = false;
	foreach($sqllines as $l)
	{
		if(preg_match('/^\s*--/',$l) == 0)
		{
			if(preg_match('/DELIMITER \$\$/',$l) != 0)
			{	
				$delim = true;
			}
			else
			{
				if(preg_match('/DELIMITER ;/',$l) != 0)
				{
					$delim = false;
				}
				else
				{
					if(preg_match('/END\$\$/',$l) != 0)
					{
						$cmd .= ' END';
					}
					else
					{
						$cmd .= ' ' . $l . "\n";
					}
				}
				if(preg_match('/.+;/',$l) != 0 && !$delim)
				{
					$result = mysql_query($cmd) or die(show_error1());
					$cmd = '';
				}
			}
		}
	}
}

function show_error1()
{
    $err .= "
<html>
<head>
<link rel='stylesheet' type='text/css' href='../styles/installer.css' />
</head>
<body>

<div style='height:280px;'>

<br /><br /><span class='header_txt'></span>

<div align='center'>
Username or Password or MySQL Configuration is incorrect
</div>
<div style='height:50px;'>&nbsp;</div>";
$err.="<div align='center'><a href='selectdb.php?mod=upgrade'><img src='images/retry.png' border='0' /></a> &nbsp; &nbsp; <a href='step0.php'><img src='images/exit.png' border='0' /></a></div>";
$err.="</div></body></html>";
echo $err;
}

?>
