<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Extended Language Class with fallback to English
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Language
 */
class MY_Lang extends CI_Lang {
    /**
     * List of translations
     *
     * @var array
     */
    public $language = array();
    /**
     * List of loaded language files
     *
     * @var array
     */
    public $is_loaded = array();
    public $last_lang = NULL;
    /**
     * Class constructor
     *
     * @return  void
     */
    function __construct() {
        parent::__construct();
        log_message('info', 'Language Class Initialized');
    }
    // --------------------------------------------------------------------
    
    /**
     * Load a language file  - fallback to English if file is missing
     *
     * @param   mixed   $langfile   Language file name
     * @param   string  $idiom      Language name (english, etc.)
     * @param   bool    $return     Whether to return the loaded array of translations
     * @param   bool    $add_suffix Whether to add suffix to $langfile
     * @param   string  $alt_path   Alternative path to look for the language file
     *
     * @return  void|string[]   Array containing translations, if $return is set to TRUE
     */
    function load($langfile, $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '') 
    {   
        if($this->language)
        {
            return true;
        }

        $ci = &get_instance();
        $ci->load->library("session");
        

        $default_language = $ci->db->where('name','default_site_language')->get('settings')->row();

        $default_lang = isset($default_language->field_output_value) ? $default_language->field_output_value : 'english'; 

        $idiom = (isset($ci->session->language) && $ci->session->language) ? strtolower($ci->session->language) : $default_lang;
        


        if(!count($this->language))
        {
            $database_lang =  $this->_get_from_db($idiom);
            if ($database_lang)
            {
                $lang = $database_lang;
                $found = TRUE;
            }
        }

        if(empty($lang) && $found == FALSE) 
        {
            log_message('error', 'Language file contains no data FOR language : ' . $idiom);
            if ($return === TRUE) 
            {
                return array();
            }
            return;
        }

        if ($return === TRUE) 
        {
            return $lang;
        }

        $this->is_loaded[$idiom] = $idiom;

        $this->language = array_merge($this->language, $lang);
        log_message('info', 'Language file loaded: language FOR : ' . $idiom );
        return TRUE;
    }
    // --------------------------------------------------------------------
    
    /**
     * Language line
     *
     * Fetches a single line of text from the language array
     *
     * @param   string  $line       Language line key
     * @param   bool    $log_errors Whether to log an error message if the line is not found
     * @return  string  Translation
     */
    function line($line, $log_errors = TRUE) 
    {
        

        $value = isset($this->language[$line]) ? $this->language[$line] : FALSE;
        // Because killer robots like unicorns!
        if ($value === FALSE && $log_errors === TRUE) {
            log_message('error', 'Could not find the language line "' . $line . '"');
        }
        return $value;
    }

    private function _get_from_db($lang_name = NULL)
    {
        $CI =& get_instance();
        $CI->load->database();

        $CI->db->select('lang_token.*');
        $CI->db->join('language', 'language.id = lang_token.language_id');
        $CI->db->where('language.lang', $lang_name);

        $query = $CI->db->get('lang_token')->result();

        $return = array();
        foreach ( $query as $row )
        {
            $return[$row->token] = $row->description;
        }
        return $return;
    }

}
