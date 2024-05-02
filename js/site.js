$(document).ready(function() {
const form = document.querySelector("form");

let isFormValid = false;

let contactEmail = false;

let contactPhone = false;

let contactEmailString = "n";

let contactPhoneString = "n";

let anredeString = "none";

const isValidEmail = (email) => {
  const re =
    /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
};

//Define where to find the different Inputs

const nameInput = $("#form-vorname");

const surnameInput = $("#form-nachname");

const emailInput = $("#form-emailadresse");

const phoneInput = $("#form-telefon");

const addressInput = $("#form-strasseundhausnummer");

const cityInput = $("#form-stadt");

const zipInput = $("#form-plz");

const contactByPhone = $("#kontaktweg-telefon");
const contactByEmail = $("#kontaktweg-email");

const messageInput = $("#form-nachricht");

//Define where to find the different Error-Message divs

const nameError = $("#error-vorname");

const surnameErrorEmpty = $("#error-nachname-emtpy");
const nachnameErrorInvalid = $("#error-nachname-invalid");

const emailErrorEmpty = $("#error-email-empty");
const emailErrorInvalid = $("#error-email-invalid");

const phoneErrorInvalid = $("#error-telefon-invalid");

const zipError = $("#error-plz");

const kontaktwegError = $("#error-kontaktweg");

const messageError = $("#error-nachricht");

//More defining... This time when the from data is valid and when not...

const resetInvalidClasses = () => {
  $(surnameInput).removeClass("invalid");
  $(surnameErrorEmpty).addClass("hidden");
  $(messageInput).removeClass("invalid");
  $(messageError).addClass("hidden");
  $(emailInput).removeClass("invalid");
  $(emailErrorEmpty).addClass("hidden");
  $(emailErrorInvalid).addClass("hidden");
  $(phoneInput).removeClass("invalid");
  $(phoneErrorInvalid).addClass("hidden");
  $(kontaktwegError).addClass("hidden");
};

const clearFields = () => {
  form.reset();
};

const validateEmailInput = () => {
  if ($(contactByEmail).is(":checked")) {
    if (isValidEmail($(emailInput).val()) == false) {
      $(emailInput).addClass("invalid");
      $(emailErrorInvalid).removeClass("hidden");
      isFormValid = false;
    }
  }
};

const validatePhoneInput = () => {
  if ($(contactByPhone).is(":checked")) {
    if ($(phoneInput).val() == "") {
      $(phoneInput).addClass("invalid");
      $(phoneErrorInvalid).removeClass("hidden");
      isFormValid = false;
    } else {
      if (isNaN($(phoneInput).val())) {
        $(phoneInput).addClass("invalid");
        $(phoneErrorInvalid).removeClass("hidden");
        isFormValid = false;
      }
    }
  }
};

const validateSurnameInput = () => {
  if (!$(surnameInput).val()) {
    $(surnameInput).addClass("invalid");
    $(surnameErrorEmpty).removeClass("hidden");
    isFormValid = false;
  }
};

const validateKontaktwegInput = () => {
  if (!$(contactByEmail).is(":checked") & !$(contactByPhone).is(":checked")) {
    $(kontaktwegError).removeClass("hidden");
    isFormValid = false;
  }
};

const checkKontaktwegInput = () => {
  if ($(contactByEmail).is(":checked")) {
    contactEmail = true;
    contactEmailString = "y";
  }
  if ($(contactByPhone).is(":checked")) {
    contactPhone = true;
    contactPhoneString = "y";
  }
};

const validateMessageInput = () => {
  if (!$(messageInput).val()) {
    messageInput.addClass("invalid");
    messageError.removeClass("hidden");
    isFormValid = false;
  }
};

const validateInputs = () => {
  resetInvalidClasses();
  isFormValid = true;
  validateSurnameInput();
  validateMessageInput();
  validateKontaktwegInput();
  validateEmailInput();
  validatePhoneInput();
};

//Finally we can do stuff with the data!

form.addEventListener("submit", (e) => {
  e.preventDefault();
  validateInputs();
  if (isFormValid) {
    checkKontaktwegInput();
    var formData = {};
    if (anredeString != "") {
      formData.anrede = anredeString;
    }
    if ($(nameInput).val() != "") {
      formData.firstname = $(nameInput).val();
    }
    formData.lastname = $(surnameInput).val();
    if ($(emailInput).val() != "") {
      formData.emailAddress = $(emailInput).val();
    }
    if ($(phoneInput).val() != "") {
      formData.phone = $(phoneInput).val();
    }
    if ($(addressInput).val() != "") {
      formData.address = $(addressInput).val();
    }
    if ($(cityInput).val() != "") {
      formData.city = $(cityInput).val();
    }
    if ($(zipInput).val() != "") {
      formData.zip = $(zipInput).val();
    }
    formData.contactByEmail = contactEmailString;
    formData.contactByPhone = contactPhoneString;
    formData.message = $(messageInput).val();

    var url = "backend/rest.php?apiFunc=sendContact";
    jQuery.post(url, JSON.stringify(formData), function (res) {
      response = res;
      if (response.status == 200) {
        form.reset();
        form.remove();
        $("#thank-you").removeClass("hidden");
      } else {
        // error response
        $("#error-message").removeClass("hidden");
        console.log(response);
      }
    });
  }
});

//Making it so we don't have to press submit every time to see if the data is valid

$(nameInput).on("input", () => {
  validateInputs();
});

$(surnameInput).on("input", () => {
  validateInputs();
});

$(messageInput).on("input", () => {
  validateInputs();
});

$(emailInput).on("input", () => {
  validateInputs();
});

$(phoneInput).on("input", () => {
  validateInputs();
});

// Event listeners for contact options
$.each([contactByEmail, contactByPhone], function(_, input) {
  input.on("click", validateInputs);
});

});