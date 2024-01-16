var values = {};
var plates = [];
var widthOf1Crop;
var canvas, image, drawImage;
var firstTime = true;
var material;
var timthumbSrc;
var mirror = true;
var changeUrlImage = false;

class Crop {
    constructor(image){
        this.image = image;
    }

    setValueX(){
        var numberPlates = parseInt($('#anzahlPlatten').val());

        return (drawImage.getDirection() == 'horizontal') ? values.width/numberPlates * 3 : values.width;
    }

    setValueY(){
        var numberPlates = parseInt($('#anzahlPlatten').val());

        return (drawImage.getDirection() == 'horizontal') ? values.height : values.height/numberPlates * 3;
    }

    getMaxWidth(){
        var numberPlates = parseInt($('#anzahlPlatten').val());

        return (drawImage.getDirection() == 'horizontal') ? widthOf1Crop * numberPlates : values.width;
    }

    getMaxHeight(){
        var numberPlates = parseInt($('#anzahlPlatten').val());

        return (drawImage.getDirection() == 'horizontal') ? values.height : values.height/3 * numberPlates;
    }

    isInitCrop(){
        return (firstTime);
    }

    initCrop(){
        var parent = this;

        var width = values.width;
        var height = values.height;

        var inputs = {
                x : $('#x'),
                y : $('#y'),
                width : $('#width'),
                height : $('#height')
           },
           fill = function(){
             if(parent.isInitCrop()){
                  //values.x = 0;
                  values.y = 0;
                  firstTime = false;
               }else{
                  firstTime = false;
                  values = parent.image.rcrop('getValues');
                  //drawImage.setAreaDrawCrop();
                  drawImage.setAreaDrawCropFill();
                  parent.image.rcrop('resize', values.width, values.height, values.x, values.y);
                  parent.drawLines();
               }
             };

        this.image.rcrop();
        this.image.on('rcrop-changed rcrop-ready', fill);

        setTimeout(function(){ parent.drawLines(); parent.image.rcrop('resize', width, height, values.x, 0);}, 20);
    }

    clearAllLines(){
        $('#draw-line').html('');
    }
    drawLines(){
        var numberPlates = parseInt($('#anzahlPlatten').val());
        var widthCrop, width;

        widthCrop = values.width;

        var i, str, xStart, xEnd, yStart, yEnd;

        this.clearAllLines();

        for(i = 1; i<numberPlates; i++){
            if(new DrawImage().getDirection() == 'horizontal'){
                width = (widthCrop / (new DrawImage().getTotalWidthPlate())) * (new DrawImage().getWidthPlate(i-1))
                xStart = (i == 1) ? values.x + width : width ;
                xEnd = xStart;
                yStart = values.y;
                yEnd = values.y + values.height;
                str = '<svg height='+yEnd+' width='+xStart+'> <line x1='+xStart+' y1='+yStart+' x2='+xEnd+' y2='+yEnd+' style="stroke:#fff;stroke-width:1" /></svg>';
                $('#draw-line').append(str);
           }else{
                yStart = (i == 1) ? values.y + values.height/3 : values.height/3 ;
                yEnd = yStart;
                xStart = values.x;
                xEnd = values.x + values.width;
                str = '<svg height='+yEnd+' width='+xEnd+'> <line x1='+xStart+' y1='+yStart+' x2='+xEnd+' y2='+yEnd+' style="stroke:#fff;stroke-width:1" /></svg>';
                $('#draw-line').append(str);
           }
        }
        //var str = '<svg> <line x1="0" y1="0" x2="200" y2="200" style="stroke:rgb(255,0,0);stroke-width:2" /></svg>';

    }
    rCrop(){
       var width = values.width;
       var height = values.height;

       var numberPlates = parseInt($('#anzahlPlatten').val());

       this.drawLines();

       this.image.rcrop('resize', width, height, values.x, values.y)
    }

