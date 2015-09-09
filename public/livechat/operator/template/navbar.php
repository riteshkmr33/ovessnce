<nav role="navigation" class="navbar navbar-default navbar-top navbar-fixed-top">
   <!-- START navbar header-->
   <div class="navbar-header">
      <a href="<?php echo BASE_URL;?>" class="navbar-brand">
         <div class="brand-logo"><?php echo LS_TITLE;?></div>
         <div class="brand-logo-collapsed"><img src="img/logo_small.png" alt="logo" /></div>
      </a>
   </div>
   <!-- END navbar header-->
   <!-- START Nav wrapper-->
   <div class="nav-wrapper">
      <!-- START Left navbar-->
      <ul class="nav navbar-nav">
         <li>
            <a href="javascript:void(0)" data-toggle="aside">
               <em class="fa fa-align-left"></em>
            </a>
         </li>
         <li>
            <a href="javascript:void(0)" data-toggle="navbar-search">
               <em class="fa fa-search"></em>
            </a>
         </li>
      </ul>
      <!-- END Left navbar-->
      <!-- START Right Navbar-->
      <ul class="nav navbar-nav navbar-right">
         <!-- START Alert menu-->
         <li>
            <a href="javascript:void(0)" id="sound_alert">
               <em class="fa <?php if ($lsuser->getVar("sound")) { echo "fa-bell-slash-o"; } else { echo "fa-bell";}?>"></em>
            </a>
         </li>
         <?php if ($lsuser->getVar("operatorchat") == 1){;?>
         <li>
         	<a href="javascript:void(0)" onclick="if(navigator.userAgent.toLowerCase().indexOf('opera') != -1 && window.event.preventDefault) window.event.preventDefault();this.newWindow = window.open('index.php?p=chat', 'lsr', 'toolbar=0,scrollbars=1,location=0,status=1,menubar=0,width=750,height=680,resizable=1');this.newWindow.focus();this.newWindow.opener=window;return false;" data-toggle="tooltip" data-title="<?php echo $tl["help"]["h9"];?>"><em class="fa fa-comments"></em></a>
         </li>
         <?php } ?>
         <!-- END Alert menu-->
         <!-- START User menu-->
         <li class="dropdown">
            <a href="#" data-toggle="dropdown" data-play="bounceIn" class="dropdown-toggle">
               <em class="fa fa-user"></em>
            </a>
            <!-- START Dropdown menu-->
            <ul class="dropdown-menu">
               <li>
                  <div class="p">
                  	<!-- Departments -->
                     <p><?php echo $tl["general"]["g107"];?> <span class="label label-success pull-right"><?php echo $LS_USR_DEPARTMENTS;?></span></p>
                  </div>
               </li>
               <li class="divider"></li>
               <li><a href="index.php?p=users&sp=edit&ssp=<?php echo LS_USERID_RHINO;?>"><?php echo $tl["general"]["g227"];?></a>
               </li>
               <li><a href="index.php?p=logout" onclick="if(!confirm('<?php echo $tl["logout"]["l2"];?>'))return false;"><?php echo $tl["logout"]["l"];?></a>
               </li>
            </ul>
            <!-- END Dropdown menu-->
         </li>
         <!-- END User menu-->
      </ul>
      <!-- END Right Navbar-->
   </div>
   <!-- END Nav wrapper-->
   <!-- START Search form-->
   <form role="search" class="navbar-form" method="post" action="index.php?p=leads">
      <div class="form-group has-feedback">
         <input type="text" class="form-control" name="jakSH" placeholder="<?php echo $tl['search']['s3'];?>">
         <div data-toggle="navbar-search-dismiss" class="fa fa-times form-control-feedback"></div>
      </div>
      <button name="search" type="submit" class="hidden btn btn-default"><?php echo $tl["search"]["s3"];?></button>
   </form>
   <!-- END Search form-->
</nav>

