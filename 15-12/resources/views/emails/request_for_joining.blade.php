<?php

$State = \App\Models\State::find($data['state_id']);
$Plan = \App\Models\Plan::find($data['plan_id']);

$root = $_SERVER['DOCUMENT_ROOT'];
$file = file_get_contents($root . '/mailers/request_for_joining.html', 'r');

$file = str_replace('#company_name', $data['company_name'], $file);
$file = str_replace('#GST', $data['GST'] ?? '-', $file);
$file = str_replace('#contact_person_name', $data['contact_person_name'], $file);
$file = str_replace('#mobile', $data['mobile'], $file);
$file = str_replace('#email', $data['email'], $file);
$file = str_replace('#Address', $data['Address'], $file);
$file = str_replace('#state_id', $State->stateName, $file);
$file = str_replace('#city', $data['city'], $file);
$file = str_replace('#pincode', $data['pincode'], $file);
$file = str_replace('#plan_id', $Plan->plan_name, $file);
$file = str_replace('#plan_amount', $data['plan_amount'], $file);
$file = str_replace('#plan_days', $data['plan_days'], $file);
$file = str_replace('#subscription_start_date', date('d-m-Y', strtoTime($data['subscription_start_date'])), $file);
$file = str_replace('#subscription_end_date', date('d-m-Y', strtoTime($data['subscription_end_date'])), $file);

echo $file;

?>
