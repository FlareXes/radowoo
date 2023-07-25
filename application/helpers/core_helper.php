<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Outputs an array in a user-readable JSON format
 *
 * @param array $array
 */
if (!function_exists('display_json')) {
    function display_json($array) {
        $data = json_indent($array);
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');
        echo xss_clean($data);
    }
}
/**
 * Convert an array to a user-readable JSON string
 *
 * @param array $array - The original array to convert to JSON
 * @return string - Friendly formatted JSON string
 */
if (!function_exists('json_indent')) {
    function json_indent($array = array()) {
        // make sure array is provided
        if (empty($array)) return NULL;
        // Encode the string
        $json = json_encode($array);
        $result = '';
        $pos = 0;
        $str_len = strlen($json);
        $indent_str = '  ';
        $new_line = "\n";
        $prev_char = '';
        $out_of_quotes = true;
        for ($i = 0;$i <= $str_len;$i++) {
            // grab the next character in the string
            $char = substr($json, $i, 1);
            // are we inside a quoted string?
            if ($char == '"' && $prev_char != '\\') {
                $out_of_quotes = !$out_of_quotes;
            }
            // if this character is the end of an element, output a new line and indent the next line
            elseif (($char == '}' OR $char == ']') && $out_of_quotes) {
                $result.= $new_line;
                $pos--;
                for ($j = 0;$j < $pos;$j++) {
                    $result.= $indent_str;
                }
            }
            // add the character to the result string
            $result.= $char;
            // if the last character was the beginning of an element, output a new line and indent the next line
            if (($char == ',' OR $char == '{' OR $char == '[') && $out_of_quotes) {
                $result.= $new_line;
                if ($char == '{' OR $char == '[') {
                    $pos++;
                }
                for ($j = 0;$j < $pos;$j++) {
                    $result.= $indent_str;
                }
            }
            $prev_char = $char;
        }
        // return result
        return $result . $new_line;
    }
}
/**
 * Save data to a CSV file
 *
 * @param array $array
 * @param string $filename
 * @return bool
 */
if (!function_exists('array_to_csv')) {
    function array_to_csv($array = array(), $filename = "export.csv") {
        $CI = get_instance();
        // disable the profiler otherwise header errors will occur
        $CI->output->enable_profiler(FALSE);
        if (!empty($array)) {
            // ensure proper file extension is used
            if (!substr(strrchr($filename, '.csv'), 1)) {
                $filename.= '.csv';
            }
            try {
                // set the headers for file download
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
                header("Cache-Control: no-cache, must-revalidate");
                header("Pragma: no-cache");
                header("Content-type: text/csv");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename={$filename}");
                $output = @fopen('php://output', 'w');
                // used to determine header row
                $header_displayed = FALSE;
                foreach ($array as $row) {
                    if (!$header_displayed) {
                        // use the array keys as the header row
                        fputcsv($output, array_keys($row));
                        $header_displayed = TRUE;
                    }
                    // clean the data
                    $allowed = '/[^a-zA-Z0-9_ @%\|\[\]\.\(\)%&-]/s';
                    foreach ($row as $key => $value) {
                        $row[$key] = preg_replace($allowed, '', $value);
                    }
                    // insert the data
                    fputcsv($output, $row);
                }
                fclose($output);
            }
            catch(Exception $e) {
            }
        }
        exit;
    }
}

/**
 * Generates a random password
 *
 * @return string
 */
if (!function_exists('generate_random_password')) {
    function generate_random_password() {
        $characters = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array();
        $alpha_length = strlen($characters) - 1;
        for ($i = 0;$i < 8;$i++) {
            $n = rand(0, $alpha_length);
            $pass[] = $characters[$n];
        }
        return implode($pass);
    }
}

/**
 * Retrieves list of language folders
 *
 * @return array
 */
if (!function_exists('get_languages')) {
    function get_languages() 
    {
        $CI = get_instance();

        $CI->load->model('languageModel');
        $get_languages_name = $CI->languageModel->get_languages_name();
        $languages = array();

        foreach ($get_languages_name as $lang_array) 
        {
            $languages[$lang_array->lang] = $lang_array->lang;
        }
        if(empty($languages))
        {
            $languages['English'] = 'English';
        }

        $CI->session->languages = $languages;
        return $languages;
    }
}

/**
 * Sort a multi-dimensional array
 *
 * @param array $arr - the array to sort
 * @param string $col - the key to base the sorting on
 * @param string $dir - SORT_ASC or SORT_DESC
 */
if (!function_exists('array_sort_by_column')) {
    function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
        $sort_col = array();
        foreach ($arr as $key => $row) {
            $sort_col[$key] = $row[$col];
        }
        array_multisort($sort_col, $dir, $arr);
    }
}

if (!function_exists('action_not_permitted')) {
    function action_not_permitted() {
        return true; die;
        $ci = & get_instance();
        $ci->session->set_flashdata('error', "This action is not permitted.");
        redirect_back();
    }
}

if ( ! function_exists('redirect_back'))
{
    function redirect_back()
    {
        if (isset($_SERVER['HTTP_REFERER']))
        {
            header('Location: '.$_SERVER['HTTP_REFERER']);
        }
        else
        {
            header('Location: http://'.$_SERVER['SERVER_NAME']);
        }
    }
}


