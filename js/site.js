$(document).ready(function () {
  // Selecting form and contact options
  const form = $("form");
  const contactByEmail = $("#kontaktweg-email");
  const contactByPhone = $("#kontaktweg-telefon");

  // Selecting form inputs and error messages
  const inputs = {
    name: $("#form-vorname"),
    surname: $("#form-nachname"),
    email: $("#form-emailadresse"),
    phone: $("#form-telefon"),
    address: $("#form-strasseundhausnummer"),
    city: $("#form-stadt"),
    zip: $("#form-plz"),
    message: $("#form-nachricht"),
  };

  const errors = {
    surnameEmpty: $("#error-nachname-emtpy"),
    surnameInvalid: $("#error-nachname-invalid"),
    emailEmpty: $("#error-email-empty"),
    emailInvalid: $("#error-email-invalid"),
    phoneInvalid: $("#error-telefon-invalid"),
    zip: $("#error-plz"),
    kontaktweg: $("#error-kontaktweg"),
    message: $("#error-nachricht"),
  };

  // Function to validate email format
  const isValidEmail = (email) => {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
  };

  // Function to reset error classes
  const resetInvalidClasses = () => {
    // Hiding error messages and removing 'invalid' class from inputs
    Object.values(errors).forEach((error) => error.addClass("hidden"));
    Object.values(inputs).forEach((input) => input.removeClass("invalid"));
  };

  const clearFields = () => {
    form.reset();
  };

  // Function to validate form inputs
  const validateInputs = () => {
    resetInvalidClasses();
    let isFormValid = true;

    // Validate surname input
    if (!inputs.surname.val().trim()) {
      inputs.surname.addClass("invalid");
      errors.surnameEmpty.removeClass("hidden");
      isFormValid = false;
    }

    // Validate email input
    if (contactByEmail.is(":checked") && !isValidEmail(inputs.email.val())) {
      inputs.email.addClass("invalid");
      errors.emailInvalid.removeClass("hidden");
      isFormValid = false;
    }

    // Validate phone input
    if (contactByPhone.is(":checked") && (inputs.phone.val().trim() === "" || isNaN(inputs.phone.val()))) {
      inputs.phone.addClass("invalid");
      errors.phoneInvalid.removeClass("hidden");
      isFormValid = false;
    }

    // Validate contact options
    if (!contactByEmail.is(":checked") && !contactByPhone.is(":checked")) {
      errors.kontaktweg.removeClass("hidden");
      isFormValid = false;
    }

    // Validate message input
    if (!inputs.message.val().trim()) {
      inputs.message.addClass("invalid");
      errors.message.removeClass("hidden");
      isFormValid = false;
    }

    return isFormValid;
  };

  // Function to collect form data
  const collectFormData = () => {
    const formData = {
      anrede: "none",
      firstname: inputs.name.val().trim(),
      lastname: inputs.surname.val().trim(),
      emailAddress: inputs.email.val().trim(),
      phone: inputs.phone.val().trim(),
      address: inputs.address.val().trim(),
      city: inputs.city.val().trim(),
      zip: inputs.zip.val().trim(),
      contactByEmail: contactByEmail.is(":checked") ? "y" : "n",
      contactByPhone: contactByPhone.is(":checked") ? "y" : "n",
      message: inputs.message.val().trim(),
    };

    return formData;
  };

  // Function to handle form submission
  const handleSubmit = (e) => {
    e.preventDefault();
    const isFormValid = validateInputs();

    if (isFormValid) {
      const formData = collectFormData();
      const url = "backend/rest.php?apiFunc=sendContact";

      // Send form data via AJAX POST request
      $.post(url, JSON.stringify(formData), function (res) {
        if (res.status === 200) {
          form[0].reset();
          form.remove();
          $("#thank-you").removeClass("hidden");
        } else {
          $("#error-message").removeClass("hidden");
          console.log(res);
        }
      });
    }
  };

  // Event listener for form submission
  form.on("submit", handleSubmit);

  // Event listeners for input changes
  $.each(inputs, function (_, input) {
    input.on("input", validateInputs);
  });

  // Event listeners for contact options
  $.each([contactByEmail, contactByPhone], function (_, input) {
    input.on("click", validateInputs);
  });

});