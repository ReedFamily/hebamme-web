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
  // set event for new user
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
  // Display users
  $.each(response.users, function (index, user) {
    var row = buildUserTableRow(user);
    $("#users-table-body").append(row);
  });
};

const buildUserTableRow = function (user) {
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
  return row;
};

const createEditUserLink = function (userid) {
  var linkId = "link-edit-user-" + userid;
  var link = $("<a />", { id: linkId, text: " " }).click(function (event) {
    event.preventDefault();
    editUser(this);
  });
  $(link).attr("data-userid", userid);
  $(link).append($("<i />", { class: "fas fa-user-edit linkchar" }));
  return link;
};

const editUser = function (ele) {
  var id = $(ele).data("userid");
  var url = "../backend/rest.php?apiFunc=getUser&userid=" + id;
  $.get(url, function (res) {
    var response = res;
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
    var response = res;
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
  if ($("#editInputPassword").val()) {
    editUserData.password = $("#editInputPassword").val();
  }
  sendEditUser(editUserData);
  $("body").off("click", "#edit-user-save-button", editUserEvent);
};

const sendEditUser = function (editUserData) {
  var url = "../backend/rest.php?apiFunc=updateUser";
  $.post(url, JSON.stringify(editUserData), function (res) {
    var response = res;
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
  $(link).append($("<i />", { class: "fas fa-user-slash linkchar" }));
  return link;
};

const sendDeleteUser = function (id) {
  var url = "../backend/rest.php?apiFunc=deleteUser&userid=" + id;
  $.get(url, function (res) {
    var response = res;
    if (response.status == 200) {
      displayUsers(response);
      $("#delete-user-acknowledge-button").removeData("id");
    } else {
      console.log(response);
    }
  });
};

const deleteUser = function (ele) {
  var id = $(ele).data("userid");
  var url = "../backend/rest.php?apiFunc=getUser&userid=" + id;
  $.get(url, function (res) {
    var response = res;
    if (response.status == 200) {
      $("#delete-user-acknowledge-button").data("id", response.user.id);
      $("#delete-username").text("'" + response.user.username + "'");
      $("#delete-user-dialog").modal("show");
    } else {
      console.log(response);
    }
  });
};
