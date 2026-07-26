'use strict';
function generateChartLines(year) {
	$(".current-year").html('Année ' + year);
	$(".selected-current-year").text(year)
	$("#sales_chart").html('');
	var order = 'year=' + year;
		$.post("components/com_dashboard/controleurs/router.php?task=getChiffreYear", order, function (theResponse) {
			var data_reponse = theResponse.split('__--__--__');
			console.log(data_reponse[1])
	
			var payment1 = data_reponse[3].split(',');
			var payment2 = data_reponse[4].split(',');
			var payment3 = data_reponse[5].split(',');
			var payment4 = data_reponse[6].split(',');
			var payment5 = data_reponse[7].split(',');
			var charge1 = data_reponse[8].split(',');
	
			var facture1 = parseInt(data_reponse[9]);
			var facture2 = parseInt(data_reponse[10]);
			var facture3 = parseInt(data_reponse[11]);
	
			// regénération du graphe Paiement par devis par mois
			var columnCtx1 = document.getElementById("sales_chart"),
			columnConfig1 = {
				colors: ['#7638ff', '#22cc62', '#ffbc34', '#009efb', '#6c757d', '#ef3737'],
				series: [
					{
						name: "Encaissements MAD",
						type: "column",
						data: payment1
					},
					{
						name: "Encaissements EUR",
						type: "column",
						data: payment2
					},
					{
						name: "Encaissements Pound",
						type: "column",
						data: payment3
					},
					{
						name: "Encaissements Dollar",
						type: "column",
						data: payment4
					},
					{
						name: "Encaissements Aed",
						type: "column",
						data: payment5
					},
					{
						name: "Charges",
						type: "column",
						data: charge1
					}
				],
				chart: {
					type: 'bar',
					fontFamily: 'Poppins, sans-serif',
					height: 350,
					toolbar: {
						show: false
					}
				},
				plotOptions: {
					bar: {
						horizontal: false,
						columnWidth: '60%',
						endingShape: 'rounded'
					},
				},
				dataLabels: {
					enabled: false
				},
				stroke: {
					show: true,
					width: 2,
					colors: ['transparent']
				},
				xaxis: {
					categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
				},
				yaxis: {
					title: {
						text: 'Encaissements / Charges'
					}
				},
				fill: {
					opacity: 1
				},
				tooltip: {
					y: {
						formatter: function (val) {
							return val
						}
					}
				}
			};
			var columnChart2 = new ApexCharts(columnCtx1, columnConfig1);
			columnChart2.render();
		
			// regénération du graphe factures
			$("#invoice_chart").html('');
			
			var pieCtx1 = document.getElementById("invoice_chart"),
				pieConfig1 = {
					colors: ['#22cc62', '#ef3737', '#ffb800'],
					series: [facture1, facture2, facture3],
					chart: {
						fontFamily: 'Poppins, sans-serif',
						height: 350,
						type: 'donut',
					},
					labels: ['Payée', 'Impayée', 'Payée Partialement'],
					legend: { show: false },
					responsive: [{
						breakpoint: 480,
						options: {
							chart: {
								width: 200
							},
							legend: {
								position: 'bottom'
							}
						}
					}]
				};
			var pieChart2 = new ApexCharts(pieCtx1, pieConfig1);
			pieChart2.render();
	
			$(".global-stats").html(data_reponse[0]);
			$(".ca-box").html(data_reponse[1]);
			$(".invoice-box").html(data_reponse[2]);
	
		});
}

