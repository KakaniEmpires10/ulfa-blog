window.api = async function (url, options = {}) {
  const {
    method = "GET",
    data = null,
    headers = {},
    beforeSend = () => {},
    onSuccess = () => {},
    onError = () => {},
    onFinish = () => {},
  } = options;

  try {
    beforeSend();

    const res = await fetch(url, {
      method,
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        ...headers,
      },
      body: data ? JSON.stringify(data) : null,
    });

    const result = await res.json();

    if (!res.ok) {
      throw result;
    }

    onSuccess(result);
    return result;
  } catch (err) {
    onError(err);
    throw err;
  } finally {
    onFinish();
  }
};

window.APP = window.APP || {};
window.APP.forms = window.APP.forms || {};
window.APP.forms.resolveDateTimeDefault = function (value) {
  if (typeof value === "string" && value.trim() !== "") {
    return value;
  }

  return new Date();
};

document.addEventListener("DOMContentLoaded", () => {
  const forms = document.querySelectorAll("form");
  const submittingForms = new WeakSet();

  const getLabel = (field) => {
    const label = field.closest(".field")?.querySelector("label");
    return label?.textContent?.trim() || field.getAttribute("placeholder") || "Kolom ini";
  };

  const getValidationMessage = (field) => {
    const label = getLabel(field);
    const validity = field.validity;

    if (validity.valueMissing) {
      return `${label} wajib diisi.`;
    }

    if (validity.typeMismatch && field.type === "email") {
      return `Format ${label.toLowerCase()} belum valid.`;
    }

    if (validity.tooShort) {
      return `${label} minimal ${field.minLength} karakter.`;
    }

    if (validity.tooLong) {
      return `${label} maksimal ${field.maxLength} karakter.`;
    }

    if (validity.patternMismatch) {
      return `${label} belum sesuai format yang dibutuhkan.`;
    }

    return "";
  };

  forms.forEach((form) => {
    form.setAttribute("novalidate", "novalidate");

    const fields = form.querySelectorAll("input, textarea, select");

    fields.forEach((field) => {
      field.addEventListener("invalid", (event) => {
        event.target.setCustomValidity(getValidationMessage(event.target));
      });

      field.addEventListener("input", (event) => {
        event.target.setCustomValidity("");
      });

      field.addEventListener("blur", (event) => {
        if (event.target.value !== "") {
          event.target.reportValidity();
        }
      });
    });

    form.addEventListener("submit", (event) => {
      const isValid = form.checkValidity();

      if (!isValid) {
        event.preventDefault();
        form.reportValidity();
        return;
      }

      if (submittingForms.has(form)) {
        event.preventDefault();
        return;
      }

      submittingForms.add(form);
    });
  });
});