    processCutout() {
       var parent = this;

       $('nav#top').hide();
       $('#myModal').addClass('show');

       setTimeout(function(){ parent.cutOut();}, 1000);
    }
    cutOut(){
       if(this.isInitCrop()){
         this.initCrop();
       }
       else{
         this.rCrop();
       }
    }
}

class DrawImage {
    constructor(canvas,image){
        this.canvas = canvas;
        this.image = image;
        this.spaceX = 2;
        this.spaceY = 2;
        this.defaultHeightPlate = 2000;
        this.defaultWidthPlate = 900;

    }

    getWidthAreaDraw(){
        return $('.drawimage').width();
    }

    getHeightcanvas(){
        var i, width = 0, height, percent = 0.7;

        height = plates[0].y;

        for(i =0; i < plates.length; i++ ){
            width = width + plates[i].x;
            //height = height + plates[i].y;
        }

        if(width < height)
          return this.getWidthAreaDraw() * percent;
        else
          return this.getWidthAreaDraw()/width * height * percent;
    }

    getWidthcanvas(){
        var i, width = 0, height;

        height = plates[0].y;

        for(i =0; i < plates.length; i++ ){
            width = width + plates[i].x;
            //height = height + plates[i].y;
        }

        if(width<height)
          return this.getWidthAreaDraw()/height * width;

        else
          return this.getWidthAreaDraw();
    }

    getWidthPlate(number){
        var i, width = 0;

        for(i = 0; i< plates.length; i++)
          width = width + plates[i].x;

        return Math.round((this.getWidthcanvas()/width) * plates[number].x);
    }

    getTotalWidthPlate(){
        var i, width = 0;

        for(i = 0; i< plates.length; i++)
          width = width + this.getWidthPlate(i);

        return width;
    }

    setValuewidthOf1Crop(){
        var numberPlates = parseInt($('#anzahlPlatten').val());
        var width = this.image.width;

        if((widthOf1Crop * numberPlates) >= width){
            values.x = 0;
            widthOf1Crop = Math.round(width/numberPlates)
        }else if( (  ((widthOf1Crop * numberPlates) + values.x) >= width  ) ){
            values.x = width - (widthOf1Crop * numberPlates);
        }
    }

    getWidthCrop(number){
        var i, width = 0;
        var numberPlates = parseInt($('#anzahlPlatten').val());

        for(i = 0; i< plates.length; i++)
          width = width + this.getWidthPlate(i);

        //return Math.round(((widthOf1Crop * numberPlates)/width) * this.getWidthPlate(number));
        return Math.round((values.width/width) * this.getWidthPlate(number));
    }

    getDirection() {
        return $('input[type=radio][name=Ausrichgung]:checked').attr('value');
    }

    clear(){
        var ctx = this.canvas.getContext('2d');
        ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        var w = canvas.width;
        canvas.width = 1;
        canvas.width = w;
        canvas.height = 600;
    }

    drawImage(sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight){
        var ctx = this.canvas.getContext('2d');
        //ctx.drawImage(this.image, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight);

        this.image.addEventListener('load', e => {
            ctx.drawImage(this.image, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight);
            //ctx.rotate(90 * Math.PI/180);
       });

    }

    drawImage1(sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight){
        var ctx = this.canvas.getContext('2d');
        ctx.setTransform(1,0,0,1,0,0);
        ctx.drawImage(this.image, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight);
    }

    mirror(sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight){
        var ctx = this.canvas.getContext('2d');
        ctx.setTransform(-1,0,0,1,this.canvas.width,0);

        ctx.drawImage(this.image, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight);
    }

    mirror1(canvas, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight){
        var ctx = canvas.getContext('2d');
        ctx.setTransform(-1,0,0,1,canvas.width,0);

        ctx.drawImage(this.image, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight);
    }

