<?php include_once APP_PATH.'operator/template/header.php';?>

<div class="alert alert-block alert-error">
<h1><?php if ($page1 == 'user-no-delete') { echo $tl["errorpage"]["u"]; } else if ($page1 == 'not-exist') { echo $tl["errorpage"]["not"]; } else if ($page1 == 'no-data') { echo $tl["errorpage"]["data"]; } else if ($page1 == 'usergroup-no-delete') { echo $tl["errorpage"]["ug"]; } else if ($page1 == 'cat-no-delete') { echo $tl["errorpage"]["cat"]; } else if ($page1 == 'mysql') { echo $tl["errorpage"]["sql"]; } else if ($page1 == 'plugin-no-delete') { echo $tl["errorpage"]["plugin"]; } ?></h1>
<p><a href="<?php echo $_SERVER['HTTP_REFERER'];?>"><?php echo $tl["general"]["re"];?></a></p>
<p><a href="<?php echo BASE_URL_ADMIN;?>"><?php echo $tl["general"]["lo"];?></a></p>
</div>

<?php include_once APP_PATH.'operator/template/footer.php';?>