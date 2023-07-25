<?php defined('BASEPATH') OR exit('No direct script access allowed');
class ContactModel extends CI_Model {
    /**
     * @vars
     */
    private $_db;
    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        // define primary table
        $this->_db = 'emails';
    }
    /**
     * Save generated CAPTCHA to database
     *
     * @param  array $data
     * @return boolean
     */
    public function save_captcha($data = array()) {
        // CAPTCHA data required
        if ($data) {
            
            // insert CAPTCHA
            $query = $this->db->insert_string('captcha', $data);

            $this->db->query($query);
            // return
            return TRUE;
        }
        return FALSE;
    }
    /**
     * Verify CAPTCHA
     *
     * @param  string $captcha
     * @return boolean
     */
    public function verify_captcha($captcha = NULL) {
        // CAPTCHA string required
        if ($captcha) {
            // remove old CAPTCHA
            $expiration = time() - 7200; // 2-hour limit
            $this->db->query("DELETE FROM captcha WHERE captcha_time < {$expiration}");
            // build query
            $sql = "
            SELECT
            COUNT(*) AS count
            FROM captcha
            WHERE word = " . $this->db->escape($captcha) . "
            AND ip_address = '" . $this->input->ip_address() . "'
            AND captcha_time > '{$expiration}'
            ";
            // execute query
            $query = $this->db->query($sql);
            // return results
            if ($query->row()->count > 0) {
                return TRUE;
            }
        }
        return FALSE;
    }
    /**
     * Save and email contact message
     *
     * @param  array $data
     * @param  array $settings
     * @return boolean
     */
    public function save_and_send_message($data = array(), $settings = array()) {
        // post data and settings required

        if ($data && $settings) {
            // build query
            $sql = "
            INSERT INTO {$this->_db} (
            name, email, title, message, created
            ) VALUES (
            " . $this->db->escape($data['name']) . ",
            " . $this->db->escape($data['email']) . ",
            " . $this->db->escape($data['title']) . ",
            " . $this->db->escape($data['message']) . ",
            '" . date('Y-m-d H:i:s') . "'
            )
            ";
            // execute query
            $this->db->query($sql);
            $id = $this->db->insert_id();
            $this->load->library('SendMail');

            if ($id) {
               
                try {
                    
                    // send email
                   
                    $mail_html = $this->load->view('email',array('html' => $data['message'],'subject' => $data['title']),true);

                    $send_mail = $this->sendmail->sendTo($settings->site_email, $data['title'], $data['name'], $mail_html);
                    //$send_mail = @$this->email->send();
                    
                    if ($send_mail) {
                        return TRUE;
                    } else {
                        // send mail failed - remove message from database
                        $this->db->query("DELETE FROM {$this->_db} WHERE id = {$id}");
                    }
                }
                catch(Exception $e) {
                    // send mail failed - remove message from database
                    $this->db->query("DELETE FROM {$this->_db} WHERE id = {$id}");
                }
            }
            
            return $this->db->insert_id();
        }

        return FALSE;
    }
    /**
     * Get list of non-deleted users
     *
     * @param  int $limit
     * @param  int $offset
     * @param  array $filters
     * @param  string $sort
     * @param  string $dir
     * @return array|boolean
     */
    function get_all($limit = 0, $offset = 0, $filters = array(), $sort = 'created', $dir = 'DESC') {
        // start building query
        $sql = "
        SELECT SQL_CALC_FOUND_ROWS *
        FROM {$this->_db}
        WHERE 1 = 1
        ";
        // apply filters
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $value = $this->db->escape('%' . $value . '%');
                $sql.= " AND {$key} LIKE {$value}";
            }
        }
        // continue building query
        $sql.= " ORDER BY {$sort} {$dir}";
        // add limit and offset
        if ($limit) {
            $sql.= " LIMIT {$offset}, {$limit}";
        }
        // execute query
        $query = $this->db->query($sql);
        // define results
        if ($query->num_rows() > 0) {
            $results['results'] = $query->result_array();
        } else {
            $results['results'] = NULL;
        }
        // get total count
        $sql = "SELECT FOUND_ROWS() AS total";
        $query = $this->db->query($sql);
        $results['total'] = $query->row()->total;
        // return results
        return $results;
    }
    /**
     * Set email message as read
     *
     * @param  int $id
     * @param  int $read_by
     * @return boolean
     */
    public function read($id = NULL, $read_by = NULL) {
        // data required
        if ($id and $read_by) {
            // build query string
            $sql = "
            UPDATE {$this->_db}
            SET `read` = '" . date('Y-m-d H:i:s') . "',
            read_by = {$read_by}
            WHERE id = {$id}
            ";
            // execute query
            $this->db->query($sql);
            // return results
            if ($this->db->affected_rows()) {
                return TRUE;
            }
        }
        return FALSE;
    }
}
