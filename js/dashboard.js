document.addEventListener(
    'DOMContentLoaded',
    function () {


        /*
        |--------------------------------------------------------------------------
        | GRAFIK STATISTIK KONTEN
        |--------------------------------------------------------------------------
        */

        (function initTrendChart() {

            const canvas =
                document.getElementById('contentTrendChart');

            if (!canvas || !window.CHART_DATA || !window.Chart) {
                return;
            }

            const chartData = window.CHART_DATA;

            const platformMeta = {
                INSTAGRAM: {
                    label: 'Instagram',
                    icon: 'fab fa-instagram',
                    solid: '#d6249f',
                    gradientFrom: 'rgba(214, 36, 159, .35)',
                    gradientTo: 'rgba(214, 36, 159, 0)'
                },
                FACEBOOK: {
                    label: 'Facebook',
                    icon: 'fab fa-facebook-f',
                    solid: '#1877f2',
                    gradientFrom: 'rgba(24, 119, 242, .35)',
                    gradientTo: 'rgba(24, 119, 242, 0)'
                },
                TIKTOK: {
                    label: 'TikTok',
                    icon: 'fab fa-tiktok',
                    solid: '#111111',
                    gradientFrom: 'rgba(17, 17, 17, .30)',
                    gradientTo: 'rgba(17, 17, 17, 0)'
                },
                YOUTUBE: {
                    label: 'YouTube',
                    icon: 'fab fa-youtube',
                    solid: '#ff0000',
                    gradientFrom: 'rgba(255, 0, 0, .30)',
                    gradientTo: 'rgba(255, 0, 0, 0)'
                }
            };

            // Lookup ikon & warna berdasarkan label dataset (dipakai tooltip kustom)
            const metaByLabel = {};
            Object.keys(platformMeta).forEach(function (key) {
                metaByLabel[platformMeta[key].label] = platformMeta[key];
            });

            const ctx = canvas.getContext('2d');

            const datasets = Object.keys(platformMeta).map(function (key) {

                const meta = platformMeta[key];

                const gradient =
                    ctx.createLinearGradient(0, 0, 0, canvas.height || 260);

                gradient.addColorStop(0, meta.gradientFrom);
                gradient.addColorStop(1, meta.gradientTo);

                return {
                    label: meta.label,
                    data: chartData.series[key] || [],
                    borderColor: meta.solid,
                    backgroundColor: gradient,
                    pointBackgroundColor: meta.solid,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 3.5,
                    pointHoverRadius: 6,
                    borderWidth: 2.5,
                    tension: 0.4,
                    fill: true
                };

            });


            /*
            |--------------------------------------------------------------------------
            | TOOLTIP KUSTOM (IKON, BUKAN KOTAK WARNA)
            |--------------------------------------------------------------------------
            */

            function getOrCreateTooltip(chart) {

                let tooltipEl = chart.canvas.parentNode.querySelector('.chart-tooltip');

                if (!tooltipEl) {
                    tooltipEl = document.createElement('div');
                    tooltipEl.className = 'chart-tooltip';
                    chart.canvas.parentNode.appendChild(tooltipEl);
                }

                return tooltipEl;

            }

            function externalTooltipHandler(context) {

                const { chart, tooltip } = context;
                const tooltipEl = getOrCreateTooltip(chart);

                if (tooltip.opacity === 0) {
                    tooltipEl.style.opacity = 0;
                    return;
                }

                if (tooltip.body) {

                    const titleLines = tooltip.title || [];

                    let html = '';

                    if (titleLines.length) {
                        html += '<div class="chart-tooltip-title">' + titleLines[0] + '</div>';
                    }

                    html += '<div class="chart-tooltip-body">';

                    tooltip.dataPoints.forEach(function (dp) {

                        const meta = metaByLabel[dp.dataset.label] || {};

                        html +=
                            '<div class="chart-tooltip-row">' +
                                '<i class="' + (meta.icon || 'fas fa-circle') + '" style="color:' + (meta.solid || '#999') + '"></i>' +
                                '<span>' + dp.dataset.label + '</span>' +
                                '<strong>' + dp.formattedValue + '</strong>' +
                            '</div>';

                    });

                    html += '</div>';

                    tooltipEl.innerHTML = html;

                }

                const { offsetLeft: posX, offsetTop: posY } = chart.canvas;

                tooltipEl.style.opacity = 1;
                tooltipEl.style.left = posX + tooltip.caretX + 'px';
                tooltipEl.style.top = posY + tooltip.caretY + 'px';

            }

            new Chart(canvas, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                font: {
                                    family: 'Poppins',
                                    size: 11
                                },
                                color: '#555'
                            }
                        },
                        tooltip: {
                            enabled: false,
                            external: externalTooltipHandler
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: 'Poppins', size: 10.5 },
                                color: '#999'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f0f2f1' },
                            ticks: {
                                font: { family: 'Poppins', size: 10.5 },
                                color: '#999',
                                precision: 0
                            }
                        }
                    }
                }
            });

        })();


        /*
        |--------------------------------------------------------------------------
        | FILTER TABEL PER PLATFORM
        |--------------------------------------------------------------------------
        */

        (function initPlatformFilter() {

            const filterWrap =
                document.getElementById('platformFilter');

            const tableCount =
                document.getElementById('tableCount');

            if (!filterWrap) {
                return;
            }

            const tabs =
                filterWrap.querySelectorAll('.filter-tab');

            const rows =
                document.querySelectorAll('#data-table tbody tr[data-platform]');

            tabs.forEach(function (tab) {

                tab.addEventListener('click', function () {

                    tabs.forEach(function (t) {
                        t.classList.remove('active');
                    });

                    tab.classList.add('active');

                    const filter = tab.dataset.filter;
                    let visibleCount = 0;

                    rows.forEach(function (row) {

                        const match =
                            filter === 'all' ||
                            row.dataset.platform === filter;

                        row.classList.toggle('row-hidden', !match);

                        if (match) {
                            visibleCount++;
                        }

                    });

                    if (tableCount) {

                        tableCount.textContent =
                            visibleCount + ' baris data ditampilkan.';

                    }

                });

            });

        })();


        /*
        |--------------------------------------------------------------------------
        | NAVIGASI ANTAR HALAMAN (TAB) DI SIDEBAR
        |--------------------------------------------------------------------------
        */

        function switchView(viewId) {

            document.querySelectorAll('.dashboard-view').forEach(function (view) {
                view.classList.toggle('active', view.id === viewId);
            });

            document.querySelectorAll('.sidebar nav .nav-link').forEach(function (link) {
                link.classList.toggle('active', link.dataset.view === viewId);
            });

            window.scrollTo({ top: 0, behavior: 'smooth' });

        }

        document.querySelectorAll('.sidebar nav .nav-link').forEach(function (link) {

            link.addEventListener('click', function (event) {

                event.preventDefault();

                switchView(this.dataset.view);

            });

        });


        /*
        |--------------------------------------------------------------------------
        | MODAL EDIT PROFIL ADMIN
        |--------------------------------------------------------------------------
        */

        (function initProfileModal() {

            const profileBtn = document.getElementById('adminProfileBtn');
            const profileModal = document.getElementById('profileModal');
            const closeProfileModal = document.getElementById('closeProfileModal');
            const btnCancelProfile = document.getElementById('btnCancelProfile');
            const profileForm = document.getElementById('profileForm');
            const profileMessage = document.getElementById('profileMessage');

            if (!profileBtn || !profileModal || !profileForm) {
                return;
            }

            function openProfileModal() {
                profileModal.classList.add('show');
                profileMessage.style.display = 'none';
                document.getElementById('profilPasswordLama').value = '';
                document.getElementById('profilPasswordBaru').value = '';
                document.getElementById('profilPasswordKonfirmasi').value = '';
            }

            function closeModal() {
                profileModal.classList.remove('show');
            }

            profileBtn.addEventListener('click', openProfileModal);

            if (closeProfileModal) {
                closeProfileModal.addEventListener('click', closeModal);
            }

            if (btnCancelProfile) {
                btnCancelProfile.addEventListener('click', closeModal);
            }

            profileModal.addEventListener('click', function (event) {
                if (event.target === profileModal) {
                    closeModal();
                }
            });

            function showProfileMessage(text, success) {
                profileMessage.textContent = text;
                profileMessage.className = 'message ' + (success ? 'success' : 'error');
                profileMessage.style.display = 'flex';
            }

            profileForm.addEventListener('submit', async function (event) {

                event.preventDefault();

                const formData = new FormData(profileForm);

                const submitBtn = profileForm.querySelector('.btn-save');
                const originalHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

                try {

                    const response = await fetch('api/update_profil.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    showProfileMessage(result.message, result.success);

                    if (result.success) {

                        document.querySelectorAll('.admin-profile strong').forEach(function (el) {
                            el.textContent = result.nama;
                        });

                        document.querySelectorAll('.admin-profile small').forEach(function (el) {
                            el.textContent = '@' + result.username;
                        });

                        setTimeout(closeModal, 900);

                    }

                } catch (error) {

                    console.error(error);
                    showProfileMessage('Terjadi kesalahan server.', false);

                } finally {

                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;

                }

            });

        })();


        const form =
            document.getElementById(
                'dataForm'
            );


        const dataId =
            document.getElementById(
                'dataId'
            );


        const platform =
            document.getElementById(
                'platform'
            );


        const tahun =
            document.getElementById(
                'tahun'
            );


        const bulan =
            document.getElementById(
                'bulan'
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


        /*
        |--------------------------------------------------------------------------
        | SIMPAN
        |--------------------------------------------------------------------------
        */

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
                        result.message,
                        result.success
                    );


                    if (
                        result.success
                    ) {

                        setTimeout(
                            function () {

                                location.reload();

                            },
                            700
                        );

                    }

                }

                catch (error) {

                    console.error(error);

                    showMessage(
                        'Terjadi kesalahan server.',
                        false
                    );

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | EDIT
        |--------------------------------------------------------------------------
        */

        document.querySelectorAll(
            '.btn-edit'
        ).forEach(
            function (button) {

                button.addEventListener(
                    'click',
                    function () {

                        dataId.value =
                            this.dataset.id;

                        platform.value =
                            this.dataset.platform;

                        tahun.value =
                            this.dataset.tahun;

                        bulan.value =
                            this.dataset.bulan;

                        jumlah.value =
                            this.dataset.jumlah;


                        btnCancel.style.display =
                            'inline-flex';


                        switchView('view-input');

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | BATAL EDIT
        |--------------------------------------------------------------------------
        */

        btnCancel.addEventListener(
            'click',
            function () {

                resetForm();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | DELETE
        |--------------------------------------------------------------------------
        */

        document.querySelectorAll(
            '.btn-delete'
        ).forEach(
            function (button) {

                button.addEventListener(
                    'click',
                    async function () {

                        const id =
                            this.dataset.id;


                        const confirmDelete =
                            confirm(
                                'Apakah Anda yakin ingin menghapus data ini?'
                            );


                        if (
                            !confirmDelete
                        ) {

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

                                        method:
                                            'POST',

                                        body:
                                            formData

                                    }
                                );


                            const result =
                                await response.json();


                            showMessage(
                                result.message,
                                result.success
                            );


                            if (
                                result.success
                            ) {

                                setTimeout(
                                    function () {

                                        location.reload();

                                    },
                                    700
                                );

                            }

                        }

                        catch (error) {

                            console.error(error);

                            showMessage(
                                'Gagal menghapus data.',
                                false
                            );

                        }

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | RESET FORM
        |--------------------------------------------------------------------------
        */

        function resetForm() {

            form.reset();

            dataId.value = '';

            tahun.value =
                new Date()
                    .getFullYear();

            btnCancel.style.display =
                'none';

        }


        /*
        |--------------------------------------------------------------------------
        | MESSAGE
        |--------------------------------------------------------------------------
        */

        function showMessage(
            text,
            success
        ) {

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
                'block';

        }

    }
);