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

/**
* /----------------------------------------------------------------------\
* | Digital Express, Inc.
* | Copyright (c) 2002 Richard Clark All rights reserved.
* | http://www.dvexinc.com
* | <richievc@hotmail.com>
* | 
* | AVERY 5160 Print Lables Moduel v 1.1.2 revised
* /----------------------------------------------------------------------\
*/
/************************************************** ************** */
/************************************************** ************** */
/************************************************** ************** */
include('Redirect_root.php'); 
include('Warehouse.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:w="urn:schemas-microsoft-com:office:word"
xmlns="http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252" />
<title>Avery 5160</title>
<!--[if gte mso 9]><xml>
<o:DocumentProperties>
<o:Author>Techmaster</o:Author>
<o:Template>Normal</o:Template>
<o:LastAuthor>Techmaster</o:LastAuthor>
<o:Revision>2</o:Revision>
<o:TotalTime>3</o:TotalTime>
<o:Created>2004-02-09T19:49:00Z</o:Created>
<o:LastSaved>2004-02-09T19:49:00Z</o:LastSaved>
<o:Pages>1</o:Pages>
<o:Words>36</o:Words>
<o:Characters>206</o:Characters>
<o:Company>Digital Express</o:Company>
<o:Lines>1</o:Lines>
<o:Paragraphs>1</o:Paragraphs>
<o:CharactersWithSpaces>241</o:CharactersWithSpaces>
<o:Version>10.3501</o:Version>
</o:DocumentProperties>
</xml><![endif]--><!--[if gte mso 9]><xml>
<w:WordDocument>
<w:SpellingState>Clean</w:SpellingState>
<w:GrammarState>Clean</w:GrammarState>
<w:Compatibility>
<w:BreakWrappedTables/>
<w:SnapToGridInCell/>
<w:WrapTextWithPunct/>
<w:UseAsianBreakRules/>
</w:Compatibility>
<w:BrowserLevel>MicrosoftInternetExplorer4</w:BrowserLevel>
</w:WordDocument>
</xml><![endif]-->
<style>
<!--
/* Style Definitions */
p.MsoNormal, li.MsoNormal, div.MsoNormal
{mso-style-parent:"";
margin-bottom:.0001pt;
mso-pagination:widow-orphan;
font-size:12.0pt;
font-family:"Times New Roman";
mso-fareast-font-family:"Times New Roman"; margin-left:0in; margin-right:0in; margin-top:0in}
span.SpellE
{mso-style-name:"";
mso-spl-e:yes}
@page Section1
{size:8.5in 11.0in;
margin:.5in 13.6pt 0in 13.6pt;
mso-header-margin:.5in;
mso-footer-margin:.5in;
mso-paper-source:4;}
div.Section1
{page:Section1;}
-->
</style>
<!--[if gte mso 10]>
<style>
/* Style Definitions */
table.MsoNormalTable
{mso-style-name:"Table Normal";
mso-tstyle-rowband-size:0;
mso-tstyle-colband-size:0;
mso-style-noshow:yes;
mso-style-parent:"";
mso-padding-alt:0in 5.4pt 0in 5.4pt;
mso-para-margin:0in;
mso-para-margin-bottom:.0001pt;
mso-pagination:widow-orphan;
font-size:10.0pt;
font-family:"Times New Roman"}
</style>
<![endif]-->

</head>
<body lang=EN-US style='tab-interval:.5in'>
<?php
// CONN TO DB 

$res = DBGet(DBQuery("SELECT * FROM address limit 20")); 

if (count($res) < 1){ 
echo "<CENTER><B>No Results</B></CENTER>";
} elseif (count($res) > 0) { 
$cols = 0; 
$rows = 0; 
$max_cols=3; 
$max_rows=10;
// Set the num of rows and cols 

foreach ($res as $data){ 

# if ($rows == 0){ // echo out table info 
#
# } 
if ($cols < 1){ // output the tr 
echo "<div class=Section1>
<table class=MsoNormalTable border=0 cellspacing=2 cellpadding=2 
style='border-collapse:collapse;padding-top-alt:0in;padding-bottom-alt: 0in'>";
echo "<tr style='yfti-irow:0;page-break-inside:avoid;height:1.0in'>"; 
} 
echo "<td width=189 style='width:189.0pt;padding:0in .75pt 0in .75pt;height:1.0in'> 
<p class=MsoNormal align=center style='margin-top:0in; margin-right:5.3pt; 
margin-bottom:0in; margin-left:5.3pt; margin-bottom:.0001pt;text-align:center'> 
<span class=SpellE>"; 
echo "<FONT SIZE=1 FACE=\"Arial\">";
echo "$data[f_name] $data[l_name]<BR>
$data[ADDRESS]<BR>
$data[CITY] $data[STATE]. $data[ZIPCODE]</FONT>"; 
echo "</span></p></td>"; 
// before, you were outputting the end of the td before the beginning 

$cols++; 

if ($cols == $max_cols){ // reset cols, make new row, output trs 
echo '</tr>'; 
$rows++; 
$cols=0; 
} 

if ($rows == $max_rows){ // reset rows, end table,put in line break--I substituted <hr> 
echo "</table></div><BR><BR>"; 
$rows=0; 
} 
}//end while loop 
//now the tricky part, completing the last table 

if ($cols == 0 && $rows == 0){ //done 

} else { //end cols, then end table 
while ($cols !=0 && $cols < $max_cols){//don't do if the cols have been reset 
echo "<td width=252 style='width:189.0pt;padding:0in .75pt 0in .75pt;height:1.0in'> 
<p class=Normal align=center style='margin-top:0in; margin-right:5.3pt; 
margin-bottom:0in; margin-left:5.3pt; margin-bottom:.0001pt;text-align:center'> 
<span class=SpellE> </td>"; 
$cols++; 
} 
if ($cols == $max_cols) echo '</tr>';//to end the row if needed 
echo '</table></div>'; //to end the table 
}//end else 
}//end if there are more than 0 rows 


?>
</body></html>
<?php

/************************************************** ************** */
/************************************************** ************** */
/************************************************** ************** */
?>