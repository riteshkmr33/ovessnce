<li<?php if ($page == '') { ?> class="active"<?php } ?>><a href="<?php echo BASE_URL_ADMIN;?>"><?php echo $tl["menu"]["m"];?></a></li>
<li<?php if ($page == 'leads') { ?> class="active"<?php } ?>><a href="index.php?p=leads"><?php echo $tl["menu"]["m1"];?></a></li>
<?php if ($LS_SPECIALACCESS) { ?>
<li<?php if ($page == 'emails') { ?> class="active"<?php } ?>><a href="index.php?p=emails"><?php echo $tl["menu"]["m8"];?></a></li>
<li<?php if ($page == 'files') { ?> class="active"<?php } ?>><a href="index.php?p=files"><?php echo $tl["menu"]["m2"];?></a></li>
<li<?php if ($page == 'response') { ?> class="active"<?php } ?>><a href="index.php?p=response"><?php echo $tl["menu"]["m3"];?></a></li>
<?php } ?>
<li<?php if ($page == 'users') { ?> class="active"<?php } ?>><a href="index.php?p=users"><?php echo $tl["menu"]["m4"];?></a></li>
<?php if ($LS_SPECIALACCESS) { ?>
<li<?php if ($page == 'settings') { ?> class="active"<?php } ?>><a href="index.php?p=settings"><?php echo $tl["menu"]["m5"];?></a></li>
<li<?php if ($page == 'logs') { ?> class="active"<?php } ?>><a href="index.php?p=logs"><?php echo $tl["menu"]["m6"];?></a></li>
<?php } ?>