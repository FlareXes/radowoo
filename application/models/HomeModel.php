<?php defined('BASEPATH') OR exit('No direct script access allowed');
class HomeModel extends CI_Model {

    function get_category() {
        return $this->db->where('parent_category',0)->where('category_status',1)->where('category_is_delete',0)->order_by('category.order','asc')->get('category')->result();
    }




    function get_all_categories() 
    {
        return $this->db->where('category_is_delete',0)->where('category_status',1)->order_by('category.order','asc')->get('category')->result();
    }



    function get_testmonials() {
        
        return $this->db->get('testimonial')->result();
        
    }

    function get_sponsers() {
        return $this->db->order_by('name','asc')->get('sponsors')->result();
    }

    function get_category_by_slug($category_slug) {
        return $this->db->where('category_slug', $category_slug)->where('category_status',1)->where('category_is_delete',0)->order_by('category.order','asc')->get('category')->row();
    }

    function get_latest_quiz($limit=4, $order='difficulty_level',$order_type ="asc")
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        
        return $this->db->select("quizes.*,
            (select count(id) from questions where questions.quiz_id = quizes.id limit 1) as total_question, 
            (select first_name from users where users.id = quizes.user_id  limit 1) as first_name , 
            (select last_name from users where users.id = quizes.user_id  limit 1) as last_name, 
            (SELECT count(id) FROM quiz_count where quiz_id = quizes.id  limit 1) as total_view,
            (SELECT id FROM quiz_like where quiz_id = quizes.id AND user_id = '".$user_id."'  limit 1) as like_id,
            (SELECT count(id) FROM quiz_like where quiz_id = quizes.id  limit 1) as total_like,
            (select count(id) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'quiz'."'  limit 1) as rating,
            (select SUM(rating) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'quiz'."'  limit 1) as total_rating")
        //->where('quizes.number_questions <= (select count(id) from questions where questions.quiz_id = quizes.id)')
        ->where(time().' BETWEEN start_date_time AND end_date_time')
        ->where('is_quiz_active',1)
        ->order_by($order,$order_type)
        ->limit($limit)
        ->get('quizes')
        ->result(); 
    }

    function get_upcoming_quiz()
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        $current_date_time = date('Y-m-d h:i:s a');
        $current_date_time = strtotime($current_date_time);
        $new_current_time = $current_date_time + ($this->settings->display_countdown_before_starting_quiz * 60 * 60);
        //p(date('Y-m-d h:i:s a',$new_current_time));

        return $this->db->select("quizes.*,
            (select count(id) from questions where questions.quiz_id = quizes.id) as total_question, 
            (select first_name from users where users.id = quizes.user_id) as first_name , 
            (select last_name from users where users.id = quizes.user_id) as last_name, 
            (SELECT count(id) FROM quiz_count where quiz_id = quizes.id) as total_view,
            (SELECT id FROM quiz_like where quiz_id = quizes.id AND user_id = '".$user_id."') as like_id,
            (SELECT count(id) FROM quiz_like where quiz_id = quizes.id) as total_like,
            (select count(id) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'quiz'."') as rating,
            (select SUM(rating) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'quiz'."') as total_rating")
        //->where('quizes.number_questions <= (select count(id) from questions where questions.quiz_id = quizes.id)')
        ->where('quizes.start_date_time !=',0)
        ->where('quizes.end_date_time !=',33168837600)
        ->where('quizes.start_date_time <=',$new_current_time)
        ->where('quizes.start_date_time >=',$current_date_time)
        ->where('is_quiz_active',1)
        //->order_by($order,$order_type)
        ->limit(4)
        ->get('quizes')
        ->result(); 
    }

    function get_quiz_by_category($category_id,$sub_category,$rating,$price)
    {
        $this->db->select("quizes.id, quizes.number_questions, (SELECT COUNT(id) FROM questions WHERE questions.quiz_id = quizes.id) questions");
        $this->db->join('category', 'category.id = quizes.category_id');

        $this->db->where('category.id', $category_id);
        $this->db->where('is_quiz_active',1);
        //->where('quizes.number_questions <= (select count(id) from questions where questions.quiz_id = quizes.id)')
        $this->db->where(time().' BETWEEN start_date_time AND end_date_time');
    
        $this->db->order_by('quizes.added', 'desc');
        return $this->db->get('quizes')->result();
    }

    function get_quiz_by_category_other($category_id,$sub_category,$rating,$price,$ids)
    {
        $this->db->select("quizes.id, quizes.number_questions, (SELECT COUNT(id) FROM questions WHERE questions.quiz_id = quizes.id) questions");
        $this->db->join('category', 'category.id = quizes.category_id');
        $this->db->join('quiz_reviews', 'quiz_reviews.rel_id = quizes.id','left');
        

        $this->db->where_in('category.id', $ids);
        $this->db->where('is_quiz_active',1);
        //->where('quizes.number_questions <= (select count(id) from questions where questions.quiz_id = quizes.id)')
        $this->db->where(time().' BETWEEN start_date_time AND end_date_time');
        if ($sub_category) 
        {
            $this->db->where_in('category.category_title', $sub_category);
        }
        
        if ($rating) 
        {
            $this->db->where('rel_type =','quiz');
            $this->db->where('quiz_reviews.rating', $rating);
        }

        if ($price == 'free') 
        {
            $this->db->where('quizes.price <', 1);
        }

        if ($price == 'paid') 
        {
            $this->db->where('quizes.price >', 0);
        }

        $this->db->order_by('quizes.added', 'desc');
        return $this->db->get('quizes')->result();
    }


    function get_category_quiz_per_page($category_id, $pro_per_page, $page, $filter_by,$sub_category,$rating,$price,$ids)
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        $this->db->select("quizes.*,(SELECT count(id) FROM quiz_count where quiz_id = quizes.id) as total_view,
            (SELECT id FROM quiz_like where quiz_id = quizes.id AND user_id = '".$user_id."' limit 1)as like_id,
            (SELECT count(id) FROM quiz_like where quiz_id = quizes.id) as total_like, 
            (select first_name from users where users.id = quizes.user_id  limit 1) as first_name , 
            (select last_name from users where users.id = quizes.user_id  limit 1) as last_name, 
            (SELECT count(id) FROM quiz_count where quiz_id = quizes.id ) as total_view,
            (select count(id) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'quiz'."') as rating,
            (select SUM(rating) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'quiz'."') as total_rating");
        $this->db->join('category', 'category.id = quizes.category_id');
        $this->db->join('quiz_reviews', 'quiz_reviews.rel_id = quizes.id','left');
        $this->db->where_in('category.id', $ids);
        $this->db->where('is_quiz_active',1);
        $this->db->where(time().' BETWEEN start_date_time AND end_date_time');

        if ($sub_category) 
        {
            $this->db->where_in('category.category_title', $sub_category);
        }

        if ($rating) 
        {
            $this->db->where('rel_type =','quiz');
            $this->db->where('quiz_reviews.rating >=', $rating);
        }

        if ($price == 'free' ) 
        {
            $this->db->where('quizes.price <', 1 );
        }

        if ($price == 'paid') 
        {
            $this->db->where('quizes.price >', 0);
        }
        
        $this->db->limit($pro_per_page, $page);
        $this->db->order_by($filter_by, "asc");
        return $this->db->get('quizes')->result(); 
        
    }

    function get_category_quiz_per_page_another($category_id, $pro_per_page, $page, $filter_by)
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        $this->db->select("quizes.*,(SELECT count(id) FROM quiz_count where quiz_id = quizes.id) as total_view,
            (SELECT id FROM quiz_like where quiz_id = quizes.id AND user_id = '".$user_id."' limit 1)as like_id,
            (SELECT count(id) FROM quiz_like where quiz_id = quizes.id) as total_like, 
            (select first_name from users where users.id = quizes.user_id  limit 1) as first_name , 
            (select last_name from users where users.id = quizes.user_id  limit 1) as last_name, 
            (SELECT count(id) FROM quiz_count where quiz_id = quizes.id ) as total_view,
            (select count(id) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'quiz'."') as rating,
            (select SUM(rating) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'quiz'."') as total_rating");
        $this->db->join('category', 'category.id = quizes.category_id');
        
        //->where('quizes.number_questions <= (select count(id) from questions where questions.quiz_id = quizes.id)')
        $this->db->where('category.id', $category_id);
        $this->db->where('is_quiz_active',1);
        $this->db->where(time().' BETWEEN start_date_time AND end_date_time');
        $this->db->limit($pro_per_page, $page);
        $this->db->order_by($filter_by, "asc");
        return $this->db->get('quizes')->result(); 
        
    }    


    function insert_quiz_like($quiz_like_data)
    {
        $this->db->insert('quiz_like',$quiz_like_data);
        return $this->db->insert_id();
    }
    function delete_like_quiz_through_quizid($quiz_id,$user_id)
    {
        $this->db->where('quiz_id',$quiz_id);
        $this->db->where('user_id',$user_id);
        $this->db->delete('quiz_like');
        return $this->db->affected_rows();
    }
    function get_count_likes_through_quiz_id($quiz_id)
    {
        $this->db->select('count(id) as total_like');
        $this->db->where('quiz_id',$quiz_id);
        return $this->db->get('quiz_like')->row();
    }

    function get_quiz_by_id($quiz_id) 
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        return $this->db->select('quizes.*,(SELECT id FROM quiz_like where quiz_id = quizes.id AND user_id = '.$user_id.') as like_id,
            (SELECT id FROM payments where quiz_id = quizes.id AND user_id = '.$user_id.' AND payment_status = "succeeded" limit 1) as payment_id,
            (select count(id) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =  1 AND quiz_reviews.rel_type = "quiz") as rating,
            (select SUM(rating) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status = 1 AND quiz_reviews.rel_type = "quiz") as total_rating,
            (select count(id) from quiz_count where quiz_count.quiz_id = quizes.id ) as total_view,
            (select count(id) from quiz_like where quiz_like.quiz_id = quizes.id ) as total_like
            ') 

        ->where('deleted','0')
        ->where('id',$quiz_id)
        ->order_by('id','asc')
        ->where('is_quiz_active',1)
        ->get('quizes')->row();
        
    }

    function insert_rating_data($review_data)
    {
        $this->db->insert('quiz_reviews',$review_data);
        $insert_id = $this->db->insert_id();

        return  $insert_id;   
    }

    function get_comment_through_quizid_userid_reltype($rel_type,$rel_id,$user_id)
    {

        $this->db->select('id');
        $this->db->where('rel_type',$rel_type);
        $this->db->where('rel_id',$rel_id);
        $this->db->where('user_id',$user_id);
        $result = $this->db->get('quiz_reviews');
        return $result->num_rows();
    }

    function get_quiz_comment($rel_id,$rel_type)
    {
        $this->db->select('quiz_reviews.*,users.first_name,users.last_name,users.image');
        $this->db->join('users', 'users.id = quiz_reviews.user_id', 'left');
        $this->db->where('rel_id',$rel_id);
        $this->db->where('rel_type',$rel_type);
        $this->db->where('quiz_reviews.status',1);
        $this->db->order_by('id','DESC');
        $this->db->limit(9);
        return $this->db->get('quiz_reviews')->result();

    }

    function get_review_like_user_wise($user_id,$review_id)
    {
        $this->db->select('review_likes.*');
        $this->db->where('review_id',$review_id);
        $this->db->where('user_id',$user_id);
        return $this->db->get('review_likes')->result();    
    }

    function insert_review_like($review_like)
    {
        $this->db->insert('review_likes',$review_like);
        return $this->db->insert_id();
    }

    function get_count_likes_through_review_id($review_id,$rel_type)
    {
        $this->db->select('count(id) as total_like');
        $this->db->where('review_id',$review_id);
        $this->db->where('rel_type',$rel_type);
        return $this->db->get('review_likes')->row();
    }

    function delete_review_like_through_reviewid_rel_type($review_id,$user_id,$rel_type)
    {
        $this->db->where('review_id',$review_id)->where('user_id',$user_id)->where('rel_type',$rel_type)->delete('review_likes');
        return $this->db->affected_rows();
    }

    function get_translated_value($lang_id, $table, $table_foreign_id, $column)
    {
        return $this->db->where('lang_id',$lang_id)
                        ->where('table',$table)
                        ->where('forigen_table_id',$table_foreign_id)
                        ->where('column',$column)
                        // ->where_in('column',$column)
                        ->get('translations')->row();
    }

    function get_language_data($language)
    {
        return $this->db->where('lang',$language)->get('language')->row();
    }

    function get_sub_category_data($parent_category_id)
    {
        return $this->db->select('*')->where('parent_category',$parent_category_id)->where('category_status',1)->where('category_is_delete',0)->order_by('category.order','asc')->get('category')->result();
    }

    function get_latest_study_material($limit=4, $order='added')
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        return $this->db->select("study_material.*,
            (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'pdf') as total_pdf, 
            (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND (type = 'video' OR type = 'vimeo-embed-code' OR type = 'youtube-embed-code')) as total_video, 
            (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'audio') as total_audio, 
            (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id) as total_file, 
            (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'doc') as total_doc, 
            (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'image') as total_images,
            (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'content') as total_other,  

            (select CONCAT(first_name, last_name) from users where users.id = study_material.user_id) as full_name, 
            (SELECT id FROM study_material_like where study_material_id = study_material.id AND user_id = '".$user_id."') as like_id,
            (SELECT count(id) FROM study_material_like where study_material_id = study_material.id) as total_like, 
            (SELECT count(id) FROM study_material_view where study_material_id = study_material.id) as total_view, 
            (select count(type) from study_material_content where study_material_content.study_material_id = study_material.id) as type,
            (select count(id) from quiz_reviews where quiz_reviews.rel_id = study_material.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'material'."') as rating,
            (select SUM(rating) from quiz_reviews where quiz_reviews.rel_id = study_material.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'material'."') as total_rating")
        ->where('study_material.status',1)
        ->order_by($order, 'desc')
        ->limit($limit)
        ->get('study_material')
        ->result();        
    }

    function get_category_study_material($category_id)
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        return $this->db->select("study_material.*,
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'pdf') as total_pdf, 
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND (type = 'video' OR type = 'vimeo-embed-code' OR type = 'youtube-embed-code')) as total_video, 
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'audio') as total_audio, 
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id) as total_file, 
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'doc') as total_doc, 
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'image') as total_images,
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'content') as total_other,  
                (select CONCAT(first_name, last_name) from users where users.id = study_material.user_id) as full_name, 
                (SELECT id FROM study_material_like where study_material_id = study_material.id AND user_id = '".$user_id."')as like_id,
                (SELECT count(id) FROM study_material_like where study_material_id = study_material.id) as total_like,
                (SELECT count(id) FROM study_material_view where study_material_id = study_material.id) as total_view,
                (select count(id) from quiz_reviews where quiz_reviews.rel_id = study_material.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'material'."') as rating,
                (select SUM(rating) from quiz_reviews where quiz_reviews.rel_id = study_material.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'material'."') as total_rating")
        
        ->join('category', 'category.id = study_material.category_id')
        ->where('category.id', $category_id)
         ->where('study_material.status',1)
        ->get('study_material')
        ->result();        
    }

    function get_category_study_material_filter($category_id,$sub_category,$rating,$price,$ids)
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        $this->db->select("study_material.*,
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'pdf') as total_pdf, 
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND (type = 'video' OR type = 'vimeo-embed-code' OR type = 'youtube-embed-code')) as total_video, 
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'audio') as total_audio, 
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id) as total_file, 
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'doc') as total_doc, 
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'image') as total_images,
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'content') as total_other,  
                (select CONCAT(first_name, last_name) from users where users.id = study_material.user_id) as full_name, 
                (SELECT id FROM study_material_like where study_material_id = study_material.id AND user_id = '".$user_id."')as like_id,
                (SELECT count(id) FROM study_material_like where study_material_id = study_material.id) as total_like,
                (SELECT count(id) FROM study_material_view where study_material_id = study_material.id) as total_view,
                (select count(id) from quiz_reviews where quiz_reviews.rel_id = study_material.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'material'."') as rating,
                (select SUM(rating) from quiz_reviews where quiz_reviews.rel_id = study_material.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'material'."') as total_rating");
        
        $this->db->join('category', 'category.id = study_material.category_id');
        $this->db->join('quiz_reviews', 'quiz_reviews.rel_id = study_material.id','left');
        $this->db->where_in('category.id', $ids);
        $this->db->where('study_material.status',1);
        if ($sub_category) 
        {
            $this->db->where_in('category.category_title', $sub_category);
        }

        if ($rating) 
        {
            $this->db->where('rel_type =','material');
            $this->db->where('quiz_reviews.rating >=', $rating);
        }

        if ($price == 'free' ) 
        {
            $this->db->where('study_material.price <', 1 );
        }

        if ($price == 'paid') 
        {
            $this->db->where('study_material.price >', 0);
        }
        return $this->db->get('study_material')->result();
    
    }

    function insert_study_like($study_like_data)
    {
        $this->db->insert('study_material_like',$study_like_data);
        return $this->db->insert_id();
    }

    function get_count_likes_through_study_id($study_material_id)
    {
        $this->db->select('count(id) as total_like');
        $this->db->where('study_material_id',$study_material_id);
        return $this->db->get('study_material_like')->row();
    }

    function delete_like_study_through_studyid($study_material_id,$user_id)
    {
        $this->db->where('study_material_id',$study_material_id);
        $this->db->where('user_id',$user_id);
        $this->db->delete('study_material_like');
        return $this->db->affected_rows();
    }

    function get_category_quizes($category_id)
    {

        return $this->db->where('deleted','0')
        ->where_in('category_id',$category_id)
        //->where('quizes.number_questions <= (select count(id) from questions where questions.quiz_id = quizes.id)')
        ->where('is_quiz_active',1)
        ->get('quizes')->result();
    }

    function check_result_by_quizid_and_userid($user_id,$quiz_id,$passing)
    {

        return $this->db->where('user_id',$user_id)
               ->where('quiz_id',$quiz_id)
               ->where('correct >=',$passing)
               ->order_by('id','desc')
               ->limit(1)
               ->get('participants')->row();
    }



    function get_quiz_by_tutors_id($serch_user_id)
    {
        return $this->db->select("quizes.id, quizes.number_questions, (SELECT COUNT(id) FROM questions WHERE questions.quiz_id = quizes.id) questions")
        ->join('category', 'category.id = quizes.category_id')
        ->where('quizes.user_id', $serch_user_id)
        ->where('is_quiz_active',1)
        //->where('quizes.number_questions <= (select count(id) from questions where questions.quiz_id = quizes.id)')
        ->where(time().' BETWEEN start_date_time AND end_date_time')
        ->order_by('added', 'desc')
        ->get('quizes')
        ->result(); 
    }




    function get_tutors_quiz_per_page($serch_user_id, $pro_per_page, $page)
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        return $this->db->select("quizes.*,(SELECT count(id) FROM quiz_count where quiz_id = quizes.id) as total_view,
            (SELECT id FROM quiz_like where quiz_id = quizes.id AND user_id = '".$user_id."' limit 1)as like_id,
            (SELECT count(id) FROM quiz_like where quiz_id = quizes.id) as total_like, 
            (select first_name from users where users.id = quizes.user_id  limit 1) as first_name , 
            (select last_name from users where users.id = quizes.user_id  limit 1) as last_name, 
            (SELECT count(id) FROM quiz_count where quiz_id = quizes.id ) as total_view,
            (select count(id) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'quiz'."') as rating,
            (select SUM(rating) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'quiz'."') as total_rating")
        ->join('category', 'category.id = quizes.category_id')
        //->where('quizes.number_questions <= (select count(id) from questions where questions.quiz_id = quizes.id)')
        ->where('quizes.user_id', $serch_user_id)
        ->where('is_quiz_active',1)
        ->where(time().' BETWEEN start_date_time AND end_date_time')
        ->limit($pro_per_page, $page)
        ->order_by('id', "asc")
        ->get('quizes')
        ->result(); 
    }




    function get_study_material_count_by_ids($user_study_metrial_ids)
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        if($user_study_metrial_ids)
        {
           return $this->db->where_in('study_material.id', $user_study_metrial_ids)
            ->where('study_material.status',1)
            ->get('study_material')
            ->result_array();    
        }
        else
        {
            return array();
        }
    }


    function get_tutors_study_material_per_page($user_study_metrial_ids,$pro_per_page, $page)
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        return $this->db->select("study_material.*,
                
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'video') as total_audio, 
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id) as total_file, 

                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND (type = 'video' OR type = 'vimeo-embed-code' OR type = 'youtube-embed-code')) as total_video,

                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'doc') as total_doc, 

                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'image') as total_images, 
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'content') as total_other, 

                
                (select CONCAT(first_name, last_name) from users where users.id = study_material.user_id) as full_name, 
                (SELECT id FROM study_material_like where study_material_id = study_material.id AND user_id = '".$user_id."')as like_id,
                (SELECT count(id) FROM study_material_like where study_material_id = study_material.id) as total_like,
                (SELECT count(id) FROM study_material_view where study_material_id = study_material.id) as total_view,
                (select count(id) from quiz_reviews where quiz_reviews.rel_id = study_material.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'material'."') as rating,
                (select SUM(rating) from quiz_reviews where quiz_reviews.rel_id = study_material.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'material'."') as total_rating")
        
        ->join('category', 'category.id = study_material.category_id')
        ->where_in('study_material.id', $user_study_metrial_ids)
        ->where('study_material.status',1)
        ->limit($pro_per_page, $page)
        ->get('study_material')
        ->result();        
    }

}