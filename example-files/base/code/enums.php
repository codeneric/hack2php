<?hh //strict

namespace codeneric\phmm\enums {
  enum CannedEmailPlaceholders : string {
    clientName = "[client-name]";
    clientUserName = "[username]";
    clientPassword = "[password]";
    linkToProjects = "[link-to-projects]";
  }
  ;

  // these states are not guaranteed to be independent
  enum UserState : string {
    NotLoggedIn = "NotLoggedIn";
    LoggedInUserWithNoAccess = "LoggedInUserWithNoAccess";
    Admin = "Admin";
    Client = "Client";
    Guest = "Guest";
  }
  ;
  enum ProjectState : string {
    Public_ = "Public_";
    Private_ = "Private_";
    PrivateWithGuestLogin = "PrivateWithGuestLogin";
    PrivateWithGuestLoginNoClientsAssigned =
      "PrivateWithGuestLoginNoClientsAssigned";

  }
  ;

  enum ProjectDisplay : string {
    ProjectWithClientConfig = "ClientWithClientConfig";
    ProjectWithProjectConfig = "ProjectWithProjectConfig";
    AdminNotice = "AdminNotice";
    LoginForm = "LoginForm";
    SplitLoginView = "SplitLoginView";
    PasswordInput = "PasswordInput";
    NoAccess = "NoAccess";
  }
  ;

  enum ClientDisplay : string {
    AdminNoticeWithClientView = "AdminNoticeWithClientView";
    LoginForm = "LoginForm";
    NoAccess = "NoAccess";
    ClientView = "ClientView";
  }
  ;
  enum PortalDisplay : string {
    AdminNotice = "AdminNotice";
    LoginForm = "LoginForm";
    Redirect = "Redirect";
  }
  ;

  enum SemaphoreExecutorReturn : string {
    Failed = "Failed";
    Finished = "Finished";
    Outstanding = "Outstanding";
  }
  ;
}
