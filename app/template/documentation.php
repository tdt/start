<div id="markdown"></div>

<script src="<?php echo $location . "assets/js/Markdown.Converter.js" ; ?>"></script>
<script>

$(document).ready(function(){
    $.ajax({
        url : "<?php echo $location ?>README.md",
        dataType: "text",
        success : function (md) {
            var converter = new Markdown.Converter();
            $("#markdown").html(converter.makeHtml(md));
        }
    });
});
</script>