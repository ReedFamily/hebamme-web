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
  var contentDiv = $("#team-content");
  jQuery.each(payload.instructors, function (index, instructor) {
    var card = jQuery("<div />", {
      class: "card",
      id: "team-member-" + instructor.id,
    });
    var cardheader = jQuery("<div />", {
      class: "card-header",
      text: instructor.firstname + " " + instructor.lastname,
    });
    var img = jQuery("<img />", {
      class: "card-img-top midwife-img",
      src: "./" + instructor.imageurl,
    });
    var cardbody = jQuery("<div />", { class: "card-body" });
    var position = jQuery("<p />", {
      class: "card-text",
      text: instructor.position,
    });
    var descript = jQuery("<p />", {
      class: "card-text",
      text: instructor.description,
    });

    card.append(cardheader);
    card.append(img);
    card.append(cardbody);
    cardbody.append(position);
    cardbody.append(descript);

    contentDiv.append(card);
  });
};
