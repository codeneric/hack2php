<?hh //strict

namespace codeneric\phmm;
use codeneric\phmm\base\admin\FrontendHandler;
use codeneric\phmm\base\includes\Error;
use codeneric\phmm\base\globals\Superglobals;
use codeneric\phmm\Logger;

use \codeneric\phmm\enums\SemaphoreExecutorReturn;
use codeneric\phmm\Semaphore;

require_once (dirname(__FILE__).'/admin.php');
require_once (dirname(__FILE__).'/../protect_images/generate_htaccess.php');
require_once (ABSPATH.'wp-admin/includes/upgrade.php');

require_once (dirname(__FILE__).'/schema/legacy/read_3_6_5.php');
require_once (dirname(__FILE__).'/schema/legacy/map.php');
require_once (dirname(__FILE__).'/schema/legacy/write_4_0_0.php');
require_once (dirname(__FILE__).'/schema/legacy/legacy_validators.php');

class FunctionContainer {

  public function update_to_1_1_0(): void { //update from 1.0.1 to 1.1.0
    $options = get_option('cc_photo_settings', array());
    //        $options = array('cc_photo_image_box'=> 1, 'cc_photo_download_text'=> 'Download all' );

    invariant(is_array($options), '%s', new Error("Expected array."));
    // TODO: denis, this does not yield the expected types
    $options['cc_photo_image_box'] = 1;
    $options['cc_photo_download_text'] = 'Download all';
    update_option('cc_photo_settings', $options);

    $posts_array = get_posts("post_type=client");
    foreach ($posts_array as $client) {
      $projects = get_post_meta($client->ID, "projects", true);
      $projects = is_array($projects) ? $projects : array();
      foreach ($projects as $k => $project) {
        $projects[$k]['downloadable'] = true;
      }

      //print_r($projects);
      update_post_meta($client->ID, "projects", $projects);
    }
  }

  public function update_to_2_2_2(): void {

    if (get_option('codeneric_phmm_error_log') === false)
      update_option('codeneric_phmm_error_log', array());
  }

  public function update_to_2_3_0(): void {

    add_role(
      "phmm_client",
      __('PhMm Client'),
      array(
        'read' => true, // true allows this capability
        'edit_posts' => false,
        'delete_posts' => false, // Use false to explicitly deny
      ),
    );
  }

  public function update_to_2_7_0(): void {

    $upload_dir = wp_upload_dir();
    $upload_dir = $upload_dir['basedir'].'/photography_management';

    if (is_link("$upload_dir/protect.php"))
      unlink("$upload_dir/protect.php"); //the htaccess redirects to the actual php-file

    //htaccess
    if (is_link("$upload_dir/.htaccess")) {
      unlink("$upload_dir/.htaccess");
    }

    if (file_exists("$upload_dir/.htaccess"))
      unlink("$upload_dir/.htaccess");

    Photography_Management_Base_Generate_Htaccess("$upload_dir/.htaccess");

  }

  public function update_to_3_2_6(): void {
    $upload_dir = wp_upload_dir();
    $upload_dir = $upload_dir['basedir'].'/photography_management';
    if (file_exists("$upload_dir/.htaccess"))
      unlink("$upload_dir/.htaccess");
    Photography_Management_Base_Generate_Htaccess("$upload_dir/.htaccess");

  }

  public function update_to_3_5_0(): void {

    $query_args = array(
      'posts_per_page' => -1,
      'offset' => 0,
      'post_type' => 'client',
    );

    $posts_array = get_posts($query_args);
    foreach ($posts_array as $client) {
      $projects = get_post_meta($client->ID, "projects", true);
      $projects = is_array($projects) ? $projects : array();
      foreach ($projects as $k => $project) {
        $uniqid = uniqid('', true);
        $uniqid = str_replace('.', '', $uniqid);
        if (/*UNSAFE_EXPR*/ empty($projects[$k]['id'])) {
          $projects[$k]['id'] = $uniqid;
        }
      }
      update_post_meta($client->ID, "projects", $projects);
    }
  }

  public function update_to_4_0_0(
  ): shape(
    "failed" => array<int>,
    "finished" => array<int>,
    "outstanding" => array<int>,
  ) {

    // $this->decouple_projects();
    /////////////// BEGIN Database Stuff ////////////////////////

    Logger::info("Starting update to 4.0.0");
    Logger::info("Memory allocated in beginning: ".memory_get_usage());

    try {
      $memory_limit = ini_get('memory_limit');

    } catch (\Exception $e) {
      $memory_limit = "not retrievable";
    }
    Logger::info("Memory limit: ".$memory_limit);
    $startTime = microtime(true);
    $wpdb = Superglobals::Globals('wpdb');
    invariant(
      $wpdb instanceof \wpdb,
      '%s',
      new Error('wpdb is not available!'),
    );

    $charset_collate = $wpdb->get_charset_collate();
    $table_name = "codeneric_phmm_comments";

    $sql = "CREATE TABLE $table_name (
      id   bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      content   text DEFAULT '' NOT NULL,
      project_id   bigint(20) UNSIGNED NOT NULL,
      attachment_id   bigint(20) UNSIGNED NOT NULL,
      wp_user_id   bigint(20) UNSIGNED NOT NULL,
      client_id   bigint(20) UNSIGNED NOT NULL,
          wp_author_id   bigint(20) UNSIGNED NOT NULL,
      UNIQUE KEY id (id)
    ) $charset_collate;";

