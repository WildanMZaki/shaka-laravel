const thousandsId = (number) => {
    const angkaString = number.toString();

    // Pisahkan angka menjadi bagian-bagian yang berdekatan dengan tanda titik
    const bagianAngka = angkaString.split(".");

    // Format bagian pertama (angka ribuan)
    const ribuanFormatted = bagianAngka[0].replace(
        /\B(?=(\d{3})+(?!\d))/g,
        "."
    );

    // Gabungkan kembali dengan bagian kedua (angka desimal), jika ada
    const hasilFormatted =
        bagianAngka.length > 1
            ? ribuanFormatted + "." + bagianAngka[1]
            : ribuanFormatted;

    return hasilFormatted;
};

const validInt = (stringInt) => {
    const fullInt = stringInt.replace(/[^0-9]/g, "");

    let validInt = parseInt(fullInt);

    if (isNaN(validInt)) {
        validInt = 0;
    }

    return validInt;
};

const ceilToHundreds = (value) => {
    value = Math.abs(value);
    let roundedValue = Math.ceil(value / 100) * 100;
    return roundedValue;
};

const rupiah = (number, withRp = "Rp ") => {
    return (withRp ? withRp : "") + thousandsId(number);
};

const mustDigit = (element) => {
    element.value = element.value.replace(/[^0-9]/g, "");
};

const mustBeAtLeast = (element, min, cbFn = null) => {
    // Replace non-digit characters with an empty string
    element.value = element.value.replace(/[^0-9]/g, "");

    const numericValue = parseInt(element.value);
    if (isNaN(numericValue) || numericValue < min) {
        element.value = min;
    }
    // element.value = numericValue;

    if (cbFn !== null) {
        cbFn(element.value);
    }
};

const mustInRupiahCurrency = (
    element,
    formatter = thousandsId,
    cbFn = null
) => {
    element.value = element.value.replace(/[^0-9]/g, "");

    const numericValue = parseInt(element.value);

    if (isNaN(numericValue)) {
        element.value = 0;
        return;
    }

    element.value = formatter(numericValue);

    if (cbFn !== null) {
        cbFn(element.value);
    }
};

class Wize {
    ajax = (options = {}) => {
        const {
            url = document.URL,
            data = {},
            method = "POST",
            headers = {},
            addon_success = null,
            successDefault = true,
            addon_error = null,
            inputSelector = null,
            modalSelector = null,
        } = options;

        if (!url) {
            alert("URL diperlukan");
            throw Error("URL ajax diperlukan");
        }

        headers["X-CSRF-TOKEN"] = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url: url,
            type: method,
            headers: headers,
            data: data,
            beforeSend: () => {
                this.clear_errors();
                this.show_loading();
            },
            success: (data) => {
                if (addon_success != null) {
                    addon_success(data);
                    if (!successDefault) {
                        return;
                    }
                }
                if (modalSelector) {
                    $(modalSelector).modal("hide");
                }
                this.show_success(data);
            },
            error: (err) => {
                if (addon_error != null) {
                    addon_error(data);
                    return;
                }
                this.error_occured(err, inputSelector);
            },
        });
    };

    show_loading = () => {
        Swal.fire({
            html: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><br>Mohon menunggu...',
            allowOutsideClick: false,
            buttonsStyling: false,
            showConfirmButton: false,
        });
    };

    show_success = (data) => {
        Swal.fire({
            icon: "success",
            text: data.message ?? "Berhasil",
            customClass: {
                confirmButton: "btn btn-success",
            },
            timer: 1000,
        });
    };

    activate_tooltips = () => {
        var tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    };

    clear_errors = () => {
        $(".is-invalid").removeClass("is-invalid");
    };

    // This is error occured for ajax request
    error_occured = (err, inputSelector = null) => {
        Swal.close();
        if (err.hasOwnProperty("responseJSON")) {
            if (
                err.responseJSON.hasOwnProperty("errors") &&
                inputSelector != null
            ) {
                const { errors } = err.responseJSON;
                for (const key in errors) {
                    const selector = inputSelector.replace("{key}", key);
                    if (Object.hasOwnProperty.call(errors, key)) {
                        const error = errors[key];
                        $(selector).addClass("is-invalid");
                        if ($(selector).next().hasClass("invalid-feedback")) {
                            $(selector).next().html(error[0]);
                        } else {
                            $(`#${key}-invalid-msg`).html(error[0]);
                        }
                    }
                }
            } else if (err.responseJSON.hasOwnProperty("message")) {
                Swal.fire({
                    icon: "error",
                    text: err.responseJSON.message,
                    showClass: {
                        popup: "animate__animated animate__shakeX",
                    },
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                });
            } else {
                Swal.fire({
                    icon: "error",
                    text: "Something went wrong!",
                    showClass: {
                        popup: "animate__animated animate__shakeX",
                    },
                    customClass: {
                        confirmButton: "btn btn-primary",
                    },
                });
            }
        } else {
            console.error(err);
            alert("Terjadi Error");
        }
    };
}
