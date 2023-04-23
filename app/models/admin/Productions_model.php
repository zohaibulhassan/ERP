<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Productions_model extends CI_Model
{
    public function __construct(){
        parent::__construct();
    }
    public function getProductionByID($id){
        $q = $this->db->get_where('productions', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }
    public function getPaymentByID($id){
        $q = $this->db->get_where('payments', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }
    public function addPayment($data = array()){
        if ($this->db->insert('payments', $data)) {
            if ($this->site->getReference('ppay') == $data['reference_no']) {
                $this->site->updateReference('ppay');
            }
            $this->site->syncProductionPayments($data['production_id']);
            return true;
        }
        return false;
    }
    public function updatePayment($id, $data = array()){
        if ($this->db->update('payments', $data, array('id' => $id))) {
            $this->site->syncProductionPayments($data['production_id']);
            return true;
        }
        return false;
    }
    public function deletePayment($id){
        $opay = $this->getPaymentByID($id);
        if ($this->db->delete('payments', array('id' => $id))) {
            $this->site->syncProductionPayments($opay->production_id);
            return true;
        }
        return FALSE;
    }
}
