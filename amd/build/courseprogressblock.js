define(['jquery', 'core/chartjs'], function ($, Chart) {
    function init() {
        var data = {
            labels: ['0%', '20%', '40%', '60%', '80%', '100%'],
            datasets: [{
                label: 'Active Users',
                data: [12, 19, 3, 5, 2, 6],
                backgroundColor: [
                    "#fe6384",
                    "#36a2eb",
                    "#fdce56",
                    "#cacbd0",
                    "#4ac0c0",
                    "#FF851B",
                ],
            }]
        };

        var options = {
            responsive: true,
            maintainAspectRatio: false,
            aspectRatio: 1,
        };
        var ctx = $('#courseprogressblock .ct-chart')[0].getContext('2d');
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