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
  });

  $.each(response.messages, function (index, m) {
    var row = buildMessageTableRow(m);
    $("#alerts-table-body").append(row);
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
