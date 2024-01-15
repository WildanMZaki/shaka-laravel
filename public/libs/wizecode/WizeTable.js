class WizeTable {
    constructor() {
        this.deleteBtn = {
            custom: null,
            icon: "ti ti-trash",
            color: "btn-label-danger",
            text: "Hapus",
            action: this.deleteAction,
        };
    }

    init = ({
        selector = "#wize-table",
        title = "Data",
        btns = false,
        url = document.URL,
        url_delete = false,
        message_delete = "Anda Yakin?",
        columns = [],
        defaultButton = {
            custom: null,
            icon: "ti ti-plus",
            color: "btn-primary",
            text: "Tambah",
            action: () => {},
        },
        addon_delete = null,
        defaultDelete = true,
    }) => {
        // Yang jelas di bawah ini untuk mencegah terjadinya auto sort ketika tabel diinisialisasi
        // $(selector).data("order", "");
        this.url = url;
        this.columns = columns;
        this.message_delete = message_delete;
        this.url_delete = url_delete;
        this.withDelete = url_delete !== false;
        this.defaultButton = defaultButton;
        this.btns = btns;
        this.addon_delete = addon_delete;
        this.defaultDelete = defaultDelete;

        if (this.withDelete) {
            if (columns.length) this.columns.unshift("checkbox");
            this.wizeTable = $(selector).DataTable({
                buttons: this.defineButtons(),
                dom: '<"card-header d-flex flex-column flex-md-row flex-lg-row justify-content-between align-items-center p-0 mt-2 mb-4 "<"head-label"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                columns: this.defineColumns(),
                columnDefs: [
                    {
                        targets: 0,
                        orderable: false,
                        responsivePriority: 3,
                        render: function (data, type, full, meta) {
                            return `
                                <div class="form-check">
                                    <input class="form-check-input dt-checkboxes is-checkbox-delete" type="checkbox" data-id="${full.id}"" />
                                    <label class="form-check-label" for="checkbox${full.id}"></label>
                                </div>
                            `;
                        },
                        checkboxes: {
                            selectAllRender:
                                '<div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll" /><label class="form-check-label" for="checkboxSelectAll"></label></div>',
                        },
                    },
                ],
                order: [],
            });
            this.wizeTable.column(0).header().innerHTML = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="all" id="select-all" title="Pilih Semua">                                        
                </div>
            `;
            $(".is-checkbox-delete").change(function () {
                const allChecked =
                    $(".is-checkbox-delete:checked").length ===
                    $(".is-checkbox-delete").length;
                $("#select-all").prop("checked", allChecked);
            });

            $("#select-all").change(function (e) {
                $(".is-checkbox-delete").prop(
                    "checked",
                    $(this).is(":checked")
                );
                $(this).attr(
                    "title",
                    $(this).is(":checked") ? "Batal Pilih Semua" : "Pilih Semua"
                );
            });
        } else {
            this.wizeTable = $(selector).DataTable({});
        }
        $("div.head-label").html(
            '<h4 class="card-title mb-0">' + title + "</h4>"
        );
    };

    defineColumns = () => {
        const $cols = [];
        let cols;
        if (this.columns[0] == "checkbox") {
            $cols[0] = {
                data: "id",
            };
            cols = this.columns.slice(1);
        } else {
            cols = this.columns;
        }
        cols.forEach((col) => {
            $cols.push({
                data: col,
            });
        });
        return $cols;
    };

    formatButton = (config) => {
        if (config.custom) {
            const { custom } = config;
            return {
                text: custom.text,
                className: custom.className,
                action: custom.action ? custom.action : () => {},
            };
        } else {
            const { icon, color, text } = config;
            const otherClass = config.hasOwnProperty("otherClass")
                ? config.otherClass
                : "";
            const action = config.hasOwnProperty("action")
                ? config.action
                : () => {};
            return {
                text: `<i class="${icon} me-sm-1"></i> <span class="d-none d-sm-inline-block">${text}</span>`,
                className: `is-button-add btn ${color} me-2 ${otherClass}`,
                action: action,
            };
        }
    };

    defineButtons = () => {
        // Jika false, atau jika default
        if (!this.btns) {
            this.btns = [];
            if (this.withDelete) {
                this.btns.push(this.formatButton(this.deleteBtn));
            }
            this.btns.push(this.formatButton(this.defaultButton));
        }
        return this.btns;
    };

    deleteAction = () => {
        var id = [];
        $(".is-checkbox-delete:checked").each(function (i) {
            id[i] = $(this).data("id");
        });
        if (id.length === 0) {
            Swal.fire({
                text: "Tolong pilih setidaknya 1 data",
                icon: "warning",
                customClass: {
                    confirmButton: "btn btn-primary",
                },
                showClass: {
                    popup: "animate__animated animate__shakeX",
                },
                buttonsStyling: false,
            });
        } else {
            Swal.fire({
                text: this.message_delete ? this.message_delete : "Anda Yakin?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-outline-danger ms-1",
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        url: this.url_delete,
                        method: "DELETE",
                        data: {
                            id: id,
                        },
                        beforeSend: function () {
                            Swal.fire({
                                html: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><br>Mohon menunggu...',
                                allowOutsideClick: false,
                                buttonsStyling: false,
                                showConfirmButton: false,
                            });
                        },
                        success: (data) => {
                            if (this.addon_delete != null) {
                                this.addon_delete(data);
                                if (!this.defaultDelete) {
                                    return;
                                }
                            }
                            this.reload();
                            Swal.fire({
                                icon: "success",
                                text: data.message ?? "Data berhasil dihapus",
                                customClass: {
                                    confirmButton: "btn btn-success",
                                },
                                timer: 1500,
                            });
                            $(".is-checkbox-delete").trigger("click");
                        },
                        error: function (data) {
                            if (data.responseJSON.message) {
                                Swal.fire({
                                    icon: "error",
                                    text: data.responseJSON.message,
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
                        },
                    });
                }
            });
        }
    };

    reload = () => {
        $.ajax({
            url: this.url,
            type: "GET",
            dataType: "json",
            success: (rows) => {
                $('[data-bs-toggle="tooltip"]').tooltip("hide");
                $('[data-bs-toggle="tooltip"]').tooltip("dispose");

                this.wizeTable.clear().draw();
                this.wizeTable.rows.add(rows).draw();

                $(".is-checkbox-delete").change(function () {
                    const allChecked =
                        $(".is-checkbox-delete:checked").length ===
                        $(".is-checkbox-delete").length;
                    $("#select-all").prop("checked", allChecked);
                });

                this.activate_tooltips();
            },
            error: (error) => {
                console.error("Error fetching data:", error);
            },
        });
    };

    extractData = (data) => {
        if (typeof data != "object") {
            throw Error("Data yang diekstrak harus dalam bentuk array");
        }
        const $rows = [];
        data.forEach((row, i) => {
            let rowItem = [];
            this.columns.forEach((col) => {
                rowItem.push(row[col]);
            });
            $rows.push(rowItem);
        });
        return $rows;
    };

    activate_tooltips = () => {
        var tooltipTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="tooltip"]')
        );
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    };
}
