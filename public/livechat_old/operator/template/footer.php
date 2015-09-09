<?php if ($LS_PROVED) { ?>
</div><!--/span-->
</div><!--/row-->
<?php } ?>

<hr>
<!-- Do not remove or modify the copyright without copyright free license http://www.livesupportrhino.com/shop/i/6/copyright-free -->
<footer>Copyright <?=date('Y');?> by <a href="http://www.livesupportrhino.com">Rhino Light</a><?php if (LS_SUPEROPERATORACCESS) echo ' ('.$tl['general']['g118'].LS_VERSION.')';?><?php if ($LS_PROVED) { ?> | <a href="index.php?p=logout" onclick="if(!confirm('<?php echo $tl["logout"]["l2"];?>'))return false;"><?php echo $tl["logout"]["l"];?></a><?php } ?></footer>

<span id="audio_alert"></span>

</div><!--/container-->

</body>
</html>