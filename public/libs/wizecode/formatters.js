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
