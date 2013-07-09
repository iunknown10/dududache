<?php
echo 'pmc_taxi_confirm<br>';
$mc = new MemCached_Wrapper();
$mc->set('aaa123','sdfsdfds',5);
var_dump($mc->get('aaa123'));
?>