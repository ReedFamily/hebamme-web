const getTeam = function () {
  jQuery.get("backend/rest.php?apiFunc=getToken", function (res) {
    response = JSON.parse(res);
    if (response.status == 200) {
      var apiToken = response.token;
      var url =
        "backend/rest.php?apiToken=" + apiToken + "&apiFunc=listInstructors";
      jQuery.get(url, function (res1) {
        response1 = JSON.parse(res1);
        if (response1.status == 200) {
          buildTeamCards(response1);
        } else {
          console.log(response1);
        }
      });
    } else {
      console.log(response);
    }
  });
};

const buildTeamCards = function (payload) {
  var contentDiv = $("#team-wrapper");
  jQuery.each(payload.instructors, function (index, instructor) {
    var cardWrapper = jQuery("<div />", { class: "col-md-4, card-wrapper" });
    var card = jQuery("<div />", {
      class: "card",
      id: "team-member-" + instructor.id,
    });
    var cardheader = jQuery("<h4 />", {
      class: "card-title",
      text: instructor.firstname + " " + instructor.lastname,
    });
    var img = jQuery("<img />", {
      class: "card-img-top",
      src: "./" + instructor.imageurl,
      id: "team-member-img-" + instructor.id,
    });
    var cardbody = jQuery("<div />", { class: "card-body" });
    var position = jQuery("<h5 />", {
      class: "card-subtitle",
      text: instructor.position,
    });
    var descript = jQuery("<p />", {
      class: "card-text",
      text: instructor.description,
    });

    cardbody.append(cardheader);
    cardbody.append(position);
    cardbody.append(descript);

    card.append(img);
    card.append(cardbody);
    cardWrapper.append(card);
    contentDiv.append(cardWrapper);
  });
};

const getHomeAlerts = function () {
  jQuery.get("backend/rest.php?apiFunc=getToken", function (res) {
    response = JSON.parse(res);
    if (response.status == 200) {
      var apiToken = response.token;
      var url =
        "backend/rest.php?apiToken=" +
        apiToken +
        "&apiFunc=locMsgs&location=home";
      jQuery.get(url, function (res1) {
        response1 = JSON.parse(res1);
        if (response1.status == 200) {
          buildHomeAlerts(response1.messages);
        } else {
          console.log(response1);
        }
      });
    } else {
      console.log(response);
    }
  });
};

const buildHomeAlerts = function (payload) {
  var msgSect = $("#home-alerts");
  if (payload.length === 0) {
    msgSect.toggleClass("hidden");
  } else {
    msgSect.toggleClass("hidden");
    var highAlertsCont = $("#home-high-alerts");
    var warnAlertsCont = $("#home-warn-alerts");
    var infoAlertsCont = $("#home-info-alerts");
    $.each(payload, function (index, msg) {
      var alert = $("<div />", {
        class: "alert",
        role: "alert",
        text: msg.message,
      });
      var clzz = "";
      switch (msg.level) {
        case "red":
          clzz = "alert-danger";
          highAlertsCont.append(alert);
          break;
        case "yellow":
          clzz = "alert-warning";
          warnAlertsCont.append(alert);
          break;
        default:
          clzz = "alert-info";
          infoAlertsCont.append(alert);
          break;
      }
      alert.addClass(clzz);
    });
  }
};
