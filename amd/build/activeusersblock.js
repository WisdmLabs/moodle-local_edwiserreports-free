define(['jquery', 'core/chartjs'], function ($, Chart) {
    function init() {
        var data = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Active Users',
                data: [12, 19, 3, 5, 2, 3, 12, 12, 19, 3, 5, 6],
                backgroundColor: [
                    'rgba(0, 0, 0, 0)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)'
                ]
            },
            {
                label: 'Course Enrolment',
                data: [2, 9, 13, 15, 6, 15, 13, 9, 4, 11, 6, 3],
                backgroundColor: [
                    'rgba(0, 0, 0, 0)'
                ],
                borderColor: [
                    'rgba(73, 222, 148, 1)'
                ]
            },
            {
                label: 'Course Completion Rate',
                data: [11, 15, 12, 5, 9, 10, 4, 11, 15, 12, 5, 10],
                backgroundColor: [
                    'rgba(0, 0, 0, 0)'
                ],
                borderColor: [
                    'rgba(62, 142, 247, 1)'
                ]
            }]
        };

        var options = {
            elements: {
                point: {
                    radius: 0
                },
                borderWidth: 4
            },
            responsive: true,
            maintainAspectRatio: false,
            aspectRatio: 1,
            scales: {
                yAxes: [{
                    ticks: {
                        autoSkip: true,
                        maxTicksLimit: 20,
                        minTicksLimit: 20,
                    }
                }]
            }
        };

        var ctx = $('#activeusersblock .ct-chart')[0].getContext('2d');
        Chart.defaults.global.defaultFontFamily = 'Open Sans';
        Chart.defaults.global.defaultFontStyle = 'bold';
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: data,
            options: options
        });
    }

    // Must return the init function
    return {
        init: init
    };
});