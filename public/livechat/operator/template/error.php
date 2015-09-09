<?php include_once APP_PATH.'operator/template/header.php';?>

<div class="alert alert-block alert-error">
<h1><?php if ($page1 == 'user-no-delete') { echo $tl["errorpage"]["u"]; } elseif ($page1 == 'not-exist') { echo $tl["errorpage"]["not"]; } elseif ($page1 == 'no-data') { echo $tl["errorpage"]["data"]; } else { echo $tl["errorpage"]["sql"]; } ?></h1>
<p><a href="<?php echo $_SERVER['HTTP_REFERER'];?>"><?php echo $tl["general"]["re"];?></a></p>
<p><a href="<?php echo BASE_URL_ADMIN;?>"><?php echo $tl["general"]["lo"];?></a></p>
</div>

<?php include_once APP_PATH.'operator/template/footer.php';?>