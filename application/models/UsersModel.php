<?php defined('BASEPATH') OR exit('No direct script access allowed');
class UsersModel extends CI_Model {
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
        $this->_db = 'users';
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
    function get_all($limit = 0, $offset = 0, $filters = array(), $sort = 'last_name', $dir = 'ASC') 
    {
        $logged_in_user = $this->session->userdata('logged_in');
        $role_query = "";
        if($logged_in_user['role'] == "tutor")
        {
            $role_query = " AND ROLE != 'tutor' AND is_admin = '0'";
        }

        $sql = "
        SELECT SQL_CALC_FOUND_ROWS *,(select title from courses where courses.id = users.course_id) as course_title
        FROM {$this->_db}
        WHERE deleted = '0' ".$role_query;
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $value = $this->db->escape('%' . $value . '%');
                $sql.= " AND {$key} LIKE {$value}";
            }
        }
        $sql.= " ORDER BY {$sort} {$dir}";
        if ($limit) {
            $sql.= " LIMIT {$offset}, {$limit}";
        }
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $results['results'] = $query->result_array();
        } else {
            $results['results'] = NULL;
        }
        $sql = "SELECT FOUND_ROWS() AS total";
        $query = $this->db->query($sql);
        $results['total'] = $query->row()->total;
        return $results;
    }


    function get_all_tutor_students($limit = 0, $offset = 0, $filters = array(), $sort = 'last_name', $dir = 'ASC',$user_ids = array()) 
    {
        $logged_in_user = $this->session->userdata('logged_in');
        $role_query = "";
        if($logged_in_user['role'] == "tutor")
        {
            $role_query = " AND ROLE != 'tutor' AND is_admin = '0'";
        }

        $sql = "
        SELECT SQL_CALC_FOUND_ROWS *
        FROM {$this->_db}
        WHERE deleted = '0' ".$role_query;
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $value = $this->db->escape('%' . $value . '%');
                $sql.= " AND {$key} LIKE {$value}";
            }
        }

        if($user_ids)
        {            
            $sql.= "AND id IN (".implode(',', $user_ids).")";
        }
        else
        {
           $sql.= "AND id IN (0)"; 
        }

        $sql.= " ORDER BY {$sort} {$dir}";
        if ($limit) {
            $sql.= " LIMIT {$offset}, {$limit}";
        }
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $results['results'] = $query->result_array();
        } else {
            $results['results'] = NULL;
        }
        $sql = "SELECT FOUND_ROWS() AS total";
        $query = $this->db->query($sql);
        $results['total'] = $query->row()->total;
        return $results;
    }

    /**
     * Get specific user
     *
     * @param  int $id
     * @return array|boolean
     */

    function get_user($id = NULL) 
    {
        if ($id) {
            $sql = "
            SELECT *
            FROM {$this->_db}
            WHERE id = " . $this->db->escape($id) . "
            AND deleted = '0'
            ";
            $query = $this->db->query($sql);
            if ($query->num_rows()) {
                return $query->row_array();
            }
        }
        return FALSE;
    }

    /**
     * Add a new user
     *
     * @param  array $data
     * @return mixed|boolean
     */
    function add_user($data = array()) 
    {
        if ($data) {
            // secure password
            $salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
            $password = hash('sha512', $data['password'] . $salt);
            $config['upload_path'] = "./assets/images/user_image";
            $config['allowed_types'] = 'gif|jpg|png|bmp|jpeg';
            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('user_image')) {
                $error = array('error' => $this->upload->display_errors());
                $this->session->set_flashdata('error', $error['error']);
            }
            $file = $this->upload->data();
            $image = $file['file_name'];

            $status = strtolower($data['status']) == 'active' ? '1' : '0'; 
            $time_accommodation = (isset($data['time_accommodation']) && $data['time_accommodation'] > 0) ? $data['time_accommodation'] : 0;
            // $is_admin = '0'; 
            $role = (isset($data['role']) && $data['role'] =='tutor') ? "tutor" : ($data['role'] == 'subadmin' ? 'subadmin' : "user"); //subadmin

            $course_id = (isset($data['course_id']) && $data['course_id']) ? $data['course_id'] : 0; //subadmin
            $institution_id = (isset($data['institution_id']) && $data['institution_id']) ? $data['institution_id'] : 0; //subadmin

            $is_admin =  $role == 'subadmin' ? "1" : "0";

            $sql = "
            INSERT INTO {$this->_db} (
            username,
            password,
            salt,
            first_name,
            last_name,
            email,
            image,
            language,
            is_admin,
            role,
            course_id,
            institution_id,
            status,
            time_accommodation,
            deleted,
            created,
            updated
            ) VALUES (
            " . $this->db->escape($data['username']) . ",
            " . $this->db->escape($password) . ",
            " . $this->db->escape($salt) . ",
            " . $this->db->escape($data['first_name']) . ",
            " . $this->db->escape($data['last_name']) . ",
            " . $this->db->escape($data['email']) . ",
            " . $this->db->escape($image) . ",
            " . $this->db->escape($this->config->item('language')) . ",
            " . $this->db->escape($is_admin) . ",
            " . $this->db->escape($role) . ",
            " . $this->db->escape($course_id) . ",
            " . $this->db->escape($institution_id) . ",
            " . $this->db->escape($status) . ",
            " . $this->db->escape($time_accommodation) . ",
            '0',
            '" . date('Y-m-d H:i:s') . "',
            '" . date('Y-m-d H:i:s') . "'
            )
            ";
            $this->db->query($sql);
            if ($id = $this->db->insert_id()) {
                return $id;
            }
        }
        return FALSE;
    }

    /**
     * User creates their own profile
     *
     * @param  array $data
     * @return mixed|boolean
     */
    function create_profile($data = array()) {
        
        if($data) 
        {
            // secure password and create validation code
            $salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
            $password = hash('sha512', $data['password'] . $salt);
            $validation_code = sha1(microtime(TRUE) . mt_rand(10000, 90000));
            $name = $_FILES['user_image']['name'];
            $profileimg = NULL;
            if($name)
            {
                $config['upload_path'] = "./assets/images/user_image";
                $config['allowed_types'] = 'gif|jpg|png|bmp|jpeg';
                $this->load->library('upload', $config);

                if(!$this->upload->do_upload('user_image')) 
                {
                    $error = array('error' => $this->upload->display_errors());
                    $this->session->set_flashdata('error', $error['error']);  
                }
                $file = $this->upload->data();
                $profileimg = $file['file_name'] ? $file['file_name'] : NULL;
            }

            $role = (isset($data['role']) && $data['role']) ? $data['role'] : 'user';
            $course_id = (isset($data['course_id']) && $data['course_id']) ? $data['course_id'] : 0;

            $institution_id = (isset($data['institution_id']) && $data['institution_id']) ? $data['institution_id'] : 0;

            $status = "0";
            if($this->settings->email_user_activation == 'NO')
            {
                $status = "1";
            }

            if($role == "tutor")
            {
                $status = "0";
            }


            $time_accommodation = 0;
            $user_request_for_tutor = (isset($data['tutor_request']) && $data['tutor_request'] == 1) ? 1 : 0;
            

            $sql = "
            INSERT INTO {$this->_db} (
            username,
            password,
            salt,
            first_name,
            last_name,
            email,
            image,
            time_accommodation,
            language,
            is_admin,
            role,
            course_id,
            institution_id,
            status,
            deleted,
            validation_code,
            created,
            updated,
            user_request_for_tutor
            ) VALUES (
            " . $this->db->escape($data['username']) . ",
            " . $this->db->escape($password) . ",
            " . $this->db->escape($salt) . ",
            " . $this->db->escape($data['first_name']) . ",
            " . $this->db->escape($data['last_name']) . ",
            " . $this->db->escape($data['email']) . ",
            " . $this->db->escape($profileimg) . ",
            " . $this->db->escape($time_accommodation) . ",
            " . $this->db->escape($data['language']) . ",
            '0',
            '" . $role . "',
            " . $course_id . ",
            " . $institution_id . ",
            '".$status."',
            '0',
            " . $this->db->escape($validation_code) . ",
            '" . date('Y-m-d H:i:s') . "',
            '" . date('Y-m-d H:i:s') . "',
            '".$user_request_for_tutor."'
            )
            ";
            $this->db->query($sql);
            $new_user_id = $this->db->insert_id();
            if($new_user_id) 
            {
                return array('validation_code' => $validation_code,'new_user_id' => $new_user_id);
            }
        }
        
        return FALSE;
    }

    /**
     * Edit an existing user
     *
     * @param  array $data
     * @return boolean
     */
    function edit_user($data = array()) {

        if ($data) {
            $sql = "
            UPDATE {$this->_db}
            SET
            username = " . $this->db->escape($data['username']) . ",
            ";
            if ($data['password'] != '') {
                // secure password
                $salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
                $password = hash('sha512', $data['password'] . $salt);
                $sql.= "
                password = " . $this->db->escape($password) . ",
                salt = " . $this->db->escape($salt) . ",
                ";
            }
            $selectImage = $this->db->where('id', $data['id'])->get('users')->row('image');
            $editimg = $selectImage;
            if ($_FILES['user_image']['name']) {
                if (!empty($selectImage)) {
                    $path = "./assets/images/user_image/$selectImage";
                    unlink($path);
                }
                
                $config['upload_path'] = "./assets/images/user_image";

                if(!is_dir($config['upload_path']))
                {
                    @mkdir($config['upload_path'], 0775, TRUE);
                }
    
                $config['allowed_types'] = 'jpg|png|bmp|jpeg';
                $this->load->library('upload', $config);


                if (!$this->upload->do_upload('user_image')) {
                    $error = array('error' => $this->upload->display_errors());
                    $this->session->set_flashdata('error', $error['error']);
                    
                }
                $file = $this->upload->data();
                $editimg = $file['file_name'];
            }
            

            $course_id = (isset($data['course_id']) && $data['course_id']) ?  $data['course_id'] : 0; 
            $institution_id = (isset($data['institution_id']) && $data['institution_id']) ?  $data['institution_id'] : 0; 


            // $role = (isset($data['role']) && $data['role'] == 'tutor') ? "tutor" : "user"; 
            $role = (isset($data['role']) && $data['role'] =='tutor') ? "tutor" : ((isset($data['role']) && $data['role'] == 'subadmin') ? 'subadmin' : "user"); //subadmin
             $is_admin =  $role == 'subadmin' ? "1" : "0";




            $user_last_record = $this->get_user($data['id']);
            if(isset($user_last_record['is_admin']) && $user_last_record['is_admin'] == 1)
            {
                $is_admin = "1";
                if($role !== 'subadmin')
                {
                   $role = "admin"; 
                }
            }


            $status = ($data['status'] == 'Active') ? '1' : '0';
            $time_accommodation = (isset($data['time_accommodation']) && $data['time_accommodation'] > 0) ? $data['time_accommodation'] : 0;
            
            $sql.= "
            first_name = " . $this->db->escape($data['first_name']) . ",
            last_name = " . $this->db->escape($data['last_name']) . ",
            email = " . $this->db->escape($data['email']) . ",
            image = " . $this->db->escape($editimg) . ",
            language = " . $this->db->escape($data['language']) . ",
            is_admin = " . $this->db->escape($is_admin) . ",
            role = " . $this->db->escape($role) . ",
            course_id = " . $this->db->escape($course_id) . ",
            institution_id = " . $this->db->escape($institution_id) . ",
            status = " . $this->db->escape($status).",
            time_accommodation = " . $this->db->escape($time_accommodation).",
            updated = '" . date('Y-m-d H:i:s') . "'
            WHERE id = " . $this->db->escape($data['id']) . "
            AND deleted = '0'
            ";
            
            $this->db->query($sql);
            
            if ($this->db->affected_rows()) 
            {
                $loged_in_user_id = $this->user['id'];
                if($data['id'] == $loged_in_user_id)
                {                    
                    $this->user = $this->UsersModel->get_user($loged_in_user_id);
                    unset($this->user['password']);
                    unset($this->user['salt']);
                    $this->session->set_userdata('logged_in', $this->user);
                    $this->session->language = $this->user['language'];
                    $this->lang->load('users', $this->user['language']);

                }
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * User edits their own profile
     *
     * @param  array $data
     * @param  int $user_id
     * @return boolean
     */
    function edit_profile($data = array(), $user_id = NULL) {
        
        if ($data && $user_id) {

            $name = $_FILES['user_image']['name'];
            
            $selectImage = $this->db->where('id', $user_id)->get('users')->row('image');
            
            $profileimg = $selectImage;
            if($name)
            {
                $config['upload_path'] = "./assets/images/user_image";
                $config['allowed_types'] = 'gif|jpg|png|bmp|jpeg';
                $this->load->library('upload', $config);
                
                if (!$this->upload->do_upload('user_image')) 
                {
                    $error = array('error' => $this->upload->display_errors());
                    $this->session->set_flashdata('error', $error['error']);
                }

                $file = $this->upload->data();
                $profileimg = $file['file_name'] ? $file['file_name'] : NULL;
            }



            $sql = "
            UPDATE {$this->_db}
            SET
            username = " . $this->db->escape($data['username']) . ",
            ";
            if ($data['password'] != '') {
                // secure password
                $salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
                $password = hash('sha512', $data['password'] . $salt);
                $sql.= "
                password = " . $this->db->escape($password) . ",
                salt = " . $this->db->escape($salt) . ",
                ";
            }
            
            // $status = $data['status']=='Active' ? 1 : 0; 
            $course_id = isset($data['course_id']) ? $data['course_id'] : 0;
            $institution_id = (isset($data['institution_id']) && $data['institution_id']) ? $data['institution_id'] : 0;


            // $is_admin = $data['is_admin']=='Admin' ? 1 :($data['is_admin']=='Authors' ? 2 : 0); 
            $sql.= "
            first_name = " . $this->db->escape($data['first_name']) . ",
            last_name = " . $this->db->escape($data['last_name']) . ",
            email = " . $this->db->escape($data['email']) . ",
            language = " . $this->db->escape($data['language']) . ",
            course_id = " . $this->db->escape($course_id) . ",
            institution_id = " . $this->db->escape($institution_id) . ",
            image = " . $this->db->escape($profileimg) . ",
            updated = '" . date('Y-m-d H:i:s') . "'
            WHERE id = " . $this->db->escape($user_id) . "
            AND deleted = '0'
            ";
            $this->db->query($sql);
            if ($this->db->affected_rows()) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Soft delete an existing user
     *
     * @param  int $id
     * @return boolean
     */
    function delete_user($id = NULL) {
        if ($id) 
        {
            $sql = "
            UPDATE {$this->_db}
            SET
            is_admin = '0',
            status = '0',
            deleted = '1',
            updated = '" . date('Y-m-d H:i:s') . "'
            WHERE id = " . $this->db->escape($id) . "
            AND id > 1
            ";
            $this->db->query($sql);
            if ($this->db->affected_rows()) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Check for valid login credentials
     *
     * @param  string $username
     * @param  string $password
     * @return array|boolean
     */
    function login($username = NULL, $password = NULL) {
        if ($username && $password) { 
            $sql = "
            SELECT
            id,
            username,
            password,
            salt,
            first_name,
            last_name,
            email,
            image,
            language,
            is_admin,
            status,
            time_accommodation,
            role,
            created,
            updated
            FROM {$this->_db}
            WHERE (username = " . $this->db->escape($username) . "
            OR email = " . $this->db->escape($username) . ")
            
            AND deleted = '0'
            LIMIT 1
            ";
            // AND status = '1'
            $query = $this->db->query($sql);

            if ($query->num_rows()) 
            {
                $results = $query->row_array();
                $salted_password = hash('sha512', $password . $results['salt']);
                if ($results['password'] == $salted_password) {
                    unset($results['password']);
                    unset($results['salt']);
                    if($results['status']==1)
                    { 
                        return $results; 
                    }
                    else
                    {
                        return 'not-active';
                    }
                }
            }
        }
        return FALSE;
    }


    function admin_login($username = NULL, $password = NULL) {
        if ($username && $password) { 
            $sql = "
            SELECT
            id,
            username,
            password,
            salt,
            first_name,
            last_name,
            email,
            image,
            language,
            is_admin,
            status,
            role,
            created,
            updated
            FROM {$this->_db}
            WHERE (username = " . $this->db->escape($username) . "
            OR email = " . $this->db->escape($username) . ")
            
            AND deleted = '0'
            LIMIT 1
            ";
            // AND status = '1'
            $query = $this->db->where('is_admin','1')->query($sql);

            if ($query->num_rows()) 
            {
                $results = $query->row_array();
                $salted_password = hash('sha512', $password . $results['salt']);
                if ($results['password'] == $salted_password) {
                    unset($results['password']);
                    unset($results['salt']);
                    if($results['status']==1)
                    { 
                        return $results; 
                    }
                    else
                    {
                        return 'not-active';
                    }
                }
            }
        }
        return FALSE;
    }



    function social_login($data) 
    {
        if ($data) 
        {
            $username_count = $this->db->where('username',$data['username'])->where('email !=',$data['email'])->from('users')->count_all_results();
            $u_name_count = $username_count > 0  ? "-".$username_count : '';
            $select_user = $this->db->where('email',$data['email'])->get('users')->row();
            if($select_user)
            {
                $id = $select_user->id;

                $username = $data['username'] != $select_user->username ? $data['username'].$u_name_count : $select_user->username;
                $data['username'] = $username;
                $data['status'] = "1";
                $data['updated'] = date('Y-m-d H:i:s');
                $this->db->where('id', $id)->update('users',$data);                

            }
            else
            {
                $username = $username_count > 0 ? $data['username'].'-'. $username_count : $data['username'];
                $data['username'] = $username;
                $data['created'] = date('Y-m-d H:i:s');
                $data['status'] = "1";

                $this->db->insert('users', $data);
                $id =  $this->db->insert_id();
            }
            
            $result = $this->db->select('id,username,first_name,last_name,email,image,language,is_admin,status,created,updated')->where('id',$id)->get('users')->row_array();
 
           
            if ($result) 
            {
                if($result['status'] == 1)
                { 
                    return $result; 
                }
                else
                {
                    return 'not-active';
                }
            }
        }
        return FALSE;
    }

    /**
     * Handle user login attempts
     *
     * @return boolean
     */
    function login_attempts() {
        // delete older attempts
        $older_time = date('Y-m-d H:i:s', strtotime('-' . $this->config->item('login_max_time') . ' seconds'));
        $sql = "
        DELETE FROM login_attempts
        WHERE attempt < '{$older_time}'
        ";
        $query = $this->db->query($sql);
        // insert the new attempt
        $sql = "
        INSERT INTO login_attempts (
        ip,
        attempt
        ) VALUES (
        " . $this->db->escape($_SERVER['REMOTE_ADDR']) . ",
        '" . date("Y-m-d H:i:s") . "'
        )
        ";
        $query = $this->db->query($sql);
        // get count of attempts from this IP
        $sql = "
        SELECT
        COUNT(*) AS attempts
        FROM login_attempts
        WHERE ip = " . $this->db->escape($_SERVER['REMOTE_ADDR']);
        $query = $this->db->query($sql);
        if ($query->num_rows()) {
            $results = $query->row_array();
            $login_attempts = $results['attempts'];
            if ($login_attempts > $this->config->item('login_max_attempts')) {
                // too many attempts
                return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * Validate a user-created account
     *
     * @param  string $encrypted_email
     * @param  string $validation_code
     * @return boolean
     */
    function validate_account($encrypted_email = NULL, $validation_code = NULL) {
        if ($encrypted_email && $validation_code) {
            $sql = "
            SELECT id
            FROM {$this->_db}
            WHERE SHA1(email) = " . $this->db->escape($encrypted_email) . "
            AND validation_code = " . $this->db->escape($validation_code) . "
            AND status = '0'
            AND deleted = '0'
            LIMIT 1
            ";
            $query = $this->db->query($sql);
            if ($query->num_rows()) {
                $results = $query->row_array();
                $sql = "
                UPDATE {$this->_db}
                SET status = '1',
                validation_code = NULL
                WHERE id = '" . $results['id'] . "'
                ";
                $this->db->query($sql);
                if ($this->db->affected_rows()) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

    /**
     * Reset password
     *
     * @param  array $data
     * @return mixed|boolean
     */
    function reset_password($data = array()) {
        if ($data) {
            $sql = "
            SELECT
            id,
            first_name
            FROM {$this->_db}
            WHERE email = " . $this->db->escape($data['email']) . "
            AND status = '1'
            AND deleted = '0'
            LIMIT 1
            ";
            $query = $this->db->query($sql);
            if ($query->num_rows()) {
                // get user info
                $user = $query->row_array();
                // create new random password
                $user_data['new_password'] = generate_random_password();
                $user_data['first_name'] = $user['first_name'];
                // create new salt and stored password
                $salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
                $password = hash('sha512', $user_data['new_password'] . $salt);
                $sql = "
                UPDATE {$this->_db} SET
                password = " . $this->db->escape($password) . ",
                salt = " . $this->db->escape($salt) . "
                WHERE id = " . $this->db->escape($user['id']) . "
                ";
                $this->db->query($sql);
                if ($this->db->affected_rows()) {
                    return $user_data;
                }
            }
        }
        return FALSE;
    }

    function reset_password_by_token($email) 
    {
        return $this->db->where('email',$email)->get('users')->row();
    }

    /**
     * Check to see if a username already exists
     *
     * @param  string $username
     * @return boolean
     */
    function username_exists($username) {
        $sql = "
        SELECT id
        FROM {$this->_db}
        WHERE username = " . $this->db->escape($username) . "
        LIMIT 1
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Check to see if an email already exists
     *
     * @param  string $email
     * @return boolean
     */
    function email_exists($email) {
        $sql = "
        SELECT id
        FROM {$this->_db}
        WHERE email = " . $this->db->escape($email) . "
        LIMIT 1
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()) {
            return TRUE;
        }
        return FALSE;
    }

    function fav_product_data($user_id) {
        $this->db->select('product.id as product_id, product_title,  product_category_id, product_brand_id, product_meta_keyword,product_description, product_meta_description, product_short_detail, product_full_detail, product_lowest_marcket, product_slug, category_slug, product_varient.id as product_varient_id,  product_varient.sku as product_sku, product_image, category_slug,favourite_products.id as is_fav');
        $this->db->join('category', 'category.id = product.product_category_id', 'left');
        $this->db->join('product_varient', 'product_varient.product_id = product.id', 'left');
        $this->db->join('product_variant_market', 'product_variant_market.product_variant_id = product_varient.id', 'left');
        $this->db->join('brand', 'brand.id = product.product_brand_id', 'left');
        $this->db->join('market', 'market.id = product_variant_market.market_id', 'left');
        $this->db->join('favourite_products', 'favourite_products.products_id = product.id', 'left');
        $this->db->join('product_variant_custom_field_values', 'product_variant_custom_field_values.product_variant_id = product_varient.id', 'left');
        $this->db->where('is_primary', 1);
        $this->db->where('favourite_products.user_id', $user_id);
        $this->db->group_by('favourite_products.products_id');
        return $this->db->get('product')->result();
    }

    function remove_from_fav_product($user_id, $product_id) 
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('products_id', $product_id);
        $this->db->delete('favourite_products');
        return $this->db->affected_rows();
    }

    function check_is_valid_user($token)
    {
        return $this->db->where('token',$token)->get('users')->row();
    }

    function update_user_token_by_email($email,$data)
    {
        $this->db->set($data)->where('email', $email)->update('users');
        return $this->db->affected_rows();
    }

    function get_liked_quiz_by_userid($user_id)
    {  
        return $this->db->select('quiz_like.quiz_id,quiz_like.user_id as loggedin_user_id,quizes.*,
            (SELECT count(id) FROM quiz_count WHERE quiz_count.quiz_id = quizes.id)as total_view,
            (SELECT id FROM quiz_like WHERE quiz_like.user_id = "'.$user_id.'" LIMIT 1) as like_id,
            (SELECT count(id) FROM quiz_like WHERE quiz_like.quiz_id = quizes.id)as total_like,
            (SELECT first_name FROM users WHERE id='.$user_id.'  limit 1)as first_name,
            (SELECT last_name FROM users WHERE id='.$user_id.'  limit 1)as last_name,
            (select count(id) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = "quiz") as rating,
            (select SUM(rating) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = "quiz") as total_rating,
            (SELECT id FROM payments where quiz_id = quizes.id AND user_id = '.$user_id.' AND payment_status = "succeeded") as payment_id

            ')

        ->join('users','users.id = quiz_like.user_id','left')
        ->join('quizes','quizes.id = quiz_like.quiz_id','left')
        ->where('is_quiz_active',1)
        ->where('quiz_like.user_id',$user_id)
        ->get('quiz_like')
        ->result();
       
    }

    function get_question_count_by_quiz_id($quiz_id)
    {
        $result = $this->db->select('count(*) as count')->where('quiz_id',$quiz_id)->get('questions')->row();
        return $result->count ? $result->count : 0;
    }

    function get_purchase_quiz_by_userid($user_id)
    {
       return $this->db->select('payments.quiz_id,payments.user_id as loggedin_user_id,quizes.*,(SELECT count(id) FROM quiz_count WHERE quiz_count.quiz_id = quizes.id )as total_view,(SELECT id FROM quiz_like WHERE quiz_like.user_id = "'.$user_id.'" LIMIT 1) as like_id,(SELECT count(id) FROM quiz_like WHERE quiz_like.quiz_id = quizes.id)as total_like,(SELECT first_name FROM users WHERE id='.$user_id.')as first_name,(SELECT last_name FROM users WHERE id='.$user_id.')as last_name,(select count(id) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = "quiz") as rating,(select SUM(rating) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = "quiz") as total_rating')
        ->join('users','users.id = payments.user_id','left')
        ->join('quizes','quizes.id = payments.quiz_id','left')
        ->where('is_quiz_active',1)
        ->where('payments.user_id',$user_id)
        ->where('payment_status','succeeded')
        ->get('payments')
        ->result(); 
    }

    function get_like_post_by_loggedin_user($user_id)
    {
        $this->db->select('post_title,post_slug,post_image,first_name,last_name,title,(SELECT count(id) FROM post_like WHERE post_id = blog_post.id) as total_like,(SELECT count(id) FROM post_count WHERE post_id = blog_post.id) as view_total_post,(SELECT count(id) FROM post_like WHERE post_id = blog_post.id AND post_like.user_id = '.$user_id.') as is_like ');
        $this->db->join('users','users.id = post_like.user_id','left');
        $this->db->join('blog_post','blog_post.id = post_like.post_id','left');
        $this->db->join('blog_category','blog_category.id = blog_post.blog_category_id','left');
        $this->db->where('post_like.user_id',$user_id);
        return $this->db->get('post_like')->result();
    }

    function get_payment_status_by_loggedin_user($user_id)
    {

        $query = $this->db->select('purchases_type,payments.id,payments.item_price,item_name,payments.payment_status,first_name,last_name,payment_gateway,item_price_currency')
        ->join('users','users.id = payments.user_id','left')
        ->where('user_id',$user_id)
        ->order_by('payments.id', 'desc')
        ->get('payments');
        
        return $query->result();   
    }

    function get_payment_detail_by_id($id)
    {  
        return $this->db->select('email,txn_id,item_name,item_price,payment_status,created,payment_gateway,token_no,item_price_currency,invoice_no,(select first_name from users where id = payments.user_id)as first_name,(select last_name from users where id = payments.user_id)as last_name,(select description from quizes where id = payments.quiz_id)as description')
            ->where('id', $id)->get('payments')->row();
            
    }

    var $table = 'participants';
    var $column_order = array('quizes.title', 'participants.questions', 'participants.total_attemp','participants.correct','participants.user_id','participants.started', NULL);
    var $column_search = array('quizes.title', 'participants.questions');
    var $order = array('participants.id' => 'DESC');

    private function _get_datatables_query() {
        $this->db->from($this->table);
        $this->db->join("quizes", "quizes.id = participants.quiz_id", "LEFT");
        $this->db->where('is_quiz_active',1);
        $i = 0;
        foreach ($this->column_search as $item) {
            // if datatable send POST for search
            if ($_POST['search']['value']) {

                // first loop
                if ($i === 0) {

                    // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->group_start();

                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                // last loop
                if (count($this->column_search) - 1 == $i) {
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
        // here order processing
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->column_order)) {
            $order = $this->column_order;
            $this->db->order_by(key($order), $order[key($order) ]);
        }
    }

    function count_filtered($user_id) 
    {
        $this->_get_datatables_query();
        $query = $this->db->where('participants.user_id',$user_id)->get();
        return $query->num_rows();
    }

    function count_all($user_id) {
        $this->db->from($this->table)->where('participants.user_id', $user_id);
        return $this->db->count_all_results();
    }

    function get_quiz_history($user_id) {
        $this->_get_datatables_query();
        if ($_POST['length'] != - 1) 
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->select('participants.*,quizes.id as quiz_id, quizes.title as quiz_title,duration_min')
        ->where('participants.user_id',$user_id)
        ->order_by('participants.id', 'asc')
        ->get();
        
        return $query->result();
    }

    function get_user_by_id($user_id) 
    {
        return $this->db->where('id',$user_id)->get('users')->row();
    }


    function get_participant_by_id($participant_id) 
    {
        return $this->db->where('id',$participant_id)->get('participants')->row_array();
    }

    function get_user_question_by_participant_id($participant_id)
    {
        return $this->db->select('user_questions.*,(select solution from questions where questions.id = user_questions.question_id) as solution, (select solution_image from questions where questions.id = user_questions.question_id) as solution_image')->where('participant_id',$participant_id)->order_by('question_id','asc')->get('user_questions')->result_array();
        
    }

    function get_quiz_by_id($quiz_id) 
    {
        return $this->db->where('deleted','0')
        ->where('id',$quiz_id)
        ->order_by('id','asc')
        ->where('is_quiz_active',1)
        ->get('quizes')->row();
    }

    function get_login_user_membership($user_id)
    {
        return $this->db->select('validity')->where('user_id',$user_id)->order_by('validity','desc')->get('user_membership_payment')->row_array();
    }

    function update_otp_data($user_id,$data)
    {
        $this->db->where('id',$user_id)->update('users',$data);
        return $this->db->affected_rows();
    }

    /**
     * Check to see if an mobile already exists
     *
     * @param  string $mobile
     * @return boolean
     */
    function mobile_exists($mobile) {
        return $this->db->select('users.*')->where('phone_no',$mobile)->get('users')->row();
    }

    function insert_otp_detail($data)
    {
        
        $this->db->insert('users', $data);
        
        return $this->db->insert_id();
    }

    function get_user_token_by_id($login_id)
    {
        return $this->db->where('id',$login_id)
                        ->get('users')
                        ->row_array(); 
    }

    function get_custom_field_by_id($user_id)
    {
        return $this->db->select('value,field_label')->join('custom_fields','custom_fields.field_name = custom_field_values.field_name','left')
                    ->where('rel_id',$user_id)->get('custom_field_values')->result_array();
    }
}
