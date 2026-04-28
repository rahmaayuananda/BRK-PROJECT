<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('forum_model');
        $this->load->helper('url');
    }

    public function index()
    {
        $data['total_topics'] = $this->forum_model->count_topics();
        $data['latest_topics'] = $this->forum_model->get_latest_topics(5);
        $data['popular_topics'] = $this->forum_model->get_popular_topics(5);
        $data['recent_activity'] = $this->forum_model->get_recent_activity(10);

        // provide a safe return URL so profile page can link back to dashboard
        $data['return_url'] = site_url('dashboard');

        $this->load->view('dashboard/vw_dashboard', $data);
    }
}
