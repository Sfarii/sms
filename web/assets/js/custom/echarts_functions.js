// 1. Init common functions on document ready

$(function() {
    "use strict";

    // page onload functions
    echarts_pie.init();
    echarts_table_pie.init();
    
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
                      data: jQuery.map( this_data, function( n, i ) {return ( n.name );})
                  },
                  series: [{
                      name: this_title,
                      type:'pie',
                      radius : [10, 50],
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
                    title: {
                        text: this_title,
                        left: 'center',
                        top: 20,
                        textStyle: {
                            color: '#444'
                        }
                    },
                    legend: {
                        x : 'center',
                        y : 'bottom',
                        data: jQuery.map( this_data, function( n, i ) {return ( n.name );})
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
                        type:'pie',
                        radius : [20, 100],
                        center : ['50%', '50%'],
                        roseType : 'radius',
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
            animationDelay: function (idx) {
                return Math.random() * 200;
            }
                    }]
                };
                $myChart.setOption(option);
            });
        }
    }
};
