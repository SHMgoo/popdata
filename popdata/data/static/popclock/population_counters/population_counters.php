<!-- U.S. and World populations -->
<script type="text/javascript">
$(function ( )
{
        popCountersViewController = new PopCountersViewController(api, animator, 'us-pop-container', 'us-pop-components-container', 'world-pop-container'<?php if ( _PAGE_ == 'print' ): echo ', false'; endif; ?>);

<?php 
     if ( _PAGE_ == ' index' || _PAGE_ == 'embed' || _PAGE_ == 'population_widget_310x200' || _PAGE_ == 'population_widget_200x402' || _PAGE_ == 'country' || _PAGE_ == 'world' ): ?>
        popCountersViewController.animator = animator;
<?php endif; ?>
        usPopContainer = $('us-pop-container');
        usPopComponentsContainer = $('#us-pop-components-container');
    <?php if (!  _PAGE_ == 'population_widget_310x200'): ?>
        usPopContainer.addClass('selected');
     <?php endif; ?>
        usPopComponentsContainer.find('p').text(config.components.us_rates.label);

        worldPopContainer = $('world-pop-container');
        worldPopComponentsContainer = $('#world-pop-historical');
     <?php if ( _PAGE_ == 'population_widget_310x200'): ?>
     	worldPopContainer.addClass('selected');
     <?php endif; ?>
        worldPopComponentsContainer.find('p').append(config.components.world_rates.label);

<?php if ( _PAGE_ == 'index' || _PAGE_ == 'country' || _PAGE_ == 'world'): ?>
        popCountersViewController.createViews(270, 115, 15, 44, 36, 484, { main: '<?php echo _APPROOT_;?>images/strip.png'});

<?php elseif ((isset($is_home_legacy) && $is_home_legacy) || (isset($is_home_page) && $is_home_page)): ?>
        popCountersViewController.headerDateLabel = $('div.component-header').find('p');

        <?php if (isset($is_home_legacy) && $is_home_legacy): ?>
        popCountersViewController.stripPopulation = false;
        <?php endif; ?>

        delete popCountersViewController.usComponentsID;

        <?php if (isset($is_home_legacy) && $is_home_legacy): ?>
        popCountersViewController.createViews(0, 0, 0, 17, 14, 187, { main: '<?php echo _APPROOT_;?>images/strip-small-legacy.png' });
        <?php else: ?>
        popCountersViewController.createViews(0, 0, 0, 22, 18, 242, { main: '<?php echo _APPROOT_;?>images/strip-small.png' });
        <?php endif; ?>

        worldPopComponentsContainer.hide();
        usPopComponentsContainer.hide();
        
<?php elseif ($is_widget_264): ?>
        popCountersViewController.headerDateLabel = $('div.component-header').find('p');

        delete popCountersViewController.usComponentsID;

        popCountersViewController.createViews(0, 0, 0, 22, 18, 242, { main: './images/strip-small.png' });

        worldPopComponentsContainer.hide();
        usPopComponentsContainer.hide();
<?php elseif ($is_widget_313): ?>
        popCountersViewController.headerDateLabel = $('div.component-header').find('p');

        delete popCountersViewController.usComponentsID;

        popCountersViewController.createWidgetViews(0, 0, 0, 22, 18, 242, { main: './images/strip-small-home.png' });

        worldPopComponentsContainer.hide();
        usPopComponentsContainer.hide();

        $('#widget-popup').hide();

        $('h2.widget_title a.embed').click(function(event) {
console.log('widget embed show');
            event.stopPropagation();
            event.preventDefault();
            $('#widget-popup').show();
        });

        $('#widget-popup .close-button a').click(function(event) {
            event.stopPropagation();
            event.preventDefault();
            $('#widget-popup').hide();
        });

        $('#widget-popup').click(function(event) {
            event.stopPropagation();
        });

        //$('#us-pop-container, #world-pop-container').click(function(event) {
        $('.widget-313').click(function(event) {
            event.stopPropagation();
            window.top.location = '<?php echo _APPROOT_;?>';
        });

        $('.widget_313_link').click(function(event) {
            console.log('clicked on chevron');
            event.stopPropagation();
            window.top.location = '<?php echo _APPROOT_;?>';
        });


        $('div#widget-popup .contents textarea').focus(function () { $(this).select(); } ).mouseup(function (e) {e.preventDefault();});

<?php else: // Embed or Print ?>

        popCountersViewController.displayDateFormat = 'mm/dd/yy';
        popCountersViewController.createViews(150, 75, 10, 22, 18, 242, { main: '<?php echo _APPROOT_;?>images/strip-small.png' });

        
        
    <?php if ( _PAGE_ == 'embed' || _PAGE_ == 'population_widget_200x402' ): ?>
        $('#world-pop-wrapper').hide();
    <?php endif; ?>
    <?php if ( _PAGE_ == 'population_widget_310x200'): ?>
      $('#us-pop-wrapper').hide();
    <?php endif; ?>

    <?php if ( _PAGE_ == 'embed' ): ?>
        var loaded_share = load_share();
        loaded_share.done(function ( ) {

                $('#download-this').click(function ( ) {
                        var gettingCSV = popCountersViewController.getCSV();
                        gettingCSV.done(function ( csv ) {
                                download_csv(csv, component);
                        });
                        return false;
                });
        });
    <?php endif; ?>

        $('ul a').click(function(event) {
            event.preventDefault();
            var $this = $(this);
            var href = $this.prop('href');
            var index = $this.parent().index();

            $('ul a').each(function(index) {
                $(this).removeClass('selected');
            });
            $this.addClass('selected');

            if (index == 0) {
                $('#us-pop-wrapper').show();
                $('#world-pop-wrapper').hide();
                $('#footnotes h3').text(config.footnotes.counter.label);
                $('#footnotes #counter-footnote').html(config.footnotes.counter.content);
            } else {
                $('#us-pop-wrapper').hide();
                $('#world-pop-wrapper').show();
                $('#footnotes h3').text(config.embed_world_footnotes.world_counter.label);
                $('#footnotes #counter-footnote').html(config.embed_world_footnotes.world_counter.content);                
            }


            return false;
        });
<?php endif; ?>
});

