<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Survey extends MY_Controller {
    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }
        $this->lang->admin_load('settings', $this->Settings->user_language);
        $this->load->admin_model('general_model');

    }
    public function questions(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('survey'), 'page' => lang('Survey')), array('link' => '#', 'page' => lang('List')));
        $meta = array('page_title' => lang('Questions'), 'bc' => $bc);
        $this->page_construct2('survey/questions', $meta, $this->data);
    }
    public function get_questions(){
        // Count Total Rows
        $this->db->from('survey_questions');
        $totalq = $this->db->get();
        $this->runquery_questions('yes');
        $query = $this->db->get();
        $recordsFiltered = $query->num_rows();
        $this->runquery_questions();
        if($_POST['length'] != -1){
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $rows = $query->result();
        $data = array();
        foreach($rows as $row){
            $option = '';
            if($row->type != 1){
                if($row->options != ""){
                    $os = json_decode($row->options);
                    $option .= '<ol>';
                    foreach($os as $o){
                        $option .= '<li>'.$o.'</li>';
                    }
                    $option .= '</ol>';
                }
            }
            $type = "Text";
            if($row->type == 2){
                $type = "Multi Selection";
            }
            else if($row->type == 3){
                $type = "Single Selection";
            }
            $button = '<a class="md-btn md-btn-warning md-btn-wave-light waves-effect waves-button waves-light md-btn-mini" href="'.base_url("admin/survey/edit_question/".$row->id).'" >Edit</a>';
            $button .= '<button class="md-btn md-btn-danger md-btn-wave-light waves-effect waves-button waves-light md-btn-mini deletebtn" data-id="'.$row->id.'" >Delete</button>';
            $data[] = array(
                $row->id,
                $row->question,
                $type,
                $option,
                $row->created_at,
                $row->status == 0 ? "Draft" : "Publish",
                $button
            );
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $totalq->num_rows(),
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        );
        // Output to JSON format
        echo json_encode($output);
    }
    public function runquery_questions($onlycoun = "no"){
        $column_search = array(
            'survey_questions.id',
            'survey_questions.question',
            'survey_questions.created_at',
        );
        //Get Data
        if($onlycoun == "yes"){
            $this->db->select('survey_questions.id as id');
        }
        else{
            $this->db->select('survey_questions.*');
        }
        $this->db->from('survey_questions as survey_questions');
        $i = 0;
        // loop searchable columns 
        if($onlycoun != "yes"){
            foreach($column_search as $item){
                // if datatable send POST for search
                if($_POST['search']['value']){
                    // first loop
                    if($i===0){
                        // open bracket
                        $this->db->group_start();
                        $this->db->like($item, $_POST['search']['value']);
                    }else{
                        $this->db->or_like($item, $_POST['search']['value']);
                    }
                    // last loop
                    if(count($column_search) - 1 == $i){
                        // close bracket
                        $this->db->group_end();
                    }
                }
                $i++;
            }
        }
        if($onlycoun != "yes"){
            $this->db->order_by($_POST['order']['0']['column']+1, $_POST['order']['0']['dir']);
        }
    }
    function add_question(){
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('survey'), 'page' => lang('survey')), array('link' => '#', 'page' => lang('Add Question')));
        $meta = array('page_title' => lang('Add Question'), 'bc' => $bc);
        $this->page_construct2('survey/question_add', $meta, $this->data);
    }
    public function create_question(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $question = $this->input->post('question');
        $type = $this->input->post('type');
        $status = $this->input->post('status');
        $options = $this->input->post('options');
        if($question != "" && $type != "" && $status != ""){
            $options = explode(',',$options);
            $options = json_encode($options);
            $insert['question'] = $question;
            $insert['type'] = $type;
            $insert['options'] = $options;
            $insert['status'] = $status;
            $this->db->insert('survey_questions',$insert);
            $senddata['message'] = "Question create successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    function edit_question($id){
        if($id != ""){
            $this->db->from('survey_questions');
            $this->db->where('id',$id);
            $q = $this->db->get();
            if($q->num_rows() > 0){
                $this->data['question'] = $q->result()[0];
                $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
                $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('survey'), 'page' => lang('Survey')), array('link' => '#', 'page' => lang('Edit Survey')));
                $meta = array('page_title' => lang('Edit Question'), 'bc' => $bc);
                $this->page_construct2('survey/question_edit', $meta, $this->data);
            }
            else{
                redirect(base_url('admin/survey'));
            }
        }
        else{
            redirect(base_url('admin/survey'));
        }

    }
    public function update_question(){
        $senddata['status'] = false;
        $senddata['message'] = "Try Again!";
        $id = $this->input->post('id');
        $question = $this->input->post('question');
        $type = $this->input->post('type');
        $status = $this->input->post('status');
        $options = $this->input->post('options');
        if($id != "" && $question != "" && $type != "" && $status != ""){
            $options = explode(',',$options);
            $options = json_encode($options);
            $set['question'] = $question;
            $set['type'] = $type;
            $set['options'] = $options;
            $set['status'] = $status;
            $this->db->set($set);
            $this->db->where('id',$id);
            $this->db->update('survey_questions');
            $senddata['message'] = "Question update successfully";
            $senddata['status'] = true;
        }
        else{
            $senddata['message'] = "Enter required field!";
        }
        echo json_encode($senddata);
    }
    public function delete_question(){
        $senddata['status'] = false;
        $senddata['message'] = "Try again!";
        $id = $this->input->post('id');
        $reason = $this->input->post('reason');
        if($this->data['Owner']){
            if($reason != ""){
                $this->db->delete('survey_questions', array('id' => $id));
                $senddata['status'] = true;
                $senddata['message'] = "Question delete successfully!";
            }
            else{
                $senddata['message'] = "Enter Reason!";
            }
        }
        else{
            $senddata['message'] = "Permission Denied!";
        }
        echo json_encode($senddata);

    }
}
