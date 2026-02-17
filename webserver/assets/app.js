let DateTime = luxon.DateTime;
let minute = DateTime.now().toFormat('m');
let request_try = 0;
let records = 0;
let sensorId = 1;

let unit_t = getCookie('unit_t') || 'C';
$('#units_t option[value='+unit_t+']').prop('selected', true);

let unit_p = getCookie('unit_p') || 'mb';
$('#units_p option[value='+unit_p+']').prop('selected', true);

let unit_a = getCookie('unit_a') || 'm';
$('#units_a option[value='+unit_a+']').prop('selected', true);


sensorId = setSensor();
records = setRecords();
chart = configChart();
getData();
checkTask();


function setSensor(sensorId) {
  const sensorCookie = getCookie('sensor');

  if (typeof sensorId != "undefined") {
    setCookie('sensor', sensorId, 365);
  } else if (sensorCookie) {
    sensorId = sensorCookie;
  } else {
    sensorId = 1 // first added
  }

  return sensorId;
}


function setRecords(rec) {
  const recordsCookie = getCookie('records');

  if (typeof rec != "undefined") {
    setCookie('records', rec, 365);
  } else if (recordsCookie) {
    rec = recordsCookie;
  } else {
    rec = 3 // ~ 3 days
  }
  
  $('#rec option[value='+rec+']').prop('selected', true);
  return rec;
}


function configChart() {
  Chart.defaults.global.defaultFontColor = 'rgba(0, 0, 0, 0.7)';
  Chart.defaults.scale.type = 'linear';
  Chart.defaults.scale.gridLines.color = 'rgba(0, 0, 0, 0.1)';
  Chart.defaults.scale.ticks.mirror = true;
  Chart.defaults.scale.ticks.padding = -10;

  if (document.documentElement.clientWidth < 767) {
    Chart.defaults.scale.gridLines.drawBorder = false;
    Chart.defaults.scale.gridLines.display = false;
  }
  // console.log(Chart.defaults);
  
  const chart = new Chart("conditions_chart", {
    type: "line",
    data: {
      labels: [],
      datasets: [
      {
        "label": t('t.label'),
        "title": t('t.title'),
        "unit": t('t.unit.'+unit_t),
        fill: true,
        lineTension: 0.1,
        borderColor: 'rgba(255, 51, 0, 1)',
        backgroundColor: 'rgba(255, 51, 0, 0.3)',
        pointHoverBorderWidth: 3,
        borderWidth: 2,
        pointRadius: 0,
        pointHitRadius: 8,
        data: [],
        yAxisID: 'temp-y-axis',
      },
      {
        "label": t('h.label'),
        "title": t('h.title'),
        "unit": t('h.unit'),
        fill: false,
        lineTension: 0.1,
        borderColor: 'rgba(0, 92, 171, 1)',
        backgroundColor: 'rgba(0, 92, 171, 0.3)',
        borderWidth: 1,
        pointRadius: 0,
        pointHitRadius: 8,
        data: [],
        yAxisID: 'hm-y-axis',
      },
      {
        "label": t('p.label'),
        "title": t('p.title'),
        "unit": t('p.unit.'+unit_p),
        fill: false,
        lineTension: 0.1,
        borderColor: 'rgba(68, 68, 68, 1)',
        backgroundColor	: 'rgba(68, 68, 68, 0.3)',
        borderWidth: 1,
        pointRadius: 0,
        pointHitRadius: 8,
        data: [],
        yAxisID: 'pr-y-axis',
      },
      {
        "label": t('v.label'),
        "title": t('v.title'),
        "unit": t('v.unit'),
        fill: false,
        lineTension: 0.1,
        borderColor: 'rgba(51, 170, 85, 1)',
        backgroundColor	: 'rgba(51, 170, 85, 0.3)',
        borderWidth: 1,
        pointRadius: 0,
        pointHitRadius: 8,
        data: [],
        yAxisID: 'v-y-axis',
        hidden: true,
      },
      {
        "label": t('a.label'),
        "title": t('a.title'),
        "unit": t('a.unit.'+unit_a),
        fill: false,
        lineTension: 0.1,
        borderColor: 'rgba(0, 0, 119, 1)',
        backgroundColor	: 'rgba(0, 0, 119, 0.3)',
        borderWidth: 1,
        pointRadius: 0,
        pointHitRadius: 8,
        data: [],
        yAxisID: 'a-y-axis',
        hidden: true,
      },
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        display: true,
        position: 'bottom',
        align: 'center',
        labels: {
          boxWidth: 6,
          usePointStyle: true,
        }
        
      },

      scales: {
        xAxes: [
          {
            type: 'time',
            time: {
              displayFormats: {
                hour: '',
                day: 'd.MM',
              },
              tooltipFormat: 'DD T'
            },
            ticks: {
              maxRotation: 0,
              minRotation: 0,
              padding: 0,
              major: {
                enabled: true,
              },
            },
            title: {
              display: true,
              text: 'Date'
            },
            gridLines: {
              display: true,
            }
          }
        ],

        yAxes: [
          {
            id: 'temp-y-axis',
            position: 'right',
          },
          {
            id: 'hm-y-axis',
            position: 'right',
            display: false,
          },
          {
            id: 'pr-y-axis',
            position: 'right',
            display: false,
          },
          {
            id: 'v-y-axis',
            position: 'right',
            display: false,
          },
          {
            id: 'a-y-axis',
            position: 'right',
            display: false,
          }
        ],

      },

      animation: {
          duration: 1000
      },
      
      tooltips: {
        callbacks: {
          label: function (tooltipItems, data) {
            return data.datasets[tooltipItems.datasetIndex].title + ' ' + tooltipItems.yLabel + data.datasets[tooltipItems.datasetIndex].unit;
          }
        },
      },
      
      plugins: {

/*        zoom: {
          pan: {
            enabled: false,
            mode: 'x'
          },
          zoom: {
            enabled: true,
            drag: true,
            mode: 'x',
          },
        }*/

      },

    }
  });

  return chart;

}


