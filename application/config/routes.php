<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern: 
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details: 
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
 


$route['default_controller']   					= 'Home_Controller';
$route['404_override']         					= 'errors/error404';
$route['translate_uri_dashes'] 					= TRUE;

$route['admin/login']                			= 'Admin_login_Controller/login';
$route['admin/logout']               			= 'Admin_login_Controller/logout';



$route['login']                					= 'user/login';
$route['logout']               					= 'user/logout';
$route['otp']                					= 'user/otp_login';
$route['login-otp/(:any)']                		= 'user/match_otp/$1';
//=======================================================================================
//================================= Admin Routes ========================================
//=======================================================================================
$route['admin']                					= 'admin/dashboard';
$route['tutor']                					= 'tutor/dashboard/index';

//================================= Db Backup Routes ========================================
$route['admin/db-backup-list']                			= 'admin/Backup/index';
$route['admin/db-backup'] 							= 'admin/Backup/db_backup';

//================================= Quiz Routes ========================================

$route['admin/quiz']                			= 'admin/QuizController/index';
$route['admin/quiz/quiz-reports']               = 'admin/QuizController/quiz_reports';
$route['admin/quiz/bulk_import']                = 'admin/Excel_import/bulk_import';
$route['admin/quiz/add']                		= 'admin/QuizController/add';
$route['admin/quiz/update/(:any)']           	= 'admin/QuizController/update/$1';
$route['admin/quiz/copy/(:any)']           		= 'admin/QuizController/copy/$1';
$route['admin/quiz/delete/(:any)']           	= 'admin/QuizController/delete/$1';
$route['admin/quiz/dropzone-file']    			= 'admin/QuizController/quiz_upload_file';
$route['admin/quiz/dropzone-file-remove']    	= 'admin/QuizController/dropzone_quiz_file_remove';
$route['admin/quiz/delete-image/(:any)']    	= 'admin/QuizController/delete_featured_image/$1';
$route['admin/quiz/image-resize/(:any)']    	= 'admin/QuizController/image_resize_library/$1';
$route['admin/quiz/question-list/(:any)']    	= 'admin/QuizController/question_list/$1';
$route['admin/quiz/questions/(:any)'] 			= 'admin/QuizController/questions/$1';
$route['admin/quiz/translate-quiz/(:any)'] 		= 'admin/QuizController/translate_quiz/$1';

//================================= Quiz Routes ========================================

$route['tutor/quiz']                			= 'tutor/QuizController/index';
$route['tutor/quiz/quiz-reports']               = 'tutor/QuizController/quiz_reports';
$route['tutor/quiz/bulk_import']                = 'tutor/Excel_import/bulk_import';
$route['tutor/quiz/add']                		= 'tutor/QuizController/add';
$route['tutor/quiz/update/(:any)']           	= 'tutor/QuizController/update/$1';
$route['tutor/quiz/copy/(:any)']           		= 'tutor/QuizController/copy/$1';
$route['tutor/quiz/delete/(:any)']           	= 'tutor/QuizController/delete/$1';
$route['tutor/quiz/dropzone-file']    			= 'tutor/QuizController/quiz_upload_file';
$route['tutor/quiz/dropzone-file-remove']    	= 'tutor/QuizController/dropzone_quiz_file_remove';
$route['tutor/quiz/delete-image/(:any)']    	= 'tutor/QuizController/delete_featured_image/$1';
$route['tutor/quiz/image-resize/(:any)']    	= 'tutor/QuizController/image_resize_library/$1';
$route['tutor/quiz/question-list/(:any)']    	= 'tutor/QuizController/question_list/$1';
$route['tutor/quiz/questions/(:any)'] 			= 'tutor/QuizController/questions/$1';
$route['tutor/quiz/translate-quiz/(:any)'] 		= 'tutor/QuizController/translate_quiz/$1';


