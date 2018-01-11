			</div> <!-- .main -->
        </div> <!-- .row -->
    </div> <!-- .container-fluid -->
    
    <?php if($slug=='event'){ ?>
        <div id="fullCalModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post" id="formModal">
                        <input type="hidden" name="action" value="bulk_update" />
                        <input type="hidden" name="event_id" id="hiddenEventID">
                        <input type="hidden" name="schedule_id" id="hiddenScheduleID">
                        <input type="hidden" name="start_date" id="hiddenStartDate">
                        <input type="hidden" name="end_date" id="hiddenEndDate">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                            <h4 id="modalTitle" class="modal-title"></h4>
                        </div>
                        <div id="modalBody" class="modal-body"></div>
                        <div class="modal-footer" style="text-align:center;">
                            <button type="button" class="btn btn-default" data-dismiss="modal">CANCEL</button>
                            <button type="submit" class="btn btn-primary btn-save">SAVE</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <table id="newTicket" class="table table-striped table-hover hidden">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th>Ticket Name</th>
                    <th>Normal Price</th> 
                    <th>Special Price</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="new-ticket">
                <tr>
                    <td><a href="javascript:void(0);" class="remove-ticket"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>
                    <td>
                        <input type="hidden" value="" name="ticketID[]" class="ticket_id">
                        <input type="text" class="form-control ticket_name" name="ticketName[]" placeholder="Name">
                    </td>
                    <td><input type="text" class="form-control ticket_price" name="ticketPrice[]" placeholder="0 = free"></td>
                    <td><input type="text" class="form-control ticket_special_price" name="ticketSpecialPrice[]"  placeholder="0 = free"></td>
                    <td><input type="text" class="form-control ticket_qty" name="ticketQty[]" placeholder="Stock"></td>
                    <td><button class="btn btn-primary update-ticket" data-action="add">ADD</button></td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="text-center">
                    <td colspan="6"><button class="btn btn-primary add-ticket" type="button">Add Ticket</button></td>
                </tr>
            </tfoot>
        </table>
	<?php } ?>     
    <?php if($slug=='user'){ ?>   
        <table id="newChild" class="table table-striped table-hover hidden">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th width="15%">Picture</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Birthdate</th>
                </tr>
            </thead>
            <tbody class="new-child">
                <tr>
                    <td><a href="javascript:void(0);" class="remove-child hidden"><span class="glyphicon glyphicon-remove" aria-hidden="true" style="color:red"></span></a></td>
                    <td>
                    	<label for="childImage" style="cursor:pointer" data-toggle="tooltip" data-placement="bottom" title="Click to change child image"><img src="<?=backend_assets('img/')?>no-image.png" class="img-responsive"/><input type="file" id="childImage#n" name="childImage#n" class="hidden childImage"></label>
                    </td>
                    <td>
                        <input type="hidden" name="childID[]" class="child_id"/>
                        <input type="text" class="form-control first_name" name="childFirstName[]" placeholder="Firstname"/>
                    </td>
                    <td><input type="text" class="form-control last_name" name="childLastName[]" placeholder="Lastname"/></td>
                    <?php if(!empty($main['genders'])){ ?>
                        <td>
                            <select name="childGender[]" class="form-control gender">
                            <?php foreach($main['genders'] as $gender){ ?>
                                <option><?=$gender?></option>
                            <?php } ?>
                            </select>
                        </td>
                    <?php } ?>
                    <td><input type="text" class="form-control child_birthdate datepicker" name="childBirthdate[]" placeholder="dd/mm/yyyy"></td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="text-center">
                    <td colspan="6"><button class="btn btn-primary add-child" type="button">Add Child</button></td>
                </tr>
            </tfoot>
        </table>
	<?php } ?>
    <?php if($slug=='order'){ ?>  
        <table id="newService" class="table table-striped table-hover hidden">
            <tr class="hover-list">
              <td><input class="form-control " name="vehicleCc[]" placeholder="" type="text"></td>
              <td>
                  <select name="vehicleTransm[]" class="form-control">
                    <option value="">Select Transmission Below</option>
                    <option value="AT">Automatic</option>
                    <option value="MT">Manual</option>
                  </select>
              </td>
              <td>
                  <div class="helper-hover hidden"><button class="btn remove-meta" data-vehicle-id="" data-meta-id =""><span class="glyphicon glyphicon-remove" style="color:red"></span></button></div>
              </td>
            </tr>
        </table>
    <?php } ?>   
    <?php if($slug=='vehicle'){ ?>   
        <table id="newTransm" class="table table-striped table-hover hidden">
                <tbody class="new-transm">
                    <tr class="hover-list">
                      <td><input class="form-control " name="vehicleCc[]" placeholder="" type="text"></td>
                      <td>
                          <select name="vehicleTransm[]" class="form-control">
                            <option value="">Select Transmission Below</option>
                            <option value="AT">Automatic</option>
                            <option value="MT">Manual</option>
                          </select>
                      </td>
                      <td>
                          <div class="helper-hover hidden"><button class="btn remove-meta" data-vehicle-id="" data-meta-id =""><span class="glyphicon glyphicon-remove" style="color:red"></span></button></div>
                      </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="text-center"> 
                        <td colspan="6"><button class="btn btn-primary add-transm" type="button">Add Transmission</button></td>
                    </tr>
                </tfoot>
                </table>

                <table id="newYear" class="table table-striped table-hover hidden">
                <tbody class="new-year">
                      <tr class="order-list hover-list">
                          <td><input class="form-control " name="vehicleYear[]" placeholder="" type="text"></td>
                          <td><div class="helper-hover hidden"><button class="btn remove-meta"><span class="glyphicon glyphicon-remove" style="color:red"></span></button></div></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="text-center">
                        <td colspan="6"><button class="btn btn-primary add-year" type="button">Add Year</button></td>
                    </tr>
                </tfoot>
                </table>

                <table id="newColor" class="table table-striped table-hover hidden">
                <tbody class="new-color">
                      <tr class="order-list hover-list">
                          <td><input class="form-control " name="vehicleColor[]" placeholder="" type="text"></td>
                          <td><div class="helper-hover hidden"><button class="btn remove-meta"><span class="glyphicon glyphicon-remove" style="color:red"></span></button></div></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="text-center">
                        <td colspan="6"><button class="btn btn-primary add-color" type="button">Add Color</button></td>
                    </tr>
                </tfoot>
                </table>
    <?php } ?>     
    <?php if($slug=='coupon'){ 
		$conditions = unserialize(COUPON_CONDITIONS_TYPE);
		$operators = array('>=' => 'Greater Than', '==' => 'Equal', '<' => 'Lower Than');
	?>
    	<table id="newCoupon" class="table table-striped table-hover hidden">
            <tbody class="new-coupon">
                <tr>
                    <td><a href="javascript:void(0);" class="remove-coupon"><span class="glyphicon glyphicon-remove" aria-hidden="true" style="color:red"></span></a></td>
                    <td>
                    <?php if(!empty($conditions)){ ?>
                        <select class="form-control select-conditions" name="condition_type[]">
                            <?php foreach($conditions as $cond => $value){ ?>
                                <option value="<?=$cond?>"><?=$value?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>		
                    </td>
                    <td>
                     <?php if(!empty($operators)){ ?>
                        <select class="form-control select-operators" name="condition_operator[]">
                            <?php foreach($operators as $op => $operator){ ?>
                                <option value="<?=$op?>"><?=$operator?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>	
                    </td>
                    <td width="40%">
                    	<input type="text" class="form-control cond-input-value" name="condition_value[]" placeholder="100">
                        <select class="form-control hidden select-value" name="condition_value_select[]">
                        </select>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="text-center">
                    <td colspan="6"><button class="btn btn-primary add-coupon" type="button">Add Coupon</button></td>
                </tr>
            </tfoot>
        </table>
    <?php }
	if($slug=='setting'){ ?>
    	<div id="newFee" class="hidden">
        	<div class="new-fee">
            	<div class="clearfix add-clearfix-new" style="margin-bottom:10px;"></div>
            	<div class="col-sm-4 add-label-new">&nbsp;</div>
            	<div class="col-sm-2 add-name-new">
                	<input type="text" name="additional_name[]" placeholder="additional name" class="form-control"/>
                </div>
            	<div class="col-sm-2 add-value-new">
                	<input type="text" name="additional_value[]" placeholder="15000" class="form-control"/>
                </div>
                <div class="col-sm-4 add-remove-new" id="addFee" data-remove="">
	                <button class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span></button>
                </div>
            </div>
        </div>
    <?php } 
	
	if(strpos($slug,'setting/copywriting')!==FALSE){ 
	?>
    
    <div id="newPayment" class="hidden">
        <div class="new-payment">
            <br /><br />
            <input type="text" class="form-control add-payment-prefix-new" id="settingPaymentGateway" name="settingPaymentGateway[]" placeholder="payment_gateway_prefix" value=""><br>
            <textarea rows="5" name="settingPaymentGatewayCopy[]" class="form-control add-payment-copy-new payment-copy" placeholder="Cara Pembayaran"></textarea><p class="help-block">use <strong>##bankcode##</strong>, <strong>##banknumber##</strong> to replace with real number from payment gateway</p><br>
            <button class="btn btn-primary add-payment-remove-new" id="addPayment" data-remove=""><span class="glyphicon glyphicon-plus"></span>Add Another</button>
        </div>
    </div>
     <?php } ?>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>