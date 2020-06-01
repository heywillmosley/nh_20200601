<?php
class WpProQuiz_Controller_Admin {
	
	protected $_ajax;
	
	public function __construct() {
		
		$this->_ajax = new WpProQuiz_Controller_Ajax();
		
		$this->_ajax->init();
		
		//deprecated - use WpProQuiz_Controller_Ajax
		add_action('wp_ajax_wp_pro_quiz_update_sort', array($this, 'updateSort'));
		add_action('wp_ajax_wp_pro_quiz_load_question', array($this, 'updateSort'));
		
		add_action('wp_ajax_wp_pro_quiz_reset_lock', array($this, 'resetLock'));
		
		add_action('wp_ajax_wp_pro_quiz_load_toplist', array($this, 'adminToplist'));
				
		add_action('wp_ajax_wp_pro_quiz_completed_quiz', array($this, 'completedQuiz'));
		add_action('wp_ajax_nopriv_wp_pro_quiz_completed_quiz', array($this, 'completedQuiz'));
		
		add_action('wp_ajax_wp_pro_quiz_check_lock', array($this, 'quizCheckLock'));
		add_action('wp_ajax_nopriv_wp_pro_quiz_check_lock', array($this, 'quizCheckLock'));
		
		//0.19
		add_action('wp_ajax_wp_pro_quiz_add_toplist', array($this, 'addInToplist'));
		add_action('wp_ajax_nopriv_wp_pro_quiz_add_toplist', array($this, 'addInToplist'));
		
		add_action('wp_ajax_wp_pro_quiz_show_front_toplist', array($this, 'showFrontToplist'));
		add_action('wp_ajax_nopriv_wp_pro_quiz_show_front_toplist', array($this, 'showFrontToplist'));
		
		add_action('wp_ajax_wp_pro_quiz_load_quiz_data', array($this, 'loadQuizData'));
		add_action('wp_ajax_nopriv_wp_pro_quiz_load_quiz_data', array($this, 'loadQuizData'));
		
		
		add_action('admin_menu', array($this, 'register_page'));
	}
	
	public function loadQuizData() {
		$q = new WpProQuiz_Controller_Quiz();
		
		echo json_encode($q->loadQuizData());
		
		exit;
	}
	
	public function adminToplist() {
		$t = new WpProQuiz_Controller_Toplist();
		$t->route();
		
		exit;
	}
	
	public function showFrontToplist() {
		$t = new WpProQuiz_Controller_Toplist();
		
		$t->showFrontToplist();
		
		exit;
	}
	
	public function addInToplist() {
		$t = new WpProQuiz_Controller_Toplist();
		
		$t->addInToplist();
		
		exit;
	}
	
	public function resetLock() {
		if ( ( isset( $_GET['post'] ) ) && ( !isset( $_GET['id'] ) ) ) {
			$_GET['id'] = get_post_meta( $_GET['post'], 'quiz_pro_id', true );
		}
		
		$c = new WpProQuiz_Controller_Quiz();
		$c->route();
	}
	
	public function quizCheckLock() {
		$quizController = new WpProQuiz_Controller_Quiz();
		
		echo json_encode($quizController->isLockQuiz($_POST['quizId']));
		
		exit;
	}
	
	public function updateSort() {
		$c = new WpProQuiz_Controller_Question();
		$c->route();
	}
	
