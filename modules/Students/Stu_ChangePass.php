<?php
include('../../Redirect_modules.php');
DrawBC("School Setup >> ".ProgramTitle());
if(($_REQUEST['action'] == 'update') && ($_REQUEST['button']=='Save') && User('PROFILE')=='student')
{
	$stu_PASS=DBGet(DBQuery("SELECT PASSWORD FROM STUDENTS WHERE STUDENT_ID='".UserStudentId()."'"));
	$pass_old=$_REQUEST['old'];
	if($pass_old=="")
	 {
	   $error[] = "Please Type The Password";
	   echo ErrorMessage($error,'Error');
	 }
	 else
	 {
	  $pass_old = str_replace("\'","''",md5($_REQUEST['old']));
	  $pass_new = str_replace("\'","''",md5($_REQUEST['new']));
	  $pass_retype = str_replace("\'","''",md5($_REQUEST['retype']));
	  if($stu_PASS[1]['PASSWORD']==$pass_old)
	   {
	 	if($pass_new==$pass_retype)
		 {
	 	  $sql="UPDATE STUDENTS SET PASSWORD='".$pass_new."' WHERE STUDENT_ID='".UserStudentId()."'";
		  DBQuery($sql);
		  $note[] = "Password Sucessfully Changed";
	 	    echo ErrorMessage($note,'note');
		 }
		else
		 {
		 	$error[] = "Please Retype Password";
	 	    echo ErrorMessage($error,'Error');
		 }
	 }
	  else
	   {
	 	$error[] = "Password Does'nt Exist";
	 	echo ErrorMessage($error,'Error');
	 }
	 }
	
}
/*
echo "<FORM name=change_password id=change_password action=Modules.php?modname=$_REQUEST[modname]&action=update method=POST>";
echo "<span id='error' name='error'></span>";
echo "<table width=70%><tr><td><fieldset style='border:1px solid #ccc'><legend><b>Change Password </b></legend><table border=0 cellpadding=4 align=center >";
echo "<tr><td align='right'><strong>Old Password :</strong> </td><td><INPUT type=password class=cell_floating name=old></td></tr>";
echo "<tr><td align='right'><strong>New Password :</strong> </td><td><INPUT type=password class=cell_floating name=new></td></tr>";
echo "<tr><td align='right'><strong>Retype Password :</strong> </td><td><INPUT type=password class=cell_floating name=retype></td></tr>";
echo "</table>";
DrawHeader('','',"<INPUT TYPE=SUBMIT name=button id=button class=btn_medium VALUE='Save' onclick='return change_pass();'></CENTER>");
echo "</FORM>";
*/

echo "<span id='error' name='error'></span>";

PopTable('header','Change Password');
echo "<FORM name=change_password id=change_password action=Modules.php?modname=$_REQUEST[modname]&action=update method=POST>";
echo "<table border=0 width=350px><tr><td><table border=0 cellpadding=4 align=center >";
echo "<tr><td align='right'><strong>Old Password :</strong> </td><td><INPUT type=password class=cell_floating name=old AUTOCOMPLETE = “off”></td></tr>";
echo "<tr><td align='right'><strong>New Password :</strong> </td><td><INPUT type=password class=cell_floating name=new AUTOCOMPLETE = “off”></td></tr>";
echo "<tr><td align='right'><strong>Retype Password :</strong> </td><td><INPUT type=password class=cell_floating name=retype AUTOCOMPLETE = “off”></td></tr>";
echo "</table></td></tr></table>";
DrawHeader('','',"<INPUT TYPE=SUBMIT name=button id=button class=btn_medium VALUE='Save' onclick='return change_pass();'></CENTER>");
echo "</FORM>";
PopTable('footer');

?>