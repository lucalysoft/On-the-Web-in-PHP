<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $survey->name ?></title>

    <link href="themes/SuiteP/css/bootstrap.min.css" rel="stylesheet">
    <link href="custom/include/javascript/rating/rating.min.css" rel="stylesheet">
    <link href="custom/include/javascript/datetimepicker/jquery-ui-timepicker-addon.css" rel="stylesheet">
    <link href="include/javascript/jquery/themes/base/jquery.ui.all.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <img height=100 src="survey_logo.jpg"/>
        </div>
    </div>
    <div class="row well">
        <div class="col-md-offset-2 col-md-8">
            <h1><?= $survey->name ?></h1>
            <p>Thanks for completing this survey.</p>
        </div>
    </div>
</div>
<script src="include/javascript/jquery/jquery-min.js"></script>
<script src="include/javascript/jquery/jquery-ui-min.js"></script>
</body>
</html>