<?php


//$subpage = rex_be_controller::getCurrentPagePart(2);

echo rex_view::title('VHS');

//include rex_be_controller::getCurrentPageObject()->getSubPath();
rex_be_controller::includeCurrentPageSubPath();
