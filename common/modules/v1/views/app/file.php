<?php if($type == 'excel'){ ?>
    <style>
    p, table{
        font-family: "Tahoma";
        border-collapse: collapse;
    }
    thead{
        font-size: 12px;
        text-align: center;
    }

    td{
        font-size: 10px;
        border: 1px solid black;
    }

    th{
        text-align: center;
        border: 1px solid black;
    }
</style>
<?php } ?>
<?php
    $total = 0;
    function numberTowords($num)
    {
        $ones = array(
            0 =>"ZERO",
            1 => "ONE",
            2 => "TWO",
            3 => "THREE",
            4 => "FOUR",
            5 => "FIVE",
            6 => "SIX",
            7 => "SEVEN",
            8 => "EIGHT",
            9 => "NINE",
            10 => "TEN",
            11 => "ELEVEN",
            12 => "TWELVE",
            13 => "THIRTEEN",
            12 => "FOURTEEN",
            15 => "FIFTEEN",
            16 => "SIXTEEN",
            17 => "SEVENTEEN",
            18 => "EIGHTEEN",
            19 => "NINETEEN",
            "012" => "FOURTEEN"
        );
        
        $tens = array( 
            0 => "ZERO",
            1 => "TEN",
            2 => "TWENTY",
            3 => "THIRTY", 
            4 => "FORTY", 
            5 => "FIFTY", 
            6 => "SIXTY", 
            7 => "SEVENTY", 
            8 => "EIGHTY", 
            9 => "NINETY" 
        );

        $hundreds = array( 
        "HUNDRED", 
        "THOUSAND", 
        "MILLION", 
        "BILLION", 
        "TRILLION", 
        "QUARDRILLION" 
        ); /*limit t quadrillion */

        $num = number_format($num,2,".",","); 
        $num_arr = explode(".",$num); 
        $wholenum = $num_arr[0]; 
        $decnum = $num_arr[1]; 
        $whole_arr = array_reverse(explode(",",$wholenum)); 
        krsort($whole_arr,1); 
        $rettxt = ""; 
        foreach($whole_arr as $key => $i){
            while(substr($i,0,1)=="0"){ $i=substr($i,1,5); }
            if($i < 20){ 
            /* echo "getting:".$i; */
            $rettxt .= $i == "" ? "" : $ones[$i]; 
            }elseif($i < 100){ 
                if(substr($i,0,1)!="0")  $rettxt .= $tens[substr($i,0,1)]; 
                if(substr($i,1,1)!="0") $rettxt .= " ".$ones[substr($i,1,1)]; 
            }else{ 
                if(substr($i,0,1)!="0") $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0]; 
                if(substr($i,1,1)!="0")$rettxt .= " ".$tens[substr($i,1,1)]; 
                if(substr($i,2,1)!="0")$rettxt .= " ".$ones[substr($i,2,1)]; 
            } 
            if($key > 0){ 
                $rettxt .= " ".$hundreds[$key]." "; 
            }
        } 

        if($decnum > 0){
            $rettxt .= " and ";
            if($decnum < 20){
                $rettxt .= $ones[$decnum];
            }elseif($decnum < 100){
                $rettxt .= $tens[substr($decnum,0,1)];
                $rettxt .= " ".$ones[substr($decnum,1,1)];
            }
        }
    return $rettxt;
}
?>
<p class="text-center" style="text-align: center;">APP-CSE 2022 FORM<br>
                    <b>ANNUAL PROCUREMENT PLAN - COMMON-USE SUPPLIES AND EQUIPMENT (APP-CSE) 2022 FORM</b>