function generateChartCercle(year) {
	$(".current-year").html('Année ' + year);
	$(".selected-current-year").text(year);
	
	let formatter = new Intl.NumberFormat('fr-FR');

	var order = 'year=' + year;
		$.post("components/com_dashboard/controleurs/router.php?task=getCAperCompany", order, function (theResponse) {
			var data_reponse = theResponse.split('__--__--__');

			var ca_verse_year = parseInt(data_reponse[0]);
			var ca_hwlabel_year = parseInt(data_reponse[1]);
			var ca_hello_dubai_year = parseInt(data_reponse[2]);
			
			var charge_verse_year = parseInt(data_reponse[3]);
			var charge_hwlabel_year = parseInt(data_reponse[4]);
			var charge_hello_dubai_year = parseInt(data_reponse[5]);

			// CA par société
        	$("#ca_per_company_chart").html('');
        	
        	var pieCtx3 = document.getElementById("ca_per_company_chart"),
        		pieConfig3 = {
        			colors: ['#22cc62', '#ef3737', '#ffb800'],
        			series: [ca_verse_year, ca_hwlabel_year, ca_hello_dubai_year],
        			chart: {
        				fontFamily: 'Poppins, sans-serif',
        				height: 350,
        				type: 'donut',
        			},
        			labels: ['VERSE CONCEPT', 'HW LABEL', 'HELLO WORLD DUBAI'],
        			legend: { show: false },
        			responsive: [{
        				breakpoint: 480,
        				options: {
        					chart: {
        						width: 200
        					},
        					legend: {
        						position: 'bottom'
        					}
        				}
        			}]
        		};
        	var pieChart3 = new ApexCharts(pieCtx3, pieConfig3);
        	pieChart3.render();
        	
        	$(".ca_hwlabel").html(formatter.format(ca_hwlabel_year) + ' DH');
        	$(".ca_verse").html(formatter.format(ca_verse_year) + ' DH');
        	$(".ca_hwdubai").html(formatter.format(ca_hello_dubai_year) + ' DH');
        	$(".total-ca").html(formatter.format(ca_hwlabel_year + ca_verse_year + ca_hello_dubai_year) + ' DH');
        	
        	// Charges par société
        	$("#charge_per_company_chart").html('');
        	
        	var pieCtx4 = document.getElementById("charge_per_company_chart"),
        		pieConfig4 = {
        			colors: ['#22cc62', '#ef3737', '#ffb800'],
        			series: [charge_verse_year, charge_hwlabel_year, charge_hello_dubai_year],
        			chart: {
        				fontFamily: 'Poppins, sans-serif',
        				height: 350,
        				type: 'donut',
        			},
        			labels: ['VERSE CONCEPT', 'HW LABEL', 'HELLO WORLD DUBAI'],
        			legend: { show: false },
        			responsive: [{
        				breakpoint: 480,
        				options: {
        					chart: {
        						width: 200
        					},
        					legend: {
        						position: 'bottom'
        					}
        				}
        			}]
        		};
        	var pieChart4 = new ApexCharts(pieCtx4, pieConfig4);
        	pieChart4.render();
        	
        	$(".charge_hwlabel").html(formatter.format(charge_hwlabel_year) + ' DH');
        	$(".charge_verse").html(formatter.format(charge_verse_year) + ' DH');
        	$(".charge_hwdubai").html(formatter.format(charge_hello_dubai_year) + ' DH');
        	$(".total-charge").html(formatter.format(charge_hwlabel_year + charge_verse_year + charge_hello_dubai_year) + ' DH');
	
		});
}

