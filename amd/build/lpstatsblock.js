define(['jquery', 'core/chartjs'], function ($, Chart) {
    function init() {
        var data = {
            labels: ['Completed', 'Incompleted'],
            datasets: [{
                label: 'Active Users',
                data: [12, 19],
                backgroundColor: [
                    "#fe6384",
                    "#36a2eb"
                ],
            }]
        };

        var options = {
            responsive: true,
            maintainAspectRatio: false,
            aspectRatio: 1,
        };
        var ctx = $('#lpstatsblock .ct-chart')[0].getContext('2d');
        var myPieChart = new Chart(ctx, {
            type: 'pie',
            data: data,
            options: options
        });
    }

    // Must return the init function
    return {
        init: init
    };
});