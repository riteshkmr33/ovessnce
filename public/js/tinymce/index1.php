<script type="text/javascript" src="js/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea",
    plugins: [
        "advlist autolink lists link image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table contextmenu paste jbimages"
    ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image jbimages",
     relative_urls: false
});
</script>

<?php
	echo $config['img_path'] = '/tinymce/images'; // Relative to domain name
	echo "<br />";
	echo $config['upload_path'] = $_SERVER['DOCUMENT_ROOT'] . $config['img_path']; // Phys
	echo "<br />";

?>


<form method="post" action="somepage">
    <textarea name="content" style="width:100%"></textarea>
</form>
