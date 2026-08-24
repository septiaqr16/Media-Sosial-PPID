document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       FILTER PLATFORM + AKUN + TAHUN + BULAN
    ========================================================= */

    (function initPlatformAccountFilter() {

        const filterWrap =
            document.getElementById('platformFilter');

        const accountContainer =
            document.getElementById(
                'accountFilterContainer'
            );

        const accountSelect =
            document.getElementById(
                'filterAkun'
            );

        const yearFilterContainer =
            document.getElementById(
                'yearFilterContainer'
            );

        const monthFilterContainer =
            document.getElementById(
                'monthFilterContainer'
            );

        const monthSelect =
            document.getElementById(
                'filterBulan'
            );

        const yearSelect =
            document.getElementById(
                'filterTahun'
            );


        if (!filterWrap) {
            return;
        }


        const tableRows =
            Array.from(
                document.querySelectorAll(
                    '#view-data-konten .table-wrapper tbody tr[data-platform]'
                )
            );


        const tabs =
            Array.from(
                filterWrap.querySelectorAll(
                    '.filter-tab'
                )
            );


        const accountOptions =
            accountSelect
                ? Array.from(
                    accountSelect.querySelectorAll(
                        'option'
                    )
                )
                : [];


        /* =====================================================
           UPDATE OPTION AKUN
        ===================================================== */

        function updateAccountOptions(platform) {

            const selectedPlatform =
                (platform || '').toUpperCase();


            accountOptions.forEach(
                function (option) {

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


                    option.hidden =
                        !isMatch;

                    option.disabled =
                        !isMatch;
                }
            );


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
           FILTER AKUN
        ===================================================== */

        function showAccountFilter(platform) {

            if (accountContainer) {

                accountContainer.classList.add(
                    'show'
                );
            }

            updateAccountOptions(platform);
        }


        function hideAccountFilter() {

            if (accountContainer) {

                accountContainer.classList.remove(
                    'show'
                );
            }

            if (accountSelect) {

                accountSelect.value = '';
            }

            updateAccountOptions('');
        }


        /* =====================================================
           FILTER TAHUN + BULAN
        ===================================================== */

        function showDateFilter() {

            if (yearFilterContainer) {

                yearFilterContainer.classList.add(
                    'show'
                );
            }

            if (monthFilterContainer) {

                monthFilterContainer.classList.add(
                    'show'
                );
            }
        }


        function hideDateFilter() {

            if (yearFilterContainer) {

                yearFilterContainer.classList.remove(
                    'show'
                );
            }

            if (monthFilterContainer) {

                monthFilterContainer.classList.remove(
                    'show'
                );
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


            tableRows.forEach(
                function (row) {

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


                    const platformMatch =
                        platform === '' ||
                        rowPlatform === platform;


                    const accountMatch =
                        accountId === '' ||
                        rowAccount === accountId;

                    const yearMatch =
                        year === '' ||
                        rowYear === year;

                    const monthMatch =
                        month === '' ||
                        rowMonth === month;

                    const isVisible =
                        platformMatch &&
                        accountMatch &&
                        yearMatch &&
                        monthMatch;

                    row.style.display =
                        isVisible
                            ? ''
                            : 'none';

                    if (isVisible) {

                        visibleCount++;
                    }
                }
            );


            /* UPDATE NOMOR URUT */

            let number = 1;


            tableRows.forEach(
                function (row) {

                    if (
                        row.style.display !== 'none'
                    ) {

                        const numberCell =
                            row.querySelector(
                                'td:first-child'
                            );


                        if (numberCell) {

                            numberCell.textContent =
                                number++;
                        }
                    }
                }
            );


            /* UPDATE JUMLAH DATA */

            const tableCount =
                document.getElementById(
                    'tableCount'
                );


            if (tableCount) {

                tableCount.textContent =
                    visibleCount +
                    ' baris data tersimpan di database.';
            }
        }


        /* =====================================================
           AMBIL FILTER AKTIF
        ===================================================== */

        function applyCurrentFilters() {

            const activeTab =
                filterWrap.querySelector(
                    '.filter-tab.active'
                );


            let platform = '';


            if (activeTab) {

                const filter =
                    (
                        activeTab.dataset.filter || ''
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
           KLIK PLATFORM
        ===================================================== */

        tabs.forEach(
            function (tab) {

                tab.addEventListener(
                    'click',
                    function () {

                        const filter =
                            (
                                tab.dataset.filter || ''
                            ).toUpperCase();


                        tabs.forEach(
                            function (item) {

                                item.classList.remove(
                                    'active'
                                );
                            }
                        );


                        tab.classList.add(
                            'active'
                        );


                        if (filter === 'ALL') {

                            hideAccountFilter();

                        } else {

                            showAccountFilter(
                                filter
                            );
                        }


                        /*
                         * Tahun dan bulan tetap ditampilkan
                         */

                        showDateFilter();


                        applyCurrentFilters();
                    }
                );
            }
        );


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
                '.filter-tab.active'
            );


        if (activeTab) {

            const currentFilter =
                (
                    activeTab.dataset.filter || ''
                ).toUpperCase();


            if (currentFilter === 'ALL') {

                hideAccountFilter();

            } else {

                showAccountFilter(
                    currentFilter
                );
            }


            showDateFilter();

        } else {

            hideAccountFilter();
            hideDateFilter();
        }


        applyCurrentFilters();

    })();


    /* =========================================================
       FORM INPUT / EDIT DATA KONTEN
    ========================================================= */

    const form =
        document.getElementById(
            'dataForm'
        );


    if (form) {

        const dataId =
            document.getElementById(
                'dataId'
            );

        const platform =
            document.getElementById(
                'platform'
            );

        const akunId =
            document.getElementById(
                'akun_id'
            );

        const bulan =
            document.getElementById(
                'bulan'
            );

        const tahun =
            document.getElementById(
                'tahun'
            );

        const jumlah =
            document.getElementById(
                'jumlah_konten'
            );

        const btnCancel =
            document.getElementById(
                'btnCancel'
            );

        const message =
            document.getElementById(
                'message'
            );


        /* =====================================================
           FILTER AKUN PADA FORM
        ===================================================== */

        function updateInputAccountOptions() {

            if (!platform || !akunId) {
                return;
            }


            const selectedPlatform =
                (
                    platform.value || ''
                ).toUpperCase();


            Array.from(
                akunId.options
            ).forEach(
                function (option) {

                    if (!option.value) {

                        option.hidden = false;
                        option.disabled = false;

                        return;
                    }


                    const optionPlatform =
                        (
                            option.dataset.platform ||
                            ''
                        ).toUpperCase();


                    if (
                        selectedPlatform === ''
                    ) {

                        option.hidden = false;

                    } else {

                        option.hidden =
                            optionPlatform !==
                            selectedPlatform;

                        option.disabled =
                            optionPlatform !==
                            selectedPlatform;
                    }
                }
            );


            const selectedOption =
                akunId.options[
                    akunId.selectedIndex
                ];


            if (
                selectedOption &&
                selectedOption.dataset.platform &&
                selectedPlatform !== '' &&
                selectedOption.dataset.platform.toUpperCase()
                    !== selectedPlatform
            ) {

                akunId.value = '';
            }
        }


        if (platform) {

            platform.addEventListener(
                'change',
                updateInputAccountOptions
            );
        }


        /* =====================================================
           SIMPAN DATA
        ===================================================== */

        form.addEventListener(
            'submit',
            async function (event) {

                event.preventDefault();


                const formData =
                    new FormData(form);


                formData.append(
                    'action',
                    'save'
                );


                try {

                    const response =
                        await fetch(
                            'api/simpan_data.php',
                            {
                                method: 'POST',
                                body: formData
                            }
                        );


                    const result =
                        await response.json();


                    showMessage(
                        result.message ||
                        'Proses selesai.',
                        result.success
                    );


                    if (result.success) {

                        const activeView =
                            document.querySelector(
                                '.dashboard-view.active'
                            );


                        if (activeView) {

                            sessionStorage.setItem(
                                'activeView',
                                activeView.id
                            );
                        }


                        setTimeout(
                            function () {

                                location.reload();

                            },
                            300
                        );
                    }

                } catch (error) {

                    console.error(error);

                    showMessage(
                        'Terjadi kesalahan server.',
                        false
                    );
                }
            }
        );


        /* =====================================================
           EDIT DATA
        ===================================================== */

        document
            .querySelectorAll(
                '#view-data-konten .btn-edit'
            )
            .forEach(
                function (button) {

                    button.addEventListener(
                        'click',
                        function () {

                            if (dataId) {

                                dataId.value =
                                    this.dataset.id || '';
                            }


                            if (platform) {

                                platform.value =
                                    this.dataset.platform || '';
                            }


                            updateInputAccountOptions();


                            if (akunId) {

                                akunId.value =
                                    this.dataset.akunId || '';
                            }


                            if (tahun) {

                                tahun.value =
                                    this.dataset.tahun ||
                                    new Date().getFullYear();
                            }


                            if (bulan) {

                                bulan.value =
                                    this.dataset.bulan || '';
                            }


                            if (jumlah) {

                                jumlah.value =
                                    this.dataset.jumlah || 0;
                            }


                            if (btnCancel) {

                                btnCancel.style.display =
                                    'inline-flex';
                            }
                        }
                    );
                }
            );


        /* =====================================================
           BATAL EDIT
        ===================================================== */

        if (btnCancel) {

            btnCancel.addEventListener(
                'click',
                resetForm
            );
        }


        /* =====================================================
           RESET FORM
        ===================================================== */

        function resetForm() {

            form.reset();


            if (dataId) {

                dataId.value = '';
            }


            if (tahun) {

                tahun.value =
                    new Date().getFullYear();
            }


            if (jumlah) {

                jumlah.value = 0;
            }


            if (btnCancel) {

                btnCancel.style.display =
                    'none';
            }


            updateInputAccountOptions();


            if (message) {

                message.style.display =
                    'none';

                message.textContent =
                    '';
            }
        }


        /* =====================================================
           MESSAGE
        ===================================================== */

        function showMessage(
            text,
            success
        ) {

            if (!message) {
                return;
            }


            message.textContent =
                text;


            message.className =
                'message ' +
                (
                    success
                        ? 'success'
                        : 'error'
                );


            message.style.display =
                'flex';
        }


        updateInputAccountOptions();
    }


    /* =========================================================
       MODAL TAMBAH DATA KONTEN
    ========================================================= */

    (function initTambahDataModal() {

        const modalTambahData =
            document.getElementById(
                'modalTambahData'
            );

        const closeTambahData =
            document.getElementById(
                'closeTambahData'
            );

        const btnBatalTambahData =
            document.getElementById(
                'btnBatalTambahData'
            );

        const formTambahData =
            document.getElementById(
                'formTambahData'
            );

        const modalPlatform =
            document.getElementById(
                'modalPlatform'
            );

        const modalAkunId =
            document.getElementById(
                'modalAkunId'
            );

        const originalAkunSelect =
            document.getElementById(
                'akun_id'
            );


        if (!modalTambahData) {
            return;
        }


        function closeModal() {

            modalTambahData.classList.remove(
                'show'
            );
        }


        if (closeTambahData) {

            closeTambahData.addEventListener(
                'click',
                closeModal
            );
        }


        if (btnBatalTambahData) {

            btnBatalTambahData.addEventListener(
                'click',
                closeModal
            );
        }


        modalTambahData.addEventListener(
            'click',
            function (event) {

                if (
                    event.target ===
                    modalTambahData
                ) {

                    closeModal();
                }
            }
        );


        /* =====================================================
           FILTER AKUN BERDASARKAN PLATFORM
        ===================================================== */

        if (
            modalPlatform &&
            modalAkunId
        ) {

            modalPlatform.addEventListener(
                'change',
                function () {

                    const selectedPlatform =
                        (
                            this.value || ''
                        ).toUpperCase();


                    Array.from(
                        modalAkunId.options
                    ).forEach(
                        function (option) {

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


                            option.hidden =
                                !match;

                            option.disabled =
                                !match;
                        }
                    );


                    const selectedOption =
                        modalAkunId.options[
                            modalAkunId.selectedIndex
                        ];


                    if (
                        selectedOption &&
                        selectedOption.dataset.platform &&
                        selectedPlatform !== '' &&
                        selectedOption.dataset.platform
                            .toUpperCase() !== selectedPlatform
                    ) {

                        modalAkunId.value = '';
                    }
                }
            );
        }


        /* =====================================================
           LOAD AKUN KE MODAL
        ===================================================== */

        function loadModalAccountOptions() {

            if (
                !modalAkunId ||
                !originalAkunSelect
            ) {
                return;
            }


            modalAkunId.innerHTML = '';


            const defaultOption =
                document.createElement(
                    'option'
                );


            defaultOption.value = '';

            defaultOption.textContent =
                '-- Pilih Akun --';


            modalAkunId.appendChild(
                defaultOption
            );


            Array.from(
                originalAkunSelect.options
            ).forEach(
                function (option) {

                    if (!option.value) {
                        return;
                    }


                    const newOption =
                        document.createElement(
                            'option'
                        );


                    newOption.value =
                        option.value;


                    newOption.textContent =
                        option.textContent.trim();


                    newOption.dataset.platform =
                        (
                            option.dataset.platform ||
                            ''
                        ).toUpperCase();


                    modalAkunId.appendChild(
                        newOption
                    );
                }
            );
        }

/* =====================================================
   MODAL TAMBAH / EDIT DATA KONTEN
===================================================== */

const btnTambahData =
    document.getElementById('btnTambahData');

const modalTitle =
    modalTambahData
        ? modalTambahData.querySelector('.modal-header h3')
        : null;

const modalDescription =
    modalTambahData
        ? modalTambahData.querySelector('.modal-header p')
        : null;

const modalIdInput =
    formTambahData
        ? formTambahData.querySelector('input[name="id"]')
        : null;

const modalTahun =
    document.getElementById('modalTahun');

const modalBulan =
    document.getElementById('modalBulan');

const modalJumlahKonten =
    document.getElementById('modalJumlahKonten');


/* =====================================================
   TOMBOL TAMBAH
===================================================== */

if (btnTambahData) {

    btnTambahData.addEventListener(
        'click',
        function () {

            loadModalAccountOptions();

            if (formTambahData) {
                formTambahData.reset();
            }

            if (modalIdInput) {
                modalIdInput.value = '';
            }

            if (modalPlatform) {
                modalPlatform.value = '';
            }

            if (modalAkunId) {
                modalAkunId.value = '';
            }

            if (modalTahun) {
                modalTahun.value =
                    new Date().getFullYear();
            }

            if (modalBulan) {
                modalBulan.value = '';
            }

            if (modalJumlahKonten) {
                modalJumlahKonten.value = 0;
            }

            if (modalTitle) {
                modalTitle.textContent =
                    'Tambah Data Konten';
            }

            if (modalDescription) {
                modalDescription.textContent =
                    'Masukkan data konten baru';
            }

            modalTambahData.classList.add('show');
        }
    );
}

        /* =====================================================
           SIMPAN DATA TAMBAH
        ===================================================== */

        if (formTambahData) {

            formTambahData.addEventListener(
                'submit',
                async function (event) {

                    event.preventDefault();


                    const formData =
                        new FormData(
                            formTambahData
                        );


                    const submitBtn =
                        document.getElementById(
                            'btnSimpanTambahData'
                        );


                    if (submitBtn) {

                        submitBtn.disabled = true;

                        submitBtn.innerHTML =
                            '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                    }


                    try {

                        const response =
                            await fetch(
                                'api/simpan_data.php',
                                {
                                    method: 'POST',
                                    body: formData
                                }
                            );


                        const result =
                            await response.json();


                        if (result.success) {

                            sessionStorage.setItem(
                                'activeView',
                                'view-data-konten'
                            );


                            setTimeout(
                                function () {

                                    location.reload();

                                },
                                700
                            );

                        } else {

                            alert(
                                result.message ||
                                'Gagal menyimpan data.'
                            );
                        }

                    } catch (error) {

                        console.error(error);

                        alert(
                            'Terjadi kesalahan server.'
                        );

                    } finally {

                        if (submitBtn) {

                            submitBtn.disabled =
                                false;

                            submitBtn.innerHTML =
                                '<i class="fas fa-save"></i> Simpan';
                        }
                    }
                }
            );
        }

    /* =====================================================
   EDIT DATA KONTEN
===================================================== */

document
    .querySelectorAll(
        '#view-data-konten .btn-edit'
    )
    .forEach(
        function (button) {

            button.addEventListener(
                'click',
                function () {

                    /*
                     * Pastikan akun dimuat terlebih dahulu
                     */
                    loadModalAccountOptions();


                    /*
                     * ID DATA
                     */
                    if (modalIdInput) {

                        modalIdInput.value =
                            this.dataset.id || '';

                    }


                    /*
                     * PLATFORM
                     */
                    if (modalPlatform) {

                        modalPlatform.value =
                            (
                                this.dataset.platform || ''
                            ).toUpperCase();

                    }


                    /*
                     * Filter akun berdasarkan platform
                     */
                    if (
                        modalPlatform &&
                        modalAkunId
                    ) {

                        const selectedPlatform =
                            (
                                modalPlatform.value || ''
                            ).toUpperCase();


                        Array.from(
                            modalAkunId.options
                        ).forEach(
                            function (option) {

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
                                    optionPlatform ===
                                    selectedPlatform;


                                option.hidden =
                                    !match;

                                option.disabled =
                                    !match;

                            }
                        );

                    }


                    /*
                     * AKUN
                     */
                    if (modalAkunId) {

                        modalAkunId.value =
                            this.dataset.akunId || '';

                    }


                    /*
                     * TAHUN
                     */
                    if (modalTahun) {

                        modalTahun.value =
                            this.dataset.tahun || '';

                    }


                    /*
                     * BULAN
                     */
                    if (modalBulan) {

                        modalBulan.value =
                            this.dataset.bulan || '';

                    }

                    /*
                     * JUMLAH KONTEN
                     */
                    if (modalJumlahKonten) {

                        modalJumlahKonten.value =
                            this.dataset.jumlah || 0;

                    }


                    /*
                     * UBAH JUDUL MODAL
                     */
                    if (modalTitle) {

                        modalTitle.textContent =
                            'Edit Data Konten';

                    }


                    if (modalDescription) {

                        modalDescription.textContent =
                            'Ubah data konten yang dipilih';

                    }


                    /*
                     * BUKA MODAL
                     */
                    if (modalTambahData) {

                        modalTambahData.classList.add(
                            'show'
                        );

                    }

                }
            );

        }
    );

    })();


    /* =========================================================
       DELETE DATA KONTEN
    ========================================================= */

    document
        .querySelectorAll(
            '#view-data-konten .btn-delete'
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
                                'ID data tidak ditemukan.'
                            );

                            return;
                        }


                        const confirmDelete =
                            confirm(
                                'Apakah Anda yakin ingin menghapus data ini?'
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
                                    'api/simpan_data.php',
                                    {
                                        method: 'POST',
                                        body: formData
                                    }
                                );


                            const result =
                                await response.json();


                            if (
                                result.success
                            ) {

                                sessionStorage.setItem(
                                    'activeView',
                                    'view-data-konten'
                                );


                                setTimeout(
                                    function () {

                                        location.reload();

                                    },
                                    700
                                );

                            } else {

                                alert(
                                    result.message ||
                                    'Gagal menghapus data.'
                                );
                            }

                        } catch (error) {

                            console.error(error);

                            alert(
                                'Gagal menghapus data.'
                            );
                        }
                    }
                );
            }
        );
