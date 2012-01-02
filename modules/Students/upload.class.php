<?php
class upload{

var $target_path;
var $destination_path;
var $name;
var $fileExtension;
var $allowExtension=array("jpg","jpeg","png","gif","bmp");
var $wrongFormat=0;
function deleteOldImage(){
if(file_exists($this->target_path))
	unlink($this->target_path);
}

function setFileExtension(){
$this->fileExtension=strtolower(substr($this->name,strrpos($this->name,".")+1));
}

function validateImage(){
if(!in_array($this->fileExtension, $this->allowExtension)){
$this->wrongFormat=1;
}
}
function get_file_extension($file_name) {
return end(explode('.',$file_name));
}
}
?>