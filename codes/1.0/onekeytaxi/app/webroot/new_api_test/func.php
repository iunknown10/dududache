<?php
function getFileContent($fileName){
	if(file_exists($fileName)){
		return file_get_contents($fileName);
	}
}
function writeFileContent($fileName,$content){
	return file_put_contents($fileName,$content);
}
function clearFile($fileName){
	if(file_exists($fileName)){
		return file_put_contents($fileName,'');
	}
}