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

const anredeFrau = document.getElementById("anrede-frau");
const anredeHerr = document.getElementById("anrede-herr");

const nameInput = document.getElementById("form-vorname");

const surnameInput = document.getElementById("form-nachname");

const emailInput = document.getElementById("form-emailadresse");

const phoneInput = document.getElementById("form-telefon");

const addressInput = document.getElementById("form-strasseundhausnummer");

const cityInput = document.getElementById("form-stadt");

const zipInput = document.getElementById("form-plz");

const contactByPhone = document.getElementById("kontaktweg-telefon");
const contactByEmail = document.getElementById("kontaktweg-email");

const messageInput = document.getElementById("form-nachricht");

//Define where to find the different Error-Message divs

const anredeError = document.getElementById("error-anrede");

const nameError = document.getElementById("error-vorname");

const surnameErrorEmpty = document.getElementById("error-nachname-emtpy");
const nachnameErrorInvalid = document.getElementById("error-nachname-invalid");

const emailErrorEmpty = document.getElementById("error-email-empty");
const emailErrorInvalid = document.getElementById("error-email-invalid");

const phoneErrorInvalid = document.getElementById("error-telefon-invalid");

const zipError = document.getElementById("error-plz");

const kontaktwegError = document.getElementById("error-kontaktweg");

const messageError = document.getElementById("error-nachricht");

//More defining... This time when the from data is valid and when not...

const resetInvalidClasses = () => {
  surnameInput.classList.remove("invalid");
  surnameErrorEmpty.classList.add("hidden");
  messageInput.classList.remove("invalid");
  messageError.classList.add("hidden");
  emailInput.classList.remove("invalid");
  emailErrorEmpty.classList.add("hidden");
  emailErrorInvalid.classList.add("hidden");
  phoneInput.classList.remove("invalid");
  phoneErrorInvalid.classList.add("hidden");
  kontaktwegError.classList.add("hidden");
  anredeError.classList.add("hidden");
};

const clearFields = () => {
  form.reset();
};

const validateEmailInput = () => {
  if (contactByEmail.checked) {
    if (!isValidEmail(emailInput.value)) {
      emailInput.classList.add("invalid");
      emailErrorInvalid.classList.remove("hidden");
      isFormValid = false;
    }
  }
};

const validateAnredeInput = () => {
  if (!nameInput.value) {
    if (!anredeFrau.checked && !anredeHerr.checked) {
      anredeError.classList.remove("hidden");
    }
  }
};

const checkAnredeInput = () => {
  if (anredeFrau.checked) {
    anredeString = "Frau";
  }
  if (anredeHerr.checked) {
    anredeString = "Herr";
  }
};

const validatePhoneInput = () => {
  if (contactByPhone.checked) {
    if (!phoneInput.value) {
      phoneInput.classList.add("invalid");
      phoneErrorInvalid.classList.remove("hidden");
      isFormValid = false;
    } else {
      if (isNaN(phoneInput.value)) {
        phoneInput.classList.add("invalid");
        phoneErrorInvalid.classList.remove("hidden");
        isFormValid = false;
      }
    }
  }
};

const validateSurnameInput = () => {
  if (!surnameInput.value) {
    surnameInput.classList.add("invalid");
    surnameErrorEmpty.classList.remove("hidden");
    isFormValid = false;
  }
};

const validateKontaktwegInput = () => {
  if (!contactByEmail.checked & !contactByPhone.checked) {
    kontaktwegError.classList.remove("hidden");
    isFormValid = false;
  }
};

const checkKontaktwegInput = () => {
  if (contactByEmail.checked) {
    contactEmail = true;
    contactEmailString = "y";
  }
  if (contactByPhone.checked) {
    contactPhone = true;
    contactPhoneString = "y";
  }
};

const validateMessageInput = () => {
  if (!messageInput.value) {
    messageInput.classList.add("invalid");
    messageError.classList.remove("hidden");
    isFormValid = false;
  }
};

const validateInputs = () => {
  resetInvalidClasses();
  isFormValid = true;
  validateAnredeInput();
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
    checkAnredeInput();
    var formData = {};
    if (anredeString != "") {
      formData.anrede = anredeString;
    }
    if (nameInput.value != "") {
      formData.firstname = nameInput.value;
    }
    formData.lastname = surnameInput.value;
    if (emailInput.value != "") {
      formData.emailAddress = emailInput.value;
    }
    if (phoneInput.value != "") {
      formData.phone = phoneInput.value;
    }
    if (addressInput.value != "") {
      formData.address = addressInput.value;
    }
    if (cityInput.value != "") {
      formData.city = cityInput.value;
    }
    if (zipInput.value != "") {
      formData.zip = zipInput.value;
    }
    formData.contactByEmail = contactEmailString;
    formData.contactByPhone = contactPhoneString;
    formData.message = messageInput.value;

    jQuery.get("backend/rest.php?apiFunc=getToken", function (res) {
      response = JSON.parse(res);
      if (response.status == 200) {
        var apiToken = response.token;
        var url =
          "backend/rest.php?apiToken=" + apiToken + "&apiFunc=sendContact";
        jQuery.post(url, JSON.stringify(formData), function (res) {
          response = JSON.parse(res);
          if (response.status == 200) {
            form.reset();
            form.remove();
            document.getElementById("thank-you").classList.remove("hidden");
          } else {
            // error response
            document.getElementById("error-message").classList.remove("hidden");
            console.log(response);
          }
        });
      }
    });
  }
});

//Making it so we don't have to press submit every time to see if the data is valid

nameInput.addEventListener("input", () => {
  validateInputs();
});

surnameInput.addEventListener("input", () => {
  validateInputs();
});

messageInput.addEventListener("input", () => {
  validateInputs();
});

emailInput.addEventListener("input", () => {
  validateInputs();
});

phoneInput.addEventListener("input", () => {
  validateInputs();
});

contactByEmail.addEventListener("click", () => {
  validateInputs();
});

contactByPhone.addEventListener("click", () => {
  validateInputs();
});

anredeFrau.addEventListener("click", () => {
  validateInputs();
});

anredeHerr.addEventListener("click", () => {
  validateInputs();
});
