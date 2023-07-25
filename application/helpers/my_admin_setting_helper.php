<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Overwriting the timezones function to include Arizona timezone
 */
if (!function_exists('get_admin_setting')) {
    function get_admin_setting($field_name) {
        $ci = & get_instance();
        $ci->load->database();
        $menu_setting = $ci->AdminSettingModel->get_admin_setting_by_field_name($field_name);
        if (isset($menu_setting->value)) {
            return $menu_setting->value;
        } else {
            return false;
        }
    }
}


if (!function_exists('old_get_currency_symbol')) {
    function old_get_currency_symbol($currency_symbol) {
        $locale = 'en-US'; //browser or user locale
        $currency = $currency_symbol;
        $fmt = new NumberFormatter($locale . "@currency=$currency", NumberFormatter::CURRENCY);
        $symbol = $fmt->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
        header("Content-Type: text/html; charset=UTF-8;");
        return $symbol;
    }
}

if (!function_exists('get_currency_symbol')) 
{
    function get_currency_symbol($currency_symbol) 
    {
         $currency_symbol = $currency_symbol ? $currency_symbol : "USD";
        $ci = & get_instance();
        $ci->load->database();
        $curency_data = $ci->db->where('code',$currency_symbol)->get('currency')->row();

        if(empty($curency_data))
        {
            $curency_data = $ci->db->where('code',"USD")->get('currency')->row();
        }

        return $curency_data->symbol;
    }
}


if (!function_exists('get_currency_symbol_from_db')) {
    function get_currency_symbol_from_db($currency_symbol) 
    {
        $currency_symbol = $currency_symbol ? $currency_symbol : "USD";
        $ci = & get_instance();
        $ci->load->database();
        $curency_data = $ci->db->where('code',$currency_symbol)->get('currency')->row();

        if(empty($curency_data))
        {
            $curency_data = $ci->db->where('code',"USD")->get('currency')->row();
        }

        return $curency_data->symbol;
    }
}

if (!function_exists('get_user_category_membership')) {
    function get_user_category_membership($user_id,$category_id) 
    {
       if($category_id)
       {
             $ci = & get_instance();
            $ci->load->database();
            return $ci->db->select('validity')->where('category_id',$category_id)->where('user_id',$user_id)->order_by('purchased','desc')->get('user_membership_payment')->row();
       }
       else
       {
        return false;
       }
       

    }
}
