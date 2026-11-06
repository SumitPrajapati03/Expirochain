# 🤝 Contributing to EXPIROCHAIN

Thank you for your interest in contributing to **EXPIROCHAIN**! We welcome contributions from developers, healthcare technologists, and open-source enthusiasts.

---

## 📜 Code of Conduct

Please treat all contributors and community members with mutual respect, professional courtesy, and constructive communication.

---

## 🛠️ How to Contribute

### 1. Reporting Bugs
- Check the [Issues](../../issues) tab to see if the bug has already been reported.
- If not, open a new issue describing:
  - Steps to reproduce the bug.
  - Expected vs. actual behavior.
  - Screenshots or error logs (ensure **no credentials or private data** are visible).
  - Environment details (PHP version, MySQL version, browser).

### 2. Suggesting Features
- Open an issue with the label `enhancement`.
- Describe the feature, its motivation in healthcare waste rescue/compliance, and suggested implementation steps.

### 3. Submitting Pull Requests (PR)

1. **Fork the Repository**: Click "Fork" on GitHub.
2. **Create a Feature Branch**:
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. **Keep Credentials Protected**:
   - **Never commit `config.php` or any passwords / API keys.**
   - Use `config.example.php` for template updates.
   - Never commit files in `uploads/` or `temp_uploads/`.
4. **Follow Code Conventions**:
   - Maintain consistent PHP, CSS, and JavaScript formatting.
   - Use prepared statements and parameterized inputs for SQL queries.
   - Sanitize all user inputs before outputting to HTML (`htmlspecialchars`).
5. **Test Your Changes**:
   - Verify that your code runs cleanly on local XAMPP without notices or warnings.
   - Test authentication, role checks, and database operations.
6. **Commit with Clear Messages**:
   ```bash
   git commit -m "feat: add batch export filtering by expiry range"
   ```
7. **Push to Your Fork and Open a PR**:
   ```bash
   git push origin feature/your-feature-name
   ```

---

## 🔒 Security Vulnerabilities

If you discover a security vulnerability (such as exposed credentials, authentication bypass, or injection flaws), please **do not open a public GitHub issue**. Instead, report it privately to the maintainers or via GitHub Security Advisories.

---

Thank you for helping make pharmaceutical distribution safer, smarter, and waste-free! 🌱

