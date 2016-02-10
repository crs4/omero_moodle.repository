<?php

// Copyright (c) 2015-2016, CRS4
//
// Permission is hereby granted, free of charge, to any person obtaining a copy of
// this software and associated documentation files (the "Software"), to deal in
// the Software without restriction, including without limitation the rights to
// use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
// the Software, and to permit persons to whom the Software is furnished to do so,
// subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in all
// copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
// FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
// COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
// IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
// CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

/**
 * Embedded Ome-Seadragon viewer using HTML iframe
 *
 * @since      Moodle 2.0
 * @package    repository_omero
 * @copyright  2015-2016 CRS4
 * @licence    https://opensource.org/licenses/mit-license.php MIT licence
 */

// Moodle ROOT directory
$MOODLE_ROOT = dirname(__FILE__) . "/../../../";
// Include Moodle configuration
require_once("$MOODLE_ROOT/config.php");

//
defined('MOODLE_INTERNAL') || die();

// check whether the user is logged
if (!isloggedin()) {
    $moodle_url = "http://" . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "/moodle";
    header('Location: ' . $moodle_url);
}

// get the image server address (from the repository configuration)
$IMAGE_SERVER = get_config('omero', 'omero_restendpoint');

// set the ID of the viewer container
$IMAGE_VIEWER_CONTAINER = "openseadragon_viewer";

// Read parameters from the actual URL
$imageId = $_GET['id'];
$frameId = $_GET['frame'];
$width = !empty($_GET['width']) ? $_GET['width'] : "100%";
$height = !empty($_GET['height']) ? $_GET['height'] : "100%";
$showRoiTable = isset($_GET['showRoiTable']) ? $_GET['showRoiTable'] : "false";
$visibleRoiList = isset($_GET['visibleRois']) ? $_GET['visibleRois'] : "";


$imageParamKeys = ["m", "p", "ia", "q", "t", "z", "zm", "x", "y"];
$imageParams = array();
foreach ($imageParamKeys as $paramName) {
    if (isset($_REQUEST[$paramName]))
        $imageParams[$paramName] = $_REQUEST[$paramName];
}
$imageParamsJs = "?" . implode('&',
        array_map(function ($v, $k) {
            return $k . '=' . $v;
        }, $imageParams, array_keys($imageParams)));

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <title>Embedded OPENSEADRAGON Viewer for Moodle</title>

    <!-- Link to the StyleSheet -->
    <link rel="stylesheet" type="text/css" href="./styles.css">

    <!-- Bootstrap CSS -->
    <!--<link href="/moodle/repository/omero/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">-->

    <!-- JQuery/Bootstrap table CSS -->
    <!--<link href="https://cdn.datatables.net/1.10.7/css/jquery.dataTables.css" rel="stylesheet">-->

    <!-- ImageViewerController -->
    <script type="text/javascript" src="/moodle/repository/omero/viewer/viewer-controller.js"></script>

    <!-- ImageModelManager -->
    <script type="text/javascript" src="/moodle/repository/omero/viewer/viewer-model.js"></script>

    <!-- OME_SEADRAGON dependencies -->
    <script src="<?php echo $IMAGE_SERVER ?>/static/ome_seadragon/js/openseadragon.min.js"></script>
    <script src="<?php echo $IMAGE_SERVER ?>/static/ome_seadragon/js/jquery-1.11.3.min.js"></script>
    <script src="<?php echo $IMAGE_SERVER ?>/static/ome_seadragon/js/paper-full.min.js"></script>
    <script src="<?php echo $IMAGE_SERVER ?>/static/ome_seadragon/js/ome_seadragon.min.js"></script>
    <script src="<?php echo $IMAGE_SERVER ?>/static/ome_seadragon/js/openseadragon-scalebar.min.js"></script>
    <!-- Bootstrap -->
    <!--<script type="text/javascript" src="/moodle/repository/omero/libs/bootstrap/js/bootstrap.min.js"></script>-->

    <!-- JQuery/Bootstrap table integration -->
    <!--<script type="text/javascript" src="https://cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>-->
    <!--<script type="text/javascript"
            src="https://cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.js"></script>-->


    <script type="text/javascript">

        $(document).ready(function () {

            $.ajaxSettings.cache = false;

            // builds the ImageViewerController
            var viewer_ctrl = new ImageViewerController("<?= $IMAGE_SERVER ?>", "<?= $frameId ?>",
                "<?= $IMAGE_VIEWER_CONTAINER ?>", "rois-table", "roi_thumb_popup", "<?= $imageId ?>",
                "<?= $showRoiTable ?>", "<?= $imageParamsJs ?>", "<?= $visibleRoiList ?>");
            // binds the controller to the window object as image_viewer_controller
            window.omero_repository_image_viewer_controller = viewer_ctrl;
        });

    </script>
</head>
<body>

<div id="graphics_container" class="container">
    <div id="<?= $IMAGE_VIEWER_CONTAINER ?>"
         style="position: absolute; width: <?= $width ?>; height: <?= $height ?>"></div>
    <canvas id="annotations_canvas" style="position: absolute; width: <?= $width ?>; height: <?= $height ?>"></canvas>
</div>

<!-- FIXME: Static table example: the table has to be dynamically generated -->
<!--<img id="roi_thumb_popup" style="border: 1px solid rgb(187, 187, 187); display: none; left: 202px; top: 78px;" src="">-->

<?php if ($showRoiTable == "true") { ?>

    <div id="rois-table-container" class="panel panel-default"
         style="margin-top: <?= str_replace("px", "", $height) + 50 ?>px;font-size: 12pt;">

        <!-- Default panel contents -->
        <div class="panel-heading">ROI Shapes Inspector</div>

        <div>
            <table id="rois-table" class="display" cellspacing="10" width="100%"></table>
        </div>
    </div>
<?php } ?>

</body>
</html>