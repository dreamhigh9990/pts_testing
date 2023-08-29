var messages  = {
    pipeanomally:"This Pipe Number was already taken.Are you Sure want to override?",
    signSelected:"Please select atleast one item",
    imgRemove:"You will not be able to recover this data once you delete.",
    selectImage:"Please select image to delete.",
    deleteMultiple:"You will not be able to recover this data once you delete.",
    saved:"Your data has been saved.",
    wrong:"Something went wrong",
    Please:"Please!",
    sure:"Are you sure?",
    deleted:"Deleted!",
    msgDeleted:"Your image has been deleted.",
    rawDeleted:"Your row has been deleted.",
    success:"Success!",
    oops:"Oops!",
    kpRangeWarning: "KP has not been cleared. Please submit Clear & Grade report first",
    outcomeWarning: "Cutting Out this Weld will remove it from the system.\n Are you sure?"
};

//document ready end here//
var call = function(data, callback) {
    var callTry = function(data, callback) {
        data.params._csrf = _csrf;
        var DATA = data.params;
        // console.log(baseurl)
        var ajxOpts = {
            url: baseurl + data.url,
            data: DATA,
            dataType: 'json',
            crossDomain: true,
            cache: false,
            type: (typeof data.type != 'undefined' ? data.type : 'Post'),
        };
        $.ajax(ajxOpts).done(function(res) {
            $('.pjax-loader').hide();
            callback(res);
        }).fail(function(r) {
            $('.pjax-loader').hide();
            toastr.error('Your server request has been failed :'+baseurl + data.url,messages.oops)
        });
    }
    callTry(data, callback);
}

function initMap(globalGeo){	
    if(globalGeo.length > 0){
        lat = globalGeo[0];
        long = globalGeo[1];    
    
        $('.geo-start-map').locationpicker({
            location: {
                latitude: lat,
                longitude: long
            },
            radius: 300,
            inputBinding: {
                locationNameInput: $('.default-address-single-from')
            },
            mapTypeId: google.maps.MapTypeId.SATELLITE,
            enableAutocomplete: true,
            onchanged: function (currentLocation, radius, isMarkerDropped) {				
                $('.geo-location').val((currentLocation.latitude) + ' , ' + (currentLocation.longitude));
            }
        });
    }
}

function initMapTwice(globalGeo, currentOrigin, _current_type){
    var lat = '-25.2744';
    var long = '133.7751';
    var currentOrigin = currentOrigin;
    
    if(globalGeo.length > 0){
        lat = globalGeo[0];
        long = globalGeo[1];
    }

    if(currentOrigin === 'start'){
        $('.geo-start-map-twice').locationpicker({
            location: {
                latitude: lat,
                longitude: long
            },
            radius: 300,
            inputBinding: {
                locationNameInput: $('.default-address-from')
            },
            mapTypeId: google.maps.MapTypeId.SATELLITE,
            enableAutocomplete: true,
            onchanged: function (currentLocation, radius, isMarkerDropped) {             
                $('.geo-start').val((currentLocation.latitude) + ' , ' + (currentLocation.longitude));
                if(typeof _current_type != "undefined" && _current_type == "cg"){
                    //getLandowner($('.geo-start').val(), $('.geo-end').val());
                }
            }
        });
    } else if(currentOrigin === 'end') {
        $('.geo-end-map-twice').locationpicker({
            location: {
                latitude: lat,
                longitude: long
            },
            radius: 300,
            inputBinding: {
                locationNameInput: $('.default-address-to')
            },
            enableAutocomplete: true,
            mapTypeId: google.maps.MapTypeId.SATELLITE,
            onchanged: function (currentLocation, radius, isMarkerDropped) {               
                $('.geo-end').val((currentLocation.latitude) + ' , ' + (currentLocation.longitude));
                if(typeof _current_type != "undefined" && _current_type == "cg"){
                    //getLandowner($('.geo-start').val(), $('.geo-end').val());
                }
            }
        });
    }
}

var getLandowner = function(start, end){
    _before_html = '<span class="text-center landowner-loader"><i class="fa fa-spin fa-spinner fa-2x"></i></span>';
    $('.landowner-details').html(_before_html);
    call({ url: "pipe/pipe-cleargrade/landowner", params: {"start":start, "end":end}, type: "POST" }, function(resp) {
        if(resp.status){
            $('.landowner-details').html(resp.list);
        } else {
            $('.landowner-details').html('<h4 class="text-center">No Landowner Available Now</h4>');
        }
    });
}