</script>
<?php
// check if this is the census main page,
// legacy format
if (isset($is_home_legacy) && $is_home_legacy):
?>
<style type="text/css">
        body {
                height: 115px;
        }
 /* odometer */
        div#us-pop-container p, div#world-pop-container p {
                margin: 50px auto;
                padding: 100px;
                clear: both;
                float: none;
        }

        div#us-pop-container p span, div#world-pop-container p span {
                margin-right: 1px;
                display: inline-block;
                overflow: hidden;
                height: 17px;
        /*      float: left; */
                position: relative;
                top: 3px;
                color: #a3a3a3;
                font-weight: bold;
                font-size: 0.75em;
 /*             text-shadow: 0px 0px 2px #000000;
                filter: dropshadow(color=#000000, offx=0, offy=0); */
        }

        div#us-pop-container p span.rolling-digit, div#world-pop-container p span.rolling-digit {
                top: 0px;
                border: 1px solid #102e40;
                -webkit-border-radius: 3px;
                border-radius: 3px;
                -webkit-box-shadow:  0px 4px 4px 0px rgba(184, 209, 221, 0.1);
                box-shadow:  0px 4px 4px 0px rgba(184, 209, 221, 0.1);
                background-image: url('<?php echo _APPROOT_;?>images/strip-bg-legacy.png');
                background-position: center center;
                background-size: 100% 100%;

        }

        div#us-pop-container p span.rolling-digit img, div#world-pop-container p span.rolling-digit img {
                position: relative;
        }
  /*
  Font family is set within the Census home.css
  Font sizes are set to 1em; therefore, should scale proportionate to the page
  .remove-on-deploy is the container div for the "widget" - once in place can be removed
  */
        div#legacy.remove-on-deploy {
                /*width: 230px;*/
                border: 1px solid #d4e9f9;
                padding: 10px;
        }

        /* Read more link - can be replaced by Census code */
        div#legacy a {
                display: block;
        }
        div#legacy a img {
                border: 0;
        }

        div#legacy .component-header {
                /*display: none;*/
                font-size: 0.5em;
                text-align: center;
        }
        div#legacy .component-header p {
                margin: 0;
                padding: 3px 0 0;
        }
        div#legacy div#us-pop-container h3, div#legacy div#world-pop-container h3 {
                margin: 0 auto;
                padding: 2px 0;
                font-size: .7em;
                font-weight: bold;
                color: #064167;
                background-repeat: no-repeat;
                background-position: center left;
                width: 152px;
        }
        div#legacy div#us-pop-container h3 {
                background-image: url('<?php echo _APPROOT_;?>css/images/us-population-icon-legacy.png');
        }
        div#legacy div#world-pop-container h3 {
                background-image: url('<?php echo _APPROOT_;?>css/images/world-population-icon-legacy.png');
        }
        div#legacy div#us-pop-container p, div#legacy div#world-pop-container p {
                margin: 0;
                /*margin-bottom: 5px;*/
                padding: 0;
                font-size: 1em;
                font-weight: normal;
        }
        div#legacy div#us-pop-container, div#legacy div#world-pop-container {
                margin: 0;
                padding: 0;
                text-align: center;
                font-weight: bold;
        }
        div#legacy div#us-pop-container {
                margin-bottom: 2px;
        }

        div#legacy div#world-pop-container {
                margin-bottom: 3px;
        }

        div#legacy div#us-pop-container p {
                color: #289ede;
        }

        .floatright {
                float: right;
        }

        .floatleft {
                float: left;
        }

        .ffix {
                height: 0px;
                overflow: hidden;
                clear: both;
                float: none;
        }
</style>
<div id="legacy" class="remove-on-deploy">
    <div id="us-pop-container" class="horizontal-gradient outset">
        <h3></h3>
        <p></p>
    </div>
    <div id="world-pop-container" class="horizontal-gradient outset">
        <h3></h3>
        <p></p>
    </div>
        <div class="component-header alt">
        <p class="floatleft">date</p>
        <a target="_parent" href="<?php echo _APPROOT_;?>?intcmp=home_pop" class="floatright"><img src="//www.census.gov/main/img/home/readmore.gif" alt="More about Current Population" /></a>
    </div>
    <div class="ffix">&nbsp;</div>
