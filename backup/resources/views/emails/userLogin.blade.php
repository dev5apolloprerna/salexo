<?php 



$html = 'Dear '.$User['UserName'].',<br> Your login password is below </br>'; 
$html .="<h4>Password: ".$User['Password'].'</h4>';

echo $html;

?>