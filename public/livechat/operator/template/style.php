<?php include_once APP_PATH.'operator/template/header.php';?>

<h3><?php echo $tl["menu"]["m13"];?></h3>

<?php if ($errors) { ?>
<div class="alert alert-danger"><?php echo $errors["e"];?></div>
<?php } ?>

<form class="ls_form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">

<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["g164"];?> <a href="javascript:void(0)" class="rhino-help" data-content="<?php echo $tl["help"]["h15"];?>" data-original-title="<?php echo $tl["help"]["t"];?>"><span class="glyphicon glyphicon-question-sign"></span></a></th>
</tr>
</thead>
<tr>
	<td><?php echo $tl["general"]["g165"];?></td>
	<td>
	<input type="file" name="uploadpp" accept="image/*" />
	</td>
</tr>
<tr>
	<td><?php echo $tl["general"]["g166"];?></td>
	<td>
	<input type="file" name="uploadpp1" accept="image/*" />
	</td>
</tr>

</table>

<button type="submit" name="upload" class="btn btn-primary btn-block"><?php echo $tl["general"]["g38"];?></button>

<hr>

<ul class="nav nav-tabs" id="ls-tabs">
  <li class="active"><a href="#buttons"><?php echo $tl["general"]["g71"];?></a></li>
  <li><a href="#style"><?php echo $tl["menu"]["m13"];?></a></li>
</ul>
 