</div>
<?php
// check if this is the census main page
elseif(isset($is_home_page) && $is_home_page):
?>
<style type="text/css">
        /* odometer */
        div#us-pop-container p, div#world-pop-container p {
                margin: 50px auto;
                padding: 100px;
                clear: both;
                float: none;
        }

        div#us-pop-container p span, div#world-pop-container p span {
                margin-right: 1px;
                display: inline-block;
                overflow: hidden;
                height: 22px;
        /*      float: left; */
                position: relative;
                top: 3px;
                color: #a3a3a3;
                font-weight: bold;
                font-size: 0.75em;
        /*      text-shadow: 0px 0px 2px #000000;
                filter: dropshadow(color=#000000, offx=0, offy=0); */
        }

        div#us-pop-container p span.rolling-digit, div#world-pop-container p span.rolling-digit {
                top: 0px;
                border: 1px solid #102e40;
                -webkit-border-radius: 3px;
                border-radius: 3px;
                -webkit-box-shadow:  0px 4px 4px 0px rgba(184, 209, 221, 0.1);
                                box-shadow:  0px 4px 4px 0px rgba(184, 209, 221, 0.1);
                background-image: url('<?php echo _APPROOT_;?>images/strip-bg.png');
                background-position: center center;
                background-size: 100% 100%;

        }

        div#us-pop-container p span.rolling-digit img, div#world-pop-container p span.rolling-digit img {
                position: relative;
        }

        .remove-on-deploy {
                width: 324px;
        }
        .component-header {
                /*display: none;*/
                font-size: 0.5em;
                text-align: center;
        }
        .component-header p {
                margin: 0;
                padding: 0;
        }
        div#us-pop-container h3, div#world-pop-container h3 {
                margin: 0;
                margin-bottom: 1em;
                padding: 0;
                color: #fefefe;
                text-shadow: 0px 2px 2px #000;
        filter: dropshadow(color=#000, offx=0, offy=2);
                font-weight: normal;
                font-size: 1em;
        }
        div#us-pop-container p, div#world-pop-container p {
                margin: 5px 0;
                padding: 0;
        }
        div#us-pop-container, div#world-pop-container {
                margin: 0;
                padding: 0;
                text-align: center;
                font-weight: bold;
                color: #0c6291;
        }

        div#us-pop-container {
                margin-bottom: 0;
                padding: 10px 0;
        /*      border-bottom: 1px solid #fff; */
                background: #0d3a53; /* Old browsers */
                background: -moz-linear-gradient(left,  #0d3a53 0%, #144968 50%, #09334b 100%); /* FF3.6+ */
                background: -webkit-gradient(linear, left top, right top, color-stop(0%,#0d3a53), color-stop(50%,#144968), color-stop(100%,#09334b)); /* Chrome,Safari4+ */
                background: -webkit-linear-gradient(left,  #0d3a53 0%,#144968 50%,#09334b 100%); /* Chrome10+,Safari5.1+ */
                background: -o-linear-gradient(left,  #0d3a53 0%,#144968 50%,#09334b 100%); /* Opera 11.10+ */
                background: -ms-linear-gradient(left,  #0d3a53 0%,#144968 50%,#09334b 100%); /* IE10+ */
                background: linear-gradient(to right,  #0d3a53 0%,#144968 50%,#09334b 100%); /* W3C */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#0d3a53', endColorstr='#09334b',GradientType=1 ); /* IE6-9 */
        }

        div#world-pop-container {
                margin-bottom: 0;
                margin-bottom: 5px;
                padding: 10px 0;
                border-top: 1px solid #000;
                background: #0d3a53; /* Old browsers */
                background: -moz-linear-gradient(left,  #0d3a53 0%, #144968 50%, #09334b 100%); /* FF3.6+ */
                background: -webkit-gradient(linear, left top, right top, color-stop(0%,#0d3a53), color-stop(50%,#144968), color-stop(100%,#09334b)); /* Chrome,Safari4+ */
                background: -webkit-linear-gradient(left,  #0d3a53 0%,#144968 50%,#09334b 100%); /* Chrome10+,Safari5.1+ */
                background: -o-linear-gradient(left,  #0d3a53 0%,#144968 50%,#09334b 100%); /* Opera 11.10+ */
                background: -ms-linear-gradient(left,  #0d3a53 0%,#144968 50%,#09334b 100%); /* IE10+ */
                background: linear-gradient(to right,  #0d3a53 0%,#144968 50%,#09334b 100%); /* W3C */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#0d3a53', endColorstr='#09334b',GradientType=1 ); /* IE6-9 */
        }

        div#us-pop-container p {
                color: #289ede;
        }

        .ffix {
                height: 0px;
                overflow: hidden;
                clear: both;
                float: none;
        }

</style>
<div class="remove-on-deploy">
    <div id="us-pop-container" class="horizontal-gradient outset">
        <h3>United States Population</h3>
<!--        <div class="progress-bar-container"><!-- progress bar, generated using Raphael </div> -->
        <p></p>
        <!-- population counter -->
        <div class="ffix">&nbsp;</div>
    </div>
    <div id="world-pop-container" class="horizontal-gradient outset">
        <h3>World Population</h3>
        <div class="progress-bar-container"><!-- progress bar, generated using Raphael --></div>
                <p></p>
        <!-- population counter -->
    </div>
        <div class="component-header alt">
        <p>date</p>
    </div>
</div>

<?php
// check if this is the CQ5 widget
elseif ($is_widget_313):
?>

<style type="text/css">


/* odometer */
        body {
            font-family: Arial, Helvetica, sans-serif;
            position: relative;
            width: 376px;
            height: 176px;
            padding: 0;
            margin: 0;
        }
        div#us-pop-container p, div#world-pop-container p {
            margin: 50px auto;
            padding: 100px;
            clear: both;
            float: none;
        }

        div#us-pop-container p span, div#world-pop-container p span {
            margin-right: 1px;
            display: inline-block;
            height: 19px;
            /* float: left; */
            position: relative;
            top: 0;
            color: #454875;
            font-weight: bold;
            font-size: 15px;
            /* text-shadow: 0px 0px 2px #000000;
            filter: dropshadow(color=#000000, offx=0, offy=0); */
        }
        div#us-pop-container p span.rolling-digit, div#world-pop-container p span.rolling-digit {
            top: 0px;
            border: 1px solid #777;
            -webkit-border-radius: 3px;
            border-radius: 3px;
           /* -webkit-box-shadow:  0px 14px 14px 0px rgba(0, 0, 0, 0.1);
            box-shadow:  0px 14px 14px 0px rgba(0, 0, 0, 0.1);*/
            background-image: url('./images/strip-bg-small.png');
            background-position: center center;
            background-size: 100% 100%;
            overflow: hidden;
        }

        div#us-pop-container p span.rolling-digit img, div#world-pop-container p span.rolling-digit img {
            position: relative;
        }

        .remove-on-deploy {
            width: 376px; /* Changed - 13-Aug-2013 from 264px */
        }
        .component-header {
            /*display: none;*/
            font-size: 10px;
            text-align: left;
            color: #333;
        }
        h2.widget_title {
            font-family: Arial, sans-serif;
            font-size:14px;
            font-weight:bold;
            color:#0c3953;
            display:block;
            position:absolute;
            width:376px;
            height: 30px;
            text-align:left;
            margin:1px 0 3px 1px;
            padding: 0;
/*
            background: #F4F4F4; 
*/
           position: absolute;
           top: 0px;
           left: 0px;
           color: #FFFFFF;
           font-weight: bold;
           font-size: 12px;
        }
        h2.widget_title span {
            margin-left: 11px;
            height: 22px;
            line-height: 30px;
            display: inline-block;
        }
        h2.widget_title a.embed {
            position: relative;
            float: right;
            width: 20px;
            height: 30px;
            background: url('./images/widget-icon-embed.png') no-repeat center center;
            display: inline-block;
            text-indent: -9999px;
            background-size: contain;
            -webkit-background-size: contain;
            -moz-background-size: contain;
            z-index: 8999;
            margin: 0 11px 0 0;
        }
        h2.widget_title a.embed:hover {
            background: url('./images/widget-icon-embed-hover.png') no-repeat center center;
            background-size: contain;
            -webkit-background-size: contain;
            -moz-background-size: contain;
        }
        div#widget-popup {
            position:absolute;
            top: 0;
            left: 0;
            background: #fff;
            width:376px;
            height:176px;
            padding: 20px;
            margin: 0;
            z-index: 9999;
            min-width: 100px;
            box-sizing: border-box;
        }
        div#widget-popup .close-button {
            position: absolute;
            top: 3px;
            right: 3px;
        }
        div#widget-popup .close-button a {
            display: block;
            text-indent: -9999px;
            width: 16px;
            height: 16px;
            background: url("./images/close-btn-square-grey_hover.png") no-repeat scroll left top;
        }
        div#widget-popup .contents {
            font-size: 12px;
        }
        div#widget-popup .contents textarea {
            width: 100%;
            height: 100px;
            box-sizing: border-box;
        }

        .widget_link {
            font-family: Arial, Helvetica, sans-serif;
            font-size:10px;
           /* font-weight:bold; */
            color:#FFFFFF;
/*
            color:#0c6391;
*/
            display:block;
            position:absolute;
            right:20px;
            top:142px;
        }

        .widget_link a  {
            color:#FFFFFF;
            font-family: pe-is-i-angle-circle-right;
        }

       .widget_313_link   {
            display:block;
            position:absolute;
            color:#FFFFFF;
            margin-left: 0.8em;
            right:12px;
            top:132px;
            width: 28px;
            height:28px;
/*
            border: 0.09em solid #FFFFFF;
*/
/*
            border-radius: 50%;
            margin-left: 0.8em;
            right:12px;
            top:132px;
*/

        }
/*
       .widget_313_link:after {
           content: '';
           display:block;
           position:absolute;
           margin-top:  0.48em; 
           margin-left: 0.35em;
           width: 0.7em;
           height: 0.7em;
           border-top: 1px solid #FFFFFF;
           border-right: 1px solid #FFFFFF;
           -moz-transform: rotate(45deg);
           -webkit-transform: rotate(45deg);
           transform: rotate(45deg);
       }

       .widget_313_link:hover {
            border: 2px solid #ff7043;
        } 
*/
        .component-header p {
            margin: 0;
            padding: 3px 0 4px 20px;
            font-family: Arial, Helvetica, sans-serif;
        }
        div#us-pop-container h3, div#world-pop-container h3 {
            margin: 0;
            margin-bottom: 0;
            padding: 0;
            color: #3072b8;
            font-weight: bold;
            font-size: 12px;
            font-family: Arial, Helvetica, sans-serif;
        }
        div#us-pop-container p, div#world-pop-container p {
            margin: 5px 0 0 0;
            padding: 0;
        }
        div#us-pop-container, div#world-pop-container {
            margin: 0;
            padding: 0;
            text-align: center;
            font-weight: bold;
            color: #0c6291;
            cursor: pointer;
        }

        div#us-pop-container {
            margin-bottom: 0; 
            padding: 35px 0 0px; 
            /* border-bottom: 1px solid #fff; */
        }

        div#world-pop-container {
            margin-bottom: 0;
            padding: 5px 0 5px; 
        }

        div#us-pop-container p {
            color: #289ede;
        }
        
        div#us-pop-container p,
        div#world-pop-container p {
            height: 29px;
        }

        .ffix {
            height: 0px;
            overflow: hidden;
            clear: both;
            float: none;
        }
        
        .widget-313 {
            height: 176px;
            background: linear-gradient(black, #112E51) no-repeat;
            cursor: pointer;
          /* background: blue; */
        }

       h2.widget_313_title {
           /*font-family:uscb-sub-heading-2 uscb-white-text uscb-bold-text; */
          /* font-family: 'Roboto';   */
          /* font-weight: normal; */
           position: absolute;
           top: 5px;
           left: 24px;
           color: #FFFFFF;
           font-size: 12px; 
        }

        h3.widget_313_date {
           position: absolute;
           top: 6px;
           right: 35px;
             font-style: normal;
          /*
           opacity: 0.6; 
           */
        }

       .widget_313_USA  {
           position: absolute;
          /* font-family: 'Roboto Condensed';   */
           font-weight: normal;
           font-size: 12px;
           top:  36px;
           left: 105px;
           color: #FFFFFF;   
           opacity: 0.9;
        }

       .widget_313_us_pop  {
           position: absolute;
           top:  30px;
           left: 105px;
           background-color:rgba(0, 0, 0, 0);
           color:white;
           border: none;
           outline:none;
           font-size: 34px;
           cursor: pointer;
        }

        .widget_313_WORLD  {
           position: absolute;
           font-family: 'Roboto Condensed';  
           font-weight: normal;
           font-size: 12px;
           top: 90px;
           left: 105px;
           color: #FFFFFF;
           opacity: 0.9;
        }

       .widget_313_world_pop  {
           position: absolute;
           font-style: bold; 
           top:  85px;
           left: 105px;
           background-color:rgba(0, 0, 0, 0);
           border: none;
           outline:none;
           cursor: pointer;
        }

         img.world_image {
            max-height: 176px;
            opacity: 0.6;
        }
</style>

<div class="remove-on-deploy widget-313">
   <?
     /*$d = date('F d, Y h:i T'); */
     $d = date('F d, Y');
   ?>
        <img src="images/PIA18033_small_v4.png" class="world_image" >
        <h2 class="uscb-sub-heading-2 uscb-white-text  uscb-bold-text widget_313_title">POPULATION CLOCK</h2>

        <h3 class="uscb-sub-heading-3-condensed uscb-white-text uscb-light-text uscb-italic-text widget_313_date "><?echo $d;?></h3>

        <h2 class="widget_313_USA uscb-h2 uscb-white-text">USA</h2>
<h2 class="widget_313_us_pop uscb-h2" id="us_pop_widget" name="us_pop_widget"></h2>

        <h2 class="widget_313_WORLD uscb-h2 uscb-white-text">WORLD</h2>
   <h2   class="uscb-h2 uscb-white-text widget_313_world_pop" id="world_pop_widget" name="world_pop_widget" ></h2>


       <!-- <img src="css/assets/oval_white.svg" class="widget_313_link"> -->
     <div id="widget_313_right_circle">
       <img src="css/images/angle-right-circle-29-white.png" class="widget_313_link">
     </div>
</div>



<?php
// check if this is the census widget
elseif ($is_widget_264):
?>
<style type="text/css">
/* odometer */
        body { width: 264px; height: 132px; padding: 0; margin: 0; }
        div#us-pop-container p, div#world-pop-container p {
                margin: 50px auto;
                padding: 100px;
                clear: both;
                float: none;
        }

        div#us-pop-container p span, div#world-pop-container p span {
                margin-right: 1px;
                display: inline-block;
                height: 19px;
                position: relative;
                top: 0;
                color: #a3a3a3;
                font-weight: bold;
                font-size: 12px;

        }

        div#us-pop-container p span.rolling-digit, div#world-pop-container p span.rolling-digit {
                top: 0px;
                border: 1px solid #102e40;
                -webkit-border-radius: 3px;
                border-radius: 3px;
                -webkit-box-shadow:  0px 4px 4px 0px rgba(184, 209, 221, 0.1);
                                box-shadow:  0px 4px 4px 0px rgba(184, 209, 221, 0.1);
                background-image: url('./images/strip-bg.png');
                background-position: center center;
                background-size: 100% 100%;
                overflow: hidden;

        }

        div#us-pop-container p span.rolling-digit img, div#world-pop-container p span.rolling-digit img {
                position: relative;
        }

        .remove-on-deploy {
                width: 264px;   }
        .component-header {
                /*display: none;*/
                font-size: 12px;
                text-align: center;
                color: #888;
        }
        .component-header p {
                margin: 0;
                padding: 3px 0 4px 0;
                font-family: Arial, Helvetica, sans-serif;
        }
        div#us-pop-container h3, div#world-pop-container h3 {
                margin: 0;
                margin-bottom: 0;
                padding: 0;
                color: #fefefe;
                text-shadow: 0px 2px 2px #000;
        filter: dropshadow(color=#000, offx=0, offy=2);
                font-weight: normal;
                font-size: 14px;
                font-family: Arial, Helvetica, sans-serif;

        }
        div#us-pop-container h3 { background: url('./images/us-icon.png') no-repeat 50px 0; }   
        div#world-pop-container h3 { background: url('./images/world-icon.png') no-repeat 55px 0;} 
        
        div#us-pop-container p, div#world-pop-container p {
                margin: 5px 0 0 0;
                padding: 0;
        }
        div#us-pop-container, div#world-pop-container {
                margin: 0;
                padding: 0;
                text-align: center;
                font-weight: bold;
                color: #0c6291;
        }

        div#us-pop-container {
                margin-bottom: 0; 
                padding: 5px 0 0 0 
                background: #0d3a53; /* Old browsers */
                background: -moz-linear-gradient(left,  #0d3a53 0%, #144968 50%, #09334b 100%); /* FF3.6+ */
                background: -webkit-gradient(linear, left top, right top, color-stop(0%,#0d3a53), color-stop(50%,#144968), color-stop(100%,#09334b)); /* Chrome,Safari4+ */
                background: -webkit-linear-gradient(left,  #0d3a53 0%,#144968 50%,#09334b 100%); /* Chrome10+,Safari5.1+ */
                background: -o-linear-gradient(left,  #0d3a53 0%,#144968 50%,#09334b 100%); /* Opera 11.10+ */
                background: -ms-linear-gradient(left,  #0d3a53 0%,#144968 50%,#09334b 100%); /* IE10+ */
                background: linear-gradient(to right,  #0d3a53 0%,#144968 50%,#09334b 100%); /* W3C */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#0d3a53', endColorstr='#09334b',GradientType=1 ); /* IE6-9 */
        }

        div#world-pop-container {
                margin-bottom: 0;
                padding: 5px 0 0 0;
                border-top: 1px solid #000;
                background: #0d3a53; /* Old browsers */
                background: -moz-linear-gradient(left,  #0d3a53 0%, #144968 50%, #09334b 100%); /* FF3.6+ */
                background: -webkit-gradient(linear, left top, right top, color-stop(0%,#0d3a53), color-stop(50%,#144968), color-stop(100%,#09334b)); /* Chrome,Safari4+ */
                background: -webkit-linear-gradient(left,  #0d3a53 0%,#144968 50%,#09334b 100%); /* Chrome10+,Safari5.1+ */
                background: -o-linear-gradient(left,  #0d3a53 0%,#144968 50%,#09334b 100%); /* Opera 11.10+ */
                background: -ms-linear-gradient(left,  #0d3a53 0%,#144968 50%,#09334b 100%); /* IE10+ */
                background: linear-gradient(to right,  #0d3a53 0%,#144968 50%,#09334b 100%); /* W3C */
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#0d3a53', endColorstr='#09334b',GradientType=1 ); /* IE6-9 */
        }

        div#us-pop-container p {
                color: #289ede;
        }
        
        div#us-pop-container p, div#world-pop-container p { height: 29px; }

        .ffix {
                height: 0px;
                overflow: hidden;
                clear: both;
                float: none;
        }
        
        .widget-264 {
            height: 132px;
        }


</style>
<div class="remove-on-deploy widget-264">
    <div id="us-pop-container" class="horizontal-gradient outset">
      <h3>United States Population</h3>
     <!--   <div class="progress-bar-container"> </div> -->
        <p></p>
        <!-- population counter -->
        <div class="ffix">&nbsp;</div>
    </div>

    <div id="world-pop-container" class="horizontal-gradient outset">
        <h3>World Population</h3>
        <div class="progress-bar-container"><!-- progress bar, generated using Raphael --></div>
            <p></p>
        <!-- population counter -->
    </div>
    <div class="component-header alt">
        <p>date</p>
    </div>
</div>

<?php
elseif($is_widget_200):
?>
<style type="text/css">
/*
Styles for U.S. and World population clocks
*/
body{ margin: 0; padding: 0; width: 200px; height: 402px; font-family: Arial, Helvetica, sans-serif; color: #4F4F4F; background: #FAF9F7; }
.component-header p { font-size: 10px; margin: 0; padding: 5px 0; text-align: center; }
div#population-counter-container p:nth-of-type(1) {
        float: none;
        clear: both;
}

ul.tabs {
        margin: 0;
        padding: 0;
        width: 188px;
        
}

ul.tabs li {
        display: inline-block;
        margin: 0;
        padding: 0;
        width: 90px;
}

ul.tabs a {
        font-size: 12px;
        padding: 5px 0 8px 0;
        display: inline-block;
        width: 100%;
        
        text-align: center;
        text-decoration: none;
        color: #656363;
        font-weight: normal;
        
        border: 2px solid #ddd;
        border-bottom: none;
        -webkit-border-top-right-radius: 5px;
        -moz-border-radius-topright: 5px;
        border-top-right-radius: 5px;
        
        -webkit-border-top-left-radius: 5px;
        -moz-border-radius-topleft: 5px;
        border-top-left-radius: 5px;
        
        background: #faf9f7;
        
}

ul.tabs a.selected {
        padding-top: 9px;
        border: 2px solid #032c44;
        border-bottom: none;

        background: #09334b;
        
        color: #fff;
        font-weight: normal;
        text-decoration: none;
        
        text-shadow: 0px 1px #000;
}
div#population-counter-container {
        /* margin: 0;
        margin-left: 6px; */
    margin: 0 0 0 25px;
        padding: 0;
        width: 188px;
        height: 402px;
}

div#us-pop-container, div#world-pop-container {
        margin: 0;
        padding: 18px 7px;
        text-align: center;
        font-weight: bold;
        color: #289EDE;
        background: #0D3A53;
        background: -moz-linear-gradient(left, #0D3A53 0%, #144968 50%, #09334B 100%);
        background: -webkit-gradient(linear, left top, right top, color-stop(0%,#0D3A53), color-stop(50%,#144968), color-stop(100%,#09334B));
        background: -webkit-linear-gradient(left, #0D3A53 0%,#144968 50%,#09334B 100%);
        background: -o-linear-gradient(left, #0D3A53 0%,#144968 50%,#09334B 100%);
        background: -ms-linear-gradient(left, #0D3A53 0%,#144968 50%,#09334B 100%);
        background: linear-gradient(to right, #0D3A53 0%,#144968 50%,#09334B 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#0d3a53', endColorstr='#09334b',GradientType=1 );
}

div#world-pop-historical {
        margin: 0 auto;
        padding: 5px;
}

div#world-pop-historical table {
        margin-left: 4px;
        width: 48%;
        
}

div#us-pop-components-container table thead {
        display: none;
}
div#world-pop-top10-container table thead {
        display: none;
}

div#world-pop-historical table td {
        padding-top: 5px;
}

div#us-pop-wrapper, div#world-pop-wrapper {
        float: none;
        clear: both;
        width: 188px;
        cursor: pointer;
}

div#us-pop-container h3, div#world-pop-container h3 {
        margin: 0;
        margin-bottom: 0;
        padding: 0;
        text-align: center;
        width: 188px;
        color: #FEFEFE;
        text-shadow: 0px 2px 2px black;
        filter: dropshadow(color=#000, offx=0, offy=2);
        font-weight: normal;
        font-size: 14px;
        font-family: Arial, Helvetica, sans-serif;
}

div#us-pop-container h3 {
        background: url('./images/us-icon.png') no-repeat 12px 0;
}

div#world-pop-container h3 {
        background: url('./images/world-icon.png') no-repeat 15px 0;
}

div#population-counter-container p:nth-of-type(1) {
}


div#us-pop-container p span, div#world-pop-container p span {
        margin-right: 1px;
        display: inline-block;
        overflow: hidden;
        height: 21px;
/*      float: left; */
        position: relative;
        top: 1px;
        color: #fff;
        font-weight: bold;
        font-size: 11px;
        text-shadow: 0px 0px 2px #000000;
        filter: dropshadow(color=#000000, offx=0, offy=0);
        
}

div#us-pop-container p span.rolling-digit, div#world-pop-container p span.rolling-digit {
        top: 0px;
        width: 15px;
        height: 21px;
        background-image: url('./images/strip-bg-widget.png');
        background-position: center center;
        background-size: 17px 21px;
}

div#us-pop-container p span.rolling-digit img, div#world-pop-container p span.rolling-digit img {
        position: relative;
        left: -1px;
}

div#us-pop-container p, div#world-pop-container p { margin: 8px 0 0 0; }

div#us-pop-components-container table td#component-timestamp {
        display: none;
}

div#world-pop-wrapper div#world-pop-historical table {
        margin-bottom: 10px;
        border-spacing:0;
        border-collapse:collapse;
        float: none;
        clear: both;
        width: 97%;
        
}

div#world-pop-historical table td {
        padding: 2px 0;
        font-size: 0.9em;
}

/* progress bars */
#us-pop-components-container div.html-progress-bar-track {
        background-image: url('./css/images/tan-bg.png');
        background-size: auto 100%;
        background-repeat: repeat-x;
        width: 160px;   
        height: 10px;
}

#us-pop-components-container span.html-progress-bar-bar {
        display: block;
        height: 10px;
}