</p>
<p><i>Introduction:</i></p>
<p class="text-center" style="text-align: center;"><b>
    This form contains the common-use supplies and equipment (CSE) carried in stock by the Procurement Service – Department of Budget and Management (PS-DBM) that may be purchased by government agencies. Consistent with the DBM Circular No. 2018-10 dated 08 November 2018, the APP-CSE shall serve as the agency’s annual procurement request for all its CSE requirements. Only agencies with uploaded APP-CSE in the Virtual Store will be able to purchase CSE from the PS-DBM. Note that the items listed on this form have been arranged in accordance with the United Nations Standard Products and Services Code (UNSPC) in preparation for integration of the APP-CSE template in the Modernized Government Electronic Procurement System (MGEPS).
</b></p>
<p><i>Reminders:</i></p>
<p><b>1.0&nbsp;&nbsp;&nbsp;The APP-CSE form must be accomplished using Microsoft Excel format. The APP-CSE shall be deemed incorrect or invalid if the form used is other than the prescribed format which is downloadable from the Virtual Store.<br>
      2.0&nbsp;&nbsp;&nbsp;All information must be provided accurately.<br>
      3.0&nbsp;&nbsp;&nbsp;Kindly refer to the CSE catalogue on the PS-DBM website (www.ps-philgeps.gov.ph) for the detailed technical specifications and sample photo of the items.<br>
      4.0&nbsp;&nbsp;&nbsp;Do not delete, add, or revise any items or rows on the PART I of this template otherwise the form will be deemed invalid.<br>
      5.0&nbsp;&nbsp;&nbsp;Additional rows for other items may be inserted in PART II, if necessary. Note that this is only applicable in the PART II of the form.<br>
      6.0&nbsp;&nbsp;&nbsp;Once signed and approved by the Property/Supply Officer, Accountant/Budget Officer, and Head of the Agency/Office, kindly upload the soft copy of the APP-CSE in Microsoft Excel format as well as the original signed copy in Portable Document Format (PDF) to the agency's Virtual Store account on or before the prescribed period or deadline. Any APP-CSE form that is unsigned or has incomplete signature shall be deemed invalid.<br>
      7.0&nbsp;&nbsp;&nbsp;Should there be changes in the agency’s CSE requirements, the agency may edit their uploaded APP-CSE directly on their Virtual Store account. However, the agency must ensure that a signed and approved copy of the supplemental APP-CSE form is available. Note that all CSE requirements in excess of the quantities indicated in the original APP-CSE form will not be served if not covered by a supplemental APP-CSE.<br>
      8.0&nbsp;&nbsp;&nbsp;For further assistance or clarification, agencies may contact the Marketing and Sales Division of PS-DBM at (02) 8-689-7750 local 4004, 4005, 4019, or visit the PS-DBM website (www.ps-philgeps.gov.ph) for the guide on how to fill-out the APP-CSE.
    </b>
