
function fetchEnrolledData() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'graph_function.php?type=enrolled', true);
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            var data = JSON.parse(xhr.responseText);
            updateEnrolledChart(data);
        }
    };
    xhr.onerror = function() {
        console.error('Request failed for enrolled data.');
    };
    xhr.send();
}

function fetchAbsentData() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'graph_function.php?type=absent', true);
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            var data = JSON.parse(xhr.responseText);
            updateAbsentChart(data);
        }
    };
    xhr.onerror = function() {
        console.error('Request failed for absent data.');
    };
    xhr.send();
}

function updateEnrolledChart(data) {
    var labels = data.labels;
    var values = data.values;

    var ctx = document.getElementById('enrolledChart').getContext('2d');

    if(window.enrolledChart instanceof Chart) {
        window.enrolledChart.data.labels = labels;
        window.enrolledChart.data.datasets[0].data = values;
        window.enrolledChart.update();
    } else {
        window.enrolledChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'NUMBER OF ENROLLED STUDENTS',
                    data: values,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    }
}

function updateAbsentChart(data) {
    var labels = data.labels;
    var values = data.values;

    var ctx = document.getElementById('absentChart').getContext('2d');

    if(window.absentChart instanceof Chart) {
        window.absentChart.data.labels = labels;
        window.absentChart.data.datasets[0].data = values;
        window.absentChart.update();
    } else {
        window.absentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'NUMBER OF ABSENT STUDENTS TODAY',
                    data: values,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    }
}

// Initial data fetch
fetchEnrolledData();
fetchAbsentData();

// Set intervals to update data every 10 seconds (optional)
setInterval(fetchEnrolledData, 10000);
setInterval(fetchAbsentData, 10000);
