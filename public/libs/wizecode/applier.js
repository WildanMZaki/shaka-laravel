/**
 *
 * !!! Important Note: File ini adalah untuk memanggil dan inisialisasi element element select dengan select2 dan jQuery
 * --- Syarat penggunaan:
 *      Tambahkan class 'apply-select2' pada select element yang diinginkan
 *      Kemudian customisasi dengan attribut data pada element tersebut
 *
 *      Berikut opsi customisasi yang tersedia
 *      Placeholder: data-placeholder
 *      data: data-options
 *      hideSearch: data-hide-search default: undefined (tidak ditambahkan)
 */

$(document).ready(() => {
    $(".apply-select2").each(function () {
        const $select = $(this);
        const config = {};

        const allowClear = parseInt($select.data().allowClear);
        config.allowClear = allowClear == 1;

        const hideSearch = parseInt($select.data().hideSearch);
        config.minimumResultsForSearch = hideSearch == 1 ? -1 : 0;

        const options = $select.data().options;
        if (options) {
            config.data = options;
        }

        $select.select2(config);
    });

    // Input File and it preview:
    $(".wize-upload-image").on("change", function (e) {
        const targetImg = $(this).data("wzTarget");
        if (!targetImg) {
            alert("data-wz-target diperlukan");
        }
        // Get the selected file
        var file = e.target.files[0];

        if (file) {
            // Create a FileReader object to read the file
            var reader = new FileReader();

            // Set a callback function to execute when the file is read
            reader.onload = function (e) {
                // Set the source of the image to the data URL
                $(targetImg).attr("src", e.target.result);
            };

            // Read the file as a data URL
            reader.readAsDataURL(file);
        } else {
            // If no file is selected, clear the image source
            $(targetImg).attr("src", "");
        }
    });
});