</p>
<br>
<br>
<p class="text-center" style="text-align: center;"><i>Note: Consistent with <b>Memorandum Circular No. 2021-1 dated 03 June 2021, issued by AO 25,</b> the APP-CSE for FY 2022 must be submitted on or before <b>31 August 2021.</b></i></p>
<p>
    <table style="width: 100%;">
    <tr>
        <td colspan=6 style="width: 20%; border: none; font-size: 12px;" align=right>Department/Bureau/Office:&nbsp;&nbsp;&nbsp;</td>
        <td colspan=4 style="width: 20%; border: none; font-size: 12px;"><u><?= $entity->value ?></u></td>      
        <td colspan=4 style="width: 15%; border: none; font-size: 12px;" align=right>Agency Code/UACS:&nbsp;&nbsp;&nbsp;</td>
        <td colspan=6 style="width: 15%; border: none; font-size: 12px;"><u><?= $agencyCode->value ?></u></td>
        <td colspan=3 style="width: 15%; border: none; font-size: 12px;" align=right>Contact Person:&nbsp;&nbsp;&nbsp;</td>
        <td colspan=3 style="width: 15%; border: none; font-size: 12px;"><u><?= $contactPerson->value ?></u></td>
    </tr>
    <tr>
        <td colspan=6 style="border: none; font-size: 12px;" align=right >Region:&nbsp;&nbsp;&nbsp;</td>
        <td colspan=4 style="border: none; font-size: 12px;" ><u><?= $region->value ?></u></td>
        <td colspan=4 style="border: none; font-size: 12px;" align=right>Organization Type:&nbsp;&nbsp;&nbsp;</td>
        <td colspan=6 style="border: none; font-size: 12px;" ><u><?= $organizationType->value ?></u></td>
        <td colspan=3 style="border: none; font-size: 12px;" align=right>Position:&nbsp;&nbsp;&nbsp;</td>
        <td colspan=3 style="border: none; font-size: 12px;" ><u><?= $contactPersonPosition->value ?></u></td>
    </tr>
    <tr>
        <td colspan=6 style="border: none; font-size: 12px;" align=right>Address:&nbsp;&nbsp;&nbsp;</td>
        <td colspan=4 style="border: none; font-size: 12px;" ><u><?= $address->value ?></u></td>
        <td colspan=4 style="border: none; font-size: 12px;" align=right></td>
        <td colspan=6 style="border: none; font-size: 12px;" ></td>
        <td colspan=3 style="border: none; font-size: 12px;" align=right>E-mail:&nbsp;&nbsp;&nbsp;</td>
        <td colspan=3 style="border: none; font-size: 12px;" ><u><span style="color: blue;"><?= $email->value ?></span></u></td>
    </tr>
    <tr>
        <td colspan=6 style="border: none; font-size: 12px;" align=right></td>
        <td colspan=4 style="border: none; font-size: 12px;" ></td>
        <td colspan=4 style="border: none; font-size: 12px;" align=right></td>
        <td colspan=6 style="border: none; font-size: 12px;" ></td>
        <td colspan=3 style="border: none; font-size: 12px;" align=right>Telephone/Mobile No.:&nbsp;&nbsp;&nbsp;</td>
        <td colspan=3 style="border: none; font-size: 12px;" ><u><?= $telephone->value.'/'.$mobile->value ?></u></td>
    </tr>
