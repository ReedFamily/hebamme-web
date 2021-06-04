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
        $("<tbody />", { id: "users-table-body" }),
        $("<tfoot/>", { id: "users-table-footer" }).append(
          $("<th />", { colspan: "7" }).append(
            $("<button />", {
              class: "btn btn-primary",
              id: "add-new-user-button",
              text: "Neu Benutzer  ",
            }).append($("<i />", { class: "fas fa-user-plus" }))
          )
        )
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
    $("#add-new-user-button").click(function (event) {
      $("#user-editor-title").text("Neue Benutzer");
      $("#editInputPassword")
        .removeAttr("data-toggle")
        .removeAttr("data-placement")
        .attr("title");
      $("body").off("click", "#edit-user-save-button", editUserEvent);
      $("body").on("click", "#edit-user-save-button", newUserEvent);
      $("#edit-user-form").trigger("reset");
      $("#edit-user-dialog").modal("show");
    });
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
      $("#edit-user-form").trigger("reset");
      $("#editInputUserId").val(response.user.id);
      $("#editInputUsername").val(response.user.username);
      $("#editInputLastname").val(response.user.last_name);
      $("#editInputFirstname").val(response.user.first_name);
      $("#editInputEmail").val(response.user.email);
      $("#editInputPassword")
        .attr("data-toggle", "tooltip")
        .attr("data-placement", "right")
        .attr("title", "nur wann es geändert soll");

      $("body").off("click", "#edit-user-save-button", newUserEvent);
      $("body").on("click", "#edit-user-save-button", editUserEvent);
      $("#edit-user-dialog").modal("show");
    } else {
      console.log(response);
    }
  });
};

const newUserEvent = function (event) {
  event.stopImmediatePropagation();
  var newUserData = new Object();
  newUserData.username = $("#editInputUsername").val();
  newUserData.firstname = $("#editInputFirstname").val();
  newUserData.lastname = $("#editInputLastname").val();
  newUserData.email = $("#editInputEmail").val();
  newUserData.password = $("#editInputPassword").val();
  sendNewUser(newUserData);
  $("body").off("click", "#edit-user-save-button", newUserEvent);
};

const sendNewUser = function (newUserData) {
  var url = "../backend/rest.php?apiFunc=createUser";
  $.post(url, JSON.stringify(newUserData), function (res) {
    var response = JSON.parse(res);
    if (response.status == 200) {
      displayUsers(response);
    } else {
      console.log(response);
    }
  });
};

const editUserEvent = function (event) {
  event.stopImmediatePropagation();
  var editUserData = new Object();
  editUserData.id = $("#editInputUserId").val();
  editUserData.username = $("#editInputUsername").val();
  editUserData.firstname = $("#editInputFirstname").val();
  editUserData.lastname = $("#editInputLastname").val();
  editUserData.email = $("#editInputEmail").val();
  if ($("editInputPassword").val()) {
    editUserData.password = $("#editInputPassword").val();
  }
  sendEditUser(editUserData);
  $("body").off("click", "#edit-user-save-button", editUserEvent);
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

const sendDeleteUser = function (id) {
  var url = "../backend/rest.php?apiFunc=deleteUser&userid=" + id;
  $.get(url, function (res) {
    var response = JSON.parse(res);
    if (response.status == 200) {
      displayUsers(response);
    } else {
      console.log(response);
    }
  });
};

const deleteUser = function (ele) {
  var id = $(ele).data("userid");
  var url = "../backend/rest.php?apiFunc=getUser&userid=" + id;
  $.get(url, function (res) {
    var response = JSON.parse(res);
    if (response.status == 200) {
      $("#delete-user-acknowledge-button").attr("data-id", response.user.id);
      $("#delete-username").text("'" + response.user.username + "'");
      $("#delete-user-dialog").modal("show");
    } else {
      console.log(response);
    }
  });
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
