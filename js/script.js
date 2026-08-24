document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       TOMBOL "JELAJAHI INFORMASI"
    ========================================================= */

    const btnJelajahi = document.querySelector('.btn-jelajahi');

    if (btnJelajahi) {
        btnJelajahi.addEventListener('click', function () {
            document.body.classList.add('expanded');
        });
    }


    /* =========================================================
       KARTU SOSMED -> MODAL GRAFIK (fitur lama)
    ========================================================= */

    const cards = document.querySelectorAll('.card[data-platform]');
    const modal = document.getElementById('chartModal');
    const closeModal = document.getElementById('closeModal');
    const yearSelect = document.getElementById('yearSelect');
    const chartTitle = document.getElementById('chartTitle');
    const chartDescription = document.getElementById('chartDescription');
    const chartTotal = document.getElementById('chartTotal');
    const chartAverage = document.getElementById('chartAverage');
    const accountSelect = document.getElementById('accountSelect');
    const canvas = document.getElementById('contentChart');
    const chartLoading = document.getElementById('chartLoading');
    const modalIcon = document.getElementById('modalIcon');
    const modalIconGlyph = document.getElementById('modalIconGlyph');
    const chartTypeToggle = document.getElementById('chartTypeToggle');

    let chartInstance = null;
    let currentPlatform = '';
    let currentChartType = 'bar';
    let lastResult = null;

    // Nama tampilan, ikon, dan warna khas tiap platform
    const PLATFORM_META = {
        INSTAGRAM: {
            label: 'Instagram',
            icon: 'fab fa-instagram',
            cssClass: 'platform-instagram',
            solid: '#218838',
            gradient: ['#72e172', '#075719']
        },
        FACEBOOK: {
            label: 'Facebook',
            icon: 'fab fa-facebook-f',
            cssClass: 'platform-facebook',
            solid: '#218838',
            gradient: ['#72e172', '#075719']
        },
        TIKTOK: {
            label: 'TikTok',
            icon: 'fab fa-tiktok',
            cssClass: 'platform-tiktok',
            solid: '#218838',
            gradient: ['#72e172', '#075719']
        },
        YOUTUBE: {
            label: 'YouTube',
            icon: 'fab fa-youtube',
            cssClass: 'platform-youtube',
            solid: '#218838',
            gradient: ['#72e172', '#075719']
        }
    };

    if (cards.length && modal && canvas) {

        cards.forEach(function (card) {

            card.addEventListener('click', function (event) {

                // Link "Kunjungi" tetap membuka halaman sosmed aslinya,
                if (event.target.closest('.btn-kunjungi')) {
                    return;
                }

                currentPlatform = this.dataset.platform;
                currentChartType = 'bar';
                setActiveChartTypeButton('bar');

                applyPlatformTheme(currentPlatform);

                modal.classList.add('show');

                loadAccountUsername(currentPlatform);

                loadChart(currentPlatform, yearSelect.value);

            });

        });

        if (yearSelect) {
            yearSelect.addEventListener('change', function () {
                if (currentPlatform) {
                    loadChart(currentPlatform, this.value);
                }
            });
        }

        /*
        |--------------------------------------------------------------------------
        | PILIH AKUN
        |--------------------------------------------------------------------------
        */

        if (accountSelect) {

            accountSelect.addEventListener(
                'change',
                function () {

                    if (!currentPlatform) {
                        return;
                    }


                    const accountId =
                        this.value;


                    loadChart(
                        currentPlatform,
                        yearSelect.value,
                        accountId
                    );

                }
            );

        }

        if (chartTypeToggle) {
            chartTypeToggle.querySelectorAll('.chart-type-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    currentChartType = this.dataset.type;
                    setActiveChartTypeButton(currentChartType);
                    if (lastResult) {
                        renderChart(currentPlatform, lastResult);
                    }
                });
            });
        }

        if (closeModal) {
            closeModal.addEventListener('click', function () {
                modal.classList.remove('show');
            });
        }

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                modal.classList.remove('show');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                modal.classList.remove('show');
            }
        });

    }

    function setActiveChartTypeButton(type) {
        if (!chartTypeToggle) return;
        chartTypeToggle.querySelectorAll('.chart-type-btn').forEach(function (btn) {
            btn.classList.toggle('active', btn.dataset.type === type);
        });
    }

    function applyPlatformTheme(platform) {
        const meta = PLATFORM_META[platform];
        if (!meta || !modal) return;

        modal.className = 'chart-modal show ' + meta.cssClass;

        if (modalIconGlyph) {
            modalIconGlyph.className = meta.icon;
        }
    }

    function makeGradient(ctx, area, colors) {
        if (!area) return colors[colors.length - 1];
        const gradient = ctx.createLinearGradient(0, area.top, 0, area.bottom);
        const step = 1 / (colors.length - 1);
        colors.forEach(function (color, index) {
            gradient.addColorStop(index * step, color);
        });
        return gradient;
    }

    async function loadAccountUsername(platform) {

    if (!accountSelect) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | TAMPILKAN LOADING
    |--------------------------------------------------------------------------
    */

    accountSelect.innerHTML = '';

    const loadingOption =
        document.createElement('option');

    loadingOption.value = '';
    loadingOption.textContent =
        'Memuat akun...';

    loadingOption.disabled = true;
    loadingOption.selected = true;

    accountSelect.appendChild(
        loadingOption
    );


    try {

        const response = await fetch(
            `api/akun.php?platform=${encodeURIComponent(platform)}`
        );


        if (!response.ok) {

            throw new Error(
                `HTTP ${response.status}`
            );

        }


        const result =
            await response.json();


        if (!result.success) {

            throw new Error(
                result.message ||
                'Data akun tidak ditemukan.'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | KOSONGKAN DROPDOWN
        |--------------------------------------------------------------------------
        */

        accountSelect.innerHTML = '';


        /*
        |--------------------------------------------------------------------------
        | OPTION SEMUA AKUN
        |--------------------------------------------------------------------------
        */

        const allOption =
            document.createElement('option');

        allOption.value = '';

        allOption.textContent =
            'Semua Akun';

        allOption.selected = true;

        accountSelect.appendChild(
            allOption
        );


        /*
        |--------------------------------------------------------------------------
        | JIKA TIDAK ADA AKUN
        |--------------------------------------------------------------------------
        */

        if (
            !Array.isArray(result.data) ||
            result.data.length === 0
        ) {

            const emptyOption =
                document.createElement('option');

            emptyOption.value = '';

            emptyOption.textContent =
                'Belum ada akun';

            emptyOption.disabled = true;

            accountSelect.appendChild(
                emptyOption
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | MASUKKAN DATA AKUN
        |--------------------------------------------------------------------------
        */

        result.data.forEach(
            function (account) {

                const option =
                    document.createElement('option');


                option.value =
                    account.id;


                option.textContent =
                    account.username;


                option.dataset.username =
                    account.username;


                accountSelect.appendChild(
                    option
                );

            }
        );


    } catch (error) {

        console.error(
            'Gagal memuat username:',
            error
        );


        accountSelect.innerHTML = '';


        const errorOption =
            document.createElement('option');


        errorOption.value = '';

        errorOption.textContent =
            'Gagal memuat akun';

        errorOption.disabled = true;

        errorOption.selected = true;


        accountSelect.appendChild(
            errorOption
        );

    }

}

    async function loadChart(
        platform,
        year,
        accountId = ''
    ) {

        const meta =
            PLATFORM_META[platform] ||
            PLATFORM_META.INSTAGRAM;


        if (chartLoading) {
            chartLoading.classList.add('show');
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | PARAMETER DATA
            |--------------------------------------------------------------------------
            */

            const params = new URLSearchParams();

            params.set(
                'platform',
                platform
            );

            params.set(
                'tahun',
                year
            );


            if (accountId !== '') {

                params.set(
                    'akun_id',
                    accountId
                );

            }


            /*
            |--------------------------------------------------------------------------
            | PARAMETER TAHUN SEBELUMNYA
            |--------------------------------------------------------------------------
            */

            const previousParams =
                new URLSearchParams();

            previousParams.set(
                'platform',
                platform
            );

            previousParams.set(
                'tahun',
                parseInt(year, 10) - 1
            );


            if (accountId !== '') {

                previousParams.set(
                    'akun_id',
                    accountId
                );

            }


            /*
            |--------------------------------------------------------------------------
            | REQUEST
            |--------------------------------------------------------------------------
            */

            const [
                response,
                prevResponse
            ] = await Promise.all([

                fetch(
                    `api/data.php?${params.toString()}`
                ),

                fetch(
                    `api/data.php?${previousParams.toString()}`
                )

            ]);


            if (!response.ok) {

                throw new Error(
                    'Gagal mengambil data grafik.'
                );

            }


            const result =
                await response.json();


            if (!result.success) {

                throw new Error(
                    result.message ||
                    'Data grafik tidak tersedia.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | DATA TAHUN SEBELUMNYA
            |--------------------------------------------------------------------------
            */

            let prevTotal = null;


            if (prevResponse.ok) {

                const prevResult =
                    await prevResponse.json();


                if (prevResult.success) {

                    prevTotal =
                        Number(
                            prevResult.total || 0
                        );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | JUDUL
            |--------------------------------------------------------------------------
            */

            chartTitle.textContent =
                `Jumlah Konten ${meta.label}`;


            const selectedOption =
                accountSelect
                    ? accountSelect.options[
                        accountSelect.selectedIndex
                    ]
                    : null;


            const selectedUsername =
                selectedOption &&
                selectedOption.value !== ''
                    ? selectedOption.textContent
                    : 'Semua Akun';


            chartDescription.textContent =
                `Statistik jumlah konten ${meta.label} - ${selectedUsername} per bulan tahun ${year}.`;


            /*
            |--------------------------------------------------------------------------
            | SIMPAN HASIL
            |--------------------------------------------------------------------------
            */

            lastResult =
                result;


            /*
            |--------------------------------------------------------------------------
            | RENDER
            |--------------------------------------------------------------------------
            */

            renderChart(platform, result);


        } catch (error) {

            console.error(
                'Gagal memuat grafik:',
                error
            );


            if (chartTotal) {
                chartTotal.textContent = '0';
            }


            if (chartAverage) {
                chartAverage.textContent = '0';
            }


            alert(
                'Data grafik gagal dimuat.'
            );


        } finally {

            if (chartLoading) {
                chartLoading.classList.remove('show');
            }

        }

    }

    function renderChart(platform, result) {

        const meta = PLATFORM_META[platform] || PLATFORM_META.INSTAGRAM;

        const labels = result.data.map(item => item.bulan.slice(0, 3));
        const values = result.data.map(item => item.jumlah);

        const total = result.total;
        const average = Math.round(total / 12);

        chartTotal.textContent = new Intl.NumberFormat('id-ID').format(total);
        chartAverage.textContent = new Intl.NumberFormat('id-ID').format(average);

        if (chartInstance) {
            chartInstance.destroy();
        }

        const isLine = currentChartType === 'line';

        chartInstance = new Chart(canvas, {
            type: isLine ? 'line' : 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Jumlah Konten',
                        data: values,
                        borderRadius: isLine ? 0 : 8,
                        borderSkipped: false,
                        backgroundColor: function (context) {
                            const chartArea = context.chart.chartArea;
                            if (isLine) {
                                return makeGradient(context.chart.ctx, chartArea, [meta.solid + '33', meta.solid + '00']);
                            }
                            return makeGradient(context.chart.ctx, chartArea, meta.gradient);
                        },
                        borderColor: meta.solid,
                        borderWidth: isLine ? 2.5 : 0,
                        hoverBackgroundColor: meta.solid,
                        pointBackgroundColor: meta.solid,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: isLine ? 4 : 0,
                        pointHoverRadius: 6,
                        fill: isLine,
                        tension: 0.35,
                        maxBarThickness: 42
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 700,
                    easing: 'easeOutQuart'
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f0f0', drawTicks: false },
                        border: { display: false },
                        ticks: {
                            precision: 0,
                            font: { family: 'Poppins', size: 10 },
                            color: '#999',
                            padding: 8
                        }
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            font: { family: 'Poppins', size: 10, weight: '600' },
                            color: '#666'
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1a1a',
                        titleFont: { family: 'Poppins', size: 11, weight: '600' },
                        bodyFont: { family: 'Poppins', size: 12, weight: '700' },
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            title: function (items) {
                                return result.data[items[0].dataIndex].bulan;
                            },
                            label: function (item) {
                                return new Intl.NumberFormat('id-ID').format(item.parsed.y) + ' konten';
                            }
                        }
                    }
                }
            }
        });

    }

});
