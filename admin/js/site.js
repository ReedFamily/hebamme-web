const loadApp = function () {
  var token = getCookieValue("apiToken");
  if (token != "") {
    var url = "../backend/rest.php?apiFunc=tokenValid&token=" + token;
    $.get(url, function (res) {
      response = JSON.parse(res);
      if (response.status != 200) {
        displayLoginForm();
      } else {
        buildSidebarNav();
        buildHome();
      }
    });
  } else {
    displayLoginForm();
  }
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

const displayUsers = function (response) {
  $("#content-window").empty();
  $("#content-window").append(
    $("<div />", { class: "col-12", id: "users-wrapper" }).append(
      $("<table />", { class: "table", id: "users-table" }).append(
        $("<thead />").append(
          $("<th />", { scope: "col", text: "ID" }),
          $("<th />", { scope: "col", text: "Benutzer Name" }),
          $("<th />", { scope: "col", text: "Name" }),
          $("<th />", { scope: "col", text: "Vorname" }),
          $("<th />", { scope: "col", text: "Email" }),
          $("<th />", { scope: "col", text: "Role" }),
          $("<th />", { scope: "col", text: "Aktionen" })
        ),
        $("<tbody />", { id: "users-table-body" })
      )
    )
  );
  $.each(response.users, function (index, user) {
    var activeUserId = getCookieValue("userId");
    var editUserLink = createEditUserLink(user.id);
    var deleteUserLink = "";
    if (user.role != 0 && user.id != activeUserId) {
      deleteUserLink = createDeleteUserLink(user.id);
    }
    var row = $("<tr />").append(
      $("<td />", { text: user.id }),
      $("<td />", { text: user.username }),
      $("<td />", { text: user.last_name }),
      $("<td />", { text: user.first_name }),
      $("<td />", { text: user.email }),
      $("<td />", { text: user.role }),
      $("<td />").append(editUserLink, deleteUserLink)
    );

    $("#users-table-body").append(row);
  });
};

const createEditUserLink = function (userid) {
  var linkId = "link-edit-user-" + userid;
  var link = $("<a />", { id: linkId, text: " " }).click(function (event) {
    event.preventDefault();
    editUser(this);
  });
  $(link).attr("data-userid", userid);
  $(link).append($("<i />", { class: "fas fa-user-edit" }));
  return link;
};

const editUser = function (ele) {
  var id = $(ele).data("userid");
  var url = "../backend/rest.php?apiFunc=getUser&userid=" + id;
  $.get(url, function (res) {
    var response = JSON.parse(res);
    if (response.status == 200) {
      $("#editInputUserId").val(response.user.id);
      $("#editInputUsername").val(response.user.username);
      $("#editInputLastname").val(response.user.last_name);
      $("#editInputFirstname").val(response.user.first_name);
      $("#editInputEmail").val(response.user.email);
      $("#edit-user-dialog").modal("show");
    } else {
      console.log(response);
    }
  });
};

const sendEditUser = function (editUserData) {
  var url = "../backend/rest.php?apiFunc=updateUser";
  $.post(url, JSON.stringify(editUserData), function (res) {
    var response = JSON.parse(res);
    if (response.status == 200) {
      displayUsers(response);
    } else {
      console.log(response);
    }
  });
};

const createDeleteUserLink = function (userid) {
  var linkId = "link-delete-user-" + userid;
  var link = $("<a />", { id: linkId, text: " " }).click(function (event) {
    event.preventDefault();
    deleteUser(this);
  });
  $(link).attr("data-userid", userid);
  $(link).append($("<i />", { class: "fas fa-user-slash" }));
  return link;
};

const deleteUser = function (ele) {
  var id = $(ele).data("userid");
  console.log("Deleting: " + id);
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
      }).append(
        $("<i />", { class: "fas fa-chalkboard-teacher" }),
        $("<span />", { text: " Dozentinnen" })
      )
    ),
    $("<li />", { class: "nav-item" }).append(
      $("<a />", {
        id: "link-classes",
        class: "nav-link",
      }).append(
        $("<i />", { class: "far fa-calendar-alt" }),
        $("<span />", { text: " Kurse" })
      )
    ),
    $("<li />", { class: "nav-item" }).append(
      $("<a />", {
        id: "link-announcments",
        class: "nav-link",
      }).append(
        $("<i />", { class: "fas fa-bullhorn" }),
        $("<span />", { text: " Mitteilungen" })
      )
    )
  );
};

const displayLoginForm = function () {
  $("#sidebar-nav-list").empty();
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
