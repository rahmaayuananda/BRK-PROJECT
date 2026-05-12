<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activitylog extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('activity_log');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->database();
    }

    /**
     * Tampilkan semua activity log
     */
    public function index()
    {
        // Cek apakah user sudah login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
            return;
        }

        // Cek apakah user adalah admin (opsional)
        // $role = $this->session->userdata('role');
        // if ($role !== 'admin') {
        //     show_error('Anda tidak memiliki akses ke halaman ini');
        //     return;
        // }

        // Get pagination
        $page = $this->input->get('page', true) ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;

        // Get filters from query string
        $filters = [];
        $action = $this->input->get('action', true);
        $user_id = $this->input->get('user_id', true);
        $target_id = $this->input->get('target_id', true);

        if ($action) $filters['action'] = $action;
        if ($user_id) $filters['user_id'] = $user_id;
        if ($target_id) $filters['target_id'] = $target_id;

        // Get activities
        $data['activities'] = $this->activity_log->get_activities_with_filters($filters, $limit);
        $data['total_activities'] = $this->activity_log->count_all_activities();
        $data['current_page'] = $page;
        $data['limit'] = $limit;
        $data['filters'] = $filters;

        $this->load->view('activitylog/index', $data);
    }

    /**
     * API: Get activity log as JSON
     */
    public function api_get_all()
    {
        if (!$this->session->userdata('logged_in')) {
            return $this->output->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Unauthorized']));
        }

        $page = $this->input->get('page', true) ?? 1;
        $limit = $this->input->get('limit', true) ?? 50;
        $offset = ($page - 1) * $limit;

        $activities = $this->activity_log->get_all_activities($limit, $offset);
        $total = $this->activity_log->count_all_activities();

        $response = [
            'success' => true,
            'data' => $activities,
            'pagination' => [
                'current_page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ];

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * API: Get activity log by user
     */
    public function api_get_by_user($user_id = null)
    {
        if (!$user_id) {
            return $this->output->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'user_id required']));
        }

        if (!$this->session->userdata('logged_in')) {
            return $this->output->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Unauthorized']));
        }

        $limit = $this->input->get('limit', true) ?? 50;
        $activities = $this->activity_log->get_user_activities($user_id, $limit);

        $response = [
            'success' => true,
            'user_id' => $user_id,
            'data' => $activities,
            'total' => count($activities)
        ];

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * API: Get activity log by action
     */
    public function api_get_by_action($action = null)
    {
        if (!$action) {
            return $this->output->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'action required']));
        }

        if (!$this->session->userdata('logged_in')) {
            return $this->output->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Unauthorized']));
        }

        $limit = $this->input->get('limit', true) ?? 50;
        $activities = $this->activity_log->get_activities_by_action($action, $limit);

        $response = [
            'success' => true,
            'action' => $action,
            'data' => $activities,
            'total' => count($activities)
        ];

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * API: Get activity log by topic
     */
    public function api_get_by_topic($topic_id = null)
    {
        if (!$topic_id) {
            return $this->output->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'topic_id required']));
        }

        if (!$this->session->userdata('logged_in')) {
            return $this->output->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Unauthorized']));
        }

        $activities = $this->activity_log->get_topic_activities($topic_id);

        $response = [
            'success' => true,
            'topic_id' => $topic_id,
            'data' => $activities,
            'total' => count($activities)
        ];

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * API: Get statistics activity
     */
    public function api_get_statistics()
    {
        if (!$this->session->userdata('logged_in')) {
            return $this->output->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Unauthorized']));
        }

        $stats = [
            'total_activities' => $this->activity_log->count_all_activities(),
            'login_count' => $this->activity_log->count_activities_by_action('LOGIN'),
            'create_topic_count' => $this->activity_log->count_activities_by_action('CREATE_TOPIC'),
            'archive_topic_count' => $this->activity_log->count_activities_by_action('ARCHIVE_TOPIC'),
            'mark_faq_count' => $this->activity_log->count_activities_by_action('MARK_FAQ')
        ];

        $response = [
            'success' => true,
            'data' => $stats
        ];

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * API: Get date range activities
     */
    public function api_get_by_date_range()
    {
        if (!$this->session->userdata('logged_in')) {
            return $this->output->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Unauthorized']));
        }

        $start_date = $this->input->get('start_date', true);
        $end_date = $this->input->get('end_date', true);
        $limit = $this->input->get('limit', true) ?? 100;

        if (!$start_date || !$end_date) {
            return $this->output->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'start_date and end_date required']));
        }

        $activities = $this->activity_log->get_activities_by_date_range($start_date, $end_date, $limit);

        $response = [
            'success' => true,
            'date_range' => [
                'start' => $start_date,
                'end' => $end_date
            ],
            'data' => $activities,
            'total' => count($activities)
        ];

        return $this->output->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}