<div class="tab-content">
  <div class="tab-pane active" id="buttons">
  
  <hr>
  
  <div class="btn-group">
    <button class="btn btn-default<?php if ($_SESSION['slide_up'] == 'on') echo ' active';?>" name="slide_up" value="slide_up"><?php echo $tl["general"]["g178"];?></button>
    <button class="btn btn-default<?php if ($_SESSION['slide_pop_up'] == 'on') echo ' active';?>" name="slide_pop_up" value="pop_up"><?php echo $tl["general"]["g178"].'/'.$tl["general"]["g71"];?></button>
    <button class="btn btn-default<?php if ($_SESSION['pop_up'] == 'on') echo ' active';?>" name="pop_up" value="pop_up"><?php echo $tl["general"]["g179"];?></button>
  </div>
  
  <hr>
  
  <div class="row">
  <div class="col-md-4">
  <?php if (isset($DEPARTMENTS_ALL) && is_array($DEPARTMENTS_ALL)) { ?><?php echo $tl["general"]["g131"];?>: <select name="jak_depid" id="changedep" class="form-control"><option value="0"<?php if ($_SESSION["departments"] == 0) { ?> selected="selected"<?php } ?>><?php echo $tl["general"]["g105"];?></option>
  <?php foreach($DEPARTMENTS_ALL as $z) { ?>
  	<option value="<?php echo $z["id"];?>"<?php if ($_SESSION["departments"] == $z["id"]) { ?> selected="selected"<?php } ?>><?php echo $z["title"];?></option>
  <?php } ?></select><?php } ?>
  </div>
  <div class="col-md-4">
  <?php if (isset($OPERATORS_ALL) && is_array($OPERATORS_ALL)) { ?><?php echo $tl["general"]["g106"];?>: <select name="jak_opid" id="changeop" class="form-control"><option value="0"<?php if ($_SESSION["operator"] == 0) { ?> selected="selected"<?php } ?>><?php echo $tl["general"]["g105"];?></option>
  <?php foreach($OPERATORS_ALL as $y) { ?>
  	<option value="<?php echo $y["id"];?>"<?php if ($_SESSION["operator"] == $y["id"]) { ?> selected="selected"<?php } ?>><?php echo $y["username"];?></option>
  <?php } ?></select><?php } ?>
  </div>
  <div class="col-md-4">
  <?php if (isset($lang_files) && is_array($lang_files)) { echo $tl["general"]["g22"];?>: <select name="ls_lang" size="1" class="form-control" id="changelang">
  <option value="<?php echo LS_LANG;?>"<?php if (!isset($_SESSION["lang_button"])) { echo 'selected="selected"';}?>><?php echo ucwords(LS_LANG);?></option>
  <?php foreach($lang_files as $lf) { ?><option value="<?php echo $lf;?>"<?php if (isset($_SESSION["lang_button"]) && $_SESSION["lang_button"] == $lf) { echo 'selected="selected"';}?>><?php echo ucwords($lf);?></option><?php } ?>
  </select>
  <?php } ?>
  </div>
  </div>
  
  <hr>
  
  <?php 
  
  if ($_SESSION['show_host'] == 'off') { 
  	$b_host = parse_url(BASE_URL_ORIG, PHP_URL_PATH);
  } else {
  	$b_host = BASE_URL_ORIG;
  }
  
  $d_id = '';
  
  if (is_numeric($_SESSION['departments'])) {
  
  	$d_id = '&amp;dep='.$_SESSION['departments'];
  	$d_sid = 'ls.did='.$_SESSION['departments'].';';
  	
  	$o_id = '';
  	$o_sid = '';
  	
  }
  
  if (is_numeric($_SESSION['operator'])) {
  
  	$o_id = '&amp;opid='.$_SESSION['operator'];
  	$o_sid = 'ls.opid='.$_SESSION['operator'].';';
  	
  	$d_id = '&amp;dep=0';
  	$d_sid = '';
  	
  }
 
  if ($_SESSION['slide_up'] == 'on') { ?>

<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["chato"];?><input type="checkbox" name="slidechato" id="slidechato" value="on"<?php if ($_SESSION['slide_chato'] == 'on') echo ' checked="checked"';?> /> | <?php echo $tl["general"]["chatc"];?><input type="checkbox" name="slidechatc" id="slidechatc" value="on"<?php if ($_SESSION['slide_chatc'] == 'on') echo ' checked="checked"';?> /> | <?php echo $tl["general"]["jquery"];?><input type="checkbox" name="jquerybutton" id="jquerybutton" value="on"<?php if ($_SESSION['show_jquery'] == 'on') echo ' checked="checked"';?> /> | <?php echo $tl["general"]["host"];?><input type="checkbox" name="hostname" id="hostname" value="off"<?php if ($_SESSION['show_host'] == 'off') echo ' checked="checked"';?> /> | <?php echo $tl["general"]["showi"];?><input type="checkbox" name="showimage" id="showimage" value="on"<?php if ($_SESSION['showimage'] == 'on') echo ' checked="checked"';?> /> | <?php echo $tl["general"]["cpro"];?><input type="checkbox" name="cproactive" id="cproactive" value="on"<?php if ($_SESSION['chat_proactive'] == 'on') echo ' checked="checked"';?> /></th>
</tr>
</thead>
<?php  

	$j_slide = '';
	$j_query = '';
	$s_chat = '';
	$s_chat1 = '';
	$hide_o = '';
	$showi = '';
	$proa = '';
	$b_lang = LS_LANG;
	
	if ($_SESSION['showimage'] == 'on') {
	
		$showi = '<div id="jrc_chat_now"></div>';
		
	}
	
	if ($_SESSION['show_jquery'] == 'on') {
	
		$j_query = '<script src="'.$b_host.'js/jquery.js" type="text/javascript"></script>';
		
	}
	
	if ($_SESSION['slide_chato'] == 'on') {
	
		$hide_o = 'ls.chi=1;';
		
	}
	
	if ($_SESSION['chat_proactive'] == 'on') {
	
		$proa = 'ls.pact=0;';
		
	}
	
	if (isset($_SESSION['lang_button']) && $_SESSION['lang_button'] != LS_LANG) {
	
		$b_lang = $_SESSION['lang_button'];
		
	}
	
	if ($_SESSION['slide_chatc'] == 'on') {
	
		$s_chat = '<div class="jrc_chatbox cb_default" id="jrc_chat_window"><div class="header header_bg_default"><p class="jrc_chat_title">'.LS_TITLE.'</p><a href="javascript:;" class="popup_chatbox">&#9776;</a><a href="javascript:;" class="minimize_chatbox" title="minimize chat window">_</a><a href="javascript:;" class="maximize_chatbox" title="maximize chat window">+</a>'.$showi.'</div><div id="jrc_main_area" class="main-area"></div>';
		
		$s_chat1 = '</div><script src="'.$b_host.'js/slide_up_cross.js" type="text/javascript"></script><script type="text/javascript">ls.main_url="'.$b_host.'";ls.socket_url="'.SOCKET_PROTOCOL.'";jrc_lang="'.$b_lang.'";'.$d_sid.$o_sid.$hide_o.$proa.'</script>';
	
	} else {
		
		$s_chat = '<div class="jrc_chatbox cb_default" id="jrc_chat_window"><div class="header header_bg_default"><p class="jrc_chat_title">'.LS_TITLE.'</p><a href="javascript:;" class="popup_chatbox">&#9776;</a><a href="javascript:;" class="minimize_chatbox" title="minimize chat window">_</a><a href="javascript:;" class="maximize_chatbox" title="maximize chat window">+</a>'.$showi.'</div><div id="jrc_main_area" class="main-area"></div>';
		
		$s_chat1 = '</div><script src="'.$b_host.'js/slide_up.js" type="text/javascript"></script><script type="text/javascript">ls.main_url="'.$b_host.'";ls.socket_url="'.SOCKET_PROTOCOL.'";jrc_lang="'.$b_lang.'";'.$d_sid.$o_sid.$hide_o.$proa.'</script>';
		
	}
	
		$buttoncode = htmlentities('<!-- live support rhino button -->'.$j_query.$s_chat.'<img src="'.$b_host.'index.php?p=b&amp;i=blue&amp;lang='.$b_lang.$d_id.$o_id.'" width="0" height="0" alt="img" />'.$s_chat1.$j_slide.'<a class="jrc_chatlink" href="'.$b_host.'index.php?p=start&amp;lang='.$b_lang.'&amp;slide=0'.$d_id.$o_id.'" target="_blank"><div class="header header_bg_default"><p class="jrc_chat_title">'.LS_TITLE.'</p></div></a><div id="proactivePopUp"></div><!--[if lt IE 10]><script type="text/javascript">ie10rLower = true;</script><![endif]--><!-- end live support rhino button -->');
		?>
		<tr>
			<td colspan="2"><?php echo $tl["help"]["h14"];?><hr><pre><code>&lt;link href=&quot;<?php echo $b_host;?>css/slide_up.css&quot; rel=&quot;stylesheet&quot; type=&quot;text/css&quot; /&gt;</code></pre><pre><code>&lt;link href=&quot;<?php echo $b_host;?>css/slide_up_left.css&quot; rel=&quot;stylesheet&quot; type=&quot;text/css&quot; /&gt;</code></pre><pre><code>&lt;link href=&quot;<?php echo $b_host;?>css/slide_out_side.css&quot; rel=&quot;stylesheet&quot; type=&quot;text/css&quot; /&gt;</code></pre></td>
		</tr>
		<tr>
			<td colspan="2"><textarea rows="8" class="form-control" readonly="readonly"><?php echo $buttoncode;?></textarea></td>
		</tr>
</table>

<?php } if ($_SESSION['slide_pop_up'] == 'on') { ?>

<table class="table table-striped">
<thead>
<tr>
<th colspan="2"><?php echo $tl["general"]["float"];?><input type="checkbox" name="floatbutton" id="floatbutton" value="on"<?php if ($_SESSION['show_float'] == 'on') echo ' checked="checked"';?> /> | <?php echo $tl["general"]["slide"];?><input type="checkbox" name="slidebutton" id="slidebutton" value="on"<?php if ($_SESSION['show_slide'] == 'on') echo ' checked="checked"';?> /> | <?php echo $tl["general"]["chato"];?><input type="checkbox" name="slidechato" id="slidechato" value="on"<?php if ($_SESSION['slide_chato'] == 'on') echo ' checked="checked"';?> /> | <?php echo $tl["general"]["chatc"];?><input type="checkbox" name="slidechatc" id="slidechatc" value="on"<?php if ($_SESSION['slide_chatc'] == 'on') echo ' checked="checked"';?> /> | <?php echo $tl["general"]["jquery"];?><input type="checkbox" name="jquerybutton" id="jquerybutton" value="on"<?php if ($_SESSION['show_jquery'] == 'on') echo ' checked="checked"';?> /> | <?php echo $tl["general"]["host"];?><input type="checkbox" name="hostname" id="hostname" value="off"<?php if ($_SESSION['show_host'] == 'off') echo ' checked="checked"';?> /> | <?php echo $tl["general"]["cpro"];?><input type="checkbox" name="cproactive" id="cproactive" value="on"<?php if ($_SESSION['chat_proactive'] == 'on') echo ' checked="checked"';?> /></th>
</tr>
</thead>
<tr>
	<td colspan="2"><?php echo $tl["help"]["h14"];?><hr><pre><code>&lt;link href=&quot;<?php echo $b_host;?>css/slide_up.css&quot; rel=&quot;stylesheet&quot; type=&quot;text/css&quot; /&gt;</code></pre><pre><code>&lt;link href=&quot;<?php echo $b_host;?>css/slide_up_left.css&quot; rel=&quot;stylesheet&quot; type=&quot;text/css&quot; /&gt;</code></pre><pre><code>&lt;link href=&quot;<?php echo $b_host;?>css/slide_out_side.css&quot; rel=&quot;stylesheet&quot; type=&quot;text/css&quot; /&gt;</code></pre></td>
</tr>
<?php if (isset($get_buttons) && is_array($get_buttons)) {
	$j_slide = '';
	$i_slide = '';
	$j_query = '';
	$s_chat = '';
	$s_chat1 = '';
	$hide_o = '';
	$proa = '';
	$b_lang = LS_LANG;
	
	if ($_SESSION['show_float'] == 'on') { 
		$b_float = ' style="position:fixed;bottom:5px;right:5px;"';
	}
	
	if ($_SESSION['show_slide'] == 'on') { 
		$b_float = ' class="rhino_chat" style="width:40px;height:100px;overflow:hidden;position:fixed;top:200px;left:0px;"';
		$i_slide = ' style="position:absolute;left: -260px;"';
		$j_slide = '<script type="text/javascript">jQuery(document).ready(function(){jQuery(".rhino_chat").hover(function(){jQuery(this).css("width", 300);jQuery("img", this).stop().animate({left:"-1px"},{queue:false,duration:250});}, function(){jQuery("img", this).stop().animate({left:"-260px"},{queue:false,duration:800,complete:function(){jQuery(".rhino_chat").css("width",40);}});});});</script>';
	}
	
	if ($_SESSION['show_jquery'] == 'on') {
	
		$j_query = '<script src="'.$b_host.'js/jquery.js" type="text/javascript"></script>';
		
	}
	
	if ($_SESSION['slide_chato'] == 'on') {
	
		$hide_o = 'ls.chi=1;';
		
	}
	
	if ($_SESSION['chat_proactive'] == 'on') {
	
		$proa = 'ls.pact=0;';
		
	}
	
	if (isset($_SESSION['lang_button']) && $_SESSION['lang_button'] != LS_LANG) {
	
		$b_lang = $_SESSION['lang_button'];
		
	}
	
	if ($_SESSION['slide_chatc'] == 'on') {
	
		$s_chat = '<div class="jrc_chatbox cb_default" id="jrc_chat_window"><div class="header header_bg_default"><p class="jrc_chat_title">'.LS_TITLE.'</p><a href="javascript:;" class="popup_chatbox">&#9776;</a><a href="javascript:;" class="minimize_chatbox" title="minimize chat window">_</a><a href="javascript:;" class="maximize_chatbox" title="maximize chat window">+</a></div><div id="jrc_main_area" class="main-area"></div>';
		
		$s_chat1 = '</div><script src="'.$b_host.'js/slide_up_cross.js" type="text/javascript"></script><script type="text/javascript">ls.main_url="'.$b_host.'";ls.socket_url="'.SOCKET_PROTOCOL.'";jrc_slide_button = true;jrc_lang="'.$b_lang.'";'.$d_sid.$hide_o.$proa.'</script>';
	
	} else {
		
		$s_chat = '<div class="jrc_chatbox cb_default" id="jrc_chat_window"><div class="header header_bg_default"><p class="jrc_chat_title">'.LS_TITLE.'</p><a href="javascript:;" class="popup_chatbox">&#9776;</a><a href="javascript:;" class="minimize_chatbox" title="minimize chat window">_</a><a href="javascript:;" class="maximize_chatbox" title="maximize chat window">+</a></div><div id="jrc_main_area" class="main-area"></div>';
		
		$s_chat1 = '</div><script src="'.$b_host.'js/slide_up.js" type="text/javascript"></script><script type="text/javascript">ls.main_url="'.$b_host.'";ls.socket_url="'.SOCKET_PROTOCOL.'";jrc_slide_button = true;jrc_lang="'.$b_lang.'";'.$d_sid.$hide_o.$proa.'</script>';
		
	}
	
		foreach($get_buttons as $v) {
		
		$buttoncode = htmlentities('<!-- live support rhino button --><img src="'.$b_host.'index.php?p=b&amp;i='.$v['shortname'].'&amp;lang='.$b_lang.$d_id.$o_id.'" width="'.$v['width'].'" height="'.$v['height'].'" alt="img" id="jrc_slide_button_img"'.$i_slide.' />'.$j_query.$s_chat.$s_chat1.$j_slide.'<a class="jrc_chatlink" href="'.$b_host.'index.php?p=start&amp;lang='.$b_lang.'&amp;slide=0'.$d_id.$o_id.'" target="_blank"><div class="header header_bg_default"><p class="jrc_chat_title">'.LS_TITLE.'</p></div></a><div id="proactivePopUp"></div><!--[if lt IE 10]><script type="text/javascript">ie10rLower = true;</script><![endif]--><!-- end live support rhino button -->');
		?>
		<tr>
			<td class="go"><img src="../<?php echo LS_FILES_DIRECTORY;?>/buttons/<?php echo $v['name'];?>" width="<?php echo $v['width'];?>" height="<?php echo $v['height'];?>" alt=""/></td>
			<td><textarea rows="8" class="form-control" readonly="readonly"><?php echo $buttoncode;?></textarea></td>
		</tr>
		<?php } } ?>
</table>

	<?php } if ($_SESSION['pop_up'] == 'on') { ?>
	
		<table class="table table-striped">
		<thead>
		<tr>
		<th colspan="2"><?php echo $tl["general"]["jquery"];?><input type="checkbox" name="jquerybutton" id="jquerybutton" value="on"<?php if ($_SESSION['show_jquery'] == 'on') echo ' checked="checked"';?> /> | <?php echo $tl["general"]["chatc"];?><input type="checkbox" name="slidechatc" id="slidechatc" value="on"<?php if ($_SESSION['slide_chatc'] == 'on') echo ' checked="checked"';?> /> | <?php echo $tl["general"]["float"];?><input type="checkbox" name="floatbutton" id="floatbutton" value="on"<?php if ($_SESSION['show_float'] == 'on') echo ' checked="checked"';?> /> | <?php echo $tl["general"]["slide"];?><input type="checkbox" name="slidebutton" id="slidebutton" value="on"<?php if ($_SESSION['show_slide'] == 'on') echo ' checked="checked"';?> /> | <?php echo $tl["general"]["host"];?><input type="checkbox" name="hostname" id="hostname" value="off"<?php if ($_SESSION['show_host'] == 'off') echo ' checked="checked"';?> /> | <?php echo $tl["general"]["cpro"];?><input type="checkbox" name="cproactive" id="cproactive" value="on"<?php if ($_SESSION['chat_proactive'] == 'on') echo ' checked="checked"';?> /></th>
		</tr>
		</thead>
		<tr>
			<td colspan="2" class="go"><?php echo $tl["help"]["h14"];?><hr><pre><code>&lt;link href=&quot;<?php echo $b_host;?>css/pop_up.css&quot; rel=&quot;stylesheet&quot; type=&quot;text/css&quot; /&gt;</code></pre></td>
		</tr>
		<?php if (isset($get_buttons) && is_array($get_buttons)) { 
			
			$b_float = '';
			$i_slide = '';
			$j_slide = '';
			$j_query = '';
			$s_chat = '';
			$s_chat1 = '';
			$b_lang = LS_LANG;
			
			if ($_SESSION['show_float'] == 'on') { 
				$b_float = ' style="position:fixed;bottom:5px;right:5px;"';
			}
			
			if ($_SESSION['show_slide'] == 'on') { 
				$b_float = ' class="rhino_chat" style="width:40px;height:100px;overflow:hidden;position:fixed;top:200px;left:0px;z-index:9999;"';
				$i_slide = ' style="position:absolute;left: -260px;"';
				$j_slide = '<script type="text/javascript">jQuery(document).ready(function(){jQuery(".rhino_chat").hover(function(){jQuery(this).css("width", 300);jQuery("img", this).stop().animate({left:"-1px"},{queue:false,duration:250});}, function(){jQuery("img", this).stop().animate({left:"-260px"},{queue:false,duration:800,complete:function(){jQuery(".rhino_chat").css("width",40);}});});});</script>';
			}
			
			if ($_SESSION['show_jquery'] == 'on') {
			
				$j_query = '<script src="'.$b_host.'js/jquery.js" type="text/javascript"></script>';
				
			}
			
			if (isset($_SESSION['lang_button']) && $_SESSION['lang_button'] != LS_LANG) {
			
				$b_lang = $_SESSION['lang_button'];
				
			}
				
				$s_chat = '<a href="'.$b_host.'index.php?p=start&amp;lang='.$b_lang.'&amp;slide=0'.$d_id.$o_id.'" target="_blank" onclick="if(navigator.userAgent.toLowerCase().indexOf(\'opera\') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open(\''.$b_host.'index.php?p=start&amp;lang='.$b_lang.'&amp;slide=0'.$d_id.$o_id.'\', \'lsr\', \'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=780,height=600,resizable=1\');this.newWindow.focus();this.newWindow.opener=window;return false;"'.$b_float.'>';
				
				if (!isset($_SESSION['chat_proactive'])) {
				if ($_SESSION['slide_chatc'] == 'on') {
					$s_chat1 = '</a><script src="'.$b_host.'js/proactive_cross.js" type="text/javascript"></script><script type="text/javascript">ls.main_url="'.$b_host.'";ls.socket_url="'.SOCKET_PROTOCOL.'";jrc_lang="'.$b_lang.'";</script>';
				} else {
					$s_chat1 = '</a><script src="'.$b_host.'js/proactive.js" type="text/javascript"></script><script type="text/javascript">ls.main_url="'.$b_host.'";ls.socket_url="'.SOCKET_PROTOCOL.'";jrc_lang="'.$b_lang.'";</script>';
				}
				}
		
			foreach($get_buttons as $v) {
		
		$buttoncode = htmlentities('<!-- live support rhino button -->'.$j_query.$s_chat.'<img src="'.$b_host.'index.php?p=b&amp;i='.$v['shortname'].'&amp;lang='.$b_lang.$d_id.$o_id.'" width="'.$v['width'].'" height="'.$v['height'].'" alt="img"'.$i_slide.' />'.$s_chat1.$j_slide.'<div id="proactivePopUp"></div><!--[if lt IE 10]><script type="text/javascript">ie10rLower=true;</script><![endif]--><!-- end live support rhino button -->');
		?>
		<tr>
			<td class="go"><img src="../<?php echo LS_FILES_DIRECTORY;?>/buttons/<?php echo $v['name'];?>" width="<?php echo $v['width'];?>" height="<?php echo $v['height'];?>" alt=""/></td>
			<td><textarea rows="8" class="form-control" readonly="readonly"><?php echo $buttoncode;?></textarea></td>
		</tr>
		<?php } } ?>
		</table>
		
	<?php } ?>

