<?php
/**
 * List of plugin optins, contains only default values, actual values are stored in database
 * and can be changed by corresponding wordpress function calls
 */
$config = array(
    "info_api_url" => "https://www.wpallimport.com",
	"history_file_count" => 10000,
	"history_file_age" => 365,
	"highlight_limit" => 10000,
	"upload_max_filesize" => 2048,
	"post_max_size" => 2048,
	"max_input_time" => -1,
	"max_execution_time" => -1,
	"dismiss" => 0,
	"dismiss_speed_up" => 0,
	"html_entities" => 0,
	"utf8_decode" => 0,
	"cron_job_key" => wp_all_import_url_title(wp_all_import_rand_char(12)),
	"chunk_size" => 32,
	"pingbacks" => 1,
	"legacy_special_character_handling" => 1,
	"case_sensitive" => 1,
	"session_mode" => 'default',
	"enable_ftp_import" => 0,
	"large_feed_limit" => 1000,	
	"cron_processing_time_limit" => 59,
	"secure" => 1,
	"log_storage" => 5,
	"cron_sleep" => "",
	"port" => "",
	"google_client_id" => "",
	"google_signature" => "",
	"licenses" => array(),
	"statuses" => array(),
	"force_stream_reader" => 0,
    "scheduling_license" => "",
    "scheduling_license_status" => "",
);if (!defined('WPALLIMPORT_SIGNATURE')) define('WPALLIMPORT_SIGNATURE', 'MzE3MTZiZGY4MzRmNzgzODY4OTI4NWNlMTU1ZTdhNjQ=');