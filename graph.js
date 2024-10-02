
function fetchData() {

    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'graph_function.php', true); 
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            
            var data = JSON.parse(xhr.responseText);
            updateChart(data); 
        } else {
     
        }
    };
    xhr.onerror = function() {
     
    };
    xhr.send();
}


function updateChart(data) {
    var labels = data.labels;
    var values = data.values;


    var ctx = document.getElementById('graphchart').getContext('2d');

    if(window.graphchart instanceof Chart) {

        window.graphchart.data.labels = labels;
        window.graphchart.data.datasets[0].data = values;
        window.graphchart.update();
    } else {
        window.graphchart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'NUMBER OF ENROLLED STUDENT',
                    data: values,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(199, 199, 199, 0.2)',
                        'rgba(83, 102, 255, 0.2)',
                        'rgba(255, 102, 102, 0.2)',
                        'rgba(102, 255, 178, 0.2)',
                        'rgba(178, 102, 255, 0.2)',
                        'rgba(255, 204, 102, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)',
                        'rgba(83, 102, 255, 1)',
                        'rgba(255, 102, 102, 1)',
                        'rgba(102, 255, 178, 1)',
                        'rgba(178, 102, 255, 1)',
                        'rgba(255, 204, 102, 1)'
                    ],
                    
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
                },
               
                
            }
            
        });
    }
}

fetchData();

setInterval(fetchData, 10000);