if (!function_exists('slugify_string')) 
{
    function slugify_string($text) 
    {
        return url_slug($text);
        exit;
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // lowercase
        $text = strtolower($text);
        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }
}

if (!function_exists('url_slug')) 
{
    function url_slug($str, $options = array()) 
    {
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
        
        $defaults = array(
            'delimiter' => '-',
            'limit' => null,
            'lowercase' => true,
            'replacements' => array(),
            'transliterate' => false,
        );
        
        // Merge options
        $options = array_merge($defaults, $options);
        
        $char_map = array(
            // Latin
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C', 
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 
            'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O', 
            'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH', 
            'ß' => 'ss', 
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c', 
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 
            'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o', 
            'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th', 
            'ÿ' => 'y',

            // Latin symbols
            '©' => '(c)',

            // Greek
            'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
            'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
            'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
            'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
            'Ϋ' => 'Y',
            'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
            'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
            'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
            'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
            'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',

            // Turkish
            'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
            'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g', 

            // Russian
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
            'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya',

            // Ukrainian
            'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
            'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',

            // Czech
            'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U', 
            'Ž' => 'Z', 
            'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
            'ž' => 'z', 

            // Polish
            'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z', 
            'Ż' => 'Z', 
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
            'ż' => 'z',

            // Latvian
            'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 
            'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
            'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
            'š' => 's', 'ū' => 'u', 'ž' => 'z'
        );
        
        // Make custom replacements
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
        
        // Transliterate characters to ASCII
        if ($options['transliterate']) {
            $str = str_replace(array_keys($char_map), $char_map, $str);
        }
        
        // Replace non-alphanumeric characters with our delimiter
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
        
        // Remove duplicate delimiters
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
        
        // Truncate slug to max. characters
        $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
        
        // Remove delimiter from ends
        $str = trim($str, $options['delimiter']);
        
        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
    }
}


if (!function_exists('xss_clean')) {
    function xss_clean($data)
    {
        // Fix &entity\n;
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do
        {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        }
        while ($old_data !== $data);

        // we are done...
        return $data;
    }
}

function get_user_review_like($review_id = false)
{

    $ci = & get_instance();
    $ci->load->database();
    $ci->load->model('HomeModel');
    if($ci->session->userdata('logged_in'))
    {
        $review_value = $ci->HomeModel->get_review_like_user_wise($ci->session->userdata('logged_in')['id'],$review_id);
        return count($review_value);    
    }
}


function get_translated_column_value($lang_id = NULL, $table = NULL, $table_foreign_id = NULL, $column = NULL)
{
    $ci = & get_instance();
    $ci->load->database();
    $ci->load->model('HomeModel');
    $data = $ci->HomeModel->get_translated_value($lang_id, $table, $table_foreign_id, $column);
    if($data)
    {
        return $data->value;
    }
    else
    {
        return null;
    }

   
}

function get_language_data_by_language($language)
{
    $ci = & get_instance();
    $ci->load->database();
    $ci->load->model('HomeModel');
    $data = $ci->HomeModel->get_language_data($language);
    if($data)
    {
        return $data->id;
    }
    else
    {
        return 1;
    }
}

function encrypt_decrypt($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'asweefdkhdsvbkdjsahflksa32432oqwru423rifu@3r4';
    $secret_iv = 'This is my secret iv';
    // hash
    $key = hash('sha256', $secret_key);

    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } 
    else if( $action == 'decrypt' ) {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
        return $output;
}


/**
 * Function that renders input for admin area based on passed arguments
 * @param  string $name             input name
 * @param  string $label            label name
 * @param  string $value            default value
 * @param  string $type             input type eq text,number
 * @param  array  $input_attrs      attributes on <input
 * @param  array  $form_group_attr  <div class="form-group"> html attributes
 * @param  string $form_group_class additional form group class
 * @param  string $input_class      additional class on input
 * @return string
 */


function render_input($name, $label = '', $value = '', $type = 'text', $input_class = '', $input_attrs = [], $form_group_attr= [], $form_group_class = '')
{

    $input            = '';
    $_form_group_attr = '';
    $_input_attrs     = '';
    foreach ($input_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = $val;
        }
        $_input_attrs .= $key . '=' . '"' . $val . '" ';
    }

    $_input_attrs = rtrim($_input_attrs);

    $form_group_attr['app-field-wrapper'] = $name;

    foreach ($form_group_attr as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val =$val;
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
    }

    $_form_group_attr = rtrim($_form_group_attr);

    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    if (!empty($input_class)) {
        $input_class = ' ' . $input_class;
    }
    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $input .= '<label for="' . $name . '" class="control-label">' . $label . '</label>';
    }
    $input .= '<input type="' . $type . '" id="' . $name . '" name="'. $name.'" class="form-control' . $input_class . '" ' . $_input_attrs . ' value="' . set_value($name, $value) . '">';

    $input .= '<span class="small text-danger form-error">'. strip_tags(form_error($name)).' </span>';

    $input .= '</div>';

    return $input;
}






/**
 * Render textarea for admin area
 * @param  [type] $name             textarea name
 * @param  string $label            textarea label
 * @param  string $value            default value
 * @param  array  $textarea_attrs      textarea attributes
 * @param  array  $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string $form_group_class form group div wrapper additional class
 * @param  string $textarea_class      <textarea> additional class
 * @return string
 */
