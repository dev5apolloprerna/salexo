<?php
// echo "<pre>";
// print_r($renewalreport);
// exit;
$filename = 'Candidate_list' . date('d-m-Y H:s:i') . '.xls';

header("Content-Type: application/vnd.ms-excel");
header("Content-disposition: attachment; filename=" . $filename);

ob_end_clean();

echo
"Sr.No"
. "\t" ."Name"
. "\t" ."Gender"
. "\t" ."DOB"
. "\t" ."Email"
. "\t" ."Mobile"
. "\t" ."State"
. "\t" ."City"
. "\t" ."Company"
. "\t" ."Designation"
. "\t" ."Experience"
. "\t" ."Current"
. "\t" ."Education"
. "\t" ."Institute"
. "\t" ."Funcation"
. "\t" ."Industry"                                          
 . "\n";


$i = 1;
$total=0;
foreach ($data as $key => $row)  
{

echo
    $i
  . "\t" . $row->firstName .$row->lastName 
  . "\t" . $row->Gender
  . "\t" . date('d-m-Y', strtotime($row->DOB))
  . "\t" .$row->Email
  . "\t" .$row->Mobile
  . "\t" .$row->state
  . "\t" .$row->city
  . "\t" .$row->Company
  . "\t" .$row->Designation
  . "\t" .$row->experience
  . "\t" .$row->current_CTC
  . "\t" .$row->education
  . "\t" .$row->Institute
  . "\t" .$row->functions
  . "\t" .$row->industry
 . "\n";

    $i++;
}