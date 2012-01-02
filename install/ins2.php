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
$_SESSION['db'] = clean_param($_REQUEST["db"],PARAM_DATA);
$purgedb = clean_param($_REQUEST["purgedb"],PARAM_ALPHA); // Added variable to check for removing existing data.

$dbconn = mysql_connect($_SESSION['host'],$_SESSION['username'],$_SESSION['password']);
$sql="select count(*) from information_schema.SCHEMATA where schema_name = '".$_SESSION['db']."'" ;
$res = mysql_query($sql);

while ($row = mysql_fetch_row($res)) {
    $exists =  $row[0];
}

if($exists!=0)
{
    if (empty($purgedb))
    {
        header('Location: step2.php?err=Database Exists. Enter a different name');
        exit;
    }
    else
    {
        $result = mysql_select_db($_SESSION['db']);
        if(!$result)
        {
            echo "<h2>" . mysql_error() . "</h2>\n";
            exit;
        }

        // Get tables, loop thru the tables and drop each table.
        $num_tables = mysql_list_tables($_SESSION['db']);
        while($row = mysql_fetch_row($num_tables))
        {
            // Drop all tables.
            $delete_table = mysql_query("DROP TABLE IF EXISTS $row[0]");

            // Separate Drop for VIEWs is needed due to mysql syntax for views.
            $delete_view = mysql_query("DROP VIEW IF EXISTS $row[0]");

            // There is currently no way to drop functions without knowing
            // the functions name and doing a DROP FUNCTION name
            // so we have to modify the mysql file to remove functions first
            // before trying to add them or else an error will occur.

            if(!$delete_table)
            {
                echo 'Unable to remove ' . $row[0] . '<br>';
            }
        }
        // Free result set to clear memory
        mysql_free_result($num_tables);

		/*
        //This begins the add portion
        $myFile = "opensis-4.5-schema-mysql.sql";
        executeSQL($myFile);
        $myFile = "opensis-4.5-procs-mysql.sql";
        executeSQL($myFile);
		*/
		
        //This begins the add portion
        #$myFile = "opensis-4.7-schema-mysql.sql";
        $myFile = "opensis-4.9-schema-mysql.sql";
        executeSQL($myFile);
        #$myFile = "opensis-4.7-procs-mysql.sql";
        $myFile = "opensis-4.9-procs-mysql.sql";
        executeSQL($myFile);
        

        mysql_close($dbconn);

        header('Location: step3.php');
    }
}
else
{
    $sql="CREATE DATABASE `".$_SESSION['db']."` CHARACTER SET=utf8;";
    $result = mysql_query($sql);
    if(!$result)
    {
        echo "<h2>" . mysql_error() . "</h2>\n";
        exit;
    }
    $result = mysql_select_db($_SESSION['db']);
    if(!$result)
    {
        echo "<h2>" . mysql_error() . "</h2>\n";
        exit;
    }
    
	/*
	$myFile = "opensis-4.5-schema-mysql.sql";
    executeSQL($myFile);
    $myFile = "opensis-4.5-procs-mysql.sql";
    executeSQL($myFile);
	*/
    #$myFile = "opensis-4.7-schema-mysql.sql";
    $myFile = "opensis-4.9-schema-mysql.sql";
    executeSQL($myFile);
    #$myFile = "opensis-4.7-procs-mysql.sql";
    $myFile = "opensis-4.9-procs-mysql.sql";
    executeSQL($myFile);

    mysql_close($dbconn);

    // edited installation
         # include('reset_auto_increment.php');
// edited installation
    header('Location: step3.php');
}

function executeSQL($myFile)
{	
    $sql = file_get_contents($myFile);
    $sqllines = split("\n",$sql);
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
                    $result = mysql_query($cmd) or die(mysql_error());
                    $cmd = '';
                }
            }
        }
    }
}
?>