    dbDelta(/*UNSAFE_EXPR*/ $sql);
    Logger::info("Comments Table created");
    /////////////// END Database Stuff ////////////////////////

    /////////////// BEGIN Plugin Settings ////////////////////////
    try {
      $plugin_settings = \codeneric\phmm\legacy\v3_6_5\read_plugin_settings();
      Logger::info("Old Plugin Settings");
      Logger::info(json_encode($plugin_settings));

      $plugin_settings_4_0_0 =
        \codeneric\phmm\legacy\map_plugin_settings_from_3_6_5(
          $plugin_settings,
        );
      Logger::info("New Plugin Settings");

      Logger::info(json_encode($plugin_settings_4_0_0));
      update_option('codeneric_phmm_plugin_settings', $plugin_settings_4_0_0);
      Logger::info("Plugin settings successfully migrated");
    } catch (\Exception $e) {
      Logger::error(
        "Migrating plugin settings failed! ".$e->__toString(),
        [
          "memory" => memory_get_usage(),
          "seconds_passed" => microtime(true) - $startTime,
        ],
      );
    }
    /////////////// END Plugin Settings ////////////////////////
    $mutex_name = "update_to_4_0_0";
    $all_client_ids = function() {
      $query_args = array(
        'posts_per_page' => -1,
        'offset' => 0,
        'post_type' => 'client',
      );

      $posts_array = get_posts($query_args);
      $ids = [];
      foreach ($posts_array as $p) {
        $ids[] = (int) $p->ID;
      }
      return $ids;
    };
    $semaphore_state = Semaphore::get_state($mutex_name, $all_client_ids);
    Logger::info("update_to_4_0_0 get_state:", $semaphore_state);

    $caller = function(int $client_id) {
      if ("%%PLUGIN_ENV%%" === "development")
        sleep(11);

      $client = get_post_meta($client_id, "client", true);

      try {
        Logger::info(
          "Migrate client data representation for ID ".$client_id,
          $client,
        );

        // Logger::info(
        //   "System health",
        //   [
        //     "memory" => memory_get_usage(),
        //     "seconds_passed" => microtime(true) - $startTime,
        //   ],
        // );

        $client =
          \codeneric\phmm\legacy\validate\client_data_representation_3_6_5(
            $client,
          );

        Logger::info(
          "Got v3.6.5 client for ID ".$client_id." was successfull!",
          $client,
        );

        Logger::info("Getting projects for client ID ".$client_id);
        $projects = \codeneric\phmm\legacy\v3_6_5\read_projects($client_id);
        Logger::info("Success! Got ".count($projects)." projects");
        $project_ids = [];
        foreach ($projects as $key => $project) {
          Logger::info("Migrating project with ID ".$project['id']);
          // Logger::info(
          //   "System health",
          //   [
          //     "memory" => memory_get_usage(),
          //     "seconds_passed" => microtime(true) - $startTime,
          //   ],
          // );

          $project_4_0_0 = \codeneric\phmm\legacy\map_project_from_3_6_5(
            $project,
            $client['pwd'],
            false,
          );

          Logger::info(
            "Success migrating project with ID ".$project['id'],
            $project_4_0_0,
          );
          $pid = wp_insert_post(
            [
              'post_title' => $project['title'],
              'post_type' => 'project',
              'post_status' => get_post_status($client_id),
              'post_password' => $client['pwd'],
              'post_content' => $project['description'],
            ],
          );
          if (is_int($pid) && $pid !== 0) {
            Logger::info(
              "Inserted WP Post for project with ID ".$project['id'],
            );
            \codeneric\phmm\legacy\v4_0_0\save_project($pid, $project_4_0_0);
            Logger::info("Saved metadata");
            $project_ids[] = $pid;

            if (!is_null($client['wp_user_id'])) {
              // map comments
              Logger::info(
                "Mapping comments for project with ID ".$project['id'],
              );
              foreach ($project['gallery'] as $i) {
                $comments = \codeneric\phmm\legacy\v3_6_5\read_comments($i);

                foreach ($comments as $c) {
                  $wp_user_id = $client['wp_user_id'];
                  $wp_user_id_of_client =
                    !is_null($wp_user_id) ? $wp_user_id : 0;
                  Logger::info("Got v3.6.5 comment", $c);
                  $comment_4_0_0 =
                    \codeneric\phmm\legacy\map_comment_from_3_6_5(
                      $c,
                      $pid,
                      $wp_user_id_of_client,
                    );
                  Logger::info("Mapped to v4.0.0 comment", $comment_4_0_0);
                  \codeneric\phmm\legacy\v4_0_0\save_comment($comment_4_0_0);
                  Logger::info("Saved comment");
                }
              }
              //map favs

              Logger::info("Saving favorites");
              \codeneric\phmm\legacy\v4_0_0\save_lable_set(
                $client_id,
                $pid,
                $project['starred'],
              );
              Logger::info("Saving favorites successfull");
            }

          }
        }
        if (!is_null($client['wp_user_id'])) {
          update_post_meta($client_id, 'wp_user', $client['wp_user_id']);
          $client_4_0_0 = \codeneric\phmm\legacy\map_client_from_3_6_5(
            $client,
            $project_ids,
          );
          Logger::info(
            "Migrated client to v4.0.0. for ID ".$client_id,
            $client,
          );
          \codeneric\phmm\legacy\v4_0_0\save_client(
            $client_id,
            $client_4_0_0,
          );
          Logger::info("Client saved", $client);
        }

      } catch (\Exception $e) {
        Logger::error(
          "Migrating client with id ".$client_id."failed! ".$e->__toString(),
          [
            "memory" => memory_get_usage(),
          // "seconds_passed" => microtime(true) - $startTime,
          ],
        );
        return SemaphoreExecutorReturn::Failed;
      }
      return SemaphoreExecutorReturn::Finished;

    };
    Logger::info("Starting migration of clients...", $semaphore_state);
    $semaphore_state = Semaphore::run($mutex_name, $semaphore_state, $caller);
    if (!is_null($semaphore_state)) {
      Logger::info("new clients migration state:", $semaphore_state);
      Semaphore::set_state($mutex_name, $semaphore_state);
    }

