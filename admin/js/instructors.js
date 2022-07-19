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
          $("<th />", { scope: "col", text: "Aktionen" })
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
    $("body").off("click", "#edit-instructor-save-button", editInstructorEvent);
    $("body").on("click", "#edit-instructor-save-button", newInstructorEvent);
    $("#edit-instructor-form").trigger("reset");
    $("#edit-instructor-dialog").modal("show");
  });

  // display instructors
  $.each(response.instructors, function (index, instructor) {
    var row = buildInstructorTableRow(instructor);
    $("#instructors-table-body").append(row);
  });

  $("#avatar-upload").change(function () {
    var len = $(this).length;
    var filedat = $(this)[0].files[0];
    var ext = filedat.name.split(".").pop().toLowerCase();
    if (jQuery.inArray(ext, ["png", "jpg", "jpeg"]) == -1) {
      alert("Invalid Image File");
      return;
    }
    var form_data = new FormData();

    var oFReader = new FileReader();
    oFReader.readAsDataURL(filedat);
    var fsize = filedat.size || filedat.fileSize;
    if (fsize > 2000000) {
      alert("Image File Size is very big");
    } else {
      form_data.append("file", filedat);
      $.ajax({
        url: "../backend/rest.php?apiFunc=uploadimg",
        method: "POST",
        data: form_data,
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
          $("#avatar-wrapper").empty();
          $("#avatar-wrapper").append(
            $("<label />", {
              class: "text-success",
              text: "Image Uploading ...",
            })
          );
        },
        success: function (data) {
          var payload = JSON.parse(data);
          var img = buildThumbnailFromUpload(payload);
          $("#avatar-wrapper").empty();
          $("#avatar-wrapper").append(img);
          //$("#avatar").attr("src", data.url);
          $("#editInstructorThumbnailUrl").val(payload.imageurl);
        },
      });
    }
  });
};

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
  var url = "../backend/rest.php?apiFunc=getInstructor&id=" + id;
  $.get(url, function (res) {
    var response = JSON.parse(res);
    if (response.status == 200) {
      $("#edit-instructor-form").trigger("reset");
      $("#editInstructorId").val(response.instructor.id);
      $("#editInstructorLastname").val(response.instructor.lastname);
      $("#editInstructorFirstname").val(response.instructor.firstname);
      $("#inputInstructorEmail").val(response.instructor.email);
      $("#inputInstructorPhone").val(response.instructor.phone);
      $("#inputInstructorMobile").val(response.instructor.mobile);
      $("#inputInstructorPosition").val(response.instructor.position);
      $("#inputInstructorDescription").val(response.instructor.description);
      $("#editInstructorThumbnailUrl").val(response.instructor.imageurl);
      var thumb = buildThumbnail(response.instructor);
      $("#imagewrapper").empty();
      $("#imagewrapper").append(thumb);
      $("body").off(
        "click",
        "#edit-instructor-save-button",
        newInstructorEvent
      );
      $("body").on(
        "click",
        "#edit-instructor-save-button",
        editInstructorEvent
      );
      $("#edit-instructor-dialog").modal("show");
    } else {
      console.log(response);
    }
  });
};

const newInstructorEvent = function (event) {
  event.stopImmediatePropagation();
  var newInstructorData = Object.create(Instructor);
  newInstructorData.firstname = $("#editInstructorFirstname").val();
  newInstructorData.lastname = $("#editInstructorLastname").val();
  var email = $("#inputInstructorEmail").val();
  newInstructorData.email = email;
  var phone = $("#inputInstructorPhone").val();
  if (phone != null && phone != "") {
    newInstructorData.phone = phone;
  }
  var mobile = $("#inputInstructorMobile").val();

  newInstructorData.mobile = mobile;

  var position = $("#inputInstructorPosition").val();

  newInstructorData.position = position;

  var description = $("#inputInstructorDescription").val();

  newInstructorData.description = description;

  var imageurl = $("#editInstructorThumbnailUrl").val();

  newInstructorData.imageurl = imageurl;

  sendNewInstructor(newInstructorData);
  $("body").off("click", "#edit-instructor-save-button", newInstructorEvent);
};

const sendNewInstructor = function (newInstructorData) {
  console.log(newInstructorData);
  var url = "../backend/rest.php?apiFunc=newInstructor";
  $.post(url, JSON.stringify(newInstructorData), function (res) {
    var response = JSON.parse(res);
    if (response.status == 200) {
      displayInstructors(response);
    } else {
      console.log(response);
    }
  });
};

const editInstructorEvent = function (event) {
  event.stopImmediatePropagation();
  var editInstructorData = new Object();
  editInstructorData.id = $("#editInstructorId").val();
  editInstructorData.firstname = $("#editInstructorFirstname").val();
  editInstructorData.lastname = $("#editInstructorLastname").val();
  editInstructorData.email = $("#inputInstructorEmail").val();
  editInstructorData.phone = $("#inputInstructorPhone").val();
  editInstructorData.mobile = $("#inputInstructorMobile").val();
  editInstructorData.position = $("#inputInstructorPosition").val();
  editInstructorData.description = $("#inputInstructorDescription").val();
  editInstructorData.imageurl = $("#editInstructorThumbnailUrl").val();
  sendEditInstructor(editInstructorData);
  $("body").off("click", "#edit-instructor-save-button", editInstructorEvent);
};

const sendEditInstructor = function (editInstructorData) {
  var url = "../backend/rest.php?apiFunc=updateInstructor";
  $.post(url, JSON.stringify(editInstructorData), function (res) {
    var response = JSON.parse(res);
    if (response.status == 200) {
      displayInstructors(response);
    } else {
      console.log(response);
    }
  });
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

const buildThumbnailFromUpload = function (payload) {
  console.log(payload);
  var img = $("<img />", {
    src: "../" + payload.imageurl,
    alt: "Profile Picture From Upload",
    class: "thumbnail-wrapper",
    id: "avatar",
  });

  return img;
};

const buildThumbnail = function (instructor) {
  var img;
  if (instructor && instructor.imageurl) {
    img = $("<img />", {
      src: "../" + instructor.imageurl,
      alt: "Profile picture for instructor " + instructor.id,
      class: "thumbnail-wrapper",
      id: "avatar",
    });
  } else {
    var num = randomInt(5, 1);
    img = $("<img />", {
      src: "./img/avatar-" + num + ".svg",
      alt: "Default profile picture for instructor",
      class: "thumbnail-wrapper",
      id: "avatar",
    });
  }
  var wrapper = $("<div />", {
    class: "thumbnail-wrapper",
    id: "avatar-wrapper",
  }).append(img);
  return wrapper;
};
