// 1. Init common functions on document ready

$(function() {
  "use strict";

  // page onload functions
  echarts_pie.init();
  echarts_table_pie.init();
  echarts_bar.init();
});

echarts_table_pie = {
  init: function() {
    var $elem = $("[data-echarts-table],.data-echarts-table");
    if ($elem.length) {
      $elem.each(function() {
        var $this = this,
          this_title = $($this).data('title'),
          this_data = $($this).data('values');
        var $myChart = echarts.init($this);
        option = {
          title: {
            text: this_title,
            textStyle: {
              color: '#444'
            }
          },
          tooltip: {
            trigger: 'item',
            formatter: "{c} : {d}%"
          },
          toolbox: {
            show: true,
            feature: {
              saveAsImage: {
                title: ' ',
                show: true
              }
            }
          },
          legend: {
            orient: 'vertical',
            x: 'left',
            y: 'bottom',
            data: jQuery.map(this_data, function(n, i) {
              return (n.name);
            })
          },
          series: [{
            name: this_title,
            type: 'pie',
            radius: [10, 50],
            center: ['50%', '50%'],
            data: this_data,
            label: {
              normal: {
                formatter: '{b}',
                position: 'inside'
              }
            },
            labelLine: {
              normal: {
                show: false
              }
            },
            itemStyle: {
              emphasis: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: 'rgba(0, 0, 0, 0.5)'
              }
            },
            animationType: 'scale',
            animationEasing: 'elasticOut',
          }]
        };
        $myChart.setOption(option);
      });
    }
  }
}



echarts_bar = {
    init: function() {
      var $elem = $("[data-echarts-bar],.data-echarts-bar");
      if ($elem.length) {
        $elem.each(function() {
            var $this = this,
              this_title = $($this).data('title'),
              this_data = $($this).data('values');
            var $myChart = echarts.init($this);
            option = {
              color: ['#e53935','#3398DB'],
              tooltip: {
                trigger: 'axis',
                axisPointer: { // 坐标轴指示器，坐标轴触发有效
                  type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
                }
              },
              grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
              },
              xAxis: [{
                type: 'category',
                data: jQuery.map(this_data, function(n, i) {
                  return (n.month);
                }),
                axisTick: {
                  alignWithLabel: true
                }
              }],
              yAxis: [{
                type: 'value'
              }],
              series: [{
                name: "{{'paymenttype.detail.credit'|trans }}",
                type: 'bar',
                data: jQuery.map(this_data, function(n, i) {
                  return (n.credit);
                })
              },
              {
                name: this_title,
                type: 'bar',
                data: jQuery.map(this_data, function(n, i) {
                  return (n.price);
                })
              }
            ]
            };
            $myChart.setOption(option);
          });

        }
      }
    }
    echarts_pie = {
      init: function() {
        var $elem = $("[data-echarts-pie],.data-echarts-pie");
        if ($elem.length) {
          $elem.each(function() {
            var $this = this,
              this_title = $($this).data('title'),
              this_data = $($this).data('values');
            var $myChart = echarts.init($this);
            option = {
              color: ['#39f' ,  '#00bfa5' , '#80d8ff' , '#00b8d4'],
              title: {
                text: this_title,
                left: 'center',
                top: 20,
                textStyle: {
                  color: '#444'
                }
              },
              legend: {
                x: 'center',
                y: 'bottom',
                data: jQuery.map(this_data, function(n, i) {
                  return (n.name);
                })
              },
              toolbox: {
                show: true,
                feature: {
                  saveAsImage: {
                    title: ' ',
                    show: true
                  }
                }
              },
              tooltip: {
                trigger: 'item',
                formatter: "{b} : {d}%"
              },
              series: [{
                name: this_title,
                data: this_data,
                type: 'pie',
                radius: [20, 100],
                center: ['50%', '50%'],
                roseType: 'radius',
                label: {
                  normal: {
                    textStyle: {
                      color: 'rgb(68, 68, 68)'
                    }
                  }
                },
                labelLine: {
                  normal: {
                    lineStyle: {
                      color: 'rgb(68, 68, 68)'
                    },
                    smooth: 0,
                    length: 30,
                    length2: 30
                  }
                },
                itemStyle: {
                  normal: {
                    shadowColor: 'rgba(0, 0, 0, 0.6)'
                  }
                },
                animationType: 'scale',
                animationEasing: 'elasticOut',
                animationDelay: function(idx) {
                  return Math.random() * 200;
                }
              }]
            };
            $myChart.setOption(option);
          });
        }
      }
    };