	public function completedQuiz() {
		// First we unpack the $_POST['results'] string
		if ( ( isset( $_POST['results'] ) ) && ( !empty( $_POST['results'] ) ) && ( is_string( $_POST['results'] ) ) ) {
			$_POST['results'] = json_decode(stripslashes($_POST['results']), true);
		}
		
		if ( is_user_logged_in() )
			$user_id			= 	get_current_user_id();
		else
			$user_id			=	0;
		
		if ( isset( $_POST['quizId'] ) )
			$id 				= 	$_POST['quizId'];
		else
			$id					= 	0;

		if ( isset( $_POST['quiz'] ) )
			$quiz_post_id 		= 	$_POST['quiz'];
		else
			$quiz_post_id		= 	0;


		// LD 2.4.3 - Change in logic. Instead of accepting the values for points, correct etc from JS we now pass the 'results' array on the complete quiz
		// AJAX action. This now let's us verify the points, correct answers etc. as each have a unique nonce. 
		$total_awarded_points = 0;
		$total_correct = 0;

		// If the results is not present then abort. 
		if ( !isset( $_POST['results'] ) ) {
			return array('text' => __('An error has occurred.', 'wp-pro-quiz'), 'clear' => true);
		}
		
		// Loop over the 'results' items. We verify and tally the points+correct counts as well as the student response 'data'. When we get to the 'comp' results element
		// we set the award points and correct as well as determine the total possible points. 
		// @TODO Need to test how this works with variabel question quizzes. 
		foreach( $_POST['results'] as $r_idx => $result ) {
			if ( $r_idx == 'comp' ) {
				$_POST['results'][$r_idx]['points'] = intval( $total_awarded_points );
				$_POST['results'][$r_idx]['correctQuestions'] = intval( $total_correct );

				//$quizMapper = new WpProQuiz_Model_QuizMapper();
				//$total_possible_points = $quizMapper->sumQuestionPoints( $id );
				//$_POST['results'][$r_idx]['possiblePoints'] = intval( $total_possible_points );
				//$_POST['results'][$r_idx]['result'] = round( intval( $_POST['results'][$r_idx]['points'] ) / intval( $_POST['results'][$r_idx]['possiblePoints'] ) * 100, 2 );
				
				continue;
			}
			
			$points_array = array(
				'points' => intval( $result['points'] ),
				'correct' => intval( $result['correct'] ),
				'possiblePoints' => intval( $result['possiblePoints'] )
			);
			if ( $points_array['correct'] === false ) $points_array['correct'] = 0;
			else if ( $points_array['correct'] === true ) $points_array['correct'] = 1;
			$points_str = maybe_serialize( $points_array );
			
			if ( !wp_verify_nonce( $result['p_nonce'], 'ld_quiz_pnonce'. $user_id .'_'. $id .'_'. $quiz_post_id .'_'. $r_idx .'_'. $points_str ) ) {
				$_POST['results'][$r_idx]['points'] = 0;
				$_POST['results'][$r_idx]['correct'] = 0;
				$_POST['results'][$r_idx]['possiblePoints'] = 0;
			}
			$total_awarded_points += intval( $_POST['results'][$r_idx]['points'] );
			$total_correct += $_POST['results'][$r_idx]['correct'];
			$response_str = maybe_serialize( $result['data'] );

			if ( !wp_verify_nonce( $result['a_nonce'], 'ld_quiz_anonce'. $user_id .'_'. $id .'_'. $quiz_post_id .'_'. $r_idx .'_'. $response_str ) ) {
				$_POST['results'][$r_idx]['data'] = array();
			}
		}
		
		$quiz = new WpProQuiz_Controller_Quiz();
		$quiz->completedQuiz();
	}
	