$route['tutor/report/(:any)'] 					= 'tutor/ReportController/index/$1';
$route['tutor/report/(:any)'] 					= 'tutor/ReportController/index/$1';
$route['tutor/report/summary/(:any)'] 			= 'tutor/ReportController/summary/$1';
$route['tutor/report/delete/(:any)/(:any)'] 	= 'tutor/ReportController/delete/$1/$2';
$route['tutor/rating/(:any)/(:num)'] 			= 'tutor/RatingController/index/$1/$2';


$route['tutor/questions']                				= 'tutor/QuestionController/index';
$route['tutor/questions/add/(:any)']                	= 'tutor/QuestionController/add/$1';
$route['tutor/questions/update/(:any)/(:any)']          = 'tutor/QuestionController/update/$1/$2';
$route['tutor/questions/copy/(:any)/(:any)']           	= 'tutor/QuestionController/copy/$1/$2';
$route['tutor/questions/delete/(:any)/(:any)']          = 'tutor/QuestionController/delete/$1/$2';
$route['tutor/questions/dropzone-file']    				= 'tutor/QuestionController/questions_upload_file';
$route['tutor/questions/dropzone-file-remove']    		= 'tutor/QuestionController/dropzone_questions_file_remove';
$route['tutor/questions/delete-image/(:any)']    		= 'tutor/QuestionController/delete_questions_image/$1';
$route['tutor/questions/get-questions-field/(:any)'] 	= 'tutor/QuestionController/get_questions_fields/$1';
$route['tutor/questions/translate-questions/(:any)'] 	= 'tutor/QuestionController/translate_questions/$1';



$route['tutor/study']                					= 'tutor/Study_Material/index';
$route['tutor/study/add']                 				= 'tutor/Study_Material/add';
$route['tutor/study/study-material-list']               = 'tutor/Study_Material/study_material_list';
$route['tutor/study/update/(:any)']               		= 'tutor/Study_Material/study_material_update/$1';
$route['tutor/study/copy/(:any)']               		= 'tutor/Study_Material/copy/$1';
$route['tutor/study/delete-study-material/(:any)']      = 'tutor/Study_Material/study_material_delete/$1';
$route['tutor/study/material-file/(:any)']      		= 'tutor/Study_Material/material_file/$1';
$route['tutor/study/add-material-file/(:num)/(:any)']   = 'tutor/Study_Material/add_material_file/$1/$2';
$route['tutor/study/material-file-list/(:any)']      	= 'tutor/Study_Material/material_file_list/$1';
$route['tutor/study/update-material-content/(:num)']   	= 'tutor/Study_Material/update_study_material_content/$1';
$route['tutor/study/delete-material-content/(:num)']   	= 'tutor/Study_Material/delete_study_material_content/$1';
$route['tutor/study/section/(:num)']    				= 'tutor/Study_Material/section/$1';
$route['tutor/study/section-update/(:num)']    			= 'tutor/Study_Material/section_update/$1';
$route['tutor/study/section-delete/(:num)']    			= 'tutor/Study_Material/section_delete/$1';




$route['tutor/quiz-grading'] 						= 'tutor/GradingController/index';
$route['tutor/quiz-grading-list'] 					= 'tutor/GradingController/ajax_list';
$route['tutor/quiz-grading-add'] 					= 'tutor/GradingController/add';
$route['tutor/quiz-grading-update/(:num)'] 			= 'tutor/GradingController/update/$1';
$route['tutor/quiz-grading-delete/(:num)'] 			= 'tutor/GradingController/delete/$1';







$route['admin/quiz-grading'] 						= 'admin/GradingController/index';
$route['admin/quiz-grading-list'] 					= 'admin/GradingController/ajax_list';
$route['admin/quiz-grading-add'] 					= 'admin/GradingController/add';
$route['admin/quiz-grading-update/(:num)'] 			= 'admin/GradingController/update/$1';
$route['admin/quiz-grading-delete/(:num)'] 			= 'admin/GradingController/delete/$1';



