<?php

/**
 * Fired during plugin activation
 *
 * @link       https://sayedakhtar.github.io
 * @since      1.0.0
 *
 * @package    Cd_Carnival
 * @subpackage Cd_Carnival/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cd_Carnival
 * @subpackage Cd_Carnival/includes
 * @author     Sayed Akhtar <sayed.akhtar@collegedunia.com>
 */
class Cd_Carnival_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		(new Cd_Carnival_Activator)->create_database_schema();
	}

	private function create_database_schema()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . CD_CARNIVAL_DB_TABLE;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			`id` int NOT NULL AUTO_INCREMENT,
			`name` varchar(255) NOT NULL,
			`email` varchar(255) NOT NULL,
			`phone` varchar(255) NOT NULL,
			`city` varchar(191) DEFAULT NULL,
			`school` varchar(191) DEFAULT NULL,
			`course` varchar(191) DEFAULT NULL,
			`qualification` varchar(191) DEFAULT NULL,
			`ref_code` varchar(191) DEFAULT NULL,
			`registration` varchar(191) DEFAULT NULL,
			`number_of_attendents` int unsigned DEFAULT 1,
			`visited` tinyint DEFAULT 0,
			`ticket_url` varchar(191) DEFAULT NULL,
			`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
    	) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}