	private function localizeScript() {
		global $wp_locale;
		
		$isRtl = isset($wp_locale->is_rtl) ? $wp_locale->is_rtl : false;
		
		$translation_array = array(
			'delete_msg' => sprintf( _x('Do you really want to delete the %s/question?', 'Do you really want to delete the quiz/question?', 'wp-pro-quiz'), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ),
			'no_title_msg' => __('Title is not filled!', 'wp-pro-quiz'),
			'no_question_msg' => __('No question deposited!', 'wp-pro-quiz'),
			'no_correct_msg' => __('Correct answer was not selected!', 'wp-pro-quiz'),
			'no_answer_msg' => __('No answer deposited!', 'wp-pro-quiz'),
			'no_quiz_start_msg' => sprintf( _x('No %s description filled!', 'No quiz description filled!', 'wp-pro-quiz'), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ),
			'fail_grade_result' => __('The percent values in result text are incorrect.', 'wp-pro-quiz'),
			'no_nummber_points' => __('No number in the field "Points" or less than 1', 'wp-pro-quiz'),
			'no_nummber_points_new' => __('No number in the field "Points" or less than 0', 'wp-pro-quiz'),
			'no_selected_quiz' => sprintf( _x('No %s selected', 'No quiz selected', 'wp-pro-quiz'), LearnDash_Custom_Label::label_to_lower( 'quiz' ) ), 
			'reset_statistics_msg' => __('Do you really want to reset the statistic?', 'wp-pro-quiz'),
			'no_data_available' => __('No data available', 'wp-pro-quiz'),
			'no_sort_element_criterion' => __('No sort element in the criterion', 'wp-pro-quiz'),
			'dif_points' => __('"Different points for every answer" is not possible at "Free" choice', 'wp-pro-quiz'),
			'category_no_name' => __('You must specify a name.', 'wp-pro-quiz'),
			'confirm_delete_entry' => __('This entry should really be deleted?', 'wp-pro-quiz'),
			'not_all_fields_completed' => __('Not all fields completed.', 'wp-pro-quiz'),
			'temploate_no_name' => __('You must specify a template name.', 'wp-pro-quiz'),
			'no_delete_answer' => __('Cannot delete only answer', 'wp-pro-quiz'),
				
				
			'closeText'         => __('Close', 'wp-pro-quiz'),
			'currentText'       => __('Today', 'wp-pro-quiz'),
			'monthNames'        => array_values($wp_locale->month),
			'monthNamesShort'   => array_values($wp_locale->month_abbrev),
			'dayNames'          => array_values($wp_locale->weekday),
			'dayNamesShort'     => array_values($wp_locale->weekday_abbrev),
			'dayNamesMin'       => array_values($wp_locale->weekday_initial),
			'dateFormat'        => WpProQuiz_Helper_Until::convertPHPDateFormatToJS(get_option('date_format', 'm/d/Y')),
			'firstDay'          => get_option('start_of_week'),
			'isRTL'             => $isRtl
		);
		
		
		
		
		wp_localize_script('wpProQuiz_admin_javascript', 'wpProQuizLocalize', $translation_array);
	}
	
	public function enqueueScript() {
		global $learndash_assets_loaded;
		
		wp_enqueue_script(
			'wpProQuiz_admin_javascript', 
			plugins_url('js/wpProQuiz_admin'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js', WPPROQUIZ_FILE),
			array('jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker'),
			LEARNDASH_SCRIPT_VERSION_TOKEN
		);
		$learndash_assets_loaded['scripts']['wpProQuiz_admin_javascript'] = __FUNCTION__;
		
		$this->localizeScript();		
	}
	
	public function register_page() {
		/*$page = add_menu_page(
					__('Advanced Quiz', 'wp-pro-quiz'),
					__('Advanced Quiz', 'wp-pro-quiz'),
					'wpProQuiz_show',
					'ldAdvQuiz',
					array($this, 'route'));*/
		
		$quiz_title = sprintf( _x('Advanced %s', 'Advanced Quiz', 'wp-pro-quiz'), LearnDash_Custom_Label::get_label( 'quiz' ) );
	    $page = add_submenu_page(
			"edit.php?post_type=sfwd-quiz", 
			$quiz_title, 
			$quiz_title, 
			"wpProQuiz_show", "ldAdvQuiz", 
			array($this, 'route')
		);

		add_action('admin_print_scripts-'.$page, array($this, 'enqueueScript'));
	}
	
	public function route() {
		$module = isset($_GET['module']) ? $_GET['module'] : 'overallView';
		
		$c = null;
		
		switch ($module) {
			case 'overallView':
				$c = new WpProQuiz_Controller_Quiz();
				break;
			case 'question':
				$c = new WpProQuiz_Controller_Question();
				break;
			case 'preview':
				$c = new WpProQuiz_Controller_Preview();
				break;
			case 'statistics':
				$c = new WpProQuiz_Controller_Statistics();
				break;
			case 'importExport':
				$c = new WpProQuiz_Controller_ImportExport();
				break;
			case 'globalSettings':
				$c = new WpProQuiz_Controller_GlobalSettings();
				break;
			case 'styleManager':
				$c = new WpProQuiz_Controller_StyleManager();
				break;
			case 'toplist':
				$c = new WpProQuiz_Controller_Toplist();
				break;
			case 'wpq_support':
				$c = new WpProQuiz_Controller_WpqSupport();
				break;
		}

		if($c !== null) {
			$c->route();
		}
	}
}