/* =========================================================
   PRINT LAPORAN KONTEN
   ========================================================= */

const btnPrint = document.getElementById('btnPrint');

if (btnPrint) {

    btnPrint.addEventListener('click', function () {

        const params = new URLSearchParams();


        /* =====================================================
           PLATFORM
        ===================================================== */

        const activeTab = document.querySelector(
            '#platformFilter .filter-tab.active'
        );

        let platform = '';

        if (activeTab) {

            const selectedPlatform =
                (activeTab.dataset.filter || '')
                    .trim()
                    .toUpperCase();

            if (
                selectedPlatform !== '' &&
                selectedPlatform !== 'ALL'
            ) {
                platform = selectedPlatform;
            }
        }


        /* =====================================================
           AKUN
        ===================================================== */

        const filterAkun =
            document.getElementById('filterAkun');

        const akunId =
            filterAkun
                ? filterAkun.value.trim()
                : '';


        /* =====================================================
           TAHUN
        ===================================================== */

        const filterTahun =
            document.getElementById('filterTahun');

        const tahun =
            filterTahun
                ? filterTahun.value.trim()
                : '';


        /* =====================================================
           BULAN
        ===================================================== */

        const filterBulan =
            document.getElementById('filterBulan');

        const bulan =
            filterBulan
                ? filterBulan.value.trim()
                : '';


        /* =====================================================
           MASUKKAN FILTER KE URL
        ===================================================== */

        if (platform !== '') {
            params.set('platform', platform);
        }

        if (akunId !== '') {
            params.set('akun_id', akunId);
        }

        if (tahun !== '') {
            params.set('tahun', tahun);
        }

        if (bulan !== '') {
            params.set('bulan', bulan);
        }


        /* =====================================================
           DEBUG
           ===================================================== */

        console.log(
            'FILTER PRINT:',
            {
                platform: platform || 'SEMUA PLATFORM',
                akun_id: akunId || 'SEMUA AKUN',
                tahun: tahun || 'SEMUA TAHUN',
                bulan: bulan || 'SEMUA BULAN'
            }
        );


        /* =====================================================
           URL LAPORAN
        ===================================================== */

        const queryString =
            params.toString();

        const printUrl =
            'print_laporan.php' +
            (
                queryString
                    ? '?' + queryString
                    : ''
            );


        console.log(
            'PRINT URL:',
            printUrl
        );


        /* =====================================================
           IFRAME PRINT TERSEMBUNYI
        ===================================================== */

        const oldFrame =
            document.getElementById(
                'hiddenPrintFrame'
            );

        if (oldFrame) {
            oldFrame.remove();
        }


        const printFrame =
            document.createElement('iframe');

        printFrame.id =
            'hiddenPrintFrame';

        printFrame.src =
            printUrl;

        printFrame.style.position =
            'fixed';

        printFrame.style.left =
            '-9999px';

        printFrame.style.top =
            '0';

        printFrame.style.width =
            '1px';

        printFrame.style.height =
            '1px';

        printFrame.style.border =
            '0';

        printFrame.style.visibility =
            'hidden';

        document.body.appendChild(
            printFrame
        );


        /* =====================================================
           SETELAH print_laporan.php SELESAI
        ===================================================== */

        printFrame.onload = function () {

            setTimeout(function () {

                try {

                    printFrame.contentWindow.focus();

                    printFrame.contentWindow.print();

                } catch (error) {

                    console.error(
                        'Gagal membuka print:',
                        error
                    );

                }

            }, 300);

        };


        /* =====================================================
           HAPUS IFRAME SETELAH PRINT
        ===================================================== */

        window.addEventListener(
            'afterprint',
            function removePrintFrame() {

                window.removeEventListener(
                    'afterprint',
                    removePrintFrame
                );

                setTimeout(function () {

                    if (printFrame) {
                        printFrame.remove();
                    }

                }, 300);

            }
        );

    });

}
    }

);