function render_textarea($name, $label = '', $value = '', $textarea_attrs = [], $form_group_attr = [], $form_group_class = '', $textarea_class = '')
{
    $textarea         = '';
    $_form_group_attr = '';
    $_textarea_attrs  = '';
    if (!isset($textarea_attrs['rows'])) {
        $textarea_attrs['rows'] = 4;
    }

    if (isset($textarea_attrs['class'])) {
        $textarea_class .= ' ' . $textarea_attrs['class'];
        unset($textarea_attrs['class']);
    }

    foreach ($textarea_attrs as $key => $val) {
        // tooltips
        if ($key == 'title') {
            $val = $val;
        }
        $_textarea_attrs .= $key . '=' . '"' . $val . '" ';
    }

    $_textarea_attrs = rtrim($_textarea_attrs);

    $form_group_attr['app-field-wrapper'] = $name;

    foreach ($form_group_attr as $key => $val) {
        if ($key == 'title') {
            $val = $val;
        }
        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
    }

    $_form_group_attr = rtrim($_form_group_attr);

    if (!empty($textarea_class)) {
        $textarea_class = trim($textarea_class);
        $textarea_class = ' ' . $textarea_class;
    }
    if (!empty($form_group_class)) {
        $form_group_class = ' ' . $form_group_class;
    }
    $textarea .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
    if ($label != '') {
        $textarea .= '<label for="' . $name . '" class="control-label">' . $label . '</label>';
    }

    $v = clear_textarea_breaks($value);
    if (strpos($textarea_class, 'tinymce') !== false) {
        $v = $value;
    }
    $textarea .= '<textarea id="' . $name . '" name="'. $name.'" class="form-control' . $textarea_class . '" ' . $_textarea_attrs . '>' . set_value($name, $v) . '</textarea>';

    $textarea .= '<span class="small text-danger form-error">'. strip_tags(form_error($name)).' </span>';

    $textarea .= '</div>';

    return $textarea;
}



function clear_textarea_breaks($text, $replace = '')
{
    $_text = '';
    $_text = $text;

    $breaks = [
        '<br />',
        '<br>',
        '<br/>',
    ];

    $_text = str_ireplace($breaks, $replace, $_text);
    $_text = trim($_text);

    return $_text;
}




function render_select($name, $label, $options, $selected = '', $classes = '', $include_blank = true)
{


    if (!empty($classes)) {
        $classes = ' ' . $classes;
    }

    $select = '<div class="form-group">';
    if ($label != '') {
        $select .= '<label for="' . $name . '" class="control-label">' .$label. '</label>';
    }
    $select .= '<select id="' . $name . '" name="'. $name.'" class="form-control' . $classes . '">';
    if ($include_blank == true) 
    {
        $select .= '<option value="">Select One</option>';
    }

    foreach ($options as $option) 
    {
        $_selected = $option == $selected ? 'selected' : '';
        $val = trim($option);       
        $select .= '<option value="' . $val . '"' . $_selected . '>' . $val . '</option>';
    }
    $select .= '</select>';

    $select .= '<span class="small text-danger form-error">'. strip_tags(form_error($name)).'</span>';

    $select .= '</div>';

    return $select;
}




function render_checkbox($name, $label, $options, $selected = [], $classes = '')
{
    ob_start(); ?>
    <div class="form-group mb-0">
        <label for="<?php echo $option_value; ?>" class="control-label ">
            <?php echo $label; ?>
        </label>
    </div>
    <div class="form-group">
        <?php
        $i = 0; 
        foreach ($options as  $option) 
        { $i++;
            $checked = in_array($option, $selected) ? 'checked' : ''; // TRUE
            ?>

            <div class="custom-control custom-checkbox custom-control-inline">
              <input <?php echo $checked; ?> type="checkbox" class="custom-control-input" id="<?php echo $name.'_'.$i; ?>" name="<?php echo $name; ?>[<?php echo $i; ?>]">
              <label class="custom-control-label" for="<?php echo $name.'_'.$i; ?>"><?php echo $option; ?></label>
            </div>

            <?php
        }
        ?>
    </div>
    <?php
    $settings = ob_get_contents();
    ob_end_clean();
    echo $settings;
}


