const displayFaqs = function(response) {
    $("#content-window").empty();
    $("#content-window").append(
        $("<div />", { class: "col-12", id: "faqs-wrapper" }).append(
            $("<table />", { class: "table", id: "faqs-table" }).append(
                $("<thead />").append(
                    $("<th />", { class: "text-center", scope: "col", text: "ID" }),
                    $("<th />", { class: "text-center", scope: "col", text: "Fragen" }),
                    $("<th />", { class: "text-center", scope: "col", text: "Aktionen" })
                ),
                $("<tbody />", { id: "faqs-table-body" }),
                $("<tfoot />", { id: "faqs-table-footer" }).append(
                    $("<th />", { colspan: "3" }).append(
                        $("<button />", {
                            class: "btn btn-primary",
                            id: "add-new-faq-button",
                            text: "Neue Frage   ",
                        }).append($("<i />", { class: "fi-cnsuxl-question-mark" }))
                    )
                )
            )
        )
    );

    $("#add-new-faq-button").click(function(event) {
        $("#faq-editor-title").text("Neue Frage");
        $("body").off("click", "#edit-faq-save-button", editFaqEvent);
        $("body").on("click", "#edit-faq-save-button", newFaqEvent);
        $("#edit-faq-form").trigger("reset");
        $("#edit-faq-dialog").modal("show");
    });

    $.each(response.faqs, function(index, faq) {
        var row = buildFaqTableRow(faq);
        $("#faqs-table-body").append(row);
    });
    friconix_update();
};

const buildFaqTableRow = function(faqItem) {
    var editLink = createEditFaqLink(faqItem.id);
    var delLink = createDeleteFaqLink(faqItem.id);
    var rowId = "faq-item-" + faqItem.id;
    var row = $("<tr />", { id: rowId }).append(
        $("<td />", { class: "text-center", text: faqItem.id }),
        $("<td />", { class: "text-center", text: faqItem.question }),
        $("<td />", { class: "text-center" }).append(editLink, delLink)
    );
    return row;
};

const createEditFaqLink = function(faqId) {
    var linkId = "edit-faq-" + faqId;
    var link = $("<a />", { id: linkId, text: " " }).click(function(event) {
        event.preventDefault();
        editFaq(this);
    });
    $(link).attr("data-id", faqId);
    $(link).append($("<i />", { class: "fi-xnsuxl-edit-solid linkchar" }));
    return link;
};

const editFaq = function(lnk) {
    var id = $(lnk).data("id");
    var url = "../backend/rest.php?apiFunc=getFaq&id=" + id;
    $.get(url, function(res) {
        if (res.status == 200) {
            $("body").off("click", "#edit-faq-save-button", newFaqEvent);
            $("body").on("click", "#edit-faq-save-button", editFaqEvent);
            $("#edit-faq-form").trigger("reset");
            $("#faqId").val(res.faq.id);
            $("#faqQuestion").val(res.faq.question);
            $("#faqMessage").val(res.faq.message);
            $("#edit-faq-dialog").modal("show");
        }
    });
};

const createDeleteFaqLink = function(faqId) {
    var linkId = "delete-faq-id" + faqId;
    var link = $("<a />", { id: linkId, text: " " }).click(function(event) {
        event.preventDefault();
        deleteFaq(this);
    });
    $(link).attr("data-id", faqId);
    $(link).append($("<i />", { class: "fi-xwsuxl-bin linkchar" }));
    return link;
};

const deleteFaq = function(ele) {
    var id = $(ele).data("id");
    var url = "../backend/rest.php?apiFunc=getFaq&id=" + id;
    $.get(url, function(res) {
        if (res.status == 200) {
            $("#delete-faq-acknowledge-button").data("id", res.faq.id);
            $("#delete-faq").text(res.faq.question);
            $("#delete-faq-dialog").modal("show");
        } else {
            console.log(res);
        }
    });
};

const sendDeleteFaq = function(id) {
    var url = "../backend/rest.php?apiFunc=delFaq&id=" + id;
    $.get(url, function(res) {
        if (res.status == 200) {
            $("#delete-faq-acknowledge-button").removeData("id");
            displayFaqs(res);
        } else {
            console.log(res);
        }
    });
};

const validateFaqBeforeSave = function(isNew) {
    var faqFormId = $("#faqId").val();
    if (isNew != true) {
        if (faqFormId && Number(faqFormId) > 0 == false) {
            throw { err: "#faqId" };
        }
        var question = $("#faqQuestion").val();
        var message = $("#faqMessage").val();
        if (isEmpty(question)) {
            throw { err: "#faqQuestion" };
        }
        if (isEmpty(message)) {
            throw { err: "#faqMessage" };
        }
    }
};

const editFaqEvent = function(event) {
    event.stopImmediatePropagation();
    try {
        validateFaqBeforeSave(true);
    } catch (e) {
        let item = e.err;
        $(item).addClass("erroredFormControl");
        return;
    }
    var editedFaqData = new Object();

    editedFaqData.id = $("#faqId").val();
    editedFaqData.question = $("#faqQuestion").val();
    editedFaqData.message = $("#faqMessage").val();
    var url = "../backend/rest.php?apiFunc=editFaq";
    $.post(url, JSON.stringify(editedFaqData), function(res) {
        if (res.status == 200) {
            displayFaqs(res);
        }
    });
};

const newFaqEvent = function(event) {
    event.stopImmediatePropagation();
    try {
        validateFaqBeforeSave(true);
    } catch (e) {
        let item = e.err;
        $(item).addClass("erroredFormControl");
        return;
    }

    var newFaqData = new Object();
    if ($("#faqId").val()) {
        newFaqData.id = $("#faqId").val();
    }
    newFaqData.question = $("#faqQuestion").val();
    newFaqData.message = $("#faqMessage").val();

    var url = "../backend/rest.php?apiFunc=newFaq";
    $.post(url, JSON.stringify(newFaqData), function(res) {
        if (res.status == 200) {
            displayFaqs(res);
        }
    });
};