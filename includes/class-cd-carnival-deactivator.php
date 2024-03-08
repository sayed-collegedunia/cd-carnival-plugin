<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://sayedakhtar.github.io
 * @since      1.0.0
 *
 * @package    Cd_Carnival
 * @subpackage Cd_Carnival/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Cd_Carnival
 * @subpackage Cd_Carnival/includes
 * @author     Sayed Akhtar <sayed.akhtar@collegedunia.com>
 */
class Cd_Carnival_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		// (new Cd_Carnival_Deactivator)->destroy_table();
	}

	private function destroy_table()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . CD_CARNIVAL_DB_TABLE;
		$wpdb->query("DROP TABLE IF EXISTS $table_name");
	}
}
