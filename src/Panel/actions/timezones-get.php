<?php

return function(){
    return DateTimeZone::listAbbreviations();
    return array_map(fn($item) => $item[2], DateTimeZone::listAbbreviations());
};