$(document).ready(function () {
    
    if(STATS_GLOBAL){
        // CA par société
    	$("#ca_per_company_chart").html('');
    	
    	var pieCtx3 = document.getElementById("ca_per_company_chart"),
    		pieConfig3 = {
    			colors: ['#22cc62', '#ef3737', '#ffb800'],
    			series: [ca_verse, ca_hwlabel, ca_hello_dubai],
    			chart: {
    				fontFamily: 'Poppins, sans-serif',
    				height: 350,
    				type: 'donut',
    			},
    			labels: ['VERSE CONCEPT', 'HW LABEL', 'HELLO WORLD DUBAI'],
    			legend: { show: false },
    			responsive: [{
    				breakpoint: 480,
    				options: {
    					chart: {
    						width: 200
    					},
    					legend: {
    						position: 'bottom'
    					}
    				}
    			}]
    		};
    	var pieChart3 = new ApexCharts(pieCtx3, pieConfig3);
    	pieChart3.render();
    	
    	
    	// Charges par société
    	$("#charge_per_company_chart").html('');
    	
    	var pieCtx4 = document.getElementById("charge_per_company_chart"),
    		pieConfig4 = {
    			colors: ['#22cc62', '#ef3737', '#ffb800'],
    			series: [charge_verse, charge_hwlabel, charge_hello_dubai],
    			chart: {
    				fontFamily: 'Poppins, sans-serif',
    				height: 350,
    				type: 'donut',
    			},
    			labels: ['VERSE CONCEPT', 'HW LABEL', 'HELLO WORLD DUBAI'],
    			legend: { show: false },
    			responsive: [{
    				breakpoint: 480,
    				options: {
    					chart: {
    						width: 200
    					},
    					legend: {
    						position: 'bottom'
    					}
    				}
    			}]
    		};
    	var pieChart4 = new ApexCharts(pieCtx4, pieConfig4);
    	pieChart4.render();
    	
    	
    	// Switch Data per Year (Global)
    	$(".switch-year-global a").click(function () {
    		var year = $(this).attr("data-year");
    		generateChartCercle(year);		
    	})
    	
    }else{
        
    	let is_superadmin = $("meta[name='spradm']").attr("content");
    	function generateData(baseval, count, yrange) {
    		var i = 0;
    		var series = [];
    		while (i < count) {
    			var x = Math.floor(Math.random() * (750 - 1 + 1)) + 1;;
    			var y = Math.floor(Math.random() * (yrange.max - yrange.min + 1)) + yrange.min;
    			var z = Math.floor(Math.random() * (75 - 15 + 1)) + 15;
    
    			series.push([x, y, z]);
    			baseval += 86400000;
    			i++;
    		}
    		return series;
    	}
    
    	// Switch Data per Year
    	$(".switch-year a").click(function () {
    		var year = $(this).attr("data-year");
    		generateChartLines(year);		
    	})
    
    	generateChartLines($(".switch-year a").attr("data-year"));
	}
	
	// envoi du formulaire filtre stats par date en ajax
	$('form#statsForm').ajaxForm({
		beforeSubmit: function() {
			$("#statsForm .loading").css('display', 'inline-block');
		},
		success: function(theResponse) {
			$("#statsForm .loading").fadeOut();
			var data_reponse = theResponse.split('__--__--__');
			
			$(".stats-result").html(data_reponse[0]);
			
			let formatter = new Intl.NumberFormat('fr-FR');
			
            // Mise à jour des graphs
			var ca_verse_year = parseInt(data_reponse[1]);
			var ca_hwlabel_year = parseInt(data_reponse[2]);
			var ca_hello_dubai_year = parseInt(data_reponse[3]);
			
			var charge_verse_year = parseInt(data_reponse[4]);
			var charge_hwlabel_year = parseInt(data_reponse[5]);
			var charge_hello_dubai_year = parseInt(data_reponse[6]);

			// CA par société
        	$("#ca_per_company_chart").html('');
        	
        	var pieCtx3 = document.getElementById("ca_per_company_chart"),
        		pieConfig3 = {
        			colors: ['#22cc62', '#ef3737', '#ffb800'],
        			series: [ca_verse_year, ca_hwlabel_year, ca_hello_dubai_year],
        			chart: {
        				fontFamily: 'Poppins, sans-serif',
        				height: 350,
        				type: 'donut',
        			},
        			labels: ['VERSE CONCEPT', 'HW LABEL', 'HELLO WORLD DUBAI'],
        			legend: { show: false },
        			responsive: [{
        				breakpoint: 480,
        				options: {
        					chart: {
        						width: 200
        					},
        					legend: {
        						position: 'bottom'
        					}
        				}
        			}]
        		};
        	var pieChart3 = new ApexCharts(pieCtx3, pieConfig3);
        	pieChart3.render();
        	
        	$(".ca_hwlabel").html(formatter.format(ca_hwlabel_year) + ' DH');
        	$(".ca_verse").html(formatter.format(ca_verse_year) + ' DH');
        	$(".ca_hwdubai").html(formatter.format(ca_hello_dubai_year) + ' DH');
        	$(".total-ca").html(formatter.format(ca_hwlabel_year + ca_verse_year + ca_hello_dubai_year) + ' DH');
			
			
			
			// Charges par société
        	$("#charge_per_company_chart").html('');
        	
        	var pieCtx4 = document.getElementById("charge_per_company_chart"),
        		pieConfig4 = {
        			colors: ['#22cc62', '#ef3737', '#ffb800'],
        			series: [charge_verse_year, charge_hwlabel_year, charge_hello_dubai_year],
        			chart: {
        				fontFamily: 'Poppins, sans-serif',
        				height: 350,
        				type: 'donut',
        			},
        			labels: ['VERSE CONCEPT', 'HW LABEL', 'HELLO WORLD DUBAI'],
        			legend: { show: false },
        			responsive: [{
        				breakpoint: 480,
        				options: {
        					chart: {
        						width: 200
        					},
        					legend: {
        						position: 'bottom'
        					}
        				}
        			}]
        		};
        	var pieChart4 = new ApexCharts(pieCtx4, pieConfig4);
        	pieChart4.render();
        	
        	$(".charge_hwlabel").html(formatter.format(charge_hwlabel_year) + ' DH');
        	$(".charge_verse").html(formatter.format(charge_verse_year) + ' DH');
        	$(".charge_hwdubai").html(formatter.format(charge_hello_dubai_year) + ' DH');
        	$(".total-charge").html(formatter.format(charge_hwlabel_year + charge_verse_year + charge_hello_dubai_year) + ' DH');
		}
	});

});