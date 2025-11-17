
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        table {
            border-collapse: collapse;
        }
    </style>
</head>

<body>

    <table style="width:100%;" border="0">
        <tbody style="text-align:center;">

            <tr>
                <?php
                // $root = $_SERVER['DOCUMENT_ROOT'];
                // $destinationpath = $root . 'CompanyLogo/';
                ?>
                <?php //dd($Quotation->strLogo);
                ?>
                <th rowspan="" style="width:100%;border:none;">
                    
                    <img style="width: 200px;" src="<?php echo $pic; ?>" alt="">
                </th>
                <!--<th style="width:35%;">{{ $Quotation->strCompanyName }}</th>-->
                <!--<th style="width:35%;"></th>-->
            </tr>
            <?php
            $fullAddress = trim($Quotation->strAddressOne . ' ' . $Quotation->strAddressTwo . ' ' . $Quotation->strAddressThree);
            ?>
            
            <!--<?php if($Quotation->strAddressOne){ ?>-->
            <!--<tr>-->
            <!--    <td>{{ $Quotation->strAddressOne }}</td>-->
            <!--</tr>-->
            <!--<?php } ?>    -->
            <!--<?php if($Quotation->strAddressTwo){ ?>-->
            <!--<tr>-->
            <!--    <td>{{ $Quotation->strAddressTwo }}</td>-->
            <!--</tr>-->
            <!--<?php } ?>    -->
            <!--<?php if($Quotation->strAddressThree){ ?>-->
            <!--<tr>-->
            <!--    <td>{{ $Quotation->strAddressThree }}</td>-->
            <!--</tr>-->
            <!--<?php } ?>    -->
            
            <?php if(!empty($fullAddress)){ ?>
            <tr>
                <td><?php echo $fullAddress; ?></td>
            </tr>
            <?php } ?>
            <?php if($Quotation->companyEmail){ ?>
                <tr>
                    <td>Email: {{ $Quotation->companyEmail }}</td>
                </tr>
            <?php } ?>    
            <?php if($Quotation->companyMobile){ ?>
            <tr>
                <td>Contact No - {{ $Quotation->companyMobile }} </td>
            </tr>
            <?php } ?> 

    </table>

    <table style="width:100%;" border="">
        <tbody>
            <tr>
                <td style="width:50%; text-align: start;">GSTIN:&nbsp;{{ $Quotation->strGST }}</td>
                <td style="width:50%;text-align:end;">PAN:&nbsp;{{ $Quotation->strPanNo }}</td>
            </tr>
        </tbody>
    </table>

    <table style="width:100%;" border="">
        <tbody>
            <tr style="border-top: none;
            border-bottom: none;">
                <th style="width:100%; text-align: center;">Sales Quotation</th>
            </tr>
        </tbody>
    </table>

    <table style="width:100%;" border="">
        <tbody>
            <tr>
                <td rowspan="5" style="width:50%; text-align: start;">Customer :
                    <?php echo $Quotation->strPartyName . ',<br />';
                    ?>
                    <?php if ($Quotation->address1 == '') {
                        echo '';
                    } else {
                        echo $Quotation->address1 . '<br />';
                    }
                    ?>
                    <?php if ($Quotation->address2 == '') {
                        echo '';
                    } else {
                        echo $Quotation->address2 . '<br />';
                    }
                    ?>
                    <?php if ($Quotation->address3 == '') {
                        echo '';
                    } else {
                        echo $Quotation->address3 . '<br />';
                    }
                    ?>
                    <?php if ($Quotation->iMobile == '') {
                        echo '';
                    } else {
                        echo "Mobile:- " . $Quotation->iMobile . ',<br />';
                    }
                    ?>
                    <?php if ($Quotation->strEmail == '') {
                        echo '';
                    } else {
                        echo "Email:- ". $Quotation->strEmail;
                    }
                    ?>
                </td>
                <td style="width:50%; text-align: start;">SQ No: &nbsp; {{ $Quotation->iQuotationNo }}
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SQ Date:{{ date('d-m-Y', strtotime($Quotation->entryDate)) }}
                </td>
            </tr>
            <tr>
                <td style="width:50%; text-align: start;">Quote Validity: {{ $Quotation->quotationValidity }}</td>
            </tr>
            <tr>
                <td style="width:50%; text-align: start;">Mode of Dispatch: {{ $Quotation->modeOfDespatch }}</td>
            </tr>
            <tr>
                <td style="width:50%; text-align: start;">Delivery Terms: {{ $Quotation->deliveryTerm }}</td>
            </tr>
            <tr>
                <td style="width:50%; text-align: start;">Payment Terms : {{ $Quotation->paymentTerms }}</td>
            </tr>
        </tbody>
    </table>

    <table style="width:100%;" border="">
        <tbody>
            <tr>
                <th>SrNo.</th>
                <th>Product Description</th>
                <th>UOM</th>
                <th>Quantity</th>
                <th>Unit Rate</th>
                <!--<th>Amount</th>-->
                <!--<th>Discount.</th>-->
                <th>Net Amount</th>
            </tr>
            <?php $i = 1; 
                $iGstAmount = 0;
                $TotalNetAmount = 0;
            ?>
            @foreach ($QuotationDetail as $detail)
                <tr>
                    <td style="text-align: center">{{ $i }}</td>
                    <td style="text-align: left; white-space: pre-line;">{!! $detail->description !!}</td>
                    <!--<td style="text-align: left">{{ $detail->description }}</td>-->
                    <td style="text-align: center">{{ $detail->uom }}</td>
                    <td style="text-align: center">{{ $detail->quantity }}</td>
                    <td style="text-align: center">{{ $detail->rate }}</td>
                    <!--<td style="text-align: center">{{ $detail->amount }}</td>-->
                    <!--<td style="text-align: center">{{ $detail->discount }}</td>-->
                    <td style="text-align: center">{{ $detail->netAmount }}</td>
                </tr>
                <?php 
                    $GstAmt = ($detail->netAmount * $detail->iGstPercentage) / 100;
                    $iGstAmount += $GstAmt;
                    $TotalNetAmount += $detail->netAmount;
                ?>
                <?php $i++; ?>
            @endforeach

        </tbody>
    </table>

    <?php
    // $Total = DB::table('quotationdetails')
    //     ->select(DB::raw('sum(quotationdetails.netAmount) as amt'))
    //     ->where(['quotationdetails.isDelete' => 0, 'quotationdetails.iStatus' => 1, 'quotationdetails.quotationID' => $Quotation->quotationId])
    //     ->first();
    //dd($Total);
    ?>

    <table style="width:100%;" border="1">
        <tbody>
            <tr>
                <td style="width:80%;text-align: right;">Sub Total</td>
                <td style="width:20%;text-align: right;">
                    <b style="float: left;"> Rs. </b>
                    <b> {{ $TotalNetAmount }} </b>
                </td>
            </tr>
            <tr>
                <td style="width:80%;text-align: right;">
                    @if($Quotation->iGstType == 2) 
                        IGST
                    @else
                        GST
                    @endif
                    Amount</td>
                <td style="width:20%;text-align: right;">
                  <b style="float: left;"> Rs. </b> <b> {{ $iGstAmount }} </b>
                </td>
            </tr>
            <tr>
                <td style="width:80%;text-align: right;">Total Amount</td>
                <td style="width:20%;text-align: right;">
                    <b style="float: left;">  Rs. </b>
                    <b> {{ $TotalNetAmount + $iGstAmount }} </b>
                </td>
            </tr>
        </tbody>
    </table>
    <table style="width:100%;" border="1">
        <tbody>
            <tr>
                <td style="width:100%;text-align: start;border:none;">Terms & Conditions:</td>
            </tr>
            <!--@foreach ($TermCondition as $termcondition)-->
                
            <!--    <tr>-->
            <!--        <td style="width:100%;text-align: start;border:none;">-->
            <!--            {{ $termcondition->description }}</td>-->
            <!--    </tr>-->
            <!--@endforeach-->
            <tr>
                <td style="width:100%;text-align: start;border:none;">
                    {!! $Quotation->strTermsCondition !!}
                </td>
            </tr>
            <tr>
                <td style="width:100%;text-align: start;">Comments:</td>
            </tr>
            <tr>
                <td style="width:100%;text-align: start;">Remarks:</td>
            </tr>
        </tbody>
    </table>
 

    <!--<p style="float:right;"> <strong> For, {{ $Quotation->strCompanyName }}</strong></p><br><br><br>-->
    <!--<p style="text-align: right;">Authorized Signatory</p>-->
    
    
        <br>
       <table  style="width:100%;" border="0">
        <tbody>
            <tr>
                <td>Bank Name : {{ $Quotation->strBankName }}</td>
                <td style="text-align: right;"><strong> For, {{ $Quotation->strCompanyName }}</strong></td>
            </tr>
             <tr>
                <td>Account No : {{ $Quotation->strAccountNo }}</td>
            </tr>
             <tr>
                <td>IFSC Code : {{ $Quotation->strIfscCode }}</td>
            </tr>
            <tr>
                <td>Branch : {{ $Quotation->strBranch }}</td>
                <td  style="text-align: right;">Authorized Signatory</td>
            </tr>
        </tbody>
        
    </table>

</body>

</html>