div#us-pop-components-container, div#world-pop-historical{
        width: 184px;
        background: white;
        
        border: 2px solid #ddd;
        border-top: none;
        
        padding-bottom: 7px;
        
        -moz-border-radius: 8px;
        -webkit-border-radius: 8px;
        border-radius: 8px;
        
        -webkit-border-top-right-radius: 0;
        -moz-border-radius-topright: 0;
        border-top-right-radius: 0;
        
        -webkit-border-top-left-radius: 0;
        -moz-border-radius-topleft: 0;
        border-top-left-radius: 0;
}

div#world-pop-top10-container {
    width: 184px;
    background: white;
    border: 2px solid #ddd;
    border-top: none;
    padding-bottom: 23px;
    -moz-border-radius: 8px;
    -webkit-border-radius: 8px;
    border-radius: 8px;
    -webkit-border-top-right-radius: 0;
    -moz-border-radius-topright: 0;
    border-top-right-radius: 0;
    -webkit-border-top-left-radius: 0;
    -moz-border-radius-topleft: 0;
    border-top-left-radius: 0;
}

#us-pop-components-container span.html-progress-bar-bar { background-image: url('./css/images/light-blue.png'); }


div#us-pop-components-container p, div#world-pop-historical p, div#world-pop-top10-container p { margin: 0; text-transform: uppercase; font-weight: bold; margin-bottom: 10px; padding: 19px 20px 0 20px; font-size: 11px; color: #4F4F4F; text-align: center; }
div#world-pop-historical p a { color: #4F4F4F; }

