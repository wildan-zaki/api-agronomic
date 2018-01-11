<h1 class="page-header"><?=$title?></h1>
<?php if(!empty($main['top'])){ ?>
<?=$main['top']?>
<!--<div class="row placeholders">
<div class="col-xs-6 col-sm-3 placeholder">
  <img src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="200" height="200" class="img-responsive" alt="Generic placeholder thumbnail">
  <h4>Label</h4>
  <span class="text-muted">Something else</span>
</div>
<div class="col-xs-6 col-sm-3 placeholder">
  <img src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="200" height="200" class="img-responsive" alt="Generic placeholder thumbnail">
  <h4>Label</h4>
  <span class="text-muted">Something else</span>
</div>
<div class="col-xs-6 col-sm-3 placeholder">
  <img src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="200" height="200" class="img-responsive" alt="Generic placeholder thumbnail">
  <h4>Label</h4>
  <span class="text-muted">Something else</span>
</div>
<div class="col-xs-6 col-sm-3 placeholder">
  <img src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" width="200" height="200" class="img-responsive" alt="Generic placeholder thumbnail">
  <h4>Label</h4>
  <span class="text-muted">Something else</span>
</div>
</div>-->
<?php } ?>

<?php if(!empty($main['section_title'])){ ?>
<h3 class="sub-header"><?=$main['section_title']?></h3>
<?php } ?>

<?php if(!empty($main['list'])){ ?>
<?=$main['list']?>
<?php } ?>