    setValueStartOfWidthHeight(){
        var numberPlates = parseInt($('#anzahlPlatten').val());

        switch(numberPlates){
            case 2:
              if(plates[1].x == 0){
                plates[1].x = this.defaultWidthPlate;
                plates[1].y = plates[0].y;
                $('#width2').val(plates[1].x);
              }

              plates[2].x = 0;
              plates[2].y = 0;
              break;

            case 3:
              if(plates[2].x == 0){
                plates[2].x = this.defaultWidthPlate;
                plates[2].y = plates[0].y;

                $('#width3').val(plates[2].x);
              }

              if(plates[1].x ==0){
                plates[1].x = (plates[0].y > plates[0].x) ? plates[0].y - plates[0].x : plates[0].x - plates[0].y;
                plates[1].x = (plates[1].x == 0) ? 1000 : plates[1].x;
                plates[1].y = plates[0].y;
                $('#width2').val(plates[1].x);
              }
              break;

            default:
              if(plates[0] == undefined){
                plates[0] = {};
                plates[0].x = this.defaultWidthPlate;
                plates[0].y = this.defaultHeightPlate;
                $('#height1').val(plates[0].y);
                $('#width1').val(plates[0].x);
              }
              //plates[0] = {};
              plates[1] = {};
              plates[2] = {};
              //plates[0].x = this.defaultWidthPlate;
              //plates[0].y = this.defaultHeightPlate;

              plates[1].x = 0
              plates[1].y = 0;

              plates[2].x = 0;
              plates[2].y = 0;
        }
    }

    getQmPlates() {
        var i, qm = 0, qm_, test;

        for(i = 0; i <= 2; i++)
          qm = qm + plates[i].x * plates[i].y;

        qm = (qm/1000000).toFixed(6).toString().split('.');
        //qm = (qm/1000000).toFixed(2).toString().split('.');
        qm_ = Math.floor((qm[1])/10).toString();

        return qm[0] + '.' + qm_;
    }

    setAreaDrawCrop(first){
       const widthCanvas = this.getWidthcanvas();
       const heightCanvas = this.getHeightcanvas();
       const maxWidthCrop = this.image.width;
       const maxHeightCrop = this.image.height;

       var numberPlates = parseInt($('#anzahlPlatten').val());

       //if(numberPlates == 1)
         //values.height = maxHeightCrop;

       values.width = Math.round((widthCanvas * values.height)/heightCanvas);

       if(values.width >= maxWidthCrop){
          values.x = 0;
          values.width = maxWidthCrop;
          values.height = Math.round((heightCanvas * values.width)/widthCanvas);

          if((values.height + values.y) >= maxHeightCrop)
            values.y = values.height + values.y - maxHeightCrop;
       }else if((values.width + values.x) >= maxWidthCrop)
            values.x = values.width + values.x -maxWidthCrop;
       else if(first){
          values.x = Math.round((maxWidthCrop - values.width)/2);
       }
    }

    setAreaDrawCropFill(){
       const widthCanvas = this.getWidthcanvas();
       const heightCanvas = this.getHeightcanvas();
       const maxWidthCrop = this.image.width;
       const maxHeightCrop = this.image.height;

       values.width = Math.round((widthCanvas * values.height)/heightCanvas);

       if(values.width >= maxWidthCrop){
          values.x = 0;
          values.width = maxWidthCrop;
          values.height = Math.round((heightCanvas * values.width)/widthCanvas);

          if((values.height + values.y) >= maxHeightCrop)
            values.y = values.height + values.y - maxHeightCrop;
       }else if((values.width + values.x) >= maxWidthCrop)
            values.x = values.width + values.x -maxWidthCrop;
    }

    setImgUseTimthumbStart(){
       const dom = $('#source');
       const dom1 = $('#sourcez');

       const width = dom.width();
       const src = dom1.attr('src');

       timthumbSrc = 'timthumb.php?src='+src+'&w='+width+'&zc=1';

       this.setImgTimthumb(timthumbSrc);
    }

