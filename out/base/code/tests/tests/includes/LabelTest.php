<?php
use \codeneric\phmm\base\includes\Labels;
use \Eris\Generator;
// PHPUNIT assertions: https://phpunit.de/manual/4.8/en/writing-tests-for-phpunit.html#writing-tests-for-phpunit.test-dependencies

final class LabelTest extends Codeneric_UnitTest {

  public function setUp() {

    parent::setUp();
    self::makeAdministrator();
  }

  private function internalLabelCount() {
    return count($this->makeInternalLabelArray());
  }
  private function makeInternalLabelArray() {
    $arr = array();
    foreach (\codeneric\phmm\base\includes\InternalLabelID::getValues() as
             $name => $id) {

      array_push($arr, array('id' => $id, 'name' => $name));
    }

    return $arr;
  }
  private function assertOnlyInternalLabelsExist() {

    $this->assertEquals(
      Labels::get_all_labels(),
      $this->makeInternalLabelArray()    );
  }

  public function testInternalLabelsShouldBeLoadedOnInit() {
    /* loaded on init */

    $this->assertSame(
      get_option(Labels::allLabelsOptionName),
      array(array("id" => "1111111111111", "name" => "Favorites"))    );
    /* consecutive inits dont mess up */
    Labels::initLabelStore();
    $this->assertSame(
      get_option(Labels::allLabelsOptionName),
      array(array("id" => "1111111111111", "name" => "Favorites"))    );

  }

  public function testGetAllLabels() {

    $this->assertSame(
      Labels::get_all_labels(),
      array(array("id" => "1111111111111", "name" => "Favorites"))    );
  }

  public function testGetLabelByName() {
    $this->forAll(
      Generator\filter(
        function($n) {
          return $n !== "Favorites";
        },
        Generator\names()      )    )->then(
      function($name) {
        $this->assertSame([], Labels::get_label_by_name($name));
      }    );

    $this->assertSame(
      Labels::get_label_by_name("Favorites"),
      array(array("id" => "1111111111111", "name" => "Favorites")),
      "Should return an array of label shapes matched to the name"    );

  }

  public function testCreateNewLabel() {
    $this->predictError(); // no empty name
    Labels::update_label("", null);
  }
  public function testCreateNewLabel2() {
    $this->predictError(); // no empty string ID
    Labels::update_label("Label", "");
  }

  public function testCreateNewLabel3() {

    $this->assertOnlyInternalLabelsExist();

    Labels::update_label("Hans", null);

    $this->assertCount(
      $this->internalLabelCount() + 1,
      Labels::get_all_labels()    );

    $match = array_filter(
      Labels::get_all_labels(),
      function($label) {
        return $label['name'] === "Hans";
      }    );
    $this->assertTrue(is_array($match));
    $this->assertCount(1, $match);
  }
  public function testUpdateCreatedLabel() {
    Labels::update_label("Hans", null);
    $this->assertCount(
      $this->internalLabelCount() + 1,
      Labels::get_all_labels()    );

    $match = array_values(
      array_filter(
        Labels::get_all_labels(),
        function($label) {
          return $label['name'] === "Hans";
        }      )    );

    // update the label name
    Labels::update_label("Dieter", $match[0]['id']);

    $this->assertArraySubset(
      array(1 => array("id" => $match[0]['id'], "name" => "Dieter")),
      Labels::get_all_labels()    );
    $this->assertCount(
      $this->internalLabelCount() + 1,
      Labels::get_all_labels()    );
  }

  public function testGetLabelIdByName() {
    $this->assertSame(
      Labels::get_label_id_by_name("Favorites"),
      "1111111111111"    );

    $this->assertNull(Labels::get_label_id_by_name(""));
    $this->assertNull(Labels::get_label_id_by_name("nonexistent"));

    $this->assertOnlyInternalLabelsExist();

  }
  public function testGetLabelIdByName2() {
    // create label with duplicate name
    Labels::update_label("Favorites", null);
    $this->predictError();
    Labels::get_label_id_by_name("Favorites");
  }
  public function testGetLabelIdByName3() {
    // create label with duplicate name
    Labels::update_label("Favorites", null);
    Labels::update_label("Favorites", null);
    $this->predictError();
    Labels::get_label_id_by_name("Favorites");
  }

