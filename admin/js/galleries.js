const displayGalleries = function (res) {
  $("#content-window").empty();
  $("#content-window").append(
    $("<div />", { class: "col-12", id: "galleries-wrapper" }).append(
      $("<table />", { class: "table", id: "galleries-table" }).append(
        $("<thead />").append(
          $("<th />", { class: "text-center", scope: "col", text: "ID" }),
          $("<th />", { class: "text-center", scope: "col", text: "Name" }),
          $("<th />", {
            scope: "col",
            text: "Beschreibung",
          }),
          $("<th />", { class: "text-center", scope: "col", text: "Aktiv?" }),
          $("<th />", { class: "text-center", scope: "col", text: "Aktionen" })
        ),
        $("<tbody />", { id: "galleries-table-body" }),
        $("<tfoot />", { id: "galleries-table-footer" }).append(
          $("<th />", { colspan: 5 }).append(
            $("<button />", {
              class: "btn btn-primary mr3",
              id: "add-new-gallery-button",
              text: "Neue Gallery ",
            }).append($("<i />", { class: "fi-xnsuxl-image-solid" })),

            $("<button />", {
              class: "btn btn-secondary ml3",
              id: "upload-photo-button",
              text: "Foto hochladen ",
            }).append($("<i />", { class: "fi-xnsuxl-upload-solid" }))
          )
        )
      )
    )
  );

  $("#add-new-gallery-button").click(function (event) {
    // load gallery editor
  });

  $("#upload-photo-button").click(function (event) {
    // load photo uploader
  });

  $.each(res.galleries, function (index, gallery) {
    var row = buildGalleryTableRow(gallery);
    $("#galleries-table-body").append(row);
  });
  friconix_update();
};

const buildGalleryTableRow = function (gallery) {
  var rowId = "gallery-item-" + gallery.id;
  var activeImg;
  if (gallery.active == 1) {
    activeImg = $("<i />", { class: "fi-cwsuxl-check checkmark" });
  } else {
    activeImg = $("<i />", { class: "fi-cnsuxl-times-solid nocheck" });
  }
  var row = $("<tr />", { id: rowId }).append(
    $("<td />", { class: "text-center", text: gallery.id }),
    $("<td />", { class: "text-center", text: gallery.name }),
    $("<td />", { text: gallery.description }),
    $("<td />", { class: "text-center" }).append(activeImg),
    $("<td/>", { class: "text-center" })
  );
  return row;
};