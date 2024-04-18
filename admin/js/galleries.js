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
          $("<th />", {
            class: "text-center",
            scope: "col",
            text: "Aktive Galerie umschalten",
          }),
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
    $("#upload-gallery-image-dialog").modal("show");
  });

  $.each(res.galleries, function (index, gallery) {
    var row = buildGalleryTableRow(gallery);
    $("#galleries-table-body").append(row);
  });
  friconix_update();

  $("#gallery-image-upload-button").click(function () {
    let formInput = $("#gallery-image");
    var filedat = $(formInput)[0].files[0];
    var ext = filedat.name.split(".").pop().toLowerCase();
    if (jQuery.inArray(ext, ["png", "jpg", "jpeg", "webp"]) == -1) {
      alert("Invalid image file");
      return;
    }
    var altText = $("#gallery-image-text").val();
    if (!altText || altText == "") {
      altText = filedat.name;
    }
    var form_data = new FormData();
    var oFReader = new FileReader();
    oFReader.readAsDataURL(filedat);
    form_data.append("file", filedat);
    form_data.append("alt", altText);
    $.ajax({
      url: "../backend/rest.php?apiFunc=uploadgal",
      method: "POST",
      data: form_data,
      contentType: false,
      processData: false,
      success: function (data) {
        uploadComplete(data);
      },
    });
  });
};

function uploadComplete(res) {
  $("#image-name").empty();
  $("#image-name").text(res.name);
  $("#upload-gallery-success").modal("show");
}

const buildGalleryTableRow = function (gallery) {
  var rowId = "gallery-item-" + gallery.id;
  var editGalleryLink = createEditLink(gallery);
  var deleteGalleryLink = createDeleteLink(gallery);
  var managePhotosLink = createManagePhotosLink(gallery);
  var activeLink = createSetActiveLink(gallery);

  var row = $("<tr />", { id: rowId }).append(
    $("<td />", { class: "text-center", text: gallery.id }),
    $("<td />", { class: "text-center", text: gallery.name }),
    $("<td />", { text: gallery.description }),
    $("<td />", { class: "text-center" }).append(activeLink),
    $("<td/>", { class: "text-center" }).append(
      managePhotosLink,
      editGalleryLink,
      deleteGalleryLink
    )
  );
  return row;
};

function createEditLink(gallery) {
  var galId = "edit-gallery-" + gallery.id;
  var link = $("<a />", { id: galId, text: " " }).click(function (event) {
    event.preventDefault();
    var id = $(this).attr("data-id");
    editGallery(id);
  });
  $(link).attr("data-id", galId);
  $(link).append($("<i />", { class: "fi-xnsuxl-edit-solid linkchar" }));
  return link;
}

function createSetActiveLink(gallery) {
  var actLink = "activate-gallery-" + gallery.id;
  var link = $("<a />", { id: actLink, text: " " }).click(function (event) {
    event.preventDefault();
    var active = $(this).attr("data-active");
    var id = $(this).attr("data-id");
    if (active == 0) {
      selectActive(id);
    }
  });
  $(link).attr("data-id", gallery.id);
  $(link).attr("data-active", gallery.active);
  if (gallery.active == 1) {
    $(link).addClass("isActive");
    $(link).attr("title", "bereits aktiv!");
    $(link).append($("<i />", { class: "fi-swluxl-thumbtack-alt linkchar" }));
  } else {
    $(link).addClass("notActive");
    $(link).append($("<i />", { class: "fi-swpuxl-thumbtack-alt linkchar" }));
  }

  return link;
}

function selectActive(id) {
  //gallery_id
  var url = "../backend/rest.php?apiFunc=setGal&gallery_id=" + id;
  $.get(url, function (res) {
    if (res.status == 200) {
      displayGalleries(res);
    } else {
      console.error(res);
    }
  });
}

function createManagePhotosLink(gallery) {
  var linkId = "manage-gallery-" + gallery.id;
  var link = $("<a />", { id: linkId, text: " " }).click(function (event) {
    event.preventDefault();
    var galId = $(this).attr("data-id");
    loadAssignedGalleryImages(galId);
  });
  $(link).attr("data-id", gallery.id);
  $(link).append($("<i />", { class: "fi-xnsuxl-image-solid linkchar" }));
  return link;
}

function createDeleteLink(gallery) {
  var linkId = "del-gallery-" + gallery.id;
  var link = $("<a />", { id: linkId, text: " " }).click(function (event) {
    event.preventDefault();
    var galId = $(this).data("id");
    var url = "../backend/rest.php?apiFunc=findGal&gallery_id=" + galId;
    $.get(url, function (res) {
      if (res.status == 200) {
        console.log(res);
        $("#delete-gallery-acknowledge-button").data(
          "id",
          res.gallery.gallery_id
        );
        $("#delete-gallery-name").text("'" + res.gallery.gallery_name + "'");
        $("#delete-gallery-dialog").modal("show");
      } else {
        console.log(res);
      }
    });
  });
  $(link).attr("data-id", gallery.id);
  $(link).append($("<i />", { class: "fi-xwsuxl-bin linkchar" }));
  return link;
}

function editGallery(galleryId) {}

function loadAssignedGalleryImages(galleryId) {
  // get all assigned images
  // get all active images;
  // display in listing
}

function sendDeleteGallery(id) {
  var url = "../backend/rest.php?apiFunc=delGal&gallery_id=" + id;
  $.get(url, function (res) {
    if (res.status == 200) {
      displayGalleries(res);
    } else {
      console.error(res);
    }
  });
}