    $semaphore_state = Semaphore::get_state($mutex_name, $all_client_ids);
    if (count($semaphore_state['outstanding']) === 0) {
      Logger::info("Clients migration finished:", $semaphore_state);
      Semaphore::delete_state($mutex_name);
    }

    return $semaphore_state;

    // $query_args = array(
    //   'posts_per_page' => -1,
    //   'offset' => 0,
    //   'post_type' => 'client',
    // );

    // $posts_array = get_posts($query_args);
    // Logger::info("Starting migration of clients");
    // foreach ($posts_array as $client_post) {

    // }
    // Logger::info("Finished updating to 4.0.0");
  }

  public function legacy(
    \codeneric\phmm\type\configuration\I $cc_phmm_config,
  ): void {
    //        $pf = get_option( 'cc_phmm_pf' );

    $p = get_option('cc_prem');

    if ($p && !$cc_phmm_config['has_premium_ext']) {
      // FrontendHandler::
      $cc_phmm_base_admin_notice_update_to_premium =
        function() {

          $class = "notice notice-error";
          //                $d_url =  $cc_phmm_config['wpps_url'] . '/premium/phmm?plugin_id=' . get_option('cc_photo_manage_id');
          $prem_url = admin_url('edit.php');
          $prem_url = add_query_arg(
            array('post_type' => 'client', 'page' => 'premium'),
            $prem_url,
          );
          $p_url = admin_url('plugins.php');
          $message =
            "Please <a id=\"cc_phmm_install_notice\" href=\"$prem_url\" data-plugins-url=\"$p_url\" >install</a> the Photography Management Premium extension!";
          wp_enqueue_script(
            'cc_phmm_admin_notice',
            plugin_dir_url(__FILE__).'/partials/admin_notice.js',
          );
          $spinner =
            '<div id="cc_phmm_notice_spinner" style="background:url(\'images/spinner.gif\') no-repeat;background-size: 20px 20px;vertical-align: middle;margin: 0 auto;height: 20px;width: 20px;display:none;"></div>';
          echo
            "<div id=\"cc_phmm_notice_wrap\" class=\"$class\"> <p>$spinner $message</p></div>"
          ;
        };
      add_action(
        'admin_notices',
        $cc_phmm_base_admin_notice_update_to_premium,
      );

    } else if ($cc_phmm_config['has_premium_ext'] &&
               !$cc_phmm_config['premium_ext_active']) {
      $cc_phmm_base_admin_notice_update_to_premium =
        function() {
          $class = "notice notice-error";
          $d_url = admin_url('plugins.php');
          $message =
            "Please <a href=\"$d_url\" >activate</a> the Photography Management Premium extension!";
          $script = ""; //"<script> function cc_phmm_handle_download_click(e){e.preventDefault();alert('alles ok');} jQuery('#cc_phmm_download_notice').on('click',cc_phmm_handle_download_click); </script>";
          echo
            "<div id=\"cc_phmm_notice_wrap\" class=\"$class\"> <p>$message</p></div>$script"
          ;
        };
      add_action(
        'admin_notices',
        $cc_phmm_base_admin_notice_update_to_premium,
      );

    }

  }

}