function getData() {
  const get_request = 'data_read.php?r='+records+'&s='+sensorId;
  let jqxhr = $.ajax({
    url: get_request,
    dataType: 'json',
    timeout: 3000 //3 second timeout
  })
  
  jqxhr.done(function(data) {
    request_try = 0;
    prepare(data);
    render(data);
    listSensors(data.sensors);
  })
  
  jqxhr.fail(function() {
    request_try++;
    if (request_try < 3) {
      getData();
    } else {
      request_try = 0;
  	  if (jqxhr.status == 503)
  	    window.location.href = "setup.php";
      return false;
    }
  })
  
  .always(function() {
    // console.log( chart.data.labels );
  });
}


function prepare(data) {
  // Calculate sea level pressure according to elevation
  const elevation = 85;
  data.p0 = data.p.map(function(val) {
    return Math.round(100*(val+(elevation/9.2)))/100;
  });

  // Dewpoint via Magnus formula approximation
  const dewPointC = function(tempC, rh) {
    const a = 17.27;
    const b = 237.3;
    const f = (a * tempC) / (b + tempC) + Math.log(rh / 100);

    const dewPointC = (b * f) / (a - f);
    return dewPointC;
  };

  data.dp = Math.round(100*(dewPointC(data.t[0], data.h[0])))/100;

  
  const CtoF = function(val) {
    return Math.round(100*(val * 9/5 + 32))/100;
  }
  const CtoK = function(val) {
    return Math.round(100*(val + 273.15))/100;
  }
  
  // Switch units
  switch (unit_t) {
    case 'C':
      data.t_converted = data.t;
      data.dp_converted = data.dp;
      break;
      
    case 'F':
      data.t_converted = data.t.map(CtoF);
      data.dp_converted = CtoF(data.dp);
      break;
      
    case 'K':
      data.t_converted = data.t.map(CtoK);
      data.dp_converted = CtoK(data.dp);
      break;
  }
  
  switch (unit_p) {
    case 'mb':
      data.p_converted =  data.p0;
      break;
      
    case 'inHg':
      data.p_converted = data.p0.map(function(val) {
        return Math.round(100*(val * 0.02953))/100;
      });
      break;
      
    case 'mmHg':
      data.p_converted = data.p0.map(function(val) {
        return Math.round(100*(val * 0.750062))/100;
      });
      break;
  }
  
  switch (unit_a) {
    case 'm':
      data.a_converted =  data.a;
      break;
      
    case 'ft':
      data.a_converted = data.a.map(function(val) {
        return Math.round(100*(val * 3.280839895))/100;
      });
      break;
  }


  // Barometric trend forecast
  const pressureTrend = function(data, timeStart, timeEnd) {
    let x = [], y = [];
    let i = 0;
    
    do {
      if (data.d[i] <= timeStart) {
        x.push(data.d[i]/hour);
        y.push(data.p[i]);
      }
    } while (data.d[i++] >= timeEnd);

    const n = y.length;
    let sumX = 0, sumY = 0, sumXY = 0, sumX2 = 0;

    for (i = 0; i < n; i++) {
      sumX += x[i];
      sumY += y[i];
      sumXY += x[i] * y[i];
      sumX2 += x[i] * x[i];
    }

    const slope = (n * sumXY - sumX * sumY) / (n * sumX2 - sumX * sumX);
    const yIntercept = (sumY / n) - (slope * sumX / n);

    return { slope, yIntercept };
  }

  const hour = 3.6e6;
  const trendNow = pressureTrend(data, data.d[0], (data.d[0] - 1.5*hour));
  const trendBefore = pressureTrend(data, (data.d[0] - 1.5*hour), (data.d[0] - 3*hour));
  
  console.log(trendNow.slope);
  console.log(trendBefore.slope);
  console.log(trendNow.slope+trendBefore.slope);

  if (trendNow.slope + trendBefore.slope > 1) {
    data.pt = 'raising';
  } else if (trendNow.slope + trendBefore.slope < -2 && data.p[0] < 1013) {
    data.pt = 'drop';
  } else if (trendNow.slope + trendBefore.slope < -1) {
    data.pt = 'falling';
  } else {
    data.pt = 'steady';
  }


  // Charge level and voltage
  if (isNaN(data.v[0])) {
    data.charge = 0;
    data.voltage = 0;
    
  } else {
    data.voltage = data.v[0];

    var chargeTable = [
      [4.10, 100],
      [4.03, 90],
      [3.96, 80],
      [3.89, 70],
      [3.82, 60],
      [3.75, 50],
      [3.68, 40],
      [3.61, 30],
      [3.54, 20],
      [3.47, 10],
      [3.40, 0],
      [0, 0],
    ];
    
    for (var i = chargeTable.length-1; i >= 0; i--)
      if (data.v[0] >= chargeTable[i][0]) 
        data.charge = chargeTable[i][1];
  }


}