$route['admin/sp/get-sp-list'] 					= 'admin/sponsors/admin_sp_list';
$route['admin/report/(:any)'] 					= 'admin/ReportController/index/$1';
$route['admin/report/(:any)'] 					= 'admin/ReportController/index/$1';
$route['admin/report/summary/(:any)'] 			= 'admin/ReportController/summary/$1';
$route['admin/report/delete/(:any)/(:any)'] 	= 'admin/ReportController/delete/$1/$2';
$route['admin/rating/(:any)/(:num)'] 			= 'admin/RatingController/index/$1/$2';

$route['admin/payment'] 					    = 'admin/Payment_Controller/index';
$route['admin/payment/payment-list'] 		    = 'admin/Payment_Controller/payment_list';
$route['admin/payment/update-status'] 		    = 'admin/Payment_Controller/update_status';
$route['admin/payment/payment-detail']   		= 'admin/Payment_Controller/payment_detail';
$route['admin/payment/invoice/(:any)']   		= 'admin/Payment_Controller/invoice/$1';

//================================= Company Routes ========================================

$route['admin/questions']                				= 'admin/QuestionController/index';
$route['admin/questions/add/(:any)']                	= 'admin/QuestionController/add/$1';
$route['admin/questions/update/(:any)/(:any)']          = 'admin/QuestionController/update/$1/$2';
$route['admin/questions/copy/(:any)/(:any)']           	= 'admin/QuestionController/copy/$1/$2';
$route['admin/questions/delete/(:any)/(:any)']          = 'admin/QuestionController/delete/$1/$2';
$route['admin/questions/dropzone-file']    				= 'admin/QuestionController/questions_upload_file';
$route['admin/questions/dropzone-file-remove']    		= 'admin/QuestionController/dropzone_questions_file_remove';
$route['admin/questions/delete-image/(:any)']    		= 'admin/QuestionController/delete_questions_image/$1';
$route['admin/questions/get-questions-field/(:any)'] 	= 'admin/QuestionController/get_questions_fields/$1';
$route['admin/questions/translate-questions/(:any)'] 	= 'admin/QuestionController/translate_questions/$1';

$route['admin/study']                					= 'admin/Study_Material/index';
$route['admin/study/add']                 				= 'admin/Study_Material/add';
$route['admin/study/study-material-list']               = 'admin/Study_Material/study_material_list';
$route['admin/study/update/(:any)']               		= 'admin/Study_Material/study_material_update/$1';
$route['admin/study/copy/(:any)']               		= 'admin/Study_Material/copy/$1';
$route['admin/study/delete-study-material/(:any)']      = 'admin/Study_Material/study_material_delete/$1';
$route['admin/study/material-file/(:any)']      		= 'admin/Study_Material/material_file/$1';
$route['admin/study/add-material-file/(:num)/(:any)']   = 'admin/Study_Material/add_material_file/$1/$2';
$route['admin/study/material-file-list/(:any)']      	= 'admin/Study_Material/material_file_list/$1';
$route['admin/study/update-material-content/(:num)']   	= 'admin/Study_Material/update_study_material_content/$1';
$route['admin/study/delete-material-content/(:num)']   	= 'admin/Study_Material/delete_study_material_content/$1';
$route['admin/study/section/(:num)']    						= 'admin/Study_Material/section/$1';
$route['admin/study/section-update/(:num)']    					= 'admin/Study_Material/section_update/$1';
$route['admin/study/section-delete/(:num)']    					= 'admin/Study_Material/section_delete/$1';

$route['my/history'] 									= 'History_Controller/history';
$route['my/history/(:num)'] 							= 'History_Controller/history/$1';

$route['quiz/leader-board/(:num)'] 						= 'History_Controller/leader_board/$1';
$route['site/common-leader-board'] 						= 'History_Controller/common_leader_board';

