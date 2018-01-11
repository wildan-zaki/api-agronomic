<table cellpadding="0" cellspacing="0" align="center" width="80%" border="0" style="border-spacing: 0;border-collapse: collapse;vertical-align: top;margin-top:20px;margin-bottom:20px;margin-left:20px;margin-right:20px;">
	<tbody>
    	<tr>
        	<td style="padding-top:10px;padding-bottom:10px;" width="30%">
            	<p style="margin: 0;font-size: 14px;line-height: 17px;text-align: left;color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;"><strong>E-ticket Code</strong></p>
            </td>
        	<td style="padding-top:10px;padding-bottom:10px;" width="70%">
            	<p style="color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;margin: 0;font-size: 14px;line-height: 17px;text-align: left"><?=$code?></p>
      		</td>
        </tr>
    	<tr>
        	<td style="padding-top:10px;padding-bottom:10px;" width="30%">
            	<p style="margin: 0;font-size: 14px;line-height: 17px;text-align: left;color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;"><strong>Activity Title</strong></p>
            </td>
   	  <td style="padding-top:10px;padding-bottom:10px;" width="70%">
            	<p style="color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;margin: 0;font-size: 14px;line-height: 17px;text-align: left"><?=$name?></p>
      		</td>
        </tr>
  		<?php if(!empty($merchant)){ ?>
            <tr>
                <td style="padding-top:10px;padding-bottom:10px;" width="30%">
                    <p style="margin: 0;font-size: 14px;line-height: 17px;text-align: left;color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;"><strong>Merchant</strong></p>
                </td>
          <td style="padding-top:10px;padding-bottom:10px;" width="70%">
                    <p style="color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;margin: 0;font-size: 14px;line-height: 17px;text-align: left"><?=$merchant?></p>
                </td>
            </tr>
        <?php } ?>
        <?php if(!empty($date)){ ?>
            <tr>
                <td style="padding-top:10px;padding-bottom:10px;" width="30%">
                    <p style="margin: 0;font-size: 14px;line-height: 17px;text-align: left;color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;"><strong>Date</strong></p>
                </td>
          <td style="padding-top:10px;padding-bottom:10px;" width="70%">
                    <p style="color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;margin: 0;font-size: 14px;line-height: 17px;text-align: left"><?=$date?></p>
                </td>
            </tr>
        <?php } ?>
        <?php if(!empty($time)){ ?>
            <tr>
                <td style="padding-top:10px;padding-bottom:10px;" width="30%">
                    <p style="margin: 0;font-size: 14px;line-height: 17px;text-align: left;color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;"><strong>Time</strong></p>
                </td>
          <td style="padding-top:10px;padding-bottom:10px;" width="70%">
                    <p style="color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;margin: 0;font-size: 14px;line-height: 17px;text-align: left"><?=$time?></p>
                </td>
            </tr>
        <?php } ?>
        <?php if(!empty($location)){ ?>
            <tr>
                <td style="padding-top:10px;padding-bottom:10px;" width="30%">
                    <p style="margin: 0;font-size: 14px;line-height: 17px;text-align: left;color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;"><strong>Location</strong></p>
                </td>
          <td style="padding-top:10px;padding-bottom:10px;" width="70%">
                    <p style="color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;margin: 0;font-size: 14px;line-height: 17px;text-align: left"><?=$location?></p>
                </td>
            </tr>
        <?php } ?>
    	<tr>
        	<td style="padding-top:10px;padding-bottom:10px;" width="30%">
            	<p style="margin: 0;font-size: 14px;line-height: 17px;text-align: left;color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;"><strong>Participant</strong></p>
            </td>
        	<td style="padding-top:10px;padding-bottom:10px;" width="70%">
            	<p style="color:#555555;line-height:120%;font-family:Arial, 'Helvetica Neue', Helvetica, sans-serif;margin: 0;font-size: 14px;line-height: 17px;text-align: left"><?=$participant['name']?> - <?=$participant['ticket']?></p>
      		</td>
        </tr>
    </tbody>
</table>