document.addEventListener("DOMContentLoaded", () => {

    const form = document.querySelector("form");
    const input = document.querySelector("input[name='user_identity']");
    const sendOtpBtn = document.querySelector("button[type='submit']");

    if (!form || !input || !sendOtpBtn) return;

    form.addEventListener("submit", (e) => {

        // Trim input value
        const value = input.value.trim();

        // ---------- BASIC VALIDATION ----------
        if (!value) {
            e.preventDefault();
            alert("Please enter your email or mobile number");
            input.focus();
            return;
        }

        // ---------- BUTTON LOADING STATE ----------
        sendOtpBtn.disabled = true;
        sendOtpBtn.textContent = "Sending OTP...";

        /*
         If backend fails and reloads page,
         button state will automatically reset.
        */
    });

});
