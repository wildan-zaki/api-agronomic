<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Administration Page <?=$title?> - GOkleen.co.id</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <?php if(!empty($main['detail']) && strpos($main['detail'],"with-calendar")!==FALSE){ ?>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.0/moment.min.js"></script>
    <script src="<?=backend_assets('js/')?>fullcalendar.min.js"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css" rel="stylesheet">
    <?php } ?>
    <?php if(!empty($main['detail']) && strpos($main['detail'],'with-map')!==FALSE){ ?>
    	<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyBc39KLut5SORkfiE0bZ8a8iE9jjxJuKyA&libraries=places" async defer></script>
    <?php } ?>
    <script src="<?=backend_assets('js/')?>general.js?v=0.0.1"></script>
    <?php if(!empty($main['detail'])){ ?>	
    	<script src="<?=backend_assets('js/')?>general-detil.js?v=0.0.1"></script>
    <?php } ?>
    <?php echo $slug; ?>
	<?php switch($slug){ 
        case 'brand':?>
            <script src="<?=backend_assets('js/')?>brand.js?v=0.0.1"></script>
    <?php break; 
		case 'vehicle':?>
            <script src="<?=backend_assets('js/')?>vehicle.js?v=0.0.1"></script>
    <?php break;
        case 'service':?>
            <script src="<?=backend_assets('js/')?>service.js?v=0.0.1"></script>
    <?php break;
		case 'order':?>
            <script src="<?=backend_assets('js/')?>order.js?v=0.0.1"></script>
    <?php break;
        case 'subservice':?>
            <script src="<?=backend_assets('js/')?>order.js?v=0.0.1"></script>
    <?php break; 
		case 'crew':?>
            <script src="<?=backend_assets('js/')?>crew.js?v=0.0.1"></script>
    <?php break;
        case 'coupon':?>
            <script src="<?=backend_assets('js/')?>coupon.js?v=0.0.1"></script>
    <?php break; 
		case 'fleet':?>
            <script src="<?=backend_assets('js/')?>fleet.js?v=0.0.1"></script>
    <?php break;
        case 'user':?>
            <script src="<?=backend_assets('js/')?>user.js?v=0.0.1"></script>
    <?php break;
        case 'slider':?>
            <script src="<?=backend_assets('js/')?>slider.js?v=0.0.1"></script>
    <?php break; 
		default:
		if(strpos($slug,'setting/category')!==FALSE || strpos($slug,'setting/interest')!==FALSE) {?>
        	<script src="<?=backend_assets('js/')?>farbtastic/farbtastic.js?v=0.0.1"></script>
            <link rel="stylesheet" href="<?=backend_assets('js/')?>farbtastic/farbtastic.css?v=0.0.1">
        <?php 
		} 
		elseif(strpos($slug,'setting/copywriting')!==FALSE) {?>
         	<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
	<?php 
		}
		if(strpos($slug,'service')!==FALSE) {?>
			<script src="<?=backend_assets('js/')?>service.js?v=0.0.1"></script>	
	<?php	}
		break; } ?>
    <script type="text/javascript">
		no_image = '<?=backend_assets('media/uploads/')?>no-image.png';
		curSlug = '<?=$slug?>';
		backend_url = '<?=backend_url()?>';
		<?php if(!empty($main['detail'])){ ?>
			detail_page = true;
		<?php } ?>		
		<?php if(!empty($main['defaultDate'])){ ?>
			defaultDate = '<?=$main['defaultDate']?>';
		<?php } ?>
		<?php if(!empty($main['vehiclelists'])){ ?>
			vehiclelists = <?=$main['vehiclelists']?>;
		<?php } ?>		
    </script>
    <link href="<?=backend_assets('css/')?>style.css?v=0.0.1" rel="stylesheet">
    <link href="<?=backend_assets('css/')?>dashboard.css?v=0.0.1" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand text-center" href="<?=backend_url('dashboard')?>"><b>GO</b>KLEEN</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <!--<li><a href="#">Dashboard</a></li>
            <li><a href="#">Settings</a></li>-->
            <li><a href="<?=backend_url('logout')?>">Logout</a></li>
          </ul>
          <form class="navbar-form navbar-right" action="<?=backend_url($slug)?>" method="get">
            <input type="text" class="form-control" placeholder="Search..." name="s">
            <?php if(!empty($_SERVER['QUERY_STRING'])){ 
				$queries = explode("&",$_SERVER['QUERY_STRING']);
				if(!empty($queries)){ 
					foreach($queries as $query){
						$sp = explode("=",$query);
						if(trim($sp[0])!='s'){
							if(!empty($sp[1])) ?>
							<input type="hidden" name="<?=$sp[0]?>" value="<?=$sp[1]?>">
                        <?php } ?>
            		<?php } ?>
            	<?php } ?>
            <?php } ?>
          </form>
        </div>
      </div>
    </nav>
    
    <div class="container-fluid">
		<div class="row">
			<div class="sidebar col-sm-3 col-md-2">
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                	<?=$menu?>
                </div>       
            </div>
        	<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">