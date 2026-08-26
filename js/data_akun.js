document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const filterContainer = document.getElementById('accountPlatformFilter');
    const tableBody = document.getElementById('accountTableBody');
    const searchInput = document.getElementById('accountSearch');
    const countText = document.getElementById('accountTableCount');

    if (!filterContainer || !tableBody) {
        console.error('Data Akun: elemen filter/table tidak ditemukan.');
        return;
    }

    const buttons = Array.from(
        filterContainer.querySelectorAll('[data-account-filter]')
    );

    function normalize(value) {
        return String(value || '').trim().toLowerCase();
    }

    function getRows() {
        return Array.from(
            tableBody.querySelectorAll('tr.account-row')
        );
    }

    let currentFilter = 'all';

    /* =========================================================
       FILTER PLATFORM
    ========================================================= */

    function applyFilter(filter) {
        currentFilter = normalize(filter) || 'all';

        const keyword = searchInput
            ? normalize(searchInput.value)
            : '';

        let visible = 0;

        /* Tombol aktif */
        buttons.forEach(function (button) {
            button.classList.toggle(
                'active',
                normalize(
                    button.getAttribute('data-account-filter')
                ) === currentFilter
            );
        });

        /* Filter baris */
        getRows().forEach(function (row) {
            const platform = normalize(
                row.getAttribute('data-platform')
            );

            const search = normalize(
                row.getAttribute('data-search') ||
                row.textContent
            );

            const platformMatch =
                currentFilter === 'all' ||
                platform === currentFilter;

            const searchMatch =
                !keyword ||
                search.includes(keyword);

            const show =
                platformMatch &&
                searchMatch;

            row.hidden = !show;
            row.style.display = show ? '' : 'none';

            if (show) {
                visible++;
            }
        });

        /* Update jumlah */
        if (countText) {
            const labels = {
                all: 'akun media sosial',
                instagram: 'akun Instagram',
                facebook: 'akun Facebook',
                tiktok: 'akun TikTok',
                youtube: 'akun YouTube'
            };

            countText.textContent =
                visible +
                ' ' +
                (labels[currentFilter] || 'akun media sosial') +
                ' tersimpan di database.';
        }
    }

    /* =========================================================
       EVENT FILTER
    ========================================================= */

    filterContainer.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '[data-account-filter]'
                );

            if (
                !button ||
                !filterContainer.contains(button)
            ) {
                return;
            }

            event.preventDefault();

            applyFilter(
                button.getAttribute(
                    'data-account-filter'
                )
            );
        }
    );

    /* =========================================================
       SEARCH
    ========================================================= */

    if (searchInput) {
        searchInput.addEventListener(
            'input',
            function () {
                applyFilter(currentFilter);
            }
        );
    }

    /* =========================================================
       MODAL
    ========================================================= */

    const modal =
        document.getElementById('modalAkun');

    const form =
        document.getElementById('accountForm');

    const idInput =
        document.getElementById('accountId');

    const platformInput =
        document.getElementById('accountPlatform');

    const nameInput =
        document.getElementById('accountName');

    const title =
        document.getElementById('accountModalTitle');

    const message =
        document.getElementById('accountMessage');


    function openModal(mode, data) {

        if (!modal) {
            return;
        }

        if (title) {
            title.textContent =
                mode === 'edit'
                    ? 'Edit Akun Media Sosial'
                    : 'Tambah Akun Media Sosial';
        }

        if (idInput) {
            idInput.value = data.id || '';
        }

        if (platformInput) {
            platformInput.value =
                normalize(data.platform)
                    .toUpperCase();
        }

        if (nameInput) {
            nameInput.value =
                data.name || '';
        }

        if (message) {
            message.textContent = '';
            message.className = 'message';
        }

        modal.classList.add('show');
    }


    function closeModal() {

        if (!modal) {
            return;
        }

        modal.classList.remove('show');

        if (form) {
            form.reset();
        }

        if (idInput) {
            idInput.value = '';
        }

        if (message) {
            message.textContent = '';
            message.className = 'message';
        }
    }


    /* =========================================================
       TAMBAH AKUN
    ========================================================= */

    const addButton =
        document.getElementById('btnTambahAkun');

    if (addButton) {
        addButton.addEventListener(
            'click',
            function () {
                openModal('add', {});
            }
        );
    }


    /* =========================================================
       TUTUP MODAL
    ========================================================= */

    const closeButton =
        document.getElementById(
            'closeAccountModal'
        );

    const cancelButton =
        document.getElementById(
            'btnCancelAccount'
        );

    if (closeButton) {
        closeButton.addEventListener(
            'click',
            closeModal
        );
    }

    if (cancelButton) {
        cancelButton.addEventListener(
            'click',
            closeModal
        );
    }

    if (modal) {
        modal.addEventListener(
            'click',
            function (event) {

                if (event.target === modal) {
                    closeModal();
                }

            }
        );
    }


    /* =========================================================
       ESC
    ========================================================= */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                modal &&
                modal.classList.contains('show')
            ) {
                closeModal();
            }

        }
    );


    /* =========================================================
       EDIT AKUN
    ========================================================= */

    document.addEventListener(
        'click',
        function (event) {

            const edit =
                event.target.closest(
                    '.btn-edit-account'
                );

            if (edit) {

                event.preventDefault();

                openModal(
                    'edit',
                    {
                        id:
                            edit.getAttribute(
                                'data-id'
                            ),

                        platform:
                            edit.getAttribute(
                                'data-platform'
                            ),

                        name:
                            edit.getAttribute(
                                'data-nama'
                            )
                    }
                );

                return;
            }


            /* =================================================
               DELETE AKUN
            ================================================= */

            const del =
                event.target.closest(
                    '.btn-delete-account'
                );

            if (!del) {
                return;
            }

            event.preventDefault();

            const id =
                del.getAttribute('data-id');

            if (!id) {
                return;
            }

            const yakin =
                confirm(
                    'Apakah kamu yakin ingin menghapus akun ini?'
                );

            if (!yakin) {
                return;
            }

            const fd =
                new FormData();

            fd.append(
                'action',
                'delete_account'
            );

            fd.append(
                'id',
                id
            );

            fetch(
                window.location.href,
                {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-Requested-With':
                            'XMLHttpRequest'
                    }
                }
            )
            .then(function (response) {
                return response.json();
            })
            .then(function (result) {

                if (!result.success) {
                    throw new Error(
                        result.message ||
                        'Gagal menghapus akun.'
                    );
                }

                window.location.reload();

            })
            .catch(function (error) {

                alert(
                    error.message ||
                    'Terjadi kesalahan.'
                );

            });
        }
    );


    /* =========================================================
       SIMPAN / EDIT AKUN
    ========================================================= */

    if (form) {

        form.addEventListener(
            'submit',
            function (event) {

                event.preventDefault();


                const platform =
                    platformInput
                        ? platformInput.value
                            .trim()
                            .toUpperCase()
                        : '';


                const name =
                    nameInput
                        ? nameInput.value.trim()
                        : '';


                const allowed = [
                    'INSTAGRAM',
                    'FACEBOOK',
                    'TIKTOK',
                    'YOUTUBE'
                ];


                /* Validasi platform */

                if (!allowed.includes(platform)) {

                    if (message) {

                        message.textContent =
                            'Platform tidak valid.';

                        message.className =
                            'message show error';

                    }

                    return;
                }


                /* Validasi nama */

                if (!name) {

                    if (message) {

                        message.textContent =
                            'Nama akun wajib diisi.';

                        message.className =
                            'message show error';

                    }

                    return;
                }


                const fd =
                    new FormData(form);


                fd.set(
                    'action',
                    'save_account'
                );

                fd.set(
                    'platform',
                    platform
                );

                fd.set(
                    'nama_akun',
                    name
                );


                fetch(
                    window.location.href,
                    {
                        method: 'POST',
                        body: fd,
                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest'
                        }
                    }
                )
                .then(function (response) {
                    return response.json();
                })
                .then(function (result) {

                    if (!result.success) {
                        throw new Error(
                            result.message ||
                            'Gagal menyimpan data.'
                        );
                    }

                    window.location.reload();

                })
                .catch(function (error) {

                    if (message) {

                        message.textContent =
                            error.message ||
                            'Terjadi kesalahan.';

                        message.className =
                            'message show error';

                    }

                });

            }
        );

    }


    /* =========================================================
       FILTER AWAL
    ========================================================= */

    applyFilter('all');


    console.log(
        'data_akun.js aktif:',
        buttons.length,
        'filter,',
        getRows().length,
        'baris.'
    );

});