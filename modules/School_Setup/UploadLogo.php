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
include("modules/Students/upload.class.php");

PopTable ('header','Upload School Logo');

$SchoolLogoPath ='assets/SchoolLogo';
    if(!file_exists($SchoolLogoPath))
	{
		mkdir($SchoolLogoPath);
	}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='edit')
{
if($SchoolLogoPath && (($file = @fopen($picture_path=$_SESSION['logo_path'],'r')) ) )
	{
            echo '<div align=center><IMG SRC="'.$picture_path.'" height=100 width=100 class=pic></div><div class=break></div>';
	}
	unset($_REQUEST['modfunc']);
}
if(UserSchool())
{
 if(clean_param($_REQUEST['action'],PARAM_ALPHAMOD)=='upload' && $_FILES['file']['name'])
{
	$upload= new upload();
        $upload->name=$_FILES["file"]["name"];
        $target_path=$SchoolLogoPath.'/'.substr($_FILES["file"]["name"],0,strrpos($_FILES["file"]["name"],".")).UserSchool().'.'.$upload->setFileExtension();
	$destination_path = $SchoolLogoPath;	    #$target_path=$StudentPicturesPath.UserSyear().'/'.UserStudentID().'.JPG';
	$upload->target_path=$target_path;
//	$upload->deleteOldImage();
	$upload->destination_path=$destination_path;
//	$upload->name=$_FILES["file"]["name"];
//	$upload->setFileExtension();
//	$upload->fileExtension;
	$upload->validateImage();
	if($upload->wrongFormat==1){
	$_FILES["file"]["error"]=1;
	}

	if ($_FILES["file"]["error"] > 0)
    {
    $msg = "<font color=red><b>Cannot upload file. Only jpeg, jpg, png, gif files are allowed.</b></font>";
    echo '
	'.$msg.'
	<form enctype="multipart/form-data" action="Modules.php?modname=School_Setup/UploadLogo.php&action=upload" method="POST">';
echo '<div align=center>Select Logo: <input name="file" type="file" /><br /><br>
<input type="submit" value="Upload" name="Submit" class=btn_medium />&nbsp;<input type=button class=btn_medium value=Cancel onclick=\'load_link("Modules.php?modname=School_Setup/Schools.php");\'></div>
</form>';
PopTable ('footer');
    }
  	else
    {
	  move_uploaded_file($_FILES["file"]["tmp_name"], $upload->target_path);
          if($_SESSION['logo_path'])
          {
              $upload_edit_sql=DBQuery("UPDATE PROGRAM_CONFIG SET VALUE='$upload->target_path' WHERE SCHOOL_ID='".UserSchool()."' AND PROGRAM='SchoolLogo' AND TITLE='PATH'");
              unlink($_SESSION['logo_path']);
              unset($_SESSION['logo_path']);
          }
          else
          {
              $upload_sql=DBQuery("INSERT INTO PROGRAM_CONFIG (SCHOOL_ID,PROGRAM ,TITLE,VALUE) VALUES('".UserSchool()."','SchoolLogo','PATH','$upload->target_path')");
          }
	  @fopen($upload->target_path,'r');
	echo '<div align=center><IMG SRC="'.$upload->target_path.'" height=100 width=100 class=pic></div><div class=break></div>';
fclose($upload->target_path);
     //echo "<b>Copied file to " .$upload->destination_path."</b><p>";
      $filename =  $upload->target_path;
 echo '<div align=center><input type=button class=btn_medium value=Done onclick=\'load_link("Modules.php?modname=School_Setup/Schools.php");\'></div>';
	PopTable ('footer');
    }
}
else
{
echo '
'.$msg.'
<form enctype="multipart/form-data" action="Modules.php?modname=School_Setup/UploadLogo.php&action=upload" method="POST">';
echo '<div align=center>Select Logo: <input name="file" type="file" /><br /><br>
<input type="submit" name="Submit"  value="Upload" class=btn_medium />&nbsp;<input type=button class=btn_medium value=Cancel onclick=\'load_link("Modules.php?modname=School_Setup/Schools.php");\'></div>
</form>';
PopTable ('footer');
}
}
else
{
	echo 'Please select a school first!';
	PopTable ('footer');
}
?>