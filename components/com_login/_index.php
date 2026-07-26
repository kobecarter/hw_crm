<?php

if ((isset($_GET['lang_sys']) && !empty($_GET['lang_sys']))) {
    if($_GET['lang_sys'] != 'fr' && $_GET['lang_sys'] != 'en'){
        $_SESSION['lang_sys'] = 'fr';
    }else {
        $_SESSION['lang_sys'] = $_GET['lang_sys'];
    }
} else if (isset($_SESSION['lang_sys'])) {
    // Ne rien faire
} else {
    $_SESSION['lang_sys'] = 'fr';
}

include "components/com_login/traduction.php";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Administration <?= strtoupper($projet); ?></title>
    <link rel='stylesheet' href='styles/simi.css' type='text/css'>
    <link rel='stylesheet' href='styles/simi-2.css' type='text/css'>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    @javascript html5shiv respond.min
    <![endif]-->

    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/jquery.form.js"></script>
    <script src='js/c81813dd5f2238060c9ddecda9683907.js'></script>
</head>

<style>
    body {
        position: relative;
    }
    .langue-sys {
        position: absolute;
        top: 20px;
        left: 20px;
    }
    .langue-sys ul {
        margin: 0;
        padding: 0;
        display: table;
    }
    .langue-sys ul li {
        list-style: none;
        margin: 10px 0;
        float: left;
        padding: 0 5px;
    }
    .langue-sys ul li:first-child {
        border-right: 2px solid #c1c1c1;
    }
    .langue-sys ul li a {
        color: #18af90;
    }
    .langue-sys ul li a:hover,
    .langue-sys ul li a:focus,
    .langue-sys ul li a.active {
        color: #3891cb;
        text-decoration: none;
        font-weight: bold;
    }
    .row {
        margin: 0;
        padding: 0;
    }
</style>

