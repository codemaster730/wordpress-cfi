<?php  // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * CRUD for Help Dialog submissions data
 *
 * @property string primary_key
 * @property string table_name
 */
class EPKB_Help_Dialog_Submissions_DB extends EPKB_DB  {

	const PER_PAGE = 20;

	const STATUS_NEW = 'new';

	const PRIMARY_KEY = 'submission_id';

	/**
	 * Maximum length of public fields
	 */
	 /** ! ===== >  changes will require upgrade to the table **/
	const LOCATION_NAME_LENGTH = 200;  // this has the same length as 'name' field inside the WordPress terms table
	const USER_NAME_LENGTH = 50;
	const USER_EMAIL_LENGTH = 50;
	const SUBJECT_LENGTH = 100;
	const COMMENT_LENGTH = 3000;
	const NOTIFICATION_DETAILS_LENGTH = 500;
	const SEARCH_KEYWORDS_LENGTH = 200;

	/**
	 * Get things started
	 *
	 * @access  public
	 */
	public function __construct() {
		/** @var $wpdb Wpdb */
		global $wpdb;

		$this->table_name  = $wpdb->prefix . 'epkb_hd_submissions';
		$this->primary_key = 'submission_id';
	}

	/**
	 * Get columns and formats
	 *
	 * @access  public
	 */
	public function get_column_format() {
		return array(
			'submission_id'         => '%d',
			'location_name'         => '%s',
			'submission_date'       => '%s',
			'user_name'             => '%s',
			'user_email'            => '%s',
			'subject'               => '%s',
			'comment'               => '%s',
			'status'                => '%s',
			'notification'          => '%s',
			'notification_details'  => '%s',
			'search_keywords'       => '%s',
			'user_ip'               => '%s',
		);
	}

	/**
	 * Get default column values
	 *
	 * @return array
	 */
	public function get_column_defaults() {
		return array(
			'submission_date'       => date('Y-m-d H:i:s'),
			'user_name'             => '',
			'user_email'            => '',
			'subject'               => '',
			'comment'               => '',
			'status'                => '',
			'notification'          => '',
			'notification_details'  => '',
			'search_keywords'       => '',
			'user_ip'               => '',
		);
	}

	/**
	 * Insert a new submission record
	 *
	 * @param $location_name
	 * @param $submission_date
	 * @param $user_name
	 * @param $user_email
	 * @param $subject
	 * @param $comment
	 * @param $status
	 * @param $notification
	 * @param $notification_details
	 * @param $search_keywords
	 * @param $user_ip
	 * @param $tracking
	 *
	 * @return int|WP_Error
	 */
	public function insert_submission( $location_name, $submission_date, $user_name, $user_email, $subject, $comment, $status, $notification, $notification_details, $search_keywords, $user_ip, $tracking ) {

		// insert the record
		$record = array(
			'location_name'         => $location_name,
			'submission_date'       => $submission_date,
			'user_name'             => $user_name,
			'user_email'            => $user_email,
			'subject'               => $subject,
			'comment'               => $comment,
			'status'                => $status,
			'notification'          => $notification,
			'notification_details'  => $notification_details,
			'search_keywords'       => $search_keywords,
			'user_ip'               => $user_ip,
		);

		$submission_id = parent::insert_record( $record );
		if ( empty($submission_id) ) {
			return new WP_Error( 'db-insert-error', 'Could not insert Help Dialog submission record' );
		}

		return $submission_id;
	}

	/**
	 * Update submission record with email status details
	 *
	 * @param $submission_id
	 * @param $notification
	 * @param $notification_details
	 *
	 * @return void|WP_Error
	 */
	public function update_submission( $submission_id, $notification, $notification_details ) {

		// update the record
		$data = array(
			'notification'          => $notification,
			'notification_details'  => $notification_details,
		);

		$result = parent::update_record( $submission_id, $data );
		if ( empty($result) ) {
			return new WP_Error( 'db-update-error', 'Could not update Help Dialog submission record: ' . $submission_id );
		}

		return;
	}

	/**
	 * Get submissions
	 *
	 * @param int $page_number
	 *
	 * @return array|WP_Error
	 */
	public function get_submissions( $page_number=1 ) {

		global $wpdb;

		$page_number = empty( $page_number ) || $page_number < 1 ? 0 : $page_number;
		$offset = ( $page_number - 1 ) * self::PER_PAGE;

		$submissions = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $this->table_name ORDER BY " . self::PRIMARY_KEY . " DESC LIMIT %d, %d ", $offset, self::PER_PAGE ) );

		if ( ! empty( $wpdb->last_error ) ) {
			$wpdb_last_error = $wpdb->last_error;   // add_log changes last_error so store it first
			EPKB_Logging::add_log( "DB failure: ", $wpdb_last_error );
			return new WP_Error( 'DB failure', $wpdb_last_error );
		}

		// always return array - parent class already logs WP Error
		if ( empty( $submissions ) || is_wp_error( $submissions ) || ! is_array( $submissions ) ) {
			$submissions = [];
		}

		// filter output data of submissions
		$submissions = self::filter_output( $submissions );

		return $submissions;
	}

