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