</table>
</p>
<br>
<table class="table-responsive table-condensed table-hover">
    <thead>
        <tr style="background-color: #F2AF94;">
            <th rowspan=2>#</th>
            <th rowspan=2>Item & Specifications</th>
            <th rowspan=2>Unit of Measure</th>
            <th colspan=20>Monthly Requirements</th>
            <th rowspan=2>Total Quantity for the Year</th>
            <th rowspan=2>Price Catalogue</th>
            <th rowspan=2>Total Amount for the year</th>
        </tr>
        <tr style="background-color: #F2AF94;">
            <th>Jan</th>
            <th>Feb</th>
            <th>Mar</th>
            <th>Q1</th>
            <th style="font-size: 10px;">Q1 AMOUNT</th>
            <th>Apr</th>
            <th>May</th>
            <th>Jun</th>
            <th>Q2</th>
            <th style="font-size: 10px;">Q2 AMOUNT</th>
            <th>Jul</th>
            <th>Aug</th>
            <th>Sep</th>
            <th>Q3</th>
            <th style="font-size: 10px;">Q3 AMOUNT</th>
            <th>Oct</th>
            <th>Nov</th>
            <th>Dec</th>
            <th>Q4</th>
            <th style="font-size: 10px;">Q4 AMOUNT</th>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($items)){ ?>
        <?php $i = 1; ?>
        <?php foreach($items as $item){ ?>
            <tr>
                <td><?= $i ?></td>
                <td><?= $item['title'] ?></td>
                <td><?= $item['unit_of_measure'] ?></td>
                <td align=center><?= number_format($item['janTotal'], 0) ?></td>
                <td align=center><?= number_format($item['febTotal'], 0) ?></td>
                <td align=center><?= number_format($item['marTotal'], 0) ?></td>
                <td align=center><?= number_format($item['janTotal'] + $item['febTotal'] + $item['marTotal'], 0) ?></td>
                <td align=right><?= number_format(($item['janTotal'] + $item['febTotal'] + $item['marTotal']) * $item['cost'], 2) ?></td>
                <td align=center><?= number_format($item['aprTotal'], 0) ?></td>
                <td align=center><?= number_format($item['mayTotal'], 0) ?></td>
                <td align=center><?= number_format($item['junTotal'], 0) ?></td>
                <td align=center><?= number_format($item['aprTotal'] + $item['mayTotal'] + $item['junTotal'], 0) ?></td>
                <td align=right><?= number_format(($item['aprTotal'] + $item['mayTotal'] + $item['junTotal']) * $item['cost'], 2) ?></td>
                <td align=center><?= number_format($item['julTotal'], 0) ?></td>
                <td align=center><?= number_format($item['augTotal'], 0) ?></td>
                <td align=center><?= number_format($item['sepTotal'], 0) ?></td>
                <td align=center><?= number_format($item['julTotal'] + $item['augTotal'] + $item['sepTotal'], 0) ?></td>
                <td align=right><?= number_format(($item['julTotal'] + $item['augTotal'] + $item['sepTotal']) * $item['cost'], 2) ?></td>
                <td align=center><?= number_format($item['octTotal'], 0) ?></td>
                <td align=center><?= number_format($item['novTotal'], 0) ?></td>
                <td align=center><?= number_format($item['decTotal'], 0) ?></td>
                <td align=center><?= number_format($item['octTotal'] + $item['novTotal'] + $item['decTotal'], 0) ?></td>
                <td align=right><?= number_format(($item['octTotal'] + $item['novTotal'] + $item['decTotal']) * $item['cost'], 2) ?></td>
                <td align=center><?= number_format($item['janTotal'] + 
                                                $item['febTotal'] + 
                                                $item['marTotal'] + 
                                                $item['aprTotal'] + 
                                                $item['mayTotal'] + 
                                                $item['junTotal'] + 
                                                $item['julTotal'] + 
                                                $item['augTotal'] + 
                                                $item['sepTotal'] + 
                                                $item['octTotal'] + 
                                                $item['novTotal'] + 
                                                $item['decTotal'], 0) ?></td>
                <td align=right><?= number_format($item['cost'], 2) ?></td>
                <td align=right><?= number_format(($item['janTotal'] + 
                                                $item['febTotal'] + 
                                                $item['marTotal'] + 
                                                $item['aprTotal'] + 
                                                $item['mayTotal'] + 
                                                $item['junTotal'] + 
                                                $item['julTotal'] + 
                                                $item['augTotal'] + 
                                                $item['sepTotal'] + 
                                                $item['octTotal'] + 
                                                $item['novTotal'] + 
                                                $item['decTotal']) * $item['cost'], 2) ?></td>
            </tr>
            <?php $total += ($item['janTotal'] + 
                                $item['febTotal'] + 
                                $item['marTotal'] + 
                                $item['aprTotal'] + 
                                $item['mayTotal'] + 
                                $item['junTotal'] + 
                                $item['julTotal'] + 
                                $item['augTotal'] + 
                                $item['sepTotal'] + 
                                $item['octTotal'] + 
                                $item['novTotal'] + 
                                $item['decTotal']) * $item['cost']; ?>
            <?php $i++ ?>
        <?php } ?>
    <?php } ?>
    <tr>
        <td colspan=26 style="border-left: none; border-right: none;">&nbsp;</td>
    </tr>
    <tr style="background-color: #9DC0E8;">
        <td colspan=3 style="font-size: 12px;"><b>A. TOTAL</b></td>
        <td colspan=20>&nbsp;</td>
        <td colspan=3 style="font-size: 12px;" align=right><b>Php <?= number_format($total, 2) ?></b></td>
    </tr>
    <tr style="background-color: #9DC0E8;">
        <td colspan=3 style="font-size: 12px;"><b>B. ADDITIONAL PROVISION FOR INFLATION (10% OF TOTAL)</b></td>
        <td colspan=20>&nbsp;</td>
        <td colspan=3 style="font-size: 12px;" align=right><b>Php <?= number_format($total * 0.10, 2) ?></b></td>
    </tr>
    <tr style="background-color: #9DC0E8;">
        <td colspan=3 style="font-size: 12px;"><b>C. ADDITIONAL PROVISION FOR TRANSPORT AND FREIGHT COST (If Applicable)</b></td>
        <td colspan=20>&nbsp;</td>
        <td colspan=3 style="font-size: 12px;" align=right><b>-</b></td>
    </tr>
    <tr style="background-color: #9DC0E8;">
        <td colspan=3 style="font-size: 12px;"><b>D. GRAND TOTAL (A + B + C)</b></td>
        <td colspan=20>&nbsp;</td>
        <td colspan=3 style="font-size: 12px;" align=right><b>Php <?= number_format($total + ($total * 0.10), 2) ?></b></td>
    </tr>
    <tr style="background-color: #9DC0E8;">
        <td colspan=3 style="font-size: 12px;"><b>E. APPROVED BUDGET BY THE AGENCY HEAD<br>In Figures and Words: </b></td>
        <td colspan=23 style="font-size: 12px;"><b>(Php <?= number_format($total + ($total * 0.10), 2) ?>) <?= ($total + ($total * 0.10)) > 0 ? numberToWords($total + ($total * 0.10)) : 'ZERO' ?> PESOS ONLY</b></td>
    </tr>
    </tbody>
