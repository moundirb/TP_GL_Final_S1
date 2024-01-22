// JS 
  
var palette = ['#fE4C14', '#4285F4']; 
  
var points = [ 
  { 
    name: 'Start', 
    id: 'Start', 
    attributes: { 
      type: 'red', 
      earlyStart: 0, 
      length: 0, 
      earlyFinish: 0, 
      lateStart: 0, 
      slack: 0, 
      lateFinish: 0 
    } 
  }, 
  { 
    name: 'B', 
    id: 'B', 
    parent: 'Start', 
    attributes: { 
      type: 'blue', 
      earlyStart: 0, 
      length: 5.33, 
      earlyFinish: 5.33, 
      lateStart: 3.84, 
      slack: 3.84, 
      lateFinish: 9.17 
    } 
  }, 
  { 
    name: 'A', 
    id: 'A', 
    parent: 'Start', 
    attributes: { 
      type: 'red', 
      earlyStart: 0, 
      length: 4, 
      earlyFinish: 4, 
      lateStart: 0, 
      slack: 0, 
      lateFinish: 4 
    } 
  }, 
  { 
    name: 'C', 
    id: 'C', 
    parent: 'A', 
    attributes: { 
      type: 'red', 
      earlyStart: 4, 
      length: 5.17, 
      earlyFinish: 9.17, 
      lateStart: 4, 
      slack: 0, 
      lateFinish: 9.17 
    } 
  }, 
  { 
    name: 'D', 
    id: 'D', 
    parent: 'A', 
    attributes: { 
      type: 'blue', 
      earlyStart: 4, 
      length: 6.33, 
      earlyFinish: 10.33, 
      lateStart: 8.68, 
      slack: 4.68, 
      lateFinish: 15.01 
    } 
  }, 
  { 
    name: 'E', 
    id: 'E', 
    parent: 'B,C', 
    attributes: { 
      type: 'red', 
      earlyStart: 9.17, 
      length: 5.17, 
      earlyFinish: 14.34, 
      lateStart: 9.17, 
      slack: 0, 
      lateFinish: 14.34 
    } 
  }, 
  { 
    name: 'F', 
    id: 'F', 
    parent: 'D', 
    attributes: { 
      type: 'blue', 
      earlyStart: 10.33, 
      length: 4.5, 
      earlyFinish: 14.83, 
      lateStart: 15.01, 
      slack: 4.68, 
      lateFinish: 19.51 
    } 
  }, 
  { 
    name: 'G', 
    id: 'G', 
    parent: 'E', 
    attributes: { 
      type: 'red', 
      earlyStart: 14.34, 
      length: 5.17, 
      earlyFinish: 19.51, 
      lateStart: 14.34, 
      slack: 0, 
      lateFinish: 19.51 
    } 
  }, 
  { 
    name: 'Finish', 
    id: 'Finish', 
    parent: 'F,G', 
    attributes: { 
      type: 'red', 
      earlyStart: 19.51, 
      length: 0, 
      earlyFinish: 19.51, 
      lateStart: 19.51, 
      slack: 0, 
      lateFinish: 19.51 
    } 
  } 
]; 
  
var legendData = { 
  name: 'Activity Name', 
  earlyStart: 'Early Start', 
  length: 'Length', 
  earlyFinish: 'Early Finish', 
  lateStart: 'Late Start', 
  slack: 'Slack', 
  lateFinish: 'Late Finish'
}; 
  
// Group points by unique roles 
var pointsByRoles = JSC.nest() 
  .key('attributes.type') 
  .entries(points); 
  
// Apply a color from palette to each group of points 
pointsByRoles.forEach(function(group, i) { 
  group.values.forEach(function(point) { 
    JSC.merge(point, { 
      outline_color: palette[i], 
      fill: [palette[i], 0.2], 
      connectorLine_color: palette[i] 
    }); 
  }); 
}); 
// Render the chart 
var chart = JSC.chart('chartDiv1', { 
  debug: true, 
  type: 'organizational right', 
  defaultTooltip_enabled: false, 
  annotations: [ 
    { 
      label: { 
        text: makeLegendText(legendData), 
        align: 'center', 
        verticalAlign: 'middle'
      }, 
      position: 'bottom right', 
      padding: [5, 0], 
      outline_color: 'gray'
    } 
  ], 
  defaultSeries: { 
    legendEntry_visible: false, 
    pointSelection: false, 
    defaultPoint: { 
      outline_width: 1, 
      /* Default line styling for connector lines */
      connectorLine: { 
        color: '#b6b6b6', 
        width: 1, 
        caps_end: { 
          type: 'arrow', 
          size: 6, 
          concavity: -0.01 
        } 
      }, 
      label: { 
        text: makeAnnotationsText(), 
        color: '#424242'
      }, 
      annotation: { 
        padding: [5, 0], 
        margin: [0, 16] 
      } 
    } 
  }, 
  series: [{ points: points }] 
}); 
  
function makeAnnotationsText() { 
  return ( 
    wrapText('%earlyStart') + 
    wrapText('%length') + 
    wrapText('%earlyFinish') + 
    '<hr>' + 
    '%name<hr>' + 
    wrapText('%lateStart') + 
    wrapText('%slack') + 
    wrapText('%lateFinish') 
  ); 
  
  function wrapText(text) { 
    return ( 
      '<span style="width:40px;align:center;">' + 
      text + 
      '</span>'
    ); 
  } 
} 
  
function makeLegendText(data) { 
  return ( 
    wrapText(data.earlyStart) + 
    wrapText(data.length) + 
    wrapText(data.earlyFinish) + 
    '<hr>' + 
    data.name + 
    '<hr>' + 
    wrapText(data.lateStart) + 
    wrapText(data.slack) + 
    wrapText(data.lateFinish) 
  ); 
  
  function wrapText(text) { 
    return ( 
      '<span style="width:70px;align:center;">' + 
      text + 
      '</span>'
    ); 
  } 
} 