div#us-pop-components-container table td { color: #4F4F4F; font-size: 11px; float:left;  padding: 1px 10px; }
div#world-pop-top10-container table td { color: #4F4F4F; font-size: 11px;  padding: 1px 10px; }
div#world-pop-top10-container table td:nth-child(2) {
    text-align:right;
}
div#world-pop-historical {
        margin: 0;
        padding: 0;
}

/* progress bar cell */
div#us-pop-components-container table td:nth-of-type(2) {
        width: 160px;
        padding-bottom: 10px;
        padding-top: 3px;
}

#us-pop-components-container #pop-counter-progress-bar-3 span.html-progress-bar-bar {
        background-image: url('./css/images/dark-blue.png');
}

div#world-pop-historical th:nth-of-type(1) a { font-size: 11px; color: #4F4F4F; text-align: center;  }

</style>
<div id="population-counter-container" class="container beveled shadow vertical-gradient">
        <div class="component-header alt">
        <p>date</p>
     </div>
    <ul class="tabs">
                <li><a href="#us" class="selected">United States</a></li>
                <li><a href="#world">World</a></li>
        </ul>
    <!-- U.S. population data -->
    <div id="us-pop-wrapper" class="lcol" onClick="parent.location='<?php echo $config->components->us->url;?>'">

        <!-- contains header, progress bar, and counter -->
        <div id="us-pop-container">
            <h3></h3>
            <!-- population counter -->
            <p></p>
        </div>

        <!-- contains table with data types and progress bars -->
        <div id="us-pop-components-container">
                <p></p>
                    <table>
                        <thead></thead>
                        <tbody></tbody>
                    </table>
        </div>
        <div>
           <p>
           The U.S. population total and population change have been adjusted to be consistent with the results of the 2020 Census. The components of population change have not been adjusted and so inconsistencies will exist between population values derived directly from the components and the population displayed in the odometer and the Select a Date tool.
           </p>
        </div>
        <div class="ffix">&nbsp;</div>
    </div>

    <!-- World population data -->
    <div id="world-pop-wrapper" class="rcol" onClick="parent.location='<?php echo $config->components->world->url;?>'">

        <!-- container header, progress bar, and counter -->
        <div id="world-pop-container">
            <h3></h3>
            <!-- population counter -->
            
            <p></p>
                </div>
            <div id="world-pop-top10-container">
              <p>Top 10 most populous countries (July 1, 2026)</p>
 
                <table>
                                <thead></thead>
                                <tbody>
                                    <?php foreach($config->components->world_rates->tables[0]->rows as $country => $pop){?>
                                        <tr>
                                            <td><?php echo $country;?></td>
                                            <td><?php echo number_format($pop,0);?></td>
                                        </tr>
                                    <?php }?>
                                </tbody>
                </table>
            </div>
        
    </div>