</table>
<br>
<br>
    <p><b>We hereby warrant that the total amount reflected in this Annual Procurement Plan to procure the listed common-use supplies, materials, and equipment has been included in or is within our approved budget for the year.</b></p>
    <table style="width: 100%;">
        <tr>
            <td colspan=3 style="font-size: 14px; border: none;"><b>Prepared By:</b></td>
            <td colspan=20 style="font-size: 14px; border: none;"><b>Certified Funds Available / Certified Appropriate Funds Available:</b></td>
            <td colspan=3 style="font-size: 14px; border: none;"><b>Approved By:</b></td>
        </tr>
        <tr>
            <td colspan=3 style="font-size: 14px; border: none;">&nbsp;</td>
            <td colspan=20 style="font-size: 14px; border: none;">&nbsp;</td>
            <td colspan=3 style="font-size: 14px; border: none;">&nbsp;</td>
        </tr>
        <tr>
            <td colspan=3 style="font-size: 14px; border: none;">&nbsp;</td>
            <td colspan=20 style="font-size: 14px; border: none;">&nbsp;</td>
            <td colspan=3 style="font-size: 14px; border: none;">&nbsp;</td>
        </tr>
        <tr>
            <td colspan=3 style="font-size: 14px; border: none;">&nbsp;</td>
            <td colspan=20 style="font-size: 14px; border: none;">&nbsp;</td>
            <td colspan=3 style="font-size: 14px; border: none;">&nbsp;</td>
        </tr>
        <tr>
            <td colspan=3 style="font-size: 14px; border: none;"><u><?= Yii::$app->user->identity->userinfo->fullName ?></u></td>
            <td colspan=20 style="font-size: 14px; border: none;"><u><?= $accountant->value ?></u></td>
            <td colspan=3 style="font-size: 14px; border: none;"><u><?= $regionalDirector->value ?></u></u></td>
        </tr>
        <tr>
            <td colspan=3 style="font-size: 14px; border: none;"><b><?= Yii::$app->user->identity->userinfo->POSITION_C ?></b></td>
            <td colspan=20 style="font-size: 14px; border: none;"><b><?= $accountantPosition->value ?></b></td>
            <td colspan=3 style="font-size: 14px; border: none;"><b>Regional Director</b></td>
        </tr>
    </table>