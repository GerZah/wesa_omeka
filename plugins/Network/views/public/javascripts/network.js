jQuery(document).bind("omeka:elementformload", function() {
  var $ = jQuery;

      $.ajax({

       url: networkDataUrl,
       method: 'GET',
       dataType: 'json',
       data: {},  //probably you have some parameters
       success: function (data) {

         $('#cy').cytoscape({
         style: // ...

         elements: {
             nodes: data.nodes,
             edges: data.edges
         },

         ready: function(){
             //...
         }
     });

 }

});

});