</div>

<div class="tab-pane" id="style">

<h3><?php echo $tl["general"]["g15"];?></h3>

<table class="table table-striped styleChanger">
<tr>
<td><?php echo $tl["style"]["s"];?></td>
<td>
<div class="stCols">
	<span style="background-color:#5c5447;"<?php if (LS_BGCOLOR_TPL == '#5c5447'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#78685b;"<?php if (LS_BGCOLOR_TPL == '#78685b'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#6a625e;"<?php if (LS_BGCOLOR_TPL == '#6a625e'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#727272;"<?php if (LS_BGCOLOR_TPL == '#727272'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#cbb8a1;"<?php if (LS_BGCOLOR_TPL == '#cbb8a1'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#a0a0a0;"<?php if (LS_BGCOLOR_TPL == '#a0a0a0'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#a7033f;"<?php if (LS_BGCOLOR_TPL == '#a7033f'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#db0440;"<?php if (LS_BGCOLOR_TPL == '#db0440'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#ef1f28;"<?php if (LS_BGCOLOR_TPL == '#ef1f28'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#ff4c36;"<?php if (LS_BGCOLOR_TPL == '#ff4c36'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#ee5c1f;"<?php if (LS_BGCOLOR_TPL == '#ee5c1f'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#ff7f40;"<?php if (LS_BGCOLOR_TPL == '#ff7f40'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#4c4880;"<?php if (LS_BGCOLOR_TPL == '#4c4880'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#0059ab;"<?php if (LS_BGCOLOR_TPL == '#0059ab'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#0a67ed;"<?php if (LS_BGCOLOR_TPL == '#0a67ed'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#0076be;"<?php if (LS_BGCOLOR_TPL == '#0076be'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#53a2d4;"<?php if (LS_BGCOLOR_TPL == '#53a2d4'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#00b2ef;"<?php if (LS_BGCOLOR_TPL == '#00b2ef'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#00736c;"<?php if (LS_BGCOLOR_TPL == '#00736c'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#00ab56;"<?php if (LS_BGCOLOR_TPL == '#00ab56'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#00d14e;"<?php if (LS_BGCOLOR_TPL == '#00d14e'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#27d000;"<?php if (LS_BGCOLOR_TPL == '#27d000'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#6ab11e;"<?php if (LS_BGCOLOR_TPL == '#6ab11e'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
	<span style="background-color:#8fc740;"<?php if (LS_BGCOLOR_TPL == '#8fc740'){ echo ' class="scurrent"'; } ?>>&nbsp;</span>
</div>
</td>
</tr>
<tr>
<td><?php echo $tl["style"]["s1"];?></td>
<td><input type="text" class="form-control" name="pcolor" id="pcolor" value="<?php echo LS_BGCOLOR_TPL;?>" /></td>
</tr>
<tr><td class="content-title" colspan="2"><?php echo $tl["style"]["s6"];?></td>
</tr>
<tr><td>H1, H2, H3...</td>
<td><input type="text" class="form-control" name="pfhead" id="phead" title="Heading" value="<?php echo LS_FHCOLOR_TPL;?>" />
</td>
</tr>
<tr><td>div, p (H1, H2, H3)...</td>
<td><input type="text" class="form-control" name="pfheadc" id="pheadcontent" title="Heading Content" value="<?php echo LS_FHCCOLOR_TPL;?>" />
</td>
</tr>
<tr><td>div, p, code...</td>
<td><input type="text" class="form-control" name="pfont" id="pfont" title="Content" value="<?php echo LS_FCOLOR_TPL;?>" />
</td>
</tr>
<tr><td><?php echo $tl["style"]["s7"];?></td>
<td><input type="text" class="form-control" name="icont" id="icont" title="Container" value="<?php echo LS_ICCOLOR_TPL;?>" />
</td>
</tr>
<tr><td><?php echo $tl["style"]["s8"];?></td>
<td><input type="text" class="form-control" name="pafont" id="pafont" value="<?php echo LS_FACOLOR_TPL;?>" />
</td>
</tr>
<tr><td><?php echo $tl["style"]["s4"];?></td>
<td><select name="gFont" id="gFont" class="form-control">
		<optgroup label="Recomended Fonts">
			<option value='Ubuntu'<?php if (LS_FONTG_TPL == 'Ubuntu'){ echo ' selected="selected"'; } ?>>Ubuntu</option>
			<option value='Walter+Turncoat'<?php if (LS_FONTG_TPL == 'Walter+Turncoat'){ echo ' selected="selected"'; } ?>>Walter Turncoat</option>
			<option value='Lato'<?php if (LS_FONTG_TPL == 'Lato'){ echo ' selected="selected"'; } ?>>Lato</option>
			<option value='Amaranth'<?php if (LS_FONTG_TPL == 'Amaranth'){ echo ' selected="selected"'; } ?>>Amaranth</option>
			<option value='Pacifico'<?php if (LS_FONTG_TPL == 'Pacifico'){ echo ' selected="selected"'; } ?>>Pacifico</option>
			<option value='Anton'<?php if (LS_FONTG_TPL == 'Anton'){ echo ' selected="selected"'; } ?>>Anton</option>
			<option value='Luckiest+Guy'<?php if (LS_FONTG_TPL == 'Luckiest+Guy'){ echo ' selected="selected"'; } ?>>Luckiest Guy</option>
			<option value='Permanent+Marker'<?php if (LS_FONTG_TPL == 'Permanent+Marker'){ echo ' selected="selected"'; } ?>>Permanent Marker</option>
			<option value='Merriweather'<?php if (LS_FONTG_TPL == 'Merriweather'){ echo ' selected="selected"'; } ?>>Merriweather</option>
			<option value='Cuprum'<?php if (LS_FONTG_TPL == 'Cuprum'){ echo ' selected="selected"'; } ?>>Cuprum</option>
			<option value='Neuton'<?php if (LS_FONTG_TPL == 'Neuton'){ echo ' selected="selected"'; } ?>>Neuton</option>
			<option value='Lobster'<?php if (LS_FONTG_TPL == 'Lobster'){ echo ' selected="selected"'; } ?>>Lobster</option>
			<option value='NonGoogle'<?php if (LS_FONTG_TPL == 'NonGoogle'){ echo ' selected="selected"'; } ?>>Use Same as Content Font</option>
		</optgroup>
		<optgroup label="Other Fonts">
			<option value='Allan'<?php if (LS_FONTG_TPL == 'Allan'){ echo ' selected="selected"'; } ?>>Allan</option>
			<option value='Allerta'<?php if (LS_FONTG_TPL == 'Allerta'){ echo ' selected="selected"'; } ?>>Allerta</option>
			<option value='Allerta+Stencil'<?php if (LS_FONTG_TPL == 'Allerta+Stencil'){ echo ' selected="selected"'; } ?>>Allerta Stencil</option>
			<option value='Anonymous+Pro'<?php if (LS_FONTG_TPL == 'Anonymous+Pro'){ echo ' selected="selected"'; } ?>>Anonymous Pro</option>
			<option value='Arimo'<?php if (LS_FONTG_TPL == 'Arimo'){ echo ' selected="selected"'; } ?>>Arimo</option>
			<option value='Arvo'<?php if (LS_FONTG_TPL == 'Arvo'){ echo ' selected="selected"'; } ?>>Arvo</option>
			<option value='Astloch'<?php if (LS_FONTG_TPL == 'Astloch'){ echo ' selected="selected"'; } ?>>Astloch</option>
			<option value='Bentham'<?php if (LS_FONTG_TPL == 'Bentham'){ echo ' selected="selected"'; } ?>>Bentham</option>
			<option value='Bevan'<?php if (LS_FONTG_TPL == 'Bevan'){ echo ' selected="selected"'; } ?>>Bevan</option>
			<option value='Buda:light'<?php if (LS_FONTG_TPL == 'Buda:light'){ echo ' selected="selected"'; } ?>>Buda</option>
			<option value='Cabin'<?php if (LS_FONTG_TPL == 'Cabin'){ echo ' selected="selected"'; } ?>>Cabin</option>
			<option value='Cabin+Sketch'<?php if (LS_FONTG_TPL == 'Cabin+Sketch'){ echo ' selected="selected"'; } ?>>Cabin Sketch</option>
			<option value='Calligraffitti'<?php if (LS_FONTG_TPL == 'Calligraffitti'){ echo ' selected="selected"'; } ?>>Calligraffitti</option>
			<option value='Candal'<?php if (LS_FONTG_TPL == 'Candal'){ echo ' selected="selected"'; } ?>>Candal</option>
			<option value='Cantarell'<?php if (LS_FONTG_TPL == 'Cantarell'){ echo ' selected="selected"'; } ?>>Cantarell</option>
			<option value='Cardo'<?php if (LS_FONTG_TPL == 'Cardo'){ echo ' selected="selected"'; } ?>>Cardo</option>
			<option value='Cherry+Cream+Soda'<?php if (LS_FONTG_TPL == 'Cherry+Cream+Soda'){ echo ' selected="selected"'; } ?>>Cherry Cream Soda</option>
			<option value='Chewy'<?php if (LS_FONTG_TPL == 'Chewy'){ echo ' selected="selected"'; } ?>>Chewy</option>
			<option value='Coda:800'<?php if (LS_FONTG_TPL == 'Coda:800'){ echo ' selected="selected"'; } ?>>Coda</option>
			<option value='Coda+Caption:800'<?php if (LS_FONTG_TPL == 'Coda+Caption:800'){ echo ' selected="selected"'; } ?>>Coda Caption</option>
			<option value='Coming+Soon'<?php if (LS_FONTG_TPL == 'Coming+Soon'){ echo ' selected="selected"'; } ?>>Coming Soon</option>
			<option value='Copse'<?php if (LS_FONTG_TPL == 'Copse'){ echo ' selected="selected"'; } ?>>Copse</option>
			<option value='Corben'<?php if (LS_FONTG_TPL == 'Corben'){ echo ' selected="selected"'; } ?>>Corben</option>
			<option value='Cousine'<?php if (LS_FONTG_TPL == 'Cousine'){ echo ' selected="selected"'; } ?>>Cousine</option>
			<option value='Covered+By+Your+Grace'<?php if (LS_FONTG_TPL == 'Covered+By+Your+Grace'){ echo ' selected="selected"'; } ?>>Covered By Your Grace</option>
			<option value='Crafty+Girls'<?php if (LS_FONTG_TPL == 'Crafty+Girls'){ echo ' selected="selected"'; } ?>>Crafty Girls</option>
			<option value='Crimson+Text'<?php if (LS_FONTG_TPL == 'Crimson+Text'){ echo ' selected="selected"'; } ?>>Crimson Text</option>
			<option value='Crushed'<?php if (LS_FONTG_TPL == 'Crushed'){ echo ' selected="selected"'; } ?>>Crushed</option>
			<option value='Dancing+Script'<?php if (LS_FONTG_TPL == 'Dancing+Script'){ echo ' selected="selected"'; } ?>>Dancing Script</option>
			<option value='Droid+Sans'<?php if (LS_FONTG_TPL == 'Droid+Sans'){ echo ' selected="selected"'; } ?>>Droid Sans</option>
			<option value='Droid+Sans+Mono'<?php if (LS_FONTG_TPL == 'Droid+Sans+Mono'){ echo ' selected="selected"'; } ?>>Droid Sans Mono</option>
			<option value='Droid+Serif'<?php if (LS_FONTG_TPL == 'Droid+Serif'){ echo ' selected="selected"'; } ?>>Droid Serif</option>
			<option value='EB+Garamond'<?php if (LS_FONTG_TPL == 'EB+Garamond'){ echo ' selected="selected"'; } ?>>EB Garamond</option>
			<option value='Expletus+Sans'<?php if (LS_FONTG_TPL == 'Expletus+Sans'){ echo ' selected="selected"'; } ?>>Expletus Sans</option>
			<option value='Fontdiner+Swanky'<?php if (LS_FONTG_TPL == 'Fontdiner+Swanky'){ echo ' selected="selected"'; } ?>>Fontdiner Swanky</option>
			<option value='Geo'<?php if (LS_FONTG_TPL == 'Geo'){ echo ' selected="selected"'; } ?>>Geo</option>
			<option value='Goudy+Bookletter+1911'<?php if (LS_FONTG_TPL == 'Goudy+Bookletter+1911'){ echo ' selected="selected"'; } ?>>Goudy Bookletter 1911</option>
			<option value='Gruppo'<?php if (LS_FONTG_TPL == 'Gruppo'){ echo ' selected="selected"'; } ?>>Gruppo</option>
			<option value='Homemade+Apple'<?php if (LS_FONTG_TPL == 'Homemade+Apple'){ echo ' selected="selected"'; } ?>>Homemade Apple</option>
			<option value='IM+Fell+DW+Pica'<?php if (LS_FONTG_TPL == 'IM+Fell+DW+Pica'){ echo ' selected="selected"'; } ?>>IM Fell DW Pica</option>
			<option value='IM+Fell+French+Canon+SC'<?php if (LS_FONTG_TPL == 'IM+Fell+French+Canon+SC'){ echo ' selected="selected"'; } ?>>IM Fell French Canon SC</option>
			<option value='IM+Fell+French+Canon'<?php if (LS_FONTG_TPL == 'IM+Fell+French+Canon'){ echo ' selected="selected"'; } ?>>IM Fell French Canon</option>
			<option value='IM+Fell+Great+Primer+SC'<?php if (LS_FONTG_TPL == 'IM+Fell+Great+Primer+SC'){ echo ' selected="selected"'; } ?>>IM Fell Great Primer SC</option>
			<option value='IM+Fell+Great+Primer'<?php if (LS_FONTG_TPL == 'IM+Fell+Great+Primer'){ echo ' selected="selected"'; } ?>>IM Fell Great Primer</option>
			<option value='IM+Fell+English+SC'<?php if (LS_FONTG_TPL == 'IM+Fell+English+SC'){ echo ' selected="selected"'; } ?>>IM Fell English SC</option>
			<option value='IM+Fell+English'<?php if (LS_FONTG_TPL == 'IM+Fell+English'){ echo ' selected="selected"'; } ?>>IM Fell English</option>
			<option value='IM+Fell+DW+Pica+SC'<?php if (LS_FONTG_TPL == 'IM+Fell+DW+Pica+SC'){ echo ' selected="selected"'; } ?>>IM Fell DW Pica SC</option>
			<option value='IM+Fell+Double+Pica+SC'<?php if (LS_FONTG_TPL == 'IM+Fell+Double+Pica+SC'){ echo ' selected="selected"'; } ?>>IM Fell Double Pica SC</option>
			<option value='IM+Fell+Double+Pica'<?php if (LS_FONTG_TPL == 'IM+Fell+Double+Pica'){ echo ' selected="selected"'; } ?>>IM Fell Double Pica</option>
			<option value='Inconsolata'<?php if (LS_FONTG_TPL == 'Inconsolata'){ echo ' selected="selected"'; } ?>>Inconsolata</option>
			<option value='Indie+Flower'<?php if (LS_FONTG_TPL == 'Indie+Flower'){ echo ' selected="selected"'; } ?>>Indie Flower</option>
			<option value='Irish+Grover'<?php if (LS_FONTG_TPL == 'Irish+Grover'){ echo ' selected="selected"'; } ?>>Irish Grover</option>
			<option value='Josefin+Sans'<?php if (LS_FONTG_TPL == 'Josefin+Sans'){ echo ' selected="selected"'; } ?>>Josefin Sans</option>
			<option value='Josefin+Slab'<?php if (LS_FONTG_TPL == 'Josefin+Slab'){ echo ' selected="selected"'; } ?>>Josefin Slab</option>
			<option value='Just+Another+Hand'<?php if (LS_FONTG_TPL == 'Just+Another+Hand'){ echo ' selected="selected"'; } ?>>Just Another Hand</option>
			<option value='Just+Me+Again+Down+Here'<?php if (LS_FONTG_TPL == 'Just+Me+Again+Down+Here'){ echo ' selected="selected"'; } ?>>Just Me Again Down Here</option>
			<option value='Kenia'<?php if (LS_FONTG_TPL == 'Kenia'){ echo ' selected="selected"'; } ?>>Kenia</option>
			<option value='Kranky'<?php if (LS_FONTG_TPL == 'Kranky'){ echo ' selected="selected"'; } ?>>Kranky</option>
			<option value='Kreon'<?php if (LS_FONTG_TPL == 'Kreon'){ echo ' selected="selected"'; } ?>>Kreon</option>
			<option value='Kristi'<?php if (LS_FONTG_TPL == 'Kristi'){ echo ' selected="selected"'; } ?>>Kristi</option>
			<option value='League+Script'<?php if (LS_FONTG_TPL == 'League+Script'){ echo ' selected="selected"'; } ?>>League Script</option>
			<option value='Lekton'<?php if (LS_FONTG_TPL == 'Lekton'){ echo ' selected="selected"'; } ?>>Lekton</option>
			<option value='Meddon'<?php if (LS_FONTG_TPL == 'Meddon'){ echo ' selected="selected"'; } ?>>Meddon</option>
			<option value='MedievalSharp'<?php if (LS_FONTG_TPL == 'MedievalSharp'){ echo ' selected="selected"'; } ?>>MedievalSharp</option>
			<option value='Molengo'<?php if (LS_FONTG_TPL == 'Molengo'){ echo ' selected="selected"'; } ?>>Molengo</option>
			<option value='Mountains+of+Christmas'<?php if (LS_FONTG_TPL == 'Mountains+of+Christmas'){ echo ' selected="selected"'; } ?>>Mountains of Christmas</option>
			<option value='Neucha'<?php if (LS_FONTG_TPL == 'Neucha'){ echo ' selected="selected"'; } ?>>Neucha</option>
			<option value='Nobile'<?php if (LS_FONTG_TPL == 'Nobile'){ echo ' selected="selected"'; } ?>>Nobile</option>
			<option value='Nova+Script'<?php if (LS_FONTG_TPL == 'Nova+Script'){ echo ' selected="selected"'; } ?>>Nova Script</option>
			<option value='Nova+Round'<?php if (LS_FONTG_TPL == 'Nova+Round'){ echo ' selected="selected"'; } ?>>Nova Round</option>
			<option value='Nova+Oval'<?php if (LS_FONTG_TPL == 'Nova+Oval'){ echo ' selected="selected"'; } ?>>Nova Oval</option>
			<option value='Nova+Mono'<?php if (LS_FONTG_TPL == 'Nova+Mono'){ echo ' selected="selected"'; } ?>>Nova Mono</option>
			<option value='Nova+Cut'<?php if (LS_FONTG_TPL == 'Nova+Cut'){ echo ' selected="selected"'; } ?>>Nova Cut</option>
			<option value='Nova+Slim'<?php if (LS_FONTG_TPL == 'Nova+Slim'){ echo ' selected="selected"'; } ?>>Nova Slim</option>
			<option value='Nova+Flat'<?php if (LS_FONTG_TPL == 'Nova+Flat'){ echo ' selected="selected"'; } ?>>Nova Flat</option>
			<option value='OFL+Sorts+Mill+Goudy+TT'<?php if (LS_FONTG_TPL == 'OFL+Sorts+Mill+Goudy+TT'){ echo ' selected="selected"'; } ?>>OFL Sorts Mill Goudy TT</option>
			<option value='Old+Standard+TT'<?php if (LS_FONTG_TPL == 'Old+Standard+TT'){ echo ' selected="selected"'; } ?>>Old Standard TT</option>
			<option value='Orbitron'<?php if (LS_FONTG_TPL == 'Orbitron'){ echo ' selected="selected"'; } ?>>Orbitron</option>
			<option value='Oswald'<?php if (LS_FONTG_TPL == 'Oswald'){ echo ' selected="selected"'; } ?>>Oswald</option>
			<option value='Philosopher'<?php if (LS_FONTG_TPL == 'Philosopher'){ echo ' selected="selected"'; } ?>>Philosopher</option>
			<option value='PT+Sans'<?php if (LS_FONTG_TPL == 'PT+Sans'){ echo ' selected="selected"'; } ?>>PT Sans</option>
			<option value='PT+Sans+Narrow'<?php if (LS_FONTG_TPL == 'PT+Sans+Narrow'){ echo ' selected="selected"'; } ?>>PT Sans Narrow</option>
			<option value='PT+Sans+Caption'<?php if (LS_FONTG_TPL == 'PT+Sans+Caption'){ echo ' selected="selected"'; } ?>>PT Sans Caption</option>
			<option value='PT+Serif'<?php if (LS_FONTG_TPL == 'PT+Serif'){ echo ' selected="selected"'; } ?>>PT Serif</option>
			<option value='PT+Serif+Caption'<?php if (LS_FONTG_TPL == 'PT+Serif+Caption'){ echo ' selected="selected"'; } ?>>PT Serif Caption</option>
			<option value='Puritan'<?php if (LS_FONTG_TPL == 'Puritan'){ echo ' selected="selected"'; } ?>>Puritan</option>
			<option value='Quattrocento'<?php if (LS_FONTG_TPL == 'Quattrocento'){ echo ' selected="selected"'; } ?>>Quattrocento</option>
			<option value='Raleway:100'<?php if (LS_FONTG_TPL == 'Raleway:100'){ echo ' selected="selected"'; } ?>>Raleway</option>
			<option value='Reenie+Beanie'<?php if (LS_FONTG_TPL == 'Reenie+Beanie'){ echo ' selected="selected"'; } ?>>Reenie Beanie</option>
			<option value='Rock+Salt'<?php if (LS_FONTG_TPL == 'Rock+Salt'){ echo ' selected="selected"'; } ?>>Rock Salt</option>
			<option value='Schoolbell'<?php if (LS_FONTG_TPL == 'Schoolbell'){ echo ' selected="selected"'; } ?>>Schoolbell</option>
			<option value='Slackey'<?php if (LS_FONTG_TPL == 'Slackey'){ echo ' selected="selected"'; } ?>>Slackey</option>
			<option value='Sniglet:800'<?php if (LS_FONTG_TPL == 'Sniglet:800'){ echo ' selected="selected"'; } ?>>Sniglet</option>
			<option value='Sunshiney'<?php if (LS_FONTG_TPL == 'Sunshiney'){ echo ' selected="selected"'; } ?>>Sunshiney</option>
			<option value='Syncopate'<?php if (LS_FONTG_TPL == 'Syncopate'){ echo ' selected="selected"'; } ?>>Syncopate</option>
			<option value='Tangerine'<?php if (LS_FONTG_TPL == 'Tangerine'){ echo ' selected="selected"'; } ?>>Tangerine</option>
			<option value='Tinos'<?php if (LS_FONTG_TPL == 'Tinos'){ echo ' selected="selected"'; } ?>>Tinos</option>
			<option value='UnifrakturCook'<?php if (LS_FONTG_TPL == 'UnifrakturCook'){ echo ' selected="selected"'; } ?>>UnifrakturCook</option>
			<option value='UnifrakturMaguntia'<?php if (LS_FONTG_TPL == 'UnifrakturMaguntia'){ echo ' selected="selected"'; } ?>>UnifrakturMaguntia</option>
			<option value='Unkempt'<?php if (LS_FONTG_TPL == 'Unkempt'){ echo ' selected="selected"'; } ?>>Unkempt</option>
			<option value='VT323'<?php if (LS_FONTG_TPL == 'VT323'){ echo ' selected="selected"'; } ?>>VT323</option>
			<option value='Vibur'<?php if (LS_FONTG_TPL == 'Vibur'){ echo ' selected="selected"'; } ?>>Vibur</option>
			<option value='Vollkorn'<?php if (LS_FONTG_TPL == 'Vollkorn'){ echo ' selected="selected"'; } ?>>Vollkorn</option>
			<option value='Yanone+Kaffeesatz'<?php if (LS_FONTG_TPL == 'Yanone+Kaffeesatz'){ echo ' selected="selected"'; } ?>>Yanone Kaffeesatz</option>
		</optgroup>
	</select>
</td>
</tr>
<tr><td><?php echo $tl["style"]["s5"];?></td>
<td><select name="cFont" id="cFont" class="form-control">
		<option value='"Trebuchet MS", Helvetica, Garuda, sans-serif'<?php if (LS_FONT_TPL == '"Trebuchet MS", Helvetica, Garuda, sans-serif'){ echo ' selected="selected"'; } ?>>Trebuchet MS</option>
		<option value='Arial, Helvetica, sans-serif'<?php if (LS_FONT_TPL == 'Arial, Helvetica, sans-serif'){ echo ' selected="selected"'; } ?>>Arial</option>
		<option value='"Comic Sans MS", Monaco, "TSCu_Comic", cursive'<?php if (LS_FONT_TPL == '"Comic Sans MS", Monaco, "TSCu_Comic", cursive'){ echo ' selected="selected"'; } ?>>Comic Sans MS</option>
		<option value='Georgia, Times, "Century Schoolbook L", serif'<?php if (LS_FONT_TPL == 'Georgia, Times, "Century Schoolbook L", serif'){ echo ' selected="selected"'; } ?>>Georgia</option>
		<option value='Verdana, Geneva, "DejaVu Sans", sans-serif'<?php if (LS_FONT_TPL == 'Verdana, Geneva, "DejaVu Sans", sans-serif'){ echo ' selected="selected"'; } ?>>Verdana</option>
		<option value='Tahoma, Geneva, Kalimati, sans-serif'<?php if (LS_FONT_TPL == 'Tahoma, Geneva, Kalimati, sans-serif'){ echo ' selected="selected"'; } ?>>Tahoma</option>
		<option value='"Lucida Sans Unicode", "Lucida Grande", Garuda, sans-serif'<?php if (LS_FONT_TPL == '"Lucida Sans Unicode", "Lucida Grande", Garuda, sans-serif'){ echo ' selected="selected"'; } ?>>Lucida Sans</option>
		<option value='Calibri, "AppleGothic", "MgOpen Modata", sans-serif'<?php if (LS_FONT_TPL == 'Calibri, "AppleGothic", "MgOpen Modata", sans-serif'){ echo ' selected="selected"'; } ?>>Calibri</option>
		<option value='"Times New Roman", Times, "Nimbus Roman No9 L", serif'<?php if (LS_FONT_TPL == '"Times New Roman", Times, "Nimbus Roman No9 L", serif'){ echo ' selected="selected"'; } ?>>Times New Roman</option>
		<option value='"Courier New", Courier, "Nimbus Mono L", monospace'<?php if (LS_FONT_TPL == '"Courier New", Courier, "Nimbus Mono L", monospace'){ echo ' selected="selected"'; } ?>>Courier New</option>
	</select>
</td>
</tr>
</table>

<button type="submit" name="save" class="btn btn-primary btn-block"><?php echo $tl["general"]["g38"];?></button>

</div>

</div>

</form>

<script type="text/javascript" src="js/page.ajax.js"></script>
<!-- Style Changer -->
<script type="text/javascript" src="js/minicolor.js"></script>
<script type="text/javascript" src="js/changer.js"></script>

<script type="text/javascript">
$(document).ready(function(){

	$('#ls-tabs a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	})
	
		setChecker(<?php echo $lsuser->getVar("id");?>);
                setInterval("setChecker(<?php echo $lsuser->getVar("id");?>);", 10000);
		setTimer(<?php echo $lsuser->getVar("id");?>);
                setInterval("setTimer(<?php echo $lsuser->getVar("id");?>);", 120000);
                
        $("#slidechato, #slidechatc, #jquerybutton, #hostname, #floatbutton, #showimage, #slidebutton, #changedep, #changeop, #changelang, #cproactive").change(function() {
            $(this).closest("form").submit();
        });
});

		ls.main_url = "<?php echo BASE_URL_ADMIN;?>";
		ls.main_lang = "<?php echo LS_LANG;?>";
		ls.ls_submit = "<?php echo $tl['general']['g69'];?>";
		ls.ls_submitwait = "<?php echo $tl['general']['g70'];?>";
</script>

<?php include_once APP_PATH.'operator/template/footer.php';?>