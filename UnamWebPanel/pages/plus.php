<?php
require_once dirname(__DIR__, 1) . '/assets/php/templates.php';
require_once dirname(__DIR__, 1) . '/assets/php/security.php';
?>
<!DOCTYPE html>
<html lang="<?= $langID ?>">

<head>
    <?php include dirname(__DIR__) . '/assets/php/scripts.php'; ?>
    <?php include dirname(__DIR__) . '/assets/php/styles.php'; ?>
    <title>Unam Web Panel+ &mdash; Plus</title>
</head>
<body class="dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <?php include dirname(__DIR__) . '/assets/php/navbar.php'; ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2"></div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="main-content">
                        <section class="section">
                            <div class="section-body">
                                <h1>Total Hashrate</h1>
                                <?php include("../plus/totalHashrate/totalHashrate.php"); ?>
                                <h1>Statistics</h1>
                                <?php include("../plus/statistics/statsViewer.php"); ?>
                                <h1>Geo</h1>
                                <?php include("../plus/geo/geo.php"); ?>
                            </div>
                        </section>
                    </div>
                </div>
            </section>
        </div>
        <?php include dirname(__DIR__) . '/assets/php/footer.php'; ?>
    </div>

</body>

</html>