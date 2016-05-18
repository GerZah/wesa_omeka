$(function(){

  var cy = window.cy = cytoscape({
    container: document.getElementById('cy'),

      layout: {
      name: 'cose',
      idealEdgeLength: 100,
      nodeOverlap: 20
    },

    style: cytoscape.stylesheet()
  .selector('node')
    .css({
      'content': 'data(name)',
      'text-valign': 'center',
      'color': 'white',
      'text-outline-width': 2,
      'text-outline-color': '#888'
    })
  .selector('edge')
    .css({
      'target-arrow-shape': 'triangle'
    })
  .selector(':selected')
    .css({
      'background-color': 'black',
      'line-color': 'black',
      'target-arrow-color': 'black',
      'source-arrow-color': 'black'
    })
  .selector('.faded')
    .css({
      'opacity': 0.25,
      'text-opacity': 0
    }),

elements: {
  nodes: [
    { data: { id: 'j', name: 'Githa' } },
    { data: { id: 'e', name: 'Elaine' } },
    { data: { id: 'k', name: 'Kramer' } },
    { data: { id: 'g', name: 'George' } }
  ],
  edges: [
    { data: { source: 'j', target: 'e' } },
    { data: { source: 'j', target: 'k' } },
    { data: { source: 'j', target: 'g' } },
    { data: { source: 'e', target: 'j' } },
    { data: { source: 'e', target: 'k' } },
    { data: { source: 'k', target: 'j' } },
    { data: { source: 'k', target: 'e' } },
    { data: { source: 'k', target: 'g' } },
    { data: { source: 'g', target: 'j' } }
  ]
},


  });
});
