document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       GRAFIK STATISTIK KONTEN
    ========================================================= */

    (function initTrendChart() {

        const canvas = document.getElementById('contentTrendChart');

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

        const metaByLabel = {};

        Object.keys(platformMeta).forEach(function (key) {
            metaByLabel[
                platformMeta[key].label
            ] = platformMeta[key];
        });

        const ctx = canvas.getContext('2d');

        const datasets = Object.keys(platformMeta).map(function (key) {

            const meta = platformMeta[key];

            const gradient = ctx.createLinearGradient(
                0,
                0,
                0,
                canvas.height || 260
            );

            gradient.addColorStop(
                0,
                meta.gradientFrom
            );

            gradient.addColorStop(
                1,
                meta.gradientTo
            );

            return {
                label: meta.label,

                data:
                    chartData.series[key] || [],

                borderColor:
                    meta.solid,

                backgroundColor:
                    gradient,

                pointBackgroundColor:
                    meta.solid,

                pointBorderColor:
                    '#ffffff',

                pointBorderWidth:
                    2,

                pointRadius:
                    3.5,

                pointHoverRadius:
                    6,

                borderWidth:
                    2.5,

                tension:
                    0.4,

                fill:
                    true
            };
        });


        /* =====================================================
           CUSTOM TOOLTIP
        ===================================================== */

        function getOrCreateTooltip(chart) {

            let tooltipEl =
                chart.canvas.parentNode.querySelector(
                    '.chart-tooltip'
                );

            if (!tooltipEl) {

                tooltipEl =
                    document.createElement('div');

                tooltipEl.className =
                    'chart-tooltip';

                chart.canvas.parentNode.appendChild(
                    tooltipEl
                );
            }

            return tooltipEl;
        }


        function externalTooltipHandler(context) {

            const chart =
                context.chart;

            const tooltip =
                context.tooltip;

            const tooltipEl =
                getOrCreateTooltip(chart);

            if (tooltip.opacity === 0) {

                tooltipEl.style.opacity = 0;

                return;
            }

            if (tooltip.body) {

                const titleLines =
                    tooltip.title || [];

                let html = '';

                if (titleLines.length) {

                    html +=
                        '<div class="chart-tooltip-title">' +
                        escapeHtml(titleLines[0]) +
                        '</div>';
                }

                html +=
                    '<div class="chart-tooltip-body">';

                tooltip.dataPoints.forEach(function (dp) {

                    const meta =
                        metaByLabel[dp.dataset.label] || {};

                    html +=
                        '<div class="chart-tooltip-row">' +

                        '<i class="' +
                        (
                            meta.icon ||
                            'fas fa-circle'
                        ) +
                        '" style="color:' +
                        (
                            meta.solid ||
                            '#999'
                        ) +
                        '"></i>' +

                        '<span>' +
                        escapeHtml(
                            dp.dataset.label
                        ) +
                        '</span>' +

                        '<strong>' +
                        escapeHtml(
                            dp.formattedValue
                        ) +
                        '</strong>' +

                        '</div>';
                });

                html +=
                    '</div>';

                tooltipEl.innerHTML =
                    html;
            }

            tooltipEl.style.opacity =
                1;

            tooltipEl.style.left =
                tooltip.caretX + 'px';

            tooltipEl.style.top =
                tooltip.caretY + 'px';
        }


        new Chart(
            canvas,
            {
                type: 'line',

                data: {
                    labels:
                        chartData.labels,

                    datasets:
                        datasets
                },

                options: {

                    responsive:
                        true,

                    maintainAspectRatio:
                        false,

                    interaction: {
                        mode:
                            'index',

                        intersect:
                            false
                    },

                    plugins: {

                        legend: {

                            display:
                                true,

                            position:
                                'top',

                            align:
                                'end',

                            labels: {

                                usePointStyle:
                                    true,

                                pointStyle:
                                    'circle',

                                boxWidth:
                                    8,

                                font: {

                                    family:
                                        'Poppins',

                                    size:
                                        11
                                },

                                color:
                                    '#555'
                            }
                        },

                        tooltip: {

                            enabled:
                                false,

                            external:
                                externalTooltipHandler
                        }
                    },

                    scales: {

                        x: {

                            grid: {
                                display:
                                    false
                            },

                            ticks: {

                                font: {

                                    family:
                                        'Poppins',

                                    size:
                                        10.5
                                },

                                color:
                                    '#999'
                            }
                        },

                        y: {

                            beginAtZero:
                                true,

                            grid: {

                                color:
                                    '#f0f2f1'
                            },

                            ticks: {

                                font: {

                                    family:
                                        'Poppins',

                                    size:
                                        10.5
                                },

                                color:
                                    '#999',

                                precision:
                                    0
                            }
                        }
                    }
                }
            }
        );

    })();


    /* =========================================================
       HELPER ESCAPE HTML
    ========================================================= */

    function escapeHtml(value) {

        const div =
            document.createElement('div');

        div.textContent =
            value == null
                ? ''
                : String(value);

        return div.innerHTML;
    }

    /* =========================================================
       MODAL PROFILE
    ========================================================= */

    (function initProfileModal() {

        const profileBtn =
            document.getElementById(
                'adminProfileBtn'
            );

        const profileModal =
            document.getElementById(
                'profileModal'
            );

        const closeProfileModal =
            document.getElementById(
                'closeProfileModal'
            );

        const btnCancelProfile =
            document.getElementById(
                'btnCancelProfile'
            );

        const profileForm =
            document.getElementById(
                'profileForm'
            );

        const profileMessage =
            document.getElementById(
                'profileMessage'
            );


        if (
            !profileBtn ||
            !profileModal ||
            !profileForm
        ) {
            return;
        }


        function openProfileModal() {

            profileModal.classList.add(
                'show'
            );

            if (profileMessage) {

                profileMessage.style.display =
                    'none';
            }


            const oldPassword =
                document.getElementById(
                    'profilPasswordLama'
                );

            const newPassword =
                document.getElementById(
                    'profilPasswordBaru'
                );

            const confirmPassword =
                document.getElementById(
                    'profilPasswordKonfirmasi'
                );


            if (oldPassword) {
                oldPassword.value = '';
            }

            if (newPassword) {
                newPassword.value = '';
            }

            if (confirmPassword) {
                confirmPassword.value = '';
            }
        }


        function closeModal() {

            profileModal.classList.remove(
                'show'
            );
        }


        profileBtn.addEventListener(
            'click',
            openProfileModal
        );


        if (closeProfileModal) {

            closeProfileModal.addEventListener(
                'click',
                closeModal
            );
        }


        if (btnCancelProfile) {

            btnCancelProfile.addEventListener(
                'click',
                closeModal
            );
        }


        profileModal.addEventListener(
            'click',
            function (event) {

                if (
                    event.target ===
                    profileModal
                ) {
                    closeModal();
                }
            }
        );


        function showProfileMessage(
            text,
            success
        ) {

            if (!profileMessage) {
                return;
            }

            profileMessage.textContent =
                text;

            profileMessage.className =
                'message ' +
                (
                    success
                        ? 'success'
                        : 'error'
                );

            profileMessage.style.display =
                'flex';
        }


        profileForm.addEventListener(
            'submit',
            async function (event) {

                event.preventDefault();


                const formData =
                    new FormData(
                        profileForm
                    );


                const submitBtn =
                    profileForm.querySelector(
                        '.btn-save'
                    );


                const originalHtml =
                    submitBtn
                        ? submitBtn.innerHTML
                        : '';


                if (submitBtn) {

                    submitBtn.disabled =
                        true;

                    submitBtn.innerHTML =
                        '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                }


                try {

                    const response =
                        await fetch(
                            'api/update_profil.php',
                            {
                                method: 'POST',
                                body: formData
                            }
                        );


                    const result =
                        await response.json();


                    showProfileMessage(
                        result.message ||
                        'Proses selesai.',
                        result.success
                    );


                    if (
                        result.success
                    ) {

                        document
                            .querySelectorAll(
                                '.admin-profile strong'
                            )
                            .forEach(
                                function (el) {

                                    el.textContent =
                                        result.nama;
                                }
                            );


                        document
                            .querySelectorAll(
                                '.admin-profile small'
                            )
                            .forEach(
                                function (el) {

                                    el.textContent =
                                        '@' +
                                        result.username;
                                }
                            );


                        setTimeout(
                            closeModal,
                            900
                        );
                    }

                } catch (error) {

                    console.error(error);

                    showProfileMessage(
                        'Terjadi kesalahan server.',
                        false
                    );

                } finally {

                    if (submitBtn) {

                        submitBtn.disabled =
                            false;

                        submitBtn.innerHTML =
                            originalHtml;
                    }
                }
            }
        );

    })();

});