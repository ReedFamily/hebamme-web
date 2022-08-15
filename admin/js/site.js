const loadApp = function () {
  var token = getCookieValue("apiToken");
  var userid = getCookieValue("userId");
  if (token != "") {
    var url = "../backend/rest.php?apiFunc=tokenValid&token=" + token;
    $.get(url, function (res) {
      response = JSON.parse(res);
      if (response.status != 200) {
        displayLoginForm();
      } else {
        $("#logout-link").removeClass("hidden");
        $("#logout-link").attr("data-userid", userid);
        buildSidebarNav();
        buildHome();
      }
    });
  } else {
    displayLoginForm();
  }
};

const logoutEvent = function (event) {
  var userid = $(this).data("userid");
  var url = "../backend/rest.php?apiFunc=logout&userid=" + userid;
  $.get(url, function (res) {
    loadApp();
  });
};

const buildHome = function () {
  $("#content-window").empty();
  var content = $("#welcome-template").html();
  $("#content-window").append(content);
};

const displayFailure = function (id) {
  $("#content-window").empty();
  var content = $(id).html();
  $("#content-window").append(content);
};

const buildSidebarNav = function () {
  $("#sidebar-nav-list").empty();
  $("#sidebar-nav-list").append(
    $("<li />", { class: "nav-item" }).append(
      $("<a />", { id: "link-home", class: "nav-link" })
        .append(
          $("<i />", { class: "fas fa-home" }),
          $("<span />", { text: " Home" })
        )
        .click(function (event) {
          event.preventDefault();
          buildHome();
        })
    ),
    $("<li />", { class: "nav-item" }).append(
      $("<a />", { id: "link-user", class: "nav-link" })
        .append(
          $("<i />", { class: "fas fa-user" }),
          $("<span />", { text: " Benutzer" })
        )
        .click(function (event) {
          event.preventDefault();
          $.get("../backend/rest.php?apiFunc=listUsers", function (res) {
            response = JSON.parse(res);
            if (response.status == 200) {
              displayUsers(response);
            } else {
              displayFailure("#failure-users-template");
            }
          });
        })
    ),
    $("<li />", { class: "nav-item" }).append(
      $("<a />", {
        id: "link-instructor",
        class: "nav-link",
      })
        .append(
          $("<i />", { class: "fas fa-chalkboard-teacher" }),
          $("<span />", { text: " Das Team" })
        )
        .click(function (event) {
          event.preventDefault();
          $.get("../backend/rest.php?apiFunc=listInstructors", function (res) {
            response = JSON.parse(res);
            if (response.status == 200) {
              displayInstructors(response);
            } else {
              displayFailure("#failure-instructors-template");
            }
          });
        })
    ),
    $("<li />", { class: "nav-item" }).append(
      $("<a />", {
        id: "link-announcments",
        class: "nav-link",
      })
        .append(
          $("<i />", { class: "fas fa-bullhorn" }),
          $("<span />", { text: " Mitteilungen" })
        )
        .click(function (event) {
          event.preventDefault();
          $.get("../backend/rest.php?apiFunc=listMsgs", function (res) {
            response = JSON.parse(res);
            if (response.status == 200) {
              displayAlerts(response);
            } else {
              displayFailure("#failure-alerts-template");
            }
          });
        })
    )
  );
};

const displayLoginForm = function () {
  $("#sidebar-nav-list").empty();
  $("#logout-link").addClass("hidden");
  $("#logout-link").removeAttr("data-userid");
  buildLoginForm();
};

const getCookieValue = function (name) {
  return (
    document.cookie.match("(^|;)\\s*" + name + "\\s*=\\s*([^;]+)")?.pop() || ""
  );
};

const validateLogin = function (user, password) {
  var request = new Object();
  request.username = user;
  request.password = password;

  $.get("../backend/rest.php?apiFunc=getToken", function (res) {
    response = JSON.parse(res);
    if (response.status == 200) {
      var url =
        "../backend/rest.php?apiToken=" + response.token + "&apiFunc=login";
      $.post(url, JSON.stringify(request), function (res1) {
        loginResponse = JSON.parse(res1);
        if (loginResponse.status == 200) {
          writeCookie("apiToken", loginResponse.token, loginResponse.validTo);
          writeCookie("userId", loginResponse.userId, loginResponse.validTo);
          $("#content-window").empty();
          loadApp();
        } else {
          $("#login-error-message").removeClass("hidden");
        }
      });
    }
  });
};

const writeCookie = function (name, value, exp) {
  var expDate = new Date(exp);
  document.cookie =
    name +
    "=" +
    value +
    "; expires=" +
    expDate.toUTCString() +
    "; path=/;sameSite=strict";
};

const buildLoginForm = function () {
  var display = $("#content-window");
  $(display).empty();
  $(display).append(
    $("<div />", { id: "login-form-wrapper", class: "col-4" }).append(
      $("<form />", { id: "login-form", class: "form-signin" })
        .append(
          $("<h1 />", {
            class: "h3 mb-3 font-weight-normal",
            text: "Bitte Anmelden",
          }),
          $("<label />", {
            for: "inputUsername",
            class: "sr-only",
            text: "Benutzername",
          }),
          $("<input />", {
            id: "inputUsername",
            class: "form-control",
            placeholder: "Benutzername",
            type: "text",
            required: "",
            autofocus: "",
          }),
          $("<label />", {
            for: "inputPassword",
            class: "sr-only",
            text: "Passwort",
          }),
          $("<input />", {
            id: "inputPassword",
            class: "form-control",
            placeholder: "Passwort",
            type: "password",
            required: "",
            autofocus: "",
          }),
          $("<div />", {
            class: "hidden col-12 alert alert-danger",
            id: "login-error-message",
          }).append(
            $("<span />", { text: "Falsche anmeldung, bitte nochmal Prüfen" })
          ),
          $("<button />", {
            id: "submitLogin",
            class: "btn btn-primary btn-block btn-large",
            text: "Anmelden",
            type: "submit",
          })
        )
        .submit(function (event) {
          event.preventDefault();
          var user = $("#inputUsername").val();
          var pass = $("#inputPassword").val();
          validateLogin(user, pass);
        })
    )
  );
};

const randomInt = function (max, min) {
  return Math.floor(Math.random() * (max - min + 1) + min);
};
