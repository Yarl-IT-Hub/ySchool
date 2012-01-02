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

include('../../Redirect_modules.php');
require_once("data.php");
$print_form=1;
$output_messages=array();
//test mysql connection

if($_REQUEST['modfunc']=='cancel')
echo " ";
else if(User('PROFILE')=='admin'&& isset($_REQUEST['action']) )
{
	$mysql_host=$DatabaseServer;
	$mysql_database=$DatabaseName;
	$mysql_username=$DatabaseUsername;
	$mysql_password=$_REQUEST['mysql_password'];
	if( 'Test Connection' == $_REQUEST['action'])
	{
		_mysql_test($mysql_host,$mysql_database, $mysql_username, $mysql_password);
	}
	else if( 'Backup' == $_REQUEST['action'])
	{
		_mysql_test($mysql_host,$mysql_database, $mysql_username, $mysql_password);
		
			$print_form=0;
			//ob_start("ob_gzhandler");\
                        $date_time=date("m-d-Y");
                    ;
                        $Export_FileName=$mysql_database.'_'.$date_time ;

			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="'.$Export_FileName.'.sql"');
			//echo "/*mysqldump.php version $mysqldump_version \n";
                        echo "-- Server version:". mysql_get_server_info()."\n";
                        echo "-- PHP Version: ".phpversion()."\n\n";
                        echo 'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";';

                        echo "\n\n/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
                        echo "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
                        echo "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
                        echo "/*!40101 SET NAMES utf8 */;\n\n";

                        echo "--\n";
                        echo "-- Database: `$mysql_database`\n";
                        echo "--\n\n";
                        echo "-- --------------------------------------------------------\n\n";



			_mysqldump($mysql_database);
			//header("Content-Length: ".ob_get_length());
			//ob_end_flush();
		}
}
function _mysqldump($mysql_database)
{


        $sql="show tables where tables_in_$mysql_database not like 'COURSE_DETAILS%' and tables_in_$mysql_database not like 'ENROLL_GRADE%'
               and tables_in_$mysql_database not like 'MARKING_PERIODS%' and tables_in_$mysql_database not like 'TRANSCRIPT_GRADES%' ;";
	$result= mysql_query($sql);
	if( $result)
	{
		while( $row= mysql_fetch_row($result))
		{
			_mysqldump_table_structure($row[0]);
			_mysqldump_table_data($row[0]);

		}
	echo "--
              --
              --

            CREATE VIEW MARKING_PERIODS AS
            SELECT q.marking_period_id, 'openSIS' AS mp_source, q.syear,
            q.school_id, 'quarter' AS mp_type, q.title, q.short_name,
            q.sort_order, q.semester_id AS parent_id,
            s.year_id AS grandparent_id, q.start_date,
            q.end_date, q.post_start_date,
            q.post_end_date, q.does_grades,
            q.does_exam, q.does_comments
           FROM SCHOOL_QUARTERS q
           JOIN SCHOOL_SEMESTERS s ON q.semester_id = s.marking_period_id
           UNION
            SELECT marking_period_id, 'openSIS' AS mp_source, syear,
            school_id, 'semester' AS mp_type, title, short_name,
            sort_order, year_id AS parent_id,
            -1 AS grandparent_id, start_date,
            end_date, post_start_date,
            post_end_date, does_grades,
            does_exam, does_comments
           FROM SCHOOL_SEMESTERS
           UNION
            SELECT marking_period_id, 'openSIS' AS mp_source, syear,
            school_id, 'year' AS mp_type, title, short_name,
            sort_order, -1 AS parent_id,
            -1 AS grandparent_id, start_date,
            end_date, post_start_date,
            post_end_date, does_grades,
            does_exam, does_comments
            FROM SCHOOL_YEARS
           UNION
           SELECT marking_period_id, 'History' AS mp_source, syear,
	   school_id, mp_type, name AS title, NULL AS short_name,
	   NULL AS sort_order, parent_id,
	   -1 AS grandparent_id, NULL AS start_date,
	   post_end_date AS end_date, NULL AS post_start_date,
	   post_end_date, 'Y' AS does_grades,
	   NULL AS does_exam, NULL AS does_comments
           FROM HISTORY_MARKING_PERIODS;\n

          

             CREATE VIEW COURSE_DETAILS AS
             SELECT cp.school_id, cp.syear, cp.marking_period_id, cp.period_id, c.subject_id,
	     cp.course_id, cp.course_period_id, cp.teacher_id, cp.secondary_teacher_id, c.title AS course_title,
	     cp.title AS cp_title, cp.grade_scale_id, cp.mp, cp.credits
             FROM COURSE_PERIODS cp, COURSES c WHERE (cp.course_id = c.course_id);\n\n

            CREATE VIEW ENROLL_GRADE AS
            SELECT e.id, e.syear, e.school_id, e.student_id, e.start_date, e.end_date, sg.short_name, sg.title
            FROM STUDENT_ENROLLMENT e, SCHOOL_GRADELEVELS sg WHERE (e.grade_id = sg.id);\n\n

            CREATE VIEW TRANSCRIPT_GRADES AS
            SELECT s.id AS school_id, mp.mp_source, mp.marking_period_id AS mp_id,
	    mp.title AS mp_name, mp.syear, mp.end_date AS posted, rcg.student_id,
	    sms.grade_level_short AS gradelevel, rcg.grade_letter, rcg.weighted_gp AS gp_value,
	    rcg.unweighted_gp AS weighting, rcg.gp_scale, rcg.credit_attempted, rcg.credit_earned,
	    rcg.credit_category, rcg.course_title AS course_name,
	    sms.cum_weighted_factor AS cum_gp_factor,
	   (sms.cum_weighted_factor * s.reporting_gp_scale) AS cum_gpa,
	   ((sms.sum_weighted_factors / sms.count_weighted_factors) * s.reporting_gp_scale) AS gpa,
	   sms.cum_rank,mp.sort_order
           FROM STUDENT_REPORT_CARD_GRADES rcg
           INNER JOIN MARKING_PERIODS mp ON mp.marking_period_id = rcg.marking_period_id AND mp.mp_type IN ('year','semester','quarter')
           INNER JOIN STUDENT_MP_STATS sms ON sms.student_id = rcg.student_id AND sms.marking_period_id = rcg.marking_period_id
           INNER JOIN SCHOOLS s ON s.id = mp.school_id;\n
            ";

        }
	else
	{
		echo "/* no tables in $mysql_database \n";
	}
	mysql_free_result($result);
}

