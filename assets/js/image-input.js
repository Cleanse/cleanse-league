$(function () {
    $(document).on('click', ".image-preview-clear", function () {
        $('.image-preview-filename').val("");
        $('.image-preview-clear').hide();
        $('.image-preview-input input:file').val("");
        $(".image-preview-input-title").text("Browse");
    });

    $(document).on('change', "#image-file:file", function () {
        var file = this.files[0];
        $(".image-preview-input-title").text("Change");
        $(".image-preview-clear").show();
        $(".image-preview-filename").val(file.name);
    });
});