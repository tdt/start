<xmp id='markdown' theme="united"></xmp>

<script>
    // Modify strapdown originBase
    var originBase = "<?php echo $location ?>assets/strapdown/";

    $(document).ready(function(){
        $.ajax({
            url : "<?php echo $location ?>README.md",
            dataType: "text",
            success : function (md) {
                // Load markdown loader
                $('#markdown').html(md);

                // Load strapdown
                $.getScript(originBase + 'strapdown.js');
            }
        });
    });
</script>