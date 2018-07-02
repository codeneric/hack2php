<?php

use \codeneric\phmm\premium\includes\WriteCommentShape;
use \codeneric\phmm\premium\includes\ReadCommentShape;
use \codeneric\phmm\premium\includes\CommentService;
use \Eris\Generator;
final class CommentServiceTest extends Codeneric_UnitTest_Premium {
  public static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    
      
    /*UNSAFE_EXPR*/
        $wpdb = $GLOBALS['wpdb'];
        /*UNSAFE_EXPR*/
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = "codeneric_phmm_comments";
        /*
                if(UAM_Config::$ENV === 'development'){
                    $wpdb->query( " DROP TABLE $table_name"	); 
                }*/ 
 
        
        /*UNSAFE_EXPR*/
        $sql = "CREATE TABLE $table_name (
		  id   bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
		  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  content   text DEFAULT '' NOT NULL,
		  project_id   bigint(20) UNSIGNED NOT NULL,
		  attachment_id   bigint(20) UNSIGNED NOT NULL,
		  wp_user_id   bigint(20) UNSIGNED NOT NULL,
		  client_id   bigint(20) UNSIGNED NOT NULL,
		  UNIQUE KEY id (id)
		) $charset_collate;";

        
        dbDelta( /*UNSAFE_EXPR*/ $sql );
    
  }
  
  private
    $write_comment = array(
      'attachment_id' => 0,
      'wp_user_id' => 0,
      'project_id' => 0,
      'content' => "test",
      'client_id' => 0,
    );

  public function setUp() {
    parent::setUp();

    // // probably better to use wpdb query or insert here to avoid inconsistency
    // // i.e. at this point we to do not know if save_comment works
    // $state = CommentService::save_comment($this->write_comment);

    // $this->assertSame(
    //   $state,
    //   true,
    //   'Saving the test comment during setup failed!',
    // );
  }

  public function tearDown() {
    parent::tearDown();

    /*UNSAFE_EXPR*/
    $wpdb = $GLOBALS['wpdb'];
    // get comment count with image id
    /*UNSAFE_EXPR*/
    $wpdb->delete(
      'codeneric_phmm_comments',
      array(
        'attachment_id' => 0,
        'wp_user_id' => 0,
        'project_id' => 0,
        'content' => "test",
        'client_id' => 0,
      )    );
  }

  private function prepareComment() {
    $state = CommentService::save_comment($this->write_comment);
    $this->assertSame(
      $state,
      true,
      'Saving the test comment during setup failed!'    );
  }

  public function testGetCommentsCount() {
    $this->prepareComment();
    // need to setup array with image ids to match get_comments_count function parameters
    $image_id = array(0);
    // call comment api
    $count = CommentService::get_comments_counts($image_id);
    // count is an array and is expected to be size 1
    $this->assertSame(is_array($count), true, 'Count is not an array!');
    $this->assertSame(
      count($count),
      1,
      'Size of count array is expected to be 1.'    );
    // image id is 0, so we check field 0 for the count of comments for image 0
    // expected to be 1 according to setup.
    $this->assertSame(
      $count[0],
      1,
      'Comment count for image_id 0 is expected to be 1.'    );
  }

  public function testGetComentsCountForProject() {
    $this->prepareComment();
    // call comment api
    $count = CommentService::get_comments_counts_for_project(0);

    // testing method is equal to get_comments_count since get_comments_counts_for_project
    // is just a wrapper to use project id for requesting comment counts
    // count is an array and is expected to be size 1
    $this->assertSame(is_array($count), true, 'Count is not an array!');
    $this->assertSame(
      count($count),
      1,
      'Size of count array is expected to be 1.'    );
    // image id is 0, so we check field 0 for the count of comments for image 0
    // expected to be 1 according to setup.
    $this->assertSame(
      $count[0],
      1,
      'Comment count for image_id 0 is expected to be 1.'    );
  }

  public function testSaveComment() {
    $this->prepareComment();
    // prepare query_result var
    $query_result = array();
    // query api and try to save comment
    $bool = CommentService::save_comment($this->write_comment);
    // check if save_comment returns true to signalise successful query with db
    $this->assertSame($bool, true, 'Something went wrong saving a comment.');
    // manually get comment and check content
    // fields that will not be checked are id, time
    // prepare query to get test comment
    $query =
      "SELECT * FROM codeneric_phmm_comments WHERE attachment_id = 0 AND content='test' AND project_id = 0 AND wp_user_id = 0 AND client_id = 0";
    /*UNSAFE_EXPR*/
    $wpdb = $GLOBALS['wpdb'];
    // query db for test entry
    /*UNSAFE_EXPR*/
    $query_result = $wpdb->get_results($query, ARRAY_A);
    // check if query result is an array
    $this->assertSame(
      is_array($query_result), 
      true,
      'Query result is not an array.'    );
    // expected size here is 2, since we have the setup entry and the entry we are saving to check
    // function save_comment
    $this->assertSame(
      count($query_result),
      2,
      'Query result array size is expected to be 2.'    );
    // get entry of query result array
    $entry = $query_result[0];
    // check for values in entry
    $this->assertSame(
      intval($entry['attachment_id']),
      0,
      'Attachment id='.$entry['attachment_id'].' does not match.'    );
    $this->assertSame(
      intval($entry['wp_user_id']),
      0,
      'WP user id='.$entry['wp_user_id'].' does not match.'    );
    $this->assertSame(
      intval($entry['project_id']),
      0,
      'Project id='.$entry['project_id'].' does not match.'    );
    $this->assertSame(
      $entry['content'],
      "test",
      'Content='.$entry['content'].' does not match.'    );
    $this->assertSame(
      intval($entry['client_id']),
      0,
      'Client id='.$entry['client_id'].' does not match.'    );
  }

  public function testGetCommentsForImage() {
    $this->prepareComment();
    // prepare query_result var
    $query_result = array();
    // query api and try to get comments for image id 0
    $query_result = CommentService::get_comments_for_image(0,0);
    // check if query result is an array and size is 1
    $this->assertSame(
      is_array($query_result),
      true,
      'Query result is not an array.'    );
    $this->assertSame(
      count($query_result),
      1,
      'Query result array size is expected to be 1.'    );
    // get entry of query result array
    $entry = $query_result[0];
    // check for values in entry
    $this->assertSame(
      intval($entry['attachment_id']),
      0,
      'Attachment id='.$entry['attachment_id'].' does not match.'    );
    $this->assertSame(
      intval($entry['wp_user_id']),
      0,
      'WP user id='.$entry['wp_user_id'].' does not match.'    );
    $this->assertSame(
      intval($entry['project_id']),
      0,
      'Project id='.$entry['project_id'].' does not match.'    );
    $this->assertSame(
      $entry['content'],
      "test",
      'Content='.$entry['content'].' does not match.'    );
    $this->assertSame(
      intval($entry['client_id']),
      0,
      'Client id='.$entry['client_id'].' does not match.'    );
  }

  public function testFuzzyGetCommentsCount() {
    $this->forAll(Generator\seq(Generator\int()))->then(
      function($ids) {
        $count = CommentService::get_comments_counts($ids);

        foreach ($ids as $id) {
          $this->assertEquals(0, $count[$id]);
        }
        // $this->assertEquals(count($array), count(array_reverse($array)));
      }    );
  }

  public function testFuzzyGetCommentsCountForProject() {
    $this->forAll(Generator\int())->then(
      function($id) {
        $count = CommentService::get_comments_counts_for_project($id);
        $this->assertEquals([], $count);
      }    );
  }
  public function testFuzzySaveComment() {
    $this->forAll(
        Generator\associative(
          [
            'attachment_id' => Generator\nat(),
            'wp_user_id' => Generator\nat(),
            'project_id' => Generator\nat(),
            'content' => Generator\string(),
            'client_id' => Generator\nat(),
          ]        )      )
      ->then(
        function($comment) {
          $bool = CommentService::save_comment($comment);
          // check if save_comment returns true to signalise successful query with db
          $this->assertSame(
            $bool,
            true,
            'Something went wrong saving a comment.'          );

          $query_result = [];

          $wpdb = /*UNSAFE_EXPR*/ $GLOBALS['wpdb'];

          $s = esc_sql($comment['content']);
          $query =
            "SELECT * FROM codeneric_phmm_comments WHERE attachment_id = $comment['attachment_id'] AND content='$s' AND project_id = $comment['project_id'] AND wp_user_id = $comment['wp_user_id'] AND client_id = $comment['client_id']";

          // query db for test entry
          /*UNSAFE_EXPR*/
          $query_result = $wpdb->get_results($query, ARRAY_A);
          // check if query result is an array
          $this->assertSame(
            is_array($query_result),
            true,
            'Query result is not an array.'          );

          // get entry of query result array
          $entry = $query_result[0];
          // check for values in entry
          $this->assertSame(
            intval($entry['attachment_id']),
            $comment['attachment_id'],
            'Attachment id='.$entry['attachment_id'].' does not match.'          );
          $this->assertSame(
            intval($entry['wp_user_id']),
            $comment['wp_user_id'],
            'WP user id='.$entry['wp_user_id'].' does not match.'          );
          $this->assertSame(
            intval($entry['project_id']),
            $comment['project_id'],
            'Project id='.$entry['project_id'].' does not match.'          );
          $this->assertSame(
            $entry['content'],
            $comment['content'],
            'Content='.$entry['content'].' does not match.'          );
          $this->assertSame(
            intval($entry['client_id']),
            $comment['client_id'],
            'Client id='.$entry['client_id'].' does not match.'          );
        }      );
  }

  public function testFuzzyGetCommentsForImage() {
    $this->forAll(Generator\int())->then(
      function($imageID, $projectID) {
        $count = CommentService::get_comments_for_image($imageID, $projectID);
        $this->assertEquals([], $count);

      }    );
  }

}
