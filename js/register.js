
/* ================= UPLOAD HANDLING ================= */

document.querySelectorAll(".upload-box input").forEach((input) => {
  input.addEventListener("change", function () {
    const file = this.files[0];
    const box = this.closest(".upload-box");

    if (!file) return;

    // ✅ allow images + pdf
    if (
      !file.type.startsWith("image/") &&
      file.type !== "application/pdf"
    ) {
      alert("Only image files (PNG, JPG, JPEG) or PDF are allowed.");
      this.value = "";
      return;
    }

    box.classList.remove("error", "shake");
    box.classList.add("uploaded");   // 🔴 THIS IS REQUIRED

    box.querySelector(".upload-icon").textContent = "✔";
    box.querySelector(".upload-text").textContent = file.name;
  });
});



document.querySelectorAll(".upload-box").forEach((box) => {
  box.addEventListener("click", () => {
    const input = box.querySelector("input[type='file']");
    if (input) input.click();
  });
});

// EyE toggle
document.querySelectorAll(".toggle-eye").forEach((eye) => {
  eye.addEventListener("click", () => {
    const input = eye.previousElementSibling;
    const icon = eye.querySelector("i");

    if (input.type === "password") {
      input.type = "text";
      icon.classList.remove("fa-eye");
      icon.classList.add("fa-eye-slash");
    } else {
      input.type = "password";
      icon.classList.remove("fa-eye-slash");
      icon.classList.add("fa-eye");
    }
  });
});


/* ================= FORM VALIDATION ================= */

// SHAKE ANIMATION FUNCTION (ADDED BACK)
function shakeField(element) {
  element.classList.remove("shake");
  void element.offsetHeight; // force reflow
  element.classList.add("shake");
}

function setError(field, withShake = false) { // Added withShake parameter
  if (field) {
    field.classList.add("error");

    if (withShake) {
      shakeField(field);
    }
  }
}

function clearError(field) {
  if (field) {
    field.classList.remove("error", "shake"); // Added shake
  }
}

function validateField(input) {
  const field = input.closest(".field");
  const uploadBox = input.closest(".upload-box");
  const value = input.value.trim();
  let valid = true;

  // Determine which container to use
  const container = field || uploadBox;
  if (!container) return true;

  // Mobile number (10 digits only)
  if (input.placeholder && input.placeholder.toLowerCase().includes("mobile")) {
    valid = /^\d{10}$/.test(value);
  }

  // Email
  else if (input.type === "email") {
    valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
  }

  // GST number (15 characters)
  else if (input.placeholder && input.placeholder.toLowerCase().includes("gst")) {
    valid = value.length === 15 && value !== "";
  }

  // DL numbers (exactly 30 characters)
  else if (input.placeholder && 
           (input.placeholder.toLowerCase().includes("license") ||
           input.placeholder.includes("DL1") ||
           input.placeholder.includes("DL2"))) {
    valid = value.length === 30;
  }

  // Select dropdown validation
  else if (input.tagName === "SELECT") {
    valid = value !== "Select type" && value !== "";
  }

  // Confirm password
  else if (input.id === "confirmPassword") {
    const pass = document.getElementById("password").value;
    valid = value !== "" && value === pass;
  }

  // Password validation (at least 6 characters)
  else if (input.id === "password") {
    valid = value.length >= 6;
  }

  // Textarea validation (Address field)
  else if (input.tagName === "TEXTAREA") {
    valid = value.length >= 10; // Minimum address length
  }

  // Generic text field validation (Firm Name, Owner Name, etc.)
  else if (input.type === "text" || input.type === "tel" || input.type === "number") {
    valid = value.length >= 2; // At least 2 characters
  }

  // Generic empty check for other inputs
  else {
    valid = value !== "";
  }

  if (valid) {
    clearError(container);
  } else {
    setError(container);
  }
  return valid;
}

// Handle upload box file validation separately
function validateUploadBox(box) {
  const fileInput = box.querySelector("input[type='file']");
  const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
  
  if (!hasFile) {
    box.classList.add("error");
    return false;
  } else {
    box.classList.remove("error");
    return true;
  }
}

// Add event listeners to ALL form inputs for real-time validation
document.querySelectorAll(".field input, .field select, .field textarea, .upload-box input[type='file']").forEach((input) => {
  if (input.type === 'file') {
    input.addEventListener("change", () => {
      const box = input.closest(".upload-box");
      if (box) validateUploadBox(box);
    });
  } else {
    input.addEventListener("input", () => validateField(input));
    input.addEventListener("blur", () => validateField(input));
  }
});

document.querySelector(".register-btn").addEventListener("click", function (e) {
  e.preventDefault();

  let isValid = true;

  // Clear all errors first
  document.querySelectorAll(".field, .upload-box").forEach(el => {
    el.classList.remove("error", "shake"); // Added shake
  });

  // Validate ALL regular form fields
  document.querySelectorAll(".field input, .field select, .field textarea").forEach((input) => {
    if (input.type === 'file') return; // Skip file inputs
    
    if (!validateField(input)) {
      setError(input.closest(".field"), true); // Added true for shake
      isValid = false;
    }
  });

  // Validate ALL upload boxes
  document.querySelectorAll(".upload-box").forEach((box) => {
    const fileInput = box.querySelector("input[type='file']");
    const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
    
    if (!hasFile) {
      setError(box, true); // Added true for shake
      isValid = false;
    }
  });

  if (isValid) {
    // alert("Account Registered Successfully!");
    // form.submit();
    document.querySelector("form").submit();
    // Uncomment to submit form
    // document.querySelector("form").submit();
  } else {
    // Find first error and scroll to it
    const firstError = document.querySelector(".field.error, .upload-box.error");
    if (firstError) {
      firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }
});

document.querySelectorAll(".upload-remove").forEach((btn) => {
  btn.addEventListener("click", function (e) {
    e.stopPropagation();

    const box = this.closest(".upload-box");
    const input = box.querySelector("input[type='file']");

    input.value = "";
    box.classList.remove("uploaded", "error", "shake");
    box.querySelector(".upload-icon").textContent = "⬆";
    box.querySelector(".upload-text").textContent = "Upload file";
  });
});
    