<!-- START aside-->
<aside class="aside">
   <!-- START Sidebar (left)-->
   <nav class="sidebar">
      <ul class="nav">
         <!-- START user info-->
         <li>
            <div data-toggle="collapse-next" class="item user-block">
               <!-- User picture-->
               <div class="user-block-picture">
                  <img src="../<?php echo LS_FILES_DIRECTORY.$lsuser->getVar("picture");?>" alt="Avatar" width="60" height="60" class="img-thumbnail img-circle">
                  <!-- Status when collapsed-->
                  <div class="user-block-status">
                     <div id="available_user_coll" class="point<?php if ($lsuser->getVar("available") == 0) { echo ' point-danger'; } elseif ($lsuser->getVar("available") == 2) { echo ' point-warning'; } else { echo ' point-success';}?> point-lg"></div>
                  </div>
               </div>
               <!-- Name and Role-->
               <div class="user-block-info">
                  <span class="user-block-name item-text"><?php echo $LS_WELCOME_NAME;?></span>
                  <!-- START Dropdown to change status-->
                  <div class="btn-group user-block-status">
                     <button type="button" data-toggle="dropdown" data-play="fadeIn" data-duration="0.2" class="btn btn-xs dropdown-toggle">
                        <div id="available_user" class="point<?php if ($lsuser->getVar("available") == 0) { echo ' point-danger'; } elseif ($lsuser->getVar("available") == 2) { echo ' point-warning'; } else { echo ' point-success';}?>"></div><span id="available_user_text"><?php if ($lsuser->getVar("available") == 0) { echo $tl["general"]["g1"]; } elseif ($lsuser->getVar("available") == 2) { echo $tl["general"]["g202"]; } else { echo $tl["general"]["g"]; } ?></span></button>
                     <ul class="dropdown-menu text-left pull-right">
                        <li>
                           <a href="javascript:void(0)" class="available_user" id="avail-1">
                              <div class="point point-success" ></div><?php echo $tl["general"]["g"];?></a>
                        </li>
                        <li>
                           <a href="javascript:void(0)" class="available_user" id="avail-2">
                              <div class="point point-warning"></div><?php echo $tl["general"]["g202"];?></a>
                        </li>
                        <li>
                           <a href="javascript:void(0)" class="available_user" id="avail-0">
                              <div class="point point-danger"></div><?php echo $tl["general"]["g1"];?></a>
                        </li>
                     </ul>
                  </div>
                  <!-- END Dropdown to change status-->
               </div>
            </div>
         </li>
         <!-- END user info-->
         <!-- START Menu-->
         <li<?php if ($page == '') echo ' class="active"';?>><a href="<?php echo BASE_URL_ADMIN;?>"><em class="fa fa-dashboard"></em><span class="item-text"><?php echo $tl["menu"]["m"];?></span></a></li>
         <?php if (ls_get_access("leads", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)){;?>
         <li<?php if ($page == 'leads') echo ' class="active"';?>><a href="index.php?p=leads"><em class="fa fa-comments-o"></em><span class="item-text"><?php echo $tl["menu"]["m1"];?></span></a></li>
         <?php } if (ls_get_access("ochat", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)){;?>
         <li<?php if ($page == 'chats') echo ' class="active"';?>><a href="index.php?p=chats"><em class="fa fa-comment"></em><span class="item-text"><?php echo $tl["menu"]["m14"];?></span></a></li>
         <?php } ?>
         <li class="dropdown"><a href="#" id="drop2" role="button" data-toggle="collapse-next" class="has-submenu"><em class="fa fa-wrench"></em><span class="item-text"><?php echo $tl["menu"]["m5"];?></span></a>
         	<ul class="nav collapse<?php if (in_array($page, array('files','response','proactive','departments','users','style','settings','maintenance'))) echo ' in';?>">
         	<?php if (ls_get_access("files", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)){;?>
         	<li<?php if ($page == 'files') echo ' class="active"';?>><a role="menuitem" href="index.php?p=files"><span class="item-text"><?php echo $tl["menu"]["m2"];?></span></a></li>
         	<?php } if (ls_get_access("responses", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)){;?>
         	<li<?php if ($page == 'response') echo ' class="active"';?>><a role="menuitem" href="index.php?p=response"><span class="item-text"><?php echo $tl["menu"]["m3"];?></span></a></li>
         	<?php } if (ls_get_access("proactive", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)){;?>
         	<li<?php if ($page == 'proactive') echo ' class="active"';?>><a role="menuitem" href="index.php?p=proactive"><span class="item-text"><?php echo $tl["menu"]["m18"];?></span></a></li>
         	<?php } if (ls_get_access("departments", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)){;?>
         	<li<?php if ($page == 'departments') echo ' class="active"';?>><a role="menuitem" href="index.php?p=departments"><span class="item-text"><?php echo $tl["menu"]["m9"];?></span></a></li>
         	<?php } ?>
         	<li<?php if ($page == 'users') echo ' class="active"';?>><a role="menuitem" href="index.php?p=users"><span class="item-text"><?php echo $tl["menu"]["m4"];?></span></a></li>
         	<?php if ($LS_SPECIALACCESS) { ?>
         	<li<?php if ($page == 'style') echo ' class="active"';?>><a role="menuitem" href="index.php?p=style"><span class="item-text"><?php echo $tl["menu"]["m13"];?></span></a></li>
         	<?php } if (ls_get_access("settings", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)){;?>
         	<li<?php if ($page == 'settings') echo ' class="active"';?>><a role="menuitem" href="index.php?p=settings"><span class="item-text"><?php echo $tl["menu"]["m5"];?></span></a></li>
         	<?php } if (ls_get_access("maintenance", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)){;?>
         	<li<?php if ($page == 'maintenance') echo ' class="active"';?>><a role="menuitem" href="index.php?p=maintenance"><span class="item-text"><?php echo $tl["menu"]["m19"];?></span></a></li>
         	<?php } ?>
         	</ul>
         </li>
         <?php if (ls_get_access("statistic", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)){;?>
         <li<?php if ($page == 'statistics') echo ' class="active"';?>><a href="index.php?p=statistics"><em class="fa fa-bar-chart-o"></em><span class="item-text"><?php echo $tl["menu"]["m10"];?></a></span></li>
         <?php } if (ls_get_access("logs", $lsuser->getVar("permissions"), LS_SUPERADMINACCESS)){;?>
         <li<?php if ($page == 'logs') echo ' class="active"';?>><a href="index.php?p=logs"><em class="fa fa-database"></em><span class="item-text"><?php echo $tl["menu"]["m6"];?></span></a></li>
         <?php } ?>
         <!-- END Menu-->
         <?php if ($LS_PROVED) { ?>
         <!-- Sidebar footer -->
         <li class="nav-footer">
            <div class="nav-footer-divider"></div>
            <!-- START button group-->
            <div class="btn-group text-center">
               
               <a href="index.php?p=users&sp=edit&ssp=<?php echo LS_USERID_RHINO;?>" data-toggle="tooltip" data-title="<?php echo $tl["general"]["g227"];?>" class="btn btn-link"><em class="fa fa-user text-muted"></em></a>
               <a href="index.php?p=logout" onclick="if(!confirm('<?php echo $tl["logout"]["l2"];?>'))return false;" data-toggle="tooltip" data-title="<?php echo $tl["logout"]["l"];?>" class="btn btn-link"><em class="fa fa-sign-out text-muted"></em></a>
            </div>
            <!-- END button group-->
         </li>
        <?php } ?>
      </ul>
   </nav>
   <!-- END Sidebar (left)-->
</aside>
<!-- End aside-->