function _mysqldump_table_structure($table)
{
	echo "--\n";
        echo "-- Table structure for table `$table` \n";
        echo "--\n\n";

       // echo "/* Table structure for table `$table` \n";
	/*if( isset($_REQUEST['sql_drop_table']))
	{
		echo "DROP TABLE IF EXISTS `$table`;\n\n";
	}*/
	         $sql="show create table `$table`; ";
		$result=mysql_query($sql);
		if( $result)
		{
			if($row= mysql_fetch_assoc($result))
			{
				echo $row['Create Table'].";\n\n";
			}
		}
		mysql_free_result($result);

}

function _mysqldump_table_data($table)
{
	$sql="select * from `$table`;";
	$result=mysql_query($sql);
	if( $result)
	{
		$num_rows= mysql_num_rows($result);
		$num_fields= mysql_num_fields($result);
                $numfields = mysql_num_fields($result);

		if( $num_rows> 0)
		{
			//echo "/* dumping data for table `$table` \n";

                        echo "--\n";
                        echo "-- Dumping data for table  `$table` \n";
                        echo "--\n";

			$field_type=array();
			$i=0;
			while( $i <$num_fields)
			{
				$meta= mysql_fetch_field($result, $i);
				array_push($field_type, $meta->type);
                                $colfields[] = mysql_field_name($result,$i);
				$i++;
			}
			//print_r( $field_type);
			echo "insert into `$table` (";
                        for($j=0; $j < $num_fields; $j++)
                        {
                            if($j==$num_fields-1)
                            echo $colfields[$j];
                        else
                        echo $colfields[$j].",";
                        }
                        echo ")values\n";
			$index=0;
			while( $row= mysql_fetch_row($result))
			{
				echo "(";
				for( $i=0; $i <$num_fields; $i++)
				{
					if( is_null( $row[$i]))
						echo "null";
					else
					{
						switch( $field_type[$i])
						{
							case 'int':
								echo $row[$i];
								break;
							case 'string':
							case 'blob' :
							default:
								echo "'".mysql_real_escape_string($row[$i])."'";
						}
					}
					if( $i <$num_fields-1)
						echo ",";
				}
				echo ")";
				if( $index <$num_rows-1)
					echo ",";
				else
					echo ";";
				echo "\n";
				$index++;
			}
		}
	}
	mysql_free_result($result);
	echo "\n";
}
function _mysql_test($mysql_host,$mysql_database, $mysql_username, $mysql_password)
{
	global $output_messages;
	$link = mysql_connect($mysql_host, $mysql_username, $mysql_password);
	if (!$link)
	{
	   array_push($output_messages, 'Could not connect: ' . mysql_error());
	}
	else
	{
		array_push ($output_messages,"Connected with MySQL server:$mysql_username@$mysql_host successfully");
		$db_selected = mysql_select_db($mysql_database, $link);
		if (!$db_selected)
		{
			array_push ($output_messages,'Can\'t use $mysql_database : ' . mysql_error());
		}
		else
			array_push ($output_messages,"Connected with MySQL database:$mysql_database successfully");
	}
}
if( $print_form>0  && !$_REQUEST['modfunc']=='cancel')
{
?>
<br>
<?php
PopTable('header', 'Backup');
?>
<form id="dataForm" name="dataForm" method="post" action="for_export.php?modname=Tools/Backup.php&action=backup&_openSIS_PDF=true" target=_blank>
<table border=0 width=450px><tr><td>

<?php echo "<font color=red><strong>Note:</strong></font> This backup utility will create a backup of the database along with the database structure. You will be able to use this backup file to restore the database. However, in order to restore, you  will need to have access to MySQL administration application like phpMyAdmin and the root user id and password to MySQL." ?>
            <br><br>       <center>
<input type="submit" name="action"  value="Backup" class=btn_medium>&nbsp;&nbsp;
<?php
#<input type="submit" name="cancel"  value="Cancel" class=btn_medium></center>
    $modname = 'Tools/Backup.php';
echo '<a href=Modules.php?modname='.$modname.'&modfunc=cancel STYLE="TEXT-DECORATION: NONE"> <INPUT type=button class=btn_medium name=Cancel value=Cancel></a>';
?>
</td></tr></table>
</form>

<?php
PopTable('footer');

}
?>