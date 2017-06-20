$("#submit-obj").click(function() {
    var fileName = $("#objFile").val();

    if(fileName) {
        var iterations = $("#it-dd").text();
        console.log("Iterations = "+iterations);
        var input = $("<input>")
            .attr("type", "hidden")
            .attr("name", "iterations").val(iterations);
        $('#obj-form').append($(input)).submit();
        $(".warning").hide();
    } else {

        $(".warning").animate({opacity:0},200,"linear",function(){
            $(this).show();
            $(this).animate({opacity:1},200);
            $(this).animate({opacity:0},200);
            $(this).animate({opacity:1},200);
        });

    }

});

$('#dropdown-it').find('a').click(function(){

    console.log("hello");
    var prevText = $('#it-dd').text();
    $('#it-dd').contents().first().replaceWith($(this).text());
    $(this).text(prevText);
});
