const displayInstructors = function (response) {
  $("#content-window").empty();
  $("#content-window").append(
    $("<div />", { class: "col-12", id: "instructors-wrapper" }).append(
      $("<table />", { class: "table", id: "instructors-table" }).append(
        $("<thead />").append(
          $("<th />", { scope: "col", text: "ID" }),
          $("<th />", { scope: "col", text: "Bild" }),
          $("<th />", { scope: "col", text: "Name" }),
          $("<th />", { scope: "col", text: "Vorname" }),
          $("<th />", { scope: "col", text: "AktionenS" })
        ),
        $("<tbody />", { id: "instructors-table-body" }),
        $("<tfoot />", { id: "instructors-table-footer" }).append(
          $("<th />", { colspan: "7" }).append(
            $("<button />", {
              class: "btn btn-primary",
              id: "add-new-instructor-button",
              text: "Neue Dozentin ",
            }).append($("<i />", { class: "fas fa-user-plus" }))
          )
        )
      )
    )
  );
  // set new instructor action
  $("#add-new-instructor-button").click(function (event) {
    $("#instructor-editor-title").text("Neue Dozentin");
    $("body").off("#save-instructor-button", editInstructorEvent);
    $("body").on("#save-instructor-button", newInstructorEvent);
    $("#edit-instructor-form").trigger("reset");
    $("#edit-instructor-dialog").modal("show");
  });

  // display instructors
  $.each(response.instructors, function (index, instructor) {
    var row = buildInstructorTableRow(instructor);
    $("#instructors-table-body").append(row);
  });
};

const editInstructorEvent = function (event) {};

const newInstructorEvent = function (event) {};

const buildInstructorTableRow = function (instructor) {
  var thumbnail = buildThumbnail(instructor);
  var editLink = createEditInstructorLink(instructor);
  var deleteLink = createDeleteInstructorLink(instructor);
  var row = $("<tr />").append(
    $("<td />", { text: instructor.id }),
    $("<td />").append(thumbnail),
    $("<td />", { text: instructor.lastname }),
    $("<td />", { text: instructor.firstname }),
    $("<td />").append(editLink, deleteLink)
  );
  return row;
};

const createEditInstructorLink = function (instructor) {
  var linkId = "link-edit-instructor-" + instructor.id;
  var link = $("<a />", { id: linkId, text: " " }).click(function (event) {
    event.preventDefault();
    editInstructor(this);
  });
  $(link).attr("data-instructor", instructor.id);
  $(link).append($("<i />", { class: "fas fa-user-edit linkchar" }));
  return link;
};

const createDeleteInstructorLink = function (instructor) {
  var linkId = "link-delete-instructor-" + instructor.id;
  var link = $("<a />", { id: linkId, text: " " }).click(function (event) {
    event.preventDefault();
    deleteInstructor(this);
  });
  $(link).attr("data-instructor", instructor.id);
  $(link).append($("<i />", { class: "fas fa-user-slash linkchar" }));
  return link;
};

const editInstructor = function (ele) {
  var id = $(ele).data("instructor");
};

const deleteInstructor = function (ele) {
  var id = $(ele).data("instructor");
  var url = "../backend/rest.php?apiFunc=getInstructor&id=" + id;
  $.get(url, function (res) {
    var response = JSON.parse(res);
    if (response.status == 200) {
      $("#delete-instructor-acknowledge-button").attr(
        "data-id",
        response.user.id
      );
      $("#delete-instructor").text(
        "'" + response.user.last_name + ", " + response.user.first_name + "'"
      );
      $("#delete-instructor-dialog").modal("show");
    } else {
      console.log(response);
    }
  });
};

const sendDeleteInstructor = function (id) {
  var url = "../backend/rest.php?apiFunc=deleteInstructor&id=" + id;
  $.get(url, function (res) {
    var response = JSON.parse(res);
    if (response.status == 200) {
      displayInstructors(response);
    } else {
      console.log(response);
    }
  });
};

const buildThumbnail = function (instructor) {
  var img;
  if (instructor && instructor.imageurl) {
    img = $("<img />", {
      src: instructor.imageurl,
      alt: "Profile picture for instructor " + instructor.id,
      class: "thumbnail-wrapper",
    });
  } else {
    var num = randomInt(5, 1);
    img = $("<img />", {
      src: "./img/avatar-" + num + ".svg",
      alt: "Default profile picture for instructor",
      class: "thumbnail-wrapper",
    });
  }
  var wrapper = $("<div />", { class: "thumbnail-wrapper" }).append(img);
  return wrapper;
};