</div>
<?php
elseif($is_widget_310):
?>
<style type="text/css">
/*
Styles for U.S. and World population clocks
*/
body{ margin: 0; padding: 0; width: 200px; height: 402px; font-family: Arial, Helvetica, sans-serif; color: #4F4F4F; background: #FAF9F7; }
.component-header p { font-size: 10px; margin: 0; padding: 5px 0; text-align: center; }
div#population-counter-container p:nth-of-type(1) {
        float: none;
        clear: both;
}

ul.tabs {
        margin: 0;
        padding: 0;
        width: 188px;
        
}

ul.tabs li {
        display: inline-block;
        margin: 0;
        padding: 0;
        width: 90px;
}

ul.tabs a {
        font-size: 12px;
        padding: 5px 0 8px 0;
        display: inline-block;
        width: 100%;
        
        text-align: center;
        text-decoration: none;
        color: #656363;
        font-weight: normal;
        
        border: 2px solid #ddd;
        border-bottom: none;
        -webkit-border-top-right-radius: 5px;
        -moz-border-radius-topright: 5px;
        border-top-right-radius: 5px;
        
        -webkit-border-top-left-radius: 5px;
        -moz-border-radius-topleft: 5px;
        border-top-left-radius: 5px;
        
        background: #faf9f7;
        
}