function get_category_stars($category_id,$user_id)
{
    $ci = & get_instance();
    $ci->load->database();
    $ci->load->model('HomeModel');
    $ci->load->model('TestModel');
    // get all quiz eit cat_id = $category_id
    $quiz_data = $ci->HomeModel->get_category_quizes($category_id);

    $star = ['d_l_1'=> false, 'd_l_2' => false, 'd_l_3' => false];
    $user_star = ['d_l_1'=> false, 'd_l_2' => false, 'd_l_3' => false];
    $total_level = ['first'=> 0, 'second' => 0, 'third' => 0];
    $passed_level = ['first'=> 0, 'second' => 0, 'third' => 0];

    foreach($quiz_data as $quiz_data_array)
    {  
        if($quiz_data_array->difficulty_level == 1)
        { 
            $star['d_l_1'] = true;
            $total_level['first']++;

            $get_quiz_result = $ci->TestModel->is_quiz_already_given_or_pass($quiz_data_array->id,$user_id);
            
            if($get_quiz_result)
            {
                // $user_star['d_l_1'] = true;
                $passed_level['first']++;
            }
        }

        if($quiz_data_array->difficulty_level == 2)
        {
            $star['d_l_2'] = true;
            $total_level['second']++;
            $get_quiz_result = $ci->TestModel->is_quiz_already_given_or_pass($quiz_data_array->id,$user_id);
            //p($get_quiz_result);
            if($get_quiz_result)
            {
                // $user_star['d_l_2'] = true;
                $passed_level['second']++;
            }  
        }

        if($quiz_data_array->difficulty_level == 3)
        {
            $star['d_l_3'] = true;
            $total_level['third']++;
            $get_quiz_result = $ci->TestModel->is_quiz_already_given_or_pass($quiz_data_array->id,$user_id);
            //p($get_quiz_result);
            if($get_quiz_result)
            {
                // $user_star['d_l_3'] = true;
                $passed_level['third']++;
            }
        }
    }


    if ($star["d_l_1"] && $star["d_l_2"] && $star["d_l_3"]) 
    {
        if($total_level['first'] > 0 && $total_level['first'] == $passed_level['first'])
        {
            $user_star['d_l_1'] = true;
        }
        if($total_level['second'] > 0 && $total_level['second'] == $passed_level['second'])
        {
            $user_star['d_l_2'] = true;
        }
        if($total_level['third'] > 0 && $total_level['third'] == $passed_level['third'])
        {
            $user_star['d_l_3'] = true;
        }

       return $user_star; 
    }
    else
    {
       return false; 
    }

}


function get_user_earn_points()
{

    $ci = & get_instance();
    $ci->load->database();
    if($ci->session->userdata('logged_in'))
    {
        $earned_points = $ci->db->select('SUM(earned_points) as earned_points')->where('user_id',$ci->session->userdata('logged_in')['id'])->get('participants')->row();
        return $earned_points->earned_points;    
    }
    return 0;
}

function get_user_level()
{

    $ci = & get_instance();
    $ci->load->database();
    if($ci->session->userdata('logged_in'))
    {
        $earned_points = $ci->db->select('SUM(earned_points) as earned_points')->where('user_id',$ci->session->userdata('logged_in')['id'])->get('participants')->row();

        $total_earned_points = $earned_points->earned_points > 0 ? $earned_points->earned_points : 0;
        $levels = $ci->db->where("min_points <= $total_earned_points")->order_by('min_points','DESC')->limit(1)->get('levels')->row();
        if(empty($levels) OR empty($levels->title))
        {
            return "NO-LEVEL";
        }

        return $levels->title;

    }
    return "NO-LEVEL";
}

function get_user_level_by_id($user_id)
{

    $ci = & get_instance();
    $ci->load->database();

    $response['last_level'] = FALSE;
    $response['user_level'] = [];
    $response['user_next_level'] = [];

    if($ci->session->userdata('logged_in'))
    {
        $earned_points = $ci->db->select('SUM(earned_points) as earned_points')->where('user_id',$user_id)->get('participants')->row();

        $total_earned_points = $earned_points->earned_points > 0 ? $earned_points->earned_points : 0;
        $levels = $ci->db->where("min_points <= $total_earned_points")->order_by('min_points','DESC')->limit(1)->get('levels')->row();
        if(empty($levels) OR empty($levels->title))
        {
            $levels_order = 0;
        }
        else
        {
            $levels_order = $levels->level_order;   
        }

        $next_levels_order = $levels_order + 1;
        $next_levels_data = $ci->db->where("level_order",$next_levels_order)->get('levels')->row();
        if(empty($next_levels_data))
        {
            $check_last_levels_data = $ci->db->order_by('level_order','DESC')->limit(1)->get('levels')->row();
            
            if($check_last_levels_data->level_order == $levels_order)
            {
                $response['last_level'] = TRUE;
            }
            $next_levels_data = $check_last_levels_data;
        }

        $response['user_level'] = $levels;
        $response['user_next_level'] = $next_levels_data;

    }
    return $response;
}

