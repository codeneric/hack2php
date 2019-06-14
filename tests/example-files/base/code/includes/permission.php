<?php //strict
namespace codeneric\phmm\base\includes;
use \codeneric\phmm\base\includes\Client;
use \codeneric\phmm\base\includes\Project;
use \codeneric\phmm\Utils;
use \codeneric\phmm\enums\UserState as userState;
use \codeneric\phmm\enums\ProjectState as projectState;
use \codeneric\phmm\enums\ProjectDisplay as display;
use \codeneric\phmm\enums\ClientDisplay as clientDisplay;
use \codeneric\phmm\enums\PortalDisplay as portalDisplay;
use \codeneric\phmm\enums\GuestReviewURLParams;
use \codeneric\phmm\enums\GuestReviewDecisionValues;
use \codeneric\phmm\base\globals\Superglobals;


class Permission {
  public static function current_user_can_access_client($client_id){
    $is_admin = Utils::is_current_user_admin();
    if ($is_admin)
      return true;

    $client = Client::get($client_id);
    if (\is_null($client))
      return false; //client does not exist
    $wp_user = $client['wp_user'];
    if (\is_null($wp_user)) {
      //                throw new Exception('Client-post password is required, but the post has no owner (wp_user_id is empty).'); //post password is required, but the post has no owner (wp_user_id), something went terribly wrong!
      // Here we are...this post requires a password, but has no owner, i.e. no
      // automatically generated wordpress user (PhMm Client) is assigned to this
      // client-post.
      return false;
    }
    $current_user = \wp_get_current_user();
    if ($current_user === 0)
      return false; //simple...user is not logged in -> dismiss
    return $current_user->ID === $wp_user->ID;

  }

  public static function current_user_can_access_project(
$project_id  ){

    $project_state = self::get_project_state($project_id);
    $client_state =
      self::get_client_state_wrt_project($project_state, $project_id);
    $allowed_states = [userState::Admin, userState::Client, userState::Guest];

    return \in_array($client_state, $allowed_states);

  }

  /* You can be logged in as a client and many guests at the same time, so we have to decide which one we select. */
  public static function get_client_id_wrt_project($project_id){

    $project_state = self::get_project_state($project_id);
    $client_state =
      self::get_client_state_wrt_project($project_state, $project_id);
    switch ($client_state) {
      case userState::Client:
        $client = Client::get_current();
        return \is_null($client) ? null : $client['ID'];
        break;
      case userState::Admin:
      case userState::Guest:
        return 0;
        break;
      default:
        return null;
        break;
    }
  }

  public static function get_client_state_wrt_project(
$projectState,
$projectID  ){

    // switch ($projectState) {
    //   case projectState::Public_:
    //     # code...
    //     break;
    //   case projectState::Private_:
    //     # code...
    //     break;
    //   case projectState::PrivateWithGuestLogin:
    //     # code...
    //     break;
    //   case projectState::PrivateWithGuestLoginNoClientsAssigned:
    //     # code...
    //     break;
    // }

    if (!\is_user_logged_in()) {

      // When no guest login exists
      if (
        $projectState !== projectState::PrivateWithGuestLogin &&
        $projectState !==
          projectState::PrivateWithGuestLoginNoClientsAssigned // && $projectState !== projectState::Public_
      )
        return $projectState !== projectState::Public_
          ? userState::NotLoggedIn
          : userState::Guest;

      // no user logged in but we have a guest login.
      // is the user a guest and already provided the pwd?

      $pwdRequired = self::post_password_required($projectID);

      if ($pwdRequired)
        return userState::NotLoggedIn;
      else
        return userState::Guest;
    } else {
      // a user is logged in.
      if (Utils::is_current_user_admin())
        return userState::Admin;

      // either client or random other logged in guy

      $maybeClient = Client::get_current();

      if (\is_null($maybeClient))
        return userState::LoggedInUserWithNoAccess;

      $client = $maybeClient;

      $hasAccess = Client::has_access_to_project($client['ID'], $projectID);

      return
        $hasAccess ? userState::Client : userState::LoggedInUserWithNoAccess;
    }
  }

  public static function get_project_state($projectID){
    $state = Project::get_protection($projectID);

    if ($state["private"] === false)
      return projectState::Public_;

    if (
      $state['password_protection'] === true &&
      !\is_null($state['password']) &&
      $state['password'] !== ""
    ) {

      return (Project::is_assigned_to_at_least_one_client($projectID))
        ? projectState::PrivateWithGuestLogin
        : projectState::PrivateWithGuestLoginNoClientsAssigned;

    }

    return projectState::Private_;

  }

