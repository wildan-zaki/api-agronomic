<style>
	._rwl8x{
		-webkit-box-orient: horizontal;
		-webkit-box-direction: normal;
		-ms-flex-direction: row;
		flex-direction: row;
		-webkit-box-pack: center;
		-ms-flex-pack: center;
		justify-content: center;	
	}
	.appstore img,.playstore img{height:40px;}
	
	@media only screen and (max-width: 767px) {
		
	}
</style>

<div class="text-center">
<div class="text-center"><a href="<?=site_url();?>"><img class="img-responsive" src="<?=backend_assets('img/')?>logo2.png?v=1.0" title="googaga" alt="googaga" style="margin:0 auto;"/></a></div>

<div id="body">
    <p>Get the app.</p>
    <p>adam</p>
    <li><a class="icon-signout" <?php echo anchor('backend/login/logout','Logout'); ?>  </a>
                            
                            </li>
    <div class="row">
		<div class="_rwl8x col-xs-12 col-sm-8 col-sm-push-1">
            <div class="appstore col-xs-6 col-sm-8">
                <a href="appstore" class="pull-right"><img src="<?=base_url()?>assets/media/static/app-store.png" class="img-responsive"/></a>
            </div>
            <div class="playstore">
                <a href="playstore"><img src="<?=base_url()?>assets/media/static/play-store.png" class="img-responsive"/></a>
            </div>
		</div>
    </div>
</div>

</div>