function get_date_formate($date)
{
    if(empty($date))
    {
        $date = date("d-m-Y");
    }

    $ci = & get_instance();
    $ci->load->database();
    $db_data = $ci->db->where('name','date_formate')->get('settings')->row();
    if ($db_data) 
    {
        return date($db_data->value,strtotime($date));
    }
    return $date; 
}


    function get_date_or_time_formate($date_time)
    {
        if(empty($date_time))
        {
            $date_time = date("d-m-Y H:i:s");
        }

        $ci = & get_instance();
        $ci->load->database();
        $db_date_data = $ci->db->where('name','date_formate')->get('settings')->row();
        $db_time_data = $ci->db->where('name','date_or_time_formate')->get('settings')->row();
        
        if ($db_date_data && $db_time_data) 
        {
            return date($db_date_data->value." ".$db_time_data->value,strtotime($date_time));
        }
        return $date_time; 
    }


    function get_email_template($slug)
    {
        $ci = & get_instance();
        $ci->load->database();

        $get_template = $ci->db->select('email_template.*')->where('slug',$slug)->get('email_template')->row();
        return $get_template;   
    }


    if (!function_exists('excute_sql_query')) 
    {
        function excute_sql_query() 
        {
            $CI = get_instance();
            $CI->load->helper('directory');
            $sql_directories = FCPATH . '/assets/themes/db.sql';
            // die($sql_directories);
            if (!is_file($sql_directories))
            {
                return TRUE;
            }
            $sql = file_get_contents($sql_directories);

            $now = date("Y-m-d H:i:s");

            $sql = str_replace('update_data_added', $now, $sql);

                try
                {
                    $sql = file_get_contents($sql_directories);
                    $sqls = explode(';---END_OF_QUERY-FOR_QUIZZY-AUTO_UPDATES---;', $sql);
                    array_pop($sqls);

                    $CI->db->trans_start();
                    foreach($sqls as $statement){
                        $statment = $statement . ";";
                        $CI->db->query($statement);
                    }

                    $CI->db->trans_complete(); 

                    $message = "success";
                }
                catch(Exception $e)
                {
                    $message = $e->getMessage();
                }
                unlink($sql_directories);

                return TRUE;
        }
    }



    function do_recaptcha_validation($str = '')
    {
        if(empty($str))
        {
            return FALSE;
        }
        $CI = & get_instance();
        $CI->load->library('form_validation');
        $google_url = 'https://www.google.com/recaptcha/api/siteverify';
        $secret     = $CI->settings->recaptcha_secret_key;
        $ip         = $CI->input->ip_address();
        $url        = $google_url . '?secret=' . $secret . '&response=' . $str . '&remoteip=' . $ip;
        $curl       = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        $res = curl_exec($curl);
        curl_close($curl);
        $temp_res = $res;
        $res = json_decode($res, true);
        //reCaptcha success check
        if (isset($res['success']) && $res['success']) 
        {
            return true;
        }

        $recaptcha_error = isset($res['error-codes'][0]) ? $res['error-codes'][0] : "Recaptcha Error";
        $CI->form_validation->set_message('recaptcha', $recaptcha_error);

        return FALSE;
    }



    if (!function_exists('excute_sql_json_query')) 
    {
        function excute_sql_json_query() 
        {
            $CI = get_instance();
            $CI->load->helper('directory');
            $sql_directories = FCPATH . 'assets/themes/db.json';

            if (!is_file($sql_directories))
            {
                return TRUE;
            }
            
            $now = date("Y-m-d H:i:s");

            try
            {
                $sql_json = file_get_contents($sql_directories);
                $sql_json = str_replace('update_data_added', $now, $sql_json);
                $sql_array = json_decode($sql_json);
                $sql_array = json_decode(json_encode($sql_array), true);
                // P($sql_array);
                if($sql_array && is_array($sql_array))
                {
                    $CI->db->trans_start();
                    foreach($sql_array as $query_data)
                    {
                        if($query_data && is_array($query_data))
                        {
                            if($query_data['query_type'] == 'alter_table')
                            {
                                $alter_table_data = $CI->db->list_fields($query_data['table_name']);
                                if($alter_table_data)
                                {
                                    $q_column_name = $query_data['column_name'];

                                    $is_column_exist = (in_array($q_column_name,$alter_table_data)) ? TRUE : FALSE;

                                    if($is_column_exist == TRUE && $query_data['alter_action'] == "drop")
                                    {
                                        $CI->db->query($query_data['query']);
                                    }
                                    if($is_column_exist == FALSE && $query_data['alter_action'] == "add")
                                    {
                                        $CI->db->query($query_data['query']);
                                    }
                                }
                            }
                            else if($query_data['query_type'] == 'insert' && $query_data['table_name'] =='settings' && $query_data['alter_action']=='check_it' && $query_data['column_name'])
                            {
                                $all_settings_data = $CI->db->get('settings')->result_array();
                                $all_settings_key = array_column($all_settings_data,"name");
                                
                                $is_exist_setting = in_array($query_data['column_name'], $all_settings_key);
                                if(!$is_exist_setting)
                                {
                                   $CI->db->query($query_data['query']); 
                                }
                            }
                            else
                            {
                                $CI->db->query($query_data['query']);
                            }
                        }                        
                    }
                    $CI->db->trans_complete(); 
                }

                
            }
            catch(Exception $e)
            {
                $message = $e->getMessage();
            }
            unlink($sql_directories);
            new_update_applyes();

            return TRUE;
        }
    }


    if (!function_exists('new_update_applyes')) 
    {
        function new_update_applyes() 
        {   
			return false;
            $CI = get_instance();

            $update_info_value  = get_setting_value_by_name('update_info');
            $update_info_json = $update_info_value ? $update_info_value : json_encode(array());
            $update_info_obj = json_decode($update_info_json);
            $update_info_array = json_decode(json_encode($update_info_obj), true);

            $status = false;

            if($update_info_array && is_array($update_info_array))
            {
                $up_info_site_update_token = trim($update_info_array['purchase_code']);
                $is_verified = $update_info_array['is_verified'];

                if($up_info_site_update_token && $is_verified) {

                    $api_url = "https://projects.ishalabs.com/updates/api/new_update_applied.php?purchase_token=$up_info_site_update_token&project_slug=quiz";  
                
                    $api_response_json = get_web_page($api_url);
                    $api_response = json_decode($api_response_json);
    
                    $new_version_code = $update_info_array['current_version_code'] + 1;
                    $update_info_array['current_version_code'] = $new_version_code;
                    $update_info_array['current_version_name'] = $update_info_array['next_version_name'];
                    $update_info_array['next_version_name'] = "";
                    $update_info_array['next_version_description'] = "";
                    $update_info_array['next_version_all_data'] = "[]";
                    $update_info_array['next_version_zip_urls'] = "[]";
                    $update_info_array['next_version_all_in_one_zip'] = "";
                    $update_info_array['last_updated'] = date("Y-m-d H:i:s");
                    $update_info_array['message'] = "You are on Latest Version";
                    $update_info_array['updated'] = date("Y-m-d H:i:s");
    
                    $setting_update_info['value'] = json_encode($update_info_array);
                    $status = $CI->db->where('name','update_info')->update('settings',$setting_update_info);    
                }

            }
            else
            {
                $update_info_array['current_version_code'] = 1;
                $update_info_array['current_version_name'] = "4.0.0";
                $update_info_array['purchase_code'] = "";
                $update_info_array['purchase_code_updated'] = FALSE;
                $update_info_array['is_verified'] = FALSE;
                
                $update_info_array['next_version_name'] = "";
                $update_info_array['next_version_description'] = "";
                $update_info_array['next_version_all_data'] = "[]";
                $update_info_array['next_version_zip_urls'] = "[]";
                $update_info_array['next_version_all_in_one_zip'] = "";
                $update_info_array['last_updated'] = date("Y-m-d H:i:s");
                $update_info_array['message'] = "Plz verfiy";
                
                $update_info_array['added'] = date("Y-m-d H:i:s");
                $update_info_array['updated'] = date("Y-m-d H:i:s");
                   
            }

            if($status)
            {
                return TRUE;
            }
            else
            {
               return FALSE;
            }
        }
    }
    

    if (!function_exists('get_setting_value_by_name')) 
    {
        function get_setting_value_by_name($field_name) 
        {   
            $CI = get_instance();
            $update_info_obj = $CI->db->where('name',$field_name)->get('settings')->row('value');
            return $update_info_obj;
        }
    }


    if (!function_exists('get_web_page')) 
    {
        function get_web_page($url) 
        {
            try
            {

                $options = array(
                    CURLOPT_RETURNTRANSFER => true,   // return web page
                    CURLOPT_HEADER         => false,  // don't return headers
                    CURLOPT_FOLLOWLOCATION => true,   // follow redirects
                    CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
                    CURLOPT_ENCODING       => "",     // handle compressed
                    // CURLOPT_USERAGENT      => $_SERVER['HTTP_HOST'], // name of client
                    CURLOPT_USERAGENT      => base_url(), // name of client
                    CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
                    CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
                    CURLOPT_TIMEOUT        => 120,    // time-out on response
                    CURLOPT_REFERER        => base_url(),    // 'https://m.facebook.com/', 
                );  

                $ch = curl_init($url);
                curl_setopt_array($ch, $options);

                $content  = curl_exec($ch);

                curl_close($ch);

                return $content;
            }
            catch(Exception $e) 
            {
                return json_encode(array());
            }
        }
    }


    function get_parents_categoryies($id)
    {
        $ci = & get_instance();
        $ci->load->database();
        $category_data = $ci->db->where('id', $id)->get('category')->row();
        if(empty($category_data))
        {
            return array();
        }

        $ci->category_array[] = ['category_name' => $category_data->category_title, 
                                'category_slug' => $category_data->category_slug, 
                                'category_pid' => $category_data->parent_category, 
                                'category_id' => $id];


        if($category_data->parent_category && $category_data->parent_category > 0)
        {
            get_parents_categoryies($category_data->parent_category, $ci->category_array);
        } 
        else 
        {
            return $ci->category_array;
        }

    }

    function get_parent_category_with_comma($category_id, $seprator = false)
    {
        $seprator = $seprator ? $seprator : ', ';

        $ci = & get_instance();
        $ci->load->database();

        $ci->category_array = [];
        get_parents_categoryies($category_id);
        $category_data = array_reverse($ci->category_array);
        
        $cat_name = "";
        if($category_data)
        {
            $i =0;
            // $url = "<i class='fas fa fa-hand-point-right mx-1'></i>";
            foreach ($category_data as $category_arr) 
            {
                $i++;
                $next = $i > 1 ? $seprator : "";
               
                $cat_name .= $next.ucfirst($category_arr['category_name']);
            }
        }
        return $cat_name;
    }



    function get_parent_category_url_list($category_id)
    {
        $ci = & get_instance();
        $ci->load->database();

        $ci->category_array = [];
        get_parents_categoryies($category_id);
        $category_data = array_reverse($ci->category_array);
        
        $url = "";
        if($category_data)
        {
            $i =1;
            // $url = "<i class='fas fa fa-hand-point-right mx-1'></i>";
            foreach ($category_data as $category_arr) 
            {
                $next = $i > 1 ? " > " : "";
                $i++;
                $cat_url = base_url("category/".$category_arr['category_slug']);
                $url .= $next."<a class='list_parent_category' href='".$cat_url."'>".$category_arr['category_name']."</a>";
            }
        }
        return $url;
    }


    function get_quiz_breadcrumbs($quiz_id)
    {
        if(empty($quiz_id))
        {
            return false;
        }
        $ci = & get_instance();
        $ci->load->database();


        $quizes = $ci->db->where('id',$quiz_id)->get('quizes')->row();
        if(empty($quizes))
        {
            return false;
        }
        $quize_name = $quizes->title;

        $ci->category_array = [];
        get_parents_categoryies($quizes->category_id);
        $category_data = array_reverse($ci->category_array);
        $url = "";
        if($category_data)
        {
            $url = "<i class='fas fa fa-hand-point-right mr-3'></i>";
            foreach ($category_data as $category_arr) 
            {
                $cat_url = base_url("category/".$category_arr['category_slug']);
                $url .= "<a href='".$cat_url."'>".$category_arr['category_name']." </a> > ";
            }
            $quiz_url = base_url("quiz-detail/quiz/").$quiz_id;
            $url .= "<a href='".$quiz_url."'>".$quize_name." </a>";
        }
        return $url;

    }



    function generatePageTree($datas, $parent = 0, $depth = 0, $seprator = false)
    {
        $seprator = ($seprator) ? $seprator : ' &nbsp; <i class="fas fa-caret-right"></i> &nbsp; ';
        
        $ci = & get_instance();
        $ci->load->database();

        $ni=count($datas);
        if($ni === 0 || $depth > 1000) return ''; 
        // Make sure not to have an endless recursion
        $tree = '';
        for($i=0; $i < $ni; $i++){
            if($datas[$i]['parent_category'] == $parent) 
            {
                // $tree .= str_repeat('&nbsp;&nbsp;', $depth);
                $depth_seprator = str_repeat($seprator, $depth);
                $tree .= $depth.'|~CB~|'.$datas[$i]['id'].'|~CB~|'.$depth_seprator.' '.$datas[$i]['category_title'].'|~CB~|'.$datas[$i]['category_slug'].'|~CB~|'.$datas[$i]['category_icon'].'|~CB~|'.$datas[$i]['category_image'].'|~CB~|'.$datas[$i]['category_status'].'||~LB~||';
                $tree .= generatePageTree($datas, $datas[$i]['id'], $depth+1, $seprator);
            }
        }
        return $tree;
    }


    function get_study_section_contant($study_material_id, $section_id)
    {
        $ci = & get_instance();
        $ci->load->database();
        return $ci->db->where('study_material_id',$study_material_id)->where('section_id',$section_id)->order_by('material_order','asc')->get('study_material_content')->result();
    }    


    function get_user_completed_s_m_section_contant($study_material_id,$section_id,$user_id)
    {
        $ci = & get_instance();
        $ci->load->database();


        $resut_dta =  $ci->db->where('s_m_id',$study_material_id)
        ->where('user_id',$user_id)
        ->where('s_m_section_id',$section_id)
        ->order_by('id','asc')->get('study_material_user_history')->result_array();
        $s_m_section_completed_ids = array();
        if($resut_dta)
        {
             $s_m_section_completed_ids = array_column($resut_dta,"s_m_contant_id");
        }
        return $s_m_section_completed_ids;


    }

    function convertBytes($value) 
    {
        if ( is_numeric( $value ) ) {
            return $value;
        } else {
            $value_length = strlen($value);
            $qty = substr( $value, 0, $value_length - 1 );
            $unit = strtolower( substr( $value, $value_length - 1 ) );
            switch ( $unit ) {
                case 'k':
                    $qty *= 1024;
                    break;
                case 'm':
                    $qty *= 1048576;
                    break;
                case 'g':
                    $qty *= 1073741824;
                    break;
            }
            return $qty;
        }
    }



    function get_categories_tree()
    {
        $ci = & get_instance();
        $ci->load->database();
    
        $categories = $ci->db->select('*, (SELECT COUNT(id) FROM category WHERE parent_category = C.id) childs')->where('category_status', 1)->order_by('C.order','asc')->get('category AS C')->result_array();
        return show_categories($categories);
    }

    function show_categories($categories, $parent_id = 0, $char = '')
    {
        $cate_child = array();
        foreach ($categories as $key => $item)
        {
            if ($item['parent_category'] == $parent_id)
            {
                $cate_child[] = $item;
                unset($categories[$key]);
            }
        }

        if ($cate_child)
        {
            if($parent_id) { echo '<ul class="dl-submenu">'; } else { echo '<ul class="dl-menu">'; }

            foreach ($cate_child as $key => $item)
            {
                echo '<li>';

                if($item['childs']) {
                    echo '<a href="#">'.$item['category_title'].'<i class="fas fa-long-arrow-alt-right float-right"></i></a>';
                } 
                echo '<span style="" onclick="javascript:location.href=\''.base_url('category/'.$item['category_slug']).'\'" > '.$item['category_title'].'</span>';

                show_categories($categories, $item['id'], $char.'|---');
                echo '</li>';
            }
            echo '</ul>';
        }
    }


