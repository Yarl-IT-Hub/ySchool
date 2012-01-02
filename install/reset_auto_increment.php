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
	$dbconn = mysql_connect($_SESSION['server'],$_SESSION['username'],$_SESSION['password']) or die();
	mysql_select_db("information_schema");
	/*$r = mysql_query(
	"select concat('select concat(''alter table ',t.table_name,' auto_increment='',max(',c.column_name,')+1) from ',t1.table_name)
  from TABLES t
	inner join TABLES t1 ON t1.table_name=REPLACE(t.table_name,'_seq','')
		inner join COLUMNS c ON c.table_name=t1.table_name AND c.column_key='PRI'
	where t.table_schema='".$_SESSION['db']."' and t.table_name like '%_seq' and t1.table_name<>'SCHEDULE'
  union
  select 'select concat(''alter table schedule_seq auto_increment='',max(id)+1) from SCHEDULE'
"); */
$r = mysql_query(
	"select concat('select concat(''alter table ',t.table_name,' auto_increment='',max(',c.column_name,')+1) from ',t1.table_name)
  from TABLES t
	inner join TABLES t1 ON t1.table_name=REPLACE(t.table_name,'_seq','')
		inner join COLUMNS c ON c.table_name=t1.table_name AND c.column_key='PRI'
	where t.table_schema='".$_SESSION['db']."' and t.table_name like '%_seq' and t1.table_name<>'SCHEDULE'
  union
  select 'select concat(''alter table SCHEDULE auto_increment='',max(id)+1) from SCHEDULE'
");

	if (!$r) {
		die(mysql_error());
	}
	$i=0;
	//echo "<h1>Getting table queries...</h1>\n";
	while($row = mysql_fetch_array($r)) {
		$s[$i++] = $row[0];
		//echo $row[0],"<br />\n";
	}
	mysql_free_result($r);

	//echo "<h1>Getting auto_increment values for $i queries...</h1>\n";
	mysql_select_db($_SESSION['db']) or die() ;
	$i1=0;
	for($x=0;$x<$i;$x++) {
		$r = mysql_query($s[$x]);
		//echo $s[$x],"<br />\n";
		if($row = mysql_fetch_array($r)) {
			$s1[$i1++] = $row[0];
		}
	}
	mysql_free_result($r);

	//echo "<h1>Updating auto_increment values...</h1>\n";
	for($x=0;$x<$i1;$x++) {
		//echo $s1[$x],"<br />\n";
		$r = mysql_query($s1[$x]);
		//echo "$r<br />\n";
	}
	mysql_free_result($r);
?>
