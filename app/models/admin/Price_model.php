<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Price_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
    }

    public function calculate_tax($price,$tax_rate,$tax_type){
        $tax_val = 0;
        if($tax_type == 1){
            $tax_val = ($price/100)*$tax_rate; 
        }
        else if($tax_type == 2){
            $tax_val = $tax_rate; 
        }
        else{
            $tax_val = 0;
        }
        return round($tax_val,4);
    }
    public function calculate_discount($price,$discount){
        $discount_val = ($price/100)*$discount;
        return round($discount_val,4);
    }
    public function calculate_dp_with_tax($price,$tax_rate,$tax_type){
        $tax = $this->calculate_tax($price,$tax_rate,$tax_type);
        $selling_price = $price+$tax;
        return round($selling_price,4);
    }
    public function calculate_dp_with_tax_and_discount($price,$tax_rate,$tax_type,$discount){
        $tp_with_tax = $this->calculate_dp_with_tax($price,$tax_rate,$tax_type);
        $discount = $this->calculate_discount($price,$discount);
        $selling_price = $price+$tp_with_tax-$discount;
        return round($selling_price,4);
    }
    public function calculate_tp_with_tax($price,$tax_rate,$tax_type){
        $tax = $this->calculate_tax($price,$tax_rate,$tax_type);
        $selling_price = $price+$tax;
        return round($selling_price,4);
    }
    public function calculate_tp_with_tax_and_discount($price,$tax_rate,$tax_type,$discount){
        $tp_with_tax = $this->calculate_dp_with_tax($price,$tax_rate,$tax_type);
        $discount = $this->calculate_discount($price,$discount);
        $selling_price = $price+$tp_with_tax-$discount;
        return round($selling_price,4);
    }
    public function calculate_crossdock_with_discount($price,$discount){
        $discount = $this->calculate_discount($price,$discount);
        $selling_price = $price-$discount;
        return round($selling_price,4);
    }
    public function calculate_mrp_with_discount($price,$discount){
        $discount = $this->calculate_discount($price,$discount);
        $selling_price = $price-$discount;
        return round($selling_price,4);
    }
}