if (!function_exists('get_front_menu_order')) 
{
    function get_front_menu_order()
    {
        $ci = get_instance();
        return $ci->db->where('status',1)->order_by('order')->get('front_menu_items')->result();
    }
}

if (!function_exists('get_quiz_detail_page_url_by_id')) 
{
    function get_quiz_detail_page_url_by_id($quiz_id)
    {
        $ci = get_instance();
        $quiz_data =  $ci->db->where('id',$quiz_id)->get('quizes')->row();
        $quiz_slug_url = base_url('quiz/').$quiz_id;
        if($quiz_data)
        {
             $quiz_slug_url = base_url('quiz/').slugify_string($quiz_data->title)."-$quiz_id";
        }
        return $quiz_slug_url;
         
    }
}

if (!function_exists('get_study_material_detail_page_url_by_id')) 
{
    function get_study_material_detail_page_url_by_id($tab_study_material_id)
    {

        $sm_slug_url = "#";
        if($tab_study_material_id)
        {
            $ci = get_instance();
            $sm_data =  $ci->db->where('id',$tab_study_material_id)->get('study_material')->row();
            $sm_slug_url = base_url('study-content/').$tab_study_material_id;
            if($sm_data)
            {
                 $sm_slug_url = base_url('study-content/').slugify_string($sm_data->title)."-$tab_study_material_id";
            }
        }
        
        return $sm_slug_url;
    }
}

