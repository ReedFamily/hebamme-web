const getTeam = function () {
  var url = "backend/rest.php?apiFunc=listInstructors&visible=true";
  jQuery.get(url, function (res1) {
    response1 = res1;
    if (response1.status == 200) {
      buildTeamCards(response1);
    } else {
      console.log(response1);
    }
  });
};

const convertMDtoHtml = function (mdData) {
  var converter = new showdown.Converter();
  return converter.makeHtml(mdData);
};

const buildTeamCards = function (payload) {
  var contentDiv = $("#team-wrapper");
  var dozentinDiv = $("#dozentin-wrapper");
  jQuery.each(payload.instructors, function (index, instructor) {
    var cardWrapper = jQuery("<div />", { class: "col-md-4 card-wrapper" });
    var card = jQuery("<div />", {
      class: "card team-member",
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
      alt: instructor.firstname + " " + instructor.lastname,
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
    var nachsorge = jQuery("<p />", { class: "card-text" });
    var reglink = jQuery("<a />", {
      href: instructor.hebamiolink,
      text: "Anfrage für Wochenbettbetreuung",
    });
    nachsorge.append(reglink);

    cardbody.append(cardheader);
    cardbody.append(position);
    cardbody.append(descript);
    if (instructor.hebamiolink) {
      cardbody.append(nachsorge);
    }

    card.append(img);
    card.append(cardbody);
    cardWrapper.append(card);
    if (instructor.team == 1) {
      contentDiv.append(cardWrapper);
    } else {
      dozentinDiv.append(cardWrapper);
    }
  });

  if (dozentinDiv.children().length == 0) {
    $("#dozentin-header").addClass("hidden");
  } else {
    $("#dozentin-header").removeClass("hidden");
  }
};

const getHomeAlerts = function () {
  var url = "backend/rest.php?apiFunc=locMsgs&location=home";
  jQuery.get(url, function (homeMsgRes) {
    response1 = homeMsgRes;
    if (response1.status == 200) {
      buildHomeAlerts(response1.messages);
    } else {
      console.log(response1);
    }
  });
};

const buildHomeAlerts = function (payload) {
  var msgSect = $("#home-alerts");
  if (payload.length === 0) {
    msgSect.remove();
  } else {
    var highAlertsCont = $("#home-high-alerts");
    var warnAlertsCont = $("#home-warn-alerts");
    var infoAlertsCont = $("#home-info-alerts");
    $.each(payload, function (index, msg) {
      var alert = $("<div />", {
        class: "alert",
        role: "alert",
        html: convertMDtoHtml(msg.message),
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

const getStyleFromDescription = function (descript) {
  if (descript.includes("Geburtsvorbereitung")) {
    return "gbv";
  }

  if (descript.includes("Rückbildung")) {
    return "rubi";
  }

  if (descript.includes("Yoga")) {
    return "yoga";
  }

  if (descript.includes("Erste Hilfe")) {
    return "eh";
  }

  if (descript.includes("Babytreff")) {
    return "bt";
  }

  if (descript.includes("Babypflege")) {
    return "bp";
  }

  return "other";
};

const getKursAlerts = function () {
  var url = "backend/rest.php?apiFunc=locMsgs&location=kurse";
  jQuery.get(url, function (homeMsgRes) {
    response1 = homeMsgRes;
    if (response1.status == 200) {
      buildKursAlerts(response1.messages);
    } else {
      console.log(response1);
    }
  });
};

const buildKursAlerts = function (payload) {
  var msgSect = $("#kurse-alerts");
  if (payload.length === 0) {
    msgSect.remove();
  } else {
    var highAlertsCont = $("#kurse-high-alerts");
    var warnAlertsCont = $("#kurse-warn-alerts");
    var infoAlertsCont = $("#kurse-info-alerts");
    $.each(payload, function (index, msg) {
      var alert = $("<div />", {
        class: "alert",
        role: "alert",
        html: convertMDtoHtml(msg.message),
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

const getClassInfo = function () {
  getKursAlerts();
  var url = "backend/rest.php?apiFunc=classes";
  jQuery.get(url, function (classRes) {
    if (classRes.status == 200) {
      var gbv = 0;
      var rubi = 0;
      var bt = 0;
      var eh = 0;
      var bp = 0;
      var yoga = 0;
      var other = 0;
      $.each(classRes.classes, function (index, classDetail) {
        var classWrapper = $("#" + classDetail.type + "-wrapper");
        var styleClass = "card-header " + classDetail.type;
        var footerStyle = "card-footer " + classDetail.type;
        var cardbodyStyle = "card-body collapse";
        var classId = classDetail.id;
        var className = classDetail.name;
        var btnUrl = classDetail.detail.hebamio_link;
        var classStartDate = classDetail.detail.date_start;
        var classEndDate = classDetail.detail.date_end;
        var classPartnerPrice = classDetail.detail.price_partner;
        var classPrice = classDetail.detail.price;
        var classMaxParticipants = classDetail.detail.max_paticipants;
        var classAvailable = classDetail.detail.available_space;
        var locationName = classDetail.detail.location.title;
        var locationAddress = classDetail.detail.location.address;
        var isFull = false;
        var isExpanded = false;
        if (classAvailable <= 0) {
          isFull = true;
        }
        var collapseButton = $("<div />", {
          class: "close",
        }).append(
          $("<i />", {
            class: "fi-xwsdxl-chevron-wide close",
            id: "class-toggler-icon" + classId,
          })
        );
        var classCard = $("<div />", {
          class: "card courses-list",
          id: "course-" + classId,
        });
        var classCardHeader = $("<div />", {
          class: styleClass,
          type: "button",
          role: "button",
        })
          .attr("data-toggle", "collapse")
          .attr("data-target", "#course-body-" + classId)
          .attr("data-classId", classId)
          .attr("aria-expanded", isExpanded)
          .attr("aria-controls", "course-body-" + classId)
          .append(
            $("<span />", {
              class: "card-title",
              text: className + " - " + classStartDate + " bis " + classEndDate,
            })
          )
          .append(collapseButton);

        var classCardFooter = $("<div />", { class: footerStyle });
        var footerContent;
        if (isFull === true) {
          footerContent = $("<span />", {
            class: "btn btn-secondary full-course",
            text: "Ausgebucht",
          });
        } else {
          footerContent = $("<a />", {
            class: "btn btn-local",
            role: "button",
            href: btnUrl,
            text: "Anmeldung",
          });
        }
        $(classCardFooter).append(footerContent);

        var classCardBody = $("<div />", {
          class: cardbodyStyle,
          id: "course-body-" + classId,
        });
        var classBodyWo = $("<p />");
        $(classBodyWo)
          .append("Wo: ")
          .append(locationName)
          .append($("<br />"))
          .append(locationAddress);
        var classBodyAvailable = $("<p />");
        $(classBodyAvailable)
          .append("Verfügbare Plätze: ")
          .append(classAvailable)
          .append(" von ")
          .append(classMaxParticipants);

        var classBodyPartner = $("<p />");
        $(classBodyPartner)
          .append("Partnergebühr: €")
          .append(classPartnerPrice);
        var classBodyPrice = $("<p />");
        $(classBodyPrice).append("Gebühr: €").append(classPrice);
        var classBodyTermine = $("<div />");
        var classBodyTermineTitle = $("<p />").append("Termine: ");
        var classBodyTermineList = $("<ul />");
        $.each(classDetail.detail.dates, function (n, dateDetail) {
          var termineItem = $("<li />");
          $(termineItem)
            .append(dateDetail.date)
            .append(" um ")
            .append(dateDetail.time_start)
            .append(" bis ")
            .append(dateDetail.time_end)
            .append(" mit ")
            .append(dateDetail.date_instructor);
          if (dateDetail.description) {
            $(termineItem)
              .append("<br />")
              .append(
                $("<span />", {
                  class: "termine-descript",
                  text: dateDetail.description,
                })
              );
          }
          $(classBodyTermineList).append(termineItem);
        });

        $(classBodyTermine)
          .append(classBodyTermineTitle)
          .append(classBodyTermineList);

        $(classCardBody).append(classBodyWo).append(classBodyAvailable);
        if (classPrice) {
          $(classCardBody).append(classBodyPrice);
        }
        if (classPartnerPrice) {
          $(classCardBody).append(classBodyPartner);
        }
        $(classCardBody).append(classBodyTermine);
        $(classCard)
          .append(classCardHeader)
          .append(classCardBody)
          .append(classCardFooter);
        $(classWrapper).append(classCard);
        friconix_update();
        switch (classDetail.type) {
          case "gbv":
            gbv++;
            break;
          case "rubi":
            rubi++;
            break;
          case "bt":
            bt++;
            break;
          case "eh":
            eh++;
            break;
          case "bp":
            bp++;
            break;
          case "yoga":
            yoga++;
            break;
          default:
            other++;
            break;
        }
      });
      if (gbv == 0) {
        //$("#gbv-classes").remove();
        $("#gbv-wrapper").append(
          "<p>Aktuell sind leider keine Geburtsvorbereitungskurse geplant.</p>"
        );
      }
      if (rubi == 0) {
        //$("#rubi-classes").remove();
        $("#rubi-wrapper").append(
          "<p>Aktuell sind leider keine Rückbildungskurse geplant.</p>"
        );
      }
      if (bt == 0) {
        $("#bt-classes").remove();
        //$("#bt-wrapper").append("<p>Aktuell sind leider keine Babytreffs geplant.</p>");
      }
      if (eh == 0) {
        //$("#eh-classes").remove();
        $("#eh-wrapper").append(
          "<p>Aktuell sind leider keine Erste-Hilfe-Kurse geplant.</p>"
        );
      }
      if (bp == 0) {
        //$("#bp-classes").remove();
        $("#bp-wrapper").append(
          "<p>Aktuell sind leider keine Babypflegekurse geplant.</p>"
        );
      }
      if (yoga == 0) {
        //$("#yoga-classes").remove();
        $("#yoga-wrapper").append(
          "<p>Aktuell sind leider keine Yogakurse geplant.</p>"
        );
      }
      if (other == 0) {
        //$("#other-classes").remove();
        $("#other-wrapper").append(
          "<p>Aktuell sind leider keine sonstigen Kurse geplant.</p>"
        );
      }
    } else {
      console.log(classRes);
    }
  });
};

const buildFaqList = function () {
  let url = "backend/rest.php?apiFunc=faqs";
  let wrapper = $("#faq-wrapper");
  wrapper.empty();
  $.get(url, function (res) {
    if (res.status == 200) {
      $.each(res.faqs, function (index, faq) {
        let faqCard = $("<div />", { id: "faq-id-" + faq.id, class: "card" });
        let faqCollapse = $("<div />", {
          class: "close",
          id: "faq-toggler-icon-container-" + faq.id,
        }).append(
          $("<i />", {
            class: "fi-xwsdxl-chevron-wide close",
            id: "faq-toggler-icon-" + faq.id,
          })
        );
        let faqHeader = $("<div />", {
          class: "card-header faq-card-header",
          id: "faq-header-" + faq.id,
          role: "button",
        })
          .attr("data-toggle", "collapse")
          .attr("data-target", "#faq-body-" + faq.id)
          .attr("data-faqId", faq.id)
          .attr("aria-expanded", false)
          .attr("aria-controls", "faq-body-" + faq.id)
          .attr("aria-label", "open or close question")
          .append(
            $("<span />", {
              class: "card-title",
              type: "button",
              role: "button",
              id: "faq-title-" + faq.id,
              text: faq.question + " ",
            }).append(faqCollapse)
          );
        let faqBody = $("<div />", {
          class: "card-body collapse faq-card-body",
          id: "faq-body-" + faq.id,
          html: convertMDtoHtml(faq.message),
        });

        faqCard.append(faqHeader, faqBody);
        wrapper.append(faqCard);
        friconix_update();
      });
    } else {
      $("#faq").remove();
    }
  });
};
