document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       FILTER PLATFORM + AKUN + TAHUN + BULAN
    ========================================================= */

    (function initFollowerFilter() {

        const filterWrap =
            document.getElementById('followerPlatformFilter');

        const accountContainer =
            document.getElementById('accountFilterContainer');

        const accountSelect =
            document.getElementById('filterAkun');

        const yearFilterContainer =
            document.getElementById('followerYearFilterContainer');

        const monthFilterContainer =
            document.getElementById('followerMonthFilterContainer');

        const monthSelect =
            document.getElementById('followerFilterBulan');

        const yearSelect =
            document.getElementById('followerFilterTahun');

        if (!filterWrap) {
            return;
        }

        /* =====================================================
           AMBIL BARIS TABEL FOLLOWER
        ===================================================== */

        const tableRows = Array.from(
            document.querySelectorAll(
                '#follower-table .table-wrapper tbody tr[data-platform]'
            )
        );

        /* =====================================================
           AMBIL TOMBOL FILTER PLATFORM
        ===================================================== */

        const tabs = Array.from(
            filterWrap.querySelectorAll(
                '.follower-filter-tab'
            )
        );

        /* =====================================================
           SIMPAN OPTION AKUN
        ===================================================== */

        const accountOptions = accountSelect
            ? Array.from(accountSelect.querySelectorAll('option'))
            : [];


        /* =====================================================
           UPDATE OPTION AKUN BERDASARKAN PLATFORM
        ===================================================== */

        function updateAccountOptions(platform) {

            const selectedPlatform =
                (platform || '').toUpperCase();

            accountOptions.forEach(function (option) {

                /* OPTION SEMUA AKUN */
                if (!option.value) {
                    option.hidden = false;
                    option.disabled = false;
                    return;
                }

                const accountPlatform =
                    (
                        option.dataset.platform || ''
                    ).toUpperCase();

                const isMatch =
                    selectedPlatform === '' ||
                    accountPlatform === selectedPlatform;

                option.hidden = !isMatch;
                option.disabled = !isMatch;
            });


            /* Jika akun yang sedang dipilih tidak sesuai platform */
            if (accountSelect) {

                const selectedOption =
                    accountSelect.options[
                        accountSelect.selectedIndex
                    ];

                if (
                    selectedOption &&
                    selectedOption.dataset.platform &&
                    selectedPlatform !== '' &&
                    selectedOption.dataset.platform.toUpperCase()
                        !== selectedPlatform
                ) {
                    accountSelect.value = '';
                }
            }
        }


        /* =====================================================
           TAMPILKAN FILTER AKUN
        ===================================================== */

        function showAccountFilter(platform) {

            if (accountContainer) {
                accountContainer.classList.add('show');
            }

            updateAccountOptions(platform);
        }


        /* =====================================================
           SEMBUNYIKAN FILTER AKUN
        ===================================================== */

        function hideAccountFilter() {

            if (accountContainer) {
                accountContainer.classList.remove('show');
            }

            if (accountSelect) {
                accountSelect.value = '';
            }

            updateAccountOptions('');
        }


        /* =====================================================
           TAMPILKAN FILTER TAHUN + BULAN
        ===================================================== */

        function showDateFilter() {

            if (yearFilterContainer) {
                yearFilterContainer.classList.add('show');
            }

            if (monthFilterContainer) {
                monthFilterContainer.classList.add('show');
            }
        }


        /* =====================================================
           SEMBUNYIKAN FILTER TAHUN + BULAN
        ===================================================== */

        function hideDateFilter() {

            if (yearFilterContainer) {
                yearFilterContainer.classList.remove('show');
            }

            if (monthFilterContainer) {
                monthFilterContainer.classList.remove('show');
            }

            if (yearSelect) {
                yearSelect.value = '';
            }

            if (monthSelect) {
                monthSelect.value = '';
            }
        }


        /* =====================================================
           FILTER TABEL
        ===================================================== */

        function filterTable(
            platform,
            accountId,
            year,
            month
        ) {

            let visibleCount = 0;

            tableRows.forEach(function (row) {

                const rowPlatform =
                    (
                        row.dataset.platform || ''
                    )
                    .trim()
                    .toUpperCase();

                const rowAccount =
                    row.dataset.akunId || '';

                const rowYear =
                    row.dataset.tahun || '';

                const rowMonth =
                    row.dataset.bulan || '';


                /* PLATFORM */
                const platformMatch =
                    platform === '' ||
                    rowPlatform === platform;


                /* AKUN */
                const accountMatch =
                    accountId === '' ||
                    rowAccount === accountId;


                /* TAHUN */
                const yearMatch =
                    year === '' ||
                    rowYear === year;


                /* BULAN */
                const monthMatch =
                    month === '' ||
                    rowMonth === month;


                const isVisible =
                    platformMatch &&
                    accountMatch &&
                    yearMatch &&
                    monthMatch;


                row.style.display =
                    isVisible ? '' : 'none';


                if (isVisible) {
                    visibleCount++;
                }
            });


            /* =================================================
               UPDATE NOMOR URUT
            ================================================= */

            let number = 1;

            tableRows.forEach(function (row) {

                if (row.style.display !== 'none') {

                    const numberCell =
                        row.querySelector('td:first-child');

                    if (numberCell) {
                        numberCell.textContent = number++;
                    }
                }
            });


            /* =================================================
               UPDATE JUMLAH DATA
            ================================================= */

            const tableCount =
                document.getElementById(
                    'followerTableCount'
                );

            if (tableCount) {

                tableCount.textContent =
                    visibleCount +
                    ' baris data follower tersimpan di database.';
            }
        }


        /* =====================================================
           AMBIL FILTER YANG SEDANG AKTIF
        ===================================================== */

        function applyCurrentFilters() {

            const activeTab =
                filterWrap.querySelector(
                    '.follower-filter-tab.active'
                );

            let platform = '';

            if (activeTab) {

                const filter =
                    (
                        activeTab.dataset.followerFilter || ''
                    ).toUpperCase();

                if (filter !== 'ALL') {
                    platform = filter;
                }
            }


            const accountId =
                accountSelect
                    ? accountSelect.value
                    : '';


            const year =
                yearSelect
                    ? yearSelect.value
                    : '';


            const month =
                monthSelect
                    ? monthSelect.value
                    : '';


            filterTable(
                platform,
                accountId,
                year,
                month
            );
        }


        /* =====================================================
           KLIK FILTER PLATFORM
        ===================================================== */

        tabs.forEach(function (tab) {

            tab.addEventListener(
                'click',
                function (event) {

                    /*
                     * Mencegah tombol melakukan submit/reload
                     */
                    event.preventDefault();


                    const filter =
                        (
                            tab.dataset.followerFilter || ''
                        ).toUpperCase();


                    /* HAPUS ACTIVE DARI SEMUA TAB */

                    tabs.forEach(function (item) {

                        item.classList.remove('active');

                    });


                    /* AKTIFKAN TAB YANG DIKLIK */

                    tab.classList.add('active');


                    /* FILTER AKUN */

                    if (filter === 'ALL') {

                        hideAccountFilter();

                    } else {

                        showAccountFilter(filter);

                    }


                    /*
                     * Filter bulan dan tahun
                     * tetap ditampilkan
                     */

                    showDateFilter();


                    /*
                     * FILTER TABEL TANPA RELOAD
                     */

                    applyCurrentFilters();
                }
            );
        });


        /* =====================================================
           PILIH AKUN
        ===================================================== */

        if (accountSelect) {

            accountSelect.addEventListener(
                'change',
                function () {

                    applyCurrentFilters();

                }
            );
        }


        /* =====================================================
           PILIH TAHUN
        ===================================================== */

        if (yearSelect) {

            yearSelect.addEventListener(
                'change',
                function () {

                    applyCurrentFilters();

                }
            );
        }


        /* =====================================================
           PILIH BULAN
        ===================================================== */

        if (monthSelect) {

            monthSelect.addEventListener(
                'change',
                function () {

                    applyCurrentFilters();

                }
            );
        }


        /* =====================================================
           KONDISI AWAL
        ===================================================== */

        const activeTab =
            filterWrap.querySelector(
                '.follower-filter-tab.active'
            );


        if (activeTab) {

            const currentFilter =
                (
                    activeTab.dataset.followerFilter || ''
                ).toUpperCase();


            if (currentFilter === 'ALL') {

                hideAccountFilter();

            } else {

                showAccountFilter(currentFilter);

            }


            showDateFilter();

        } else {

            hideAccountFilter();
            hideDateFilter();

        }


        /* =====================================================
           TERAPKAN FILTER SAAT PERTAMA KALI LOAD
        ===================================================== */

        applyCurrentFilters();

    })();



    /* =========================================================
       MODAL TAMBAH / EDIT FOLLOWER
    ========================================================= */

    (function initFollowerModal() {

        const modal =
            document.getElementById('modalTambahData');

        const btnTambah =
            document.getElementById('btnTambahData');

        const closeModalButton =
            document.getElementById('closeFollowerModal');

        const btnCancel =
            document.getElementById('btnCancelFollower');

        const form =
            document.getElementById('followerForm');

        const modalTitle =
            document.getElementById('followerModalTitle');

        const followerId =
            document.getElementById('followerId');

        const platform =
            document.getElementById('followerPlatform');

        const akun =
            document.getElementById('followerAkun');

        const tahun =
            document.getElementById('followerTahun');

        const bulan =
            document.getElementById('followerBulan');

        const jumlahFollower =
            document.getElementById('jumlahFollower');

        const message =
            document.getElementById('followerMessage');


        if (!modal) {
            return;
        }


        /* =====================================================
           FILTER AKUN PADA FORM
        ===================================================== */

        function updateFormAccountOptions() {

            if (!platform || !akun) {
                return;
            }

            const selectedPlatform =
                (
                    platform.value || ''
                ).toUpperCase();


            Array.from(akun.options).forEach(
                function (option) {

                    /* OPTION DEFAULT */

                    if (!option.value) {

                        option.hidden = false;
                        option.disabled = false;

                        return;
                    }


                    const optionPlatform =
                        (
                            option.dataset.platform || ''
                        ).toUpperCase();


                    const match =
                        selectedPlatform === '' ||
                        optionPlatform === selectedPlatform;


                    option.hidden = !match;
                    option.disabled = !match;
                }
            );


            /* =================================================
               RESET AKUN JIKA TIDAK SESUAI PLATFORM
            ================================================= */

            const selectedOption =
                akun.options[
                    akun.selectedIndex
                ];


            if (
                selectedOption &&
                selectedOption.dataset.platform &&
                selectedPlatform !== '' &&
                selectedOption.dataset.platform.toUpperCase()
                    !== selectedPlatform
            ) {

                akun.value = '';

            }
        }


        /* =====================================================
           PLATFORM BERUBAH
        ===================================================== */

        if (platform) {

            platform.addEventListener(
                'change',
                function () {

                    updateFormAccountOptions();

                }
            );
        }


        /* =====================================================
           BUKA MODAL TAMBAH
        ===================================================== */

        if (btnTambah) {

            btnTambah.addEventListener(
                'click',
                function () {

                    if (form) {
                        form.reset();
                    }


                    if (followerId) {
                        followerId.value = '';
                    }


                    if (tahun) {
                        tahun.value =
                            new Date().getFullYear();
                    }


                    if (jumlahFollower) {
                        jumlahFollower.value = '0';
                    }


                    if (modalTitle) {

                        modalTitle.textContent =
                            'Tambah Data Follower';

                    }


                    if (message) {

                        message.textContent = '';
                        message.className = 'message';
                        message.style.display = 'none';

                    }


                    updateFormAccountOptions();


                    modal.classList.add('show');
                }
            );
        }


        /* =====================================================
           TUTUP MODAL
        ===================================================== */

        function closeModal() {

            modal.classList.remove('show');

        }


        if (closeModalButton) {

            closeModalButton.addEventListener(
                'click',
                closeModal
            );
        }


        if (btnCancel) {

            btnCancel.addEventListener(
                'click',
                closeModal
            );
        }


        /* =====================================================
           KLIK AREA LUAR MODAL
        ===================================================== */

        modal.addEventListener(
            'click',
            function (event) {

                if (event.target === modal) {

                    closeModal();

                }
            }
        );


        /* =====================================================
           SUBMIT FORM TAMBAH / EDIT
        ===================================================== */

        if (form) {
    form.addEventListener(
        'submit',
        async function (event) {
            event.preventDefault();

            const formData = new FormData(form);

            /*
             * Pastikan jumlah follower dikirim
             * sebagai angka tanpa pemisah ribuan.
             */
            if (jumlahFollower) {
                const rawValue = jumlahFollower.value
                    .toString()
                    .replace(/\./g, '')
                    .replace(/,/g, '');

                formData.set(
                    'jumlah_follower',
                    rawValue
                );
            }

            formData.set(
                'action',
                followerId && followerId.value
                    ? 'update'
                    : 'save'
            );

            const submitButton =
                form.querySelector('.btn-save');

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML =
                    '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            }

            try {
                const response = await fetch(
                    'api/simpan_follower.php',
                    {
                        method: 'POST',
                        body: formData
                    }
                );

                const result =
                    await response.json();

                if (result.success) {

                    if (message) {
                        message.textContent =
                            result.message ||
                            'Data follower berhasil disimpan.';

                        message.className =
                            'message success';

                        message.style.display =
                            'flex';
                    }

                    setTimeout(
                        function () {
                            location.reload();
                        },
                        500
                    );

                } else {

                    if (message) {
                        message.textContent =
                            result.message ||
                            'Gagal menyimpan data follower.';

                        message.className =
                            'message error';

                        message.style.display =
                            'flex';
                    }
                }

            } catch (error) {

                console.error(error);

                if (message) {
                    message.textContent =
                        'Terjadi kesalahan server.';

                    message.className =
                        'message error';

                    message.style.display =
                        'flex';
                }

            } finally {

                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.innerHTML =
                        '<i class="fas fa-save"></i> Simpan Data';
                }
            }
        }
    );
}


        /* =====================================================
           EDIT DATA FOLLOWER
        ===================================================== */

        document
            .querySelectorAll(
                '#follower-table .btn-edit-follower'
            )
            .forEach(
                function (button) {

                    button.addEventListener(
                        'click',
                        function () {

                            /* ================================
                               ID
                            ================================= */

                            if (followerId) {

                                followerId.value =
                                    this.dataset.id || '';

                            }


                            /* ================================
                               PLATFORM
                            ================================= */

                            if (platform) {

                                platform.value =
                                    (
                                        this.dataset.platform || ''
                                    ).toUpperCase();

                            }


                            /*
                             * Update option akun terlebih dahulu
                             */

                            updateFormAccountOptions();


                            /* ================================
                               AKUN
                            ================================= */

                            if (akun) {

                                akun.value =
                                    this.dataset.akunId || '';

                            }


                            /* ================================
                               TAHUN
                            ================================= */

                            if (tahun) {

                                tahun.value =
                                    this.dataset.tahun || '';

                            }


                            /* ================================
                               BULAN
                            ================================= */

                            if (bulan) {

                                bulan.value =
                                    this.dataset.bulan || '';

                            }


                            /* ================================
                               JUMLAH FOLLOWER
                            ================================= */

                            if (jumlahFollower) {

                                jumlahFollower.value =
                                    this.dataset.jumlah || 0;

                            }


                            /* ================================
                               JUDUL MODAL
                            ================================= */

                            if (modalTitle) {

                                modalTitle.textContent =
                                    'Edit Data Follower';

                            }


                            /* ================================
                               BUKA MODAL
                            ================================= */

                            modal.classList.add('show');

                        }
                    );
                }
            );


        /* =====================================================
           KONDISI AWAL
        ===================================================== */

        updateFormAccountOptions();

    })();



    /* =========================================================
       DELETE DATA FOLLOWER
    ========================================================= */

    document
        .querySelectorAll(
            '#follower-table .btn-delete-follower'
        )
        .forEach(
            function (button) {

                button.addEventListener(
                    'click',
                    async function () {

                        const id =
                            this.dataset.id;


                        if (!id) {

                            alert(
                                'ID data follower tidak ditemukan.'
                            );

                            return;
                        }


                        const confirmDelete =
                            confirm(
                                'Apakah Anda yakin ingin menghapus data follower ini?'
                            );


                        if (!confirmDelete) {
                            return;
                        }


                        const formData =
                            new FormData();


                        formData.append(
                            'action',
                            'delete'
                        );


                        formData.append(
                            'id',
                            id
                        );


                        try {

                            const response =
                                await fetch(
                                    'api/simpan_follower.php',
                                    {
                                        method: 'POST',
                                        body: formData
                                    }
                                );


                            const result =
                                await response.json();


                            if (result.success) {

                                setTimeout(
                                    function () {

                                        location.reload();

                                    },
                                    300
                                );

                            } else {

                                alert(
                                    result.message ||
                                    'Gagal menghapus data follower.'
                                );

                            }

                        } catch (error) {

                            console.error(error);

                            alert(
                                'Terjadi kesalahan saat menghapus data follower.'
                            );

                        }

                    }
                );
            }
        );

});