$route['my/test/summary/(:any)'] 						= 'Test_Controller/test_summary/$1';


$route['admin/quiz/import'] 							= 'admin/Excel_import/index';
$route['admin/quiz/import/(:num)'] 		 				= 'admin/Excel_import/index/$1';

$route['admin/study-data/import'] 							= 'admin/Excel_import/study_data_import';
$route['admin/study-data/import/(:num)'] 		 			= 'admin/Excel_import/study_data_import/$1';

$route['tutor/study-data/import'] 							= 'tutor/Excel_import/study_data_import';
$route['tutor/study-data/import/(:num)'] 		 			= 'tutor/Excel_import/study_data_import/$1';

$route['tutor/quiz/import'] 							= 'tutor/Excel_import/index';
$route['tutor/quiz/import/(:num)'] 		 				= 'tutor/Excel_import/index/$1';


$route['admin/template'] 		 						= 'admin/Template_Controller/index';
$route['admin/template/email_list'] 		 			= 'admin/Template_Controller/email_template_list';
$route['admin/template/update/(:any)'] 		 			= 'admin/Template_Controller/update/$1';


//================================= Other Routes ========================================  
$route['sitemap\.xml']         					= 'sitemap';

$route['instruction/(:any)/(:any)'] 		    = 'Quiz_Controller/instruction/$1/$2';
$route['start-test/(:num)']		 		    	= 'Test_Controller/set_test_session/$1';
$route['test/(:num)/(:num)']		 		    = 'Test_Controller/test/$1/$2';
$route['result/(:num)']		 		    		= 'Test_Controller/test_result/$1';
$route['test-submit-request']		 		    = 'Test_Controller/test_submit_request';
$route['category/(:any)/(:any)']		 		= 'Quiz_Controller/category/$1/$2'; 
$route['category/(:any)']		 		    	= 'Quiz_Controller/category/$1';
$route['all-quiz-categories']		 		    = 'Quiz_Controller/all_category';
$route['category-difficulty/(:any)']		 	= 'Quiz_Controller/difficulty_level/$1';
$route['like/(:any)']							= 'Quiz_Controller/like_quiz'; 
$route['dislike/(:any)']						= 'Quiz_Controller/like_quiz_delete'; 
$route['quiz-detail/(:any)/(:any)']				= 'Quiz_Controller/quiz_detail/$1/$2'; 
$route['quiz/(:any)']							= 'Quiz_Controller/quiz_detail_by_slug/$1'; 
$route['rating/(:any)']							= 'Quiz_Controller/submit_rating/$1'; 
$route['study-like/(:any)']					    = 'Study_Controller/like_study'; 
$route['study-dislike/(:any)']					= 'Study_Controller/like_study_delete'; 
$route['study-material-detail/(:any)/(:any)']   = 'Study_Controller/index/$1/$2'; 
$route['study-material/(:any)/(:num)']   		= 'Study_Controller/study_details/$1/$2'; 
$route['study-material/(:any)']   				= 'Study_Controller/study_details/$1'; 
$route['study-content/(:any)']   				= 'Study_Controller/study_details_show/$1'; 
$route['my-study-data']   						= 'Study_Controller/user_study_data'; 
$route['material-rating/(:any)']			    = 'Study_Controller/submit_rating/$1';

$route['search/(:any)']         				= 'Filter_Controller/search/$1';
$route['product/(:any)/(:any)']         		= 'Product_detail_Controller/index/$1/$2';
$route['compare/(:any)']         				= 'Compare_Controller/index/$1';
$route['compare-product/(:any)/(:any)']         = 'Compare_Controller/compare_product/$1/$2';
$route['compare-product-remove/(:any)/(:any)']  = 'Compare_Controller/compare_product_remove/$1/$2';
$route['compare-product-nav-data']  			= 'Compare_Controller/compare_product_nav_data';
$route['admin/admin-setting']  					= 'Admin_setting_Controller/index';