ul.tabs a.selected {
        padding-top: 9px;
        border: 2px solid #032c44;
        border-bottom: none;

        background: #09334b;
        
        color: #fff;
        font-weight: normal;
        text-decoration: none;
        
        text-shadow: 0px 1px #000;
}
div#population-counter-container {
        /* margin: 0;
        margin-left: 6px; */
    margin: 0 0 0 25px;
        padding: 0;
        width: 188px;
        height: 402px;
}

div#us-pop-container, div#world-pop-container {
        margin: 0;
        padding: 18px 7px;
        text-align: center;
        font-weight: bold;
        color: #289EDE;
        background: #0D3A53;
        background: -moz-linear-gradient(left, #0D3A53 0%, #144968 50%, #09334B 100%);
        background: -webkit-gradient(linear, left top, right top, color-stop(0%,#0D3A53), color-stop(50%,#144968), color-stop(100%,#09334B));
        background: -webkit-linear-gradient(left, #0D3A53 0%,#144968 50%,#09334B 100%);
        background: -o-linear-gradient(left, #0D3A53 0%,#144968 50%,#09334B 100%);
        background: -ms-linear-gradient(left, #0D3A53 0%,#144968 50%,#09334B 100%);
        background: linear-gradient(to right, #0D3A53 0%,#144968 50%,#09334B 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#0d3a53', endColorstr='#09334b',GradientType=1 );
}

div#world-pop-historical {
        margin: 0 auto;
        padding: 5px;
}

div#world-pop-historical table {
        margin-left: 4px;
        width: 48%;
        
}

div#us-pop-components-container table thead {
        display: none;
}
div#world-pop-top10-container table thead {
        display: none;
}

div#world-pop-historical table td {
        padding-top: 5px;
}

div#us-pop-wrapper, div#world-pop-wrapper {
        float: none;
        clear: both;
        width: 188px;
        cursor: pointer;
}

div#us-pop-container h3, div#world-pop-container h3 {
        margin: 0;
        margin-bottom: 0;
        padding: 0;
        text-align: center;
        width: 188px;
        color: #FEFEFE;
        text-shadow: 0px 2px 2px black;
        filter: dropshadow(color=#000, offx=0, offy=2);
        font-weight: normal;
        font-size: 14px;
        font-family: Arial, Helvetica, sans-serif;
}

div#us-pop-container h3 {
        background: url('./images/us-icon.png') no-repeat 12px 0;
}

div#world-pop-container h3 {
        background: url('./images/world-icon.png') no-repeat 15px 0;
}

div#population-counter-container p:nth-of-type(1) {
}


div#us-pop-container p span, div#world-pop-container p span {
        margin-right: 1px;
        display: inline-block;
        overflow: hidden;
        height: 21px;
/*      float: left; */
        position: relative;
        top: 1px;
        color: #fff;
        font-weight: bold;
        font-size: 11px;
        text-shadow: 0px 0px 2px #000000;
        filter: dropshadow(color=#000000, offx=0, offy=0);
        
}

div#us-pop-container p span.rolling-digit, div#world-pop-container p span.rolling-digit {
        top: 0px;
        width: 15px;
        height: 21px;
        background-image: url('./images/strip-bg-widget.png');
        background-position: center center;
        background-size: 17px 21px;
}

div#us-pop-container p span.rolling-digit img, div#world-pop-container p span.rolling-digit img {
        position: relative;
        left: -1px;
}

div#us-pop-container p, div#world-pop-container p { margin: 8px 0 0 0; }

div#us-pop-components-container table td#component-timestamp {
        display: none;
}

div#world-pop-wrapper div#world-pop-historical table {
        margin-bottom: 10px;
        border-spacing:0;
        border-collapse:collapse;
        float: none;
        clear: both;
        width: 97%;
        
}

div#world-pop-historical table td {
        padding: 2px 0;
        font-size: 0.9em;
}

/* progress bars */
#us-pop-components-container div.html-progress-bar-track {
        background-image: url('./css/images/tan-bg.png');
        background-size: auto 100%;
        background-repeat: repeat-x;
        width: 160px;   
        height: 10px;
}

#us-pop-components-container span.html-progress-bar-bar {
        display: block;
        height: 10px;
}

div#us-pop-components-container, div#world-pop-historical{
        width: 184px;
        background: white;
        
        border: 2px solid #ddd;
        border-top: none;
        
        padding-bottom: 7px;
        
        -moz-border-radius: 8px;
        -webkit-border-radius: 8px;
        border-radius: 8px;
        
        -webkit-border-top-right-radius: 0;
        -moz-border-radius-topright: 0;
        border-top-right-radius: 0;
        
        -webkit-border-top-left-radius: 0;
        -moz-border-radius-topleft: 0;
        border-top-left-radius: 0;
}