if (!function_exists('get_study_material_user_progress')) 
{
    function get_study_material_user_progress($study_material_id)
    {
        $ci = get_instance();
        $ci->load->model('StudyModel');
        $user_id = (isset($ci->user['id']) && $ci->user['id']) ? $ci->user['id'] : 0;
        $s_m_completed_data = $ci->StudyModel->get_user_completed_s_m_contant($study_material_id,$user_id);
        $s_m_completed_content_ids = array();
        if($s_m_completed_data && count($s_m_completed_data))
        {
            $s_m_completed_content_ids = array_column($s_m_completed_data,"s_m_contant_id");
        }
        return count($s_m_completed_content_ids);
    }
}



if (!function_exists('get_advertisment_by_position')) 
{
    function get_advertisment_by_position($position) 
    {   

        $ci = get_instance();

        $ads_display_as = $ci->settings->ads_display_as;

        $result_obj = $ci->db->where('position',$position)->where('status',1);
        if($ads_display_as == "ALL")
        {
            $result_obj = $result_obj->order_by('ad_order','asc');
        }
        else
        {
            $result_obj = $result_obj->order_by('rand()')->limit(1);
        }

        $result = $result_obj->get('advertisment')->result();
        return $result;
    }
}


if (!function_exists('get_google_advertisments')) 
{
    function get_google_advertisments()
    {
        $ci = get_instance();
        $user_id = (isset($ci->user['id']) && $ci->user['id']) ? $ci->user['id'] : 0;
        return $ci->db->where('is_goole_adsense',1)->where('status',1)->get('advertisment')->result();
    }
}

