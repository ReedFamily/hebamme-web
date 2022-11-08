const displayAlerts = function (response) {
  $("#content-window").empty();
  $("#content-window").append(
    $("<div />", { class: "col-12", id: "alerts-wrapper" }).append(
      $("<table />", { class: "table", id: "alerts-table" }).append(
        $("<thead />").append(
          $("<th />", { class: "text-center", scope: "col", text: "ID" }),
          $("<th />", { class: "text-center", scope: "col", text: "Level" }),
          $("<th />", { class: "text-center", scope: "col", text: "Loc" }),
          $("<th />", {
            class: "text-center",
            scope: "col",
            text: "Erstellt von",
          }),
          $("<th />", {
            class: "text-center",
            scope: "col",
            text: "Erstellt am",
          }),
          $("<th />", {
            class: "text-center",
            scope: "col",
            text: "Dauerhaft",
          }),
          $("<th />", { class: "text-center", scope: "col", text: "von" }),
          $("<th />", { class: "text-center", scope: "col", text: "bis" }),
          $("<th />", { class: "text-center", scope: "col", text: "Aktionen" })
        ),
        $("<tbody />", { id: "alerts-table-body" }),
        $("<tfoot />", { id: "alerts-table-footer" }).append(
          $("<th />", { colspan: 9 }).append(
            $("<button />", {
              class: "btn btn-primary",
              id: "add-new-alert-button",
              text: "Neue Alert  ",
            }).append($("<i />", { class: "fas fa-exclamation-circle" }))
          )
        )
      )
    )
  );
  $("#add-new-alert-button").click(function (event) {
    $("#alert-editor-title").text("Neue Alert");
    let uname = getCookieByName("uname");
    $("#alert-edit-form").trigger("reset");
    $("#alertCreatedBy").val(uname);
    $("#alert-blue").prop("checked", "true").trigger("change");
    $("body").off("click", "#alert-editor-save-button", updateAlertEvent);
    $("body").on("click", "#alert-editor-save-button", newAlertEvent);
    $("#edit-alert-dialog").modal("show");
  });

  $.each(response.messages, function (index, m) {
    var row = buildMessageTableRow(m);
    $("#alerts-table-body").append(row);
  });
};

const newAlertEvent = function (event) {
  event.stopImmediatePropagation();
  try {
    validateAlertEditorForm(true);
  } catch (e) {
    let item = e.err;
    $(item).addClass("erroredFormControl");
  }
  var data = new Object();
  data.createdBy = $("#alertCreatedBy").val();
  data.createdDate = new Date().toISOString().slice(0, 19).replace("T", " ");
  data.level = $("input[name='alertLevel']:checked").val();
  data.location = "home";
  data.permanent = 1;
  data.message = $("#alertContent").val();

  var url = "../backend/rest.php?apiFunc=newMsg";

  $.post(url, data, function (res) {
    if (res.status == 200) {
      displayAlerts(res);
    } else {
      console.log(res);
    }
  });
};

const removeErrorFormControls = function () {
  $("#alert-edit-form *")
    .filter(":input")
    .each(function (ctrl) {
      $(ctrl).removeClass("erroredFormControl");
    });
};

const validateAlertEditorForm = function (isNew) {
  removeErrorFormsControls();
  var id = $("#alertId").val();
  if (isNew != true) {
    if (Number(id) > 0 == false) {
      throw { err: "#alertId" };
    }
  }
  var uname = $("#alertCreatedBy").val();
  if (isEmpty(uname)) {
    throw { err: "#alertCreatedBy" };
  }
  var content = $("#alertContent").val();
  if (isEmpty(content)) {
    throw { err: "#alertContent" };
  }
};

const isEmpty = function (value) {
  return value == null || value.trim().length === 0;
};

const updateAlertEvent = function (event) {
  event.stopImmediatePropagation();
  try {
    validateAlertEditorForm(true);
  } catch (e) {
    let item = e.err;
    $(item).addClass("erroredFormControl");
  }
  var data = new Object();
  data.id = $("#alertId").val();
  data.createdBy = $("#alertCreatedBy").val();
  data.createdDate = $("#alertCreatedDate").val();
  data.level = $("input[name='alertLevel']:checked").val();
  data.location = "home";
  data.permanent = 1;
  data.message = $("#alertContent").val();

  var url = "../backend/rest.php?apiFunc=modMsg";
  $.post(url, data, function (res) {
    if (res.status == 200) {
      displayAlerts(res);
    }
  });
};

