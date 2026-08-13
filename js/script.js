document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       TOMBOL "JELAJAHI INFORMASI" (animasi hero -> desain baru)
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
    const chartHighest = document.getElementById('chartHighest');
    const chartHighestLabel = document.getElementById('chartHighestLabel');
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
            solid: '#d6249f',
            gradient: ['#f9ce34', '#d6249f', '#4f5bd5']
        },
        FACEBOOK: {
            label: 'Facebook',
            icon: 'fab fa-facebook-f',
            cssClass: 'platform-facebook',
            solid: '#1877f2',
            gradient: ['#4da3ff', '#1877f2']
        },
        TIKTOK: {
            label: 'TikTok',
            icon: 'fab fa-tiktok',
            cssClass: 'platform-tiktok',
            solid: '#010101',
            gradient: ['#25f4ee', '#010101']
        },
        YOUTUBE: {
            label: 'YouTube',
            icon: 'fab fa-youtube',
            cssClass: 'platform-youtube',
            solid: '#ff0000',
            gradient: ['#ff5f5f', '#ff0000']
        }
    };

    if (cards.length && modal && canvas) {

        cards.forEach(function (card) {

            card.addEventListener('click', function (event) {

                // Link "Kunjungi" tetap membuka halaman sosmed aslinya,
                // tidak memicu modal grafik.
                if (event.target.closest('.btn-kunjungi')) {
                    return;
                }

                currentPlatform = this.dataset.platform;
                currentChartType = 'bar';
                setActiveChartTypeButton('bar');

                applyPlatformTheme(currentPlatform);

                modal.classList.add('show');

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

    async function loadChart(platform, year) {

        const meta = PLATFORM_META[platform] || PLATFORM_META.INSTAGRAM;

        if (chartLoading) chartLoading.classList.add('show');

        try {

            const [response, prevResponse] = await Promise.all([

                fetch(`api/data.php?platform=${encodeURIComponent(platform)}&tahun=${encodeURIComponent(year)}`),

                fetch(`api/data.php?platform=${encodeURIComponent(platform)}&tahun=${encodeURIComponent(parseInt(year, 10) - 1)}`)

            ]);

            if (!response.ok) {
                throw new Error('Gagal mengambil data.');
            }

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message);
            }

            let prevTotal = null;

            if (prevResponse.ok) {
                const prevResult = await prevResponse.json();
                if (prevResult.success) {
                    prevTotal = prevResult.total;
                }
            }

            chartTitle.textContent = `Jumlah Konten ${meta.label}`;
            chartDescription.textContent = `Statistik jumlah konten ${meta.label} per bulan tahun ${year}.`;

            lastResult = result;

            renderChart(platform, result);
            renderGrowthBadge(result.total, prevTotal, year);

        } catch (error) {
            console.error(error);
            alert('Data grafik gagal dimuat.');
        } finally {
            if (chartLoading) chartLoading.classList.remove('show');
        }

    }

    function renderGrowthBadge(total, prevTotal, year) {

        const badge = document.getElementById('chartGrowth');

        if (!badge) return;

        const prevYear = parseInt(year, 10) - 1;

        if (prevTotal === null || prevTotal === 0) {

            if (total > 0) {
                badge.className = 'stat-growth up';
                badge.innerHTML = '<i class="fas fa-star"></i> Data baru';
            } else {
                badge.className = 'stat-growth flat';
                badge.innerHTML = `<i class="fas fa-minus"></i> vs ${prevYear}`;
            }

            return;

        }

        const change = Math.round(((total - prevTotal) / prevTotal) * 100);

        if (change > 0) {
            badge.className = 'stat-growth up';
            badge.innerHTML = `<i class="fas fa-arrow-up"></i> ${change}% vs ${prevYear}`;
        } else if (change < 0) {
            badge.className = 'stat-growth down';
            badge.innerHTML = `<i class="fas fa-arrow-down"></i> ${Math.abs(change)}% vs ${prevYear}`;
        } else {
            badge.className = 'stat-growth flat';
            badge.innerHTML = `<i class="fas fa-equals"></i> Setara ${prevYear}`;
        }

    }

    function renderChart(platform, result) {

        const meta = PLATFORM_META[platform] || PLATFORM_META.INSTAGRAM;

        const labels = result.data.map(item => item.bulan.slice(0, 3));
        const values = result.data.map(item => item.jumlah);

        const total = result.total;
        const monthsWithData = values.filter(v => v > 0).length || 12;
        const average = Math.round(total / 12);

        let highestIndex = 0;
        values.forEach(function (v, i) {
            if (v > values[highestIndex]) highestIndex = i;
        });

        chartTotal.textContent = new Intl.NumberFormat('id-ID').format(total);
        chartAverage.textContent = new Intl.NumberFormat('id-ID').format(average);

        if (total > 0) {
            chartHighest.textContent = result.data[highestIndex].bulan;
            chartHighestLabel.textContent = `Bulan Tertinggi (${new Intl.NumberFormat('id-ID').format(values[highestIndex])})`;
        } else {
            chartHighest.textContent = '-';
            chartHighestLabel.textContent = 'Bulan Tertinggi';
        }

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
