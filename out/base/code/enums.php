<?php //strict
namespace codeneric\phmm\enums {
final class CannedEmailPlaceholders { private function __construct() {} 
private static $hacklib_values = array(
"clientName" => "[client-name]" ,
"clientUserName" => "[username]" ,
"clientPassword" => "[password]" ,
"linkToProjects" => "[link-to-projects]" 
);
use \HH\HACKLIB_ENUM_LIKE;
const clientName = "[client-name]";
const clientUserName = "[username]";
const clientPassword = "[password]";
const linkToProjects = "[link-to-projects]";
 }
  ;
final class UserState { private function __construct() {} 
private static $hacklib_values = array(
"NotLoggedIn" => "NotLoggedIn" ,
"LoggedInUserWithNoAccess" => "LoggedInUserWithNoAccess" ,
"Admin" => "Admin" ,
"Client" => "Client" ,
"Guest" => "Guest" 
);
use \HH\HACKLIB_ENUM_LIKE;
const NotLoggedIn = "NotLoggedIn";
const LoggedInUserWithNoAccess = "LoggedInUserWithNoAccess";
const Admin = "Admin";
const Client = "Client";
const Guest = "Guest";
 }
  ;
final class ProjectState { private function __construct() {} 
private static $hacklib_values = array(
"Public_" => "Public_" ,
"Private_" => "Private_" ,
"PrivateWithGuestLogin" => "PrivateWithGuestLogin" ,
"PrivateWithGuestLoginNoClientsAssigned" => "PrivateWithGuestLoginNoClientsAssigned" 
);
use \HH\HACKLIB_ENUM_LIKE;
const Public_ = "Public_";
const Private_ = "Private_";
const PrivateWithGuestLogin = "PrivateWithGuestLogin";
const PrivateWithGuestLoginNoClientsAssigned = "PrivateWithGuestLoginNoClientsAssigned";
 }
  ;
final class ProjectDisplay { private function __construct() {} 
private static $hacklib_values = array(
"ProjectWithClientConfig" => "ClientWithClientConfig" ,
"ProjectWithProjectConfig" => "ProjectWithProjectConfig" ,
"AdminNotice" => "AdminNotice" ,
"LoginForm" => "LoginForm" ,
"SplitLoginView" => "SplitLoginView" ,
"PasswordInput" => "PasswordInput" ,
"NoAccess" => "NoAccess" 
);
use \HH\HACKLIB_ENUM_LIKE;
const ProjectWithClientConfig = "ClientWithClientConfig";
const ProjectWithProjectConfig = "ProjectWithProjectConfig";
const AdminNotice = "AdminNotice";
const LoginForm = "LoginForm";
const SplitLoginView = "SplitLoginView";
const PasswordInput = "PasswordInput";
const NoAccess = "NoAccess";
 }
  ;
final class ClientDisplay { private function __construct() {} 
private static $hacklib_values = array(
"AdminNoticeWithClientView" => "AdminNoticeWithClientView" ,
"LoginForm" => "LoginForm" ,
"NoAccess" => "NoAccess" ,
"ClientView" => "ClientView" ,
"GuestPendingReview" => "GuestPendingReview" ,
"GuestReviewAccepted" => "GuestReviewAccepted" ,
"GuestReviewDeclined" => "GuestReviewDeclined" ,
"ReviewGuestRequestNotPermitted" => "ReviewGuestRequestNotPermitted" 
);
use \HH\HACKLIB_ENUM_LIKE;
const AdminNoticeWithClientView = "AdminNoticeWithClientView";
const LoginForm = "LoginForm";
const NoAccess = "NoAccess";
const ClientView = "ClientView";
const GuestPendingReview = "GuestPendingReview";
const GuestReviewAccepted = "GuestReviewAccepted";
const GuestReviewDeclined = "GuestReviewDeclined";
const ReviewGuestRequestNotPermitted = "ReviewGuestRequestNotPermitted";
 }
  ;
final class PortalDisplay { private function __construct() {} 
private static $hacklib_values = array(
"AdminNotice" => "AdminNotice" ,
"LoginForm" => "LoginForm" ,
"Redirect" => "Redirect" 
);
use \HH\HACKLIB_ENUM_LIKE;
const AdminNotice = "AdminNotice";
const LoginForm = "LoginForm";
const Redirect = "Redirect";
 }
  ;
final class SemaphoreExecutorReturn { private function __construct() {} 
private static $hacklib_values = array(
"Failed" => "Failed" ,
"Finished" => "Finished" ,
"Outstanding" => "Outstanding" 
);
use \HH\HACKLIB_ENUM_LIKE;
const Failed = "Failed";
const Finished = "Finished";
const Outstanding = "Outstanding";
 }
  ;
final class AdvancedBoolSettings { private function __construct() {} 
private static $hacklib_values = array(
"PHMM_REDIRECT_CLIENT_TO_SINGLE_PROJECT" => "PHMM_REDIRECT_CLIENT_TO_SINGLE_PROJECT" ,
"PHMM_APPLY_TEMPLATE_TO_CLIENT_PAGE" => "PHMM_APPLY_TEMPLATE_TO_CLIENT_PAGE" ,
"PHMM_ALLOW_EDITORS" => "PHMM_ALLOW_EDITORS" ,
"PHMM_ENABLE_MEDIA_SEPARATION" => "PHMM_ENABLE_MEDIA_SEPARATION" 
);
use \HH\HACKLIB_ENUM_LIKE;
const PHMM_REDIRECT_CLIENT_TO_SINGLE_PROJECT = "PHMM_REDIRECT_CLIENT_TO_SINGLE_PROJECT";
const PHMM_APPLY_TEMPLATE_TO_CLIENT_PAGE = "PHMM_APPLY_TEMPLATE_TO_CLIENT_PAGE";
const PHMM_ALLOW_EDITORS = "PHMM_ALLOW_EDITORS";
const PHMM_ENABLE_MEDIA_SEPARATION = "PHMM_ENABLE_MEDIA_SEPARATION";
 }
  ;
final class GuestReviewURLParams { private function __construct() {} 
private static $hacklib_values = array(
"DecisionKey" => "phmm_guest_review_decision" ,
"SecretKey" => "phmm_guest_review_secret" 
);
use \HH\HACKLIB_ENUM_LIKE;
const DecisionKey = "phmm_guest_review_decision";
const SecretKey = "phmm_guest_review_secret";
 }
  ;
final class GuestReviewDecisionValues { private function __construct() {} 
private static $hacklib_values = array(
"Accept" => "accept" ,
"Decline" => "decline" 
);
use \HH\HACKLIB_ENUM_LIKE;
const Accept = "accept";
const Decline = "decline";
 }
  ;
}
