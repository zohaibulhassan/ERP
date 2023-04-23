<?php defined('BASEPATH') OR exit('No direct script access allowed');

class General_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
    }
    public function GetAllWarehouses(){
        $this->db->select('id,name as text');
        $this->db->from('warehouses');
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllBrands(){
        $this->db->select('id,name as text');
        $this->db->from('brands');
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllFormulas(){
        $this->db->select('id,code as text');
        $this->db->from('product_formulas');
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllDisease(){
        $this->db->select('id,name as text');
        $this->db->from('diseases');
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllFormulaForm(){
        $this->db->select('id,name as text');
        $this->db->from('formula_forms');
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllFormulaStrengths(){
        $this->db->select('id,name as text');
        $this->db->from('formula_strengths');
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllProductForms(){
        $this->db->select('id,name as text');
        $this->db->from('product_forms');
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllManufacturers(){
        $this->db->select('id,name as text');
        $this->db->from('manufacturers');
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllCategories($id = 'all'){
        $this->db->select('id,name as text');
        $this->db->from('categories');
        if($id != "" && $id != "all"){
            $this->db->where('parent_id',$id);
        }
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllSubCategories(){
        $this->db->select('id,name as text');
        $this->db->from('categories');
        $this->db->where('parent_id != 0');
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllGroups(){
        $this->db->select('id,name as text');
        $this->db->from('product_groups');
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllUnits(){
        $this->db->select('id,name as text');
        $this->db->from('units');
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllProduct_tax(){
        $this->db->select('id,name as text');
        $this->db->from('tax_rates');
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllSuppliers(){
        $this->db->select('id,name as text');
        $this->db->from('companies');
        $this->db->where('group_name','supplier');
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllCustomers(){
        $this->db->select('id,name as text');
        $this->db->from('companies');
        $this->db->where('group_name','customer');
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllOwnCompanies(){
        $this->db->select('id,companyname as text');
        $this->db->from('own_companies');
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllWallets(){
        $this->db->select('id,title as text');
        $this->db->from('wallets');
        $this->db->where('status','active');
        $q = $this->db->get();
        return $q->result();
    }
    public function GetAllExpenseCategories(){
        $this->db->select('id,name as text');
        $this->db->from('expense_categories');
        $q = $this->db->get();
        return $q->result();
    }

}
