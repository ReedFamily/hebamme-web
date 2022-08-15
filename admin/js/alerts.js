const displayAlerts = function (response) {
  $("#content-window").empty();
  $("#content-window").append(
    $("<div />", { class: "col-12", id: "alerts-wrapper" }).append(
      $("<table />", { class: "table", id: "alerts-table" }).append(
        $("<thead />").append(
          $("<th />", { scope: "col", text: "ID" }),
          $("<th />", { scope: "col", text: "Level" }),
          $("<th />", { scope: "col", text: "Loc" }),
          $("<th />", { scope: "col", text: "Erstellt von" }),
          $("<th />", { scope: "col", text: "Erstellt am" }),
          $("<th />", { scope: "col", text: "Dauerhaft" }),
          $("<th />", { scope: "col", text: "von" }),
          $("<th />", { scope: "col", text: "bis" }),
          $("<th />", { scope: "col", text: "Aktionen" })
        )
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
  );
  $("#add-new-alert-button").click(function (event) {
    $("#alert-editor-title").text("Neue Alert");
  });
};