const deleteMessage = function (ele) {
  var id = $(ele).data("message");
  var url = "../backend/rest.php?apiFunc=getMsg&id=" + id;
  $.get(url, function (res) {
    if (res.status == 200) {
      $("#delete-alert-acknowledge-button").data("id", res.message.id);
      $("#delete-alert").text(res.message.id);
      $("#delete-alert-dialog").modal("show");
    } else {
      console.log(res);
    }
  });
};

const sendDeleteAlert = function (id) {
  var url = "../backend/rest.php?apiFunc=delMsg&id=" + id;
  $.get(url, function (res) {
    if (res.status == 200) {
      $("#delete-alert-acknowledge-button").removeData("id");
      displayAlerts(res);
    } else {
      console.log(res);
    }
  });
};

const buildMessageTableRow = function (message) {
  var level = buildLevelCellEntry(message.level);
  var permanent = buildPermanentCellEntry(message.permanent);
  var eLink = buildEditLink(message.id);
  var dLink = buildDeleteLink(message.id);
  var row = $("<tr />").append(
    $("<td />", { class: "text-center", text: message.id }),
    $("<td />", { class: "text-center" }).append(level),
    $("<td />", { class: "text-center", text: message.location }),
    $("<td />", { class: "text-center", text: message.createdBy }),
    $("<td />", { class: "text-center", text: message.createdDate }),
    $("<td />", { class: "text-center" }).append(permanent),
    $("<td />", { class: "text-center", text: message.startDate }),
    $("<td />", { class: "text-center", text: message.endDate }),
    $("<td />", { class: "text-center" }).append(eLink, dLink)
  );
  return row;
};

const buildEditLink = function (messageId) {
  var linkId = "edit-message-" + messageId;
  var link = $("<a />", { id: linkId, text: " " }).click(function (event) {
    event.preventDefault();
    editMessage(this);
  });

  $(link).attr("data-message", messageId);
  $(link).append($("<i />", { class: "fas fa-edit linkchar" }));
  return link;
};

const editMessage = function (lnk) {
  var id = $(lnk).data("message");
  var url = "../backend/rest.php?apiFunc=getMsg&id=" + id;
  $.get(url, function (res) {
    if (res.status == 200) {
      $("body").off("click", "#alert-editor-save-button", newAlertEvent);
      $("body").on("click", "#alert-editor-save-button", updateAlertEvent);
      $("#alert-edit-form").trigger("reset");
      $("#alertId").val(res.message.id);
      $("#alertCreatedBy").val(res.message.createdBy);
      $("#alertContent").val(res.message.message);
      $("#alertCreatedDate").val(res.message.createdDate);
      var key = "#alert-" + res.message.level;
      $(key).prop("checked", true).trigger("change");
      $("#alert-editor-title").text("Bearbeitung Alert");
      $("#edit-alert-dialog").modal("show");
    } else {
      console.log(res);
    }
  });
};

const buildDeleteLink = function (messageId) {
  var linkId = "delete-message-" + messageId;
  var link = $("<a />", { id: linkId, text: " " }).click(function (event) {
    event.preventDefault();
    deleteMessage(this);
  });
  $(link).attr("data-message", messageId);
  $(link).append($("<i />", { class: "fas fa-trash linkchar" }));
  return link;
};

const buildPermanentCellEntry = function (perm) {
  if (perm == true || perm == 1) {
    return $("<i />", { class: "fas fa-check-circle checkmark" });
  }
  return $("<i />", { class: "fas fa-minus-circle nocheck" });
};

const buildLevelCellEntry = function (level) {
  var lvlClass = buildLevelClassName(level);
  var spn = $("<span />", { text: level, class: lvlClass });
  return spn;
};

const buildLevelClassName = function (level) {
  var res = "alert-info";
  switch (level) {
    case "red":
      res = "alert-danger levelcellentry";
      break;
    case "yellow":
      res = "alert-warning levelcellentry";
      break;
    default:
      res = "alert-info levelcellentry";
      break;
  }
  return res;
};
