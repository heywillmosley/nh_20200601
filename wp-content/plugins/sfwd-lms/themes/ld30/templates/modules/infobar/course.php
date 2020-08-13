<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Displays the infobar in course context
 *
 * Will have access to same variables as course.php
 *
 * Available Variables:
 * $course_id                  : (int) ID of the course
 * $course                     : (object) Post object of the course
 * $course_settings            : (array) Settings specific to current course
 *
 * $courses_options            : Options/Settings as configured on Course Options page
 * $lessons_options            : Options/Settings as configured on Lessons Options page
 * $quizzes_options            : Options/Settings as configured on Quiz Options page
 *
 * $user_id                    : Current User ID
 * $logged_in                  : User is logged in
 * $current_user               : (object) Currently logged in user object
 *
 * $course_status              : Course Status
 * $has_access                 : User has access to course or is enrolled.
 * $materials                  : Course Materials
 * $has_course_content         : Course has course content
 * $lessons                    : Lessons Array
 * $quizzes                    : Quizzes Array
 * $lesson_progression_enabled : (true/false)
 * $has_topics                 : (true/false)
 * $lesson_topics              : (array) lessons topics
 *
 * @since 3.0
 *
 * @package LearnDash\Course
 */ ?>

<?php
$course_pricing = learndash_get_course_price( $course_id );