function render(data) {
  $('#datetime .label').text(DateTime.now().setLocale(t('locale')).toFormat('ff'));
  

  chart.data.labels = data.d;
  chart.data.datasets[0].data = data.t_converted;
  chart.data.datasets[1].data = data.h;
  chart.data.datasets[2].data = data.p_converted;
  chart.data.datasets[3].data = data.v;
  chart.data.datasets[4].data = data.a_converted;
  chart.update();
  $('#conditions_chart').show();


  let latestDate = '';
  if (data.d[0])
    latestDate = DateTime.fromMillis(data.d[0]).setLocale(t('locale')).toRelative({style: 'short'});
    
  const delay = ( DateTime.now()-data.d[0] ) / 60000;

  $('#datetime .label').text(DateTime.now().setLocale(t('locale')).toFormat('ff'));
  
  $('#temperature').text(parseFloat(data.t_converted[0]).toFixed(1)+'°');
  $('#humidity .label').text(t('h.label')+': ');
  $('#humidity .value').text(parseFloat(data.h[0]).toFixed(1)+t('h.unit'));
  $('#dewpoint .label').text(t('dp.label')+': ');
  $('#dewpoint .value').text(parseFloat(data.dp_converted).toFixed(1)+"°");
  $('#pressure .label').text(t('p.label')+': ');
  $('#pressure .value').text(parseFloat(data.p_converted[0]).toFixed(1)+t('p.unit.'+unit_p));
  $('#forecast .label').text(t('fc.label')+': ');
  $('#forecast .value').text(t('fc.trend.'+data.pt));
  $('#forecast .icon').removeClass().addClass('icon '+data.pt);
  $('#voltage .label').text(t('v.level')+': ');
  $('#voltage .value').text(data.charge+'% '+data.voltage+t('v.unit'));
  $('#updated .label').text(t('date.latest')+': ');
  $('#updated .value').text(latestDate);
  $('#setup .label').text(t('setup.title'));

  $('#records .label').text(t('rec.howmuch'));
  $('#rec option:nth-child(1)').text(t('rec.show1'));
  $('#rec option:nth-child(2)').text(t('rec.show3'));
  $('#rec option:nth-child(3)').text(t('rec.show7'));
  $('#rec option:nth-child(4)').text(t('rec.showall'));
  if (data.sensors[sensorId])
    $('title').text(data.sensors[sensorId]['name']+': '+parseFloat(data.t_converted[0]).toFixed(0)+'°');
  $('link[rel="icon"]').attr('href', 'assets/favicon/?t='+parseFloat(data.t_converted[0]).toFixed(0));


	$('#temperature').attr("title", '');
  $('#temperature').removeClass();

  if (data.charge <= 20 && data.charge > 0) {     // if 0 - assume sensor without battery
  	$('#temperature').addClass("low_battery");
  	$('#temperature').attr("title", t('nodata'));
  } 
  
  // if (delay > 10) {                   // 10min
  // if (delay > 30) {                   // 30min
  // if (delay > 180) {                   // 3h
  if (delay > 360 || isNaN(delay)) {       // 6h
  	$('#temperature').addClass("no_data");
  	$('#temperature').attr("title", t('nodata'));
  }
  
  if (data.t[0] < 0) $('#temperature').addClass("freeze");
  if (data.t[0] > 30) $('#temperature').addClass("hot");

  $('.battery1, .battery2, .battery3').addClass('hidden');
  
  [[20, '.battery1'], [40, '.battery2'], [70, '.battery3']].forEach((level) => {
    if (data.charge > level[0])
      $('#latest '+level[1]).removeClass('hidden');
  });
  
  if (data.d.length == 0) {
    $('#temperature').text("N/A");
    $('#latest .value').text('N/A');
    return;
  }

}


