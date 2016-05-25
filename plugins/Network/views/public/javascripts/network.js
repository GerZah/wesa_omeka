jQuery(document).ready(function () {
    var $ = jQuery;

    graphFunction();

    function graphFunction() {

      $.ajax({

       url: networkDataUrl,
       method: 'GET',
       dataType: 'json',
       data: {},
       success: function (data) {

         $('#cy').cytoscape({
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
             nodes: data.nodes,
             edges: data.edges
         },



    window.cy = this;

    cy.elements().unselectify();

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
}
});
}
});