    setImgTimthumb(src) {
       const dom = $('#source');
       const dom1 = $('#sourcez');

       dom.attr('src',src);
       dom1.attr('src',src);
    }

    setDefaultMaterial() {
        var count = 1;
        var numberPlate = parseInt($('#number_plate').val());

        $('.number_plates .padding_choose_number').each(function(e){
            if(count == numberPlate) {
              this.click();
              count++;
            }
            count++;
        })

        count = 0;
        $('.checkbox input:radio').each(function(e){
            if(count == 0)
              this.click();

            count++;
        })
    }

    drawCanvasMirror() {
        var width = this.image.width;
        var height = this.image.height;

        var canvas = document.getElementById('canvas_mirror');


        $('#canvas_mirror').attr('width',''+width+'');
        $('#canvas_mirror').attr('height',''+height+'');

        this.mirror1(canvas, 0, 0, width, height, 0, 0, width, height);

        var imagedata = canvas.toDataURL('image/png');
	    var imgdata = imagedata.replace(/^data:image\/(png|jpg);base64,/, "");
        var filename = $('#img_mirror').val() + '.png';

        $.ajax({
		url: 'index.php?route=product/product/drawCanvasMirror',
		type: 'post',
		data:{
			    imgdata: imgdata,
				filename: filename
			},
		dataType: 'json',
		beforeSend: function() {
		},
		complete: function() {
		  $('#canvas_mirror').hide();
        },
		success: function(json) {
		  $('#canvas_mirror').hide();
        },
        error: function(xhr, ajaxOptions, thrownError) {
        }
	});
    }

    processMirror() {
        var src = $('#source').attr('src');
        //src = 'timthumb.php?src='+src+'&w='+width+'&zc=1';

        var image = document.getElementById('sourcez');
        var src_ = 'image/uploads/' + $('#img_mirror').val() + '.png';
        var width = this.image.width;
        var timthumbSrc_ = 'timthumb.php?src='+src_+'&w='+width+'&zc=1';

        if(src == timthumbSrc)
          this.setImgTimthumb(timthumbSrc_);
        else
          this.setImgTimthumb(timthumbSrc);

        image.addEventListener('load', e => {
            changeUrlImage = false;
            mirror = true;

            var src = $('#source').attr('src');

            if(src != timthumbSrc) {
               new Crop($('#sourcez')).processCutout();
            }
        });
   }

    start() {
        this.setImgUseTimthumbStart();

        this.image.addEventListener('load', e => {
            if(!changeUrlImage) {
               values.height = this.image.height;
            this.image.style.display = 'none';
            const left = values.width/3;

            this.drawCanvasMirror();

            if(this.getDirection() == 'horizontal'){
                this.showImage(true);

                this.setDefaultMaterial();

                $('#height').css('top',10);
                $('#height').css('left',values.width/3+10);
            }else{
                left = x;
                top = y/3
                $(canvas).attr('width',left);
                $(canvas).attr('height',top);
                this.drawImage(0, 0, left, top, 0, 0, left, top);
                $('#height').css('top',top/2);
                $('#height').css('left',left+10);
                this.setValueWidthHeight(left,top);
            }
         }
        });
    }

    hideWidth(position){
        // hide height input of 2, 3
        $('#height2').hide();
        $('#height3').hide();

        switch(position){
            case 2:
              $('#width3').hide();
            break;

            case 1:
              $('#width2').hide();
              $('#width3').hide();
              break;
        }
    }

    hideHeight(position){
        // hide width input of 2, 3
        $('#width2').hide();
        $('#width3').hide();

        switch(position){
            case 2:
              $('#height3').hide();
            break;

            case 1:
              $('#height2').hide();
              $('#height3').hide();
              break;
        }
    }

    getLeftPlate(position){
        var i, left= 0;

        for(i = 0; i< position; i++)
          left = left + this.getWidthPlate(i);

        return left;
    }