if ( is_user_logged_in() && isset( $has_access ) && $has_access ) :
	?>

	<div class="ld-course-status ld-course-status-enrolled">

		<?php
		/**
		 * Fires inside the breadcrumbs (before).
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'learndash-course-infobar-access-progress-before', get_post_type(), $course_id, $user_id );

		learndash_get_template_part(
			'modules/progress.php',
			array(
				'context'   => 'course',
				'user_id'   => $user_id,
				'course_id' => $course_id,
			),
			true
		);

		/**
		 * Fires inside the breadcrumbs after the progress bar.
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'learndash-course-infobar-access-progress-after', get_post_type(), $course_id, $user_id );

		learndash_status_bubble( $course_status );

		/**
		 * Fires inside the breadcrumbs after the status.
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'learndash-course-infobar-access-status-after', get_post_type(), $course_id, $user_id );
		?>

	</div> <!--/.ld-course-status-->

<?php elseif ( 'open' !== $course_pricing['type'] ) : ?>

	<div class="ld-course-status ld-course-status-not-enrolled">

		<?php
		/**
		 * Fires inside the un-enrolled course infobox before the status.
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'learndash-course-infobar-noaccess-status-before', get_post_type(), $course_id, $user_id );
		?>

		<div class="ld-course-status-segment ld-course-status-seg-status">

			<?php
			/**
			 * Fires before the course infobar status cell.
			 *
			 * @param string|false $post_type Post type slug.
			 * @param int          $course_id Course ID.
			 * @param int          $user_id   User ID.
			 */
			do_action( 'learndash-course-infobar-status-cell-before', get_post_type(), $course_id, $user_id );
			?>

			<span class="ld-course-status-label"><?php echo esc_html__( 'Current Status', 'learndash' ); ?></span>
			<div class="ld-course-status-content">
				<span class="ld-status ld-status-waiting ld-tertiary-background" data-ld-tooltip="<?php esc_attr_e( 'Enroll in this course to get access', 'learndash' ); ?>"><?php esc_html_e( 'Not Enrolled', 'learndash' ); ?></span>
			</div>

			<?php
			/**
			 * Fires after the course infobar status cell.
			 *
			 * @param string|false $post_type Post type slug.
			 * @param int          $course_id Course ID.
			 * @param int          $user_id   User ID.
			 */
			do_action( 'learndash-course-infobar-status-cell-after', get_post_type(), $course_id, $user_id );
			?>

		</div> <!--/.ld-course-status-segment-->

		<?php
		/**
		 * Fires inside the un-enrolled course infobox before the price.
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'learndash-course-infobar-noaccess-price-before', get_post_type(), $course_id, $user_id );
		?>

		<div class="ld-course-status-segment ld-course-status-seg-price">

			<?php
			/**
			 * Fires before the course infobar price cell.
			 *
			 * @param string|false $post_type Post type slug.
			 * @param int          $course_id Course ID.
			 * @param int          $user_id   User ID.
			 */
			do_action( 'learndash-course-infobar-price-cell-before', get_post_type(), $course_id, $user_id );
			?>

			<span class="ld-course-status-label"><?php echo esc_html__( 'Price', 'learndash' ); ?></span>

			<div class="ld-course-status-content">
				<span class="ld-course-status-price">
					<?php
					if ( isset( $course_pricing['price'] ) && ! empty( $course_pricing['price'] ) ) :
						if ( 'closed' !== $course_pricing['type'] ) :
							echo wp_kses_post( '<span class="ld-currency">' . learndash_30_get_currency_symbol() . '</span>' );
							endif;
						echo wp_kses_post( $course_pricing['price'] );
						else :
								/**
								 * Filters label to be displayed when there is no price set for a course or it is closed.
								 *
								 * @param string $label The label displayed when there is no price.
								 */
								$label = apply_filters( 'learndash_no_price_price_label', ( 'closed' === $course_pricing['type'] ? __( 'Closed', 'learndash' ) : __( 'Free', 'learndash' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Late escaped on output
								echo esc_html( $label );
						endif;

						if ( isset( $course_pricing['type'] ) && 'subscribe' === $course_pricing['type'] ) :
							?>
							<span class="ld-text ld-recurring-duration">
									<?php
									echo sprintf(
										// translators: Recurring duration message.
										esc_html_x( 'Every %1$s %2$s', 'Recurring duration message', 'learndash' ),
										esc_html( $course_pricing['interval'] ),
										esc_html( $course_pricing['frequency'] )
									);
									?>
							</span>
						<?php endif; ?>
				</span>
			</div>

			<?php
			/**
			 * Fires after the infobar price cell.
			 *
			 * @param string|false $post_type Post type slug.
			 * @param int          $course_id Course ID.
			 * @param int          $user_id   User ID.
			 */
			do_action( 'learndash-course-infobar-price-cell-after', get_post_type(), $course_id, $user_id );
			?>

		</div> <!--/.ld-course-status-segment-->

		<?php
		/**
		 * Fires inside the un-enrolled course infobox before the action.
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'learndash-course-infobar-noaccess-action-before', get_post_type(), $course_id, $user_id );

		/**
		 * Filters infobar course status segment CSS class.
		 *
		 * @param string $segment_class The List of course segment CSS class.
		 */
		$course_status_class = apply_filters(
			'ld-course-status-segment-class',
			'ld-course-status-segment ld-course-status-seg-action status-' .
			( isset( $course_pricing['type'] ) ? sanitize_title( $course_pricing['type'] ) : '' )
		);
		?>

		<div class="<?php echo esc_attr( $course_status_class ); ?>">
			<span class="ld-course-status-label"><?php echo esc_html_e( 'Get Started', 'learndash' ); ?></span>
			<div class="ld-course-status-content">
				<div class="ld-course-status-action">
					<?php
						/**
						 * Fires before the course infobar action cell.
						 *
						 * @param string|false $post_type Post type slug.
						 * @param int          $course_id Course ID.
						 * @param int          $user_id   User ID.
						 */
						do_action( 'learndash-course-infobar-action-cell-before', get_post_type(), $course_id, $user_id );

						$login_model = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Theme_LD30', 'login_mode_enabled' );

						/** This filter is documented in themes/ld30/includes/shortcode.php */
						$login_url = apply_filters( 'learndash_login_url', ( 'yes' === $login_model ? '#login' : wp_login_url( get_permalink() ) ) );

					switch ( $course_pricing['type'] ) {
						case ( 'open' ):
						case ( 'free' ):

							/**
							 * Filters whether to show login modal.
							 *
							 * @param boolean $show_login_modal Whether to show login modal.
							 * @param int     $course_id        Course ID.
							 * @param int     $user_id          User ID.
							 */
							if ( apply_filters( 'learndash_login_modal', true, $course_id, $user_id ) && ! is_user_logged_in() ) :
								echo '<a class="ld-button" href="' . esc_url( $login_url ) . '">' . esc_html__( 'Login to Enroll', 'learndash' ) . '</a></span>';
								else :
									echo learndash_payment_buttons( $post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputs Payment button HTML
								endif;
							break;
						case ( 'paynow' ):
						case ( 'subscribe' ):
							// Price (Free / Price)
							$ld_payment_buttons = learndash_payment_buttons( $post );
							echo $ld_payment_buttons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputs Button HTML

							/** This filter is documented in themes/ld30/templates/modules/infobar/course.php */
							if ( apply_filters( 'learndash_login_modal', true, $course_id, $user_id ) && ! is_user_logged_in() ) :
								echo '<span class="ld-text">';
								if ( ! empty( $ld_payment_buttons ) ) {
									esc_html_e( 'or', 'learndash' );
								}
								echo '<a class="ld-login-text" href="' . esc_url( $login_url ) . '">' . esc_html__( 'Login', 'learndash' ) . '</a></span>';
								endif;
							break;
						case ( 'closed' ):
							$button = learndash_payment_buttons( $post );
							if ( empty( $button ) ) :
								echo '<span class="ld-text">' . esc_html__( 'This course is currently closed', 'learndash' ) . '</span>';
								else :
									echo $button; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Outputs Button HTML
								endif;
							break;
					}

					/**
					 * Fires after the course infobar action cell.
					 *
					 * @param string|false $post_type Post type slug.
					 * @param int          $course_id Course ID.
					 * @param int          $user_id   User ID.
					 */
					do_action( 'learndash-course-infobar-action-cell-after', get_post_type(), $course_id, $user_id );
					?>
				</div>
			</div>
		</div> <!--/.ld-course-status-action-->

		<?php
		/**
		 * Fires inside the un-enrolled course infobox after the price
		 *
		 * @since 3.0.0
		 *
		 * @param string|false $post_type Post type slug.
		 * @param int          $course_id Course ID.
		 * @param int          $user_id   User ID.
		 */
		do_action( 'learndash-course-infobar-noaccess-price-after', get_post_type(), $course_id, $user_id );
		?>

	</div> <!--/.ld-course-status-->

<?php endif; ?>