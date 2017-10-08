<?php
/**
 * Frontend Default Layout
 */

$siteName = Config::get('app.name', SITETITLE);

// Prepare the current User Info.
$user = Auth::user();

// Generate the Language Changer menu.
$langCode = Language::code();
$langName = Language::name();

$languages = Config::get('languages');

if (isset($user->image) && $user->image->exists()) {
    $imageUrl = resource_url('images/users/' .basename($user->image->path));
} else {
    $imageUrl = vendor_url('dist/img/avatar5.png', 'almasaeed2010/adminlte');
}

//
ob_start();

foreach ($languages as $code => $info) {
?>
<li class="header <?php if ($code == $langCode) { echo 'active'; } ?>">
    <a href='<?= site_url('language/' .$code); ?>' title='<?= $info['info']; ?>'><?= $info['name']; ?></a>
</li>
<?php
}

$langMenuLinks = ob_get_clean();
?>
<!DOCTYPE html>
<html lang="<?= $langCode; ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $title; ?> | <?= $siteName; ?></title>
    <?= isset($meta) ? $meta : ''; // Place to pass data / plugable hook zone ?>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?php
    Assets::css(array(
        // Bootstrap 3.3.5
        vendor_url('bower_components/bootstrap/dist/css/bootstrap.min.css', 'almasaeed2010/adminlte'),
        // Bootstrap XL
        theme_url('css/bootstrap-xl-mod.min.css', 'AdminLite'),
        // Font Awesome
        vendor_url('bower_components/font-awesome/css/font-awesome.min.css', 'almasaeed2010/adminlte'),
        // Ionicons
        vendor_url('bower_components/Ionicons/css/ionicons.min.css', 'almasaeed2010/adminlte'),
        // Theme style
        vendor_url('dist/css/AdminLTE.min.css', 'almasaeed2010/adminlte'),
        // AdminLTE Skins
        vendor_url('dist/css/skins/_all-skins.min.css', 'almasaeed2010/adminlte'),
        // iCheck
        vendor_url('plugins/iCheck/square/blue.css', 'almasaeed2010/adminlte'),
        // Custom CSS
        theme_url('css/style.css', 'AdminLite'),
    ));

    echo isset($css) ? $css : ''; // Place to pass data / plugable hook zone

    //Add Controller specific JS files.
    Assets::js(array(
            vendor_url('bower_components/jquery/dist/jquery.min.js', 'almasaeed2010/adminlte'),
        )
    );

    ?>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-<?= Config::get('app.color_scheme', 'blue'); ?> layout-top-nav">
<div class="wrapper">
  <header class="main-header">
    <nav class="navbar navbar-static-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <a href="<?= site_url(); ?>" class="navbar-brand"><strong><?= $siteName; ?></strong></a>
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
            <i class="fa fa-bars"></i>
          </button>
        </div>
        <!-- Navbar Left Menu -->
        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
            <ul class="nav navbar-nav">
                <?php foreach ($leftMenuItems as $item) { ?>
                    <?= View::partial('Partials/Frontend/NavbarMenuItems', 'AdminLite', array('item' => $item)); ?>
                <?php } ?>
            </ul>
            <!-- Search Form -->
            <?php if (isset($hasNavbarSearch) && ($hasNavbarSearch === true)) { ?>
            <form class="navbar-form navbar-left" role="search" action="<?= site_url('search'); ?>" method="GET">
                <div class="form-group">
                    <input type="text" name="query" class="form-control" id="navbar-search-input" placeholder="<?= __d('admin_lite', 'Search...'); ?>">
                </div>
            </form>
             <?php } ?>
        </div>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <?php foreach ($rightMenuItems as $item) { ?>
                    <?= View::partial('Partials/Frontend/NavbarMenuItems', 'AdminLite', array('item' => $item)); ?>
                <?php } ?>
                <li class="dropdown language-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class='fa fa-language'></i> <?= $langName; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <?= $langMenuLinks; ?>
                    </ul>
                </li>
                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="<?= $imageUrl ?>" class="user-image" alt="User Image">
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs"><?= $user->username; ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                        <img src="<?= $imageUrl ?>" class="img-circle" alt="User Image">

                        <p>
                            <?= $user->realname; ?> - <?= implode(', ', $user->roles->lists('name')); ?>
                            <?php $sinceDate = $user->created_at->formatLocalized(__d('admin_lite', '%d %b %Y, %R')); ?>
                            <small><?= __d('admin_lite', 'Member since {0}', $sinceDate); ?></small>
                        </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="<?= site_url('account'); ?>" class="btn btn-default btn-flat"><?= __d('admin_lite', 'Account'); ?></a>
                            </div>
                            <div class="pull-right">
                                <a href="<?= site_url('logout'); ?>" class="btn btn-default btn-flat"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <?= __d('admin_lite', 'Sign out'); ?>
                                </a>
                                <form id="logout-form" action="<?= site_url('logout'); ?>" method="POST" style="display: none;">
                                    <input type="hidden" name="csrfToken" value="<?= $csrfToken; ?>" />
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- /.navbar-custom-menu -->
      </div>
      <!-- /.container-fluid -->
    </nav>
  </header>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="container">
            <!-- Main content -->
            <section class="content">
                <?= $content; ?>
            </section>
        </div>
    </div>

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
      <small><!-- DO NOT DELETE! - Profiler --></small>
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="http://www.novaframework.com/" target="_blank"><b>Nova Framework <?= $version; ?> / Kernel <?= VERSION; ?></b></a> - </strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->
<?php
Assets::js(array(
    // Bootstrap 3.3.5
    vendor_url('bower_components/bootstrap/dist/js/bootstrap.min.js', 'almasaeed2010/adminlte'),
    // AdminLTE App
    vendor_url('dist/js/adminlte.min.js', 'almasaeed2010/adminlte'),
    // iCheck
    vendor_url('plugins/iCheck/icheck.min.js', 'almasaeed2010/adminlte'),
));

echo isset($js) ? $js : ''; // Place to pass data

?>

<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>

<!-- DO NOT DELETE! - Forensics Profiler -->

</body>
</html>