function checkTask() {
    const now = new Date();
    const millisecondsToNextMinute = (60 - now.getSeconds()) * 1000 - now.getMilliseconds();

    setTimeout(function() {
        // The task to perform
        getData();

        // Schedule the next execution
        checkTask();
    }, millisecondsToNextMinute);
}


function listSensors(sensors) {
  if ($('#sensors *').length > 0)
    return false;
    
  $('#sensors').empty();
  for (const [id, sensor] of Object.entries(sensors)) {
    $('#sensors').append('<option value="'+id+'">'+sensor.name+'</option>');
  }
  $('#sensors option[value='+sensorId+']').prop('selected', true);
}


function fullRender() {
  chart.destroy();
  chart = configChart();
  getData();
}


$('#sensors').on('change', function() {
  sensorId = $(this).find('option:selected').val();
  setSensor(sensorId);
  fullRender();
});

$('#rec').on('change', function() {
  var rec = $(this).find('option:selected').val();
  records = setRecords(rec);
  fullRender();
});

$('#units_t').on('change', function() {
  unit_t = $(this).find('option:selected').val();
  switch (unit_t) {
    case 'F':
      unit_p = 'inHg';
      unit_a = 'ft';
      break;
    default:
      unit_p = 'mb';
      unit_a = 'm';
      break;
  }
  setCookie('unit_t', unit_t, 365);
  setCookie('unit_p', unit_p, 365);
  setCookie('unit_a', unit_a, 365);
  fullRender();
});

/*
$('#units_t').on('change', function() {
  unit_t = $(this).find('option:selected').val();
  setCookie('unit_t', unit_t, 365);
  fullRender();
});

$('#units_p').on('change', function() {
  unit_p = $(this).find('option:selected').val();
  setCookie('unit_p', unit_p, 365);
  fullRender();
});

$('#units_a').on('change', function() {
  unit_a = $(this).find('option:selected').val();
  setCookie('unit_a', unit_a, 365);
  fullRender();
});
*/

// Switch axes
document.getElementById("conditions_chart").onclick = function(evt) {

  var activePoints = chart.getElementAtEvent(evt);
  if (activePoints.length > 0) {
    var datasetIndex = activePoints[0]._datasetIndex;

    for (i = 0; i < chart.data.datasets.length; i++) {
      chart.options.scales.yAxes[i]['display'] = false;
      chart.data.datasets[i]['fill'] = false;
    }

    chart.options.scales.yAxes[datasetIndex]['display'] = true;
    chart.data.datasets[datasetIndex]['fill'] = true;
    chart.update(); 

  }
};

