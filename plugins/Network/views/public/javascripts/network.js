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
              'background-color': '#888', 'text-outline-color': '#888',
            })
          .selector('edge')
            .css({
              'curve-style': 'bezier',
              'target-arrow-shape': 'triangle',
              'label': 'data(label)',
              'edge-text-rotation': 'autorotate',
              'color': "#888",
              'line-color': "#ccc",
              'target-arrow-color': "#ccc",
            })
          // .selector(':selected')
          //   .css({
          //     'background-color': 'black',
          //     'line-color': 'black',
          //     'target-arrow-color': 'black',
          //     'source-arrow-color': 'black'
          //   })
          .selector(".color0").css({ 'background-color': 'black', 'text-outline-color': 'black' })
          .selector(".color1").css({ 'background-color': 'crimson', 'text-outline-color': 'crimson' })
          .selector(".color2").css({ 'background-color': 'darkblue', 'text-outline-color': 'darkblue' })
          .selector(".color3").css({ 'background-color': 'green', 'text-outline-color': 'green' })
          .selector(".color4").css({ 'background-color': 'gold', 'text-outline-color': 'gold' })
          .selector(".color5").css({ 'background-color': 'coral', 'text-outline-color': 'coral' })
          .selector(".color6").css({ 'background-color': 'darkcyan', 'text-outline-color': 'darkcyan' })
          .selector(".color7").css({ 'background-color': 'maroon', 'text-outline-color': 'maroon' })
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
        if (!name) { return; }
        var color = this.data.color;
        var itemUrl = cytoBaseUrl + "/items/show/" + this.data.id;
        var withUrl = ( (this.data.public) || (seesNonPublic) );
        if ( (!withUrl) && (nonPublicItems==1) ) {
          withUrl = true;
          itemUrl = omekaBaseUrl;
        }
        var curContent = ( withUrl
          ? "<a href='"+itemUrl+"' target='_blank'>" + name +  "</a>"
          : name
        );
        console.log(this.data, withUrl);
        cy.$("#"+id).qtip({
          content: curContent,
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
        cy.$("#"+id).addClass("color"+color);
      });

      var stickySelection = stickyNodeSelection;
      var initialFade = true;

      cy.on('tap', 'node', function(e){
        var node = e.cyTarget;
        var neighborhood = node.neighborhood().add(node);

        if ( (initialFade) || (!stickySelection) ) {
          cy.elements().addClass('faded');
          initialFade = false;
        }
        neighborhood.removeClass('faded');
      });

      cy.on('tap', function(e){
        if( e.cyTarget === cy ){
          cy.elements().removeClass('faded');
          initialFade = true;
        }
      });
    }

});
