class Formatter {
    rupiah = (number, withRp = "Rp ") => {
        return (withRp ? withRp : "") + this.thousand(number);
    };

    thousand = (number) => {
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
}
