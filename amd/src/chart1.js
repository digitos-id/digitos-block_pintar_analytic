window.onload = init;
let url = window.location.search;
let queryString = url.split('?')[1];
let id = queryString.split('=')[1];
function init() {
	$.ajax({
		url: '/blocks/pintar_analytic/user.php',
		method: 'POST',
		data: {id: id},
		dataType: 'json',
		success: function (data) {
			console.log(data.total_enrolled_user);
			
			Highcharts.chart("chart1", {
                		chart: {
					type: "pie",
					plotShadow: false,
					plotBorderWidth: null,
					plotBackgroundColor: null
            			},
            			title: {
                			text: "Persentase Peserta Selesai"
            			},
				tooltip: {
					pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>',
				},
				accessibility: {
					point: {
						valueSuffix: '%'
					}
				},
				plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enable: true,
							format: '<b>{point.name}</b>: {point.percentage:.1f} %'
						},
						showInLegend: true
					}
				},
				series: [{
			                	name: "Peserta",
						colorByPoint: true,
			                	data: [{
							name: 'Selesai 100%',
							y: data.persen100,
							sliced: true,
							selected: true
						}, {
							name: 'Dibawah 100%',
							y: data.persenKurang100,
						}]
				}]
        		});
		}
	});
}
