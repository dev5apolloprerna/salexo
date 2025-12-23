<?php
ob_start();
$filename = 'LeadMaster_List_' . date('d-m-Y_H-i-s') . '.xls';
ob_end_clean();

// Define headers
// echo "Sr No\tCompany Name\tGST\tCustomer Name\tEmail\tMobile\tAlternative Mobile\tAddresstService\tLead Source\tAmount\n";

// Define headers
echo "Sr No\tCompany Name\tGST\tCustomer Name\tEmail\tMobile\tAlternative Mobile\tAddress\tService\tLead Source";

if ($LeadType == "deal_done") {
    echo "\tDeal Done Date";
}

if ($LeadType == "deal_cancel") {
    echo "\tLead Cancel Reason";
    echo "\tLead Cancel Date";
}

echo "\tAmount\n";


$i = 1;
foreach ($leads as $row) {
    echo $i . "\t"
        . ($row->company_name ?? '-') . "\t"
        . ($row->GST_No ?? '-') . "\t"
        . ($row->customer_name ?? '-') . "\t"
        . ($row->email ?? '-') . "\t"
        . ($row->mobile ?? '-') . "\t"
        . ($row->alternative_no ?? '-') . "\t"
        . ($row->address ?? '-') . "\t"
        . ($row->service_name ?? '-') . "\t"
        . ($row->leadSource->lead_source_name ?? '-');
        
        if ($LeadType == "deal_done") {
            echo "\t" . ($row->deal_done_at ?? '-');  // assuming column exists
        }
    
        if ($LeadType == "deal_cancel") {
            echo "\t" . ($row->cancel_reason_name ?? '-');
            echo "\t" . ($row->deal_cancel_at ?? '-');
        }
    
        echo "\t" . ($row->amount ?? '0') . "\n";
        $i++;
}

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
exit();
