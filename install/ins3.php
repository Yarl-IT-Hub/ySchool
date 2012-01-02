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
include '../functions/ParamLib.php';

session_start();
$dbconn = mysql_connect($_SESSION['host'],$_SESSION['username'],$_SESSION['password']);
$result = mysql_select_db($_SESSION['db']);
        if(!$result)
        {
            echo "<h2>" . mysql_error() . "</h2>\n";
            exit;
        }

// this part should be commented out
/* if($_POST["syear"])
{
	$_SESSION['syear'] = $_POST["syear"];
	$_SESSION['nextyear'] = $_POST["syear"]+1;
}
else
{
	$_SESSION['syear'] = '2009';
	$_SESSION['nextyear'] = '2010';
}
$dbconn = mysql_connect($_SESSION['host'],$_SESSION['username'],$_SESSION['password']);
mysql_select_db($_SESSION['db']);

$sql="update STAFF set username='".$_SESSION['admin_name']."', password='".$_SESSION['admin_pwd']."', profile_id=1 where username='admin' ";
$result = mysql_query($sql);
mysql_close($dbconn);
 * 
 */
// this part should be commented out



/* edited installation similar code
 *
 *    // edited installation
 * // dump data here
   // $myFile = "../modules/Food_Service/mfoodService.sql";
   // executeSQL($myFile);
    #$myFile = "../modules/Billing/install/sql/install.sql";
    #executeSQL($myFile);
    #$myFile = "../modules/Discipline/discipline.sql";
     #   executeSQL($myFile);
     // edited installation

    // edited installation
       # include('sql.php');
 *
 */


if(clean_param($_REQUEST['sname'],PARAM_NOTAGS) && clean_param($_REQUEST['sample_data'],PARAM_ALPHA)){
     $_SESSION['sname']=clean_param($_REQUEST['sname'],PARAM_NOTAGS);
      $beg_date=str_replace("/","-", $_REQUEST['beg_date']);
    $end_date=str_replace("/","-", $_REQUEST['end_date']);
     $school_beg_date=explode("-",$beg_date);
    $school_end_date=explode("-", $end_date);
    $_SESSION['user_school_beg_date']=$school_beg_date[2].'-'.$school_beg_date[0].
    '-'.$school_beg_date[1];
    $_SESSION['user_school_end_date']=$school_end_date[2].'-'.$school_end_date[0].
    '-'.$school_end_date[1];
  #  $_SESSION['syear']=$school_beg_date[2];
  #  $_SESSION['nextyear'] = $school_beg_date[2]+1;
    $_SESSION['syear'] = '2010';
    $_SESSION['nextyear'] = '2011';
    include('sql_for_client_school_and_sample_data.php');
	$_SESSION['school_installed']='both';

}
else if(clean_param($_REQUEST['sname'],PARAM_NOTAGS)){
	$_SESSION['sname']=clean_param($_REQUEST['sname'],PARAM_NOTAGS);
    $beg_date=str_replace("/","-", $_REQUEST['beg_date']);
    $end_date=str_replace("/","-", $_REQUEST['end_date']);
    $school_beg_date=explode("-",$beg_date);
    $school_end_date=explode("-",$end_date);
    $_SESSION['user_school_beg_date']=$school_beg_date[2].'-'.$school_beg_date[0].
    '-'.$school_beg_date[1];
    $_SESSION['user_school_end_date']=$school_end_date[2].'-'.$school_end_date[0].
    '-'.$school_end_date[1];
    $_SESSION['syear']=$school_beg_date[2];
    $_SESSION['nextyear'] = $school_beg_date[2]+1;
   
    include('sql_for_client_school.php');
  $_SESSION['school_installed']='user';

}
else if(clean_param($_REQUEST['sample_data'],PARAM_ALPHA)){
     include('sql_sample_data.php');
     $_SESSION['syear'] = '2010';
	$_SESSION['nextyear'] = '2011';
$_SESSION['school_installed']='sample';

}
echo '<script type="text/javascript">window.location = "step4.php"</script>';

// edited installation
# header('Location: step3.php');
?>
