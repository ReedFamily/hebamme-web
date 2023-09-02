const loadApp = function () {
  var token = getCookieValue("apiToken");
  var userid = getCookieValue("userId");
  if (token != "") {
    var url = "../backend/rest.php?apiFunc=tokenValid&token=" + token;
    $.get(url, function (res) {
      response = res;
      if (response.status != 200) {
        displayLoginForm();
      } else {
        $("#logout-link").removeClass("hidden");
        $("#logout-link").attr("data-userid", userid);
        fillAlertLocs();
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
  friconix_update();
};

const fillAlertLocs = function () {
  var url = "../backend/rest.php?apiFunc=msgLoc";
  $.get(url, function (res) {
    if (res.status == 200) {
      var locationsSelect = $("#alertLoc");
      $.each(res.locations, function (index, val) {
        locationsSelect.append(
          $("<option />", { value: val, text: val, id: "opt-" + val })
        );
      });
    }
  });
};

const displayFailure = function (id) {
  $("#content-window").empty();
  var content = $(id).html();
  $("#content-window").append(content);
  friconix_update();
};

const buildSidebarNav = function () {
  $("#sidebar-nav-list").empty();
  $("#sidebar-nav-list").append(
    $("<li />", { class: "nav-item" }).append(
      $("<a />", { id: "link-home", class: "nav-link" })
        .append(
          $("<i />", { class: "fi-xnsuxl-house-solid" }),
          $("<span />", { text: " Home" })
        )
        .click(function (event) {
          event.preventDefault();
          buildHome();
          friconix_update();
        })
    ),
    $("<li />", { class: "nav-item" }).append(
      $("<a />", { id: "link-user", class: "nav-link" })
        .append(
          $("<i />", { class: "fi-xnsuxl-user-solid" }),
          $("<span />", { text: " Benutzer" })
        )
        .click(function (event) {
          event.preventDefault();
          $.get("../backend/rest.php?apiFunc=listUsers", function (res) {
            response = res;
            if (response.status == 200) {
              displayUsers(response);
              friconix_update();
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
          $("<i />", { class: "fi-xnsuxl-team-solid" }),
          $("<span />", { text: " Das Team" })
        )
        .click(function (event) {
          event.preventDefault();
          $.get("../backend/rest.php?apiFunc=listInstructors", function (res) {
            response = res;
            if (response.status == 200) {
              displayInstructors(response);
              friconix_update();
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
          $("<i />", { class: "fi-xnluxl-megaphone-solid" }),
          $("<span />", { text: " Mitteilungen" })
        )
        .click(function (event) {
          event.preventDefault();
          $.get("../backend/rest.php?apiFunc=listMsgs", function (res) {
            response = res;
            if (response.status == 200) {
              displayAlerts(response);
              friconix_update();
            } else {
              displayFailure("#failure-alerts-template");
            }
          });
        })
    ),
    $("<li />", { class: "nav-item" }).append(
      $("<a />", { id: "link-faq", class: "nav-link" })
        .append(
          $("<i />", { class: "fi-cnsuxl-question-mark" }),
          $("<span />", { text: " FAQs" })
        )
        .click(function (event) {
          event.preventDefault();
          $.get("../backend/rest.php?apiFunc=faqs", function (res) {
            if (res.status == 200) {
              displayFaqs(res);
            } else {
              displayFailure("#failure-faqs-template");
            }
          });
        })
    )
  );
  friconix_update();
};

const displayLoginForm = function () {
  $("#sidebar-nav-list").empty();
  $("#logout-link").addClass("hidden");
  $("#logout-link").removeAttr("data-userid");
  buildLoginForm();
  friconix_update();
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
    response = res;
    if (response.status == 200) {
      var url =
        "../backend/rest.php?apiToken=" + response.token + "&apiFunc=login";
      $.post(url, JSON.stringify(request), function (res1) {
        loginResponse = res1;
        if (loginResponse.status == 200) {
          writeCookie("apiToken", loginResponse.token, loginResponse.validTo);
          writeCookie("userId", loginResponse.userId, loginResponse.validTo);
          writeCookie("uname", loginResponse.username, loginResponse.validTo);
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

const getCookieByName = function (cname) {
  let name = cname + "=";
  let cVal = decodeURIComponent(document.cookie);
  let ca = cVal.split(";");
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == " ") {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
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
  friconix_update();
};

const randomInt = function (max, min) {
  return Math.floor(Math.random() * (max - min + 1) + min);
};