<body>
<div class="all-wrapper no-menu-wrapper light-bg">
    <div class="row">

        <?php
        @$task = $_GET['task'];
        switch ($task) {
            case 'login' :
                showForm();
                break;
            case 'forgotPass' :
                forgotPass();
                break;
            case 'resetPassword' :
                resetPassword();
                break;
            default :
                showForm();
        }

        /* ---------------------------- function ---------------------------- */
        function showForm()
        {
            global $siteURL, $trad_com_login;
            ?>
            <script type="text/javascript">
                $(document).ready(function () {
                    var succes = "<?= $trad_com_login['SUCCES'][$_SESSION['lang_sys']];?>";
                    var error = "<?= $trad_com_login['ERREUR'][$_SESSION['lang_sys']];?>";
                    var warning = "<?= $trad_com_login['ATTENTION'][$_SESSION['lang_sys']];?>";

                    // Press Enter button
                    $(document).keypress(function(e) {
                        if(e.which == 13) {
                            $('form#loginForm').submit();
                        }
                    });

                    $(".login").click(function (event) {
                        event.preventDefault();
                        $('form#loginForm').submit();
                    });

                    $('form#loginForm').ajaxForm({
                        beforeSubmit: function () {
                            //chargement
                            $("#loginForm .loading").show();
                        },
                        success: function (theResponse) {
                            var succes_msg = "<?= $trad_com_login['SUCCES_CNX'][$_SESSION['lang_sys']];?>";
                            var error_msg = "<?= $trad_com_login['ERREUR_CNX'][$_SESSION['lang_sys']];?>";
                            var warning_msg = "<?= $trad_com_login['ATTENTION_CNX'][$_SESSION['lang_sys']];?>";
                            var exec_msg = "<?= $trad_com_login['ERREUR_EXECUTION'][$_SESSION['lang_sys']];?>";

                            $("#loginForm .loading").hide();
                            if (parseInt(theResponse) === 1) {
                                $('#loginForm .msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>' + succes + '</strong> ' + succes_msg + '</div>');
                                setTimeout(function () {
                                    document.location = "index.php?option=com_dashboard";
                                }, 1500)
                            }
                            else if (parseInt(theResponse) === 2) {
                                $('#loginForm .msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong> ' + error_msg + '</div>');
                                $('#loginForm .msgbox').slideDown();
                            }
                            else if (parseInt(theResponse) === 0) {
                                $('#loginForm .msgbox').html('<div class="alert alert-warning alert-dismissable"><i class="icon-exclamation-sign"></i> <strong>' + warning + '</strong> ' + warning_msg + '</div>');
                                $('#loginForm .msgbox').slideDown();
                            }
                            else {
                                $('#loginForm .msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong> ' + exec_msg + '</div>');
                                $('#loginForm .msgbox').slideDown();
                            }
                        }
                    });
                });
            </script>
        <?php
        $logo = "";
        $directory = "../images/config/";
        foreach (glob($directory . '*.*') as $file) {
            $logo = $file;
        }
        if ($logo == "") {
            if (file_exists("../images/cms-logo.png"))
                $logo = "../images/cms-logo.png";
        }
        ?>

            <div class="langue-sys">
                <ul>
                    <?php
                    $langs_sys = array('fr' => 'Français', 'en' => 'English');
                    foreach($langs_sys as $code => $lang){
                        $class = ($_SESSION['lang_sys'] == $code)? 'class = "active"' : '';
                        ?>
                        <li><a id="<?= $code ;?>" <?= $class ;?> href="<?= $siteURL ;?>hw-admin/index.php?option=com_login&lang_sys=<?= $code ;?>"><?= $lang ;?></a></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>

            <div class="col-md-4 col-md-offset-4">
                <img src="<?= $logo; ?>" height="80" style="margin:10px auto; display:table">
                <div class="widget widget-green">
                    <div class="widget-title">
                        <h3 class="text-center"><i class="icon-lock"></i> <?= $trad_com_login['com_login'][$_SESSION['lang_sys']]; ?></h3>
                    </div>
                    <div class="widget-content">
                        <form method="post" action="components/com_login/controleur/login.php?task=doLogin" role="form"
                              id="loginForm">
                            <div class="msgbox"></div>
                            <div class="lined-separator"><?= $trad_com_login['SAISIR_LOGIN_PASSE'][$_SESSION['lang_sys']]; ?></div>
                            <div class="form-group relative-w">
                                <input type="text" name="login" class="form-control" placeholder="<?= $trad_com_login['LOGIN'][$_SESSION['lang_sys']]; ?>">
                                <i class="icon-user input-abs-icon"></i></div>
                            <div class="form-group relative-w">
                                <input type="password" name="password" class="form-control" placeholder="<?= $trad_com_login['MOT_DE_PASSE'][$_SESSION['lang_sys']]; ?>">
                                <i class="icon-lock input-abs-icon"></i></div>
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox">
                                        <?= $trad_com_login['SE_SOUVENIR'][$_SESSION['lang_sys']]; ?>
                                    </label>
                                </div>
                            </div>
                            <a href="#0" class="btn btn-primary btn-rounded btn-iconed login"> <span><?= $trad_com_login['CNX'][$_SESSION['lang_sys']]; ?></span> <i
                                        class="icon-arrow-right"></i>
                            </a>
                            <div class="no-account-yet"> <?= $trad_com_login['OUBLIER_PASSE'][$_SESSION['lang_sys']]; ?> <a
                                        href="index.php?option=com_login&task=forgotPass"><?= $trad_com_login['CLIQUEZ_ICI'][$_SESSION['lang_sys']]; ?></a>
                            </div>
                            <div class="no-account-yet"> <?= $trad_com_login['BACK_HOME'][$_SESSION['lang_sys']]; ?> <a
                                        href="<?= $siteURL;?>"><?= $siteURL;?></a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }

        function forgotPass()
        {
            global $siteURL, $trad_com_login;
            ?>
            <script type="text/javascript">
                $(document).ready(function () {
                    var succes = "<?= $trad_com_login['SUCCES'][$_SESSION['lang_sys']];?>";
                    var error = "<?= $trad_com_login['ERREUR'][$_SESSION['lang_sys']];?>";
                    var warning = "<?= $trad_com_login['ATTENTION'][$_SESSION['lang_sys']];?>";

                    $(".login").click(function (event) {
                        event.preventDefault();
                        $('form#loginForm').submit();
                    });

                    $('form#loginForm').ajaxForm({
                        beforeSubmit: function () {
                            //chargement
                            $("#loginForm .loading").show();
                        },
                        success: function (theResponse) {
                            var succes_msg = "<?= $trad_com_login['SUCCES_FORGOT'][$_SESSION['lang_sys']];?>";
                            var error_msg = "<?= $trad_com_login['ERREUR_FORGOT'][$_SESSION['lang_sys']];?>";
                            var warning_msg = "<?= $trad_com_login['ATTENTION_FORGOT'][$_SESSION['lang_sys']];?>";
                            var exec_msg = "<?= $trad_com_login['ERREUR_EXECUTION'][$_SESSION['lang_sys']];?>";

                            $("#loginForm .loading").hide();
                            if (parseInt(theResponse) == 1) {
                                $('#loginForm .msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>' + succes + '</strong> ' + succes_msg + '</div>');
                                setTimeout(function () {
                                    document.location = "index.php?option=com_login&task=resetPassword";
                                }, 1500)
                            }
                            else if (parseInt(theResponse) == 2) {
                                $('#loginForm .msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong> ' + error_msg + '</div>');
                                $('#loginForm .msgbox').slideDown();
                            }
                            else if (parseInt(theResponse) == 0) {
                                $('#loginForm .msgbox').html('<div class="alert alert-warning alert-dismissable"><i class="icon-exclamation-sign"></i> <strong>' + warning + '</strong> ' + warning_msg + '</div>');
                                $('#loginForm .msgbox').slideDown();
                            }
                             else{
                                 $('#loginForm .msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong> ' + exec_msg + '</div>');
                                 $('#loginForm .msgbox').slideDown();
                             }
                        }
                    });
                });
            </script>

        <?php
        $logo = "";
        $directory = "../images/config/";
        foreach (glob($directory . '*.*') as $file) {
            $logo = $file;
        }
        if ($logo == "") {
            if (file_exists("../images/cms-logo.png"))
                $logo = "../images/cms-logo.png";
        }
        ?>

            <div class="langue-sys">
                <ul>
                    <?php
                    $langs_sys = array('fr' => 'Français', 'en' => 'English');
                    foreach($langs_sys as $code => $lang){
                        $class = ($_SESSION['lang_sys'] == $code)? 'class = "active"' : '';
                        ?>
                        <li><a id="<?= $code ;?>" <?= $class ;?> href="<?= $siteURL ;?>hw-admin/index.php?option=com_login&task=forgotPass&lang_sys=<?= $code ;?>"><?= $lang ;?></a></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>

            <div class="col-md-4 col-md-offset-4">
                <img src="<?= $logo; ?>" height="80" style="margin:10px auto; display:table">
                <div class="widget widget-green">
                    <div class="widget-title">
                        <h3 class="text-center"><i class="icon-lock"></i>  <?= $trad_com_login['CODE_SECURITE'][$_SESSION['lang_sys']]; ?></h3>
                    </div>
                    <div class="widget-content">
                        <form method="post" action="components/com_login/controleur/login.php?task=restaurePass"
                              role="form"
                              id="loginForm">
                            <div class="msgbox"></div>
                            <div class="lined-separator"> <?= $trad_com_login['SAISIR_E_MAIL'][$_SESSION['lang_sys']]; ?></div>
                            <div class="form-group relative-w">
                                <input type="text" name="email" class="form-control" placeholder=" <?= $trad_com_login['E_MAIL'][$_SESSION['lang_sys']]; ?>">
                                <i class="icon-user input-abs-icon"></i>
                            </div>
                            <a href="#0" class="btn btn-primary btn-rounded btn-iconed login">
                                <span> <?= $trad_com_login['ENVOYER'][$_SESSION['lang_sys']]; ?></span> <i class="icon-arrow-right"></i>
                            </a>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }

        function resetPassword()
        {
            global $siteURL, $trad_com_login;
            ?>
            <script type="text/javascript">
                $(document).ready(function () {
                    var succes = "<?= $trad_com_login['SUCCES'][$_SESSION['lang_sys']];?>";
                    var error = "<?= $trad_com_login['ERREUR'][$_SESSION['lang_sys']];?>";
                    var warning = "<?= $trad_com_login['ATTENTION'][$_SESSION['lang_sys']];?>";

                    $(".login").click(function (event) {
                        event.preventDefault();
                        $('form#loginForm').submit();
                    });

                    $('form#loginForm').ajaxForm({
                        beforeSubmit: function () {
                            //chargement
                            $("#loginForm .loading").show();
                        },
                        success: function (theResponse) {
                            var succes_msg = "<?= $trad_com_login['SUCCES_RESET'][$_SESSION['lang_sys']];?>";
                            var error_msg = "<?= $trad_com_login['ERREUR_RESET'][$_SESSION['lang_sys']];?>";
                            var warning_msg = "<?= $trad_com_login['ATTENTION_RESET'][$_SESSION['lang_sys']];?>";
                            var exec_msg = "<?= $trad_com_login['ERREUR_EXECUTION'][$_SESSION['lang_sys']];?>";

                            $("#loginForm .loading").hide();
                            if (parseInt(theResponse) == 1) {
                                $('#loginForm .msgbox').html('<div class="alert alert-success alert-dismissable"><i class="icon-check-sign"></i> <strong>' + succes + '</strong> ' + succes_msg + '</div></div>');
                                setTimeout(function () {
                                    document.location = "index.php?option=com_login&task=login";
                                }, 1500)
                            }
                            else if (parseInt(theResponse) == 2) {
                                $('#loginForm .msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong> ' + error_msg + '</div>');
                                $('#loginForm .msgbox').slideDown();
                            }
                            else if (parseInt(theResponse) == 0) {
                                $('#loginForm .msgbox').html('<div class="alert alert-warning alert-dismissable"><i class="icon-exclamation-sign"></i> <strong>' + warning + '</strong> ' + warning_msg + '</div>');
                                $('#loginForm .msgbox').slideDown();
                            }
                             else{
                                 $('#loginForm .msgbox').html('<div class="alert alert-danger alert-dismissable"><i class="icon-remove-sign"></i> <strong>' + error + '</strong> ' + exec_msg + '</div>');
                                 $('#loginForm .msgbox').slideDown();
                             }
                        }
                    });
                });
            </script>
        <?php
        $logo = "";
        $directory = "../images/config/";
        foreach (glob($directory . '*.*') as $file) {
            $logo = $file;
        }
        if ($logo == "") {
            if (file_exists("../images/cms-logo.png"))
                $logo = "../images/cms-logo.png";
        }
        ?>

            <div class="langue-sys">
                <ul>
                    <?php
                    $langs_sys = array('fr' => 'Français', 'en' => 'English');
                    foreach($langs_sys as $code => $lang){
                        $class = ($_SESSION['lang_sys'] == $code)? 'class = "active"' : '';
                        ?>
                        <li><a id="<?= $code ;?>" <?= $class ;?> href="<?= $siteURL ;?>hw-admin/index.php?option=com_login&task=resetPassword&lang_sys=<?= $code ;?>"><?= $lang ;?></a></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>

            <div class="col-md-4 col-md-offset-4">
                <img src="<?= $logo; ?>" height="80" style="margin:10px auto; display:table">
                <div class="widget widget-green">
                    <div class="widget-title">
                        <h3 class="text-center"><i class="icon-lock"></i>  <?= $trad_com_login['RESTAURATION_PASSE'][$_SESSION['lang_sys']]; ?></h3>
                    </div>
                    <div class="widget-content">
                        <form method="post" action="components/com_login/controleur/login.php?task=newPass" role="form"
                              id="loginForm">
                            <div class="msgbox"></div>
                            <div class="lined-separator"> <?= $trad_com_login['SAISIR_CODE'][$_SESSION['lang_sys']]; ?>
                            </div>
                            <div class="form-group relative-w">
                                <input type="text" name="code" class="form-control" placeholder="<?= $trad_com_login['CODE'][$_SESSION['lang_sys']]; ?>">
                                <i class="icon-user input-abs-icon"></i>
                            </div>
                            <div class="form-group relative-w">
                                <input type="password" name="password" class="form-control"
                                       placeholder="<?= $trad_com_login['NV_MOT_DE_PASSE'][$_SESSION['lang_sys']]; ?>">
                                <i class="icon-lock input-abs-icon"></i>
                            </div>
                            <a href="#0" class="btn btn-primary btn-rounded btn-iconed login">
                                <span><?= $trad_com_login['ENVOYER'][$_SESSION['lang_sys']]; ?></span> <i class="icon-arrow-right"></i>
                            </a>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        }

        ?>
    </div>
</div>
</body>
</html>