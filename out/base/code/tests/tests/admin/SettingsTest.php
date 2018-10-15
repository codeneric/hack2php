<?php
use \codeneric\phmm\base\admin\Settings;

// PHPUNIT assertions: https://phpunit.de/manual/4.8/en/writing-tests-for-phpunit.html#writing-tests-for-phpunit.test-dependencies

final class SettingsTest extends Codeneric_UnitTest {

  public function setUp() {
    // delete_option(Settings::option_name);
  }

  public function testDefaultConfiguration() {
    $this->assertSame(
      [
        'hide_admin_bar' => false,
        'accent_color' => '#0085ba',
        'cc_photo_image_box' => false,
        'cc_photo_enable_styling' => true,
        'cc_photo_lightbox_theme' => 'dark',
        'page_template' => '',
        'custom_css' => '',
        'remove_images_on_project_deletion' => false,
        'canned_emails' => [],
        'max_zip_part_size' => 10,
      ],
      Settings::getDefaultSettings()    );
  }
  public function testUntouchedConfiguration() {
    $this->assertSame(
      Settings::getDefaultSettings(),
      Settings::getCurrentSettings()    );
  }

}
