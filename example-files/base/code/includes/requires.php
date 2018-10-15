<?hh //strict
require_once plugin_dir_path(dirname(__FILE__)).'includes/exception.php';
require_once plugin_dir_path(dirname(__FILE__)).'includes/error.php';
require_once plugin_dir_path(dirname(__FILE__)).'../vendor/autoload.php';

require_once
  plugin_dir_path(dirname(__FILE__)).'admin/schema/validators.php'
;

require_once plugin_dir_path(dirname(__FILE__)).'includes/logger.php';
// require_once
//   plugin_dir_path(dirname(__FILE__)).'includes/background-process.php'
// ;

require_once plugin_dir_path(dirname(__FILE__)).'includes/watermarker.php';

require_once
  plugin_dir_path(dirname(__FILE__)).'protect_images/generate_htaccess.php'
;
require_once
  plugin_dir_path(dirname(__FILE__)).'protect_images/security-logic.php'
;
require_once plugin_dir_path(dirname(__FILE__)).'protect_images/protect.php';

require_once plugin_dir_path(dirname(__FILE__)).'types.php';
require_once plugin_dir_path(dirname(__FILE__)).'enums.php';
require_once plugin_dir_path(dirname(__FILE__)).'utils.php';
require_once plugin_dir_path(dirname(__FILE__)).'shapesfn.php';
require_once plugin_dir_path(dirname(__FILE__)).'configuration.php';

require_once plugin_dir_path(dirname(__FILE__)).'includes/loader.php';

require_once plugin_dir_path(dirname(__FILE__)).'includes/i18n.php';
require_once plugin_dir_path(dirname(__FILE__)).'includes/email.php';
require_once plugin_dir_path(dirname(__FILE__)).'includes/semaphore.php';
require_once plugin_dir_path(dirname(__FILE__)).'admin/dbupdater.php';
require_once plugin_dir_path(dirname(__FILE__)).'includes/project.php';
require_once plugin_dir_path(dirname(__FILE__)).'includes/client.php';
require_once plugin_dir_path(dirname(__FILE__)).'includes/fileStream.php';
require_once plugin_dir_path(dirname(__FILE__)).'includes/labels.php';
require_once plugin_dir_path(dirname(__FILE__)).'includes/image.php';
require_once plugin_dir_path(dirname(__FILE__)).'includes/superglobals.php';
require_once plugin_dir_path(dirname(__FILE__)).'includes/privacy.php';

require_once (ABSPATH.WPINC.'/class-phpass.php');
require_once plugin_dir_path(dirname(__FILE__)).'includes/permission.php';

require_once plugin_dir_path(dirname(__FILE__)).'admin/settings.php';
require_once plugin_dir_path(dirname(__FILE__)).'admin/premium_page.php';
require_once plugin_dir_path(dirname(__FILE__)).'admin/support_page.php';
require_once
  plugin_dir_path(dirname(__FILE__)).'admin/interactions_page.php'
;
require_once plugin_dir_path(dirname(__FILE__)).'admin/frontendHandler.php';
require_once plugin_dir_path(dirname(__FILE__)).'admin/ajax/helper.php';
require_once plugin_dir_path(dirname(__FILE__)).'admin/ajax/request.php';

require_once plugin_dir_path(dirname(__FILE__)).'admin/admin.php';
require_once plugin_dir_path(dirname(__FILE__)).'admin/ajax/endpoints.php';
require_once plugin_dir_path(dirname(__FILE__)).'public/public.php';