var _ini = function(){
    var l = localStorage.getItem('bar');
    if(l=="lock"){
       $('.bgsm-side').addClass('shaded');
    }else{
       $('.bgsm-side').removeClass('shaded');
    }

    if($('.div-weekly').length == 1){
        $('.div-weekly').hide();
        $('.div-weekly').find('input').attr('disabled',true);
    }

    if($('.div-daily').length == 1){
        $('.div-daily').hide();
        $('.div-daily').find('input').attr('disabled',true);
    }
    
    if($('.middel-hide').length == 1){
        // alert($('.middel-hide').length);
        $('.right-table').css({width:'49.4%'});
        $('.left-table').addClass('info-table');
        
    }
    
    $(".pickadate").pickadate({formatSubmit: "yyyy-mm-dd",format: "yyyy-mm-dd"});
    $(".js-example-basic-multiple").select2();
    $(".multiple-select2").select2();
    
    $(document).on('click','.addField',function(e){
        e.stopImmediatePropagation();
        call({ url: "pipe/default/add-defect-field", params: {}, type: "GET" }, function(resp) {
            $('.field-holder').append(resp.html);        
        });
    });
    $(document).on('click','.removeField',function(){ 
        $(this).parent().parent().remove();
    });
    $(document).on('click','.addFieldNdt',function(e){   
        e.stopImmediatePropagation();
        var _this = $(this);
        _this.attr('disabled',true);
        call({ url: "welding/default/get-ndt-field", params: {}, type: "GET" }, function(resp) {
            $('.field-holder').append(resp.html);        
            _this.attr('disabled',false);
        });
    });
    $(document.body).on("change",".multiple-select2",function(){
        var _val = this.value;
        if(_val != "" && _val != "None"){
            $('.defect-position').show();
        } else {
            $('.defect-position').hide();
        }
    });
    var getPipeDefect = function(PipeNumber){
        call({ url: "pipe/default/getdefectfield", params: {"PipeNumber":PipeNumber}, type: "GET" }, function(resp) {
              $('.main-holder').html(resp.html);
        });
    }
    $( ".auto-pipe" ).autocomplete({
        delay: 50,
        source: function (request, response) {
            call({ url: "pipe/default/pipe-auto-list", params: {"pipe_number":request.term}, type: "GET" }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: item.pipe_number, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {               
            $(this).val(ui.item.value.pipe_number);
            getPipeDefect(ui.item.value.pipe_number);           

            $("#pipe_length").val(ui.item.value.length);
            $("#pipe_heat_number").val(ui.item.value.heat_number);
            $("#pipe_kp").val(ui.item.value.kp);
            $("#pipe_thickness").val(ui.item.value.wall_thikness);
            $("#pipe_location").val(ui.item.value.location);  
            $("#yeild_strength").val(ui.item.value.yeild_strength);  


            if($("#cutting-length").length>0){                
                // $("#cutting-length").val(ui.item.value.length);                   
                var _length1 = (ui.item.value.length/2).toFixed(2);
                var _length2 = (ui.item.value.length/2).toFixed(2);
                $("#cutting-length_1").val(_length1);  
                $("#cutting-length_2").val(_length2);
                if(ui.item.value.pipe_number){
                    call({ url: "pipe/cutting/get-new-pipes", params: {'pipe_number': ui.item.value.pipe_number}, type: "POST" }, function(resp) {
                        if(resp.status){
                            var _pipe1 = resp.pipes.pipe_1;
                            var _pipe2 = resp.pipes.pipe_2;
                            $("#cutting-new_pipe_1").val(_pipe1);
                            $("#cutting-new_pipe_2").val(_pipe2);
                        }
                    });
                }
            }        
            return false;
        }
    });
    $( ".auto-pipe-from-reception" ).autocomplete({
        delay: 50,
        source: function (request, response) {
            call({ url: "pipe/default/pipe-auto-list-for-stringing", params: {"pipe_number":request.term,'location':$('#stringing-location').val()}, type: "GET" }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: item.pipe_number, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {               
            $(this).val(ui.item.value.pipe_number); 
            getPipeDefect(ui.item.value.pipe_number);
            $("#pipe_length").val(ui.item.value.length);
            $("#pipe_heat_number").val(ui.item.value.heat_number);
            $("#pipe_kp").val(ui.item.value.kp);
            $("#pipe_thickness").val(ui.item.value.wall_thikness);
            $("#pipe_location").val(ui.item.value.location);        
            $("#yeild_strength").val(ui.item.value.yeild_strength);      
            return false;
        }
    });
    $( ".auto-pipe-for-bending" ).autocomplete({
        delay: 50,
        source: function (request, response) {
            call({ url: "pipe/default/pipe-auto-list-for-bending", params: {"pipe_number":request.term}, type: "GET" }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: item.pipe_number, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {               
            $(this).val(ui.item.value.pipe_number);  
            getPipeDefect(ui.item.value.pipe_number);  
            $("#pipe_length").val(ui.item.value.length);
            $("#pipe_heat_number").val(ui.item.value.heat_number);
            $("#pipe_kp").val(ui.item.value.kp);
            $("#pipe_thickness").val(ui.item.value.wall_thikness);
            $("#pipe_location").val(ui.item.value.location);
            $("#yeild_strength").val(ui.item.value.yeild_strength);           
            return false;
        }
    });
    $( ".auto-pipe-from-stringing" ).autocomplete({
        delay: 50,
        source: function (request, response) {
            call({ url: "pipe/default/pipe-auto-list-for-welding", params: {"pipe_number":request.term, "kp":$('.weld-kp').val(),'next':'no'}, type: "GET" }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: item.pipe_number, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {               
            $(this).val(ui.item.value.pipe_number);    
            getPipeDefect(ui.item.value.pipe_number);
            $("#pipe_length").val(ui.item.value.length);
            $("#pipe_heat_number").val(ui.item.value.heat_number);
            $("#pipe_kp").val(ui.item.value.kp);
            $("#pipe_thickness").val(ui.item.value.wall_thikness);
            $("#pipe_location").val(ui.item.value.location);     
            $("#yeild_strength").val(ui.item.value.yeild_strength);      
            return false;
        }
    });
    $( ".auto-pipe-from-stringing-next" ).autocomplete({
        delay: 50,
        source: function (request, response) {
            call({ url: "pipe/default/pipe-auto-list-for-welding", params: {"pipe_number":request.term, "kp":$('.weld-kp').val(),'next':'yes'}, type: "GET" }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: item.pipe_number, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {               
            $(this).val(ui.item.value.pipe_number);  
            $("#pipe_length").val(ui.item.value.length);
            $("#pipe_heat_number").val(ui.item.value.heat_number);
            $("#pipe_kp").val(ui.item.value.kp);
            $("#pipe_thickness").val(ui.item.value.wall_thikness);
            $("#pipe_location").val(ui.item.value.location);     
            $("#yeild_strength").val(ui.item.value.yeild_strength);      
            return false;
        }
    });
    $( ".drum_number" ).autocomplete({
        source: function (request, response) {
            console.log($(this).attr('id'))
            call({ url: "cabling/default/auto-list", params: {"drum_number":request.term}, type: "GET" }, function(resp) {
                 response( $.map( resp, function( item ) {
                                        return {
                                            label: item.drum_number, 
                                            value: item
                                        }
                              }));
            });
        },
        select: function( event, ui ) {   
            $(".drum_number").val(ui.item.value.drum_number);           
            return false;
        }
    });	
    $( ".splice_drum_number" ).autocomplete({
        source: function (request, response) {
            console.log($(this).attr('id'))
            call({ url: "cabling/default/auto-list", params: {"drum_number":request.term,'fromStringing':1}, type: "GET" }, function(resp) {
                 response( $.map( resp, function( item ) {
                                        return {
                                            label: item.drum_number, 
                                            value: item
                                        }
                              }));
            });
        },
        select: function( event, ui ) {   
            $("#cabsplicing-drum_number").val(ui.item.value.drum_number);           
            return false;
        }
    });	
    $( ".splice_next_drum_number" ).autocomplete({
        delay: 50,
        source: function (request, response) {
            call({ url: "cabling/default/auto-list", params: {"drum_number":request.term,'fromStringing':1}, type: "GET" }, function(resp) {
                 response( $.map( resp, function( item ) {
                                        return {
                                            label: item.drum_number, 
                                            value: item
                                        }
                              }));
            });
        },
        select: function( event, ui ) {   
            $("#cabsplicing-next_drum").val(ui.item.value.drum_number);           
            return false;
        }
    });	
    initMap([],'');

    var _taxo_lat = $('.tx-lat').val();
    var _taxo_long = $('.tx-long').val();
    
    $('.taxo-loc-picker-container').locationpicker({
        location: {
            latitude: typeof _taxo_lat != "undefined" ? _taxo_lat : '-25.2744',
            longitude: typeof _taxo_long != "undefined" ? _taxo_long : '133.7751'
        },
        radius: 300,
        zoom: typeof _taxo_lat != "undefined" &&  typeof _taxo_long != "undefined" ? 12 : 5,
        inputBinding: {
            locationNameInput: $('.default-address-taxo-loc')
        },
        enableAutocomplete: true,
        mapTypeId: google.maps.MapTypeId.SATELLITE,
        onchanged: function (currentLocation, radius, isMarkerDropped) {
            $('.tx-lat').val(currentLocation.latitude);
            $('.tx-long').val(currentLocation.longitude);
        }
    });

    $('.weld-number-auto').autocomplete({
        delay: 50,
        source: function (request, response) {
            var _type = this.element.attr('data-weld');
            var kp_value = typeof $('.parameter-kp').val() != "undefined" ? $('.parameter-kp').val() : '';
            if(typeof _type != "undefined" && _type == "from"){
                kp_value = $('.parameter-kp-from').val();
                if($('.parameter-kp-from').val() == ''){
                    swal({
                        title: 'Oops!',
                        text: 'Please first enter FROM KP number.',
                        type: 'warning',
                        timer: 3000
                    });
                    $(this).val('');
                    return false;
                }
            } else if(typeof _type != "undefined" && _type == "to"){
                kp_value = $('.parameter-kp-to').val();
                if($('.parameter-kp-to').val() == ''){                    
                    swal({
                        title: 'Oops!',
                        text: 'Please first enter TO KP number.',
                        type: 'warning',
                        timer: 3000
                    });
                    $(this).val('');
                    return false;
                }
            } else if($('.parameter-kp').val() == ''){
                swal({
                    title: 'Oops!',
                    text: 'Please first enter KP number.',
                    type: 'warning',
                    timer: 3000
                });
                $(this).val('');
                return false;
            }
            call({ url: 'welding/default/auto-weld-number', params: {'weld_number':request.term,'kp':kp_value}, type: 'GET' }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: item.weld_number, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {   
            $('.respRec').removeClass('hide');
            $(this).val(ui.item.value.weld_number);
            return false;
        }
    });


    $('.trenching-auto').autocomplete({
        delay: 50,
        source: function (request, response) {
            var _type = this.element.attr('data-weld');
            var kp_value = typeof $('.parameter-kp').val() != "undefined" ? $('.parameter-kp').val() : '';

            if(typeof _type != "undefined" && _type == "from"){
                kp_value = $('.parameter-kp-from').val();
                if($('.parameter-kp-from').val() == ''){
                    swal({
                        title: 'Oops!',
                        text: 'Please first enter FROM KP number.',
                        type: 'warning',
                        timer: 3000
                    });
                    $(this).val('');
                    return false;
                }
            } else if(typeof _type != "undefined" && _type == "to"){
                kp_value = $('.parameter-kp-to').val();
                if($('.parameter-kp-to').val() == ''){                    
                    swal({
                        title: 'Oops!',
                        text: 'Please first enter TO KP number.',
                        type: 'warning',
                        timer: 3000
                    });
                    $(this).val('');
                    return false;
                }
            }
            call({ url: 'welding/default/civil-weld-number', params: {'weld_number':request.term,'kp':kp_value, 'action':"Trenching", "type": _type}, type: 'GET' }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: item.weld_number, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {   
            $('.respRec').removeClass('hide');
            $(this).val(ui.item.value.weld_number);
            return false;
        }
    });

    $('.lowering-auto').autocomplete({
        delay: 50,
        source: function (request, response) {
            var _type = this.element.attr('data-weld');
            var kp_value = typeof $('.parameter-kp').val() != "undefined" ? $('.parameter-kp').val() : '';

            if(typeof _type != "undefined" && _type == "from"){
                kp_value = $('.parameter-kp-from').val();
                if($('.parameter-kp-from').val() == ''){
                    swal({
                        title: 'Oops!',
                        text: 'Please first enter FROM KP number.',
                        type: 'warning',
                        timer: 3000
                    });
                    $(this).val('');
                    return false;
                }
            } else if(typeof _type != "undefined" && _type == "to"){
                kp_value = $('.parameter-kp-to').val();
                if($('.parameter-kp-to').val() == ''){                    
                    swal({
                        title: 'Oops!',
                        text: 'Please first enter TO KP number.',
                        type: 'warning',
                        timer: 3000
                    });
                    $(this).val('');
                    return false;
                }
            }
            call({ url: 'welding/default/civil-weld-number', params: {'weld_number':request.term,'kp':kp_value, 'action':"Lowering", "type": _type}, type: 'GET' }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: _type == "from" ? item.from_weld : item.to_weld, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {   
            $('.respRec').removeClass('hide');
            if($(this).attr('data-weld') == "from"){
                $(this).val(ui.item.value.from_weld);
            }
            if($(this).attr('data-weld') == "to"){
                $(this).val(ui.item.value.to_weld);
            }
            return false;
        }
    });

    $('.backfilling-auto').autocomplete({
        delay: 50,
        source: function (request, response) {
            var _type = this.element.attr('data-weld');
            var kp_value = typeof $('.parameter-kp').val() != "undefined" ? $('.parameter-kp').val() : '';

            if(typeof _type != "undefined" && _type == "from"){
                kp_value = $('.parameter-kp-from').val();
                if($('.parameter-kp-from').val() == ''){
                    swal({
                        title: 'Oops!',
                        text: 'Please first enter FROM KP number.',
                        type: 'warning',
                        timer: 3000
                    });
                    $(this).val('');
                    return false;
                }
            } else if(typeof _type != "undefined" && _type == "to"){
                kp_value = $('.parameter-kp-to').val();
                if($('.parameter-kp-to').val() == ''){                    
                    swal({
                        title: 'Oops!',
                        text: 'Please first enter TO KP number.',
                        type: 'warning',
                        timer: 3000
                    });
                    $(this).val('');
                    return false;
                }
            }
            call({ url: 'welding/default/civil-weld-number', params: {'weld_number':request.term,'kp':kp_value, 'action':"Backfilling", "type": _type}, type: 'GET' }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: _type == "from" ? item.from_weld : item.to_weld, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {   
            $('.respRec').removeClass('hide');
            if($(this).attr('data-weld') == "from"){
                $(this).val(ui.item.value.from_weld);
            }
            if($(this).attr('data-weld') == "to"){
                $(this).val(ui.item.value.to_weld);
            }
            return false;
        }
    });

    $('.reinstatement-auto').autocomplete({
        delay: 50,
        source: function (request, response) {
            var _type = this.element.attr('data-weld');
            var kp_value = typeof $('.parameter-kp').val() != "undefined" ? $('.parameter-kp').val() : '';

            if(typeof _type != "undefined" && _type == "from"){
                kp_value = $('.parameter-kp-from').val();
                if($('.parameter-kp-from').val() == ''){
                    swal({
                        title: 'Oops!',
                        text: 'Please first enter FROM KP number.',
                        type: 'warning',
                        timer: 3000
                    });
                    $(this).val('');
                    return false;
                }
            } else if(typeof _type != "undefined" && _type == "to"){
                kp_value = $('.parameter-kp-to').val();
                if($('.parameter-kp-to').val() == ''){                    
                    swal({
                        title: 'Oops!',
                        text: 'Please first enter TO KP number.',
                        type: 'warning',
                        timer: 3000
                    });
                    $(this).val('');
                    return false;
                }
            }
            call({ url: 'welding/default/civil-weld-number', params: {'weld_number':request.term,'kp':kp_value, 'action':"Backfilling", "type": _type}, type: 'GET' }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: _type == "from" ? item.from_weld : item.to_weld, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {   
            $('.respRec').removeClass('hide');
            if($(this).attr('data-weld') == "from"){
                $(this).val(ui.item.value.from_weld);
            }
            if($(this).attr('data-weld') == "to"){
                $(this).val(ui.item.value.to_weld);
            }
            return false;
        }
    });
    $('.cathodic-auto').autocomplete({
        delay: 50,
        source: function (request, response) {
            var _type = this.element.attr('data-weld');
            var kp_value = typeof $('.parameter-kp').val() != "undefined" ? $('.parameter-kp').val() : '';

            if(typeof _type != "undefined" && _type == "from"){
                kp_value = $('.parameter-kp-from').val();
                if($('.parameter-kp-from').val() == ''){
                    swal({
                        title: 'Oops!',
                        text: 'Please first enter FROM KP number.',
                        type: 'warning',
                        timer: 3000
                    });
                    $(this).val('');
                    return false;
                }
            } else if(typeof _type != "undefined" && _type == "to"){
                kp_value = $('.parameter-kp-to').val();
                if($('.parameter-kp-to').val() == ''){                    
                    swal({
                        title: 'Oops!',
                        text: 'Please first enter TO KP number.',
                        type: 'warning',
                        timer: 3000
                    });
                    $(this).val('');
                    return false;
                }
            }
            call({ url: 'welding/default/precom-weld-number', params: {'weld_number':request.term,'kp':kp_value, 'action':"Cathodic", "type": _type}, type: 'GET' }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: _type == "from" ? item.from_weld : item.to_weld, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {   
            $('.respRec').removeClass('hide');
            if($(this).attr('data-weld') == "from"){
                $(this).val(ui.item.value.from_weld);
            }
            if($(this).attr('data-weld') == "to"){
                $(this).val(ui.item.value.to_weld);
            }
            return false;
        }
    });
    $('.cleangauge-auto').autocomplete({
        delay: 50,
        source: function (request, response) {
            var _type = this.element.attr('data-weld');
            var kp_value = typeof $('.parameter-kp').val() != "undefined" ? $('.parameter-kp').val() : '';

            if(typeof _type != "undefined" && _type == "from"){
                kp_value = $('.parameter-kp-from').val();
                if($('.parameter-kp-from').val() == ''){
                    swal({
                        title: 'Oops!',
                        text: 'Please first enter FROM KP number.',
                        type: 'warning',
                        timer: 3000
                    });
                    $(this).val('');
                    return false;
                }
            } else if(typeof _type != "undefined" && _type == "to"){
                kp_value = $('.parameter-kp-to').val();
                if($('.parameter-kp-to').val() == ''){                    
                    swal({
                        title: 'Oops!',
                        text: 'Please first enter TO KP number.',
                        type: 'warning',
                        timer: 3000
                    });
                    $(this).val('');
                    return false;
                }
            }
            call({ url: 'welding/default/precom-weld-number', params: {'weld_number':request.term,'kp':kp_value, 'action':"Cleangauge", "type": _type}, type: 'GET' }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: _type == "from" ? item.from_weld : item.to_weld, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {   
            $('.respRec').removeClass('hide');
            if($(this).attr('data-weld') == "from"){
                $(this).val(ui.item.value.from_weld);
            }
            if($(this).attr('data-weld') == "to"){
                $(this).val(ui.item.value.to_weld);
            }
            return false;
        }
    });

    $('.hydrotesting-auto').autocomplete({
        delay: 50,
        source: function (request, response) {
            var _type = this.element.attr('data-weld');
            var kp_value = typeof $('.parameter-kp').val() != "undefined" ? $('.parameter-kp').val() : '';

            if(typeof _type != "undefined" && _type == "from"){
                kp_value = $('.parameter-kp-from').val();
                if($('.parameter-kp-from').val() == ''){
                    swal({
                        title: 'Oops!',
                        text: 'Please first enter FROM KP number.',
                        type: 'warning',
                        timer: 3000
                    });
                    $(this).val('');
                    return false;
                }
            } else if(typeof _type != "undefined" && _type == "to"){
                kp_value = $('.parameter-kp-to').val();
                if($('.parameter-kp-to').val() == ''){                    
                    swal({
                        title: 'Oops!',
                        text: 'Please first enter TO KP number.',
                        type: 'warning',
                        timer: 3000
                    });
                    $(this).val('');
                    return false;
                }
            }
            call({ url: 'welding/default/precom-weld-number', params: {'weld_number':request.term,'kp':kp_value, 'action':"Hydrotesting", "type": _type}, type: 'GET' }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: _type == "from" ? item.from_weld : item.to_weld, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {   
            $('.respRec').removeClass('hide');
            if($(this).attr('data-weld') == "from"){
                $(this).val(ui.item.value.from_weld);
            }
            if($(this).attr('data-weld') == "to"){
                $(this).val(ui.item.value.to_weld);
            }
            return false;
        }
    });

    // $(document).on('change','.change-wps',function(){
    //     alert(11111);
    //     var _val = $(this).val();
    //     call({ url: 'welding/default/get-welder-by-wps', params: {'wps': _val}, type: 'POST' }, function(resp) {
    //         $('.weld-type').html(_html);
    //     });
    // });

    $('.ndt-reject-weld').autocomplete({
        delay: 50,
        source: function (request, response) {
            var _type = this.element.attr('data-weld');
            var kp_value = typeof $('.parameter-kp').val() != "undefined" ? $('.parameter-kp').val() : '';
            
           if($('.parameter-kp').val() == ''){
                swal({
                    title: 'Oops!',
                    text: 'Please first enter KP number.',
                    type: 'warning',
                    timer: 3000
                });
                $(this).val('');
                return false;
            }
            call({ url: 'welding/default/ndt-reject-weld', params: {'weld_number':request.term,'kp':kp_value}, type: 'GET' }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: item.weld_number, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {   
            $('.respRec').removeClass('hide');
            $(this).val(ui.item.value.weld_number);

            if($('.main-weld-id').length > 0){
                $('.main-weld-id').val(ui.item.value.main_weld_id);
            }

            call({ url: 'welding/default/get-weld-data', params: {'number':ui.item.value.weld_number,'kp':$('.parameter-kp').val()}, type: 'POST' }, function(resp) {
                var _weld_type = typeof resp.data.weld_type !== 'undefined' ? resp.data.weld_type : '';
                var _weld_sub_type = typeof resp.data._weld_sub_type !== 'undefined' ? resp.data._weld_sub_type : '';
                _html = '<div class="form-group field-parameter-wps clearfix">\
                    <div class="col-md-12 clearfix">\
                        <label class="control-label" for="parameter-wps">Weld Type</label>\
                        <input type="text" disabled id="parameter-wps" class="form-control" name="Parameter[wps]" value="'+_weld_type+'">\
                    </div>\
                </div>';
    
                $('.weld-type').html(_html);
                $('.change-wps').val(resp.data.WPS);
                $('.change-wps').trigger('change');
            });
            call({ url: 'welding/weldingrepair/get-ndt-data', params: {'number':ui.item.value.weld_number,'kp':$('.parameter-kp').val()}, type: 'POST' }, function(resp) {
                var report_number = typeof resp.data.report_number !== 'undefined' ? resp.data.report_number : '';
                var ndt_defects = typeof resp.data.ndt_defects !== 'undefined' ? resp.data.ndt_defects : '';
                var ndt_defect_position = typeof resp.data.defect_position !== 'undefined' ? resp.data.defect_position : '';
                
                $('#ndt_defacts').html(ndt_defects);

                var checkNone = ndt_defects.hasOwnProperty("None");
                if(!checkNone){
                    $('.repair-position').val(ndt_defect_position);
                }
                _html = '<div class="form-group field-parameter-wps clearfix">\
                    <div class="col-md-12 clearfix">\
                        <label class="control-label" for="parameter-wps">NDT Report</label>\
                        <input type="text" disabled id="parameter-wps" class="form-control" name="Parameter[wps]" value="'+report_number+'">\
                    </div>\
                </div>';
                $(".respRec").removeClass("hide");    
                $('.ndt-report').html(_html);
            });
            
            getNdtHtml(ui.item.value.weld_number,'weld_repair'); 
            return false;
        }
    });
    var getNdtHtml = function(weldnumber,section=""){
        call({ url: "welding/default/get-ndt-field", params: {'weld_number':weldnumber,'section':section}, type: "GET" }, function(resp) {
            $('.field-holder').html(resp.html);    
        });
    }
    $('.parameter-weld').autocomplete({
        delay: 50,
        source: function (request, response) {
            var _type = this.element.attr('data-weld');
            var kp_value = typeof $('.parameter-kp').val() != "undefined" ? $('.parameter-kp').val() : '';
            
           if($('.parameter-kp').val() == ''){
                swal({
                    title: 'Oops!',
                    text: 'Please first enter KP number.',
                    type: 'warning',
                    timer: 3000
                });
                $(this).val('');
                return false;
            }
            call({ url: 'welding/default/auto-weld-number-from-param', params: {'weld_number':request.term,'kp':kp_value}, type: 'GET' }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: item.weld_number, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {   
            $('.respRec').removeClass('hide');
            $(this).val(ui.item.value.weld_number);
            if($('.main-weld-id').length > 0){
                $('.main-weld-id').val(ui.item.value.id);
            }
            var html = '<div class="form-group field-parameter-wps clearfix">\
                    <div class="col-md-6 col-sm-6 clearfix">\
                        <label class="control-label" for="parameter-wps">Weld Type</label>\
                        <input type="text" disabled id="weld_type" class="form-control" name="Parameter[wps]" value="'+ui.item.value.weld_type+'">\
                    </div>\
                    <div class="col-md-6 col-sm-6 clearfix">\
                        <label class="control-label">Weld Sub Type</label>\
                        <input type="text" disabled id="weld_sub_type" class="form-control" name="Parameter[wps]" value="'+ui.item.value.weld_sub_type+'">\
                    </div>\
                </div>';
            $('.weld-type').html(html);
            getNdtHtml(ui.item.value.weld_number); 
            
            return false;
        }
    });  
    $('.production-weld').autocomplete({
        delay: 50,
        source: function (request, response) {
            var _type = this.element.attr('data-weld');
            var kp_value = typeof $('.parameter-kp').val() != "undefined" ? $('.parameter-kp').val() : '';
            
           if($('.parameter-kp').val() == ''){
                swal({
                    title: 'Oops!',
                    text: 'Please first enter KP number.',
                    type: 'warning',
                    timer: 3000
                });
                $(this).val('');
                return false;
            }
            call({ url: 'welding/default/auto-weld-number-from-ndt', params: {'weld_number':request.term,'kp':kp_value}, type: 'GET' }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: item.weld_number, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {   
            $('.respRec').removeClass('hide');
            $(this).val(ui.item.value.weld_number);
            if($('.main-weld-id').length > 0){
                $('.main-weld-id').val(ui.item.value.main_weld_id);
            }
            var html = '<div class="form-group field-production-wps clearfix">\
                    <div class="col-md-6 col-sm-6 clearfix">\
                        <label class="control-label" for="production-wps">Weld Type</label>\
                        <input type="text" disabled id="weld_type" class="form-control" name="Production[weld_type]" value="'+ui.item.value.weld_type+'">\
                    </div>\
                    <div class="col-md-6 col-sm-6 clearfix">\
                        <label class="control-label" for="production-wps">Weld Sub Type</label>\
                        <input type="text" disabled id="weld_sub_type" class="form-control" name="Production[weld_sub_type]" value="'+ui.item.value.weld_sub_type+'">\
                    </div>\
                </div>';
            $('.weld-type').html(html);
            return false;
        }
    });  
    $('.coatingrepair-weld').autocomplete({
        delay: 50,
        source: function (request, response) {
            var _type = this.element.attr('data-weld');
            var kp_value = typeof $('.parameter-kp').val() != "undefined" ? $('.parameter-kp').val() : '';
            
           if($('.parameter-kp').val() == ''){
                swal({
                    title: 'Oops!',
                    text: 'Please first enter KP number.',
                    type: 'warning',
                    timer: 3000
                });
                $(this).val('');
                return false;
            }
            call({ url: 'welding/default/auto-weld-number-from-production', params: {'weld_number':request.term,'kp':kp_value}, type: 'GET' }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: item.weld_number, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {   
            $('.respRec').removeClass('hide');
            $(this).val(ui.item.value.weld_number);
            if($('.main-weld-id').length > 0){
                $('.main-weld-id').val(ui.item.value.main_weld_id);
            }
            var html = '<div class="form-group field-production-wps clearfix">\
                    <div class="col-md-6 col-sm-6 clearfix">\
                        <label class="control-label" for="production-wps">Weld Type</label>\
                        <input type="text" disabled id="weld_type" class="form-control" name="Production[weld_type]" value="'+ui.item.value.weld_type+'">\
                    </div>\
                    <div class="col-md-6 col-sm-6 clearfix">\
                        <label class="control-label" for="production-wps">Weld Sub Type</label>\
                        <input type="text" disabled id="weld_sub_type" class="form-control" name="Production[weld_sub_type]" value="'+ui.item.value.weld_sub_type+'">\
                    </div>\
                </div>';
            $('.weld-type').html(html);

            return false;
        }
    });    
    $(".from-kp-land").autocomplete({
        delay: 50,
        source: function (request, response) {            
            call({ url: "admin/landowner/get-kp-list", params: {"kp":request.term, "state":"from_kp"}, type: "POST" }, function(resp) {
                response( $.map( resp, function( item ) {
                    console.log(item.from_kp);
                    return {
                        label: item.from_kp,
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {
            $(this).val(ui.item.value.from_kp);
            $('.geo-from-land').val(ui.item.value.from_geo_code);
            return false;
        }
    });

    $(".to-kp-land").autocomplete({
        delay: 50,
        source: function (request, response) {
            call({ url: "admin/landowner/get-kp-list", params: {"kp":request.term, "state":"to_kp"}, type: "POST" }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: item.to_kp,
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {
            $(this).val(ui.item.value.to_kp);
            $('.geo-to-land').val(ui.item.value.to_geo_code);
            return false;
        }
    });

    //vehicle list according to vehicle number
    $( ".auto-vehicle-number" ).autocomplete({
        delay: 50,
        source: function (request, response) {
            call({ url: "vehicle/default/vehicle-auto-list", params: { "vehicle_number":request.term }, type: "GET" }, function(resp) {
                response( $.map( resp, function( item ) {
                    return {
                        label: item.vehicle_number, 
                        value: item
                    }
                }));
            });
        },
        select: function( event, ui ) {               
            $(this).val(ui.item.value.vehicle_number);
            getVehiclePartList(ui.item.value.id);    
            return false;
        }
    });

    var getVehiclePartList = function(_vehicle_id){
        call({ url: "vehicle/default/vehicle-part-list", params: { 'vehicle': _vehicle_id }, type: "POST" }, function(resp) {
            var _html = partListHtml(resp.list);
            $('.selected-part-list').html(_html);
            $('.defect-comment').hide();
        });
    }

    var partListHtml = function(data){
        var _html = '<table class="table">\
            <thead>\
                <tr>\
                    <th>Part</th>\
                    <th>Result</th>\
                </tr>\
            </thead>\
            <tbody>';
            $.each(data, function() {
                var _id = this.id;
                _html += '<tr class="table-active">\
                    <th colspan="2">'+this.name+'</th>\
                </tr>';
                $.each(this.questions, function(i) {
                    var _que_id = this.que_id;
                    var _que_name = this.question;
                    _html += '<tr>\
                        <td>'+_que_name+'</td>\
                        <td>\
                            <select class="form-control part-status" name="MapPartVehicleInspection['+_id+']['+_que_id+'][status]">\
                                <option value="">Please Select</option>\
                                <option value="Acceptable">Acceptable</option>\
                                <option value="Needs Attention">Needs Attention</option>\
                            </select>\
                        </td>\
                    </tr>\
                    <tr class="defect-comment">\
                        <td colspan="2">\
                            <textarea class="form-control" name="MapPartVehicleInspection['+_id+']['+_que_id+'][defect_comment]" placeholder="Defect Comments" style="resize:none;"></textarea>\
                        </td>\
                    </tr>';
                });
            });
        
        _html += '</tbody>\
        </table>';
        
        return _html;
    }
}
function printDiv() {
    var printContents = document.getElementById('print-body').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;

    window.print();

    document.body.innerHTML = originalContents;

    document.close();
    setTimeout(function(){location.reload();},10);

    // var divToPrint = document.getElementById('print-body');
    // var newWin = window.open('','Print-Window');
    // newWin.document.open();
    // newWin.document.write('<html><head><link href="'+baseurl+'/app-assets/css/app.css" rel="stylesheet"><link href="'+baseurl+'/css/groovy.css" rel="stylesheet"></head><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
    // newWin.document.close();
    // setTimeout(function(){newWin.close();},10);
}

function getCode(kp, type){
    call({ url: "admin/landowner/get-geo-code", params: {"kp":kp,"state":type, "project":project}, type: "POST" }, function(resp) {
        if (resp.status) {	
            if(type == "from"){			
                $(".from-geo-code-land").val(resp.code);
            } else if(type == "to") {
                $(".to-geo-code-land").val(resp.code);
            }
        } else {
            if(type == "from"){
                $(".from-geo-code-land").val("");
            } else if(type == "to") {
                $(".to-geo-code-land").val("");
            }
        }
    });
}

$(document).ready(function(){
    
    _ini();
    if($('.middel-hide').length == 1){
        // alert($('.middel-hide').length);
        $('.right-table').css({width:'49.4%'});
        $('.left-table').addClass('info-table');
        
    }
    $('#slam-form').validate();
    $(document).on('change','.mainans',function(e){
        $('#slam-form').validate();
        e.preventDefault();
        $(this).closest('tr').find('.sub-holder').hide()
        if($(this).val()=="Yes"){            
            $(this).closest('tr').find('.sub-holder').show();;
            $(this).closest('tr').find('.sub-holder').find('.q').attr('required','required');
            $(this).closest('tr').find('.sub-holder').find('.ans').attr('required','required');
        }else{
            $(this).closest('tr').find('.sub-holder').find('.q').removeAttr('required');
            $(this).closest('tr').find('.sub-holder').find('.ans').removeAttr('required');
            $(this).closest('tr').find('.sub-holder').find('.q').val('');
            $(this).closest('tr').find('.sub-holder').find('.ans').val('');
        }
       
    });
    $(document).on('click','.add-remove',function(e){
        e.preventDefault();
        if(localStorage.getItem('bar')=="lock"){
            localStorage.setItem('bar','unlock');
        }else{
         
            localStorage.setItem('bar','lock');
        }
        $('.bgsm-side').toggleClass('shaded');
        // $('.glyphicon-pencil').toggleClass('shaded');
       
    });
    
    $(document).on('click','.legend-collap-in', function(){
        $('#legend').show();
        $(this).hide();
    });

    $(document).on('click','.legend-collap', function(){
        $('#legend').hide();
        $('.legend-collap-in').show();
    });

    var $item = $('.anomaly-li');
   // console.log($item.parent().parent().width());console.log($('.anomaly-tabls ul').scrollLeft());
    $(document).on('click','.anomaly-li',function(e){
        e.preventDefault();
        e.stopPropagation();
        var index = $('.anomaly-li').index( this );
     //   console.log(index);
    
        var thisWidth = $(this).parent().width();
        var scrllft = $('.anomaly-tabls ul').scrollLeft();
        // console.log($('.anomaly-tabls ul').scrollRight());
        $('.anomaly-li').parent().removeClass('active');
        $(this).parent().addClass('active');
        if($(window).width() > 767){
       
        var scrllft = $('.anomaly-tabls li.active').width();
        //console.log(scrllft);
            if(index > 14){
                //alert(index);
                $('.anomaly-tabls ul').animate({
                    // 'marginLeft' : "-=150px"
                    'scrollLeft' : "+="+scrllft
                },500);
            }else if(index < 8){
                $('.anomaly-tabls ul').animate({
                    // 'marginLeft' : "-=150px"
                    'scrollLeft' : '-=150',
                },500); 
            }
        }
        // return false;
    });    
  
    $(document).on('click','.get-weldbook',function(e) {
        e.preventDefault();
        $('.pjax-loader').show();
        // console.log(file.name);
         var formData = new FormData();
        // var from_kp=$("input[name=fromkp]").val();
        // var to_kp=$("input[name=tokp]").val();
        // formData.append('Clearance[from_kp]', from_kp);
        // formData.append('Clearance[to_kp]', to_kp);
         console.log(formData);
        $.ajax({      
            url: baseurl+'/report/report/weldbook-report', 
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType:'json',
            //Ajax events
            success: function(data){
                $('.pjax-loader').hide();
                if(data.status){                   
                    toastr.success('Your WeldBook report downloaded');
                    window.open(data.file,'_blank');
                   
                }else{
                    toastr.error(data.message,messages.opps,{timeOut:0,closeButton: !0});
                }
            },
			error: function () {
                $('.pjax-loader').hide();
                toastr.error('Your server request has been failed,please try after sometime','Request Failed!',{timeOut:0,closeButton: !0});
			}
        });
    });
    $(document).on('click', '.visual-prograss', function(e){
        //alert(1);
        initializeReportMap();
    });
    
    $(document).on('click','.get-welderanalysis',function(e) {
        // alert(1);
        $.ajax({      
            url: baseurl+'/report/report/clearance-report', 
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType:'json',
            //Ajax events
            success: function(data){
                $('.pjax-loader').hide();                
                if(data.status){                   
                    toastr.success("Your report is generated");
                    $('#header-titleprint').text('Clearance Report');
                    $('#print-body').html('<div class="table-Approval-main"> <div class="container"> <div class="table-appro"> <div class="logo-appro"> <img src="'+imagepath+'" alt="logo" class="appro-logo"> </div><div class="appro-title"> <h1>Approval to Lower Pipe Strings</h1> </div><div class="br-div-tb"></div><div class="selectiondetail-tabel"> <div class="detail-tabel-title">PipelineSectionDetails</div><div class="row pro-loc-main"> <div class="project-name"> <label for="name">Project -</label> </div><div class="location-name"> <label for="location">Location -</label> </div></div><div class="selectiondetailtable"> <table class="table table-selectiondetail"> <thead> <tr> <th>StartSection</th> <th>EndSection</th> </tr></thead> <tbody> <tr> <td>StartPipeNo.</td><td>Start WeldNo.</td></tr><tr> <td>EndPipeNo.</td><td>EndWeldNo.</td></tr><tr> <td>StartKP.</td><td>EndKP.</td></tr></tbody> </table> </div></div><div class="selectiondetail-tabel"> <div class="performed-tabel-title">QAQC Checks to be performed:</div><div class="selectiondetailtable"> <table class="table table-selectiondetail performed"> <thead> <tr> <th>Check</th> <th>Check Accepted</th> <th>By</th> </tr></thead> <tbody> <tr> <td>All Weld Data Received</td><td></td><td></td></tr><tr> <td>All NDT Data received</td><td></td><td></td></tr><tr> <td>No outstanding repairs</td><td></td><td></td></tr><tr> <td>Coating Performed</td><td></td><td></td></tr><tr> <td>Coating Accepted</td><td></td><td></td></tr><tr> <td>Anomaly Report Checked and no items outstanding</td><td></td><td></td></tr></tbody> </table> </div><div class="performed-tabel-text">Checks listed above are for mainline welds, tie-ins will be cleared on a case by case basis, joint to be signed off for welding visual, NDT and Coating prior to backfill</div></div><div class="remark-div">Remarks:</div><div class="all-br"> <div class="br-line"> </div><div class="br-line"> </div><div class="br-line"> </div><div class="br-line"> </div><div class="br-line"> </div><div class="br-line"> </div></div><div class="detail-tabel-title add-br">AuthorisationtoProceed–</div><div class="Authorisationto-table-div"> <table class="table table-selectiondetail Authorisationto"> <tbody> <tr> <td>QA</td><td>Name:</td><td>Sign:</td><td>Date:</td></tr><tr> <td>Construction Manager:</td><td>Name:</td><td>Sign:</td><td>Date:</td></tr><tr> <td>Witness:</td><td>Name:</td><td>Sign:</td><td>Date:</td></tr></tbody> </table> </div></div></div></div>'); 
                    $('#print-modal').modal('show'); 
                }else{
                    toastr.error(data.message,messages.opps,{timeOut:0,closeButton: !0});
                }
            },
			error: function () {
                $('.pjax-loader').hide();
                toastr.error('Your server request has been failed,please try after sometime','Request Failed!',{timeOut:0,closeButton: !0});
			}
        });
    });


	$(document).on('pjax:start', function() {      
		$('.pjax-loader').show();
   
	});
	$(document).on('pjax:end', function() {
        _ini();
		$('.pjax-loader').hide();
    });
    
    $(document).on('click','.nav-item > a, .has-sub > a',function(e){
        e.preventDefault();
    });

    $(document).on('click','.map-picker-addon',function(){
		var _current_geo = $(this).closest('.input-group').find('input').val();
        var _global_geo = _current_geo.split(',');
        var _current_section = $(this).attr('data-section');
        
        var _current_type = $(this).closest('form').find('.section-type').val();
        
        var current_origin = '';
		if($(this).closest('.input-group').find('input').hasClass('geo-start')){
			current_origin = 'start';
		} else if($(this).closest('.input-group').find('input').hasClass('geo-end')) {
			current_origin = 'end';
        }
        
		if(typeof _current_section != "undefined" && (_current_section == "landowner" || _current_section =="line")){
            if(_current_geo == "") _global_geo = ['-25.2744', '133.7751'];
            if(current_origin == 'start'){
                initMapTwice(_global_geo, current_origin, _current_type);
                $('.body-end').hide();
                $('.body-start').show();
            } else if(current_origin == 'end'){
                initMapTwice(_global_geo, current_origin, _current_type);
                $('.body-end').show();
                $('.body-start').hide();
            }
            $('#geo-location-twice-modal').modal();
        } else {
            if(_current_geo !== ''){
                if(current_origin == 'start'){
                    initMapTwice(_global_geo, current_origin, _current_type);
                    $('.body-end').hide();
                    $('.body-start').show();
                } else if(current_origin == 'end'){
                    initMapTwice(_global_geo, current_origin, _current_type);
                    $('.body-end').show();
                    $('.body-start').hide();
                }
                $('#geo-location-twice-modal').modal();
            } else {
                swal({
                    title: 'Oops!',
                    text: 'Please select location',
                    type: 'error'
                });
            }
        }
    });

    $(document).on('click','.map-picker-addon-single',function(){
        var _current_geo = $(this).closest('.input-group').find('input').val();
        var _global_geo = _current_geo.split(',');

        var _current_section = $(this).attr('data-section');

        if(typeof _current_section != "undefined" && _current_section == "surveying"){
            _current_geo = "surveying";
            _global_geo = ['-25.2744', '133.7751'];
        }
        
        if(_current_geo !== ''){
            initMap(_global_geo);
            $('.body-start').show();
            $('.body-end').hide();			
            $('#geo-location-modal').modal();
        } else {
            swal({
                title: 'Oops!',
                text: 'Please select location',
                type: 'error'
            });
        }
    });


	$('#geo-location-modal').on('hide.bs.modal', function() {
		initMap([],'');
    });    
    $(document).on('click','.signed-selected',function(e){
        e.preventDefault();
		var Url =$(this).attr('url');
        if($(".grid-view").yiiGridView("getSelectedRows")!=""){ 
            call({ url:Url, params:{'signedId':$(".grid-view").yiiGridView("getSelectedRows")}, type: 'POST' }, function(resp) {
                $.pjax.reload({container:'#idofpjaxcontainer'});                                     
            });
        }else{
            swal({
                title: messages.Please,
                text: messages.signSelected,
                type: "error",
                timer: 3000
            });
        }
    });
	$(document).on('click','.print-selected',function(e){
        e.preventDefault();
        var ids =  $('.grid-view').yiiGridView('getSelectedRows');
		var Url =$(this).attr('url');
        call({ url:Url, params:{'printid':'Yes','id':ids}, type: 'POST' }, function(resp) {
            $('#print-modal').html(resp.html); 
            $('#print-modal').modal('show');                        
        });
    });
      //2018-03-02
    $(".pickadate").pickadate({
		formatSubmit: "yyyy-mm-dd",
		format: "yyyy-mm-dd",
    });
    
    $(document).on('change','.prod-opt-change',function(){
        var _current_val = $(this).val();
        if(_current_val == "weekly"){
            $('.div-weekly').show();
            $('.div-weekly').find('input').attr('disabled',false);            
            $('#weekRange').daterangepicker({
                locale: {
                    format: 'YYYY/MM/DD'
                },
            });
            $('.div-daily').hide();
            $('.div-daily').find('input').attr('disabled',true);
        } else if(_current_val == "daily") {
            $('#dailyRange').daterangepicker({
                locale: {
                    format: 'YYYY/MM/DD'
                },
                singleDatePicker: true,
            });

            $('.div-daily').show();
            $('.div-daily').find('input').attr('disabled',false);
            $('.div-weekly').hide();
            $('.div-weekly').find('input').attr('disabled',true);
        } else {
            $('.div-weekly').hide();
            $('.div-weekly').find('input').attr('disabled',true);
            $('.div-daily').hide();
            $('.div-daily').find('input').attr('disabled',true);
        }
    });

    $(document).on('click','print-modal',function () {
        $("#print-modal").modal("hide");
    });

	$(document).on('click','.img-remove',function(e){  
	   e.preventDefault();  
	   var imgName = $(this).attr('data-image');
	   if(imgName!="") {
            call({ url: 'pipe/default/delete-img', params:{'ImgName':imgName}, type: 'GET' }, function(resp) {
                    
                    toastr.success('Image is removed.','Success!')
                    $.pjax.reload({container:'#idofpjaxcontainer'});
            });
	   }else{
	   	   swal({
                title: messages.Please,
                text: messages.selectImage,
                type: "error",
                timer: 3000
            });
	   }
    });
    
    $(document).on('click','.delete-multipe-anomaly',function(e){  
        e.preventDefault();   
		var Url = $(this).attr('url');
        if($(".grid-view").yiiGridView("getSelectedRows") != ""){
            swal({
                title: messages.sure,
                text: messages.deleteMultiple,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: false,
                closeOnCancel: true
            },function (isConfirm) {
                if (isConfirm) {
                    call({ url: Url, params:{'deleteId' : $(".grid-view").yiiGridView("getSelectedRows") }, type: 'POST' }, function(resp) {
                        $.pjax.reload({container:'#idofpjaxcontainer'});
                        swal({
                            title: messages.deleted,
                            text: messages.rawDeleted,
                            type: "success",
                            timer: 3000
                        });
                    });
                }
            });
        }else{
            swal({
                title: messages.Please,
                text: messages.signSelected,
                type: "error",
                timer: 3000
            });
        }
    });


    $(document).on('click','.delete-multipe',function(e){  
        e.preventDefault();   
		var Url =$(this).attr('url');
        if($(".grid-view").yiiGridView("getSelectedRows")!=""){ 
        swal({
            title: messages.sure,
            text: messages.deleteMultiple,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false,
            closeOnCancel: true,
            showLoaderOnConfirm: true
            },
            function (isConfirm) {
                if (isConfirm) {
                    
                        call({ url: Url, params:{'deleteId':$(".grid-view").yiiGridView("getSelectedRows")}, type: 'POST' }, function(resp) {
                                $.pjax.reload({container:'#idofpjaxcontainer'});
                                swal({
                                    title: messages.deleted,
                                    text: messages.rawDeleted,
                                    type: "success",
                                    timer: 3000
                                });
                                
                        });
                    
                }
            });
        }else{
            swal({
                title: messages.Please,
                text: messages.signSelected,
                type: "error",
                timer: 3000
            });
        }
    });
    $(document).on('click','.nav-item > a',function(e){
        e.preventDefault();
    });
    
    $(document).on('change','.taxonomy-val',function(e){
        var _id = $(this).val();
        var html = '';
		if(_id == 2){
            call({ url: 'admin/taxonomy/projects', params: {id : 4}, type: 'POST' }, function(resp) {
                html = '<div class="col-md-6 clearfix">\
                    <label class="control-label" for="taxonomyValue-location_lat">Latitude</label>\
                    <input type="text" id="taxonomyValue-location_lat" class="form-control tx-lat" name="TaxonomyValue[location_lat]" aria-required="true" aria-invalid="true">\
                </div>\
                <div class="col-md-6 clearfix">\
                    <label class="control-label" for="taxonomyValue-location_long">Longitude</label>\
                    <input type="text" id="taxonomyValue-location_long" class="form-control tx-long" name="TaxonomyValue[location_long]" aria-required="true" aria-invalid="true">\
                </div>\
                <div class="col-md-12 mt-1 clearfix text-right">\
                    <div class="taxo-loc-picker">Pick from Map</div>\
                </div>';            

                $('.taxonomy-html').html(html);
            });
		} else if(_id == 30){
            html = '<div class="col-md-12 clearfix">\
                <label class="control-label" for="partList-question">Questions <span class="red">*</span></label>\
                <div class="part-question clearfix">\
                    <input type="text" id="partList-question" class="form-control list-question" name="MapPartQuestion[question][]" aria-required="true" aria-invalid="true">\
                </div>\
            </div>\
            <div class="col-md-12 mt-1 clearfix text-right">\
                <a href="#" class="btn btn-primary btn-sm btn-add-more-que">Add Question</a>\
            </div>';
            $('.taxonomy-html').html(html);
        } else {
			call({ url: 'admin/taxonomy/taxomonychild', params: {id : _id}, type: 'POST' }, function(resp) {				
				if(resp.status == true) {
					$.each(resp.data, function(key, value) {
						html += '<div class="col-6"><div class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0">\
							<input type="checkbox" name="TaxonomyValue[taxonomyChildId][]" class="custom-control-input" id="customcheckbox'+key+'" value="'+value.id+'">\
							<label class="custom-control-label" for="customcheckbox'+key+'">'+value.value+'</label>\
						</div></div>';
					});
					$('.taxonomy-html').html(html);
				} else {
					$('.taxonomy-html').html('');
				}
			});
		}
    });

    //******************* New Vehicle Feature Start ***********************/
    //unique validation on part name
    $(document).on('blur', '#taxonomyvalue-value', function(){
        var _val = $(this).val();
        var _taxonomy_val = $('.taxonomy-val').val();
        var edit_id = typeof getUrlParameter('EditId') != 'undefined' ? getUrlParameter('EditId') : 0;
        var _this = $(this);
        if(typeof _taxonomy_val != 'undefined' && _taxonomy_val == 30){
            call({ url: 'admin/taxonomy/unique-part-name', params: {'name': _val, 'taxo': _taxonomy_val, 'edit': edit_id}, type: 'POST' }, function(resp) {
                if (!resp.status) {
                    toastr.error(resp.message,'Opps!')
                    _this.val('');
                }
            });
        }
    });

    $(document).on('change', '.taxonomy-val', function(){
        var _taxonomy_val = $(this).val();
        var _val = $('#taxonomyvalue-value').val();
        var edit_id = typeof getUrlParameter('EditId') != 'undefined' ? getUrlParameter('EditId') : 0;
        if(typeof _taxonomy_val != 'undefined' && _taxonomy_val == 30 && _val != ''){
            call({ url: 'admin/taxonomy/unique-part-name', params: {'name': _val, 'taxo': _taxonomy_val, 'edit': edit_id}, type: 'POST' }, function(resp) {
                if (!resp.status) {
                    toastr.error(resp.message,'Opps!')
                    $('#taxonomyvalue-value').val('');
                }
            });
        }
    });

    //add new question for part
    $(document).on('click', '.btn-add-more-que', function(e){
        e.preventDefault();
        html = '<div class="new-que-container col-md-12 clearfix mt-2 p-0">\
            <div class="col-md-10 clearfix p-0">\
                <input type="text" id="partList-question" class="form-control list-question" name="MapPartQuestion[question][]" aria-required="true" aria-invalid="true">\
            </div>\
            <div class="col-md-2 clearfix p-r-0 text-right">\
                <a href="#" class="btn btn-danger btn-sm btn-remove-que"><i class="fa fa-trash-o"></i></a>\
            </div>\
        </div>';
        $('.part-question').append(html);
    });

    //remove question
    $(document).on('click', '.btn-remove-que', function(e){
        e.preventDefault();
        $(this).closest('.new-que-container').remove();
    });

    //clone part list in vehicle schedule
    $(document).on('click', '.btn-clone-part-schedule', function(){
        var _html = $( ".clone-part-list" ).html();
        call({ url: 'vehicle/schedule/get-part', params: {}, type: 'POST' }, function(resp) {
            if (resp.status) {
                $(".section-new-added-part").append(resp.html);
            }
        });
    });

    //remove cloned part
    $(document).on('click','.btn-remove-part-schedule',function(){
        var _get_total_parts = $('.btn-remove-part-schedule').length;
        if(_get_total_parts <= 1){
            toastr.error('You have to select at least one part', 'Oops!')
        } else {
            $(this).closest('.clearfix').remove();
        }
    });

    //check vehicle unique number
    $(document).on('blur', '.vehicle-number', function(){
        var _number = $(this).val();
        var _vehicle = $(this).attr('data-vehicle');
        _vehicle = typeof _vehicle != 'undefined' ? _vehicle : 0;
        var _this = $(this);
        if(typeof _number != 'undefined' && _number != ""){
            call({ url: 'vehicle/schedule/unique-vehicle-number', params: {'number': _number, 'vehicle': _vehicle}, type: 'POST' }, function(resp) {
                if (!resp.status) {
                    toastr.error(resp.message,'Opps!')
                    _this.val('');
                }
            });
        }
    });

    //check unique barcode for vehicle part
    $(document).on('blur', '.part-barcode', function(){
        if(!$(this).attr('readonly')){
            var _code = $(this).val();
            var _vehicle_part = $(this).closest('.part-container').find('.vehicle-part').val();
            _vehicle = typeof _vehicle != 'undefined' ? _vehicle : 0;
            var _this = $(this);
            if(typeof _code != 'undefined' && _code != ""){
                call({ url: 'vehicle/schedule/unique-part-barcode', params: {'code': _code, 'part': _vehicle_part}, type: 'POST' }, function(resp) {
                    if (!resp.status) {
                        toastr.error(resp.message,'Opps!')
                        _this.val('');
                    } else {
                        // if($(".part-barcode").length > 1){
                        //     $(".part-barcode").each(function() {
                        //         var _vehicle = $(this).closest('.part-container').find('.vehicle-part option:selected').html();
                        //         if($(this).val() == _code){
                        //             toastr.error("'"+_code+"' has already been taken for another part "+_vehicle, 'Oops!', {timeOut:0,closeButton: !0});
                        //             _this.val('');
                        //         }
                        //     });
                        // }
                    }
                });
            }
        }
    });

    //copy selected vehicle
    $(document).on('click', '.btn-copy-selected', function(){
        var _url = $(this).attr('url');
        if($(".grid-view").yiiGridView("getSelectedRows").length > 1){
            toastr.error('You can copy just one vehicle at a time.','Opps!')
        } else if($(".grid-view").yiiGridView("getSelectedRows").length == 0) {
            toastr.error('You have to select one vehicle to copy.','Opps!')
        } else {
            var _id = $(".grid-view").yiiGridView("getSelectedRows")[0];
            window.location.href = _url+'?CopyId='+_id;
        }
    });

    //pick location for vehicle inspection
    $(document).on('click','.map-picker-vehicle-inspection',function(){
        var _current_geo = $(this).closest('.input-group').find('input').val();
        var _global_geo = _current_geo.split(',');
        if(_current_geo == ''){
            _global_geo = ['-25.2744', '133.7751'];
        }

        initMap(_global_geo);
        $('.body-start').show();
        $('.body-end').hide();			
        $('#geo-location-modal').modal();
    });

    //show defect comment textarea, if status is fail
    $(document).on('change', '.part-status', function(){
        var _val = $(this).val();
        if(_val == 'Needs Attention'){
            $(this).closest('tr').next().show();
        } else {
            $(this).closest('tr').next().hide();
            $(this).closest('tr').next().find('textarea').val('');
        }
    });

    //check vehicle number exist in record, prevent if not found - Vehicle Inspection
    $(document).on('blur', '.valid-vehi-number', function(){
        var _number = $(this).val();
        var _this = $(this);
        if(typeof _number != 'undefined' && _number != ''){
            call({ url: 'vehicle/default/check-availability', params: { 'number': _number }, type: 'POST' }, function(resp) {
                if(!resp.status) {
                    toastr.error(resp.message, 'Opps!')
                    _this.val('');
                }
            });
        }
    });

    //show barcode textbox after select any part - Vehicle Schedule
    // $(document).on('change', '.vehicle-part', function(){
    //     var _val = $(this).val();
    //     if(_val != 0){
    //         $(this).closest('.part-container').find('.v-container').addClass('col-md-5').removeClass('col-md-7');
    //         $(this).closest('.part-container').find('.b-container').addClass('col-md-5').removeClass('col-md-3');
    //         $(this).closest('.part-container').find('input').removeClass('hide');
    //     } else {
    //         $(this).closest('.part-container').find('.v-container').removeClass('col-md-5').addClass('col-md-7');
    //         $(this).closest('.part-container').find('.b-container').removeClass('col-md-5').addClass('col-md-3');
    //         $(this).closest('.part-container').find('input').addClass('hide');
    //     }
    // });

    //************************** New Vehicle Feature End ********************************/

    $(document).on('click','.taxo-loc-picker', function(e){
        $('#taxo-location-modal').modal();
    });

    $('#basicSelect').change(function(event) {
        var pid= $('option:selected', this).val();
        call({ url: 'pipe/default/changeproject', params: {'projectid':pid}, type: 'POST' }, function(resp) {
            if (resp.success) {
                if($('#idofpjaxcontainer').length > 0){
                    $.pjax.reload({container:'#idofpjaxcontainer'});
                } else {
                    $.pjax.reload({container: '#listproject'});
                }
                toastr.success(resp.message,'Success!');
              //  window.location.href = baseurl;
            } else{
                toastr.error('Error','Opps!')
            }
        });
    });
    
    $(document).on('change', '#langSelect', function(e){
        e.preventDefault();
        var lang = $('option:selected', this).val();
        call({ url: 'site/change-lang', params: { 'lang': lang }, type: 'POST' }, function(resp) {
            if (resp.status) {
                $.pjax.reload({container: '#listlanguage'});
                location.reload();
            } else {
                toastr.error('Error','Opps!')
            }
        });
    });

    $('#basicSelect').change(function(event) {
        var pid= $('option:selected', this).val();
        call({ url: 'pipe/default/changeproject', params: {'projectid':pid}, type: 'POST' }, function(resp) {
            if (resp.success) {
                if($('#idofpjaxcontainer').length > 0){
                    $.pjax.reload({container:'#idofpjaxcontainer'});
                } else {
                    $.pjax.reload({container: '#listproject'});
                }
                toastr.success(resp.message,'Success!');
              //  window.location.href = baseurl;
            } else{
                toastr.error('Error','Opps!')
            }
        });
    });
	
	$(document).on('change','.location-drop',function(){
		var _id = $(this).val();
		var _s_type = $(this).closest('form').find('.section-type').val();
        var section = '';
        if(typeof _s_type != 'undefined' && _s_type != ""){
            section = _s_type;
        }
		call({ url: 'pipe/pipe-cleargrade/location-geo-code', params: {'id': _id, 'section': section}, type: 'POST' }, function(resp) {
            if (resp.status && resp.geocode.lat != "" && resp.geocode.lat != "") {				
                $('.geo-location-twice').val(resp.geocode.lat+' , '+resp.geocode.long);
                $('.geo-location').val(resp.geocode.lat+' , '+resp.geocode.long);
            } else {
                $('.geo-location-twice').val('');
                $('.geo-location').val('');
			}
        });
	});
	
	$(document).on('change','.kp-drop',function(){
		var _kp = $(this).val();
		var _kp_state = $(this).attr('data-kp');
		call({ url: 'admin/landowner/get-geo-code', params: {'kp':_kp, 'state':_kp_state}, type: 'POST' }, function(resp) {
            if (resp.status) {	
				if(_kp_state == 'from'){			
                	$('.from-geo-code').val(resp.code);
				} else if(_kp_state == 'to') {
					$('.to-geo-code').val(resp.code);
				}
            } else {
				if(_kp_state == 'from'){
					$('.from-geo-code').val('');
				} else if(_kp_state == 'to') {
					$('.to-geo-code').val('');
				}
			}
        });
    });
    
    $(document).on('blur','.kp-range',function(){
        var _kp = $(this).val();
        var _this = $(this);
        if(_kp !== ""){
            call({ url: 'pipe/stringing/kp-range', params: {'kp':_kp,'location':$('#stringing-location').val()}, type: 'POST' }, function(resp) {
                if (!resp.status) {	
                    swal({
                        title: messages.oops,
                        text: messages.kpRangeWarning,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Cancel",
                        cancelButtonText: "Ignore",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            _this.val('');
                        }
                    });
                }
            });
        }
    });
    
    $(document).on('change','.change-wps',function(){
        var _current_val = $(this).val();
        var _list_data_elec = '';
        var _list_data_weld = '<option value="">Please Select</option>';
        call({ url: 'welding/welding/get-electrods', params: {'wps':_current_val,}, type: 'POST' }, function(resp) {            
            if (resp.status) {
                $.each(resp.data,function(i,obj) {
                    _list_data_elec += '<option value="'+obj+'">'+obj+'</option>';
                });
            }
            $('.list-electrods').html(_list_data_elec);
        });

        call({ url: 'welding/welding/get-welders', params: {'wps':_current_val,}, type: 'POST' }, function(resp) {
            if (resp.status) {
                $.each(resp.data,function(i,obj) {
                    _list_data_weld += '<option value="'+obj+'">'+obj+'</option>';
                });
            }
            $('.list-welders').html(_list_data_weld);
        });
    });

    $(document).on('blur', '.weld-number-check', function(e){
        var _number = $(this).val();
        var _kp = $('.weld-kp').val();
        if(_kp == ""){
            toastr.error('KP can\'t be blank.', 'Oops', {timeOut:5000,closeButton: !0});
            $(this).val('');
            return false;
        }
        call({ url: 'welding/welding/weld-number-alert', params: {'number':_number, 'kp':_kp}, type: 'POST' }, function(resp) {            
            if (!resp.status) {
                toastr.warning('It will be saved in anomaly.','Weld number has already taken.',{timeOut:5000,closeButton: !0})
            }
        });
    });

    $(document).on('change', '.weld-type', function(){
        var _current_type = $(this).val();
        var _html = '';
        
        if(_current_type != 'W'){
            var _kp = $('.weld-kp').val();
            if(_kp == ""){
                toastr.error('KP can\'t be blank.', 'Oops', {timeOut:5000,closeButton: !0});
                $(this).val('W');
                return false;
            }                        
            // call({ url: 'welding/welding/weld-crossing', params: {'kp':_kp}, type: 'POST' }, function(resp) {
            call({ url: 'welding/welding/weld-crossing', params: {'weld':_current_type, 'kp':_kp}, type: 'POST' }, function(resp) {
                _html = '<div class="form-group field-welding-weld_crossing clearfix">\
                    <div class="col-md-6 col-sm-6 clearfix">\
                        <label class="control-label" for="welding-weld_crossing">Weld Crossing</label>\
                        <input type="text" id="welding-weld_crossing" class="form-control weld-crossing-inp" name="Welding[weld_crossing]" value="'+parseInt(resp.count)+'">\
                    </div>\
                </div>';
                $('.weld-crossing').html(_html);
            });
        } else {
            $('.weld-crossing').html(_html);
        }
    });

    $(document).on('blur', '.weld-number-auto, .ndt-reject-weld', function(){
        var _kp = $('.parameter-kp').val();
        var _weld_number = $(this).val();

        var _type = typeof $(this).closest('form').attr('data-type') !== 'undefined' ? $(this).closest('form').attr('data-type') : '';
        if(_type == ""){
            call({ url: 'welding/parameter/get-weld-wps', params: {'kp':_kp, 'number':_weld_number}, type: 'POST' }, function(resp) {
                _html = '<div class="form-group field-parameter-wps clearfix">\
                    <div class="col-md-12 clearfix">\
                        <label class="control-label" for="parameter-wps">WPS</label>\
                        <input type="text" disabled id="parameter-wps" class="form-control" name="Parameter[wps]" value="'+resp.wps+'">\
                    </div>\
                </div>';
                $('.weld-wps').html(_html);
                // $('.wps-welder').html(resp.welders);
            });
        } else if(_type == 'ndt' || _type == 'production' ||  _type == 'coating_repair') {
            call({ url: 'welding/default/get-weld-data', params: {'number':_weld_number,'kp':_kp}, type: 'POST' }, function(resp) {
                var _weld_type = typeof resp.data.weld_type !== 'undefined' ? resp.data.weld_type : '';
                var _weld_sub_type = typeof resp.data._weld_sub_type !== 'undefined' ? resp.data._weld_sub_type : '';
                _html = '<div class="form-group field-parameter-wps clearfix">\
                    <div class="col-md-6 clearfix">\
                        <label class="control-label" for="parameter-wps">Weld Type</label>\
                        <input type="text" disabled id="parameter-wps" class="form-control" name="Parameter[wps]" value="'+_weld_type+'">\
                    </div>\
                    <div class="col-md-6 clearfix">\
                        <label class="control-label" for="parameter-wps">Weld Sub Type</label>\
                        <input type="text" disabled id="parameter-wps" class="form-control" name="Parameter[wps]" value="'+_weld_sub_type+'">\
                    </div>\
                </div>';
    
                $('.weld-type').html(_html);
            });
        } else if(_type == 'weld_repair') {
            call({ url: 'welding/default/get-weld-data', params: {'number':_weld_number,'kp':_kp}, type: 'POST' }, function(resp) {
                var _weld_type = typeof resp.data.weld_type !== 'undefined' ? resp.data.weld_type : '';
                var _weld_sub_type = typeof resp.data._weld_sub_type !== 'undefined' ? resp.data._weld_sub_type : '';
                _html = '<div class="form-group field-parameter-wps clearfix">\
                    <div class="col-md-12 clearfix">\
                        <label class="control-label" for="parameter-wps">Weld Type</label>\
                        <input type="text" disabled id="parameter-wps" class="form-control" name="Parameter[wps]" value="'+_weld_type+'">\
                    </div>\
                </div>';
    
                $('.weld-type').html(_html);
            });
            call({ url: 'welding/weldingrepair/get-ndt-data', params: {'number':_weld_number,'kp':_kp}, type: 'POST' }, function(resp) {
                var report_number = typeof resp.data.report_number !== 'undefined' ? resp.data.report_number : '';
                var ndt_defects = typeof resp.data.ndt_defects !== 'undefined' ? resp.data.ndt_defects : '';
                var ndt_defect_position = typeof resp.data.defect_position !== 'undefined' ? resp.data.defect_position : '';
                
                $('#ndt_defacts').html(ndt_defects);

                var checkNone = ndt_defects.hasOwnProperty("None");
                if(!checkNone){
                    $('.repair-position').val(ndt_defect_position);
                }

                _html = '<div class="form-group field-parameter-wps clearfix">\
                    <div class="col-md-12 clearfix">\
                        <label class="control-label" for="parameter-wps">NDT Report</label>\
                        <input type="text" disabled id="parameter-wps" class="form-control" name="Parameter[wps]" value="'+report_number+'">\
                    </div>\
                </div>';
                $(".respRec").removeClass("hide");    
                $('.ndt-report').html(_html);
            });
        }
        getNdtHtml(_weld_number); 
    });

    $(document).on('click', '.make-active', function(){
        var Id = $(this).attr('id');
        var Model = $(this).attr('model');
        if(Model.length!="" && Id!="" ){
            call({ url: 'admin/anomaly/active', params: {'Id':Id, 'Model':Model}, type: 'GET' }, function(resp) {
                if(resp.status== true){
                    $.pjax.reload({container:'#idofpjaxcontainer'});
                    toastr.success(resp.message,'Success!');
                }else{
                    toastr.error(resp.message,'Error!');
                }
            });
        }else{
            toastr.error('id or model is invalid','Error!');
        }
    });	

    $(document).on('blur','.weld-kp',function(){
        $('.weld-type').val('W');
        $('.weld-crossing-inp').val('');
        $('.weld-crossing').html('');
    });

    $(document).on('blur','.kp-landowner',function(){
        var _from = $('.landowner-from-kp').val();
        var _to = $('.landowner-to-kp').val();
        if(_from != "" && _to != ""){
            getLandowner(_from, _to);
        }
    });

    $(document).on('change','.change-ndt-outcome',function(){
        var _current_val = $(this).val();
        var _this = $(this);
        //as per client says don't remove the weld
        // if(_current_val == "Cut Out"){
        //     swal({
        //         title: messages.oops,
        //         text: messages.outcomeWarning,
        //         type: "warning",
        //         showCancelButton: true,
        //         confirmButtonColor: "#DD6B55",
        //         confirmButtonText: "Yes",
        //         cancelButtonText: "No",
        //         closeOnConfirm: true,
        //         closeOnCancel: true
        //     }, function (isConfirm) {
        //         if (!isConfirm) {
        //             _this.val('');
        //         }         
        //     });
        // }
    });
    $(document).on('change','#change-welder',function(){
        var _current_val = $(this).val();
        window.location.href = baseurl+'/report/report/welder-detail?welder_name='+_current_val;
    });    
	var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;
    
        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
    
            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };
	//##############--------All form submit here-----------############
	$(document).on('beforeSubmit','form',function(e) {
        var form      = $(this);
        var formData  = new FormData( this );
        var formId    = form.attr('id');

        var submitForm =function(formData){
            if(formId == 'plant-dashboard-form'){
                return false;
            }
            if(form.find('button[type="submit"]').length > 0){
                form.find('button[type="submit"]').attr('disabled','disabled');	
            }
            $.ajax({
                url : form.attr("action"),
                type: form.attr("method"),
                data: formData,
                processData: false,
                contentType: false,
                dataType:"json",
                success: function (data) {  
                    if(form.find('button[type="submit"]').length > 0){
                        form.find('button[type="submit"]').removeAttr('disabled');
                    }           
                    if(data.status){
                        if(typeof(data.modelData) != "undefined" && typeof(data.modelData.is_anomally) != "undefined" && data.modelData.is_anomally!== null && data.modelData.is_anomally =="Yes") {
                            toastr.warning(data.modelData.why_anomally,'Your data has been saved as anomaly',{timeOut:0,closeButton: !0})
                        }else{

                            toastr.success(messages.saved,messages.success);
                        }
                        $.pjax.reload({container: '#idofpjaxcontainer'}).done(function () {
                            $("html, body").animate({ scrollTop: 0 }, "slow");
                            $.pjax.reload({container: '#listproject'});
                        });
                    } else {
                        if(jQuery.type(data.message) == "object"){                    
                            $.each(data.message, function( index, value ){
                                $.each(value , function( k, v ){
                                    toastr.error(v,index,{timeOut:0,closeButton: !0});
                                });
                            });
                        }else{
                            if(jQuery.type(data.message) == "string"){     
                            toastr.error(data.message,'Oops!',{timeOut:0,closeButton: !0});
                            }else{
                                toastr.error(JSON.stringify(data.message),'Oops!',{timeOut:0,closeButton: !0});
                            }
                        }
                    }
                },
                error: function () {  
                    if(form.find('button[type="submit"]').length > 0){
                        form.find('button[type="submit"]').removeAttr('disabled');	
                    }            
                    toastr.error('Your server request has been failed,please try after sometime','Request Failed!',{timeOut:0,closeButton: !0});
                }
            });
        }

        if(formId =="slam-form"){            
            var fy = false;
            $(".mainans").each(function() {
                if($(this).val()==""){
                    toastr.error("Please ensure all questions have been answered",'Oops!',{timeOut:0,closeButton: !0});
                    fy = true;
                    return false;
                }
            });
            if(fy== true){
                return false;
            }            
            if(!$('#slam-form').valid()){
                return false;
            }   
            var EditId = typeof getUrlParameter('EditId')=="undefined"?0:getUrlParameter('EditId')
            call({ url: 'report/safety/slam?EditId='+EditId+'&isCheck=1', params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status==true){
                    if(resp.status_two== "warning"){
                        swal({
                            title:'Warning',
                            text:"You answered that a risk is NOT managed. DO NOT PROCEED Seek help from your supervisor",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes",
                            cancelButtonText: "No",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        }, function (isConfirm) {
                            if (isConfirm) {
                                submitForm(formData);
                            }         
                        });
                    } else{
                        $.pjax.reload({container: '#idofpjaxcontainer'}).done(function () {
                            $("html, body").animate({ scrollTop: 0 }, "slow");
                            $.pjax.reload({container: '#listproject'});
                        });
                    }                   
                }else{
                    submitForm(formData);
                }
            });
           
        }else if(formId =="pipe-form"){
            var EditId = typeof getUrlParameter('EditId')=="undefined"?0:getUrlParameter('EditId')
            call({ url: 'pipe/warning/pipe-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status== true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });
           
        }else if(formId =='pipe-reception-form'){
            var EditId = typeof getUrlParameter('EditId')=="undefined"?0:getUrlParameter('EditId')
            call({ url: 'pipe/warning/pipe-reception-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status== true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId =='pipe-transfer-form'){
            var EditId = typeof getUrlParameter('EditId')=="undefined"?0:getUrlParameter('EditId')
            call({ url: 'pipe/warning/pipe-transfer-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status== true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId =='pipe-stringing-form'){
            var EditId = typeof getUrlParameter('EditId')=="undefined"?0:getUrlParameter('EditId')
            call({ url: 'pipe/warning/pipe-stringing-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status== true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId =='pipe-cleargrade-form'){
            var EditId = typeof getUrlParameter('EditId')=="undefined"?0:getUrlParameter('EditId')
            call({ url: 'pipe/warning/pipe-cleargrade-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status== true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId =='pipe-bending-form'){
            var EditId = typeof getUrlParameter('EditId')=="undefined"?0:getUrlParameter('EditId')
            call({ url: 'pipe/warning/pipe-bending-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status== true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId =='pipe-cutting-form'){
            swal({
                title:'Warning',
                text:"Are you sure you want to cut ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    var EditId = typeof getUrlParameter('EditId')=="undefined"?0:getUrlParameter('EditId')
                    call({ url: 'pipe/warning/pipe-cutting-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                        if(resp.status== true){
                            swal({
                                title:'Warning',
                                text:resp.message +" Do you still wish to continue?",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Yes",
                                cancelButtonText: "No",
                                closeOnConfirm: true,
                                closeOnCancel: true
                            }, function (isConfirm) {
                                if (isConfirm) {
                                    submitForm(formData);
                                }         
                            });
                        }else{
                            submitForm(formData);
                        }
                    });  
                }  
            });       
        }else if(formId =='welding-form'){
            var EditId = typeof getUrlParameter('EditId')=="undefined"?0:getUrlParameter('EditId')
            call({ url: 'pipe/warning/welding-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status== true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId =='parameter-form'){
            var EditId = typeof getUrlParameter('EditId')=="undefined"?0:getUrlParameter('EditId')
            call({ url: 'pipe/warning/welding-parameter-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status== true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId =='ndt-form'){
            var EditId = typeof getUrlParameter('EditId')=="undefined"?0:getUrlParameter('EditId')
            call({ url: 'pipe/warning/welding-ndt-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status== true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            $('.main-weld-id').val(0);
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId =='weldrepair-form'){
            var EditId = typeof getUrlParameter('EditId')=="undefined"?0:getUrlParameter('EditId')
            call({ url: 'pipe/warning/welding-weldrepair-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status== true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            $('.main-weld-id').val(0);
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId =='production-form'){
            var EditId = typeof getUrlParameter('EditId')=="undefined"?0:getUrlParameter('EditId')
            call({ url: 'pipe/warning/welding-production-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status== true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            $('.main-weld-id').val(0);
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId =='coatingrepair-form'){
            var EditId = typeof getUrlParameter('EditId')=="undefined"?0:getUrlParameter('EditId')
            call({ url: 'pipe/warning/welding-coatingrepair-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status== true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            $('.main-weld-id').val(0);
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId == 'trenching-form'){
            var EditId = typeof getUrlParameter('EditId') == "undefined" ? 0 : getUrlParameter('EditId');
            call({ url: 'pipe/warning/civil-trenching-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status == true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId == 'lowering-form'){
            var EditId = typeof getUrlParameter('EditId') == "undefined" ? 0 : getUrlParameter('EditId');
            call({ url: 'pipe/warning/civil-lowering-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status == true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId == 'backfilling-form'){
            var EditId = typeof getUrlParameter('EditId') == "undefined" ? 0 : getUrlParameter('EditId');
            call({ url: 'pipe/warning/civil-backfilling-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status == true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId == 'reinstatement-form'){
            var EditId = typeof getUrlParameter('EditId') == "undefined" ? 0 : getUrlParameter('EditId');
            call({ url: 'pipe/warning/civil-reinstatement-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status == true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId =='cleanguage-form'){
            var EditId = typeof getUrlParameter('EditId')=="undefined"?0:getUrlParameter('EditId')
            call({ url: 'pipe/warning/precom-cleanguage-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status== true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId =='hydrotesting-form'){
            var EditId = typeof getUrlParameter('EditId')=="undefined"?0:getUrlParameter('EditId')
            call({ url: 'pipe/warning/precom-hydrotesting-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status== true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId == 'cable-form'){
            var EditId = typeof getUrlParameter('EditId') == "undefined" ? 0 : getUrlParameter('EditId');
            call({ url: 'pipe/warning/cable-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status == true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId == 'cab-stringing-form'){
            var EditId = typeof getUrlParameter('EditId') == "undefined" ? 0 : getUrlParameter('EditId');
            call({ url: 'pipe/warning/cab-stringing-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status == true){
                    swal({
                        title:'Warning',
                        text:resp.message +" This record will be added to the anomaly list. Do you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });           
        }else if(formId == 'cab-splicing-form'){
            var EditId = typeof getUrlParameter('EditId') == "undefined" ? 0 : getUrlParameter('EditId');
            call({ url: 'pipe/warning/cab-splicing-warning?EditId='+EditId, params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status == true){
                    swal({
                        title:'Warning',
                        text:resp.message+"\n"+" This record will be added to the anomaly list.\nDo you still wish to continue?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        cancelButtonText: "No",
                        closeOnConfirm: true,
                        closeOnCancel: true
                    }, function (isConfirm) {
                        if (isConfirm) {
                            submitForm(formData);
                        }         
                    });
                }else{
                    submitForm(formData);
                }
            });         
        }else if(formId == 'clearance-form'){
            call({ url: 'report/report/clearance', params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status == true){
                   $('#clearance').html(resp.html);
                }else{
                    alert('Parameters are missing');
                }
            });        
        }else if(formId == 'production-report'){
            $('.btn-production-report').html('Getting Report...');
            $('.btn-production-report').attr('disabled',true);
            call({ url: 'report/report/production?filter=true', params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status == true){
                    $('.production-data').html(resp.html);
                }
                $('.btn-production-report').html('Get Report');
                $('.btn-production-report').attr('disabled',false);
            });           
        }else if(formId == 'welder-combine-form'){
            $('.btn-welder-combine').html('Searching...');
            $('.btn-welder-combine').attr('disabled',true);
            call({ url: 'report/report/welder-combine', params: form.serialize(), type: 'POST' }, function(resp) {
                if(resp.status == true){
                    $('.tbl-data').html(resp.html);
                }
                $('.btn-welder-combine').html('Search');
                $('.btn-welder-combine').attr('disabled',false);
            });           
        } else if(formId == 'taxonomy-form'){
            var queflag = false;
            $(".list-question").each(function() {
                if($(this).val()==""){
                    toastr.error("Please ensure all questions have been filled",'Oops!',{timeOut:0,closeButton: !0});
                    queflag = true;
                    return false;
                }
            });
            if(queflag){
                return false;
            } else {
                submitForm(formData);
            }
        } else if(formId == 'vehicle-schedule-form'){
            var queflag = false;
            //validate part
            if($(".vehicle-part").length > 0){
                $(".vehicle-part").each(function() {
                    if($(this).val() == 0){
                        toastr.error("Please ensure all parts have been selected.", 'Oops!', {timeOut:0,closeButton: !0});
                        queflag = true;
                        return false;
                    }
                });
            } else {
                toastr.error("Please select atleast one part to the vehicle.", 'Oops!', {timeOut:0,closeButton: !0});
                queflag = true;
                return false;
            }

            //validate barcode
            // if(!queflag && $(".part-barcode").length > 0){
                // $(".part-barcode").each(function() {
                //     if($(this).val()==""){
                //         toastr.error("Please ensure all barcodes have been filled.", 'Oops!', {timeOut:0,closeButton: !0});
                //         queflag = true;
                //         return false;
                //     }
                // });
                
                var _value_array = [];
                var _dropdown_array = [];
                // $(".part-container").each(function() {
                //     _value_array.push($(this).find('input').val());
                // });
                
                $(".vehicle-part").each(function() {
                    _dropdown_array.push($(this).val());
                });
                // var _value_array_sort = _value_array.sort(); 
                var _dropdown_array_sort = _dropdown_array.sort(); 

                //for input text
                // var _value_array_duplicate = [];
                // for (var i = 0; i < _value_array_sort.length - 1; i++) {
                //     if (_value_array_sort[i + 1] == _value_array_sort[i]) {
                //         _value_array_duplicate.push(_value_array_sort[i]);
                //     }
                // }
                
                //for dropdown value
                var _dropdown_array_duplicate = [];
                for (var i = 0; i < _dropdown_array_sort.length - 1; i++) {
                    if (_dropdown_array_sort[i + 1] == _dropdown_array_sort[i]) {
                        _dropdown_array_duplicate.push(_dropdown_array_sort[i]);
                    }
                }
                // if(_value_array_duplicate.length > 0){
                //     toastr.error("There are some duplicate barcodes found. Please make them unique.", 'Oops!', {timeOut:0,closeButton: !0});
                //     queflag = true;
                //     return false;
                // }
                
                if(_dropdown_array_duplicate.length > 0){
                    toastr.error("There are some duplicate parts found. Please make them unique.", 'Oops!', {timeOut:0,closeButton: !0});
                    queflag = true;
                    return false;
                }
            // }
            
            if(queflag){
                return false;
            } else {
                submitForm(formData);
            }
        } else if(formId == 'vehicle-inspection-form'){
            var queflag = false;
            $(".part-status").each(function() {
                if($(this).val() == ""){
                    toastr.error("Please ensure all questions have been answered", 'Oops!', {timeOut:0,closeButton: !0});
                    queflag = true;
                    return false;
                }
            });
            if(queflag){
                return false;
            } else {
                submitForm(formData);
            }
        }else {
           submitForm(formData);
        }        
	}).on('submit', function(e){
			e.preventDefault();
    });
    $(document).on('change','.pull-kp',function(e) {
        var kp = $(this).val();
        call({ url: 'welding/default/get-geo-code', params: {'kp':kp}, type: 'GET' }, function(resp) {
                $('.put-kp').val(resp.code);
        });
    });    
    $(document).on('click','.legend-button',function(e) {
        var checkbox = [];
        $(".label-checkbox:not(:checked)").each(function(){
            checkbox.push($(this).val());
        });
        var url = baseurl+'report/report/visual-progress';
        if(checkbox.length>0){           
            $.each( checkbox, function( index, value ){
                if(index==0){
                    url = url+'?'+value+'=false';
                }else{
                    url = url+'&'+value+'=false';
                }
            });
             
        }
        window.location.href =  url;
        
    });
    $(document).on('change','.inp-file',function(e) {
        $('.pjax-loader').show();
        var file = this.files[0];
        var _this = $(this);
        // console.log(file.name);
        var formData = new FormData();
        formData.append('CsvImport[file]', file);
        $.ajax({      
            url: _this.attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType:'json',
            //Ajax events
            success: function(data){
                $('.pjax-loader').hide();
                $.pjax.reload({container:'#idofpjaxcontainer'});
                if(data.status){                   
                    toastr.success(messages.saved,data.message);
                    if(typeof(data.error) != "undefined" && data.error !== null) {
                        $.each(data.error, function (key, val) {
                            toastr.error(val,'Pipe No : '+key);
                        });
                    }
                }else{
                    toastr.error(data.message,messages.opps,{timeOut:0,closeButton: !0});
                }
                _this.val('');
            },
			error: function () {
                $('.pjax-loader').hide();
                toastr.error('Your server request has been failed,please try after sometime','Request Failed!',{timeOut:0,closeButton: !0});
			}
        });
        //uploadFile(file);
    });

    $(document).on('click', '.make-sequence', function(e){
        e.preventDefault();
        var _this = $(this);
        _this.attr('disabled', true);
        call({ url: 'report/report/sequence', params:{}, type: 'GET' }, function(resp) {
            _this.attr('disabled', false);
            if(resp){
                toastr.success('Sequence successfully done.','Success!');
                setTimeout(function(){
                    location.reload();
                },1500);
            }
        });
    });

    $(document).on('focusout', '.calc-travel', function(e){
        e.preventDefault();
        var rol = $('.parameter-rol').val();
        var rot = $('.parameter-rot').val();

        if(rol !== '' && rot !== ''){
            var obj = {
                rol: rol,
                rot: rot
            };
            var result = calcParameterTravel(obj);
            $('.calc-travel-result').val(result);
            $('.calc-travel-result').trigger('change');
        }
    });

    $(document).on('change', '.calc-travel-result', function(e){
        e.preventDefault();
        var amps = $('.parameter-amps').val();
        var volt = $('.parameter-volt').val();
        var travel = $('.parameter-travel').val();

        if(amps !== '' && volt !== '' && travel !== ''){
            var obj = {
                amps: amps,
                volt: volt,
                travel: travel
            };
            var result = calcParameterHeatInput(obj);
            $('.calc-heat-result').val(result);
        }
    });
    
    $(document).on('focusout', '.calc-heat', function(e){
        e.preventDefault();
        var amps = $('.parameter-amps').val();
        var volt = $('.parameter-volt').val();
        var travel = $('.parameter-travel').val();

        if(amps !== '' && volt !== '' && travel !== ''){
            var obj = {
                amps: amps,
                volt: volt,
                travel: travel
            };
            var result = calcParameterHeatInput(obj);
            $('.calc-heat-result').val(result);
        }
    });

    $(document).on('focusout', '.duplicate-cutting-check', function(e){
        var _number = $(this).val();        
        if(_number !== ''){
            call({ url: 'pipe/cutting/check-duplicate-cut', params: {'number': _number,}, type: 'POST' }, function(resp) {            
                if (resp.status) {
                    toastr.warning('Do you still want to cut?', 'The pipe number has already been cut.',{timeOut:5000,closeButton: !0})
                }
            });
        }
    });

    $(document).on('blur', '.cr-pipe-defect', function(e){
        var _number = $(this).val();
        var _this = $(this);
        if(_number !== ''){
            call({ url: 'welding/coatingrepair/check-valid-pipe', params: {'number': _number,}, type: 'POST' }, function(resp) {
                if(!resp.status){
                    _this.val('');
                    toastr.error("Pipe number is not available in Pipe List. Please enter a valid pipe number!", 'Oops!', {timeOut: 3000, closeButton: !0});
                } else {
                    call({ url: 'welding/coatingrepair/get-pipe-defects', params: {'number': _number,}, type: 'POST' }, function(resp) {
                        if (resp.status) {
                            var _html = '';
                            if(resp.data.length > 0){
                                $.each(resp.data, function(index, item) {
                                    _html += '<li class="list-group-item"><b>'+item+'</b></li>';
                                });
                            } else {
                                _html += '<li class="list-group-item">No defects found.</li>';
                            }
                            $('.defect-list').html(_html);
                        }
                    });
                }
            });
        }
    });

    $(document).on('focusout', '.coatingrepair-weld', function(e){
        e.preventDefault();
        var _weld = $(this).val();
        var _kp = $('.coatingrepair-kp').val();
        var _this = $(this);

        if(_weld !== '' && _kp !== ''){
            call({ url: 'welding/coatingrepair/check-valid-weld', params: {'weld': _weld, 'kp': _kp }, type: 'POST' }, function(resp) {
                if(!resp.status){
                    _this.val('');
                    toastr.error("Weld number is not available in Welding. Please enter a valid weld number!", 'Oops!', {timeOut: 3000, closeButton: !0});
                }
            });
        }
    });

    if($('.wr-req-fields').length > 0){
        $('.wr-req-fields').closest('.form-group').addClass('required');
        $('#weldingrepair-date').closest('.form-group').addClass('required');
        $('#weldingrepair-report_number').closest('.form-group').addClass('required');
    }
    $(document).on('change', '.wr-excavation', function(e){
        var _val = $(this).val();
        if(_val !== 'Cut-Out'){
            $('.wr-req-fields').closest('.form-group').addClass('required');
            $('#weldingrepair-date').closest('.form-group').addClass('required');
            $('#weldingrepair-report_number').closest('.form-group').addClass('required');
        } else {
            $('.wr-req-fields').closest('.form-group').removeClass('required');
            $('#weldingrepair-date').closest('.form-group').removeClass('required');
            $('#weldingrepair-report_number').closest('.form-group').removeClass('required');
        }
    });
});

function calcParameterTravel(obj){
    var rol = obj.rol;
    var rot = obj.rot;

    var calc = (rol / rot) * 60;
    calc = calc.toFixed(2);

    return calc;
}

function calcParameterHeatInput(obj){
    var amps = obj.amps;
    var volt = obj.volt;
    var travel = obj.travel;

    var calc = (amps * volt * 60) / (travel * 1000);
    calc = calc.toFixed(2);

    return calc;
}