div#world-pop-top10-container {
    width: 184px;
    background: white;
    border: 2px solid #ddd;
    border-top: none;
    padding-bottom: 23px;
    -moz-border-radius: 8px;
    -webkit-border-radius: 8px;
    border-radius: 8px;
    -webkit-border-top-right-radius: 0;
    -moz-border-radius-topright: 0;
    border-top-right-radius: 0;
    -webkit-border-top-left-radius: 0;
    -moz-border-radius-topleft: 0;
    border-top-left-radius: 0;
}

#us-pop-components-container span.html-progress-bar-bar { background-image: url('./css/images/light-blue.png'); }


div#us-pop-components-container p, div#world-pop-historical p, div#world-pop-top10-container p { margin: 0; text-transform: uppercase; font-weight: bold; margin-bottom: 10px; padding: 19px 20px 0 20px; font-size: 11px; color: #4F4F4F; text-align: center; }
div#world-pop-historical p a { color: #4F4F4F; }

div#us-pop-components-container table td { color: #4F4F4F; font-size: 11px; float:left;  padding: 1px 10px; }
div#world-pop-top10-container table td { color: #4F4F4F; font-size: 11px;  padding: 1px 10px; }
div#world-pop-top10-container table td:nth-child(2) {
    text-align:right;
}
div#world-pop-historical {
        margin: 0;
        padding: 0;
}

/* progress bar cell */
div#us-pop-components-container table td:nth-of-type(2) {
        width: 160px;
        padding-bottom: 10px;
        padding-top: 3px;
}

#us-pop-components-container #pop-counter-progress-bar-3 span.html-progress-bar-bar {
        background-image: url('./css/images/dark-blue.png');
}

div#world-pop-historical th:nth-of-type(1) a { font-size: 11px; color: #4F4F4F; text-align: center;  }

</style>
<div id="population-counter-container" class="container beveled shadow vertical-gradient">
        <div class="component-header alt">
        <p>date</p>
     </div>
    <ul class="tabs">
                <li><a href="#us">United States</a></li>
                <li><a href="#world" class="selected">World</a></li>
        </ul>
    <!-- U.S. population data -->
    <div id="us-pop-wrapper" class="lcol" onClick="parent.location='<?php echo $config->components->us->url;?>'">

        <!-- contains header, progress bar, and counter -->
        <div id="us-pop-container">
            <h3></h3>
            <!-- population counter -->
            <p></p>
        </div>

        <!-- contains table with data types and progress bars -->
        <div id="us-pop-components-container">
                <p></p>
                    <table>
                        <thead></thead>
                        <tbody></tbody>
                    </table>
        </div>
        <div class="ffix">&nbsp;</div>
    </div>

    <!-- World population data -->
    <div id="world-pop-wrapper" class="rcol" onClick="parent.location='<?php echo $config->components->world->url;?>'">

        <!-- container header, progress bar, and counter -->
        <div id="world-pop-container">
            <h3></h3>
            <!-- population counter -->
            
            <p></p>
                </div>
            <div id="world-pop-top10-container">
                <p>Top 10 most populous countries (July 1, 2026)</p>
                <table>
                                <thead></thead>
                                <tbody>
                                    <?php foreach($config->components->world_rates->tables[0]->rows as $country => $pop){?>
                                        <tr>
                                            <td><?php echo $country;?></td>
                                            <td><?php echo number_format($pop,0);?></td>
                                        </tr>
                                    <?php }?>
                                </tbody>
                </table>
            </div>
        
    </div>
</div>
<?php
// tabs for embedded view
elseif (isset($_GET['component'])):
?>
<ul class="tabs">   
    <li><a href="#us" class="selected">U.S. Population</a></li>
    <li><a href="#world">World Population</a></li>  
</ul>
<div id="population-counter-container" class="container beveled shadow vertical-gradient">
        <div class="component-header alt">
        <p>date</p>
        <p class="share"><a href="#counter-footnote"></a> | <a href="./embed.php?component=counter"></a></p>
     </div>
    <div class="ffix">&nbsp;</div>
    <!-- U.S. population data -->
    <div id="us-pop-wrapper" class="lcol">

        <!-- contains header, progress bar, and counter -->
        <div id="us-pop-container">
            <h3></h3>
            <!-- population counter -->
            <p></p>
        </div>

        <!-- contains table with data types and progress bars -->
        <div id="us-pop-components-container">
                <p></p>
            <table>
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="ffix">&nbsp;</div>
    </div>

    <!-- World population data -->
    <div id="world-pop-wrapper" class="rcol">

        <!-- container header, progress bar, and counter -->
        <div id="world-pop-container">
            <h3></h3>
            <!-- population counter -->
            <p></p>
        </div>

        <!-- contains two tables with population on date -->
        <div id="world-pop-historical">
                <p></p>
            <table>
            </table>
            <table>
            </table>
        </div>
        <div class="ffix">&nbsp;</div>
    </div>
        <div class="ffix">&nbsp;</div>
</div>
<?php else: ?>
<div id="population-counter-container" class="container beveled shadow vertical-gradient">
        <div class="component-header alt">
        <p>date</p>
        <?php if ( _PAGE_ == 'country' ): ?>
            <p class="share"><a href="#world-footer"></a> | <a href="./embed.php?component=counter"></a></p>        	
        <?php else: ?>
        	<p class="share"><a href="#counter-footnote"></a> | <a href="./embed.php?component=counter"></a></p>
        <?php endif; ?>  
     </div>
    <div class="ffix">&nbsp;</div>
    <!-- U.S. population data -->
    <div id="us-pop-wrapper" class="lcol">

        <!-- contains header, progress bar, and counter -->
        <div id="us-pop-container">
            <h3></h3>
            <!-- population counter -->
            <p></p>
        </div>
<?php if(_PAGE_ == 'index'  || _PAGE_ == 'population_widget_310x200' || _PAGE_ == 'population_widget_200x402'): ?> 
        <!-- contains table with data types and progress bars -->
        <div id="us-pop-components-container">
                <p></p>
            <table>
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
<?php endif;?>                
        <div class="ffix">&nbsp;</div>
    </div>

    <!-- World population data -->
    <div id="world-pop-wrapper" class="rcol">

        <!-- container header, progress bar, and counter -->
        <div id="world-pop-container">
            <h3></h3>
            <!-- population counter -->
            <p></p>
        </div>
<?php if(_PAGE_ == 'index' || _PAGE_ == 'population_widget_310x200' || _PAGE_ == 'population_widget_200x402'): ?>
        <!-- contains two tables with population on date -->
        <div id="world-pop-historical">
                <p></p>
            <table>
            </table>
            <table>
            </table>
        </div>
<?php endif;?>                
        <div class="ffix">&nbsp;</div>
    </div>
        <div class="ffix">&nbsp;</div>
</div>
<?php endif; ?>
<!-- End U.S. and World populations --><!-- U.S. and World populations -->
