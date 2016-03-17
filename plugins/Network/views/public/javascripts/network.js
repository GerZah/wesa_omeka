$(function(){

  var cy = window.cy = cytoscape({
    container: document.getElementById('cy'),

    layout: {
      name: 'cose',
      idealEdgeLength: 100,
      nodeOverlap: 20
    },

    style: [
      {"selector":"core","style":{"selection-box-color":"#AAD8FF","selection-box-border-color":"#8BB0D0","selection-box-opacity":"0.5"}},
      {"selector":"node","style":{"width":"mapData(score, 0, 0.006769776522008331, 20, 60)","height":"mapData(score, 0, 0.006769776522008331, 20, 60)","content":"data(name)","font-size":"12px","text-valign":"center","text-halign":"center","background-color":"#555","text-outline-color":"#555","text-outline-width":"2px","color":"#fff","overlay-padding":"6px","z-index":"10"}},
      {"selector":"node[?attr]","style":{"shape":"rectangle","background-color":"#aaa","text-outline-color":"#aaa","width":"16px","height":"16px","font-size":"6px","z-index":"1"}},
      {"selector":"node[?query]","style":{"background-clip":"none","background-fit":"contain"}},
      {"selector":"node:selected","style":{"border-width":"6px","border-color":"#AAD8FF","border-opacity":"0.5","background-color":"#77828C","text-outline-color":"#77828C"}},
      {"selector":"edge","style":{"curve-style":"haystack","haystack-radius":"0.5","opacity":"0.4","line-color":"#bbb","width":"mapData(weight, 0, 1, 1, 8)","overlay-padding":"3px"}},{"selector":"node.unhighlighted","style":{"opacity":"0.2"}},{"selector":"edge.unhighlighted","style":{"opacity":"0.05"}},
      {"selector":".highlighted","style":{"z-index":"999999"}},
      {"selector":"node.highlighted","style":{"border-width":"6px","border-color":"#AAD8FF","border-opacity":"0.5","background-color":"#394855","text-outline-color":"#394855","shadow-blur":"12px","shadow-color":"#000","shadow-opacity":"0.8","shadow-offset-x":"0px","shadow-offset-y":"4px"}},
      {"selector":"edge.filtered","style":{"opacity":"0"}},
      {"selector":"edge[group=\"coexp\"]","style":{"line-color":"#d0b7d5"}},
      {"selector":"edge[group=\"coloc\"]","style":{"line-color":"#a0b3dc"}},
      {"selector":"edge[group=\"gi\"]","style":{"line-color":"#90e190"}},
      {"selector":"edge[group=\"path\"]","style":{"line-color":"#9bd8de"}},
      {"selector":"edge[group=\"pi\"]","style":{"line-color":"#eaa2a2"}},
      {"selector":"edge[group=\"predict\"]","style":{"line-color":"#f6c384"}},
      {"selector":"edge[group=\"spd\"]","style":{"line-color":"#dad4a2"}},
      {"selector":"edge[group=\"spd_attr\"]","style":{"line-color":"#D0D0D0"}},
      {"selector":"edge[group=\"reg\"]","style":{"line-color":"#D0D0D0"}},
      {"selector":"edge[group=\"reg_attr\"]","style":{"line-color":"#D0D0D0"}},
      {"selector":"edge[group=\"user\"]","style":{"line-color":"#f0ec86"}}
    ],

    elements: [
      {"data":{"id":"605755","idInt":605755,"name":"PCNA","score":0.006769776522008331,"query":true,"gene":true},"position":{"x":481.0169597039117,"y":384.8210888234145},"group":"nodes","removed":false,"selected":false,"selectable":true,"locked":false,"grabbed":false,"grabbable":true,"classes":"fn10273 fn6944 fn9471 fn10569 fn8023 fn6956 fn6935 fn8147 fn6939 fn6936 fn6629 fn7928 fn6947 fn8612 fn6957 fn8786 fn6246 fn9367 fn6945 fn6946 fn10024 fn10022 fn6811 fn9361 fn6279 fn6278 fn8569 fn7641 fn8568 fn6943"},
      {"data":{"id":"611408","idInt":611408,"name":"FEN1","score":0.006769776522008331,"query":false,"gene":true},"position":{"x":531.9740635094307,"y":464.8210898234145},"group":"nodes","removed":false,"selected":false,"selectable":true,"locked":false,"grabbed":false,"grabbable":true,"classes":"fn10273 fn6944 fn9471 fn6284 fn6956 fn6935 fn8147 fn6939 fn6936 fn6949 fn6629 fn7952 fn6680 fn6957 fn8786 fn6676 fn10713 fn7495 fn7500 fn9361 fn6279 fn6278 fn8569 fn7641 fn8568"},
      {"data":{"id":"612341","idInt":612341,"name":"RAD9A","score":0.0028974131563619387,"query":false,"gene":true},"position":{"x":455.8128125018193,"y":555.4591537139819},"group":"nodes","removed":false,"selected":false,"selectable":true,"locked":false,"grabbed":false,"grabbable":true,"classes":"fn6935 fn6219 fn6680 fn6676 fn10713 fn7552 fn7495"},
      {"data":{"id":"608473","idInt":608473,"name":"RAD9B","score":0.0026928704785200708,"query":false,"gene":true},"position":{"x":363.1144068403203,"y":515.7352912086707},"group":"nodes","removed":false,"selected":false,"selectable":true,"locked":false,"grabbed":false,"grabbable":true,"classes":"fn6935"},
      {"data":{"id":"611560","idInt":611560,"name":"APEX2","score":0.0026215687185565106,"query":false,"gene":true},"position":{"x":689.1927803956215,"y":634.0100611862405},"group":"nodes","removed":false,"selected":false,"selectable":true,"locked":false,"grabbed":false,"grabbable":true,"classes":""},
      {"data":{"id":"600585","idInt":600585,"name":"POLD3","score":0.0024938385347587078,"query":false,"gene":true},"position":{"x":118.3562364528364,"y":384.3877516879044},"group":"nodes","removed":false,"selected":false,"selectable":true,"locked":false,"grabbed":false,"grabbable":true,"classes":"fn10273 fn6944 fn9471 fn10569 fn8823 fn9180 fn6956 fn6935 fn8147 fn6939 fn6936 fn6648 fn6947 fn6957 fn8786 fn6246 fn9367 fn9368 fn6945 fn6946 fn7921 fn6811 fn8380 fn6279 fn6278 fn8569 fn7641 fn8568 fn6943"},
      {"data":{"id":"599889","idInt":599889,"name":"RAD51","score":0.002453016748286352,"query":false,"gene":true},"position":{"x":759.7017646483837,"y":338.3207127700095},"group":"nodes","removed":false,"selected":false,"selectable":true,"locked":false,"grabbed":false,"grabbable":true,"classes":"fn6931 fn9632 fn7950 fn9188 fn6956 fn6935 fn7338 fn6936 fn6949 fn6629 fn6957 fn6246 fn7453 fn7451 fn10024 fn7456 fn7454 fn7469 fn7467 fn10022 fn7552 fn7495 fn7463 fn7464 fn9361"},
      {"data":{"id":"602299","idInt":602299,"name":"LIG1","score":0.0023873089881679688,"query":false,"gene":true},"position":{"x":264.10227893804523,"y":631.7198779917306},"group":"nodes","removed":false,"selected":false,"selectable":true,"locked":false,"grabbed":false,"grabbable":true,"classes":"fn10273 fn6944 fn9471 fn6956 fn6935 fn8147 fn6939 fn6936 fn6949 fn6957 fn8786 fn6945 fn6946 fn6279 fn6278 fn8569 fn7641 fn8568 fn6943"},
      {"data":{"id":"603070","idInt":603070,"name":"RFC5","score":0.0022841757103715943,"query":false,"gene":true},"position":{"x":282.21587939790476,"y":109.57476207336902},"group":"nodes","removed":false,"selected":false,"selectable":true,"locked":false,"grabbed":false,"grabbable":true,"classes":"fn10273 fn9471 fn6956 fn6935 fn8147 fn6939 fn6936 fn6957 fn8786 fn6945 fn6946 fn6811 fn6279 fn6278 fn8569 fn7641 fn8568 fn6943"},
      {"data":{"id":"610236","idInt":610236,"name":"RFC4","score":0.002235382441847178,"query":false,"gene":true},"position":{"x":205.41323994659498,"y":122.2715768040765},"group":"nodes","removed":false,"selected":false,"selectable":true,"locked":false,"grabbed":false,"grabbable":true,"classes":"fn10273 fn9471 fn6956 fn6935 fn8147 fn6939 fn6936 fn6957 fn8786 fn6945 fn6946 fn6811 fn6279 fn6278 fn8569 fn7641 fn8568 fn6943"},
      {"data":{"id":"605365","idInt":605365,"name":"GADD45G","score":0.0021779529408011977,"query":false,"gene":true},"position":{"x":335.2681018951896,"y":398.62289259289554},"group":"nodes","removed":false,"selected":false,"selectable":true,"locked":false,"grabbed":false,"grabbable":true,"classes":""},
      {"data":{"id":"599863","idInt":599863,"name":"RFC2","score":0.001982524582665901,"query":false,"gene":true},"position":{"x":422.6986944382589,"y":59.422072599905285},"group":"nodes","removed":false,"selected":false,"selectable":true,"locked":false,"grabbed":false,"grabbable":true,"classes":"fn10273 fn9471 fn6956 fn6935 fn8147 fn6939 fn6936 fn6957 fn8786 fn6945 fn6946 fn6811 fn6279 fn6278 fn8569 fn7641 fn8568 fn6943"}
    ]

  });
});