  /*
   * Decider function what to display frontend for projects
   */
  public static function display_project($projectID){

    $projectState = self::get_project_state($projectID);

    $client = self::get_client_state_wrt_project($projectState, $projectID);

    switch ($client) {
      case userState::Client:
        return display::ProjectWithClientConfig;

      case userState::Admin:
        return $projectState === projectState::Public_
          ? display::ProjectWithProjectConfig
          : display::AdminNotice;
      case userState::Guest:
        return $projectState === projectState::Private_
          ? display::LoginForm
          : display::ProjectWithProjectConfig;
      case userState::NotLoggedIn:
        switch ($projectState) {
          case projectState::Private_:
            return display::LoginForm;
          case projectState::PrivateWithGuestLogin:
            return display::SplitLoginView;
          case projectState::PrivateWithGuestLoginNoClientsAssigned:
            return display::PasswordInput;
          case projectState::Public_:
            return display::ProjectWithProjectConfig;

        }

      case userState::LoggedInUserWithNoAccess:
        switch ($projectState) {
          case projectState::Private_:
            return display::NoAccess;
          case projectState::PrivateWithGuestLogin:
          case projectState::PrivateWithGuestLoginNoClientsAssigned:
            return display::PasswordInput;
          case projectState::Public_:
            return display::ProjectWithProjectConfig;

        }
    }

  }

  public static function display_portal(){
    if (Utils::is_current_user_admin())
      return portalDisplay::AdminNotice;

    $c = Client::get_current();
    if (\is_null($c))
      return portalDisplay::LoginForm;

    return portalDisplay::Redirect;

  }

  /*
   * Decider function what to display frontend for projects
   */
  public static function display_client($clientID){
    $G = Superglobals::Get();

    if (
      \array_key_exists(GuestReviewURLParams::DecisionKey, $G) &&
      \array_key_exists(GuestReviewURLParams::SecretKey, $G)
    ) {
      $decision = $G[(string)GuestReviewURLParams::DecisionKey];
      $secret = $G[(string)GuestReviewURLParams::SecretKey];
      if (self::allowed_to_perform_guest_review($clientID, $secret)) {
        return $decision === GuestReviewDecisionValues::Accept
          ? clientDisplay::GuestReviewAccepted
          : clientDisplay::GuestReviewDeclined;

      } else {
        return clientDisplay::ReviewGuestRequestNotPermitted;
      }
    }

    if (Utils::is_current_user_admin())
      return clientDisplay::AdminNoticeWithClientView;

    $c = Client::get_current();
    if (\is_null($c))
      return clientDisplay::LoginForm;

    if (
      $c['ID'] === $clientID &&
      !Client::is_guest($clientID) &&
      !Client::is_pending_review($clientID)
    ) {
      return clientDisplay::ClientView;
    }

    if (
      $c['ID'] === $clientID &&
      Client::is_guest($clientID) &&
      !Client::is_pending_review($clientID)
    ) {
      return clientDisplay::ClientView;
    }

    if (
      $c['ID'] === $clientID &&
      Client::is_guest($clientID) &&
      Client::is_pending_review($clientID)
    ) {
      return clientDisplay::GuestPendingReview;
    }

    return clientDisplay::NoAccess;


  }

  private static function allowed_to_perform_guest_review(
$client_id,
$secret  ){
    return Client::get_random_review_permission_secret($client_id) === $secret;
  }

  public static function post_password_required($post_id){
    // $post = get_post($post_id);
    // if (is_null($post))
    //   return false;

    // if (!is_string($post->post_password) || $post->post_password === '') {
    //   return false;
    // }

    // if (/*UNSAFE_EXPR*/ !isset($_COOKIE['wp-postpass_'.COOKIEHASH])) {
    //   return true;
    // }
    // return false;
    // UNSAFE

    $post = get_post($post_id);

    if (empty($post->post_password)) {
      /** This filter is documented in wp-includes/post-template.php */
      return apply_filters('post_password_required', false, $post);
    }

    if (!isset($_COOKIE['wp-postpass_'.COOKIEHASH])) {
      /** This filter is documented in wp-includes/post-template.php */
      // return apply_filters('post_password_required', true, $post);
      return true;
    }

    $hasher = new \PasswordHash(8, true);

    $hash = wp_unslash($_COOKIE['wp-postpass_'.COOKIEHASH]);
    $required = !$hasher->CheckPassword($post->post_password, $hash);
    // if (0 !== strpos($hash, '$P$B')) {
    //   $required = true;
    // } else {
    //   $required = !$hasher->CheckPassword($post->post_password, $hash);
    // }

    return $required;

  }

}
