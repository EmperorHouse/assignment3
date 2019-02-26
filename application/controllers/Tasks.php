<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tasks extends CI_Controller
{
    public function __construct(){
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('no_access', 'Sorry you are NOT allowed or not logged in');

            redirect('home/index');
        }
    }

    public function display($task_id)
    {
        $data['project_id'] = $this->task_model->get_task_project_id($task_id);
        $data['project_name'] = $this->task_model->get_project_name($data['project_id']);

        $data['task'] = $this->task_model->get_task($task_id);
        $data['main_view'] = "tasks/display";
        $this->load->view('layouts/main', $data);
    }

    // your new methods go here
    public function create($project_id){

        $this->form_validation->set_rules('task_name', 'Task Name', 'trim|required');
        $this->form_validation->set_rules('task_body', 'Task Description', 'trim|required');

        if ($this->form_validation->run() == false) {
            $data['main_view'] = 'tasks/create_task';
            $this->load->view('layouts/main', $data);
        } else {
            $data = array(
                //'task_user_id' => $this->session->userdata('user_id'),
                'project_id' => $project_id,
                'task_name' => $this->input->post('task_name'),
                'task_body' => $this->input->post('task_body'),
                'due_date' => $this->input->post('due_date')
                );

            if ($this->task_model->create_task($data)) {
                $this->session->set_flashdata('task_created', 'Your task has been created');

                redirect("projects/display/" . $project_id);
            }
        }
    }

    public function edit($task_id){
        $this->form_validation->set_rules('task_name', 'Task Name', 'trim|required');
        $this->form_validation->set_rules('task_body', 'Task Description', 'trim|required');

        if ($this->form_validation->run() == false) {
            $data['the_task'] = $this->task_model->get_task($task_id);//get_task_info($task_id);

            $data['main_view'] = 'tasks/edit_task';
            $this->load->view('layouts/main', $data);
        } else {
            $project_id = $this->task_model->get_task_project_id($task_id);
            $data = array(
                //'task_user_id' => $this->session->userdata('user_id'),
                'id' => $task_id,
                'task_name' => $this->input->post('task_name'),
                'task_body' => $this->input->post('task_body'),
                'due_date' => $this->input->post('due_date')
                );

            if ($this->task_model->edit_task($task_id, $data)) {
                $this->session->set_flashdata('task_updated', 'Your Task has been updated');

                redirect("projects/display/" . $project_id);
            }
        }

        
    }

    public function delete($task_id){
        $project_id = $this->task_model->get_task_project_id($task_id);
        $this->task_model->delete_task($task_id);
        $this->session->set_flashdata('task_deleted', 'Your task has been deleted');
        redirect("projects/display/" . $project_id);
    }

    public function index(){
        $user_id = $this->session->userdata('user_id');
        $data['tasks'] = $this->task_model->get_all_tasks($user_id);
        //$data['projects'] = $this->project_model->get_projects();

        $data['main_view'] = "tasks/index";

        $this->load->view('layouts/main', $data);
    }
}
