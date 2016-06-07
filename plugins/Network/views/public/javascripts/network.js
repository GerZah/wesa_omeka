jQuery(document).ready(function () {
    var $ = jQuery;

    // http://jsbin.com/gist/621d51ea7de19608127e?js,output
    var cy = null;

    initGraph();

    function initGraph() {

      var cytoLayout = {};
      switch (cytoGraphStructure) {
        case 1:
            cytoLayout = {
              name: 'spread',
              minDist: 200
            };
          break;
        default:
          cytoLayout = {
            name: 'grid',
            padding: 10
          };
      }

      cy = cytoscape({
        container: document.querySelector('#cy'),

        boxSelectionEnabled: false,
        autounselectify: true,

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
              'curve-style': 'bezier',
              'target-arrow-shape': 'triangle',
              'label': 'data(label)',
              'edge-text-rotation': 'autorotate',
              'text-opacity': 0.25
            })
          .selector(':selected')
            .css({
              'background-color': 'black',
              'line-color': 'black',
              'target-arrow-color': 'black',
              'source-arrow-color': 'black'
            })
          .selector(".foo")
            .css({
              'background-color': 'red',
              'text-outline-color': 'red',
            })
          .selector('.faded')
            .css({
              'opacity': 0.25,
              'text-opacity': 0
            }),

        elements: {
          nodes: nodeData,
          edges: edgeData
        },

        layout: cytoLayout
      });

      $(nodeData).each(function(){
        var id = this.data.id;
        var name = this.data.name;
        var type = this.data.type;
        var itemUrl = cytoBaseUrl + "/items/show/" + this.data.id;
        cy.$("#"+id).qtip({
          content: "<a href='"+itemUrl+"' target='_blank'>" + name +  "</a>",
            position: {
              my: 'top center',
              at: 'bottom center'
            },
            style: {
              classes: 'qtip-bootstrap',
              tip: {
                width: 16,
                height: 8
              }
            }
        });
        if (type>0) {
          cy.$("#"+id).addClass("foo");
        }
      });

      cy.on('tap', 'node', function(e){
        var node = e.cyTarget;
        var neighborhood = node.neighborhood().add(node);

        cy.elements().addClass('faded');
        neighborhood.removeClass('faded');
      });

      cy.on('tap', function(e){
        if( e.cyTarget === cy ){
          cy.elements().removeClass('faded');
        }
      });
    }

});