if (!function_exists('get_icon_by_name')) 
{
    function get_icon_by_name($icon_name)
    {
        if($icon_name == 'docs')
        {
            return '<i class="far fa-file-word"></i>';
        }
        else if($icon_name == 'video')
        {
            return '<i class="fas fa-video"></i>';
        }
        else if($icon_name == 'audio')
        {
            return '<i class="fa fa-volume-up"> </i>';
        }
        else if($icon_name == 'images')
        {
            return '<i class="far fa-file-image"></i>';
        }
        else if($icon_name == 'pdf')
        {
            return '<i class="far fa-file-pdf"></i>';
        }
        else if($icon_name == 'other')
        {
            return '<i class="far fa-file-alt"></i>';
        }
        else
        {
            return '<i class="far fa-file-alt"></i>';
        }
    }
}

if (!function_exists('get_study_material_box_contant_count_by_icon')) 
{
    function get_study_material_box_contant_count_by_icon($icon_name, $study_data)
    {
        if($icon_name == 'docs')
        {
            return $study_data->total_doc;
        }
        else if($icon_name == 'video')
        {
           return $study_data->total_video;
        }
        else if($icon_name == 'audio')
        {
            return $study_data->total_audio;
        }
        else if($icon_name == 'images')
        {
            return $study_data->total_images;
        }
        else if($icon_name == 'pdf')
        {
            return $study_data->total_pdf;
        }
        else if($icon_name == 'other')
        {
            return $study_data->total_other;
        }
        else
        {
            return $study_data->total_file;
        }
    }
}


if (!function_exists('is_loged_in_user_is_subadmin')) 
{
    function is_loged_in_user_is_subadmin()
    {
        $ci = get_instance();
        $user_id = (isset($ci->user['id']) && $ci->user['id']) ? $ci->user['id'] : NULL;

        if(empty($user_id))
        {
            return redirect(base_url());
        }

        $is_subadmin = (isset($ci->user['role']) && $ci->user['role'] == 'subadmin') ? TRUE : FALSE;
        
        return $is_subadmin;
    }
}


if (!function_exists('get_user_to_assign_new')) 
{
    function get_user_to_assign_new($user_id)
    {
        $ci = get_instance();
        $users_obj = $ci->db->where('id !=',$user_id)->get('users')->result(); 
        $new_users_array = array();
        $new_users_array[] = lang('select_user_user_to_assign_contant');
        if($users_obj)
        {
            foreach ($users_obj as $key => $user_obj) 
            {
                if($user_obj->role == "tutor" OR $user_obj->is_admin == 1)
                {
                    $new_users_array[$user_obj->id] = $user_obj->first_name." ".$user_obj->last_name;   
                }
            }
        }       
        return $new_users_array;
    }
}

