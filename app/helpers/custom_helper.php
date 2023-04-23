<?php
   function decimalallow($val,$zero = 4){
      return number_format((float)$val, $zero, '.', '');
   }
   function amountformate($v, $d = 4, $c = "", $cl = 'l'){
      $sendvalue = number_format($v,$d);
      if($cl == "l" && $c != ""){
         $sendvalue = $c." ".$sendvalue;
      }
      else if($cl == "r" && $c != ""){
         $sendvalue = $sendvalue." ".$c;
      }
      return $sendvalue;
   }
   function dateformate($date,$formate = "Y-m-d"){
      return date($formate, strtotime($date));
   }