$route['remove-from-favourite/(:num)']  		= 'Profile/remove_from_favourite/$1';
$route['add-to-fav-product/(:num)']  			= 'Home_Controller/add_to_fav_product/$1';
$route['page/(:any)']         					= 'Page_Controller/index/$1';
$route['pages/(:any)']         					= 'Contant_Controller/index/$1';

$route['blogs/(:any)']         					= 'Blog_Controller/index/$1';
$route['blogs']         						= 'Blog_Controller/index';
$route['blog/(:any)']         					= 'Blog_Controller/detail/$1';
$route['blog/list/(:any)']         				= 'Blog_Controller/list/$1';	
$route['blog/list/(:any)/(:any)']         		= 'Blog_Controller/list/$1/$2';	


$route['membership']         						= 'Membership_Controller/index'; 

$route['stripe/pay-now/(:any)/(:any)']         	    			= 'Stripe/index/$1/$2';  
$route['stripe/check-payment/(:any)/(:any)/(:any)']        		= 'Stripe/check/$1/$2/$3';
$route['stripe/checkout/quiz-pay/(:any)/(:any)/(:any)']        = 'Stripe/check/$1/$2/$3';
$route['stripe/quiz-pay/payment-success/(:any)/(:any)/(:any)'] = 'Stripe/payment_success/$1/$2/$3';
$route['stripe/quiz-pay/payment-fail/(:any)/(:any)']    = 'Stripe/payment_error/$1/$2';


$route['quiz-pay/payment-mode/(:any)/(:any)']         	= 'payment/payment_mode/$1/$2';

$route['paypal/payment/quiz-pay/(:any)/(:any)']         = 'payment/create_payment/$1/$2';
$route['paypal/quiz-pay/pay-successfuly/(:any)/(:any)'] = 'payment/success_payment/$1/$2';
$route['paypal/quiz-pay/payment-fail/(:any)/(:any)']    = 'payment/cancel/$1/$2';
$route['paypal/quiz-pay/payment-status/(:any)/(:any)/(:any)']  = 'payment/paypal_payment_view/$1/$2/$3';


$route['razorpay/checkout/(:any)/(:any)']         	    	= 'Razorpay/index/$1/$2';
$route['razorpay/verify/(:any)/(:any)/(:any)']         	    = 'Razorpay/verify/$1/$2/$3';
$route['razorpay/quiz-payment/(:any)/(:any)/(:any)']        = 'Razorpay/quiz_payment/$1/$2/$3';

$route['quiz-pay/bank-transfer-insert']         		= 'payment/save_bank_transfer';
$route['quiz-pay/bank-transfer-update']         		= 'payment/update_bank_transfer';

$route['instamojo/(:any)/(:any)']         	    		= 'Instamojo_Controller/index/$1/$2';
$route['instamojo/checkout/(:any)/(:any)']        		= 'Instamojo_Controller/checkout/$1/$2';
$route['instamojo/payment-status']        				= 'Instamojo_Controller/payment_status';
$route['instamojo/payment-success/(:any)/(:any)/(:any)'] = 'Instamojo_Controller/payment_success/$1/$2/$3';
$route['instamojo/payment-fail/(:any)/(:any)']    		= 'Instamojo_Controller/payment_error/$1/$2';

$route['user/reset-my-password/(:any)'] 		= 'User/reset_password_form/$1';
$route['user/update-my-password'] 				= 'Home_Controller/update_user_password';
$route['view-payment-detail']  					= 'Profile/view_payment_detail';
$route['invoice/(:any)']   						= 'Profile/invoice/$1';
$route['search']   								= 'Search_Controller/index';
$route['search/(:any)']   						= 'Search_Controller/index/$1';
$route['tutor-quiz/(:any)']   					= 'Search_Controller/find_tutor_quiz/$1';
$route['tutor-study-material/(:any)']   		= 'Search_Controller/find_tutor_study_material/$1';