	/**
	 * Delete a submission by primary key
	 *
	 * @param $primary_key
	 * @return bool
	 */
	public function delete_submission( $primary_key ) {
		return $this->delete_record( $primary_key );
	}

	/**
	 * Delete all submissions
	 *
	 * @return bool
	 */
	public function delete_all_submissions() {
		return $this->clear_table();
	}

	/**
	 * Get list of submission's fields with their translated titles which need to display as columns
	 *
	 * @return array
	 */
	public static function get_submission_column_fields() {
		return [
			'submission_date'   		=> __( 'Date', 'echo-knowledge-base' ),
			'user_name'                 => __( 'User Name', 'echo-knowledge-base' ),
			'user_email'                => __( 'User Email', 'echo-knowledge-base' ),
			'location_name'             => __( 'Location Name', 'echo-knowledge-base' ),
			'notification'              => __( 'Notification', 'echo-knowledge-base' ),
		];
	}

	/**
	 * Get list of submission's fields with their translated titles which need to display as rows
	 *
	 * @return array
	 */
	public static function get_submission_row_fields() {
		return [
			'subject' => __( 'Subject', 'echo-knowledge-base' ),
			'comment' => __( 'Comment', 'echo-knowledge-base' ),
		];
	}

	/**
	 * Get list of submission's optional fields with their translated titles which need to display as rows
	 *
	 * @return array
	 */
	public static function get_submission_optional_row_fields() {
		return [
			'notification_details' => __( 'Notification Details', 'echo-knowledge-base' ),
		];
	}

	/**
	 * Get total number of submissions
	 *
	 * @return int
	 */
	public function get_total_number_of_submissions() {
		return $this->get_number_of_rows();
	}

	/**
	 * Update location name from  old value to new value
	 *
	 * @param $old_name
	 * @param $new_name
	 */
	public function update_location_name( $old_name, $new_name ) {
		global $wpdb;

		$result = $wpdb->update(
			$this->table_name,
			array( 'location_name' => $new_name ),
			array( 'location_name' => $old_name ),
			array( '%s' ),
			array( '%s' )
		);
		if ( $result === false && ! empty( $wpdb->last_error ) ) {
			$wpdb_last_error = $wpdb->last_error;
			EPKB_Logging::add_log( "DB failure: ", $wpdb_last_error );
		}
	}

	/**
	 * Filter output of Submissions
	 *
	 * @param $submissions
	 *
	 * @return array
	 */
	private function filter_output( $submissions ) {

		foreach ( $submissions as $index => $data ) {

			// change format of showing submissions datetime
			$submissions[$index]->submission_date = date( 'M j, Y @ g:ia', strtotime( $data->submission_date ) );
		}

		return $submissions;
	}

	/**
	 * Create the table
	 *
	 * @access  public
	 * @since   2.1
	 */
	public function create_table_if_not_exists() {

		// Do nothing if table already exists
		if ( $this->table_exists( $this->table_name ) ) {
			return;
		}

		global $wpdb;

		$collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
	                submission_id BIGINT(20) NOT NULL AUTO_INCREMENT,
	                location_name VARCHAR(" . self::LOCATION_NAME_LENGTH . ") NOT NULL,
	                submission_date datetime NOT NULL,
	                user_name VARCHAR(" . self::USER_NAME_LENGTH . ") NOT NULL,
	                user_email VARCHAR(" . self::USER_EMAIL_LENGTH . ") NOT NULL,
	                subject VARCHAR(" . self::SUBJECT_LENGTH . ") NOT NULL,
	                comment VARCHAR(" . self::COMMENT_LENGTH . ") NOT NULL,
	                status VARCHAR(50) NOT NULL,
	                notification VARCHAR(50) NOT NULL,
	                notification_details VARCHAR(" . self::NOTIFICATION_DETAILS_LENGTH . ") NOT NULL,
	                search_keywords VARCHAR(" . self::SEARCH_KEYWORDS_LENGTH . ") NOT NULL,
	                user_ip VARCHAR(50) NOT NULL,
	                PRIMARY KEY (submission_id)               
		) " . $collate . ";";

		dbDelta( $sql );
	}

	/**
	 * Delete all records in the table
	 *
	 * @return bool|int
	 */
	public function clear_table() {
		global $wpdb;
		return $wpdb->query( "DELETE FROM " . $this->table_name );
	}

	/**
	 * Drop the table
	 *
	 * @return bool|int
	 */
	public function delete_table() {
		global $wpdb;
		return $wpdb->query( "DROP TABLE IF EXISTS " . $this->table_name );
	}
}