  public function testDeleteLabel() {
    $this->assertOnlyInternalLabelsExist();
    $this->assertFalse(Labels::delete_label("nonexistent"));
    $this->assertOnlyInternalLabelsExist();

  }
  public function testDeleteLabel2() {

    $this->predictError();
    Labels::delete_label("1111111111111");
  }
  public function testDeleteLabel3() {
    Labels::update_label("Labelname", null);

    $this->assertCount(
      $this->internalLabelCount() + 1,
      Labels::get_all_labels()    );
    $label = Labels::get_label_by_name("Labelname");

    $this->assertTrue(Labels::delete_label($label[0]['id']));

    $this->assertOnlyInternalLabelsExist();

  }

  public function testGetLabelSet() {
    // test that empty label string is not possible.
    $this->predictError();
    Labels::get_set(0, 0, "");
  }
  public function testGetLabelSet2() {
    // test that clientID must be int.
    $this->predictError();
    /* UNSAFE_EXPR */
    Labels::get_set("", 0, "something");
  }
  public function testGetLabelSet3() {
    // test that projectID must be int.
    $this->predictError();
    /* UNSAFE_EXPR */
    Labels::get_set(0, "some", "something");
  }
  public function testGetLabelSet4() {
    $this->assertSame(
      Labels::get_set(0, 0, "something"),
      array(),
      "Non-existent set should return empty array"    );
  }
  public function testGetLabelSet5() {

    // mock an entry
    $clientID = 42;
    $projectID = 15789;
    $labelID = "something";
    $name = "codeneric/phmm/labels/".md5("$clientID/$projectID/$labelID");
    update_option($name, array(1, 4, 7, 2, 5, 8));

    $this->assertSame(
      Labels::get_set($clientID, $projectID, $labelID),
      array(1, 4, 7, 2, 5, 8)    );
  }
  public function testSaveSet() {
    // test that empty label string is not possible.
    $this->predictError();
    Labels::save_set(0, 0, "", array());
  }
  public function testSaveSet2() {
    // test that clientID must be int.
    $this->predictError();
    /* UNSAFE_EXPR */
    Labels::save_set("", 0, "something", array());
  }
  public function testSaveSet3() {
    // test that projectID must be int.
    $this->predictError();
    /* UNSAFE_EXPR */
    Labels::save_set(0, "some", "something", array());
  }
  public function testSaveSet4() {

    $name = "codeneric/phmm/labels/".md5("42/15789/something");
    $this->assertFalse(get_option($name));
    $this->assertTrue(
      Labels::save_set(42, 15789, "something", array(9, 8, 7))    );

    $this->assertSame(get_option($name), array(9, 8, 7));

  }
  public function testSaveSet5() {
    $clientID = 42;
    $projectID = 15789;
    $labelID = "something";
    $name = "codeneric/phmm/labels/".md5("$clientID/$projectID/$labelID");

    $this->assertTrue(
      Labels::save_set($clientID, $projectID, $labelID, array(9, 8, 7))    );
    $this->assertSame(get_option($name), array(9, 8, 7));

    $this->assertFalse(
      Labels::save_set($clientID, $projectID, $labelID, array(9, 8, 7)),
      "Returns false when trying to update with same input"    );
    $this->assertTrue(
      Labels::save_set(
        $clientID,
        $projectID,
        $labelID,
        array(123, 456, 789)      )    );

    $this->assertSame(get_option($name), array(123, 456, 789));

  }
  public function testDeleteSet() {
    // test that empty label string is not possible.
    $this->predictError();
    Labels::delete_set(0, 0, "");
  }
  public function testDeleteSet2() {
    // test that clientID must be int.
    $this->predictError();
    /* UNSAFE_EXPR */
    Labels::delete_set("", 0, "something");
  }
  public function testDeleteSet3() {
    // test that projectID must be int.
    $this->predictError();
    /* UNSAFE_EXPR */
    Labels::delete_set(0, "some", "something");
  }
  public function testDeleteSet4() {
    $clientID = 42;
    $projectID = 15789;
    $labelID = "something";

    $this->assertTrue(
      Labels::save_set($clientID, $projectID, $labelID, array(1, 2, 3))    );

    $this->assertNotEmpty(Labels::get_set($clientID, $projectID, $labelID));

    $this->assertTrue(Labels::delete_set($clientID, $projectID, $labelID));

    $this->assertEmpty(Labels::get_set($clientID, $projectID, $labelID));
  }

}
