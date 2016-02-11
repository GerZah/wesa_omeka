jQuery(document).bind("omeka:elementformload", function() {
  var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

  var options = {};

  function resetOptions() {
      options['partialReference'] = '';
      options['item_typeReference'] = -1;
      options['sortReference'] = 'mod_desc';
      options['pageReference'] = 0;
      options['per_pageReference'] = 15;
      options = {
          partialReference: '',
          item_typeReference: -1,
          sortReference: 'mod_desc',
          pageReference: 0,
          per_pageReference: 15,
          max_pageReference: 0
      };
  }

  resetOptions();
  updateChoices();

  function updateChoices() {
      options['partialReference'] = $('#partialReference_object_titleReference').val();
      options['item_typeReference'] = $('#new_relation_object_item_typeReference_id').val();
      if ($('input[name=itemsListsortReference]:checked').val() === 'timestamp') {
          options['sortReference'] = 'mod_desc';
      }
      else {
          options['sortReference'] = 'alpha_asc';
      }
      if (options['pageReference'] < 0) {
          options['pageReference'] = 0;
      }
      if (options['pageReference'] > options['max_pageReference']) {
          options['pageReference'] = options['max_pageReference'];
      }
      $.ajax({
          url: url,
          dataType: 'json',
          data: options,
          success: function (data) {
              var i;
              var items = [];

              /* options */
              $('#lookup-resultsReference').find('li').remove();
              for (i = 0; i < data['items'].length; ++i) {
                  items.push('<li data-value="' + data['items'][i]['value'] + '">' + data['items'][i]['label'] + '</li>');
              }
              $('#lookup-resultsReference').append(items.join(''));

              /* pagination */
              options['max_pageReference'] = Math.floor(data['count'] / options['per_pageReference']);

              if (0 < options['pageReference']) {
                  $('#selector-previous-pageReference').removeClass('pg_disabledReference');
              }
              else {
                  $('#selector-previous-pageReference').addClass('pg_disabledReference');
              }

              if (options['pageReference'] < options['max_pageReference']) {
                  $('#selector-next-pageReference').removeClass('pg_disabledReference');
              }
              else {
                  $('#selector-next-pageReference').addClass('pg_disabledReference');
              }
          }
      });
  }

  $('#lookup-resultsReference').on('click', 'li', function () {
      $('#new_reference_object_item_id').val($(this).attr('data-value'));
      $('#object_titleReference').html($(this).html());
  });

  $('#selector-previous-pageReference').click(function () {
      if (0 < options['pageReference']) {
          options['pageReference']--;
          updateChoices();
      }
  });

  $('#selector-next-pageReference').click(function () {
      if (options['pageReference'] < options['max_pageReference']) {
          options['pageReference']++;
          updateChoices();
      }
  });

  $('#new_relation_object_item_typeReference_id').change(function () {
      updateChoices();
  });

  $('#new_selectObjectsortReferenceTimestamp').click(function () {
      updateChoices();
  });

  $('#new_selectObjectsortReferenceName').click(function () {
      updateChoices();
  });

  $('#partialReference_object_titleReference').on('input', function () {
      updateChoices();
  });



  var lightbox = lity(); // https://www.npmjs.com/package/lity
  var selectButtonTxt = $(".itemReferencesBtn").first().text();

  $(".itemReferencesBtn").unbind("click").click(function(e) {
    e.preventDefault();

    //$("#new_relation_property_id").hide().prev().hide().prev().hide();
    $("#add-reference").hide();
    $("#add-reference").parent().append("<a href='#' id='select_item' class='green button'>"+selectButtonTxt+"</a>");

    var currentTitle = $(this).prev().prev().attr("id"); // for title
    var currentId = $(this).prev().attr("id"); // for id

    // console.log(currentTitle);
    // console.log(currentId);

    $("#select_item").click(function(e) {
      e.preventDefault();
      $("#"+currentTitle).val($('#object_titleReference').text());
      $("#"+currentId).val($('#new_reference_object_item_id').val());

      lightbox.close();
    });

    lightbox("#item-reference-selector");
  });

  $(".itemReferencesClearBtn").click(function(e) {
    e.preventDefault();

    var currentTitle = $(this).prev().prev().prev().attr("id"); // for title
    var currentId = $(this).prev().prev().attr("id"); // for id

    $("#"+currentTitle).val("");
    $("#"+currentId).val("");
  } );

  $("#item-reference-selector button").click(function(e) { e.preventDefault(); });

  $(document).on('lity:close', function(event, lightbox) {
    //$("#new_relation_property_id").show().prev().show().prev().show();
    $("#add-reference").show();
    $("#select_item").remove();
  });

} );
