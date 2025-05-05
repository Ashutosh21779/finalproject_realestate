$(function() {
  'use strict';

  var colors = {
    primary        : "#6571ff",
    secondary      : "#7987a1",
    success        : "#05a34a",
    info           : "#66d1d1",
    warning        : "#fbbc06",
    danger         : "#ff3366",
    light          : "#e9ecef",
    dark           : "#060c17",
    muted          : "#7987a1",
    gridBorder     : "rgba(77, 138, 240, .15)",
    bodyColor      : "#b8c3d9",
    cardBg         : "#0c1427"
  };

  var fontFamily = "'Roboto', Helvetica, sans-serif";

  // Date Picker
  if($('#dashboardDate').length) {
    flatpickr("#dashboardDate", {
      wrap: true,
      dateFormat: "d-M-Y",
      defaultDate: "today",
    });
  }
  // Date Picker - END

  // Admin Dashboard Charts

  // Users Chart (Admin)
  if($('#customersChart').length && typeof userChartData !== 'undefined') {
    var options1 = {
      chart: {
        type: "line",
        height: 60,
        sparkline: {
          enabled: !0
        }
      },
      series: [{
        name: 'Users',
        data: userChartData
      }],
      stroke: {
        width: 2,
        curve: "smooth"
      },
      markers: {
        size: 0
      },
      colors: [colors.primary],
    };
    new ApexCharts(document.querySelector("#customersChart"),options1).render();
  }

  // Properties Chart (Admin)
  if($('#ordersChart').length && typeof propertyChartData !== 'undefined') {
    var options2 = {
      chart: {
        type: "bar",
        height: 60,
        sparkline: {
          enabled: !0
        }
      },
      plotOptions: {
        bar: {
          borderRadius: 2,
          columnWidth: "60%"
        }
      },
      colors: [colors.primary],
      series: [{
        name: 'Properties',
        data: propertyChartData
      }],
    };
    new ApexCharts(document.querySelector("#ordersChart"),options2).render();
  }

  // Agents Chart (Admin)
  if($('#growthChart').length && typeof agentChartData !== 'undefined') {
    var options3 = {
      chart: {
        type: "line",
        height: 60,
        sparkline: {
          enabled: !0
        }
      },
      series: [{
        name: 'Agents',
        data: agentChartData
      }],
      stroke: {
        width: 2,
        curve: "smooth"
      },
      markers: {
        size: 0
      },
      colors: [colors.success],
    };
    new ApexCharts(document.querySelector("#growthChart"),options3).render();
  }

  // Agent Dashboard Charts

  // Properties Chart (Agent)
  if($('#agentTotalPropertyChart').length && typeof agentPropertyData !== 'undefined') {
    var optionsAgentTotalProp = {
      chart: {
        type: "line",
        height: 60,
        sparkline: {
          enabled: !0
        }
      },
      series: [{
        name: 'Properties',
        data: agentPropertyData
      }],
      stroke: {
        width: 2,
        curve: "smooth"
      },
      markers: {
        size: 0
      },
      colors: [colors.primary],
      tooltip: {
        enabled: true
      }
    };
    new ApexCharts(document.querySelector("#customersChart"),optionsAgentTotalProp).render();
  }

  // Messages Chart (Agent)
  if($('#agentMessageChart').length && typeof agentMessageData !== 'undefined') {
    var optionsAgentMsg = {
      chart: {
        type: "line",
        height: 60,
        sparkline: {
          enabled: !0
        }
      },
      series: [{
        name: 'Messages',
        data: agentMessageData
      }],
      stroke: {
        width: 2,
        curve: "smooth"
      },
      markers: {
        size: 0
      },
      colors: [colors.success],
      tooltip: {
        enabled: true
      }
    };
    new ApexCharts(document.querySelector("#ordersChart"),optionsAgentMsg).render();
  }

  // Property Status Chart (Agent)
  if($('#agentPropertyStatusChart').length && typeof propertyStatusData !== 'undefined') {
    var optionsAgentStatus = {
      chart: {
        type: "bar",
        height: 60,
        sparkline: {
          enabled: !0
        }
      },
      plotOptions: {
        bar: {
          borderRadius: 2,
          columnWidth: "60%"
        }
      },
      colors: [colors.primary, colors.success],
      series: [{
        name: 'Properties',
        data: [propertyStatusData.rent, propertyStatusData.buy]
      }],
      xaxis: {
        categories: ['Rent', 'Buy']
      },
      tooltip: {
        enabled: true,
        y: {
          formatter: function (val) {
            return val + " Properties";
          }
        }
      }
    };
    new ApexCharts(document.querySelector("#growthChart"),optionsAgentStatus).render();
  }

  // Monthly Sales Chart
  if ($('#monthlySalesChart').length && typeof monthlySalesData !== 'undefined') {
    var options = {
      chart: {
        type: 'bar',
        height: 300,
        parentHeightOffset: 0,
        foreColor: colors.bodyColor,
        background: colors.cardBg,
        toolbar: {
          show: false
        },
      },
      theme: {
        mode: 'light'
      },
      tooltip: {
        theme: 'light'
      },
      colors: [colors.primary],
      fill: {
        opacity: .9
      },
      grid: {
        padding: {
          bottom: -4
        },
        borderColor: colors.gridBorder,
        xaxis: {
          lines: {
            show: true
          }
        }
      },
      series: [{
        name: 'Properties',
        data: monthlySalesData.data
      }],
      xaxis: {
        type: 'category',
        categories: monthlySalesData.categories,
        axisBorder: {
          color: colors.gridBorder,
        },
        axisTicks: {
          color: colors.gridBorder,
        },
      },
      yaxis: {
        title: {
          text: 'Number of Properties',
          style:{
            size: 9,
            color: colors.muted
          }
        },
      },
      legend: {
        show: true,
        position: "top",
        horizontalAlign: 'center',
        fontFamily: fontFamily,
        itemMargin: {
          horizontal: 8,
          vertical: 0
        },
      },
      stroke: {
        width: 0
      },
      dataLabels: {
        enabled: true,
        style: {
          fontSize: '10px',
          fontFamily: fontFamily,
        },
        offsetY: -27
      },
      plotOptions: {
        bar: {
          columnWidth: "50%",
          borderRadius: 4,
          dataLabels: {
            position: 'top',
            orientation: 'vertical',
          }
        },
      }
    }

    var apexBarChart = new ApexCharts(document.querySelector("#monthlySalesChart"), options);
    apexBarChart.render();
  }

});