    setPaddingLeftCanvas() {
        var width = $(window).width();
        var paddingLeft = (this.getWidthAreaDraw() - this.getWidthcanvas())/2
        if(width > 786) {
            $(canvas).parent().css('padding-left',''+paddingLeft+'px');
            $('#width').css('left',''+paddingLeft+'px')
        }
    }
    horizontalDirection(){
            var sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight, marginLeftPlate;
            var numberPlates = parseInt($('#anzahlPlatten').val());

            sHeight = values.height;

            dx = 0;
            dy = 0;

            dHeight = this.getHeightcanvas();

            var i;

            $(canvas).attr('width',this.getWidthcanvas());
            $(canvas).attr('height',this.getHeightcanvas());

            this.setPaddingLeftCanvas();

            this.hideWidth(numberPlates);

            //set value.x and widthOf1Crop
            this.setValuewidthOf1Crop();

            sx = (values.x == undefined) ? 0 : values.x;
            sy = (values.y == undefined) ? 0 : values.y;

            for(i = 0; i < numberPlates; i++){
                dWidth =  this.getWidthPlate(i);
                sWidth = this.getWidthCrop(i);

                if(!changeUrlImage)
                  this.drawImage1(sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight);
                else
                  this.mirror(sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight);

                //show width of plate i if i is not first
                if(i != 0) {
                    $('#width'+(i+1)+'').css('left', this.getLeftPlate(i));
                    $('#width'+(i+1)+'').css('opacity','1');
                    $('#width'+(i+1)+'').css('display','inline-block');
                }

                sx = sx + sWidth;
                dx = dx + dWidth + this.spaceX;

                //set left position of height input
                $('#height').css('left',dx);
            }
    }

    verticalDirection(width,height){
            var x = width;
            var y = height;

            //this.setValueStartOfWidthHeight(x,y/3);

            var sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight;
            var numberPlates = parseInt($('#anzahlPlatten').val());

            sWidth = x;
            sHeight = y/3;

            sx = (values.x != undefined) ? values.x : 0;
            sy = (values.y != undefined) ? values.y : 0;

            dx = 0;
            dy = 0;

            dHeight = 0;

            var i;

            //set height canvas
            for(i=1; i<=numberPlates; i++){
                  dHeight = parseInt($('#height'+i+'').val());
                  dy = dy + dHeight + this.spaceY;
              }

            $(canvas).attr('height',dy);
            $(canvas).attr('width',sWidth);

            //set left position of height input
            $('#height').css('left',sWidth + 10);

            //restart dy
            dy = 0;

            //hide height base on numberPlates
            this.hideHeight(numberPlates);

            for(i=1; i<=numberPlates; i++){
                dWidth = parseInt($('#width1').val());
                dHeight = parseInt($('#height'+i+'').val());

                this.drawImage1(sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight);

                //show width of plate i if i is not first
                if(i != 1) {
                    $('#height'+i+'').css('margin-top',dHeight);
                    $('#height'+i+'').css('opacity','1');
                    $('#height'+i+'').show();
                }

                sy = sy + sHeight;
                dy = dy + dHeight + this.spaceY;
            }
    }

    setValuePlate(){
        plates[0].y = parseInt($('#height1').val());
        plates[0].x = parseInt($('#width1').val());

        if(plates[1].x != 0) {
            plates[1].x = parseInt($('#width2').val());
            plates[1].y = parseInt($('#height1').val());
        }

        if(plates[2].x != 0) {
            plates[2].x = parseInt($('#width3').val());
            plates[2].y = parseInt($('#height1').val());
        }
    }

    showImage(firstTime){
        this.setValueStartOfWidthHeight();
        this.setAreaDrawCrop(firstTime);

        if(this.getDirection() == 'horizontal')
            this.horizontalDirection();
        else
            this.verticalDirection(0,0);
    }

