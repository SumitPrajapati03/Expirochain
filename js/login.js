
// Eye toggle (same as register)
document.querySelectorAll(".toggle-eye").forEach((eye) => {
  eye.addEventListener("click", () => {
    const input = eye.previousElementSibling;
    const icon = eye.querySelector("i");

    if (input.type === "password") {
      input.type = "text";
      icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
      input.type = "password";
      icon.classList.replace("fa-eye-slash", "fa-eye");
    }
  });
});

function shakeField(element) {
  element.classList.remove("shake");
  void element.offsetHeight; // force reflow
  element.classList.add("shake");
}


function validateLoginField(input) {
  const field = input.closest(".field");
  const value = input.value.trim();
  let valid = true;

  // Mobile number (10 digits)
  if (input.placeholder.toLowerCase().includes("mobile")) {
    valid = /^\d{10}$/.test(value);
  }

  // Password (required only, no length limit)
  else if (input.type === "password") {
    valid = value !== "";
  }

  // Generic required
  else {
    valid = value !== "";
  }

  if (valid) {
    field.classList.remove("error");
  } else {
    field.classList.add("error"); // ❌ NO SHAKE HERE
  }

  return valid;
}

document
  .querySelectorAll(".field input")
  .forEach((input) => {
    input.addEventListener("input", () => validateLoginField(input));
    input.addEventListener("blur", () => validateLoginField(input));
  });


// Basic validation
document.querySelector(".register-btn").addEventListener("click", (e) => {
  e.preventDefault();

  let isValid = true;

  document.querySelectorAll(".field input").forEach((input) => {
    const field = input.closest(".field");

    if (!validateLoginField(input)) {
      field.classList.add("error");
      shakeField(field);
   // ✅ SHAKE ONLY ON SUBMIT
      isValid = false;
    }
  });

  if (isValid) {
    // alert("Login successful");
    document.querySelector("form").submit();
  }
});
