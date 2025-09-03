<?php 

class ConstantsMacronnect {

    // Type Filters
    public static array $TYPE_FILTERS = [
        "=" => "=",
        ">" => "%5Bgt%5D=",
        "<" => "%5Blt%5D=",
        ">=" => "%5Bgte%5D=",
        "<=" => "%5Blte%5D=",
        "IN" => "%5Bin%5D=",
        "NOT IN" => "%5BninA%5D=",
        "<>" => "%5Bdif%5D="
    ];

}


?>