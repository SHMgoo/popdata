<?php

if ( strcasecmp( $_SERVER['HTTP_HOST'], 'www.census.gov' ) == 0 ) {

?>
  <script src="//assets.adobedtm.com/526d5084b7f8f688ea81a3aba09755d76a81f8e8/satelliteLib-5b0c69cba9998404374755048324ea6deb6c9db5.js"></script>
<?php

} else  {

?>
  <script src="//assets.adobedtm.com/526d5084b7f8f688ea81a3aba09755d76a81f8e8/satelliteLib-5b0c69cba9998404374755048324ea6deb6c9db5-staging.js"></script>
<?php

}

