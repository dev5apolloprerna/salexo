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
                <th rowspan="" style="width:100%;border:none;">
                    <img style="width: 200px;" src="<?php echo $pic; ?>" alt="">
                </th>
                
                <!--<th style="width:35%;">{{ $popupQuotation->strCompanyName }}</th>-->
                <!--<th style="width:35%;"></th>-->
            </tr>
            <?php
            $fullAddress = trim($popupQuotation->strAddressOne . ' ' . $popupQuotation->strAddressTwo . ' ' . $popupQuotation->strAddressThree);
            ?>
            
            <!--if($popupQuotation->strAddressOne){ ?>-->
            <!--<tr>-->
            <!--    <td>{{ $popupQuotation->strAddressOne }}</td>-->
            <!--</tr>-->
            <!--<?php // } ?>    -->
            <!--<?php if($popupQuotation->strAddressTwo){ ?>-->
            <!--<tr>-->
            <!--    <td>{{ $popupQuotation->strAddressTwo }}</td>-->
            <!--</tr>-->
            <!--<?php } ?>    -->
            <!--<?php if($popupQuotation->strAddressThree){ ?>-->
            <!--<tr>-->
            <!--    <td>{{ $popupQuotation->strAddressThree }}</td>-->
            <!--</tr>-->
            <!--<?php } ?>-->
            
            <?php if(!empty($fullAddress)){ ?>
            <tr>
                <td><?php echo $fullAddress; ?></td>
            </tr>
            <?php } ?>
            <?php if($popupQuotation->companyEmail){ ?>
                <tr>
                    <td>Email: {{ $popupQuotation->companyEmail }}</td>
                </tr>
            <?php } ?>    
            <?php if($popupQuotation->companyMobile){ ?>
            <tr>
                <td>Contact No - {{ $popupQuotation->companyMobile }} </td>
            </tr>
            <?php } ?>    

    </table>

    <table style="width:100%;" border="">
        <tbody>
            {{-- @foreach ($Quotation as $quotation) --}}
            <tr>
                <td style="width:50%; text-align: start;">GSTIN:{{ $popupQuotation->strGST }}</td>
                <td style="width:50%;text-align:end;">PAN:{{ $popupQuotation->strPanNo }}</td>
            </tr>
            {{-- @endforeach --}}
        </tbody>
    </table>

    <table style="width:100%;" border="">
        <tbody>
            <tr>
                <th style="width:100%; text-align: center;">Sales Quotation</th>
            </tr>
        </tbody>
    </table>

    <table style="width:100%;" border="">
        <tbody>
            {{-- @foreach ($Quotation as $quotation) --}}
            <tr>
                <td rowspan="5" style="width:50%; text-align: start;">Customer :
                    <?php echo $popupQuotation->strPartyName . ',<br />';
                    ?>
                    <?php if ($popupQuotation->address1 == '') {
                        echo '';
                    } else {
                        echo $popupQuotation->address1 . '<br />';
                    }
                    ?>
                    <?php if ($popupQuotation->address2 == '') {
                        echo '';
                    } else {
                        echo $popupQuotation->address2 . '<br />';
                    }
                    ?>
                    <?php if ($popupQuotation->address3 == '') {
                        echo '';
                    } else {
                        echo $popupQuotation->address3 . '<br />';
                    }
                    ?>
                    <?php if ($popupQuotation->iMobile == '') {
                        echo '';
                    } else {
                        echo "Mobile:- " . $popupQuotation->iMobile . ',<br />';
                    }
                    ?>
                    <?php if ($popupQuotation->strEmail == '') {
                        echo '';
                    } else {
                        echo "Email:- ". $popupQuotation->strEmail;
                    }
                    ?>
                </td>
                <td style="width:50%; text-align: start;">SQ No: {{ $popupQuotation->iQuotationNo }}
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SQ
                    Date: {{ date('d-m-Y', strtotime($popupQuotation->entryDate)) }}
                </td>
            </tr>
            <tr>
                <!-- <td style="width:50%; text-align: start;">Sales Quotation</td> -->
                <td style="width:50%; text-align: start;">Quote Validity: {{ $popupQuotation->quotationValidity }}</td>
            </tr>
            <tr>
                <!-- <td style="width:50%; text-align: start;">Sales Quotation</td> -->
                <td style="width:50%; text-align: start;">Mode of Despatch: {{ $popupQuotation->modeOfDespatch }}</td>
            </tr>
            <tr>
                <!-- <td style="width:50%; text-align: start;">Mode of Despatch: Road</td> -->
                <td style="width:50%; text-align: start;">Delivery term: {{ $popupQuotation->deliveryTerm }}</td>
            </tr>
            <tr>
                <!-- <td style="width:50%; text-align: start;">Mode of Despatch: Road</td> -->
                <td style="width:50%; text-align: start;">Payment Term : {{ $popupQuotation->paymentTerms }}</td>
            </tr>
            {{-- @endforeach --}}
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

                    <td style="text-align: center">{{ $detail->uom }}</td>
                    <td style="text-align: center">{{ $detail->quantity }}</td>
                    <td style="text-align: center">{{ $detail->rate }}</td>
                    <!--<td style="text-align: center">{{ $detail->amount }}</td>-->
                    <!--<td style="text-align: center">{{ $detail->discount }}</td>-->
                    <td style="text-align: center">{{ $detail->netAmount }}</td>
                    {{-- <td>73920.00</td> --}}
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
    //     ->where(['quotationdetails.isDelete' => 0, 'quotationdetails.iStatus' => 1, 'quotationdetails.quotationID' => $popupQuotation->quotationId])
    //     ->first();
    ?>

    <table style="width:100%;" border="1">
        <tbody>
            <tr>
                <td style="width:80%;text-align: right;">Sub Total</td>
                <td style="width:20%;text-align: right;">
                  <b style="float: left;">  Rs. </b><?php if($TotalNetAmount == "") {
                    echo "";
                } else{ ?>
                    <b> {{ $TotalNetAmount }} </b>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td style="width:80%;text-align: right;">
                    @if($popupQuotation->iGstType == 2) 
                        IGST
                    @else
                        GST
                    @endif
                    Amount</td>
                <td style="width:20%;text-align: right;">
                  <b style="float: left;">  Rs. </b><b> {{ $iGstAmount }} </b>
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
    <table style="width:100%;" border="1" >
        <tbody>
            <tr>
                <td style="width:100%;text-align: start; border:none;">Terms & Conditions:</td>
            </tr>
            
            <!--@foreach ($TermCondition as $termcondition)-->
            <!--<tr>    -->
                
            <!--        <td style="width:100%;text-align: start;border:none;">-->
            <!--            {{ $termcondition->description }}</td>-->
            <!--</tr>    -->
            <!--@endforeach-->
            
            
                <tr>
                    <td style="width:100%;text-align: start;border:none;">
                        <!--{{ str_replace('  ', '<br />', $popupQuotation->strTermsCondition)  }}-->
                        {!! $popupQuotation->strTermsCondition !!}
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
    <br>
   <!-- <table  style="width:100%;" border="0">
        <tbody>
            <tr>
                <td>Bank Name :</td>
                <td> {{ $popupQuotation->strBankName }}</td>
                <td style="text-align: right;"><strong> For, {{ $popupQuotation->strCompanyName }}</strong></td>
            </tr>
             <tr>
                <td>Account No : </td> <td>{{ $popupQuotation->strAccountNo }}</td>
            </tr>
             <tr>
                <td>IFSC Code : </td><td> {{ $popupQuotation->strIfscCode }}</td>
                
            </tr>
            <tr>
                <td>Branch : </td> <td> {{ $popupQuotation->strBranch }}</td>
                <td  style="text-align: right;">Authorized Signatory</td>
            </tr>
        </tbody>
    </table> -->
</body>

</html>
