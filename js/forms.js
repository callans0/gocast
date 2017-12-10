$(document).ready(function(){
  $(".add-form").prop("disabled", true);
  $("#podcast_title").change(function(){
    var selected_id = $("#podcast_title").find(':selected').attr('data-id');
    $('#collection_id').val(selected_id);
    $(".add-form").prop("disabled", false);
    var itunesAPI = "https://itunes.apple.com/lookup?entity=podcast";
    var itunesOptions = {
      "id": selected_id
    };
    function displayPodcast(data) {
      // alert("You selected " + selected_id + " " + data.results[0].trackName);
      $("#genre").val(data.results[0].primaryGenreName);
      $("#producer").val(data.results[0].artistName);
      $("#rating").val(data.results[0].rating);
      $("#country").val(data.results[0].country);
      $("#image").val(data.results[0].artworkUrl600);
      $("#collection_id").val(data.results[0].collectionId);
    };
    $.getJSON( itunesAPI, itunesOptions, displayPodcast);
  }); // end change function
}); //end document ready