    showImageCutout(setValueWidthHeight){
        var numberPlates = parseInt($('#anzahlPlatten').val());

        var x = values.width;
        var y = values.height;

        if(this.getDirection() == 'horizontal'){
            if(setValueWidthHeight)
              this.setValueStartOfWidthHeight(x/numberPlates,y);

            this.horizontalDirection(x*3/numberPlates,y);
      }else{
            if(setValueWidthHeight)
              this.setValueStartOfWidthHeight(x,y/numberPlates);

            this.verticalDirection(x,y*3/numberPlates);
        }
    }

    setPrice(material_id) {
        var qm = this.getQmPlates();

        $.ajax({
		url: 'index.php?route=product/product/getPriceMaterial',
		type: 'post',
		data: { 'material_id': material_id, 'qm': qm },
		dataType: 'json',
		beforeSend: function() {
			//$('#recurring-description').html('');
		},
		success: function(html) {
			$('.col-3-price .price').show();
            $('.col-3-price .price').html(html + '€');
            $('#product_price').val(html);
        }
	});
    }

    mirrorX() {
        var dy = (values.y != undefined)? values.y : 0;
        this.mirror(values.x, dy, values.width, values.height, 0, 0, this.canvas.width, this.canvas.height);
    }

    notMirrorX() {
        var dy = (values.y != undefined)? values.y : 0;
        this.drawImage1(values.x, dy, values.width, values.height, 0, 0, this.canvas.width, this.canvas.height);
    }
}


$(document).ready(function(){
  canvas = document.getElementById('canvas');
  image = document.getElementById('source');
  drawImage = new DrawImage(canvas,image);

  $('#anzahlPlatten').change(function(){
     drawImage.initData();
     drawImage.showImage();
  });

  $('.custom-control-input').click(function(){
    drawImage.showImage();
  })

  $('.drawimage :input').on('input',function(e){
    drawImage.setValuePlate();
    drawImage.showImage();
    drawImage.setPrice(material);
  });

  $('#height :input').on('input',function(e){
    drawImage.setValuePlate();
    drawImage.showImage();
    drawImage.setPrice(material);
  });

  $('.button-cut').click(function(){
    if(changeUrlImage) {
        drawImage.processMirror();
    }
    else {
        new Crop($('#sourcez')).processCutout();
    }
  })

  $('.change-direction').click(function(){
    if(mirror) {
        mirror = false;
        changeUrlImage = true;
        drawImage.showImage();
    }
    else {
        mirror = true;
        changeUrlImage = false;
        drawImage.showImage();
    }
  })

  $('.btn-save').click(function(){
    $('nav#top').show();
    drawImage.showImage();
  })

  $('.radio input:radio').change(function(){
    var addition = parseInt($(this).attr('price').replace('$','').replace('â‚¬',''));
    value = (value == null) ? parseInt($('.price-product li span').html().replace('$','').replace('â‚¬','')) : value;
    var newValue = addition + value;
    $('.price-product li span').html('â‚¬'+newValue+'');
  })

  $('.checkbox input:radio').change(function(){
    var id = $(this).attr('id');
    material = id;

    drawImage.setPrice(id);
  })

  $('.padding_choose_number').click(function(){
    $('.plate_1').css('background','url("image/number_plates/1.png")');
    $('.plate_2').css('background','url("image/number_plates/2.png")');
    $('.plate_3').css('background','url("image/number_plates/3.png")');

    var number = parseInt($(this).html());
    $(this).css('background','url("image/number_plates/'+number+'_.png")');
    $('#anzahlPlatten').val(number);

    drawImage.showImage();

    drawImage.setPrice(material);
  })

  $('label.check').click(function(){
    $(this).parent().find('input[type=radio]').click();
  })

  $('#myModal .close').click(function(){
    $('nav#top').show();
  })
})

  $(window).load(function() {
    drawImage.start();
  });

