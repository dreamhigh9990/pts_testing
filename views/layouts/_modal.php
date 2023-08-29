<!-- Modal to use all picker in location section taxonomy -->
<div class="modal fade text-left" id="taxo-location-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel8" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
    	<div class="modal-content">
        	<div class="modal-header bg-primary white">
            	<h4 class="modal-title" id="myModalLabel8"><?= Yii::$app->trans->getTrans('Set Geo Location'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body body-start">
                <input type="text" class="form-control mb-2 default-address-taxo-loc" id="default-address" />
                <div id="us3" class="taxo-loc-picker-container"></div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
            	<button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal to use all picker in pipe section -->
<div class="modal fade text-left" id="geo-location-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel8" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
    	<div class="modal-content">
        	<div class="modal-header bg-primary white">
            	<h4 class="modal-title" id="myModalLabel8"><?= Yii::$app->trans->getTrans('Set Geo Location'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body body-start">
                <input type="text" class="form-control mb-2 default-address-single-from" id="default-address" />
                <div id="us3" class="geo-map-container geo-start-map"></div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-body body-end">
                <input type="text" class="form-control mb-2 default-address-single-to" id="default-address" />
                <div id="us3" class="geo-map-container geo-end-map"></div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
            	<button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal to use picker in pipe section while double location select -->
<div class="modal fade text-left" id="geo-location-twice-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel8" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
    	<div class="modal-content">
        	<div class="modal-header bg-primary white">
            	<h4 class="modal-title" id="myModalLabel8"><?= Yii::$app->trans->getTrans('Set Geo Location'); ?></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body body-start">
                <input type="text" class="form-control mb-2 default-address-from" id="default-address" />
                <div id="us3" class="geo-map-container geo-start-map-twice"></div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-body body-end">
                <input type="text" class="form-control mb-2 default-address-to" id="default-address" />
                <div id="us3" class="geo-map-container geo-end-map-twice"></div>
                <div class="clearfix"></div>
            </div>
            <div class="modal-footer">
            	<button type="button" class="btn btn-outline-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="print-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel5" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="header-titleprint">Print Selected Items</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
        <div class="modal-body" id="print-body">
        </div>    
        <div class="modal-footer">
            <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-outline-primary" onClick="printDiv();">Print</button>
        </div>
    </div>
</div>


